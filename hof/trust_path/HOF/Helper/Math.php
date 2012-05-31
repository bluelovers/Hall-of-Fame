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

	static function rand_seed()
	{
		srand((microtime(true) - time()) * 10000000);
		mt_srand((microtime(true) - time()) * 10000000);
	}

}

