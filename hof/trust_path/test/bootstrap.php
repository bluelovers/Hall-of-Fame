<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("../bootstrap.php");

//Zend_Session::$_unitTestEnabled = true;

echo '<pre>';

function _e($v)
{
	$argv = func_get_args();
	echo implode(' ', $argv)."\n";
}

