<?php

namespace DC\Tests\JSON;

class HandlerRegistryTest extends \PHPUnit_Framework_TestCase {
    public function testFindHandlerFindsDirectRegistration() {
        $handlerMock = $this->getMock('\DC\JSON\Handler');
        $handlerMock->method('getHandledTypes')
            ->willReturn(['\DateTime', '\DateTimeImmutable']);

        $irrelevantHandlerMock = $this->getMock('\DC\JSON\Handler');
        $irrelevantHandlerMock->method('getHandledTypes')
            ->willReturn(['\Foo']);

        $registry = new \DC\JSON\HandlerRegistry([$handlerMock, $irrelevantHandlerMock]);

        $handler = $registry->findHandler('\DateTime');
        $this->assertEquals($handlerMock, $handler);
    }

    public function testFindHandlerReturnsNullForUnknownType() {
        $handlerMock = $this->getMock('\DC\JSON\Handler');
        $handlerMock->method('getHandledTypes')
            ->willReturn(['\DateTime']);

        $registry = new \DC\JSON\HandlerRegistry([$handlerMock]);
        $handler = $registry->findHandler('\DatePeriod');
        $this->assertNull($handler);
    }

    public function testFindHandlerLocatesImplementedInterface() {
        $handlerMock = $this->getMock('\DC\JSON\Handler');
        $handlerMock->method('getHandledTypes')
            ->willReturn(['\DateTime']);

        $registry = new \DC\JSON\HandlerRegistry([$handlerMock]);

        $handler = $registry->findHandler('\DateTimeInterface');
        $this->assertEquals($handlerMock, $handler);
    }

    /**
     * @expectedException \DC\JSON\Exceptions\MultipleHandlersFoundException
     */
    public function testFindHandlerThrowsWhenMultipleHandlerInterfacesMatch() {
        $handlerMock = $this->getMock('\DC\JSON\Handler');
        $handlerMock->method('getHandledTypes')
            ->willReturn(['\DateTime']);

        $handlerMock2 = $this->getMock('\DC\JSON\Handler');
        $handlerMock2->method('getHandledTypes')
            ->willReturn(['\DateTimeImmutable']);

        $registry = new \DC\JSON\HandlerRegistry([$handlerMock, $handlerMock2]);

        $registry->findHandler('\DateTimeInterface');
    }
}