<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//error_reporting(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

unset($_ENV['autoloaders']);

ob_start('ob_gzhandler');

if (file_exists(dirname(__file__) . '/bootstrap.options.php'))
{
	@include (dirname(__file__) . '/bootstrap.options.php');
}

require_once ('Scorpio/bootstrap.php');

if (file_exists(dirname(__file__) . '/config/setting.php'))
{
	@require dirname(__file__) . '/config/setting.php';
}

@require dirname(__file__) . '/config/setting.dist.php';

/*
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
*/

Sco_Loader_Autoloader::getInstance()
	->pushAutoloader(BASE_TRUST_PATH, 'HOF_', true)
;

foreach($_ENV['autoloaders'] as $autoloader)
{
	Sco_Loader_Autoloader::getInstance()
		->pushAutoloader($autoloader[0], $autoloader[1])
	;
}

unset($_ENV['autoloaders']);

Sco_Loader::loadFile('syntax.func.php', BASE_TRUST_PATH.'HOF/Syntax', true);
Sco_Loader::loadFile('syntax.json.php', BASE_TRUST_PATH.'HOF/Syntax', true);

Sco_Loader::loadFile('const.game.php', BASE_TRUST_PATH.'HOF/Const', true);

HOF::getInstance();

//set_time_limit(60);

