<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Reflection_Helper
{

	protected static $_cache;
	protected static $_enableCache = false;

	public static function enableCache($flag = null)
	{
		if (null === $flag)
		{
			return self::$_enableCache;
		}

		$old = self::$_enableCache;

		self::$_enableCache = (bool)$flag;

		return $old;
	}

	/**
	 * Get Class Public Methods
	 *
	 * @url http://hardforum.com/showthread.php?t=1480605
	 */
	public static function get_public_methods($class, $skip = array(), $over = false, $include_static = false)
	{
		if (!self::$_enableCache || $over || !isset(self::$_cache[__FUNCTION__][$class]))
		{
			$methods = array();

			$_skip = array('__call', '__construct', '__destruct', '__get', '__set');

			foreach (get_class_methods($class) as $key => $method)
			{
				/* Get a reflection object for the class method */
				$reflect = new ReflectionMethod($class, $method);

				/* For private, use isPrivate().  For protected, use isProtected() */
				if ($reflect->isPublic() && !(!$include_static && $reflect->isStatic() || $reflect->isConstructor() || $reflect->isDestructor()))
				{
					if (!in_array($method, $_skip))
					{
						// Put the methods we care about into an array
						$methods[] = $method;
					}
				}
			}

			$methods = array_values(array_diff((array)$methods, (array)$_skip));

			self::$_enableCache && self::$_cache[__FUNCTION__][$class] = $methods;
		}
		else
		{
			$methods = self::$_cache[__FUNCTION__][$class];
		}

		return array_diff($methods, (array)$skip);
	}

}
