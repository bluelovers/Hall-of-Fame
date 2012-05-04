<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Loader extends Zend_Loader
{

	public static function loadClass($class, $dirs = null, $ns = null)
	{
		try
		{
			if ($ns)
			{
				$_len = strlen($ns);

				if ($ns == substr($class, 0, $_len))
				{
					$_class = substr($class, $_len);
				}
			}

			parent::loadClass($_class, $dirs, $ns);
		}
		catch (Exception $e)
		{

		}

		if (!class_exists($class, false) && !interface_exists($class, false)) {
			parent::loadClass($class, $dirs);
		}

		if (!class_exists($class, false) && !interface_exists($class, false)) {
			throw new Zend_Exception("File \"$file\" does not exist or class \"$class\" was not found in the file");
		}
	}

}

