<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
spl_autoload_register(function ($class) {
    if (0 === strpos(ltrim($class, '/'), 'Symfony\Component\Templating')) {
        if (file_exists($file = __DIR__.'/../'.substr(str_replace('\\', '/', $class), strlen('Symfony\Component\Templating')).'.php')) {
            require_once $file;
        }
    }
});
*/

class Symfony_Component_Templating_Test_Autoloader
{
	/**
	 * Registers sfTemplateAutoloader as an SPL autoloader.
	 */
	static public function register()
	{
		//ini_set('unserialize_callback_func', 'spl_autoload_call');
		spl_autoload_register(array(new self, 'autoload'));
	}

	static public function unregister()
	{
		spl_autoload_unregister(array(new self, 'autoload'));
	}

	/**
	 * Handles autoloading of classes.
	 *
	 * @param  string  $class  A class name.
	 *
	 * @return boolean Returns true if the class has been loaded
	 */
	public function autoload($class)
	{
		if (0 !== $_pos = strpos($class, 'Symfony_Component_Templating_'))
		{
			return false;
		}
		elseif (!$_class = substr($class, strlen('Symfony_Component_Templating_')))
		{
			return false;
		}

		$_class = str_replace('_', '/', $_class);

		require __DIR__ . '/../' . $_class . '.php';

		if (!class_exists($class) && !interface_exists($class) && (!function_exists('trait_exists') || !trait_exists($class)))
		{
			throw new InvalidArgumentException(sprintf('Unable to load class "%s"', $class));
		}

		return true;
	}
}

Symfony_Component_Templating_Test_Autoloader::register();
