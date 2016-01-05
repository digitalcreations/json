<?php

namespace DC\Tests\JSON;

class CamelCaseNamingConventionTest extends \PHPUnit_Framework_TestCase {
    function testIsGetter() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();

        $notGetters = [
            "get",
            "GetValue",
            "get_value",
            "setValue",
            "getvalue"
        ];

        foreach ($notGetters as $name) {
            $this->assertFalse($convention->isGetter($name), "$name should not be a getter");
        }

        $getters = [
            "getValue",
            "getValueForSomeLongName"
        ];

        foreach ($getters as $name) {
            $this->assertTrue($convention->isGetter($name), "$name should be a getter");
        }
    }

    function testIsSetter() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();

        $notGetters = [
            "set",
            "SetValue",
            "set_value",
            "setvalue",
            "getValue"
        ];

        foreach ($notGetters as $name) {
            $this->assertFalse($convention->isSetter($name), "$name should not be a setter");
        }

        $getters = [
            "setValue",
            "setValueForSomeLongName"
        ];

        foreach ($getters as $name) {
            $this->assertTrue($convention->isSetter($name), "$name should be a setter");
        }
    }

    function testPropertyNameFromGetterName() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();

        $this->assertEquals("value", $convention->propertyNameFromGetterName("getValue"));
        $this->assertEquals("fooBar", $convention->propertyNameFromGetterName("getFooBar"));
    }

    function testPropertyNameFromSetterName() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();

        $this->assertEquals("value", $convention->propertyNameFromSetterName("setValue"));
        $this->assertEquals("fooBar", $convention->propertyNameFromSetterName("setFooBar"));
    }

    function testGetterNameFromPropertyName() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();
        $this->assertEquals("getValue", $convention->getterNameFromPropertyName("value"));
        $this->assertEquals("getFooBar", $convention->getterNameFromPropertyName("fooBar"));
    }

    function testSetterNameFromPropertyName() {
        $convention = new \DC\JSON\CamelCaseNamingConvention();
        $this->assertEquals("setValue", $convention->setterNameFromPropertyName("value"));
        $this->assertEquals("setFooBar", $convention->setterNameFromPropertyName("fooBar"));
    }
}