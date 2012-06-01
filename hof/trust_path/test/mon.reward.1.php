<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$map = array('mon', );

$map_reward = array(
	'moneyhold',
	'itemtable',
	'exphold',
	);

foreach ($map as $idx)
{
	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		foreach ($map_reward as $k)
		{
			if (isset($data[$k]))
			{
				$data['reward'][$k] = $data[$k];
				unset($data[$k]);
			}
		}

		HOF_Class_Yaml::save($file, $data);
	}

}
