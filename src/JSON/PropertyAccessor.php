<?php

namespace DC\JSON;

class PropertyAccessor {
    /**
     * @var callable
     */
    private $getter;
    /**
     * @var callable
     */
    private $setter;
    /**
     * @var string
     */
    private $type;

    public function getValue($object) {
        return call_user_func($this->getter, $object);
    }

    public function setValue($object, $value) {
        call_user_func($this->setter, $object, $value);
    }

    public function setSetter(callable $setter) {
        $this->setter = $setter;
    }

    public function setGetter(callable $getter) {
        $this->getter = $getter;
    }

    public function setType($type) {
        if ($type != null) {
            $this->type = $type;
        }
    }

    public function getType() {
        return $this->type;
    }
}