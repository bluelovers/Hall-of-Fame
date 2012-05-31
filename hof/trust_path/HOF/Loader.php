<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Loader extends Zend_Loader
{

	protected static $_suppressNotFoundWarnings = false;

	/**
	 * Loads a class from a PHP file.  The filename must be formatted
	 * as "$class.php".
	 *
	 * If $dirs is a string or an array, it will search the directories
	 * in the order supplied, and attempt to load the first matching file.
	 *
	 * If $dirs is null, it will split the class name at underscores to
	 * generate a path hierarchy (e.g., "Zend_Example_Class" will map
	 * to "Zend/Example/Class.php").
	 *
	 * If the file was not found in the $dirs, or if no $dirs were specified,
	 * it will attempt to load it from PHP's include_path.
	 *
	 * @param string $class      - The full class name of a Zend component.
	 * @param string|array $dirs - OPTIONAL Either a path or an array of paths
	 *                             to search.
	 * @return void
	 * @throws Zend_Exception
	 */
	public static function _loadClass($class, $dirs = null)
	{
		if ((null !== $dirs) && !is_string($dirs) && !is_array($dirs))
		{
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Directory argument must be a string or an array');
		}

		$className = ltrim($class, '\\');
		$file = '';
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\'))
		{
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$file .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		if (!empty($dirs))
		{
			// use the autodiscovered path

			$dirPath = dirname($file);

			if (is_string($dirs))
			{
				$dirs = explode(PATH_SEPARATOR, $dirs);
			}

			foreach ($dirs as $key => $dir)
			{
				if ($dir == '.')
				{
					$dirs[$key] = $dirPath;
				}
				else
				{
					$dir = rtrim($dir, '\\/');
					$dirs[$key] = $dir . DIRECTORY_SEPARATOR . $dirPath;
				}
			}
			$file = basename($file);

			self::_loadFile($file, $dirs, true);
		}
		else
		{
			self::_loadFile($file, null, true);
		}
	}

	/**
	 * Loads a PHP file.  This is a wrapper for PHP's include() function.
	 *
	 * $filename must be the complete filename, including any
	 * extension such as ".php".  Note that a security check is performed that
	 * does not permit extended characters in the filename.  This method is
	 * intended for loading Zend Framework files.
	 *
	 * If $dirs is a string or an array, it will search the directories
	 * in the order supplied, and attempt to load the first matching file.
	 *
	 * If the file was not found in the $dirs, or if no $dirs were specified,
	 * it will attempt to load it from PHP's include_path.
	 *
	 * If $once is TRUE, it will use include_once() instead of include().
	 *
	 * @param  string        $filename
	 * @param  string|array  $dirs - OPTIONAL either a path or array of paths
	 *                       to search.
	 * @param  boolean       $once
	 * @return boolean
	 * @throws Zend_Exception
	 */
	public static function _loadFile($filename, $dirs = null, $once = false)
	{
		self::_securityCheck($filename);

		/**
		 * Search in provided directories, as well as include_path
		 */
		$incPath = false;
		if (!empty($dirs) && (is_array($dirs) || is_string($dirs)))
		{
			if (is_array($dirs))
			{
				$dirs = implode(PATH_SEPARATOR, $dirs);
			}

			if (strpos($dirs, PATH_SEPARATOR) !== false)
			{
				$incPath = get_include_path();
				set_include_path($dirs . PATH_SEPARATOR . $incPath);
			}
			else
			{
				$filename = $dirs . DIRECTORY_SEPARATOR . $filename;
			}
		}

		/**
		 * Try finding for the plain filename in the include_path.
		 */
		if (($incPath && self::isReadable($filename)) || file_exists($filename))
		{
			if ($once)
			{
				include_once $filename;
			}
			else
			{
				include $filename;
			}
		}

		/**
		 * If searching in directories, reset include_path
		 */
		if ($incPath)
		{
			set_include_path($incPath);
		}

		return true;
	}

	public static function loadClass($class, $dirs = null, $ns = null)
	{
		if (class_exists($class, false) || interface_exists($class, false))
		{
			return;
		}

		self::_loadClass($class, $dirs);

		if (!class_exists($class, false) && !interface_exists($class, false))
		{
			if ($ns)
			{
				$_len = strlen($ns);

				if ($ns == substr($class, 0, $_len))
				{
					$_class = substr($class, $_len);

					self::_loadClass($_class, $dirs);
				}
			}
		}

		if (!class_exists($class, false) && !interface_exists($class, false))
		{

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
