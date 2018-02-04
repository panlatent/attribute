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
        if (property_exists($this, '_' . $name)) {
            $getter = 'get' . $name;
            if (method_exists($this, $getter)) {
                return $this->$getter();
            } elseif (method_exists($this, 'set' . $name)) {
                throw new WriteOnlyPropertyException($this, $name, 'Cannot getting write-only protected property');
            }
        }
        if (! method_exists(get_parent_class(), '__get')) {
            throw new UndefinedPropertyException($this, $name);
        }

        return parent::__get($name);
    }
}