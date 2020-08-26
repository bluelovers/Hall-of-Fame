<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 *
 * @method void setFlags
 * @method void exchangeArray
 *
 * @example
 * $a = new Dura_Class_Array();

 * $a->a = 1;
 * $a['b'] = 2;

 * echo '<pre>';
 * var_dump($a);

 * var_dump(array(
 * $a['a'],
 * $a->b,
 * (ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS),
 * ));
 */
class HOF_Class_Array_Base extends ArrayObject
{

	function __construct($input)
	{
		$this->setFlags(HOF_Class_Array::ARRAY_PROP_BOTH);
		$this->exchangeArray($input);
	}

}
