<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Loader extends Zend_Loader
{

	const NS_SEP = '\\';
	const CLASS_SEP = '_';

	protected static $_suppressNotFoundWarnings = false;

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

	protected static function _loadClass($class, $dirs, $class_sep = self::CLASS_SEP, $noerror = false)
	{
		// Autodiscover the path from the class name
		// Implementation is PHP namespace-aware, and based on
		// Framework Interop Group reference implementation:
		// http://groups.google.com/group/php-standards/web/psr-0-final-proposal
		$className = ltrim($class, self::NS_SEP);
		$file = '';
		$namespace = '';
		if ($lastNsPos = strrpos($className, self::NS_SEP))
		{
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$file = str_replace(self::NS_SEP, DIR_SEP, $namespace) . DIR_SEP;
		}
		$file .= str_replace($class_sep, DIR_SEP, $className) . '.php';

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
					$dirs[$key] = $dir . DIR_SEP . $dirPath;
				}
			}
			$file = basename($file);
			$return = self::loadFile($file, $dirs, true, false, $noerror);
		}
		else
		{
			$return = self::loadFile($file, null, true, false, $noerror);
		}

		return array(
			$return,
			$file,
			$dirs);
	}

	public static function existsClass($class, $autoload = true)
	{
		return (bool)(class_exists($class, $autoload) || interface_exists($class, $autoload));
	}

	public static function loadClass($class, $dirs = null, $ns = null, $class_sep = self::CLASS_SEP, $noerror = false)
	{
		if (!$class || self::existsClass($class, false))
		{
			return;
		}

		if ((null !== $dirs) && !is_string($dirs) && !is_array($dirs))
		{
			require_once 'Zend/Exception.php';
			throw new Zend_Exception('Directory argument must be a string or an array');
		}

		$chk = false;

		$class_sep === null && $class_sep = self::CLASS_SEP;

		list($return, $file) = self::_loadClass($class, $dirs, $class_sep, ($chk = ($ns && substr($class, 0, $_len = strlen($ns)) == $ns)) || $noerror);

		if ($chk && $class != $ns && !self::existsClass($class, false))
		{
			list($return, $file, $dirs) = self::_loadClass(substr($class, $_len), $dirs, $class_sep, $noerror);
		}

		if (self::existsClass($class, false))
		{
			return true;
		}
		elseif (!self::$_suppressNotFoundWarnings && !$noerror)
		{
			require_once 'Zend/Exception.php';
			throw new Zend_Exception("File \"$file\" does not exist or class \"$class\" was not found in the file");
		}

		return false;
	}

	function fileExists($file)
	{
		if (file_exists($file)) return true;

		foreach (explode(PATH_SEPARATOR, get_include_path()) as $path)
		{
			if (file_exists($path . DIR_SEP . $file)) return true;
		}

		return false;
	}

	public static function includeFile($filename, $once = false, $require = false, $noerror = false)
	{
		if ($noerror && !(file_exists($filename) || self::isReadable($filename)))
		{
			return false;
		}

		if ($require)
		{
			if ($once)
			{
				return require_once ($filename);
			}
			else
			{
				return require ($filename);
			}
		}
		else
		{
			if ($once)
			{
				return include_once ($filename);
			}
			else
			{
				return include ($filename);
			}
		}
	}

	public static function loadFile($filename, $dirs = null, $once = false, $require = false, $noerror = false)
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
			$incPath = get_include_path();
			set_include_path($dirs . PATH_SEPARATOR . $incPath);
		}

		/**
		 * Try finding for the plain filename in the include_path.
		 */
		$return = self::includeFile($filename, $once, $require, $noerror);

		/**
		 * If searching in directories, reset include_path
		 */
		if ($incPath)
		{
			set_include_path($incPath);
		}

		return $return;
	}

}
