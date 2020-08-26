<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Func_Distance
{

	const DIRECTION_NONE = 0;

	const DIRECTION_NORTH = 1;
	const DIRECTION_EAST = 2;
	const DIRECTION_SOUTH = 3;
	const DIRECTION_WEST = 4;

	/**
	 * Taxicab geometry, Manhattan distance, or Manhattan length
	 *
	 * @see http://zh.wikipedia.org/zh-hant/%E6%9B%BC%E5%93%88%E9%A0%93%E8%B7%9D%E9%9B%A2
	 * @see http://en.wikipedia.org/wiki/Taxicab_geometry
	 *
	 * @assert (10, 0) == 10
	 * @assert (0, 10) == 10
	 * @assert (5, 5) == 10
	 */
	public static function ManhattanDistance($x2, $y2, $x1 = 0, $y1 = 0, $return = false)
	{
		if ($return)
		{
			$x = $x2 - $x1;
			$y = $y2 - $y1;

			return array(
				abs($x) + abs($y),
				$x,
				$y);
		}

		return abs($x2 - $x1) + abs($y2 - $y1);
	}

	public static function HexagonDistance($x2, $y2, $x1 = 0, $y1 = 0, $return = false)
	{

	}

	/**
	 * @assert (+0, +0) === false
	 * @assert (+0, +1) === 90.0
	 * @assert (+1, +1) === 45.0
	 * @assert (+1, +0) === 0.0
	 * @assert (+0, -1) === -90.0
	 * @assert (-1, -1) === -135.0
	 * @assert (-1, +0) === 180.0
	 * @assert (-1, -1) === -135.0
	 *
	 * @assert (5, 5) === 45.0
	 * @assert (-5, 5) === 135.0
	 * @assert (5, -5) === -45.0
	 * @assert (-5, -5) === -135.0
	 *
	 * @assert (5, 5, 0, 0, 1) === 45.0
	 * @assert (-5, 5, 0, 0, 1) === 135.0
	 * @assert (5, -5, 0, 0, 1) === 315.0
	 * @assert (-5, -5, 0, 0, 1) === 225.0
	 *
	 * @see http://www.php.net/manual/zh/function.atan2.php
	 * @see http://blog.csdn.net/meetlunay/article/details/7683593
	 */
	public static function azimuth_compass($x2, $y2, $x1 = 0, $y1 = 0, $abs = false, $return = false)
	{
		$x = $x2 - $x1;
		$y = $y2 - $y1;

		if ($x || $y)
		{
			//$p = 360 * (atan2($y, $x) / (2 * pi()));
			$p = rad2deg(atan2($y, $x));

			if ($abs && $p < 0)
			{
				$p += 360;
			}
		}
		else
		{
			$p = false;
		}

		if ($return)
		{
			return array(
				$p,
				$x,
				$y);
		}

		return $p;
	}

	/**
	 *    Given an origin point of (0,0) and a destination point $x,$y
	 *  somewhere on an axis grid, compass() determines the compass
	 *  heading(direction) of the destination point from the origin
	 *
	 *  HOWEVER, atan2(y,x)'s natural compass thinks east is north,
	 *
	 *  {135}-------{ 90}-------{45}
	 *      | +-----[ +y]-----+ |
	 *      | |               | |
	 *      | |               | |
	 *  {180} [-x]  [0,0]  [+x] {0} <--------- North ?
	 *      | |               | |
	 *      | |               | |
	 *      | +-----[ -y]-----+ |
	 * {-135}-------{-90}-------{-45}
	 *
	 *
	 *    SO, we simply transpose the (y,x) parameters to atan2(x,y)
	 *     which will both rotate(left) and reflect(mirror) the compass.
	 *
	 *  Which gives us this compass
	 *
	 *  {-45}-------{ 0 }-------{45}
	 *      | +-----[ +y]-----+ |
	 *      | |               | |
	 *      | |               | |
	 *  {-90} [-x]  [0,0]  [+x] {90}
	 *      | |               | |
	 *      | |               | |
	 *      | +-----[ -y]-----+ |
	 * {-135}-------{180}-------{135}
	 *
	 *  FINALLY,` we check if param $x was indeed a negative number,
	 *  if so we simply add 360 to the negative angle returned by atan2()
	 *
	 * @see http://www.php.net/manual/zh/function.atan2.php#88119
	 *
	 * @assert (+0, +0) === false
	 * @assert (+0, +1) === 0.0
	 * @assert (+1, +1) === 45.0
	 * @assert (+1, +0) === 90.0
	 * @assert (+0, -1) === 180.0
	 * @assert (-1, -1) === -135.0
	 * @assert (-1, +0) === -90.0
	 * @assert (-1, -1) === -135.0
	 */
	public static function azimuth_compass_2($x2, $y2, $x1 = 0, $y1 = 0, $abs = false, $return = false)
	{
		$x = $x2 - $x1;
		$y = $y2 - $y1;

		if ($x || $y)
		{
			//$p = 360 * (atan2($y, $x) / (2 * pi()));
			$p = rad2deg(atan2($x, $y));

			if ($abs && $p < 0)
			{
				$p += 360;
			}
		}
		else
		{
			$p = false;
		}

		if ($return)
		{
			return array(
				$p,
				$x,
				$y);
		}

		return $p;
	}

	/**
	 * @see http://www.php.net/manual/zh/function.atan2.php#88119
	 *
	 * @assert (0, 0) === ''
	 * @assert (0, 1) === 'N'
	 * @assert (1, 0) === 'E'
	 * @assert (0, -1) === 'S'
	 * @assert (-1, 0) === 'W'
	 * @assert (1, 1) === 'NE'
	 * @assert (-1, 1) === 'NW'
	 * @assert (1, -1) === 'SE'
	 * @assert (-1, -1) === 'SW'
	 */
	public static function polar($x, $y)
	{
		$N = ($y > 0) ? 'N' : '';
		$S = ($y < 0) ? 'S' : '';
		$E = ($x > 0) ? 'E' : '';
		$W = ($x < 0) ? 'W' : '';

		return $N . $S . $E . $W;
	}

	/**
	 * use 22.5 split
	 *
	 * @assert (0, 0) === ''
	 * @assert (0, 1) === 'N'
	 * @assert (1, 1) === 'NE'
	 * @assert (1, 0) === 'E'
	 * @assert (0, -1) === 'S'
	 * @assert (-1, -1) === 'SW'
	 * @assert (-1, 0) === 'W'
	 * @assert (-1, -1) === 'SW'
	 */
	public static function polar_2($x, $y)
	{
		if ($x == 0 && $y == 0)
		{
			return '';
		}

		$p = self::azimuth_compass_2($x, $y, 0, 0, true);

		$list = array(
			'N' => 337.5,
			'NW' => 292.5,
			'W' => 247.5,
			'SW' => 202.5,
			'S' => 157.5,
			'SE' => 112.5,
			'E' => 67.5,
			'NE' => 22.5,
			);

		foreach ($list as $k => $v)
		{
			if ($p >= $v)
			{
				return $k;
			}
		}

		return 'N';
	}

}
