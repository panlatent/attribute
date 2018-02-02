<?php

namespace Panlatent\Property;

use Exception;
use LogicException;

class PropertyException extends LogicException
{
    /**
     * @var object
     */
    protected $class;
    /**
     * @var string
     */
    protected $property;

    /**
     * PropertyException constructor.
     *
     * @param object         $class
     * @param string         $property
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($class, $property, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->class = $class;
        $this->property = $property;
    }

    /**
     * @return object
     */
    final public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    final public function getProperty()
    {
        return $this->property;
    }
}