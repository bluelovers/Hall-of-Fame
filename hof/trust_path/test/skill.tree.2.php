<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$data = HOF_Class_Yaml::load(dirname(__FILE__) . '/skill.tree.yml', $datas);

HOF_Class_File::mkdir(dirname(__FILE__) . '/skilltree/');

$newdata = array();

foreach ($data['base'] as $job => $skills)
{
	foreach ((array )$skills as $skill)
	{
		$v = array();
		$v['job'][] = $job;

		$newdata['skill'][$skill][]['and'] = $v;
	}
}

foreach ($data['job'] as $job => $list)
{
	foreach ((array )$list as $skill_need => $skills)
	{
		foreach ((array )$skills as $skill)
		{
			$v = array();
			$v['job'][] = $job;
			$v['skill'][] = $skill_need;

			$newdata['skill'][$skill][]['and'] = $v;
		}
	}
}

$lv_skill = array(
	'4000' => 20,
	'9000' => 5,
	'1000' => 1,
	);

foreach ($lv_skill as $skill => $lv)
{
	$v = array();
	$v['lv'][] = $lv;

	$newdata['skill'][$skill][]['and'] = $v;
}

$skill_mulit_need = array();

$skill_mulit_need[0] = array(
	'1119' => array('102', array(
			1114,
			1117,
			1102)),
	'2015' => array('201', array(2011, 2014)),
	'2041' => array('201', array(2000, 2021)),
	'2040' => array('201', array(2011, 2021)),
	'2055' => array('203', array(2461, 2462)),
	'2465' => array('203', array(2464, 2463)),
	'3103' => array('301', array(
			3102,
			3220,
			3230,
			)),
	'2307' => array('401', array(
			2306,
			2305,
			2302)),
	'2407' => array('402', array(2408, 2405)),
	'3308' => array('402', array(
			3300,
			3301,
			3302,
			3303)),
	'3310' => array('402', array(
			3300,
			3301,
			3302,
			3303)),
	);

foreach ($skill_mulit_need as $_list)
{
	foreach ($_list as $skill => $list)
	{
		$v = array();
		$v['job'][] = $list[0];
		$v['skill'] = $list[1];

		$newdata['skill'][$skill][]['and'] = $v;
	}
}

$skill_job = array(
	1001 => array(100, 101, 102, 103),
	1002 => array(200, 201, 202, 203),
	3010 => array(300, 301, 302),
	3101 => array(300, 301, 302),
	2300 => array(400, 401, 402, 403),
	2310 => array(400, 401, 402, 403),
);

foreach ($skill_job as $skill => $list)
{
	$v = array();
	$v['job'] = $list;

	$newdata['skill'][$skill][]['or'] = $v;
}

$skill = 3310;
$v1 = array();
$v1['job'][] = 200;
$v2 = array();
$v2['skill'][] = 3010;
$v = array('and' => $v1, 'not' => $v2);

debug($v);

$newdata['skill'][$skill][] = $v;

ksort($newdata['skill'], SORT_NUMERIC);

//debug($newdata['skill']);

foreach ($newdata['skill'] as $skill => $skill_data)
{
	//$skill_data = multiSort($skill_data, 'job', 'lv');

	foreach ($skill_data as $k => $v)
	{
		foreach ($v as &$__v)
		{
			foreach ($__v as $_k => &$_v)
			{
				$_v = array_unique($_v);
			}

			sort($_v, SORT_NUMERIC);
		}

		asort($v);

		//uasort($v, array(new HOF_Class_Array_Comparer_MuliteSubKey('job', 'lv', 'skill'), 'compare'));

		$skill_data[$k] = $v;
	}

	$newdata['skill'][$skill] = $skill_data;
}

foreach ($newdata['skill'] as $skill => $_data)
{
	$_data = array('no' => $skill, 'check' => $_data);

	HOF_Class_Yaml::save(dirname(__FILE__) . '/skilltree/skilltree.'.$skill.'.yml', $_data);
}

function multiSort()
{
	//get args of the function
	$args = func_get_args();
	$c = count($args);
	if ($c < 2)
	{
		return false;
	}
	//get the array to sort
	$array = array_splice($args, 0, 1);
	$array = $array[0];
	//sort with an anoymous function using args
	usort($array, function ($a, $b)use ($args)
	{

		$i = 0; $c = count($args); $cmp = 0; while ($cmp == 0 && $i < $c)
		{
			$cmp = strcmp($a[$args[$i]], $b[$args[$i]]); $i++; }

		return $cmp; }
	);

	return $array;

}

//usort($array,array(new cmp($key), "cmp__"));

class compare_subkey
{

	var $keys;

	function __construct($keys)
	{
		$this->keys = func_get_args();
	}

	function compare($a, $b)
	{
		$i = 0;
		$c = count($this->keys);

		$cmp = 0;
		while ($cmp == 0 && $i < $c)
		{
			$cmp = strcmp($a[$this->keys[$i]], $b[$this->keys[$i]]);
			$i++;
		}

		return $cmp;
	}

}
