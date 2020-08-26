<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_PHP_Handler_Interface
{

	public static function start();

	public static function set($error_handler);

	public static function get();

	public static function stop();

}
