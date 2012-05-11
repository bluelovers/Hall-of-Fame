<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

foreach(array('judge') as $idx)
{
	$regex = HOF_Class_Data::_filename($idx, '*');
	$regex = '/^'.str_replace('\*', '(.+)', preg_quote($regex, '/')).'$/i';

	$last = null;

	$datas = array();

	$files = array();

	foreach(glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$no = preg_replace($regex, '$1', $file);
		$files[$no] = $file;
	}

	ksort($files, SORT_NUMERIC);

	foreach($files as $file)
	{
		$no = preg_replace($regex, '$1', $file);

		echo $file."\n";

		$data = HOF_Class_Yaml::load($file);

		if (!$data['no']) continue;

		unset($data['css']);
		unset($data['tag']);
		unset($data['subs']);
		unset($data['quantity']);

		if (in_num($data['no'], 1, 9000))
		{
			if (
				strpos($data['exp'], '○○') === false
				&& !in_array($data['no'], array(1600, 1700, 1701, 1000, 1001))
			)
			{
				$_cache['tag'] = $data['exp'];
				$_cache['tagid'] = (string)$data['no'];

				echo $data['no'].': '.$_cache['tag']."\n";

				$data['css'] = true;
			}

			if ($_cache['tag']) {
				$data['tag']['no'] = (string)$_cache['tagid'];
				$data['tag']['exp'] = $_cache['tag'];
			}

			if ($data['css'])
			{
				unset($data['quantity']);
				unset($data['tag']);
			}
			else
			{
				$data['quantity'] = (strpos($data['exp'], '○○') !== false) ? true : false;
			}

			if ($data['tag'] && isset($datas[(string)$_cache['tagid']]))
			{
				$datas[(string)$_cache['tagid']]['subs'][(string)$data['no']] = $data;
			}
		}

		$last = $data;

		$datas[(string)$data['no']] = $data;

		HOF_Class_Yaml::save($file, $data);
	}

	if (!empty($datas))
	{
		foreach($datas as $no => $data)
		{
			HOF_Class_Yaml::save(HOF_Class_Data::_filename($idx, (string)$no), $data);
		}
	}
}