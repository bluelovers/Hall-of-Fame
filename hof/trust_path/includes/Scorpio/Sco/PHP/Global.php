<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_PHP_Global
{

	public static function init()
	{
		static $init;

		if (isset($init)) return false;

		$_SERVER = new Sco_Array_Iterator_Lockable($_SERVER);
		$_GET = new Sco_Array_Iterator_Lockable($_GET);

		$_POST = new Sco_Array_Iterator_Lockable($_SERVER['REQUEST_METHOD'] == 'GET' ? array() : $_POST);

		$_SERVER->setName('_SERVER');
		$_GET->setName('_GET');
		$_POST->setName('_POST');

		$_SERVER->lock();
		$_GET->lock();
		$_POST->lock();

		$GLOBALS = array();

		return $init = true;
	}

}
