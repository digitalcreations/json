<?php

namespace DC\Tests\JSON\IoC;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    function testModuleWithoutSetup() {
        $container = new \DC\IoC\Container();
        $container->registerModules([new \DC\JSON\IoC\Module(), new \DC\Cache\Module()]);
        $serializer = $container->resolve('\DC\JSON\Serializer');
        $this->assertInstanceOf('\DC\JSON\Serializer', $serializer);
    }
}
