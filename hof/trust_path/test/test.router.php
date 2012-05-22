<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$c = array(
	'foo.bar',
	'foo-Bar',
	'foo_baR',
	'fooBar',
	'FooBar',
);

foreach ($c as $ov)
{
	$v = $ov;

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));

	$v = '-'.$ov.'-';

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));

	$v = ucfirst($ov);

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));

	$v = ucwords($ov);

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));
}

$str = 'log';

parse_str($str, $extra);

debug($extra);
