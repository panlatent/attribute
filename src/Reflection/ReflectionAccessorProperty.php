<?php

namespace Panlatent\Property\Reflection;

use Panlatent\Property\ReadOnlyPropertyException;
use Panlatent\Property\WriteOnlyPropertyException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class ReflectionAccessorProperty extends ReflectionProperty
{
    protected $virtul;
    /**
     * @var object
     */
    protected $object;
    /**
     * @var bool
     */
    protected $readable;
    /**
     * @var bool
     */
    protected $writable;
    /**
     * @var array
     */
    protected $types = [];
    /**
     * @var string
     */
    protected $description = '';

    public function __construct($class, $name)
    {
        if (! $class instanceof ReflectionClass) {
            if (is_object($class)) {
                $this->object = $class;
            }
            $class = new ReflectionClass($class);
        }

        $shadow = '_' . $name;
        if (! $class->hasProperty($shadow)) {
            throw new ReflectionException('Accessor property must exists a shadow property: _' . $name);
        } elseif (! $class->getProperty($shadow)->isPrivate()) {
            throw new ReflectionException('Accessor shadow property accessibility must be private');
        }
        
        parent::__construct($class, $shadow);
        $this->resolve();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->virtual;
    }

    /**
     * @param object $object
     * @return mixed
     * @throws ReflectionException
     */
    public function getValue($object = null)
    {
        if (! $this->readable) {
            throw new WriteOnlyPropertyException($object, $this->virtual);
        }
        if ($object === null) {
            if ($this->object !== null) {
                $object = $this->object;
            } else {
                throw new ReflectionException('Not resolve reflection class object');
            }
        }
        $getter = 'get' . $this->virtual;

        return $object->$getter();
    }

    /**
     * @param object $object
     * @param mixed  $value
     * @throws ReflectionException
     */
    public function setValue($object = null, $value = null)
    {
        if (! $this->writable) {
            throw new ReadOnlyPropertyException($object, $this->virtual);
        }
        if ($object === null) {
            if ($this->object !== null) {
                $object = $this->object;
            } else {
                throw new ReflectionException('Not resolve reflection class object');
            }
        }

        $setter = 'set' . $this->virtual;
        $object->$setter($value);
    }

    /**
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @return ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->owner;
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
        $docComment = "/**\n * @var " . implode('|', $this->types);
        if (! empty($this->description)) {
            $docComment .= ' ' . $this->description;
        }
        $docComment .= "\n */\n";

        return $docComment;
    }

    /**
     * Resolve this property.
     */
    private function resolve()
    {
        $getter = 'get' . $this->virtual;
        if (($this->readable = $this->owner->hasMethod($getter))) {
            if (($docBlock = $this->getDocBlock($getter)) && $docBlock->hasTag('return')) {
                $returns = $docBlock->getTagsByName('return');
                foreach ($returns as $return) {
                    /** @var DocBlock\Tags\Return_ $return */
                    $this->addType($return->getType());
                }
            }
        }
        $setter = 'set' . $this->virtual;
        if (($this->writable = $this->owner->hasMethod($setter))) {
            if (($docBlock = $this->getDocBlock($setter)) && $docBlock->hasTag('param')) {
                /** @var DocBlock\Tags\Param $param */
                $param = $docBlock->getTagsByName('param')[0];
                $this->addType($param->getType());
            }
        }
        if (!$this->readable && !$this->writable) {
            throw new ReflectionException('Undefined accessor property: ' . $this->virtual);
        }
    }

    private function getDocBlock($method)
    {
        if (! $docComment = $this->owner->getMethod($method)->getDocComment()) {
            return false;
        }
        $factory = DocBlockFactory::createInstance();
        $context = (new ContextFactory())->createFromReflector($this->owner);

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