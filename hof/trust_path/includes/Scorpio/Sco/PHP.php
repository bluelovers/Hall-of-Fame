<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_PHP
{

	/**
	 * @return Sco_Spl_Callback_Iterator
	 */
	protected static $_shutdown_handler;

	/**
	 * @return bool
	 */
	protected static $_shutdown_registed;

	/**
	 * @return Sco_Spl_Callback_Iterator
	 */
	public static function shutdown_handler()
	{
		if (!isset(self::$_shutdown_handler))
		{
			self::$_shutdown_handler = new Sco_Spl_Callback_Iterator(array());
		}

		return self::$_shutdown_handler;
	}

	public static function shutdown_disable($disable = true)
	{
		if (!isset(self::$_shutdown_handler))
		{
			self::shutdown_handler();
		}

		return self::$_shutdown_handler->disable($disable);
	}

	/**
	 * Registers a callback to be executed after script execution finishes or exit() is called.
	 */
	public static function shutdown_register($shutdown_handler)
	{
		if ($shutdown_handler === true)
		{
			$callback = array('Sco_PHP_Handler_Error', 'fatal_error_handler');
		}
		else
		{
			$callback = $shutdown_handler;

			if (!is_callable($callback, true, $callable_name))
			{
				throw new InvalidArgumentException(sprintf('Invalid shutdown callback \'%s\' passed', $callable_name));
			}
		}

		$handler = self::shutdown_handler()->append($callback);

		if (!self::$_shutdown_registed)
		{
			register_shutdown_function($handler->callback());

			self::$_shutdown_registed = true;
		}

		return true;
	}

	public static function error_reporting($level = null, $add = null)
	{
		if ($level === null)
		{
			return error_reporting();
		}
		else
		{

			if (defined('E_DEPRECATED'))
			{
				$level = $level ^ E_DEPRECATED;
			}

			if ($add !== null)
			{
				$level |= $add;
			}

			return error_reporting($level);
		}
	}

	public static function prepend_include_path($path)
	{
		return set_include_path($path . PATH_SEPARATOR . get_include_path());
	}

	public static function append_include_path($path)
	{
		return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	}

}
