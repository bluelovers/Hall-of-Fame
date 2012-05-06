<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

function e($string, $escape = false)
{
	$string = $escape ? HOF::escapeHtml($string) : $string;

	if (is_array($string))
	{
		print_r($string);
	}
	else
	{
		echo (string)$string;
	}
}

