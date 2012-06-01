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

	protected static $_call_no_warning = false;

	protected $_call_work = false;

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

		foreach ((array )$this->_extends_[$idx]['method'] as $method)
		{
			$null = null;
			$this->_extends_method_[$method] = $null;

			unset($this->_extends_method_[$method]);
		}

		$null = null;
		$this->_extends_[$idx] = $null;

		unset($this->_extends_[$idx]);

		return array($idx, $class);
	}

	public function hasExtend($extend, $idx = null)
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

		return isset($this->_extends_[$idx]);
	}

	public function hasExtendMethod($method)
	{
		return isset($this->_extends_method_[$method]);
	}

	protected function refreshExtend()
	{
		foreach ($this->_extends_ as $idx => $data)
		{
			$class = $data['class'];

			$this->extend($class, $idx);
		}
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
		$this->_call_work = false;

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

			$this->_call_work = true;

			return call_user_func_array($this->_extends_[$idx]['callback'][$func], $argv);
		}
		elseif (!self::$_call_no_warning)
		{
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

}
