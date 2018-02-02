<?php

namespace Panlatent\Property\Reflection;

use ReflectionMethod;
use ReflectionProperty;

class ReflectionClass extends \ReflectionClass
{
    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return parent::hasProperty($name) || $this->hasAccessor($name);
    }

    /**
     * @param string $name
     * @return ReflectionProperty
     */
    public function getProperty($name)
    {
        if (parent::hasProperty($name)) {
            return parent::getProperty($name);
        }

        return $this->getAccessor($name)->getProperty();
    }

    /**
     * @param int $filter
     * @return array|ReflectionProperty[]
     */
    public function getProperties($filter = null)
    {
        if ($filter === null) {
            $properties = parent::getProperties();
        } else {
            $properties = parent::getProperties($filter);
        }
        if ($filter === null || $filter == ReflectionProperty::IS_PUBLIC) {
            $properties = array_merge_recursive($properties, $this->getAccessorProperties());
        }

        return $properties;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAccessor($name)
    {
        if (! parent::hasProperty('_' . $name)) {
            return false;
        }
        $property = parent::getProperty('_' . $name);

        return $this->isAccessorProperty($property);
    }

    /**
     * @param string $name
     * @return ReflectionAccessor
     */
    public function getAccessor($name)
    {
        return new ReflectionAccessor($this, $name);
    }

    /**
     * @return ReflectionAccessor[]
     */
    public function getAccessors()
    {
        $accessor = [];
        foreach (parent::getProperties(ReflectionMethod::IS_PRIVATE) as $property) {
            if ($this->isAccessorProperty($property)) {
                $name = substr($property->getName(), 1);
                $properties[$name] = new ReflectionAccessor($this, $name);
            }
        }

        return $accessor;
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getAccessorProperties()
    {
        $properties = parent::getProperties(ReflectionProperty::IS_PRIVATE);
        $properties = array_filter($properties, function(ReflectionProperty $property) {
            return $this->isAccessorProperty($property);
        });

        return $properties;
    }

    /**
     * @param ReflectionProperty $property
     * @return bool
     */
    protected function isAccessorProperty($property)
    {
        if (! $property->isPrivate() || $property->getName()[0] != '_') {
            return false;
        }
        $name = substr($property->getName(), 1);
        if ($this->hasMethod('get' . $name)) {
            return true;
        } elseif ($this->hasMethod('set' . $name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    protected function isAccessorMethod(ReflectionMethod $method)
    {
        if (strlen($method->getName()) <= 3) {
            return false;
        } elseif (strncmp($method->getName(), 'get', 3) == 0 ||
            strncmp($method->getName(), 'set', 3) == 0) {
            $name = '_' . substr($method->getName(), 3);
            if (parent::hasProperty($name) && parent::getProperty($name)->isPrivate()) {
                return true;
            }
        }
        return false;
    }
}