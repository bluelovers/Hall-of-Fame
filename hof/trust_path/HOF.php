<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF
{

	public static $input = null;

	protected static $instance;

	function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;
			self::$input = new HOF_Class_Request;
		}
		else
		{
			die('error!!');
		}
	}

	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function putintoClassParts($str)
	{
		static $cache;

		$k = $str;

		if (!isset($cache[$k]))
		{
			// 支援 將 AbcDef => abc_def
			$str = preg_replace('/([A-Z])/e', '\'_\'.strtolower(\'\\1\')', $str);
			$str = trim($str, '_');

			$str = preg_replace('/[^a-z0-9_]/', '', $str);
			$str = explode('_', $str);
			$str = array_map('trim', $str);
			$str = array_diff($str, array(''));
			$str = array_map('ucfirst', $str);
			$str = implode('', $str);

			$cache[$k] = $str;
		}
		else
		{
			$str = $cache[$k];
		}

		return $str;
	}

	public static function putintoPathParts($str)
	{
		static $cache;

		$k = $str;

		if (!isset($cache[$k]))
		{
			$str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
			$str = preg_replace('/([A-Z])/', '_$1', $str);
			$str = strtolower($str);
			$str = substr($str, 1, strlen($str));

			$cache[$k] = $str;
		}
		else
		{
			$str = $cache[$k];
		}

		return $str;
	}

	public static function escapeHtml($string)
	{
		if (is_array($string))
		{
			foreach ($string as $_k => $_v)
			{
				$string[$_k] = self::escapeHtml($_v);
			}

			return $string;
		}

		return htmlspecialchars((string )$string, ENT_QUOTES);
	}

	public static function addslashes($var)
	{
		if (is_string($var))
		{
			$var = addslashes($var);
		}
		elseif (is_array($var))
		{
			foreach ($var as $k => &$v)
			{
				$v = self::addslashes($v);
			}
		}

		return $var;
	}

	public static function stripslashes($var)
	{
		if (is_string($var))
		{
			$var = stripslashes($var);
		}
		elseif (is_array($var))
		{
			foreach ($var as $k => &$v)
			{
				$v = self::stripslashes($v);
			}
		}

		return $var;
	}

	public static function &user()
	{
		$user = &HOF_Class_Main::getInstance();
		return $user;
	}

}
