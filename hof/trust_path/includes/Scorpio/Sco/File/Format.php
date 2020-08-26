<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_File_Format
{

	/**
	 * @assert ('../test/.././test/./.\/\/\test.txt') == '../test/test.txt/'
	 * @assert ('../test/.././test/./.\/\/\test.txt', '', true) == '../test/'
	 * @assert ('../test/.././test/./.\/\/\test.txt', '../', true) == '../'
	 */
	public static function dirname($path, $chdir = '', $dirnamefunc = false)
	{
		if ($dirnamefunc) $path = dirname($path);

		return ($chdir) ? self::path($path, $chdir) : self::path($path);
	}

	/**
	 * @assert ('../test/.././test/./.\/\/\/') == '../test/'
	 * @assert ('../test/.././test/./.\/\/\.') == '../test/.'
	 * @assert ('../test/.././test/./.\/\/\test.txt') == '../test/test.txt'
	 */
	public static function fix($url)
	{
		// FIXME - fix url::fix regex

		return preg_replace(array( //			'/([\\/]+(\s*\.\s*[\\/]+)*)+/i',
			'/([\\\\\\/]+(\s*\.\s*[\\\\\\/]+)*)+/i',
			'/\/+[^\.\/:]+\/+([^\.\/:]+\/\s*\.\.\s*\/+)?\s*\.\.\s*\/+/i',
			'/(^|\/+)[^\.\/:]+\/+\s*\.\.\s*\/+/i',
			'/^\.\/+/i',
			'/(^|\/+)[^\.\/:]+\/+\s*\.\.\s*$/i',
			), array(
			DIR_SEP,
			DIR_SEP,
			'$1',
			'',
			'$1'), trim($url));
	}

	protected static function _path_join()
	{
		if (func_num_args() > 1 || is_array($array = func_get_arg(0)))
		{
			self::_recursive2array(func_get_args(), $array);
		}

		/*
		if (func_num_args() > 1)
		{
		//$array = $args;

		foreach ($args as $arg)
		{
		if (is_array($arg))
		{

		}
		}
		}
		else
		{
		$array = $args[0];
		if (is_array($array[0]))
		{
		$array = $array[0];
		}
		}
		*/

		if (is_string($array)) return $array;

		$ret = '';
		do
		{
			$ret = array_shift($array);
		} while (self::_empty($ret));

		if (!empty($array))
		{
			foreach ($array as $_v)
			{
				$_v = trim($_v);
				if (self::_empty($_v)) continue;

				$ret .= DIR_SEP . $_v;
			}
		}

		return $ret;
	}

	protected static function _empty($ret)
	{
		return (empty($ret) && $ret !== 0 && $ret !== '0');
	}

	/**
	 * @assert ('../test/.././test/./.\/\/\/') == '../test/'
	 * @assert ('../test/.././test/./.\/\/\/.') == '../test/'
	 * @assert ('../test/.././test/./.\/\/\test.txt') == '../test/test.txt/'
	 */
	public static function path()
	{
		return rtrim(self::fix(self::_path_join(func_get_args(), DIR_SEP)), DIR_SEP) . DIR_SEP;
	}

	/**
	 * @assert ('../test/.././test/./.\/\/\test.txt') == '../test/test.txt'
	 */
	public static function file()
	{
		return rtrim(self::fix(self::_path_join(func_get_args())), DIR_SEP);
	}

	/**
	 * @assert ('../test/.././test/./.\/\/\test.txt', '..') == 'test/test.txt'
	 */
	public static function remove_root($path, $root)
	{
		$root = self::path($root);
		$path = self::file($path);

		$ret = (strpos($path, $root) === 0) ? substr($path, strlen($root)) : $path;

		return $ret;
	}

	public static function basename($path, $suffix = '')
	{
	    $v = preg_split('/(\?|#)/', $path);
		return basename(array_shift($v), $suffix);
	}

	protected static function _recursive2array($array, &$return = null)
	{
		if (func_num_args() <= 2 || !func_get_arg(2))
		{
			$return = array();
		}

		foreach ($array as $entry)
		{
			if (is_array($entry))
			{
				self::_recursive2array($entry, $return, true);
			}
			else
			{
				$return[] = $entry;
			}
		}

		return $return;
	}

}
