<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Spl_Helper
{

	const REGEX_FUNCNAME = '/^[a-zA-Z_][a-zA-Z_0-9]*$/';

	static $lambda = array();

	public static function createFunction($func_name, $callback)
	{
		if (is_array($func_name))
		{
			foreach ($func_name as $func)
			{
				self::createFunction($func, $callback);
			}

			return $func_name;
		}

		if (!self::vaildFunctionName($func_name))
		{
			throw new RuntimeException(sprintf('Fatal error: syntax error "%s" not a vaild Function name', (string )$func_name));
			//trigger_error(sprintf('syntax error "%s" not a vaild Function name', (string)$func_name), E_USER_ERROR);
			return;
		}

		$exists = isset(self::$lambda[$func_name]);

		self::$lambda[$func_name] = $callback;

		if (!$exists)
		{
			if (function_exists($func_name))
			{
				throw new RuntimeException(sprintf('Fatal error: Cannot redeclare Function %s() previously declared', (string )$func_name));
				return;
			}

			$eval = 'function %1$s(){ if (Sco_Spl_Helper::$lambda[\'%1$s\'] instanceof Sco_Spl_Callback_Interface) { return Sco_Spl_Helper::$lambda[\'%1$s\']->exec_array(func_get_args()); } else { return call_user_func_array(Sco_Spl_Helper::$lambda[\'%1$s\'], func_get_args()); } }';

			eval(sprintf($eval, (string )$func_name));
		}

		return $func_name;
	}

	public static function vaildFunctionName($func_name)
	{
		return preg_match(self::REGEX_FUNCNAME, $func_name);
	}

	public static function getFunction($func_name)
	{
		if (!self::vaildFunctionName($func_name))
		{
			throw new RuntimeException(sprintf('Fatal error: syntax error "%s" not a vaild Function name', (string )$func_name));
			//trigger_error(sprintf('syntax error "%s" not a vaild Function name', (string)$func_name), E_USER_ERROR);
			return;
		}

		if ($exists = isset(self::$lambda[$func_name]))
		{
			return self::$lambda[$func_name];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Return TRUE if the given function has been defined
	 */
	public static function function_exists($function)
	{
		return (function_exists($function) || is_callable($function));
	}

}
