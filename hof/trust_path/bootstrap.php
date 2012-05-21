<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

error_reporting(0);

$t = microtime(true);

define('REQUEST_TIME', (!$_SERVER['REQUEST_TIME'] || $_SERVER['REQUEST_TIME'] > $t) ? $t : $_SERVER['REQUEST_TIME'] );
$_SERVER['REQUEST_TIME'] = REQUEST_TIME;

unset($t);

unset($_ENV['autoloaders']);

ob_start('ob_gzhandler');

if (file_exists(dirname(__file__) . '/bootstrap.options.php'))
{
	@include (dirname(__file__) . '/bootstrap.options.php');
}

if (file_exists(dirname(__file__) . '/config/setting.php'))
{
	@require dirname(__file__) . '/config/setting.php';
}

@require dirname(__file__) . '/config/setting.dist.php';

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

foreach($_ENV['autoloaders'] as $autoloader)
{
	HOF_Autoloader::getInstance()
		->pushAutoloader($autoloader[0], $autoloader[1])
	;
}

unset($_ENV['autoloaders']);

HOF_Loader::loadFile('syntax.func.php', BASE_TRUST_PATH.'HOF/Syntax', true);
HOF_Loader::loadFile('syntax.json.php', BASE_TRUST_PATH.'HOF/Syntax', true);

HOF_Loader::loadFile('const.game.php', BASE_TRUST_PATH.'HOF/Const', true);

HOF::getInstance();

//set_time_limit(60);

