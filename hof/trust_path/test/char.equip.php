<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$map_equip = array(
		"weapon" => 'main-hand',
		"shield" => 'Off-hand',
		"armor" => 'armor',
		"item" => 'item',
		);

$map_attr = array(
		"hp",
		"max_hp",
		"sp",
		"max_sp",
		'str',
		'int',
		'dex',
		'spd',
		'luk',
		);

$map = array(
	'char',
	'mon',
	);

foreach ($map as $idx)
{
	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		/*
		foreach ($map_attr as $v)
		{
			$k = str_replace('_', '', $v);

			if ($data[$k])
			{
				$v = HOF::putintoClassParts($v);
				$v = HOF::putintoPathParts($v);

				$data['attr'][$v] = (int)$data[$k];
				unset($data[$k]);
			}
		}
		*/

		foreach ($map_equip as $k => $v)
		{
			if ($data[$k])
			{
				$v = HOF::putintoClassParts($v);
				$v = HOF::putintoPathParts($v);

				$data['equip'][$v] = (string)$data[$k];
				unset($data[$k]);
			}
		}


		HOF_Class_Yaml::save($file, $data);
	}
}