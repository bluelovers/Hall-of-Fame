<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Comparer_Helper
{

	/**
	 * eazy comparer all type
	 */
	public static function cmp($a, $b)
	{
		if ($a == $b)
		{
			return CMP_KEEP;
		}

		return ($a < $b) ? CMP_NEXT : CMP_BACK;
	}

	/**
	 * @todo
	 */
	public static function cmpStack($a, $b)
	{
		if ($a == $b)
		{
			return CMP_KEEP;
		}

		return ($a < $b) ? CMP_NEXT : CMP_BACK;
	}

	/**
	 * @todo this func only maybe work on first time
	 */
	public static function cmpQueue($a, $b)
	{
		if ($a == $b)
		{
			return CMP_BACK;
		}

		return ($a < $b) ? CMP_NEXT : CMP_BACK;
	}

}
