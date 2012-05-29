<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char
{

	const OWNER_SYSTEM = 0;

	const TYPE_CHAR = 'Char';
	const TYPE_MON = 'Mon';
	const TYPE_UNION = 'Union';
	const TYPE_SUMMON = 'Summon';

	/**
	 * @return HOF_Class_Char_Abstract
	 */
	function factory($type, $id, $owner = self::OWNER_SYSTEM)
	{
		$class = HOF::putintoClassParts($type);
		$classname = 'HOF_Class_Char_Type_'.$class;

		if (!class_exists($classname))
		{
			throw new Exception("Char:$class is not supported");
		}

		$char = new $classname($id, $owner);

		if(!($char instanceof HOF_Class_Char_Abstract))
		{
			throw new Exception("\"$className\" is not an instance of HOF_Class_Char_Abstract");
		}

		return $char;
	}

}
