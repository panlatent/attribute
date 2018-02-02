<?php


namespace Panlatent\Property\Tests\Reflection;

use Panlatent\Property\Accessor\Getter;
use Panlatent\Property\Accessor\Setter;

class _Property
{
    use Getter, Setter;

    public $nickname;
    protected $phone;
    /**
     * @var string
     */
    private $_name;

    /**
     * @return string The test class name.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string|array|Getter|int $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getAge()
    {

    }

    public function setNumber()
    {

    }
}