<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

class _temp_A extends HOF_Class_Base_Extend_Root
{

}

class _temp_B implements HOF_Class_Base_Extend_ExtendInterface
{

	protected $_root_;

	function __construct(&$root_obj)
	{
		$this->_root_ = &$root_obj;
	}

	function public_call()
	{
		echo __METHOD__ . "\n";
	}

	function public_call_ref(&$a, $b = 1)
	{
		echo __METHOD__ . "\n";
		echo $a . "\n";
		$a = 321;
	}

	function public_call_ref2($a, $b = 1)
	{
		echo __METHOD__ . "\n";
		echo $a . "\n";
		$a = 321;
	}

	protected function protected_call()
	{
		echo __METHOD__ . "\n";
	}

	static function static_call()
	{
		echo __METHOD__ . "\n";
	}

}

$class = new ReflectionClass('_temp_B');
$method = $class->getMethod('public_call_ref');
$parameter = $method->getParameters();
//$parameter = new ReflectionParameter(array($class, $method));

//echo $parameter->__toString();

foreach ($parameter as $param)
{
	var_dump($param);

	if ($param->isPassedByReference())
	{
		echo $param->getName() . ' : isPassedByReference \n';
	}
}


_e(__LINE__);

$a = new _temp_A;

$a->extend('_temp_B');

$a->public_call();


_e(__LINE__);

try
{
	$c = '123';

	$a->public_call_ref($c);

	_e($c);
}
catch (Exception $e)
{
	echo $e->getMessage() . "\n";
}


_e(__LINE__);

try
{
	$c = '123';

	$a->public_call_ref2($c);

	_e($c);
}
catch (Exception $e)
{
	_e($e->getMessage());
}

_e(__LINE__);

try
{
	$a->protected_call();
}
catch (Exception $e)
{
	_e($e->getMessage());
}

_e(__LINE__);

try
{
	$a->static_call();
}
catch (Exception $e)
{
	_e($e->getMessage());
}