<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Object
{

	function extend($root_obj, $class)
	{
		if (($root_obj instanceof HOF_Class_Base_Extend_RootInterface) || method_exists($root_obj, 'extend'))
		{
			return $root_obj->extend($class);
		}
		else
		{
			throw new RuntimeException('class not allow extend.');
		}
	}

	/**
	 * Get Class Public Methods
	 *
	 * @url http://hardforum.com/showthread.php?t=1480605
	 */
	function get_public_methods($class, $skip = array())
	{
		$methods = array();

		$skip = array_merge(array('__call', '__construct', '__destruct', '__get', '__set'), (array)$skip);

		foreach (get_class_methods($class) as $key => $method)
		{
			/* Get a reflection object for the class method */
			$reflect = new ReflectionMethod($class, $method);

			/* For private, use isPrivate().  For protected, use isProtected() */
			if ($reflect->isPublic() && !($reflect->isStatic() || $reflect->isConstructor() || $reflect->isDestructor()))
			{
				if (!in_array($method, $skip))
				{
					// Put the methods we care about into an array
					$methods[] = $method;
				}
			}
		}

		return $methods;
	}

}
