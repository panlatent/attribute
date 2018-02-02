<?php

namespace Panlatent\Property\Reflection;

use Panlatent\Property\ReadOnlyPropertyException;
use Panlatent\Property\WriteOnlyPropertyException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use Reflection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

class ReflectionAccessor extends Reflection
{
    /**
     * @var ReflectionClass
     */
    protected $class;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var ReflectionProperty
     */
    protected $property;
    /**
     * @var ReflectionMethod
     */
    protected $getter;
    /**
     * @var ReflectionMethod
     */
    protected $setter;
    /**
     * @var array
     */
    protected $types = [];

    /**
     * ReflectionAccessor constructor.
     *
     * @param mixed $class
     * @param string $name
     * @throws ReflectionException
     */
    public function __construct($class, $name)
    {
        if (! $class instanceof ReflectionClass) {
            $class = new ReflectionClass($class);
        }
        $this->class = $class;
        $this->name = $name;
        $this->resolve();
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param object $object
     * @return mixed
     */
    public function getValue($object)
    {
        if (! $this->getter) {
            throw new WriteOnlyPropertyException($object, $this->name);
        }

        return $this->getter->invoke($object);
    }

    /**
     * @param object $object
     * @param mixed  $value
     */
    public function setValue($object, $value)
    {
        if (! $this->setter) {
            throw new ReadOnlyPropertyException($object, $this->name);
        }
        $this->setter->invoke($object, $value);
    }

    /**
     * @return bool
     */
    public function hasGetter()
    {
        return (bool)$this->getter;
    }

    /**
     * @return bool
     */
    public function hasSetter()
    {
        return (bool)$this->setter;
    }

    /**
     * @return ReflectionProperty
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return ReflectionMethod
     */
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * @return ReflectionMethod
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * @return ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function getDocComment()
    {
        $docComment = "@property " . implode('|', $this->types) . ' ' . $this->name;
        if (! empty($this->description)) {
            $docComment .= ' ' . $this->description;
        }

        return $docComment;
    }

    /**
     * Resolve this property.
     */
    private function resolve()
    {
        $this->prepareProperty();
        $this->prepareGetter();
        $this->prepareSetter();
        if (! $this->hasGetter() && ! $this->hasSetter()) {
            throw new ReflectionException('Undefined accessor: not found getter or setter in ' . $this->class->getName());
        }
    }

    private function prepareProperty()
    {
        $property = '_' . $this->name;
        if (! $this->class->hasProperty($property)) {
            throw new ReflectionException('Undefined accessor property: ' . $this->class->getName() . '::$' . $property);
        }
        $this->property = new ReflectionAccessorProperty($this->class->getName(), $property);
        if (! $this->property->isPrivate()) {
            throw new ReflectionException('Invalid accessor property: $' .  $property .
                ' accessibility must be set as private');
        }
        if (! ($docComment = $this->property->getDocComment())) {
            return;
        }
        if (($docBlock = $this->getDocBlock($docComment)) && $docBlock->hasTag('var')) {
            /** @var DocBlock\Tags\Var_[] $vars */
            $vars = $docBlock->getTagsByName('var');
            foreach ($vars as $var) {
                $this->addType($var->getType());
            }
        }
    }

    private function prepareGetter()
    {
        $getter = 'get' . $this->name;
        if ( ! $this->class->hasMethod($getter)) {
            return;
        }
        $this->getter = $this->class->getMethod($getter);
        if (! ($docComment = $this->getter->getDocComment())) {
            return;
        }
        if (($docBlock = $this->getDocBlock($docComment)) && $docBlock->hasTag('return')) {
            /** @var DocBlock\Tags\Return_[] $returns */
            $returns = $docBlock->getTagsByName('return');
            foreach ($returns as $return) {
                $this->addType($return->getType());
            }
        }
    }

    private function prepareSetter()
    {
        $setter = 'set' . $this->name;
        if (! $this->class->hasMethod($setter)) {
            return;
        }
        $this->setter = $this->class->getMethod($setter);
        if (! ($docComment = $this->setter->getDocComment())) {
            return;
        }
        if (($docBlock = $this->getDocBlock($docComment)) && $docBlock->hasTag('param')) {
            /** @var DocBlock\Tags\Param $param */
            $param = $docBlock->getTagsByName('param')[0];
            $this->addType($param->getType());
        }
    }

    private function getDocBlock($docComment)
    {
        $factory = DocBlockFactory::createInstance();
        $context = (new ContextFactory())->createFromReflector($this->class);

        return $factory->create($docComment, $context);
    }

    private function addType(Type $type)
    {
        if ($type instanceof Compound) {
            foreach ((array)explode('|', $type) as $type) {
                if (! in_array($type, $this->types)) {
                    $this->types[] = $type;
                }
            }
        } elseif (! in_array($type, $this->types)) {
            $this->types[] = (string)$type;
        }
    }
}