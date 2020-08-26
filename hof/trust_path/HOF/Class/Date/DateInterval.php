<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Date_DateInterval implements ArrayAccess
{

	/* Properties */

	/*
	public $y;
	public $m;
	public $d;
	public $h;
	public $i;
	public $s;
	public $invert;
	public $days;
	*/

	/**
	 * DAY * 365
	 */
	const YEAR = 31536000;
	const MONTH = 2592000;
	const WEEK = 604800;
	const DAY = 86400;
	const HOUR = 3600;
	const HOUR_N = 24;
	const MINUTE = 60;
	const MINUTE_N = 60;
	const SECOND_N = 60;

	/**
	 * timestamp
	 */
	protected $_timestamp;

	protected $_date;

	public $invert = 0;

	/* Methods */

	public function __construct($interval_spec)
	{
		$timestamp = $interval_spec;

		$this->_timestamp = abs($timestamp);

		if ($timestamp < 0){
			$this->invert = 1;
		}
	}

	function __toString()
	{
		return (string)$this->_date;
	}

	public static function createFromDateString(string $time)
	{
		throw new UnexpectedValueException(__METHOD__ . ' not Complete.');
	}

	public function format(string $format)
	{
		throw new UnexpectedValueException(__METHOD__ . ' not Complete.');
	}

	function _part($part, $mode = false)
	{
		switch($part)
		{
			case '%Y':
			case '%M':
			case '%D':
			case '%H':
			case '%I':
			case '%S':
				$ret = $this->_part(strtolower($part));

				if (strlen($ret) == 1)
				{
					$ret = '0' . $ret;
				}

				break;
			case '%y':
				$ret = floor($this->_timestamp / self::YEAR);
				break;
			case '%m':
				$ret = floor($this->_timestamp / self::MONTH);

				if ($mode)
				{
					$ret %= self::MONTH_N;
				}

				break;
			case '%d':
				$ret = floor($this->_timestamp / self::DAY);
				break;
			case '%h':
				$ret = floor($this->_timestamp / self::HOUR);

				if ($mode)
				{
					$ret %= self::HOUR_N;
				}

				break;
			case '%i':
				$ret = floor($this->_timestamp / self::MINUTE);

				if ($mode)
				{
					$ret %= self::MINUTE_N;
				}

				break;
			case '%s':
				$ret = $this->_timestamp;

				if ($mode)
				{
					$ret %= self::SECOND_N;
				}

				break;
			case '%R':
				$ret = $this->invert ? '-' : '+';
				break;
			case '%r':
				$ret = $this->invert ? '-' : '';
				break;
			default:
				$ret = $part;
				break;
		}

		return $ret;
	}

	public function __get($k)
	{
		switch ($k)
		{
			case 's':
			case 'second':
				$ret = $this->_timestamp;
				//$ret = floor($this->_timestamp % self::MINUTE);
				break;
		}

		return (int)$ret;
	}

	public function offsetGet($k)
	{
		return $this->__get($k);
	}

	public function offsetSet($offset, $value)
	{
		throw new UnexpectedValueException(__CLASS__ . ' is readonly.');
	}

	public function offsetExists($offset)
	{
		return ($this->__get($offset) !== null) ? true : false;
	}

	public function offsetUnset($offset)
	{
		throw new UnexpectedValueException(__CLASS__ . ' is readonly.');
	}

}
