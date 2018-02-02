<?php

namespace Panlatent\Property\Reflection;

use ReflectionException;
use ReflectionProperty;

class ReflectionAccessorProperty extends ReflectionProperty
{
    /**
     * ReflectionAccessorProperty constructor.
     *
     * @param mixed $class
     * @param string $name
     * @throws ReflectionException
     */
    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        if (! $this->isPrivate()) {
            throw new ReflectionException('Accessor property accessibility must be set as private: ' . $name);
        } elseif ($name[0] != '_') {
            throw new ReflectionException('Accessor property name must have an underscore prefix:' . $name);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return substr($this->name, 1);
    }

    /**
     * @return string
     */
    public function getDeclareName()
    {
        return parent::getName();
    }

    /**
     * @return ReflectionAccessor
     */
    public function getAccessor()
    {
        return new ReflectionAccessor($this->class, substr($this->name, 1));
    }
}