<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Session
{

	private static $_Options = array(
		'save_path' => BASE_PATH_SESSION,
		'name' => 'HOFSESSION',

		'gc_maxlifetime' => COOKIE_EXPIRE,

		'cookie_lifetime' => COOKIE_EXPIRE,
		'cookie_path' => BASE_URL_ROOT,

		'cache_expire' => 86400,

		'remember_me_seconds' => 86400,
		);

	protected static $_namespace = "Default";

	private static $CLASS = 'Zend_Session';
	private static $CLASS_NAMESPACE = 'Zend_Session_Namespace';

	private static $namespace = array();

	private static $_defaultOptionsSet = false;

	function __construct()
	{
		self::setOptions();

		return $this;

		if ($_COOKIE["NO"] && self::sessionExists($_COOKIE["NO"]))
		{
			/**
			 * クッキーに保存してあるセッションIDのセッションを呼び出す
			 */
			self::setId($_COOKIE["NO"]);
		}
		else
		{
			unset($_COOKIE['NO']);
			setcookie('NO', '', -1, BASE_URL_ROOT);
		}
	}

	private function sessionExists($id)
	{
		return file_exists(self::getOptions('save_path').'sess_'.$id);
	}

	public function start()
	{
		self::setOptions();

		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	public function setId($id)
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ), $id);
	}

	public function getId()
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	public function getOptions($optionName = null)
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ), $optionName);
	}

	public function setOptions(array $userOptions = array())
	{
		if (!self::$_defaultOptionsSet)
		{
			call_user_func(array(self::$CLASS, __FUNCTION__ ), (array )self::$_Options);
		}

		if ($userOptions)
		{
			foreach ((array )$userOptions as $k => $v)
			{
				self::$_Options[$k] = $v;
			}

			return call_user_func(array(self::$CLASS, __FUNCTION__ ), (array )$userOptions);
		}
	}

	public function destroy()
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	public function forgetMe()
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	public function rememberUntil($seconds = 0)
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ), $seconds);
	}

	public function regenerateId()
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	public function stop()
	{
		return call_user_func(array(self::$CLASS, __FUNCTION__ ));
	}

	/**
	 * @return Zend_Session_Namespace
	 */
	public function getNamespace($namespaceName = null)
	{
		if (!$namespaceName || $namespaceName === true) $namespaceName = self::$_namespace;

		if (!isset(self::$namespace[$namespaceName]))
		{
			$class = self::$CLASS_NAMESPACE;

			self::$namespace[$namespaceName] = new $class($namespaceName, true);
		}

		return self::$namespace[$namespaceName];
	}

}
