<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

/*
foreach (HOF_Model_Data::getSkillTreeList() as $skill){
	HOF_Model_Data::getSkillTreeData($skill);
}
*/

$jobs = array(0, 100, 200, 202, 402);
//$jobs = array(202);

foreach ($jobs as $job)
{
	$list[$job] = HOF_Model_Data::getSkillTreeListByJob($job);
}

debug($list);