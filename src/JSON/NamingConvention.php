<?php

namespace DC\JSON;

interface NamingConvention {
    /**
     * @param string $name Name of a method
     * @return bool True if this is the name of a getter
     */
    function isGetter($name);

    /**
     * @param string $name The name of a getter method.
     * @return string The name of the property.
     */
    function propertyNameFromGetterName($name);

    /**
     * @param string $name Name of a method.
     * @return bool True if this is the name of a setter.
     */
    function isSetter($name);

    /**
     * @param string $name The name of a setter method.
     * @return string The name of a the property.
     */
    function propertyNameFromSetterName($name);

    /**
     * @param string $name The name of a property.
     * @return string The name of the setter.
     */
    function setterNameFromPropertyName($name);

    /**
     * @param string $name The name of a property.
     * @return string The name of the getter.
     */
    function getterNameFromPropertyName($name);
}