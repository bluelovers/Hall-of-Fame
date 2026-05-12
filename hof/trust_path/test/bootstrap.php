<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/** 先載入 bootstrap-core.php 取得 PROJECT_TEST_PATH（無依賴）*/
require_once dirname(__FILE__) . '/../bootstrap-core.php';

require_once ("../bootstrap.php");

//Zend_Session::$_unitTestEnabled = true;

echo '<pre>';

function _e($v)
{
	$argv = func_get_args();
	echo implode(' ', $argv)."\n";
}

