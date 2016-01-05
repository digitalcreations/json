<?php

namespace DC\Tests\JSON\Handlers;

class DateTimeHandlerTest extends \PHPUnit_Framework_TestCase {
    public function testSerialize() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $date = new \DateTime('2016-01-01 00:00:00', new \DateTimeZone('UTC'));
        $string = $handler->serialize($date);
        $this->assertEquals('2016-01-01T00:00:00+0000', $string);
    }

    public function testDeserializeWithStringInputToImmutable() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $input = '2016-01-01T00:00:00-0200';

        $date = $handler->deserialize($input, '\DateTimeImmutable');

        $this->assertInstanceOf('\DateTimeImmutable', $date);
        $this->assertEquals(-7200 /* 2 hours in seconds */, $date->getOffset());
        $this->assertEquals($input, $date->format(\DateTime::ISO8601));
    }

    public function testDeserializeWithStringInput() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $input = '2016-01-01T00:00:00+0200';

        $date = $handler->deserialize($input, '\DateTime');

        $this->assertInstanceOf('\DateTime', $date);
        $this->assertEquals(7200 /* 2 hours in seconds */, $date->getOffset());
        $this->assertEquals($input, $date->format(\DateTime::ISO8601));
    }

    public function testDeserializeWithStringInputToInterface() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $input = '2016-01-01T00:00:00+0200';

        $date = $handler->deserialize($input, '\DateTimeInterface');

        $this->assertInstanceOf('\DateTimeInterface', $date);
        $this->assertEquals(7200 /* 2 hours in seconds */, $date->getOffset());
        $this->assertEquals($input, $date->format(\DateTime::ISO8601));
    }

    public function testDeserializeWithArrayInputToImmutable() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $json = '{"date":"2016-01-03 08:15:43.000000","timezone_type":3,"timezone":"GMT+2"}';
        $date = $handler->deserialize(json_decode($json), '\DateTimeImmutable');

        $this->assertInstanceOf('\DateTimeImmutable', $date);
        $this->assertEquals('2016-01-03T08:15:43+0200', $date->format(\DateTime::ISO8601));
        $this->assertEquals(7200, $date->getOffset());
    }

    public function testDeserializeWithArrayInput() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $json = '{"date":"2016-01-03 08:15:43.000000","timezone_type":3,"timezone":"GMT+2"}';
        $date = $handler->deserialize(json_decode($json), '\DateTime');

        $this->assertInstanceOf('\DateTime', $date);
        $this->assertEquals('2016-01-03T08:15:43+0200', $date->format(\DateTime::ISO8601));
        $this->assertEquals(7200, $date->getOffset());
    }

    public function testDeserializeWithArrayInputToInterface() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $json = '{"date":"2016-01-03 08:15:43.000000","timezone_type":3,"timezone":"GMT+2"}';
        $date = $handler->deserialize(json_decode($json), '\DateTimeInterface');

        $this->assertInstanceOf('\DateTimeInterface', $date);
        $this->assertEquals('2016-01-03T08:15:43+0200', $date->format(\DateTime::ISO8601));
        $this->assertEquals(7200, $date->getOffset());
    }

    /**
     * @expectedException \DC\JSON\Exceptions\DeserializationException
     */
    public function testThrowsOnUnknownData() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $handler->deserialize(1, '\DateTime');
    }

    /**
     * @expectedException \DC\JSON\Exceptions\DeserializationException
     */
    public function testThrowsOnUnknownString() {
        $handler = new \DC\JSON\Handlers\DateTimeHandler();
        $handler->deserialize("foo", '\DateTime');
    }
}