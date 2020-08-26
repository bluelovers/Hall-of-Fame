<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Router_Helper
{

	/**
	 * Word delimiter characters
	 * @var array
	 */
	protected static $_wordDelimiter = array(
		'-',
		'.',
		'_',
		);

	public static function putintoClassParts($str, $sep = '')
	{
		/*
		static $cache;

		$k = $str;

		if (0 && !isset($cache[$k]))
		{
		$str = $cache[$k];
		}
		else
		{
		*/
		// 支援 將 AbcDef => abc_def

		$str = preg_replace('/([A-Z])/', ' $1', $str);
		$str = str_replace(self::$_wordDelimiter, ' ', strtolower($str));
		$str = preg_replace('/[^a-z0-9 ]/', '', $str);
		$str = str_replace(' ', $sep, ucwords($str));

		/*
		$cache[$k] = $str;
		}
		*/

		return $str;
	}

	public static function putintoPathParts($str)
	{
		/*
		static $cache;

		$k = $str;

		if (0 && isset($cache[$k]))
		{
		$str = $cache[$k];
		}
		else
		{
		*/
		$str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
		$str = preg_replace('/([A-Z])/', '_$1', $str);
		$str = strtolower($str);
		$str = trim($str, '_');

		/*
		$cache[$k] = $str;
		}
		*/

		return $str;
	}

}