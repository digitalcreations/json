<?php

namespace DC\Tests\JSON;

class ClassWithProperties {
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;
}

class ClassWithSetters {
    private $id;
    public $usedSetter = false;

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
        $this->usedSetter = true;
    }
}

class ClassWithDateTime {
    /**
     * @var \DateTime
     */
    public $date;
}

class SerializerTest extends \PHPUnit_Framework_TestCase {
    function testDeserializeToPublicProperties() {
        $serializer = new \DC\JSON\Serializer();
        $object = $serializer->deserialize('{"id":1,"name":"Vegard"}', '\DC\Tests\JSON\ClassWithProperties');
        $this->assertInstanceOf('\DC\Tests\JSON\ClassWithProperties', $object);
        $this->assertEquals(1, $object->id);
        $this->assertEquals("Vegard", $object->name);
    }

    function testDeserializeWithSetters() {
        $serializer = new \DC\JSON\Serializer();
        $object = $serializer->deserialize('{"id":1}', '\DC\Tests\JSON\ClassWithSetters');
        $this->assertInstanceOf('\DC\Tests\JSON\ClassWithSetters', $object);
        $this->assertTrue($object->usedSetter);
    }

    function testDeserializeFromDateUsingHandler() {
        $json = '{"date":"2016-01-01T23:12:11+0200"}';
        $serializer = new \DC\JSON\Serializer();
        $object = $serializer->deserialize($json, '\DC\Tests\JSON\ClassWithDateTime');
        $this->assertInstanceOf('\DateTime', $object->date);
    }

    function testDeserializeToArray() {
        $serializer = new \DC\JSON\Serializer();
        $array = $serializer->deserialize('[{"id":1,"name":"Vegard"},{"id":2,"name":"Francis"}]', '\DC\Tests\JSON\ClassWithProperties[]');
        $this->assertTrue(is_array($array));
        $this->assertInstanceOf('\DC\Tests\JSON\ClassWithProperties', $array[0]);
        $this->assertInstanceOf('\DC\Tests\JSON\ClassWithProperties', $array[1]);
    }

    function testDeserializeScalar() {
        $serializer = new \DC\JSON\Serializer();
        $int = $serializer->deserialize('23', 'int');
        $this->assertTrue(is_int($int));
        $this->assertEquals($int, 23);
    }

    function testSerializeScalarValue() {
        $values = [
            "string",
            1,
            1.23,
            true,
            false
        ];

        $serializer = new \DC\JSON\Serializer();
        foreach ($values as $value) {
            $json = $serializer->serialize($value);
            $result = $serializer->deserialize($json);
            $this->assertEquals($value, $result, gettype($value) . " value did not serialize and deserialize to same type");
        }
    }

    function testSerializeUsingDateTimeHandler() {
        $serializer = new \DC\JSON\Serializer();
        $json = $serializer->serialize(new \DateTime('2016-01-05T19:41:00+0200'));
        $this->assertEquals('"2016-01-05T19:41:00+0200"', $json);
    }

    function testSerializeArrayUsingHandler() {
        $serializer = new \DC\JSON\Serializer();
        $json = $serializer->serialize([new \DateTime('2016-01-05T19:41:00+0200'), "string"]);
        $obj = json_decode($json);
        $this->assertTrue(is_array($obj));
        $this->assertEquals('2016-01-05T19:41:00+0200', $obj[0]);
        $this->assertEquals("string", $obj[1]);
    }
}