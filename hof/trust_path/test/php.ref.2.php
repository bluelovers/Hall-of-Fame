<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

/*
$ref[0] = new ReflectionClass('HOF_Class_Array');

$props[0] = $ref[0]->getProperties(ReflectionProperty::IS_PROTECTED);
*/

$ref[1] = new HOF_Class_Reflection_Class('HOF_Class_Array_Prop');

$props[1] = $ref[1]->getProperties(HOF_Class_Reflection_Property::IS_PROP | HOF_Class_Reflection_Property::IS_PUBLIC | -HOF_Class_Reflection_Property::IS_PUBLIC);

debug($props);

$a = new HOF_Class_Array_Prop(array(
	1,
	2,
	3,
	'o' => 'o',
	'a' => 9,
	));

debug($a);

var_dump($a);

var_dump(new ArrayIterator(array()));
