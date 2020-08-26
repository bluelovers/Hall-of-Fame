<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$_GET['a'] = 123;
$_GET['b'] = 456;
$_POST['b'] = 123;
$_POST['c'] = 123;

$request = new HOF_Class_Request();

debug((string)$request);

$post = new HOF_Class_Request_Query('_POST', (array)$_POST);

//$post['k'] = 2;
$post->k = 1;

$post->k2 = 1;

//$post['k'] = 9;

$post['v'] = 2;

debug((string)$post, $post, $post['k']);
