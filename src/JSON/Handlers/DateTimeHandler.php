<?php

namespace DC\JSON\Handlers;

use DC\JSON\Exceptions\DeserializationException;

class DateTimeHandler implements \DC\JSON\Handler {

    /**
     * Get a list of the classes this handler can serialize.
     *
     * @return string[]
     */
    function getHandledTypes()
    {
        return [
            '\DateTime',
            '\DateTimeImmutable',
            '\DateTimeInterface'
        ];
    }

    /**
     * @param object $object The object to serialize.
     * @return mixed Object or scalar suitable for serialization with json_encode
     */
    function serialize($object)
    {
        /** @var \DateTime $object */
        return $object->format(\DateTime::ISO8601);
    }

    /**
     * Deserialize data to correct type.
     *
     * @param object $data
     * @param string $type
     * @return \DateTimeInterface
     * @throws \DC\JSON\Exceptions\DeserializationException
     */
    function deserialize($data, $type)
    {
        if (is_object($data)) {
            if ($type == '\DateTime') {
                $date = new \DateTime($data->date, new \DateTimeZone($data->timezone));
            } else if ($type == '\DateTimeImmutable' || $type == '\DateTimeInterface') {
                $date = new \DateTimeImmutable($data->date, new \DateTimeZone($data->timezone));
            }
        } elseif (is_string($data)) {
            if ($type == '\DateTime') {
                $date = \DateTime::createFromFormat(\DateTime::ISO8601, $data);
            } elseif ($type == '\DateTimeImmutable' || $type == '\DateTimeInterface') {
                $date = \DateTimeImmutable::createFromFormat(\DateTime::ISO8601, $data);
            }
        }

        if (!isset($date) || $date === false) {
            throw new DeserializationException($data, $type);
        }

        return $date;
    }
}