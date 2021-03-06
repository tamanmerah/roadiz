<?php
/*
 * Copyright © 2014, Ambroise Maupate and Julien Blanchet
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * Except as contained in this notice, the name of the ROADIZ shall not
 * be used in advertising or otherwise to promote the sale, use or other dealings
 * in this Software without prior written authorization from Ambroise Maupate and Julien Blanchet.
 *
 * Description
 *
 * @file FoldersController.php
 * @author Ambroise Maupate
 */

namespace Themes\Rozier\Controllers;

use RZ\Roadiz\Core\Entities\Document;
use RZ\Roadiz\Core\Entities\Folder;
use RZ\Roadiz\Core\Entities\FolderTranslation;
use RZ\Roadiz\Core\Entities\Translation;
use RZ\Roadiz\Utils\StringHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Themes\Rozier\Forms\FolderTranslationType;
use Themes\Rozier\Forms\FolderType;
use Themes\Rozier\RozierApp;

/**
 * Folders controller
 */
class FoldersController extends RozierApp
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        $listManager = $this->createEntityListManager(
            'RZ\Roadiz\Core\Entities\Folder'
        );
        $listManager->handle();

        $this->assignation['filters'] = $listManager->getAssignation();
        $this->assignation['folders'] = $listManager->getEntities();

        return $this->render('folders/list.html.twig', $this->assignation);
    }

    /**
     * Return an creation form for requested folder.
     *
     * @param Request $request
     * @param int     $parentFolderId
     *
     * @return Response
     * @throws \Twig_Error_Runtime
     */
    public function addAction(Request $request, $parentFolderId = null)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        $folder = new Folder();

        if (null !== $parentFolderId) {
            $parentFolder = $this->getService('em')
                                 ->find('RZ\Roadiz\Core\Entities\Folder', (int) $parentFolderId);
            if (null !== $parentFolder) {
                $folder->setParent($parentFolder);
            }
        }
        /** @var Form $form */
        $form = $this->createForm(new FolderType(), $folder, [
            'em' => $this->getService('em'),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                /** @var Translation $translation */
                $translation = $this->getService('defaultTranslation');
                $folderTranslation = new FolderTranslation($folder, $translation);
                $this->getService('em')->persist($folder);
                $this->getService('em')->persist($folderTranslation);

                $this->getService('em')->flush();

                $msg = $this->getTranslator()->trans(
                    'folder.%name%.created',
                    ['%name%' => $folder->getFolderName()]
                );
                $this->publishConfirmMessage($request, $msg);
            } catch (\RuntimeException $e) {
                $this->publishErrorMessage($request, $e->getMessage());
            }

            return $this->redirect($this->generateUrl('foldersHomePage'));
        }

        $this->assignation['form'] = $form->createView();

        return $this->render('folders/add.html.twig', $this->assignation);
    }

    /**
     * Return a deletion form for requested folder.
     *
     * @param Request $request
     * @param int     $folderId
     *
     * @return Response
     */
    public function deleteAction(Request $request, $folderId)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        /** @var Folder $folder */
        $folder = $this->getService('em')
                       ->find('RZ\Roadiz\Core\Entities\Folder', (int) $folderId);

        if (null !== $folder) {
            $form = $this->buildDeleteForm($folder);
            $form->handleRequest($request);

            if ($form->isValid() &&
                $form->getData()['folder_id'] == $folder->getId()) {
                try {
                    $this->deleteFolder($folder);
                    $msg = $this->getTranslator()->trans(
                        'folder.%name%.deleted',
                        ['%name%' => $folder->getFolderName()]
                    );
                    $this->publishConfirmMessage($request, $msg);
                } catch (\RuntimeException $e) {
                    $this->publishErrorMessage($request, $e->getMessage());
                }

                return $this->redirect($this->generateUrl('foldersHomePage'));
            }

            $this->assignation['form'] = $form->createView();
            $this->assignation['folder'] = $folder;

            return $this->render('folders/delete.html.twig', $this->assignation);
        } else {
            return $this->throw404();
        }
    }

    /**
     * Return an edition form for requested folder.
     *
     * @param Request $request
     * @param int     $folderId
     *
     * @return Response
     */
    public function editAction(Request $request, $folderId)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        /** @var Folder $folder */
        $folder = $this->getService('em')
                       ->find('RZ\Roadiz\Core\Entities\Folder', (int) $folderId);

        if ($folder !== null) {
            /** @var Form $form */
            $form = $this->createForm(new FolderType(), $folder, [
                'em' => $this->getService('em'),
                'name' => $folder->getFolderName(),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->getService('em')->flush();

                    $msg = $this->getTranslator()->trans(
                        'folder.%name%.updated',
                        ['%name%' => $folder->getFolderName()]
                    );
                    $this->publishConfirmMessage($request, $msg);
                } catch (\RuntimeException $e) {
                    $this->publishErrorMessage($request, $e->getMessage());
                }

                return $this->redirect($this->generateUrl('foldersEditPage', ['folderId' => $folderId]));
            }

            $this->assignation['folder'] = $folder;
            $this->assignation['form'] = $form->createView();
            $this->assignation['available_translations'] = $this->getService('em')
                ->getRepository('RZ\Roadiz\Core\Entities\Translation')
                ->findAllAvailable();

            return $this->render('folders/edit.html.twig', $this->assignation);
        }
        return $this->throw404();
    }

    /**
     * @param Request $request
     * @param $folderId
     * @param $translationId
     * @return Response
     * @throws \Twig_Error_Runtime
     */
    public function editTranslationAction(Request $request, $folderId, $translationId)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        /** @var Folder $folder */
        $folder = $this->getService('em')
            ->find('RZ\Roadiz\Core\Entities\Folder', (int) $folderId);

        /** @var Translation $translation */
        $translation = $this->getService('em')
            ->find('RZ\Roadiz\Core\Entities\Translation', (int) $translationId);

        /** @var FolderTranslation $folderTranslation */
        $folderTranslation = $this->getService('em')
            ->getRepository('RZ\Roadiz\Core\Entities\FolderTranslation')
            ->findOneBy([
                'folder' => $folder,
                'translation' => $translation,
            ]);

        if (null === $folderTranslation) {
            $folderTranslation = new FolderTranslation($folder, $translation);
        }

        if (null !== $folder && null !== $translation) {
            /** @var Form $form */
            $form = $this->createForm(new FolderTranslationType(), $folderTranslation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->getService('em')->merge($folderTranslation);
                    $this->getService('em')->flush();
                    $msg = $this->getTranslator()->trans(
                        'folder.%name%.updated',
                        ['%name%' => $folder->getFolderName()]
                    );
                    $this->publishConfirmMessage($request, $msg);
                } catch (\RuntimeException $e) {
                    $this->publishErrorMessage($request, $e->getMessage());
                }

                return $this->redirect($this->generateUrl('foldersEditTranslationPage', [
                    'folderId' => $folderId,
                    'translationId' => $translationId,
                ]));
            }

            $this->assignation['folder'] = $folder;
            $this->assignation['translation'] = $translation;
            $this->assignation['form'] = $form->createView();
            $this->assignation['available_translations'] = $this->getService('em')
                ->getRepository('RZ\Roadiz\Core\Entities\Translation')
                ->findAllAvailable();

            return $this->render('folders/edit.html.twig', $this->assignation);
        }
        return $this->throw404();
    }

    /**
     * Return a ZipArchive of requested folder.
     *
     * @param Request $request
     * @param int     $folderId
     *
     * @return Response
     */
    public function downloadAction(Request $request, $folderId)
    {
        $this->validateAccessForRole('ROLE_ACCESS_DOCUMENTS');

        /** @var Folder $folder */
        $folder = $this->getService('em')
                       ->find('RZ\Roadiz\Core\Entities\Folder', (int) $folderId);

        if ($folder !== null) {
            // Prepare File
            $file = tempnam("tmp", "zip");
            $zip = new \ZipArchive();
            $zip->open($file, \ZipArchive::OVERWRITE);

            $documents = $this->getService('em')
                              ->getRepository('RZ\Roadiz\Core\Entities\Document')
                              ->findBy([
                                  'folders' => [$folder],
                              ]);
            /** @var Document $document */
            foreach ($documents as $document) {
                $zip->addFile($document->getAbsolutePath(), $document->getFilename());
            }

            // Close and send to users
            $zip->close();

            $filename = StringHandler::slugify($folder->getFolderName()) . '.zip';

            $response = new Response(
                file_get_contents($file),
                Response::HTTP_OK,
                [
                    'content-type' => 'application/zip',
                    'content-length' => filesize($file),
                    'content-disposition' => 'attachment; filename=' . $filename,
                ]
            );
            unlink($file);

            return $response;
        } else {
            return $this->throw404();
        }
    }

    /**
     * Build delete folder form with name constraint.
     *
     * @param Folder $folder
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function buildDeleteForm(Folder $folder)
    {
        $builder = $this->createFormBuilder()
                        ->add('folder_id', 'hidden', [
                            'data' => $folder->getId(),
                        ]);

        return $builder->getForm();
    }

    /**
     * @param Folder $folder
     *
     * @return void
     */
    protected function deleteFolder(Folder $folder)
    {
        $this->getService('em')->remove($folder);
        $this->getService('em')->flush();
    }
}
