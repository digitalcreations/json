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
    /**
     * @var \DC\Cache\ICache
     */
    private $cache;

    function __construct(NamingConvention $namingConvention, HandlerRegistry $handlerRegistry, \DC\Cache\ICache $cache = null) {
        $this->namingConvention = $namingConvention;
        $this->handlerRegistry = $handlerRegistry;
        $this->cache = $cache;
    }

    public function getBuilderForClass($class) {
        if (!array_key_exists($class, $this->builders)) {
            $this->builders[$class] = new ClassBuilder($class, $this, $this->namingConvention, $this->handlerRegistry, $this->cache);
        }

        return $this->builders[$class];
    }
}