<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_PHP_Helper
{

	/**
	 * @assert (true) === true
	 * @assert ('on') === true
	 * @assert ('true') === true
	 * @assert ('yes') === true
	 *
	 * @assert (false) === false
	 * @assert ('off') === false
	 * @assert ('false') === false
	 * @assert ('no') === false
	 */
	public static function var_ini_bool($val)
	{
		if (is_bool($val) === true)
		{
			return $val;
		}
		elseif ($val === null || $val === '' || $val === 0)
		{
			return false;
		}

		$val_lc = trim(strtolower($val));

		$ret = null;
		switch ($val_lc)
		{
			case 'on':
			case 'true':
			case 'yes':
				$ret = true;
				break;
			case 'off':
			case 'false':
			case 'no':
				$ret = false;
				break;
			default:
				$ret = $val;
				break;
		}

		return $ret;
	}

	/**
	 * @param $filename
	 * @param bool - return runtime_defined_vars
	 *
	 * @return array|mixed
	 */
	public static function include_file()
	{
		if (is_file(func_get_arg(0)))
		{
			include func_get_arg(0);
			if (true === func_get_arg(1))
			{
				return self::get_runtime_defined_vars(get_defined_vars());
			}
		}
		else
		{
			throw new Exception(sprintf('PHP Warning: %s(): Filename cannot be empty or not exists!!', __METHOD__));
		}

		return array();
	}

	/**
	 *
	 * @param $varList
	 * @param $excludeList
	 * @example get_runtime_defined_vars(get_defined_vars(), array('b'));
	 * @example get_runtime_defined_vars(get_defined_vars());
	 */
	public static function get_runtime_defined_vars(array $varList, $excludeList = array())
	{
		/**

		 * $a = 1;

		 * function abc($c = 2) {
		 * global $a;
		 * $b = 3;

		 * $a = 4;
		 * $GLOBALS['s'] = 5;

		 * get_runtime_defined_vars(get_defined_vars(), array('b'));
		 * }
		 * abc();
		 * get_runtime_defined_vars(get_defined_vars(), array('b'));

		 * Array
		 * (
		 * [c] => 2
		 * [a] => 4
		 * )
		 * Array
		 * (
		 * [a] => 4
		 * [s] => 5
		 * )
		 **/

		if ($varList)
		{
			$excludeList = array_merge((array )$excludeList, array(
				'GLOBALS',
				'_FILES',
				'_COOKIE',
				'_POST',
				'_GET',
				'_SERVER',
				'_ENV',
				'_REQUEST',
				));
			$varList = array_diff_key((array )$varList, array_flip($excludeList));
		}

		//print_r($varList);

		return $varList;
	}

	/**
	 * Checks if the class method exists in the given object .
	 *
	 * @return bool
	 */
	public static function func_exists($object, $method_name = null)
	{
		return $method_name === null ? function_exists($object) : method_exists($object, $method_name);
	}

	public static function sprint()
	{
		return self::vprint(func_get_args());
	}

	public static function vprint($args, $sep = '', $append = '', $retstring = false)
	{
		if ($retstring)
		{
			$ret = implode($sep, $args) . $append;
			echo $ret;

			return $ret;
		}
		else
		{
			echo implode($sep, $args) . $append;

			return true;
		}
	}

	public static function sprintnl()
	{
		return self::vprint(func_get_args(), '', NL);
	}

	public static function vprintnl($args)
	{
		return self::vprint($args, '', NL);
	}

	public static function void()
	{
		return null;
	}

	public static function errno_const($errno, $chk = false)
	{

		switch ($errno)
		{
			case E_ERROR:
				// 1
				$typestr = 'E_ERROR';
				break;
			case E_WARNING:
				// 2
				$typestr = 'E_WARNING';
				break;
			case E_PARSE:
				// 4
				$typestr = 'E_PARSE';
				break;
			case E_NOTICE:
				// 8
				$typestr = 'E_NOTICE';
				break;
			case E_CORE_ERROR:
				// 16
				$typestr = 'E_CORE_ERROR';
				break;
			case E_CORE_WARNING:
				// 32
				$typestr = 'E_CORE_WARNING';
				break;
			case E_COMPILE_ERROR:
				// 64
				$typestr = 'E_COMPILE_ERROR';
				break;
			case E_CORE_WARNING:
				// 128
				$typestr = 'E_COMPILE_WARNING';
				break;
			case E_USER_ERROR:
				// 256
				$typestr = 'E_USER_ERROR';
				break;
			case E_USER_WARNING:
				// 512
				$typestr = 'E_USER_WARNING';
				break;
			case E_USER_NOTICE:
				// 1024
				$typestr = 'E_USER_NOTICE';
				break;
			case E_STRICT:
				// 2048
				$typestr = 'E_STRICT';
				break;
			case E_RECOVERABLE_ERROR:
				// 4096
				$typestr = 'E_RECOVERABLE_ERROR';
				break;
			case E_DEPRECATED:
				// 8192
				$typestr = 'E_DEPRECATED';
				break;

			case E_USER_DEPRECATED:
				// 16384
				$typestr = 'E_USER_DEPRECATED';
				break;
			default:
				$fail = true;
				$typestr = 'E_UNKNOW[' . $errno . ']';
				break;
		}

		return $chk ? array(
			$fail,
			$typestr,
			$errno) : $typestr;
	}

	public static function phpinfo_array($what = INFO_ALL)
	{
		ob_start();
		phpinfo($what);
		$info_arr = array();
		$info_lines = explode(LF, strip_tags(ob_get_clean(), '<tr><td><h2>'));
		$cat = 'General';
		foreach ($info_lines as $line)
		{
			// new cat?
			preg_match('~<h2>(.*)</h2>~', $line, $title) ? $cat = $title[1] : null;
			if (preg_match('~<tr><td[^>]+>\s*([^<]*)\s*</td><td[^>]+>\s*([^<]*)\s*</td></tr>~', $line, $val))
			{
				$info_arr[$cat][trim($val[1])] = trim($val[2]);
			}
			elseif (preg_match('~<tr><td[^>]+>\s*([^<]*)\s*</td><td[^>]+>\s*([^<]*)\s*</td><td[^>]+>\s*([^<]*)\s*</td></tr>~', $line, $val))
			{
				$info_arr[$cat][trim($val[1])] = array('local' => trim($val[2]), 'master' => trim($val[3]));
			}
		}
		return $info_arr;
	}

	/**
	 * Defines a named constant at runtime.
	 */
	public static function define($name, $value)
	{
		return define(strtoupper($name), $value);
	}

	/**
	 * func_get_arg — Return an item from the argument list with reference
	 *
	 * @param int $arg_num
	 * @return mixed Returns the specified argument, or FALSE on error.
	 *
	 * @see http://cstruter.com/blog/144
	 */
	public static function &func_get_arg($arg_num)
	{
		$stack = debug_backtrace();

		if (@!isset($stack[1]['args'][$arg_num]))
		{
			trigger_error(sprintf('%s:  Argument %d not passed to function', __METHOD__, $arg_num), E_USER_WARNING);

			return false;
		}

		return $stack[1]['args'][$arg_num];
	}

	/**
	 * func_get_args — Returns an array comprising a function's argument list with reference
	 *
	 * @return array Returns an array in which each element is a copy of the corresponding member of the current user-defined function's argument list.
	 *
	 * @see http://cstruter.com/blog/144
	 */
	public static function func_get_args()
	{
		$stack = debug_backtrace();
		return (array )$stack[1]['args'];
	}

}
