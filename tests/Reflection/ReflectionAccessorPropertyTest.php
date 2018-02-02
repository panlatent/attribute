<?php

namespace Panlatent\Property\Tests\Reflection;

use Panlatent\Property\Reflection\ReflectionAccessorProperty;
use PHPUnit_Framework_TestCase;

class ReflectionAccessorPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorException()
    {
        $this->setExpectedException('ReflectionException');
        new ReflectionAccessorProperty(_Property::class, 'unknown');
    }

    public function testGetName()
    {
        $ap = new ReflectionAccessorProperty(_Property::class, 'name');
        $this->assertEquals('name', $ap->getName());
    }

    public function testGetValue()
    {
        $property = new _Property();
        $property->setName('theValue');
        $ap = new ReflectionAccessorProperty($property, 'name');
        $this->assertEquals('theValue', $ap->getValue($property));
    }

    public function testSetValue()
    {
        $property = new _Property();
        $ap = new ReflectionAccessorProperty($property, 'name');
        $ap->setValue($property, 'theValue');
        $this->assertAttributeEquals('theValue', '_name', $property);
    }

    public function testIsReadable()
    {
        $ap = new ReflectionAccessorProperty(_Property::class, 'name');
        $this->assertTrue($ap->isReadable());
        $ap = new ReflectionAccessorProperty(_Property::class, 'age');
        $this->assertTrue($ap->isReadable());
        $ap = new ReflectionAccessorProperty(_Property::class, 'number');
        $this->assertFalse($ap->isReadable());
    }

    public function testIsWritable()
    {
        $ap = new ReflectionAccessorProperty(_Property::class, 'name');
        $this->assertTrue($ap->isWritable());
        $ap = new ReflectionAccessorProperty(_Property::class, 'age');
        $this->assertFalse($ap->isWritable());
        $ap = new ReflectionAccessorProperty(_Property::class, 'number');
        $this->assertTrue($ap->isWritable());
    }

    public function testGetDeclaringClass()
    {
        $ap = new ReflectionAccessorProperty(_Property::class, 'name');
        $this->assertInstanceOf('ReflectionClass', $ap->getDeclaringClass());
    }

    public function testGetDocComment()
    {
        $ap = new ReflectionAccessorProperty(_Property::class, 'name');

        $this->assertEquals('@property string|array|\Panlatent\Property\Accessor\Getter|int', $ap->getDocComment());
    }
}
