<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Helper
{

	public static $rand = 1000000;

	public static function minmax($num, $min, $max)
	{
		return max($min, min($max, $num));
	}

	public static function rand_seed()
	{
		static $salt;

		if (!isset($salt))
		{
			$salt[0] = mt_rand();
			$salt[1] = mt_rand();
		}

		mt_srand($salt[1] = abs(mt_rand(-50, 150) / 100 * ($salt[0] + (microtime(true) - time()) * 10000000)) % 100000000);
		srand($salt[0] = abs(mt_rand(-50, 150) / 100 * ($salt[0] + (microtime(true) - time()) * 10000000)) % 100000000);

		var_dump($salt);
	}

	protected static function _srand()
	{
		$scale = 15;
		//bcscale(9);

		self::$rand = bcadd((float)microtime(true) - (float)time(), (float)self::$rand * mt_rand(-100, 200) / 100, $scale);

		return self::$rand;
	}

	protected static function _rand(array & $r, $retval)
	{
		shuffle($r['a']);

		$r['n3'] = $r['a'][$r['n1']];
		$r['n4'] = $r['a'][$r['n2']];

		//var_dump($r);

		if ($retval)
		{
			$r['r'] = $r['c'] ? $r['n3'] : $r['n4'];
		}
		else
		{
			if ($r['n4'] == $r['n3'] || $ra == $r['n3'])
			{
				$r['r'] = 2;
			}
			else
			{
				$r['n4'] = $ra ? $ra : $r['a'][$r['n2']];

				$r['r'] = $r['c'] ? (($r['n4'] >= $r['n3']) ? 1 : 0) : (($r['n4'] <= $r['n3']) ? 1 : 0);
			}
		}
		return $r['r'];
	}

	public static function rand($ra = 0, $rb = 0, $low = 1, $high = 100, $step = 1, $retval = true)
	{
		srand((float)microtime(true) * rand(-100, 200) / 100 * self::_srand() + ((float)microtime(true) - time()));

		$r = array();

		$r['a'] = range($low, $high, ($step ? $step : 1));
		$r['n1'] = array_rand($r['a']);
		$r['n2'] = array_rand($r['a']);
		$r['c'] = rand(0, 1 + $rb);

		self::_rand($r, $retval);

		return $r['r'];
	}

	public static function mt_rand($ra = 0, $rb = 0, $low = 1, $high = 100, $step = 1, $retval = true)
	{
		mt_srand((float)microtime(true) * mt_rand(-100, 200) / 100 * self::_srand() + ((float)microtime(true) - time()));

		$r = array();

		$r['a'] = range($low, $high, ($step ? $step : 1));
		$r['n1'] = array_rand($r['a']);
		$r['n2'] = array_rand($r['a']);
		$r['c'] = mt_rand(0, 1 + $rb);

		self::_rand($r, $retval);

		return $r['r'];
	}

	/**
	 * fix -0 => 0
	 * @param $n
	 */
	public static function fixzero($n)
	{
		return $n ? $n : 0;
	}

	public static function sign($n)
	{
		if ($n)
		{
			return $n / abs($n);
		}
		else
		{
			return 0;
		}
	}

}
