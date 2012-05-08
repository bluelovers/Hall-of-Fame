<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

function e($string, $escape = false)
{
	$string = $escape ? HOF::escapeHtml($string) : $string;

	if (is_object($string) && method_exists($string, 'toArray'))
	{
		print_r($string->toArray());
	}
	elseif (is_object($string) && method_exists($string, '__toString'))
	{
		echo (string)$string;
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
	var_dump(a($var, 1));
}

function a($var)
{
	return HOF_Class_Array::_fixArray($var);
}
