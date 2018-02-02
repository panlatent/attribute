<?php

namespace Panlatent\Property;

use Exception;

class UndefinedPropertyException extends PropertyException
{
    public function __construct($class, $property, $code = 0, Exception $previous = null)
    {
        $message = 'Undefined property ' . get_class($class) . '::$' . $property;
        parent::__construct($class, $property, $message, $code, $previous);
    }
}