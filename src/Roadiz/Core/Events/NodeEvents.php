<?php
/**
 * Copyright © 2015, Ambroise Maupate and Julien Blanchet
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
 * @file NodeEvents.php
 * @author Ambroise Maupate
 */
namespace RZ\Roadiz\Core\Events;

/**
 *
 */
final class NodeEvents
{
    /**
     * Event node.created is triggered each time a node
     * is created.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_CREATED = 'node.created';

    /**
     * Event node.updated is triggered each time a node
     * is updated.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_UPDATED = 'node.updated';

    /**
     * Event node.deleted is triggered each time a node
     * is deleted.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_DELETED = 'node.deleted';

    /**
     * Event node.undeleted is triggered each time a node
     * is undeleted.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_UNDELETED = 'node.undeleted';

    /**
     * Event node.tagged is triggered each time a node
     * is linked or unlinked with a Tag.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_TAGGED = 'node.tagged';

    /**
     * Event node.visibilityChanged is triggered each time a node
     * becomes visible or unvisible.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_VISIBILITY_CHANGED = 'node.visibilityChanged';

    /**
     * Event node.statusChanged is triggered each time a node
     * status changes.
     *
     * Event listener will be given a:
     * RZ\Roadiz\Core\Events\FilterNodeEvent instance
     *
     * @var string
     */
    const NODE_STATUS_CHANGED = 'node.statusChanged';
}
