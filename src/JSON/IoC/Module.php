<?php

namespace DC\JSON\IoC;

class Module extends \DC\IoC\Modules\Module {
    /**
     * @var array|\string[]
     */
    private $controllers;
    /**
     * @var ModuleOptions
     */
    private $options;

    public function __construct(ModuleOptions $options = null)
    {
        parent::__construct("dc/json", ["dc/cache"]);
        if (isset($options)) {
            $this->options = $options;
        }
        else {
            $this->options = new ModuleOptions();
        }
    }

    /**
     * @param \DC\IoC\Container $container
     * @return null
     */
    function register(\DC\IoC\Container $container)
    {
        $namingConvention = $this->options->getNamingConvention();
        if (is_object($namingConvention)) {
            $container->register($namingConvention)->to('\DC\JSON\NamingConvention');
        }
        else {
            $container->register($namingConvention)->to('\DC\JSON\NamingConvention')->withContainerLifetime();
        }
        $container->register('\DC\JSON\Handlers\DateTimeHandler')->to('\DC\JSON\Handler')->withContainerLifetime();
        $container->register('\DC\JSON\HandlerRegistry')->withContainerLifetime();
        $container->register('\DC\JSON\ClassBuilderRegistry')->withContainerLifetime();
        $container->register('\DC\JSON\Serializer')->withContainerLifetime();
    }
}