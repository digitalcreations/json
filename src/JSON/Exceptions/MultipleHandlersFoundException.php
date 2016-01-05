<?php

namespace DC\JSON\Exceptions;

class MultipleHandlersFoundException extends \Exception {
    public function __construct($type, array $handlers)
    {
        parent::__construct("Found multiple handlers for " . $type . ': [' . implode(', ', $handlers) . ']');
    }

}