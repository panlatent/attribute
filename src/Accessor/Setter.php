<?php

namespace Panlatent\Property\Accessor;

use Panlatent\Property\ReadOnlyPropertyException;
use Panlatent\Property\UndefinedPropertyException;

trait Setter
{
    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (property_exists($this, '_' . $name)) {
            $getter = 'get' . $name;
            if (method_exists($this, $getter)) {
                return $this->$getter() !== null;
            }
        }
        if (! method_exists(get_parent_class(), '__isset')) {
            return false;
        }

        return parent::__isset($name);
    }

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
                return;
            } elseif (method_exists($this, 'get' . $name)) {
                throw new ReadOnlyPropertyException($this, $name, 'Cannot setting read-only property');
            }
        }
        if (! method_exists(get_parent_class(), '__set')) {
            throw new UndefinedPropertyException($this, $name);
        }

        parent::__set($name, $value);
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
                return;
            } elseif (method_exists($this, 'get' . $name)) {
                throw new ReadOnlyPropertyException($this, $name, 'Cannot unset read-only property');
            }
        }
        if (! method_exists(get_parent_class(), '__unset')) {
            throw new UndefinedPropertyException($this, $name);
        }

        parent::__unset($name);
    }
}