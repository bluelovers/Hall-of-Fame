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
}

$a = new A;
$b = new B($a);

$a->c = __LINE__;

debug($a, $b);