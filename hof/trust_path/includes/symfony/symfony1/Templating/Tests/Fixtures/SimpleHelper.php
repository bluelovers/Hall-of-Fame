<?php

//use Symfony\Component\Templating\Helper\Helper;

class SimpleHelper extends Symfony_Component_Templating_Helper_Helper
{
    protected $value = '';

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function getName()
    {
        return 'foo';
    }
}
