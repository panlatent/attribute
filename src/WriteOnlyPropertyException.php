<?php

namespace Panlatent\Property;

use Exception;

class WriteOnlyPropertyException extends PropertyException
{
    public function __construct($class, $property, $message = '', $code = 0, Exception $previous = null)
    {
        $message = $message . ' ' . get_class($class) . '::$' . $property;
        parent::__construct($class, $property, $message, $code, $previous);
    }
}