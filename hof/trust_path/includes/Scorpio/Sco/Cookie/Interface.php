<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_Cookie_Interface
{

	public function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null);
	public function get($index, $default = null);
	public function save();

}
