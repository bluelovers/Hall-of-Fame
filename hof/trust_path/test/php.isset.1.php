<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

class A
{

	function __get($k)
	{
		_e(__METHOD__, $k);
		return $this->$k();
	}

	/*
	function __isset($k)
	{
		_e(__METHOD__, $k);
		return isset($this->$k);
	}
	*/

	function item()
	{
		_e(__METHOD__);

		if (!isset($this->item))
		{
			$this->item = array();
		}

		return $this->item;
	}

}

$a = new A;

debug(__LINE__, $a, isset($a->item));
debug(__LINE__, $a, isset($a->item));
var_dump($a->item);
debug(__LINE__, $a, isset($a->item));