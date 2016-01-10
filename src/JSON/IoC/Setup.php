<?php

namespace \DC\JSON\IoC;

class Setup
{
    public static function setup(\DC\IoC\Container $container) {
        $container->register(function() { return new \DC\JSON\CamelCaseNamingConvention(); })->to('\DC\JSON\NamingConvention')->withContainerLifetime();
        $container->register('\DC\JSON\Handlers\DateTimeHandler')->to('\DC\JSON\Handler')->withContainerLifetime();
        $container->register('\DC\JSON\HandlerRegistry')->withContainerLifetime();
        $container->register('\DC\JSON\ClassBuilderRegistry')->withContainerLifetime();
        $container->register('\DC\JSON\Serializer')->withContainerLifetime();
    }
}