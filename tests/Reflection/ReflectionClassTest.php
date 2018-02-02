<?php

namespace Panlatent\Property\Tests\Reflection;

use Panlatent\Property\Reflection\ReflectionAccessorProperty;
use Panlatent\Property\Reflection\ReflectionClass;

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testHasProperty()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertTrue($class->hasProperty('nickname'));
        $this->assertTrue($class->hasProperty('phone'));
        $this->assertTrue($class->hasProperty('name'));
        $this->assertFalse($class->hasProperty('unknown'));
    }

    public function testGetProperty()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertInstanceOf(\ReflectionProperty::class, $class->getProperty('nickname'));
        $this->assertInstanceOf(ReflectionAccessorProperty::class, $class->getProperty('name'));
    }

    public function testGetProperties()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertCount(6, $class->getProperties());
    }

    public function testHasAccessorProperty()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertFalse($class->hasAccessorProperty('nickname'));
        $this->assertFalse($class->hasAccessorProperty('phone'));
        $this->assertTrue($class->hasAccessorProperty('name'));
    }

    public function testGetAccessorProperty()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertInstanceOf(ReflectionAccessorProperty::class, $class->getAccessorProperty('name'));
    }

    public function testGetAccessorProperties()
    {
        $class = new ReflectionClass(_Property::class);
        $this->assertCount(3, $class->getAccessorProperties());
    }
}
