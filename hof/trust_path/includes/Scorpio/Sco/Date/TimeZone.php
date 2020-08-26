<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Date_TimeZone extends DateTimeZone
{

	static $_useDefaultTimeZone = false;

	public function __construct($timezone = 'GMT')
	{
		if (self::$_useDefaultTimeZone && empty($timezone)) $timezone = (string)date_default_timezone_get();

		/**
		 * 使 $timezone 支援 GMT[+-]\d+ 格式, 自動轉換為 Etc/GMT 格式
		 */
		if (preg_match('/^GMT([+\-]\d+)$/i', (string )$timezone, $m))
		{
			$m[1] = 0 - (int)$m[1];
			if ($m[1] >= 0)
			{
				$m[1] = '+' . abs($m[1]);
			}

			$timezone = 'Etc/GMT' . $m[1];

			unset($m);
		}

		parent::__construct((string )$timezone);

		return $this;
	}

	public function __toString()
	{
		return $this->getName();
	}

	public static function useDefaultTimeZone($flag = null)
	{
		if (null === $flag)
		{
			return self::$_useDefaultTimeZone;
		}

		$old = self::$_useDefaultTimeZone;

		self::$_useDefaultTimeZone = (bool)$flag;

		return $old;
	}

}
