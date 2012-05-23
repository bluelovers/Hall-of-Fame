<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Yaml extends Symfony_Component_Yaml_Yaml
{

	const INLINE = 6;
	static $auto_addslashes = false;
	static $auto_fixarray = -1;

	public static function load($file, $enablePhpParsing = false)
	{
		if (HOF_Class_File::is_resource_file($file) || file_exists($file))
		{
			if (HOF_Class_File::is_resource_file($file))
			{
				$data = HOF_Class_File::fp_get_contents($file);
			}
			else
			{
				HOF_Class_File::opened_files_add($file);

				$data = $file;
			}

			$old = HOF_Class_Yaml::$enablePhpParsing;
			if ($old != $enablePhpParsing) HOF_Class_Yaml::$enablePhpParsing = $enablePhpParsing;

			$yaml = self::parse($data);

			if ($old != HOF_Class_Yaml::$enablePhpParsing) HOF_Class_Yaml::$enablePhpParsing = $old;

			if (self::$auto_addslashes)
			{
				$yaml = HOF::stripslashes($yaml);
			}
		}
		else
		{
			$yaml = false;
		}

		return $yaml;
	}

	public static function save($file, $data, $inline = HOF_Class_Yaml::INLINE)
	{
		if (self::$auto_addslashes)
		{
			$data = HOF::addslashes($data);
		}

		if (self::$auto_fixarray !== null && self::$auto_fixarray !== false && self::$auto_fixarray >= -1)
		{
			$data = HOF_Class_Array::_fixArrayRecursive($data, self::$auto_fixarray == -1 ? HOF_Class_Array::ARRAY_RECURSIVE_ALL : self::$auto_fixarray);
		}

		$dump = self::dump($data, $inline);

		if (is_resource($file))
		{
			ftruncate($file, 0);
			rewind($file);
			fputs($file, $dump);

			return true;
		}
		else
		{
			$ret = file_put_contents($file, $dump, LOCK_EX);
		}

		return $ret;
	}

	public static function dump($array, $inline = HOF_Class_Yaml::INLINE)
	{
		return parent::dump($array, $inline);
	}

}

