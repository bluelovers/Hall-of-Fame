<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Loader extends Zend_Loader
{

	protected static $_suppressNotFoundWarnings = true;

	public static function loadClass($class, $dirs = null, $ns = null)
	{
		try
		{
			@parent::loadClass($class, $dirs);
		}
		catch (Exception $e)
		{
			HOF_Autoloader::$error[$ns][] = $e->getMessage();
		}

		if (!class_exists($class, false) && !interface_exists($class, false)) {
			if ($ns)
			{
				$_len = strlen($ns);

				if ($ns == substr($class, 0, $_len))
				{
					$_class = substr($class, $_len);

					if (self::$_suppressNotFoundWarnings)
					{
						@parent::loadClass($_class, $dirs, $ns);
					}
					else
					{
						parent::loadClass($_class, $dirs, $ns);
					}
				}
			}
		}

		if (!class_exists($class, false) && !interface_exists($class, false)) {
			if (!self::$_suppressNotFoundWarnings)
			{
				throw new Zend_Exception("File \"$file\" does not exist or class \"$class\" was not found in the file");
			}

			return false;
		}
		else
		{
			return true;
		}
	}

}

