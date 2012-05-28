<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

final class HOF
{

	/**
	 * @var HOF_Class_Request
	 */
	public static $input = null;

	/**
	 * @var HOF
	 */
	protected static $instance;

	const CHARSET = 'UTF-8';

	/**
	 * @var HOF_Class_File_Cache
	 */
	protected static $_cache_;

	/**
	 * @var HOF_Class_File_Log
	 */
	protected static $_log_;

	/**
	 * @var HOF_Class_Session
	 */
	protected static $_session_;

	/**
	 * @var HOF_Class_Main
	 */
	protected static $_user_;

	protected static $_date_;

	/**
	 * @var Zend_Session_Namespace
	 */
	public static $session;

	/**
	 * Word delimiter characters
	 * @var array
	 */
	protected static $_wordDelimiter = array(
		'-',
		'.',
		'_');

	public static $local = 'ja';

	public static $timezone = 'Asia/Taipei';
	//public static $timezone = 'Asia/Tokyo';

	public static $_destruct_call;

	function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;

			mb_internal_encoding(self::CHARSET);
			header('content-type: text/html; charset: '.self::CHARSET);

			self::$input = new HOF_Class_Request;

			self::$input->server->REQUEST_URI = substr(self::$input->server->REQUEST_URI, strlen(BASE_URL_ROOT) - 1);

			self::$_cache_ = new HOF_Class_File_Cache();
			self::$_log_ = new HOF_Class_File_Log();

			self::$_session_ = new HOF_Class_Session();

			Zend_Date::setOptions(array(
				'format_type' => 'php',
				'fix_dst' => False,
				));

			$locale = new Zend_Locale(self::$local);
			Zend_Registry::set('Zend_Locale', $locale);
		}
		else
		{
			die('error!!');
		}
	}

	public static function setup()
	{
		static $do;

		if ($do) return;

		HOF_Class_Session::start();

		self::$session = self::session()->getNamespace();
	}

	public static function router($controller = null, $action = null)
	{
		if (!file_exists(DAT_DIR . 'initialize' . EXT_LOCK))
		{
			return self::run('initialize');
		}

		HOF::setup();

		if ($controller !== null)
		{
			return self::run($controller, $action);
		}
		else
		{
			return new HOF_Model_Main();
		}
	}

	/**
	 * @return HOF_Class_Controller
	 */
	public static function run($controller, $action = null)
	{
		return HOF_Class_Controller::newInstance($controller, $action)->main();
	}

	function __destruct()
	{
		if (isset(self::$_user_) && self::user()->id && self::user()->cache())
		{
			self::user()->cache()->__destruct();
		}

		foreach ((array )self::$_destruct_call as $func)
		{
			@call_user_func($func);
		}

		self::$_cache_->__destruct();

		HOF_Class_File::fpclose_all();
	}

	/**
	 * @return HOF
	 */
	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return HOF_Class_Request
	 */
	public static function &request()
	{
		return self::$input;
	}

	/**
	 * @return HOF_Class_File_Cache
	 */
	public static function &cache()
	{
		return self::$_cache_;
	}

	/**
	 * @return HOF_Class_File_Log
	 */
	public static function &log()
	{
		return self::$_log_;
	}

	/**
	 * @return HOF_Class_Session
	 */
	public static function session($namespace = null)
	{
		if ($namespace !== null) return self::session()->getNamespace($namespace);

		return self::$_session_;
	}

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

	public static function end()
	{
		exit();
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
		if (!isset(self::$_user_))
		{
			self::$_user_ = &HOF_Class_Main::getInstance();
		}

		return self::$_user_;
	}

	public static function ip($ipv6 = false, $allow_private = true)
	{
		$keys = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR');

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

	/**
	 * @param mixed|$controller
	 * @param string|$action
	 * @param array|$extra
	 */
	public static function redirect($controller = null, $action = null, $extra = array())
	{

		if (is_array($controller))
		{
			list($controller, $action, $extra) = $controller;
		}

		$url = self::url((string )$controller, (string )$action, (array )$extra);
		header('Location: ' . $url);
		/*
		die;
		*/
		// bluelovers
		Dura::_exit();
		// bluelovers
	}

	/**
	 * @param mixed|$controller
	 * @param string|$action
	 * @param array|$extra
	 */
	public static function url($controller = null, $action = null, $extra = array())
	{

		if (is_array($controller))
		{
			list($controller, $action, $extra) = $controller;
		}

		$params = array();

		if ($action == HOF_Class_Controller::DEFAULT_ACTION)
		{
			unset($action);
		}

		if ($controller == HOF_Class_Controller::DEFAULT_CONTROLLER && !$action)
		{
			unset($controller);
		}

		$urls = array();

		if (BASE_URL_REWRITE || !($controller || $action))
		{
			$urls[] = substr(BASE_URL, 0, -1);
		}
		else
		{
			$urls[] = BASE_URL . INDEX;
		}

		if ($controller)
		{
			if (DURA_USE_REWRITE)
			{
				$urls[] = $controller;
			}
			else
			{
				$params['controller'] = $controller;
			}
		}

		if ($action)
		{
			if (BASE_URL_REWRITE)
			{
				$urls[] = $action;
			}
			else
			{
				$params['action'] = $action;
			}
		}

		if (!empty($extra) && is_array($extra))
		{
			$params = array_merge($params, $extra);

			unset($extra);
		}

		$url = implode('/', $urls);

		if ($params)
		{
			$params = array_filter((array )$params);

			if ($param = http_build_query($params))
			{
				$url .= '?' . $param;
			}
		}

		if (!empty($extra) && is_string($extra))
		{
			$url .= (strpos($url, '?') ? '&' : '?') . trim($extra, '?&');
		}

		return $url;
	}

	function date($timestamp = null, $format = null)
	{
		if (!isset(self::$_date_))
		{
			self::$_date_ = new HOF_Class_Date();
		}

		if ($timestamp && is_numeric($format) || $timestamp !== null && !is_numeric($timestamp))
		{
			list($format, $timestamp) = array($timestamp, $format);
		}

		if ($timestamp !== null)
		{
			self::$_date_->setTimestamp($timestamp);
		}

		if ($format !== null)
		{
			return self::$_date_->toString($format, 'php');
		}

		return self::$_date_;
	}

}
