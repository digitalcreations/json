<?php

namespace DC\Tests\JSON;


use DC\JSON\CamelCaseNamingConvention;
use DC\JSON\HandlerRegistry;

class Constructor {
    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

class ConstructorWithTypeInformation {
    function __construct($id, Constructor $child) {
        $this->id = $id;
        $this->child = $child;
    }
}

class ConstructorWithExtraSetter extends Constructor {
    function setAge($age) {
        $this->age = $age;
    }
}

class Property {
    public $id;
}

class ClassBuilderTest extends \PHPUnit_Framework_TestCase {
    function testConstructorBuilding() {
        $registry = new \DC\JSON\ClassBuilderRegistry(new CamelCaseNamingConvention(), new HandlerRegistry());
        $builder = $registry->getBuilderForClass('\DC\Tests\JSON\Constructor');
        $data = (object)(['id' => 1, 'name' => "vegard"]);
        $instance = $builder->build($data);
        $this->assertInstanceOf('\DC\Tests\JSON\Constructor', $instance);
        $this->assertEquals(1, $instance->id);
        $this->assertEquals("vegard", $instance->name);
    }

    function testConstructorBuildingWithClassDependency() {
        $registry = new \DC\JSON\ClassBuilderRegistry(new CamelCaseNamingConvention(), new HandlerRegistry());
        $builder = $registry->getBuilderForClass('\DC\Tests\JSON\ConstructorWithTypeInformation');
        $data = (object)(['id' => 1, 'child' => (object)["id" => 2, "name" => "vegard"]]);
        $instance = $builder->build($data);

        $this->assertInstanceOf('\DC\Tests\JSON\ConstructorWithTypeInformation', $instance);
        $this->assertInstanceOf('\DC\Tests\JSON\Constructor', $instance->child);
        $this->assertEquals(1, $instance->id);
        $this->assertEquals(2, $instance->child->id);
        $this->assertEquals("vegard", $instance->child->name);
    }

    function testConstructorWithSetter() {
        $registry = new \DC\JSON\ClassBuilderRegistry(new CamelCaseNamingConvention(), new HandlerRegistry());
        $builder = $registry->getBuilderForClass('\DC\Tests\JSON\ConstructorWithExtraSetter');
        $data = (object)(['id' => 1, 'name' => "vegard", "age" => 31]);
        $instance = $builder->build($data);
        $this->assertInstanceOf('\DC\Tests\JSON\ConstructorWithExtraSetter', $instance);
        $this->assertEquals(1, $instance->id);
        $this->assertEquals("vegard", $instance->name);
        $this->assertEquals(31, $instance->age);
    }

    function testProperty() {
        $registry = new \DC\JSON\ClassBuilderRegistry(new CamelCaseNamingConvention(), new HandlerRegistry());
        $builder = $registry->getBuilderForClass('\DC\Tests\JSON\Property');
        $instance = $builder->build((object)['id' => 1]);
        $this->assertInstanceOf('\DC\Tests\JSON\Property', $instance);
        $this->assertEquals(1, $instance->id);

    }

}