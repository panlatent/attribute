<?php

namespace Panlatent\Property\Accessor;

use Panlatent\Property\UndefinedPropertyException;
use Panlatent\Property\WriteOnlyPropertyException;

trait Getter
{
    /**
     * @param string $name
     * @return mixed
     * @throws UndefinedPropertyException
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new WriteOnlyPropertyException($this, $name, 'Cannot getting write-only protected property');
        }
        throw new UndefinedPropertyException($this, $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }

        return false;
    }
}