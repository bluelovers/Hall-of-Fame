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

	protected static $_suppressNotFoundWarnings = true;

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

		$class = $pre . implode('', $_class);

		static $cache;

		if ($cache[$class])
		{
			$classname = $cache[$class];
		}
		else
		{

			$classname = 'HOF_Class_Char_Type_' . $class;

			$found = false;

			$old = HOF_Loader::suppressNotFoundWarnings(true);

			try
			{
				$found = @class_exists($classname);
			}
			catch (Exception $e)
			{

			}

			if (!$found)
			{
				if ($isSummon !== false)
				{
					unset($_class[$isSummon]);

					$class = $pre . implode('', $_class);
					$classname = 'HOF_Class_Char_Type_' . $class;

					$found = class_exists($classname);
				}

				if (!$found)
				{
					throw new Exception("Char:$class is not supported");
				}
			}

			HOF_Loader::suppressNotFoundWarnings($old);

			$cache[$class] = $classname;

		}

		if (!self::$_suppressNotFoundWarnings)
		{
			$char = new $classname($no, $options, $owner, $player);

			if (!($char instanceof HOF_Class_Char_Abstract))
			{
				throw new Exception("\"$className\" is not an instance of HOF_Class_Char_Abstract");
			}

			return $char;
		}
		else
		{
			try
			{
				$char = new $classname($no, $options, $owner, $player);

				if (!($char instanceof HOF_Class_Char_Abstract))
				{
					throw new Exception("\"$className\" is not an instance of HOF_Class_Char_Abstract");
				}

				return $char;
			}
			catch (Exception $e)
			{
				return $e;
			}
		}

		return null;
	}

	public function suppressNotFoundWarnings($flag = null)
	{
		if (null === $flag)
		{
			return self::$_suppressNotFoundWarnings;
		}

		$old = self::$_suppressNotFoundWarnings;

		self::$_suppressNotFoundWarnings = (bool)$flag;

		return $old;
	}

}
