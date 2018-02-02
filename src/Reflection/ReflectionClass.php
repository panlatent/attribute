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
        return parent::hasProperty($name) || $this->hasAccessorProperty($name);
    }

    /**
     * @param string $name
     * @return ReflectionAccessorProperty|ReflectionProperty
     */
    public function getProperty($name)
    {
        if (parent::hasProperty($name)) {
            return parent::getProperty($name);
        }

        return $this->getAccessorProperty($name);
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
    public function hasAccessorProperty($name)
    {
        if (parent::hasMethod('get' . $name)) {
            return true;
        } elseif (parent::hasMethod('set' . $name)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @return ReflectionAccessorProperty
     */
    public function getAccessorProperty($name)
    {
        return new ReflectionAccessorProperty($this, $name);
    }

    /**
     * @return ReflectionAccessorProperty[]
     */
    public function getAccessorProperties()
    {
        $properties = [];
        foreach (parent::getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->isAccessorMethod($method)) {
                $name  = lcfirst(substr($method->getName(), 3));
                if (! isset($properties[$name])) {
                    $properties[$name] = new ReflectionAccessorProperty($this, $name);
                }
            }
        }

        return array_values($properties);
    }

    public function getAccessorDocComment()
    {
        $lines = [];
        foreach ($this->getAccessorProperties() as $property) {
            $docComment = ' * @property ' . implode('|', $property->getTypes());
            if ($this->hasProperty('_' . $property->getName())) {
                $docComment .= ' ' . $property->description;
            }
            $docComment .= "\n"

            $lines[] = $property->getDocComment();
        }
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    private function isAccessorMethod(ReflectionMethod $method)
    {
        if (strlen($method->getName()) <= 3) {
            return false;
        } elseif (strncmp($method->getName(), 'get', 3) == 0) {
            return true;
        } elseif (strncmp($method->getName(), 'set', 3) == 0) {
            return true;
        }
        return false;
    }
}