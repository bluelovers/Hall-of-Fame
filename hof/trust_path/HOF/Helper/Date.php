<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * $t[] = HOF_Helper_Date::microtime();
 * $t[] = HOF_Helper_Date::microtime_split($t[0][1].$t[0][0]);
 * var_dump($t);
 */
class HOF_Helper_Date
{

	const MICROTIME_LEN = 8;

	function microtime($get_as_float = false)
	{
		$microtime = microtime(true);

		$time = floor($microtime);

		// 0.31520000
		//$microsec = bcsub((float)$microtime, (float)$time, 8);

		// 0.15699505805969
		$microsec = $microtime - $time;

		if ($get_as_float)
		{
			return (float)$time + (float)$microsec;
		}
		else
		{
			return array((int)substr($microsec, 2, self::MICROTIME_LEN), (int)$time, $microtime);
		}
	}

	function microtime_split($microtime)
	{
		$time = substr($microtime, 0, 10);
		$microsec = substr($microtime, 10);

		return array((int)$microsec, (int)$time);
	}

}
