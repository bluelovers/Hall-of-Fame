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
		$string = (string)$string;
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

