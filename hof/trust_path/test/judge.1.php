<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$map = array(
	'char',
//	'job',
//	'mon',
	);

foreach ($map as $idx)
{
	foreach (glob(HOF_Class_Data::_filename($idx, '*')) as $file)
	{
		$data = HOF_Class_Yaml::load($file);

		if ($idx == 'char')
		{
			if ($data['Pattern'])
			{
				$data['pattern'] = array();

				foreach ($data['Pattern']['judge'] as $i => $v)
				{
					$data['pattern'][$i]['judge'] = $data['Pattern']['judge'][$i];
					$data['pattern'][$i]['quantity'] = $data['Pattern']['quantity'][$i];
					$data['pattern'][$i]['action'] = $data['Pattern']['action'][$i];
				}

				_e("Update [$idx]:{$data[no]}");
			}

			unset($data['Pattern']);

			$_cache[$idx][$data['job']] = $data;
		}
		elseif ($idx == 'job')
		{
			if ($_cache['char'][$data['no']])
			{
				$data['pattern'] = $_cache['char'][$data['no']]['pattern'];
			}

			$data['job'] = $data['no'];

			if (isset($data['img_male']))
			{
				$data['img'] = $data['img_male'];

				$data['gender']['1']['img'] = $data['img_female'];
				$data['gender']['0']['img'] = $data['img_male'];
			}

			if (isset($data['name_male']))
			{
				$data['job_name'] = $data['name_male'];

				$data['gender']['1']['job_name'] = $data['name_female'];
				$data['gender']['0']['job_name'] = $data['name_male'];
			}

			unset($data['img_male']);
			unset($data['img_female']);

			unset($data['name_male']);
			unset($data['name_female']);

			_e("Update [$idx]:{$data[no]}");
		}
		elseif ($idx == 'mon')
		{
			if (isset($data['judge']) || isset($data['quantity']) || isset($data['action']))
			{
				$data['pattern'] = array();

				foreach ($data['judge'] as $i => $v)
				{
					if (!$data['judge'][$i] || !$data['action'][$i]) continue;

					$data['pattern'][$i]['judge'] = $data['judge'][$i];
					$data['pattern'][$i]['quantity'] = $data['quantity'][$i];
					$data['pattern'][$i]['action'] = $data['action'][$i];
				}

				_e("Update [$idx]:{$data[no]}");
			}

			unset($data['judge']);
			unset($data['quantity']);
			unset($data['action']);
		}

		//ksort($data);

		HOF_Class_Yaml::save($file, $data);
	}
}