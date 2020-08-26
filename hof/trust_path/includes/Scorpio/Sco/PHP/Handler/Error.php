<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_PHP_Handler_Error implements Sco_PHP_Handler_Interface
{

	const ST_START = 1;
	const ST_PAUSE = 2;
	const ST_STOP = 4;

	protected static $_error_handler;
	protected static $_error_types;

	protected static $_error_handler_top;
	protected static $_result;

	protected static $_state;
	protected static $_state_true;

	public static function start($error_handler = null, $error_types = null)
	{
		if (self::$_state === self::ST_START)
		{
			trigger_error(sprintf('%s already %s', __CLASS__, __FUNCTION__ ), E_USER_WARNING);
		}
		elseif (self::$_state === self::ST_PAUSE && $error_handler === null)
		{
			$error_handler = self::$_error_handler;
			$error_types = self::$_error_types;
		}

		self::$_state = self::ST_START;

		$ret = true;

		if ($error_handler !== null)
		{
			$ret = self::set($error_handler, $error_types);
		}

		return $ret;
	}

	public static function pause()
	{
		self::_check_start(__METHOD__);

		self::$_state = self::ST_PAUSE;

		return self::reset();
	}

	public static function stop($buildin = false)
	{
		self::_check_start(__METHOD__);

		self::$_state = self::ST_STOP;

		self::reset($buildin);

		unset(self::$_error_handler);
		unset(self::$_error_types);

		return true;
	}

	public static function set($error_handler, $error_types = null)
	{
		self::_check_start(__METHOD__);

		if ($error_types === null)
		{
			if (self::$_error_types === null)
			{
				self::$_error_types = error_reporting() | E_FATAL;
			}

			$error_types = self::$_error_types;
		}

		self::$_error_handler = $error_handler;
		self::$_error_types = $error_types;

		$old = set_error_handler(self::$_error_handler, self::$_error_types);

		if (!isset(self::$_state_true))
		{
			self::$_state_true = true;
			self::$_error_handler_top = $old;
		}

		return self::$_result = array(
			self::$_error_handler,
			self::$_error_types,
			$old);
	}

	public static function get($true = null)
	{
		self::_check_start(__METHOD__);

		if ($true)
		{
			$now = set_error_handler(create_function('$errno,$errstr', 'return false;'), self::$_error_handler_top);
			restore_error_handler();
		}
		else
		{
			$now = self::$_error_handler;
		}

		return array(
			$now,
			self::$_error_types,
			self::$_error_handler_top);
	}

	public static function state()
	{
		return self::$_state;
	}

	public static function last_result()
	{
		return self::$_result;
	}

	public static function restore()
	{
		//return restore_error_handler();

		self::_check_start(__METHOD__);

		if (self::$_error_handler_top !== ($old = set_error_handler(create_function('$errno,$errstr', 'return false;'), self::$_error_handler_top)))
		{
			// Unset the error handler we just set.
			restore_error_handler();
		}

		restore_error_handler();

		list(self::$_error_handler, ) = self::get(true);

		return array(
			self::$_error_handler,
			self::$_error_types,
			$old);
	}

	public static function reset($buildin = false)
	{
		if ($buildin && self::$_state !== null && self::$_state !== self::ST_STOP)
		{
			return self::stop($buildin);
		}

		if ($buildin)
		{
			unset(self::$_state_true);
			unset(self::$_error_handler_top);
		}

		while (self::$_error_handler_top !== (self::$_error_handler = set_error_handler(create_function('$errno,$errstr', 'return false;'), self::$_error_handler_top)))
		{
			// Unset the error handler we just set.
			restore_error_handler();

			// Unset the previous error handler.
			restore_error_handler();
		}

		// Restore the built-in error handler.
		return restore_error_handler();
	}

	public static function has_started()
	{
		return (bool)(self::$_state !== null);
	}

	protected static function _check_start($method)
	{
		if (self::$_state === null)
		{
			throw new Exception(sprintf('You must call %s::satrt() before %s()', __CLASS__, $method));

			return false;
		}

		return true;
	}

	public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext = null)
	{
		if (0 && !(error_reporting() & $errno))
		{
			return;
		}

		$typestr = Sco_PHP_Helper::errno_const($errno);

		$file = Sco_File_Format::remove_root($errfile, SCORPIO_PATH_SYS . '../../');

		printf('<div>%s: %s in %s %d</div>', $typestr, $errstr, $file, $errline);

		return true;
	}

	public static function fatal_error_handler()
	{
		if ($e = @error_get_last())
		{
			if ($e['type'] & E_FATAL)
			{
				$callback = null;

				if (self::has_started())
				{
					list ($callback, ) = self::get(true);
				}

				if (!is_callable($callback))
				{
					$callback = array(__CLASS__, 'error_handler');
				}

				//self::error_handler($e['type'], $e['message'], $e['file'], $e['line']);
				return call_user_func($callback, $e['type'], $e['message'], $e['file'], $e['line']);
			}
		}

		return true;
	}

}
