<?php

namespace Panlatent\Property\Accessor;

use Panlatent\Property\ReadOnlyPropertyException;
use Panlatent\Property\UndefinedPropertyException;

trait Setter
{
    /**
     * @param string $name
     * @param mixed  $value
     * @throws UndefinedPropertyException
     */
    public function __set($name, $value)
    {
        if (property_exists($this, '_' . $name)) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (method_exists($this, 'get' . $name)) {
                throw new ReadOnlyPropertyException($this, $name, 'Cannot setting read-only property');
            }
        }

        throw new UndefinedPropertyException($this, $name);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        if (property_exists($this, '_' . $name)) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter(null);
            } elseif (method_exists($this, 'get' . $name)) {
                throw new ReadOnlyPropertyException($this, $name, 'Cannot unset read-only property');
            }
        }

        throw new UndefinedPropertyException($this, $name);
    }
}