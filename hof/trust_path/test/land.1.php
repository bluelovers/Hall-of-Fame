<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$list = HOF_Model_Data::getLandList();

foreach ($list as $no)
{
	$data = HOF_Model_Data::getLandData($no);

	unset($data['subs']);

	HOF_Model_Data::getInstance()->_save('land', $no, $data);
}
