<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

error_reporting(0);

define('REQUEST_TIME', time());
$_SERVER['REQUEST_TIME'] = REQUEST_TIME;

if (file_exists(dirname(__file__) . '/bootstrap.options.php'))
{
	include (dirname(__file__) . '/bootstrap.options.php');
}

if (file_exists(dirname(__file__) . '/config/setting.php'))
{
	require dirname(__file__) . '/config/setting.php';
	require dirname(__file__) . '/config/setting.dist.php';
}
else
{
	require dirname(__file__) . '/config/setting.dist.php';
}
