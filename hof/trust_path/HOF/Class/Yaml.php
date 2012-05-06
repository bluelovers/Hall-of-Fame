<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Yaml extends Symfony_Component_Yaml_Yaml
{

	function load($file, $enablePhpParsing = false)
	{
		HOF_Class_Yaml::$enablePhpParsing = $enablePhpParsing;

		if (file_exists($file))
		{
			$yaml = self::parse($file);
		}
		else
		{
			$yaml = false;
		}

		HOF_Class_Yaml::$enablePhpParsing = false;

		return $yaml;
	}

	function save($file, $data)
	{
		return file_put_contents($file, self::dump($data), LOCK_EX);
	}

}

