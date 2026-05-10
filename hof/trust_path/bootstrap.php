<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$t = microtime(true);

define('REQUEST_TIME', (!$_SERVER['REQUEST_TIME'] || $_SERVER['REQUEST_TIME'] > $t) ? $t : $_SERVER['REQUEST_TIME'] );
$_SERVER['REQUEST_TIME'] = REQUEST_TIME;

unset($t);

unset($_ENV['autoloaders']);

/**
 * 暫時停用以顯示原始 PHP 錯誤訊息
 * 正式環境可啟用
 *
 * Temporarily disabled to expose raw PHP errors during development
 */
//ob_start('ob_gzhandler');

if (file_exists(dirname(__file__) . '/bootstrap.options.php'))
{
	@include (dirname(__file__) . '/bootstrap.options.php');
}

if (file_exists(dirname(__file__) . '/config/setting.php'))
{
	@require dirname(__file__) . '/config/setting.php';
}

@require dirname(__file__) . '/config/setting.dist.php';

/**
 * 設定引用路徑，加入 trust_path 以支援 Zend 與 Symfony stub 類別載入
 *
 * trust_path/Zend/   — Zend Framework 1 Stub（Zend_Loader, Zend_Date, Zend_Session 等）
 * trust_path/Symfony/ — Symfony YAML Component Stub（YAML 解析與產生）
 *
 * 此設定使 PHP 的 autoloader 與 require_once 能透過 include_path 找到這些 stub，
 * 不需要依賴外部 php.ini 的路徑設定。
 *
 * Set include path to include trust_path for Zend and Symfony stub class loading.
 * This enables PHP autoloader and require_once to locate stubs via include_path
 * without relying on external php.ini configuration.
 */
set_include_path(
    dirname(__file__) . PATH_SEPARATOR . get_include_path()
);

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

foreach((array)$_ENV['autoloaders'] as $autoloader)
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

