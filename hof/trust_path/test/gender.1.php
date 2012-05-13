<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$map = array(
	'job',
	);

foreach ($map as $idx)
{
	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		$gender = $data['gender'];

		$data['gender'] = array();
		$data['gender'][1] = $gender[0];
		$data['gender'][2] = $gender[1];

		HOF_Class_Yaml::save($file, $data);
	}
}