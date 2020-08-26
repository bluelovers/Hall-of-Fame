<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$list = HOF_Model_Data::getJudgeList();

foreach ($list as $no)
{
	$data = HOF_Model_Data::getJudgeData($no);

	unset($data['subs']);

	HOF_Model_Data::getInstance()->_save('judge', $no, $data);
}
