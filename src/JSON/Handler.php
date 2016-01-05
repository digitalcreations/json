<?php

namespace DC\JSON;

interface Handler {
    /**
     * Get a list of the classes this handler can serialize.
     *
     * @return string[]
     */
    function getHandledTypes();

    /**
     * @param object $object The object to serialize.
     * @return mixed Object or scalar suitable for serialization with json_encode
     */
    function serialize($object);

    /**
     * Deserialize data to correct type.
     *
     * @param object $data
     * @param string $type
     * @return mixed
     */
    function deserialize($data, $type);
}