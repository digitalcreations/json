<?php

namespace DC\JSON\Exceptions;

use Exception;

class DeserializationException extends \Exception {
    public function __construct($data, $type, $additionalMessage = "")
    {
        parent::__construct("Could not deserialize " . json_encode($data) . " to $type." . $additionalMessage);
    }

}