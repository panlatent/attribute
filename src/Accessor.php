<?php

namespace Panlatent\Property;

use Panlatent\Property\Accessor\Getter;
use Panlatent\Property\Accessor\Setter;
use Panlatent\Property\Reflection\ReflectionAccessor;
use Panlatent\Property\Reflection\ReflectionClass;

trait Accessor
{
    use Getter, Setter;

    public function hasAccessor($name)
    {
        $class = new ReflectionClass($this);

        return $class->hasAccessor($name);
    }

    public function canGetAccessor($name)
    {
        $accessor = new ReflectionAccessor($this, $name);

        return $accessor->hasGetter();
    }

    public function canSetAccessor($name)
    {
        $accessor = new ReflectionAccessor($this, $name);

        return $accessor->hasSetter();
    }
}