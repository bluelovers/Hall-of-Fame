<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Rand_Helper
{

	/**
	 * 不等概率随机选取算法
	 *
	 * A reservoir-type adaptation of algorithm A is the following algorithm A-Res:
	 * Algorithm A with a Reservoir (A-Res)
	 * Input : A population V of n weighted items
	 * Output : A reservoir R with a WRS of size m
	 *
	 * 1: The first m items of V are inserted into R
	 * 2: For each item vi 2 R: Calculate a key ki = u(1/wi)
	 * i , where ui = random (0,1)
	 * 3: Repeat Steps 4–7 for i = m + 1, m + 2, . . . , n
	 * 4: The smallest key in R is the current threshold T
	 * 5: For item vi: Calculate a key ki = u(1/wi)
	 * i , where ui = random (0,1)
	 * 6: If the key ki is larger than T, then:
	 * 7: The item with the minimum key in R is replaced by item vi
	 *
	 * 以上解释如果问题，请参看 http://utopia.duth.gr/~pefraimi/research/data/2007EncOfAlg.pdf
	 *
	 * @param array $arr
	 * @param string $key
	 * @param string $weightKey
	 *
	 * @author http://blog.wangzhong.me/unequal-probability-random-selection-algorithm-to-achieve-weightedrandom-php.html
	 */
	public static function weightedRandom(array $arr, $key, $weightKey)
	{
		$rawArr = array();
		foreach ($arr as $k => $v)
		{
			$rawArr[$v[$key]] = $v[$weightKey];
		}

		$num = 10;
		$cnt = count($rawArr);
		$ret = array();

		//The first m items of V are inserted into $ret
		$ret = array_slice($rawArr, 0, 10, true);
		$remain = array_slice($rawArr, 10, $cnt - 10, true);

		//For each item vi 2 R: Calculate a key ki = u(1/wi)i , where ui = random (0,1)
		$kArr = array();
		foreach ($ret as $k => $v)
		{
			srand();
			$u = mt_rand();
			$u = $u / mt_getrandmax();
			$kArr[$k] = pow($u, 1 / $v);
		}

		foreach ($remain as $k => $v)
		{
			//The smallest key in R is the current threshold T
			asort($kArr);
			list($tk, $tv) = each($kArr);
			reset($kArr);

			//For item vi: Calculate a key ki = u(1/wi)i , where ui = random (0,1)
			srand();
			$u = mt_rand();
			$u = $u / mt_getrandmax();
			$ki = pow($u, 1 / $v);
			if ($ki > $tv)
			{
				//replace
				foreach ($ret as $rk => $rv)
				{
					if ($rk == $tk)
					{
						unset($ret[$rk]);
						$ret[$k] = $v;
						break;
					}
					;
				}
				unset($kArr[$tk]);
				$kArr[$k] = $ki;
			}
		}

		//组合数据
		$newItemArr = array();
		foreach ($arr as $k => $v)
		{
			$newItemArr[$v[$key]] = $v;
		}

		$ret = array_intersect_key($newItemArr, $ret);
		return $ret;
	}

	public static function rand($min = 0, $max = 100, $ra = 0, $rb = 0, $retval = true)
	{
		if ($min > $max)
		{
			list($min, $max) = array($max, $min);
		}
		elseif ($min == $max)
		{
			return $min;
		}

		do
		{
			$p = rand($min, $max);
		} while (!$p);

		$a = rand($min, $p);
		$b = rand($p, $max);

		$r = rand(0 - $ra, 1 + $rb);

		if ($retval)
		{
			return $r > 0 ? $b : $a;
		}
		else
		{
			return (bool)($r > 0 ? ($b > $a) : ($a > $b));
		}
	}

	public static function mt_rand($min = 0, $max = 100, $ra = 0, $rb = 0, $retval = true)
	{
		if ($min > $max)
		{
			list($min, $max) = array($max, $min);
		}
		elseif ($min == $max)
		{
			return $min;
		}

		do
		{
			$p = mt_rand($min, $max);
		} while (!$p);

		$a = mt_rand($min, $p);
		$b = mt_rand($p, $max);

		$r = mt_rand(0 - $ra, 1 + $rb);

		if ($retval)
		{
			return $r > 0 ? $b : $a;
		}
		else
		{
			return (bool)($r > 0 ? ($b > $a) : ($a > $b));
		}
	}

}
