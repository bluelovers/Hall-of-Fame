<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Yaml extends Symfony_Component_Yaml_Yaml
{

	function load($file)
	{
		$data = file_get_contents($file);

		$yaml = self::parse($data);

		return $yaml;
	}

	function save($file, $data)
	{
		return file_put_contents($file, self::dump($data), LOCK_EX);
	}

}

