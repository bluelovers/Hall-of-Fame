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
	static function factory($type, $no = null, $options = array(), $owner = null, $player = null)
	{
		$pre = '';

		$_class = is_array($type) ? $type : explode('_', HOF::putintoPathParts(HOF::putintoClassParts($type)));
		$_class = array_map('HOF::putintoClassParts', $_class);

		$idx = array_search(self::TYPE_UNION, $_class);
		if ($idx !== false)
		{
			$pre = self::TYPE_UNION;
			unset($_class[$idx]);
		}

		if (false !== $isSummon = array_search(self::TYPE_SUMMON, $_class))
		{
			$options['summon'] = true;
		}

		$class = $pre.implode('', $_class);

		$classname = 'HOF_Class_Char_Type_'.$class;

		if (!$found = class_exists($classname))
		{
			if ($isSummon !== false)
			{
				unset($_class[$isSummon]);

				$class = $pre.implode('', $_class);
				$classname = 'HOF_Class_Char_Type_'.$class;

				$found = class_exists($classname);
			}

			if (!$found)
			{
				throw new Exception("Char:$class is not supported");
			}
		}

		try
		{
			$char = new $classname($no, $options, $owner, $player);

			if(!($char instanceof HOF_Class_Char_Abstract))
			{
				throw new Exception("\"$className\" is not an instance of HOF_Class_Char_Abstract");
			}

			return $char;
		}
		catch(Exception $e)
		{
			return $e;
		}

		return null;
	}

}
