<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Yaml extends Symfony_Component_Yaml_Yaml
{

	const INLINE = 6;

	protected static $_enableDefaultDumpFilter = false;
	protected static $_defaultDumpFilter;

	public static function load($file, $enablePhpParsing = false)
	{
		if (Sco_File_Helper::is_resource_file($file))
		{
			$data = Sco_File_Helper::fp_get_contents($file);
		}
		elseif (is_readable($file))
		{
			$data = $file;
		}
		else
		{
			return false;
		}

		$yaml = self::parse($data);

		return $yaml;
	}

	public static function save($file, $data, $inline = self::INLINE, $callback = null)
	{
		$dump = self::dump($data, $inline, $callback);

		if (Sco_File_Helper::is_resource_file($file))
		{
			$ret = Sco_File_Helper::fp_put_contents($file, $dump, LOCK_EX);
		}
		else
		{
			$ret = file_put_contents($file, $dump, LOCK_EX);
		}

		return $ret;
	}

	public static function dump($array, $inline = self::INLINE, $callback = null)
	{
		if (is_object($array) && method_exists($array, 'toYaml'))
		{
			return $array->toYaml($inline, $callback);
		}

		if ($callback && is_callable($callback))
		{
			$dump = call_user_func($callback, $array, $inline);
		}
		elseif (self::$_enableDefaultDumpFilter && is_callable(self::$_defaultDumpFilter))
		{
			$dump = call_user_func(self::$_defaultDumpFilter, $array, $inline);
		}
		else
		{
			$dump = $array;
		}

		return parent::dump($dump, $inline);
	}

	public static function setDefaultDumpFilter($callback)
	{
		$old = self::$_defaultDumpFilter;

		self::$_defaultDumpFilter = $callback;

		return $old;
	}

	public static function enableDefaultDumpFilter($flag = null)
	{
		if (null === $flag)
		{
			return self::$_enableDefaultDumpFilter;
		}

		$old = self::$_enableDefaultDumpFilter;

		self::$_enableDefaultDumpFilter = (bool)$flag;

		return $old;
	}

}
