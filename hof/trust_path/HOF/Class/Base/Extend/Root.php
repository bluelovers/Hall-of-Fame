<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//class HOF_Class_Base_Extend_Root
class HOF_Class_Base_Extend_Root implements HOF_Class_Base_Extend_RootInterface
{

	protected $_extends_ = array();
	protected $_extends_method_ = array();

	protected $_extends_method_invalids_ = array();

	public function extend($extend)
	{
		$this->_extends_[$class] = null;

		if (is_object($extend))
		{
			$class = get_class($extend);
			$this->_extends_[$class]['obj'] = &$extend;
		}
		else
		{
			$class = $extend;
			$this->_extends_[$class]['obj'] = null;
		}

		$this->_extends_[$class]['class'] = $class;

		$methods = HOF_Helper_Object::get_public_methods($class, $this->_extends_method_invalids_);

		foreach ($methods as $v)
		{
			$this->_extends_method_[$v] = $class;
		}

		return $this;
	}

	public function __call($func, $argv)
	{
		if (isset($this->_extends_method_[$func]) && !empty($this->_extends_method_[$func]))
		{
			$class = $this->_extends_method_[$func];

			if (!$this->_extends_[$class]['callback'][$func])
			{
				if (!is_object($this->_extends_[$class]['obj']))
				{
					$this->_extends_[$class]['obj'] = new $class(&$this);

					$this->_extends_[$class]['callback'][$func] = array($this->_extends_[$class]['obj'], $func);
				}
			}

			return call_user_func_array($this->_extends_[$class]['callback'][$func], $argv);
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

}
