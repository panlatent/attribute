<?php

namespace Panlatent\Property\Tests\Reflection;

use Panlatent\Property\Reflection\ReflectionAccessor;
use Panlatent\Property\Reflection\ReflectionAccessorProperty;
use Panlatent\Property\Reflection\ReflectionClass;
use ReflectionProperty;

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testHasProperty()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertTrue($class->hasProperty('nickname'));
        $this->assertTrue($class->hasProperty('phone'));
        $this->assertTrue($class->hasProperty('name'));
        $this->assertFalse($class->hasProperty('unknown'));
    }

    public function testGetProperty()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertInstanceOf(ReflectionProperty::class, $class->getProperty('nickname'));
        $this->assertInstanceOf(ReflectionAccessorProperty::class, $class->getProperty('name'));
    }

    public function testGetProperties()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertCount(8, $class->getProperties());
    }

    public function testIsAccessorProperty()
    {

    }

    public function testHasAccessor()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertFalse($class->hasAccessor('nickname'));
        $this->assertFalse($class->hasAccessor('phone'));
        $this->assertTrue($class->hasAccessor('name'));
    }

    public function testGetAccessor()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertInstanceOf(ReflectionAccessor::class, $class->getAccessor('name'));
    }

    public function testGetAccessorProperties()
    {
        $class = new ReflectionClass(_Accessor::class);
        $this->assertCount(3, $class->getAccessorProperties());
    }
}
