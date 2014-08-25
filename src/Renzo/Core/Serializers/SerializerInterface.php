<?php
/*
 * Copyright REZO ZERO 2014
 *
 * Description
 *
 * @file EntitySerializer.php
 * @copyright REZO ZERO 2014
 * @author Thomas Aufresne
 */
namespace RZ\Renzo\Core\Serializers;

/**
 * EntitySerializer that implements simple serialization/deserialization methods.
 */
interface SerializerInterface
{

    /**
     * Serializes data.
     *
     * @return mixed
     */
    public function serialize();

    /**
     * Deserializes a json file into a readable array of datas.
     *
     * @param string $string Input to deserialize
     *
     * @return mixed
     */
    public static function deserialize($string);
}