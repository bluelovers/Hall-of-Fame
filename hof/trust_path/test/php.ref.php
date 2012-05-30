<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

class A
{

}

class B
{
	function __construct($a)
	{
		$this->a = $a;
		$a->a = __CLASS__;

		$this->a->b = __METHOD__;
	}

	function __clone()
	{
		/*
		$null = null;

		$a = clone $this->a;
		$this->a = &$null;
		$this->a = $a;
		*/
		$this->a = clone $this->a;
	}
}

$a = new A;
$b = new B(&$a);

$c = clone $b;

$a->c = __LINE__;

debug($a, $b, $c);