<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Array_Comparer_MuliteSubKey
{

	var $keys;

	function __construct($keys)
	{
		$this->keys = func_get_args();
	}

	function compare($a, $b)
	{
		$i = 0;
		$c = count($this->keys);

		$cmp = 0;
		while ($cmp == 0 && $i < $c)
		{
			$cmp = strcmp($a[$this->keys[$i]], $b[$this->keys[$i]]);
			$i++;
		}

		return $cmp;
	}

}
