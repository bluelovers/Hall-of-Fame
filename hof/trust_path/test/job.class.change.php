<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$classchange = array(
	101 => array(
		'job' => array(100),
		'lv' => 20,
	),
	102 => array(
		'job' => array(100),
		'lv' => 25,
	),
	103 => array(
		'job' => array(100),
		'lv' => 23,
	),
	201 => array(
		'job' => array(200),
		'lv' => 20,
	),
	202 => array(
		'job' => array(200),
		'lv' => 25,
	),
	203 => array(
		'job' => array(200),
		'lv' => 22,
	),
	301 => array(
		'job' => array(300),
		'lv' => 25,
	),
	302 => array(
		'job' => array(300),
		'lv' => 20,
	),
	401 => array(
		'job' => array(400),
		'lv' => 20,
	),
	402 => array(
		'job' => array(400),
		'lv' => 25,
	),
	403 => array(
		'job' => array(400),
		'lv' => 22,
	),
);

$map = array(
	'job',
	);

foreach ($map as $idx)
{
	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		$no = $data['no'];

		unset($data['data_ex']['job_conditions']);
		if (empty($data['data_ex'])) unset($data['data_ex']);

		if ($classchange[$no])
		{
			$data['data_ex']['job_conditions'] = array();

			foreach ($classchange[$no]['job'] as $k)
			{
				$data['data_ex']['job_conditions']['job_from'][$k]['lv'] = $classchange[$no]['lv'];
			}

			if (empty($data['data_ex']['job_conditions'])) unset($data['data_ex']['job_conditions']);
		}

		$basejob = floor($data['job'] / 100) * 100;

		unset($data['data_ex']['job_base']);

		if ($basejob != $data['job'])
		{
			$data['data_ex']['job_base'] = $basejob;
		}

		if ($data['data_ex']['info']) $data['info'] = $data['data_ex']['info'];
		unset($data['data_ex']['info']);

		unset($data['change']);
		if (empty($data['data_ex'])) unset($data['data_ex']);

		if (!$data['coe']['maxhp'])
		{
			list($hpc, $spc) = $data['coe'];
			$data['coe'] = array();
			$data['coe']['maxhp'] = $hpc;
			$data['coe']['maxsp'] = $spc;
		}

		$datas[$idx][$no] = $data;

		HOF_Class_Yaml::save($file, $data);
	}
}