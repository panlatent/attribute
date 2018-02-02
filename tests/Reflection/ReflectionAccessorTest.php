<?php

namespace Panlatent\Property\Tests\Reflection;

use Panlatent\Property\Reflection\ReflectionAccessor;
use PHPUnit_Framework_TestCase;

class ReflectionAccessorTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorException()
    {
        $this->setExpectedException('ReflectionException');
        new ReflectionAccessor(_Accessor::class, 'unknown');
    }

    public function testGetName()
    {
        $accessor = new ReflectionAccessor(_Accessor::class, 'name');
        $this->assertEquals('name', $accessor->getName());
    }

    public function testGetValue()
    {
        $property = new _Accessor();
        $property->setName('theValue');
        $accessor = new ReflectionAccessor($property, 'name');
        $this->assertEquals('theValue', $accessor->getValue($property));
    }

    public function testSetValue()
    {
        $property = new _Accessor();
        $accessor = new ReflectionAccessor($property, 'name');
        $accessor->setValue($property, 'theValue');
        $this->assertAttributeEquals('theValue', '_name', $property);
    }

    public function testHasGetter()
    {
        $accessor = new ReflectionAccessor(_Accessor::class, 'name');
        $this->assertTrue($accessor->hasGetter());
        $accessor = new ReflectionAccessor(_Accessor::class, 'age');
        $this->assertTrue($accessor->hasGetter());
        $accessor = new ReflectionAccessor(_Accessor::class, 'number');
        $this->assertFalse($accessor->hasGetter());
    }

    public function testHasSetter()
    {
        $accessor = new ReflectionAccessor(_Accessor::class, 'name');
        $this->assertTrue($accessor->hasSetter());
        $accessor = new ReflectionAccessor(_Accessor::class, 'age');
        $this->assertFalse($accessor->hasSetter());
        $accessor = new ReflectionAccessor(_Accessor::class, 'number');
        $this->assertTrue($accessor->hasSetter());
    }

    public function testGetDeclaringClass()
    {
        $accessor = new ReflectionAccessor(_Accessor::class, 'name');
        $this->assertInstanceOf('ReflectionClass', $accessor->getDeclaringClass());
    }

    public function testGetDocComment()
    {
        $accessor = new ReflectionAccessor(_Accessor::class, 'name');
        $this->assertEquals('@property string|array|\Panlatent\Property\Accessor\Getter|int name',
            $accessor->getDocComment());
    }
}
