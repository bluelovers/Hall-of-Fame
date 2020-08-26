<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class Sco_Cookie
{

	const EXPIRE_NOW = -1;

	/**
	 * Class name of the singleton registry object.
	 * @var string
	 */
	private static $_registryClassName = 'Sco_Cookie_Object';

	/**
	 * Registry object provides storage for shared objects.
	 * @var Sco_Cookie_Interface
	 */
	private static $_registry = null;

	/**
	 * Retrieves the default registry instance.
	 *
	 * @return Sco_Cookie_Interface
	 */
	public static function &getInstance($registry = null)
	{
		if (!isset(self::$_registry))
		{
			self::init($registry);
		}

		return self::$_registry;
	}

	public static function setInstance(Sco_Cookie_Interface $registry)
	{
		if (self::$_registry !== null)
		{
			throw new Exception('Cookie is already initialized');
		}

		if (!$registry instanceof Sco_Cookie_Interface)
		{
			throw new Exception("Sco_Cookie_Interface");
		}

		self::setClassName(get_class($registry));
		self::$_registry = $registry;
	}

	/**
	 * Initialize the default registry instance.
	 *
	 * @return void
	 */
	protected static function init($registry)
	{
		self::setInstance(new self::$_registryClassName($registry));
	}

	public static function setClassName($registryClassName = 'Sco_Cookie_Object')
	{
		if (self::$_registry !== null)
		{
			throw new Exception('Cookie is already initialized');
		}

		if (!is_string($registryClassName))
		{
			throw new Exception("Argument is not a class name");
		}

		self::$_registryClassName = $registryClassName;
	}

	/**
	 * Unset the default registry instance.
	 * Primarily used in tearDown() in unit tests.
	 * @returns void
	 */
	public static function _unsetInstance()
	{
		self::$_registry = null;
	}

	/**
	 * getter method, basically same as offsetGet().
	 *
	 * This method can be called from an object of type Zend_Registry, or it
	 * can be called statically.  In the latter case, it uses the default
	 * static instance stored in the class.
	 *
	 * @param string $index - get the value associated with $index
	 * @return mixed
	 * @throws Zend_Exception if no entry is registerd for $index.
	 */
	public static function get($index, $default = null)
	{
		return self::getInstance()->get($index, $default);
	}

	/**
	 * setter method, basically same as offsetSet().
	 *
	 * This method can be called from an object of type Zend_Registry, or it
	 * can be called statically.  In the latter case, it uses the default
	 * static instance stored in the class.
	 *
	 * @param string $index The location in the ArrayObject in which to store
	 *   the value.
	 * @param mixed $value The object to store in the ArrayObject.
	 * @return void
	 */
	public static function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
	{
		call_user_func_array(array(self::getInstance(), 'set'), func_get_args());
	}

	/**
	 * Returns TRUE if the $index is a named value in the registry,
	 * or FALSE if $index was not found in the registry.
	 *
	 * @param  string $index
	 * @return boolean
	 */
	public static function isRegistered($index)
	{
		if (self::$_registry === null)
		{
			return false;
		}
		return self::$_registry->offsetExists($index);
	}

	public static function setcookie($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
	{
		$args = func_get_args();
		if ($args[1] === null || $args[2] < 0)
		{
			$args[1] = null;
			$args[2] = self::EXPIRE_NOW;
		}

		return call_user_func_array('setcookie', $args);
	}

	public static function setcookie_array($_config)
	{
		if ($_config['value'] === null || $_config['expire'] < 0)
		{
			$_config['value'] = null;
			$_config['expire'] = self::EXPIRE_NOW;
		}
		else
		{
			$_config['value'] = (string )$_config['value'];
		}

		return setcookie($_config['name'], $_config['value'], (int)$_config['expire'], (string )$_config['path'], (string )$_config['domain'], (bool)$_config['secure'], (bool)$_config['httponly']);
	}

	public static function save()
	{
		$instance = self::getInstance();

		return $instance->save();
	}

}
