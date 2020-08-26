<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Format
{

	const PRINTF_INT = '%+.0f';
	const PRINTF_FLOAT = '%+.8f';

	public static function int($number)
	{
		return sprintf(self::PRINTF_INT, $number);
	}

	public static function float($number, $scale = null)
	{
		return sprintf($scale === null ? self::PRINTF_FLOAT : "%+.{$scale}f", $number);
	}

	/**
	 *
	 * @param type $number
	 * @return type
	 *
	 * @see http://www.php.net/manual/zh/function.bcscale.php#88463
	 *
	 * @assert ('1.2500000000') === '1.25'
	 * @assert ('1340959614.07259200') === '1340959614.072592'
	 */
	public static function clean_decimal($number)
	{
		$number = explode('.', $number, 2);

		$number[1] = rtrim($number[1], '0');

		if ($number[1])
		{
			return implode('.', $number);
		}
		else
		{
			return $number[0];
		}
	}

}
