<?php

namespace DC\JSON;

class ClassBuilderRegistry {


    /**
     * @var ClassBuilder[]
     */
    private $builders = [];
    /**
     * @var
     */
    private $namingConvention;
    /**
     * @var HandlerRegistry
     */
    private $handlerRegistry;

    function __construct(NamingConvention $namingConvention, HandlerRegistry $handlerRegistry) {
        $this->namingConvention = $namingConvention;
        $this->handlerRegistry = $handlerRegistry;
    }

    public function getBuilderForClass($class) {
        if (!array_key_exists($class, $this->builders)) {
            $this->builders[$class] = new ClassBuilder($class, $this, $this->namingConvention, $this->handlerRegistry);
        }

        return $this->builders[$class];
    }
}