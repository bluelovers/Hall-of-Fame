<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("../bootstrap.php");

$gamer = array();

foreach (glob(USER.'*', GLOB_ONLYDIR) as $user)
{

	$user = explode('/', $user);

	$name = end($user);

	$data = HOF_Class_User::getInstance($name);
	$data->CharDataLoadAll();

	$data->_cache();

	$gamer['user'][$data->id] = $data->name;
}

debug($gamer);

HOF_Class_Yaml::save(REGISTER.'.yml', $gamer);