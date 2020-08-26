<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

//error_reporting(E_ALL);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

//error_reporting(E_ALL ^ E_NOTICE);


$salt = CRYPT_KEY;
$salt = '$1$'.md5(HOF_Class_Crypto_Salt::uniqid()).'$';
$salt = '$1$1236$77777';

debug($salt, CRYPT_SALT_LENGTH);

$map = array(
	'HOF_Class_Crypto_Discuz',
	'HOF_Class_Crypto_HOF',
	'HOF_Class_Crypto_MD5',
);

$val = 'admin';

foreach($map as $c)
{
	$class = $c::newInstance($salt);

	debug($c, $class->encode($val), $class->salt(456), $class);
}