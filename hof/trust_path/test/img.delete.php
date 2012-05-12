<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("../bootstrap.php");

echo '<pre>';

echo "this script help to chk image file exists";

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$datas = array();

$map = array(
	'job' => HOF_Class_Icon::IMG_CHAR,
	'mon' => HOF_Class_Icon::IMG_CHAR,
	'item' => HOF_Class_Icon::IMG_ITEM,
	'skill' => HOF_Class_Icon::IMG_SKILL,
	'land' => HOF_Class_Icon::IMG_LAND,
	);

foreach ($map as $idx => $dir)
{
	$datas[$idx]['dir'] = $dir;

	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		$i = $data['no'];

		if ($idx == 'job')
		{
			$datas[$idx]['files'][] = $data['img_male'];
			$datas[$idx]['files'][] = $data['img_female'];
		}
		elseif ($idx == 'land')
		{
			$datas[$idx]['files'][] = 'bg_' . $data['land']['land'];
			$datas[$idx]['files'][] = 'land_' . $data['land']['land'];
		}
		else
		{
			$datas[$idx]['files'][$i] = $data['img'];
		}
	}

	if ($idx == 'land')
	{
		$path = BASE_PATH . HOF_Class_Icon::getImage('noimage', $dir);

		list($name, $ext) = HOF_Class_File::basename($path);

		$datas[$idx]['files'][] = $name;

		$datas[$idx]['files'][] = $name;
	}
	else
	{
		$datas[$idx]['files'][] = 'noimage';
	}


}

foreach ($datas as $idx => $data)
{
	$dir = $data['dir'];

	$data['files'] = array_unique((array )$data['files']);

	foreach ($data['files'] as $i => $no)
	{
		$path = BASE_PATH . HOF_Class_Icon::getImage($no, $dir, true);

		list($name, $ext) = HOF_Class_File::basename($path);

		if (file_exists($path))
		{
			$data['exists'][$i] = $name . $ext;
		}
		else
		{
			$data['lost'][$i] = $name . $ext;
		}
	}

	foreach (glob(BASE_PATH . $dir . '*') as $path)
	{
		if (is_dir($path)) continue;

		list($name, $ext) = HOF_Class_File::basename($path);

		$data['glob'][] = $name . $ext;
	}

	unset($data['files']);

	$datas[$idx] = $data;
}

$datas['char'] = array_merge_recursive($datas['job'], $datas['mon']);

$map2 = $map;
unset($map2['job']);
unset($map2['mon']);
$map2['char'] = 'char';

foreach (array_keys($map2) as $idx)
{

	if (is_array($datas[$idx]['dir']))
	{
		$dir = reset($datas[$idx]['dir']);
	}
	else
	{
		$dir = $datas[$idx]['dir'];
	}

	$dir = BASE_PATH . $dir;

	$dir_nouse = $dir . 'nouse/';

	@mkdir($dir_nouse);

	$datas[$idx]['glob'] = array_unique((array )$datas[$idx]['glob']);
	$datas[$idx]['exists'] = array_unique((array )$datas[$idx]['exists']);

	$datas[$idx]['diff'] = array_diff((array )$datas[$idx]['glob'], (array )$datas[$idx]['exists']);

	//	debug($dir, $dir_nouse, $datas[$idx]['diff']);
	continue;

	if ($idx != 'land')
	{

	foreach ($datas[$idx]['diff'] as $file)
	{
		$datas[$idx]['rename'][] = $file;
		rename($dir . $file, $dir_nouse . $file);
	}

	}

}

//exit;

foreach ($datas as $idx => $data)
{
	debug($idx, $data['dir'], $data['lost'], $data['files']);
}
