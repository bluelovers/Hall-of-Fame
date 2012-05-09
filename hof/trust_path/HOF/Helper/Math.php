<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Math
{

	static function minmax($num, $min, $max)
	{
		return max($min, min($max, $num));
	}

}

