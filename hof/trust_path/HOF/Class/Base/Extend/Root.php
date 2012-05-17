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

	protected function _extend_init()
	{

	}

	public function extend_remove($extend, $idx = null)
	{
		if (is_object($extend))
		{
			$class = get_class($extend);
		}
		else
		{
			$class = $extend;
		}

		if (!$idx) $idx = $class;

		foreach((array)$this->_extends_[$idx]['method'] as $method)
		{
			unset($this->_extends_method_[$method]);
		}

		unset($this->_extends_[$idx]);

		return array($idx, $class);
	}

	public function extend($extend, $idx = null)
	{
		list($class, $idx) = $this->extend_remove($extend, $idx);

		$this->_extends_[$idx]['idx'] = $idx;

		if (is_object($extend))
		{
			$this->_extends_[$idx]['obj'] = &$extend;
		}
		else
		{
			$this->_extends_[$idx]['obj'] = null;
		}

		$this->_extends_[$idx]['class'] = $class;

		$methods = HOF_Helper_Object::get_public_methods($class, $this->_extends_method_invalids_);

		$this->_extends_[$idx]['method'] = $methods;

		foreach ($methods as $method)
		{
			$this->_extends_method_[$method] = $idx;
		}

		return $this;
	}

	public function __call($func, $argv)
	{
		if (!empty($this->_extends_method_[$func]))
		{
			$idx = $this->_extends_method_[$func];
			$class = $this->_extends_[$idx]['class'];

			if (empty($this->_extends_[$idx]['callback'][$func]))
			{
				if (!is_object($this->_extends_[$idx]['obj']))
				{
					$this->_extends_[$idx]['obj'] = new $class(&$this);
				}

				$this->_extends_[$idx]['callback'][$func] = array(&$this->_extends_[$idx]['obj'], $func);
			}

			return call_user_func_array($this->_extends_[$idx]['callback'][$func], $argv);
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

}
