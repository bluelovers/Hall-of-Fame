<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Loader_Autoloader_Direct implements Zend_Loader_Autoloader_Interface
{

	protected $_autoloaders = array();

	public function autoload($class)
	{
		if (class_exists($class, false))
		{
			return true;
		}

		if ($autoloaders = &$this->getClassAutoloaders($class))
		{
			foreach ($autoloaders as $file)
			{
				Sco_Loader::loadFile($file, null, true, false, true);

				if (class_exists($class, false))
				{
					return true;
				}
			}
		}

		return false;
	}

	public function getClassAutoloaders($class)
	{
		return $this->_autoloaders[$class];
	}

	public function pushAutoloader($class, $file)
	{
		if (!isset($this->_autoloaders[$class]))
		{
			$this->_autoloaders[$class] = array();
		}

		array_push($this->_autoloaders[$class], $file);

		return $this;
	}

	public function unshiftAutoloader($class, $file)
	{
		if (!isset($this->_autoloaders[$class]))
		{
			$this->_autoloaders[$class] = array();
		}

		array_unshift($this->_autoloaders[$class], $file);

		return $this;
	}

}