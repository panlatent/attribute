<?php

namespace Panlatent\Property;

use Panlatent\Property\Accessor\Getter;
use Panlatent\Property\Accessor\Setter;

trait Property
{
    use Getter, Setter;

    public function hasProperty($name)
    {
        return $this->canGetProperty($name) || $this->canSetProperty($name);
    }

    public function canGetProperty($name)
    {
        if (property_exists($this, $name)) {
            return true;
        } elseif (! property_exists($this, '_' . $name)) {
           return false;
        }
        $getter = 'get' . $name;

        return method_exists($this, $getter);
    }

    public function canSetProperty($name)
    {
        if (property_exists($this, $name)) {
            return true;
        } elseif (! property_exists($this, '_' . $name)) {
            return false;
        }
        $setter = 'set' . $name;

        return method_exists($this, $setter);
    }
}