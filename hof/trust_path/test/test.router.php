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

foreach ($c as $v)
{
	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));

	$v = ucfirst($v);

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));

	$v = ucwords($v);

	_e($v, HOF::putintoClassParts($v), HOF::putintoPathParts(HOF::putintoClassParts($v)));
}

