<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

function e($string, $escape = false)
{
	$string = $escape ? HOF::escapeHtml($string) : $string;

	if (is_object($string) && method_exists($string, '__toString'))
	{
		echo (string)$string;
	}
	elseif (is_object($string) && method_exists($string, 'toArray'))
	{
		print_r($string->toArray());
	}
	elseif (is_array($string))
	{
		print_r($string);
	}
	else
	{
		echo (string)$string;
	}
}

function debug($var)
{
	$agrs = func_get_args();

	var_dump(a($agrs, 2));
}

function a($var, $loop = 3)
{
	return HOF_Class_Array::_fixArrayRecursive($var, $loop);
}

function f($format)
{
	$argv = func_get_args();
	array_shift($argv);

	foreach ($argv as $i => $v)
	{
		$list['{'.$i.'}'] = $v;
	}

	$text = strtr((string)$format, (array)$list);

	return $text;
}