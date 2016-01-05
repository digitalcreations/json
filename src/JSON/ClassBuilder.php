<?php

namespace DC\JSON;

/**
 * Internal class used for storing the configuration when building a class.
 *
 * @internal
 * @package DC\JSON
 */
class ClassBuilderConfiguration {
    public $typeMapping = [];

    /**
     * @var string[]
     */
    public $setters = [];

    /**
     * @var string[]
     */
    public $properties = [];

    /**
     * @var string[]
     */
    public $constructorParameters = [];
}

class ClassBuilder {
    /**
     * @var string Name of the class.
     */
    private $class;

    /**
     * @var \ReflectionClass Reflection
     */
    private $reflection;

    /**
     * @var ClassBuilderRegistry
     */
    private $registry;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var HandlerRegistry
     */
    private $handlerRegistry;

    function __construct($class, ClassBuilderRegistry $registry, NamingConvention $namingConvention, HandlerRegistry $handlerRegistry, \DC\Cache\ICache $cache = null)
    {
        $this->class = $class;
        $this->reflection = new \ReflectionClass($class);
        $this->registry = $registry;
        $this->handlerRegistry = $handlerRegistry;

        $this->configuration = new ClassBuilderConfiguration();

        if ($cache != null) {
            $this->configuration = $cache->getWithFallback("classbuilder_" . $class, function () use ($namingConvention) {
                $this->buildConfiguration($namingConvention);
                return $this->configuration;
            });
        }
        else {
            $this->buildConfiguration($namingConvention);
        }
    }

    private function buildConfiguration(NamingConvention $namingConvention) {
        $this->extractConstructor();
        $this->extractSetters($namingConvention);
        $this->extractProperties();
    }

    public function build($data) {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->configuration->typeMapping)) {
                $class = $this->configuration->typeMapping[$key];
                if ($handler = $this->handlerRegistry->findHandler($class)) {
                    $data->$key = $handler->deserialize($data->$key, $class);
                }
                else {
                    $builder = $this->registry->getBuilderForClass($class);
                    $data->$key = $builder->build($value);
                }
            }
        }

        $instance = new $this->class(...array_map(function($m) use ($data) { return $data->$m; }, $this->configuration->constructorParameters));

        foreach ($this->configuration->setters as $parameterName => $setterName) {
            $instance->$setterName($data->$parameterName);
        }

        foreach ($this->configuration->properties as $propertyName) {
            if (isset($data->$propertyName)) {
                $instance->$propertyName = $data->$propertyName;
            }
        }

        return $instance;
    }

    private function addTypeMap($name, $class) {
        if (class_exists($class)) {
            $this->configuration->typeMapping[$name] = $class;
        }
    }

    /**
     * @param NamingConvention $namingConvention
     */
    private function extractSetters(NamingConvention $namingConvention)
    {
        $methods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodName = $method->getName();
            if ($namingConvention->isSetter($methodName)) {
                $propertyName = $namingConvention->propertyNameFromGetterName($methodName);
                if (in_array($propertyName, $this->configuration->constructorParameters)) {
                    continue;
                }
                if ($method->getNumberOfRequiredParameters() !== 1) {
                    continue;
                }
                $parameters = $method->getParameters();
                /** @var \ReflectionParameter $valueParameter */
                $valueParameter = reset($parameters);
                $valueClass = $valueParameter->getClass();
                if ($valueClass != null) {
                    $this->addTypeMap($propertyName, $valueClass->getName());
                } else {
                    $type = PHPDocHelper::getDocumentedTypeFromParameter($method, $propertyName);
                    if ($type != null) {
                        $this->addTypeMap($propertyName, $type);
                    }
                }
                $this->configuration->setters[$propertyName] = $methodName;
            }
        }
    }

    private function extractConstructor()
    {
        $constructor = $this->reflection->getConstructor();
        if ($constructor != null) {
            $constructorPhpdoc = new \phpDocumentor\Reflection\DocBlock($constructor);
            /** @var \phpDocumentor\Reflection\DocBLock\Tag\ParamTag[] $params */
            $params = $constructorPhpdoc->getTagsByName("param");

            foreach ($params as $param) {
                $this->addTypeMap($param->getName(), $param->getType());
            }

            $constructorParameters = $constructor->getParameters();
            foreach ($constructorParameters as $parameter) {
                $parameterName = $parameter->getName();
                $parameterClass = $parameter->getClass();
                if ($parameterClass != null) {
                    $this->addTypeMap($parameterName, $parameterClass->getName());
                }
                $this->configuration->constructorParameters[] = $parameterName;
            }
        }
    }

    private function extractProperties()
    {
        $properties = $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if (in_array($propertyName, $this->configuration->constructorParameters)
                || array_key_exists($propertyName, $this->configuration->setters)) {
                continue;
            }

            $type = PHPDocHelper::getDocumentedTypeFromProperty($property);
            if ($type != null) {
                $this->addTypeMap($propertyName, $type);
            }

            $this->configuration->properties[] = $propertyName;
        }
    }
}