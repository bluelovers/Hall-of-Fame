<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF
{

	public static $input = null;

	protected static $instance;

	const CHARSET = 'UTF-8';

	public static $_cache_;

	function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;
			self::$input = new HOF_Class_Request;

			self::$_cache_ = new HOF_Class_File_Cache();
		}
		else
		{
			die('error!!');
		}
	}

	function __destruct()
	{
		self::$_cache_->__destruct();

		HOF_Class_File::fpclose_all();
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

	public static function ip($ipv6 = false, $allow_private = true)
	{
		$keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

		$flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

		if ($allow_private === true)
		{
			$flags = FILTER_FLAG_NO_RES_RANGE;
		}

		if (!$ipv6)
		{
			$flags = $flags | FILTER_FLAG_IPV4;
		}

		foreach ($keys as $k)
		{
			if (filter_var($_SERVER[$k], FILTER_VALIDATE_IP, $flags))
			{
				$ip = $_SERVER[$k];
				break;
			}
		}

		return $ip;
	}

}
