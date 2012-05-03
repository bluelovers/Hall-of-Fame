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
}

require dirname(__file__) . '/config/setting.dist.php';

ob_start('ob_gzhandler');

require_once ('Zend/Loader/Autoloader.php');

Zend_Loader_Autoloader::getInstance()
	->suppressNotFoundWarnings(true)
;

Zend_Loader::loadClass('HOF_Autoloader', BASE_TRUST_PATH);
Zend_Loader::loadClass('HOF_Loader', BASE_TRUST_PATH);

HOF_Autoloader::getInstance()
	->pushAutoloader(BASE_TRUST_PATH, 'HOF_')
	->setDefaultAutoloader(array('HOF_Loader', 'loadClass'));
;



