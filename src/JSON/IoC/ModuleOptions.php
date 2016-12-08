<?php

namespace DC\JSON\IoC;

class ModuleOptions
{
    /**
     * @var string|\DC\JSON\NamingConvention
     */
    private $namingConvention;

    public function __construct()
    {
        $this->namingConvention = '\DC\JSON\CamelCaseNamingConvention';
    }

    /**
     * @return string|\DC\JSON\NamingConvention
     */
    public function getNamingConvention()
    {
        return $this->namingConvention;
    }

    /**
     * @param string $namingConvention Name of naming convention, or an object that implements that interface.
     */
    public function setNamingConvention($namingConvention)
    {
        if (!class_exists($namingConvention) && !($namingConvention instanceof \DC\JSON\NamingConvention)) {
            $this->namingConvention = $namingConvention;
        }
        else {
            throw new \InvalidArgumentException("Naming convention was not a valid class name or instance of NamingConvention");
        }
    }
}