<?php

namespace DC\JSON;

class Serializer {
    /**
     * @var NamingConvention
     */
    private $namingConvention;
    /**
     * @var HandlerRegistry
     */
    private $handlerRegistry;
    /**
     * @var ClassBuilderRegistry
     */
    private $classBuilderRegistry;

    function __construct(NamingConvention $namingConvention = null, HandlerRegistry $handlerRegistry = null, ClassBuilderRegistry $classBuilderRegistry = null)
    {
        if ($namingConvention == null) {
            $namingConvention = new CamelCaseNamingConvention();
        }
        if ($handlerRegistry == null) {
            $handlerRegistry = new HandlerRegistry();

        }
        if ($classBuilderRegistry == null) {
            $classBuilderRegistry = new ClassBuilderRegistry($namingConvention, $handlerRegistry);
        }
        $this->namingConvention = $namingConvention;
        $this->handlerRegistry = $handlerRegistry;
        $this->classBuilderRegistry = $classBuilderRegistry;
    }

    function serialize($object) {
        $object = $this->serializeWalk($object);
        return json_encode($object);
    }

    function deserialize($object, $type = '\stdClass') {
        $default = json_decode($object);
        if ($type != '\stdClass') {
            return $this->mapType($type, $default);
        }
        return $default;
    }

    private function serializeWalk($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        if (is_object($object)) {
            if ($handler = $this->handlerRegistry->findHandler('\\' . get_class($object))) {
                return $handler->serialize($object);
            }
        }

        $isArray = is_array($object);
        foreach ($object as $key => $value) {
            if ($isArray) {
                $object[$key] = $this->serializeWalk($value);
            }
            else {
                $object->$key = $this->serializeWalk($value);
            }
        }

        return $object;
    }

    /**
     * @param string $type
     * @param object $data
     * @return mixed
     */
    private function mapType($type, $data) {
        if (PHPDocHelper::isArrayType($type) && (is_object($data) || is_array($data))) {
            $type = PHPDocHelper::removeArray($type);
            $out = [];
            foreach ($data as $k => $item) {
                $out[$k] = $this->mapType($type, $item);
            }
            return $out;
        }

        if (class_exists($type)) {
            $builder = $this->classBuilderRegistry->getBuilderForClass($type);
            return $builder->build($data);
        }

        return $data;
    }
}