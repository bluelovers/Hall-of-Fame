<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

class _temp_A extends HOF_Class_Base_Extend_Root
{

}

class _temp_B extends HOF_Class_Base_Extend_Extend
{

	function public_call()
	{
		echo __METHOD__ . "\n";
	}

	protected function protected_call()
	{
		echo __METHOD__ . "\n";
	}

}


$a = new _temp_A;

$a->extend('_temp_B');

$a->public_call();

try
{
	$a->protected_call();
}
catch (Exception $e)
{
	echo $e->getMessage() . "\n";
}
