<?php

namespace DC\JSON;

class PHPDocHelper {
    static function getDocumentedTypeFromParameter(\ReflectionFunctionAbstract $method, $parameterName) {
        $phpdoc = new \phpDocumentor\Reflection\DocBlock($method);
        $params = $phpdoc->getTagsByName("param");
        $filtered = array_filter($params, function(\phpDocumentor\Reflection\DocBlock\Tag\ParamTag $tag) use ($parameterName) {
            return $tag->getName() == '$'. $parameterName;
        });
        if (count($filtered) > 0) {
            return reset($filtered)->getType();
        }
        return null;
    }

    static function getDocumentedTypeFromProperty(\ReflectionProperty $property) {
        $phpdoc = new \phpDocumentor\Reflection\DocBlock($property);
        $tags = $phpdoc->getTagsByName("var");
        if (count($tags) == 1) {
            /** @var \phpDocumentor\Reflection\DocBlock\Tag\VarTag $tag */
            $tag = reset($tags);
            return $tag->getType();
        }
        return null;
    }

    static function isArrayType($type) {
        return $type[strlen($type) - 2] == '[' && $type[strlen($type) - 1] == ']';
    }

    static function removeArray($type)
    {
        return trim($type, '[]');
    }
}