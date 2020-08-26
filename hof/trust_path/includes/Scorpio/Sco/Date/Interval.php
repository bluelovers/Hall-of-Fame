<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Date_Interval extends ArrayObject
{

	const WEEK = 604800;
	const DAY = 86400;

	const HOUR = 3600;
	const MINUTE = 60;
	const SECOND = 1;

	const REGEX_PRINTF = '/(?<!%)(?:%%)*(%([YyMmDdaHhIiSsRr]))/';
	const REGEX_PRINTF2 = '/(?<!%)(?:%%)*(%([YyMmDdaHhIiSsRrUuw]))/';

	protected $_timestamp;

	protected $_interval_spec;

	protected $_options = array(
		'spec_microtime' => true,

		/**
		 * if true use php mode handle week & day
		 * weeks. These get converted into days, so can not be combined with D.
		 *
		 * @see http://www.php.net/manual/en/dateinterval.construct.php
		 */
		'php_week' => false,
		);

	/**
	 * Creates new DateInterval object
	 */
	public function __construct($interval_spec)
	{
		parent::__construct(array(
			'y' => 0,
			'm' => 0,
			'd' => 0,
			'h' => 0,
			'i' => 0,
			's' => 0,
			'invert' => 0,
			'days' => false,

			'u' => null,
			), self::ARRAY_AS_PROPS);

		$this->_interval_spec = (string )$interval_spec;

		list($timestamp, $r) = $this->_parse_spec($this->_interval_spec);

		$this->_timestamp = abs($timestamp);

		foreach ($r as $_k => $_v)
		{
			$this[$_k] = $_v;
		}

		if ($timestamp < 0)
		{
			$this->setInvert(1);
		}
	}

	/**
	 * Sets up a DateInterval from the relative parts of the string
	 */
	public static function createFromDateString($time)
	{
		$DateTimeZone = Sco_Date_Helper::getDateTimeZoneGMT();

		$now = time();

		$microsecond = null;

		if (preg_match('/([0-9]+)\s*microseconds?/', $time, $match, PREG_OFFSET_CAPTURE))
		{
			$microsecond = sprintf(Sco_Date_Helper::MICROSECOND_PRINTF, $match[1][0]);

			$time = substr_replace($time, '', $match[0][1], strlen($match[0][0]));
		}

		//var_dump($time, $match);

		$end = new DateTime($time, $DateTimeZone);

		$timestamp = $end->getTimestamp() - $now;

		/*
		$invert = ($timestamp < 0);

		$timestamp = abs((int)$timestamp);

		$interval = new self("PT{$timestamp}S");
		$microsecond && $interval->setMicrosecond($microsecond);

		$interval->calSpec();

		$interval->setInvert($invert);
		*/
		$interval = self::createFromTimestamp((int)$timestamp, $microsecond);

		return $interval;
	}

	public static function createFromTimestamp($time, $microsecond = null)
	{
		$invert = ($time < 0);
		$time = abs($time);

		if ($microsecond !== null || is_int($time))
		{
			$interval = new self("PT{$time}S");
			$interval->setMicrosecond($microsecond);
		}
		elseif (is_float($time))
		{
			$microtime = Sco_Date_Helper::microtime(null, $time);

			$interval = new self("PT{$microtime[1]}S");
			$interval->setMicrosecond($microtime[0]);
		}
		else
		{
			$interval = new self("PT{$time}S");
		}

		$interval->calSpec();
		$interval->setInvert($invert);

		return $interval;
	}

	public function setOptions($options)
	{
		foreach ($options as $k => $v)
		{
			$this->_options[$k] = $v;
		}

		return $this;
	}

	/**
	 * Is 1 if the interval is inverted and 0 otherwise. See DateInterval::format().
	 */
	public function setInvert($flag)
	{
		$old = $this->invert;

		$this->invert = $flag ? 1 : 0;

		return $old;
	}

	/**
	 * Is 1 if the interval is inverted and 0 otherwise. See DateInterval::format().
	 */
	public function getInvert()
	{
		return $this->invert;
	}

	/**
	 * Formats the interval
	 *
	 * @see http://www.php.net/manual/zh/dateinterval.format.php
	 */
	public function format($format)
	{
		$pos = 0;
		$cache = array();

		while (preg_match(self::REGEX_PRINTF2, $format, $match, PREG_OFFSET_CAPTURE, $pos))
		{
			//var_dump($format, $match, $pos);

			$r = null;
			$k = $match[2][0];

			if (isset($cache[$k]))
			{
				$r = $cache[$k];
			}
			else
			{
				switch ($k)
				{
					case 'a':
						$r = floor($this->getTimestamp() / self::DAY);
						break;
					case 'w':
						$r = floor($this->getTimestamp() / self::WEEK);
						break;
					case 'R':
						$r = $this->invert ? '-' : '+';
						break;
					case 'r':
						$r = $this->invert ? '-' : '';
						break;
					case 'y':
					case 'm':
					case 'd':
					case 'h':
					case 'i':
					case 's':
						$r = $this->$k;
						break;
					case 'Y':
					case 'M':
					case 'D':
					case 'H':
					case 'I':
					case 'S':
						$_k = strtolower($k);

						$r = $this->$_k;

						if ($r < 10)
						{
							$r = '0' . $r;
						}

						break;
					case 'U':
						$r = $this->u;
						break;
					case 'u':
						$r = sprintf(Sco_Date_Helper::DATE_U_PRINTF, $this->u);
						break;

				}

				$cache[$k] = $r;
			}

			//var_dump($r);

			if (isset($r))
			{
				$format = substr_replace($format, $r, $match[1][1], strlen($match[1][0]));

				$pos = $match[1][1] + strlen($r);
			}
			else
			{
				$pos = $match[1][1] + strlen($match[1][0]);
			}
		}

		//var_dump($format, $match, $pos);

		return $format;
	}

	public function getTimestamp()
	{
		$DateTimeZone = Sco_Date_Helper::getDateTimeZoneGMT();

		$now = time();

		$modify = new DateTime('@' . $now, $DateTimeZone);
		$modify->modify($this->formatRelative());

		$timestamp = $modify->getTimestamp() - $now;

		if ($this->u)
		{
			$invert = $this->invert ? -1 : 1;

			$microsecond = '0.' . $this->u;

			$timestamp += ((float)$microsecond * $invert);
		}

		return $timestamp;
	}

	public function setMicrosecond($microsecond)
	{
		$old = $this->u;

		if ($microsecond === null)
		{
			$this->u = null;
		}
		else
		{
			$this->u = sprintf(Sco_Date_Helper::MICROSECOND_PRINTF, $microsecond);
		}

		return $old;
	}

	public function getMicrosecond()
	{
		return $this->u;
	}

	/**
	 * @see http://www.php.net/manual/zh/datetime.formats.relative.php
	 */
	public function formatRelative()
	{
		$invert = $this->invert ? -1 : 1;

		$arr = array(
			'y' => 0,
			'm' => 0,
			'd' => 0,
			'h' => 0,
			'i' => 0,
			's' => 0,
			);

		foreach (array_keys($arr) as $k)
		{
			$arr[$k] = $this->$k * $invert;
		}

		return vsprintf('%+d years, %+d months, %+d days, %+d hours, %+d minutes, %+d seconds', $arr);
	}

	public function getSpec($recalculate = false, $spec_microtime = null)
	{
		if ($spec_microtime === null)
		{
			$spec_microtime = $this->_options['spec_microtime'];
		}

		if ($recalculate)
		{
			return self::formatSpec($this->calSpec(false), $spec_microtime);
		}

		return self::formatSpec($this, $spec_microtime);
	}

	public static function formatSpec($arr, $spec_microtime = true)
	{
		$arr = (array )$arr;

		return sprintf('P%dY%dM%dDT%dH%dM%dS', $arr['y'], $arr['m'], $arr['d'], $arr['h'], $arr['i'], $arr['s']) . (($spec_microtime && $arr['u'] > 0) ? sprintf(Sco_Date_Helper::MICROSECOND_PRINTF, $arr['u']) . 'U' : '');
	}

	public function calSpec($update = true)
	{
		$s = 0;
		foreach (array(
			'd',
			'h',
			'i',
			's') as $k)
		{
			switch ($k)
			{
				case 'd':
					$s += $this[$k] * self::DAY;
					break;
				case 'h':
					$s += $this[$k] * self::HOUR;
					break;
				case 'i':
					$s += $this[$k] * self::MINUTE;
					break;
				case 's':
					$s += $this[$k] * self::SECOND;
					break;
			}
		}

		$r = array();

		foreach (array(
			'd',
			'h',
			'i',
			's') as $k)
		{
			switch ($k)
			{
				case 'd':
					$r[$k] = floor($s / self::DAY);
					break;
				case 'h':
					$r[$k] = floor($s / self::HOUR) % 24;
					break;
				case 'i':
					$r[$k] = floor($s / self::MINUTE) % 60;
					break;
				case 's':
					$r[$k] = floor($s / self::SECOND) % 60;
					break;
			}
		}

		if ($update)
		{
			foreach ($r as $k => $v)
			{
				$this[$k] = $r[$k];
			}

			return $this;
		}
		else
		{
			$arr = $this->getArrayCopy();

			foreach ($r as $k => $v)
			{
				$arr[$k] = $r[$k];
			}

			return $arr;
		}
	}

	protected function _parse_spec($interval_spec)
	{
		$timestamp = 0;

		if (strpos($interval_spec, 'P') === 0)
		{
			$spec = explode('T', substr($interval_spec, 1), 2);

			$r = array();

			if ($str = $spec[0])
			{
				$pos = 0;
				while (preg_match('/(\d+)([YMDW])/', $str, $match, PREG_OFFSET_CAPTURE, $pos))
				{
					$u = strtolower($match[2][0]);
					$r[$u] = (int)$match[1][0];

					$pos = $match[2][1] + 1;
				}
			}

			if ($str = $spec[1])
			{
				$pos = 0;
				while (preg_match('/(\d+)([HMSU])/', $str, $match, PREG_OFFSET_CAPTURE, $pos))
				{
					$u = strtolower($match[2][0]);

					if ($u == 'm') $u = 'i';

					if ($u == 'u')
					{
						$r[$u] = sprintf(Sco_Date_Helper::MICROSECOND_PRINTF, $match[1][0]);
					}
					else
					{
						$r[$u] = (int)$match[1][0];
					}

					$pos = $match[2][1] + 1;
				}
			}

			if ($r['w'] && (!isset($r['d']) || !$this->_options['php_week']))
			{
				$r['d'] += $r['w'] * 7;
				unset($r['w']);
			}
		}

		return array($timestamp, $r);
	}

}
