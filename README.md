Property
========
[![Build Status](https://travis-ci.org/panlatent/property.svg)](https://travis-ci.org/panlatent/property)
[![Coverage Status](https://coveralls.io/repos/github/panlatent/property/badge.svg?branch=master)](https://coveralls.io/github/panlatent/property?branch=master)
[![Latest Stable Version](https://poser.pugx.org/panlatent/property/v/stable.svg)](https://packagist.org/packages/panlatent/property)
[![Total Downloads](https://poser.pugx.org/panlatent/property/downloads.svg)](https://packagist.org/packages/panlatent/property) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/property/v/unstable.svg)](https://packagist.org/packages/panlatent/property)
[![License](https://poser.pugx.org/panlatent/property/license.svg)](https://packagist.org/packages/panlatent/property)

Property is a class properties access control DSL for PHP (getter and setter traits).

What's This
===========
在面向对象编程（ OOP ）中，Getter 和 Setter 有着很重要的作用，PHP 本身没有直接提供 Getter／Setter 语法。通常，我们使用魔术方法模拟它们，
例如使用 `__get` 和 `__set` 魔术方法，但是在实现中，由于缺乏语言结构的直接支持，没有一个良好的约束规范 Getter 和 Setter 的使用。

这个组件试图规范化 Getter 和 Setter 的定义以及使用方式，旨在提供清晰程序逻辑和优雅的编程体验。

在这里，Getter、Setter 被严格定义：

> 必须具有一个以下划线开头的私有成员变量和以这个变量为核心的一个公开的非静态方法， 
三者的名称除了前缀 `_`, `get`, `set` 之外必须完全一致（ 不区分大小写 ）。这样的一组类内的小结构，被称为 `Aeccessor（访问器 ）`。
    
 > (!) Notice: 不区分大小写是由于 PHP 不区分方法大小写造成的。

Installation
------------
It's recommended that you use [Composer](https://getcomposer.org/) to install this project.

```bash
$ composer require panlatent/property
```

This will install the library and all required dependencies. The library requires PHP 5.5 or newer.

Usage
-----

这个组件提供了一些 traits 以支持 Getter、Setter 访问器，并且提供了相应的反射类用于代码分析或代码生成。

```php
class User
{
    use Getter, Setter;
    
    private $_name;
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    private $_status;
    
    public functionn getStatus()
    {
        if ($this->_status === null) {
            $this->status = 1;
        }
        return $this->status;
    }
}
```

```
$user = new User();
$user->name = 'Job';
echo $user->name; // print "Job"
echo $user->status; // print 1 use defualt value in getter
$user->status = 2;  // error, read-only
```

License
-------
The Gorgeous is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).