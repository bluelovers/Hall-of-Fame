<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Base_ObjectAttr extends HOF_Class_Array
{

	/**
	 * jQuery style attr
	 */
	function attr($attr, $value = null)
	{
		if (is_string($attr))
		{
			if ($value === null)
			{
				return $this->$attr;
			}

			$this->$attr = $value;
		}
		elseif (is_array($attr))
		{
			foreach ($attr as $k => $v)
			{
				$this->$k = $v;
			}
		}

		return $this;
	}

	function __call($func, $args)
	{
		if (property_exists($this, $func) || isset($this[$func]))
		{
			$val = $this->$func;

			return $val;
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method '.get_class($this).'::'.$func.'()');
		}
	}

}
