<?php

namespace DC\JSON;

class CamelCaseNamingConvention implements NamingConvention {
    /**
     * @var string
     */
    private $setterPrefix;
    /**
     * @var string
     */
    private $getterPrefix;

    function __construct($setterPrefix = "set", $getterPrefix = "get")
    {
        $this->setterPrefix = $setterPrefix;
        $this->getterPrefix = $getterPrefix;
    }

    /**
     * @param string $name Name of a method
     * @return bool True if this is the name of a getter
     */
    function isGetter($name)
    {
        $prefixLength = strlen($this->getterPrefix);
        return strpos($name, $this->getterPrefix) === 0 // should start with prefix
            && strlen($name) > $prefixLength // be at least one character longer than prefix
            && strpos($name, '_') === false // does not contain underscore
            && strtoupper($name[$prefixLength]) == $name[$prefixLength]; // first character of property name should be capitalized
    }

    /**
     * @param string $name The name of a getter method.
     * @return string The name of the property.
     */
    function propertyNameFromGetterName($name)
    {
        return lcfirst(substr($name, strlen($this->getterPrefix)));
    }

    /**
     * @param string $name Name of a method.
     * @return bool True if this is the name of a setter.
     */
    function isSetter($name)
    {
        $prefixLength = strlen($this->setterPrefix);
        return strpos($name, $this->setterPrefix) === 0 // should start with prefix
            && strlen($name) > $prefixLength // be at least one character longer than prefix
            && strpos($name, '_') === false // does not contain underscore
            && strtoupper($name[$prefixLength]) == $name[$prefixLength]; // first character of property name should be capitalized
    }

    /**
     * @param string $name The name of a setter method.
     * @return string The name of a the property.
     */
    function propertyNameFromSetterName($name)
    {
        return lcfirst(substr($name, strlen($this->setterPrefix)));
    }

    /**
     * @param string $name The name of a property.
     * @return string The name of the setter.
     */
    function setterNameFromPropertyName($name)
    {
        return $this->setterPrefix . ucfirst($name);
    }

    /**
     * @param string $name The name of a property.
     * @return string The name of the getter.
     */
    function getterNameFromPropertyName($name)
    {
        return $this->getterPrefix . ucfirst($name);
    }
}