<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * $t[] = Sco_Date_Helper::microtime();
 * $t[] = Sco_Date_Helper::microtime_split($t[0][1].$t[0][0]);
 * var_dump($t);
 */
class Sco_Date_Helper
{

	const MICROSECOND_LEN = 8;
	const MICROSECOND_PRINTF = '%-08.8s';

	const MICROTIME_PRINTF = '%0.8f';

	const DATE_U_PRINTF = '%-06.6s';

	public static function microtime($get_as_float = false, $microtime = null)
	{
		if ($microtime)
		{
			if (is_array($microtime))
			{
				list($microsec, $time) = $microtime;

				return array(
					sprintf(self::MICROSECOND_PRINTF, (string )$microsec),
					(int)$time,
					(float)((float)$time + (float)('0.' . (string )$microsec)),
					);
			}
		}
		else
		{
			$microtime = microtime(true);
		}

		if ($get_as_float)
		{
			return (float)$microtime;
		}
		else
		{
			$time = floor($microtime);

			// 0.31520000
			//$microsec = bcsub((float)$microtime, (float)$time, 8);

			// 0.15699505805969
			$microsec = (float)$microtime - $time;

			return array(
				sprintf(self::MICROSECOND_PRINTF, substr($microsec, 2, self::MICROSECOND_LEN)),
				(int)$time,
				(float)$microtime,
				);
		}
	}

	public static function microtime_split($microtime)
	{
		$time = substr($microtime, 0, 10);
		$microsec = sprintf(self::MICROSECOND_PRINTF, substr($microtime, 10, self::MICROSECOND_LEN));

		return array((string )$microsec, (int)$time);
	}

	public static function date_format_fix($format, $timestamp)
	{
		$microtime = $u = null;

		$pos = 0;
		while (preg_match('`(?<!\\\\)u`', $format, $match, PREG_OFFSET_CAPTURE, $pos))
		{
			if (!isset($u))
			{
				if (!isset($microtime))
				{
					$microtime = self::microtime(false, $timestamp);
				}

				$u = sprintf(self::DATE_U_PRINTF, $microtime[0]);
			}

			$format = substr_replace($format, $u, $match[0][1], 1);

			$pos = $match[0][1] + 6;
		}

		//var_dump($timestamp, $microtime, self::microtime_split($microtime[1].$microtime[0]));

		return $format;
	}

	/**
	 * Formats the elapsed time as a string.
	 *
	 * @param float $time
	 * @return string
	 *
	 * @author https://github.com/sebastianbergmann/php-timer
	 *
	 * @assert (0) == '0 seconds'
	 * @assert (1) == '1 second'
	 * @assert (2) == '2 seconds'
	 * @assert (60) == '01:00'
	 * @assert (61) == '01:01'
	 * @assert (120) == '02:00'
	 * @assert (121) == '02:01'
	 * @assert (3600) == '01:00:00'
	 * @assert (3601) == '01:00:01'
	 */
	public static function secondsToTimeString($time)
	{
		$buffer = '';

		$hours = sprintf('%02d', ($time >= 3600) ? floor($time / 3600) : 0);
		$minutes = sprintf('%02d', ($time >= 60) ? floor($time / 60) - 60 * $hours : 0);
		$seconds = sprintf('%02d', $time - 60 * 60 * $hours - 60 * $minutes);

		if ($hours == 0 && $minutes == 0)
		{
			$seconds = sprintf('%1d', $seconds);

			$buffer .= $seconds . ' second';

			if ($seconds != '1')
			{
				$buffer .= 's';
			}
		}
		else
		{
			if ($hours > 0)
			{
				$buffer = $hours . ':';
			}

			$buffer .= $minutes . ':' . $seconds;
		}

		return $buffer;
	}

	public static function getDateTimeZoneGMT()
	{
		static $DateTimeZoneGMT;

		if (!isset($DateTimeZoneGMT))
		{
			$DateTimeZoneGMT = new DateTimeZone('GMT');
		}

		return $DateTimeZoneGMT;
	}

	/**
	 * Wrapper for microtime().
	 *
	 * @return float
	 */
	public static function getMicrotime()
	{
		return microtime(true);
	}

}
