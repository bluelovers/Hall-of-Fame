<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

include_once (DATA_SKILL_TREE);

$fake_char = new HOF_Class_Array();

$fake_char->skill = array();

$job_list = HOF_Model_Data::getJobList();

$skill_list = HOF_Model_Data::getSkillList();

$datas = array();

$_tree_null = LoadSkillTree($fake_char);

foreach ($job_list as $job)
{
	$fake_char->job = $job;

	$_tree_job = LoadSkillTree($fake_char);

	$datas['base'][$job] = $_tree_job;

	$fake_char->skill = $skill_list;

	$_tree_all = LoadSkillTree($fake_char);

	$_tree_all = array_diff((array)$_tree_all, (array)$_tree_job);

	sort($_tree_all);

	$datas['base_2'][$job] = $_tree_all;

	$fake_char->skill = array();

	foreach($skill_list as $skill)
	{
		$fake_char->skill = array($skill);

		if ($_tree = LoadSkillTree($fake_char))
		{
			if ($v = array_diff((array)$_tree, (array)$_tree_job))
			{
				$datas['char_skill_3'][] = $skill;

				sort($v, SORT_NUMERIC);

				$datas['job'][$job][$skill] = $v;

				$datas['char_skill_2'] = array_merge((array)$datas['char_skill_2'], (array)$v);
			}
		}
	}

	$datas['char_skill'] = array_merge((array)$datas['char_skill'], $_tree_job, (array)$datas['char_skill_2'], (array)$datas['char_skill_3']);
}

foreach ($datas['job'] as $job => $list)
{
	$base = floor($job / 100) * 100;

	if ($base != $job)
	{
		$v = array_diff((array)$datas['base_2'][$job], (array)$datas['base_2'][$base]);

		sort($v, SORT_NUMERIC);

		$datas['base_3'][$job] = $v;

		/*
		for ($i = 0; $i <= 4; $i++)
		{
			if (($i + $base) == $job) continue;

			$v = array_diff((array)$v, (array)$datas['base_2'][$base + $i]);
		}

		sort($v, SORT_NUMERIC);

		$datas['base_4'][$job] = $v;
		*/

		foreach ($list as $skill => $_tree)
		{
			if ($v = array_diff((array)$_tree, (array)$datas['job'][$base][$skill], (array)$datas['base'][$job]))
			{
				sort($v, SORT_NUMERIC);

				$datas['job_2'][$job][$skill] = $v;
			}
		}
	}
	else
	{
		$datas['job_2'][$job] = $list;
	}

}

$datas['char_skill'] = array_unique((array)$datas['char_skill']);
$datas['char_skill_2'] = array_unique((array)$datas['char_skill_2']);
$datas['char_skill_3'] = array_unique((array)$datas['char_skill_3']);

sort($datas['char_skill']);

$datas['none_skill'] = array_diff((array)$skill_list, (array)$datas['char_skill']);

sort($datas['none_skill']);

$datas['char_skill_2'] = array_unique((array)$datas['char_skill_2']);

sort($datas['char_skill_2']);

$datas['none_skill_2'] = array_diff((array)$skill_list, (array)$datas['char_skill_2']);

sort($datas['none_skill_2']);


sort($datas['char_skill_3']);

$v = array_diff((array)$datas['char_skill_3'], (array)$datas['char_skill_2']);

foreach ($datas['base'] as $job => $list)
{
	$v = array_diff($v, (array)$list, (array)$datas['base_2'][$job]);
}

$datas['none_skill_3'] = $v;

sort($datas['none_skill_3']);

debug(array(
	'skill' => count($skill_list),
	'char_skill' => count($datas['char_skill']),
	'char_skill_2' => count($datas['char_skill_2']),

	'char_skill_3' => count($datas['char_skill_3']),

	'none_skill' => count($datas['none_skill']),
	'none_skill_2' => count($datas['none_skill_2']),
	'none_skill_3' => count($datas['none_skill_3']),
));

//unset($datas['none_skill']);
//unset($datas['char_skill']);
//unset($datas['char_skill_2']);



HOF_Class_Yaml::save(dirname(__FILE__).'/skill.tree.yml', $datas);
