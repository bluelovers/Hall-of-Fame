<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$arr = array(
	'a' => 1,
	'b' => array(
		1,
		2,
		3,
		),

	'c' => array('c2' => array('c3' => array('c4' => array(), ), ), ),
	);

$arr = new HOF_Class_Array_Prop($arr, 0, 4);

debug(is_array($arr), count($arr));

var_dump($arr);
var_dump($arr['c']);

$arr['d'] = array(789, array(999));

var_dump($arr['d']);

foreach($arr as $k => $v)
{
	debug($k);
}

var_dump($arr->getIterator());