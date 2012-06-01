<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Array_Prop extends ArrayObject
{

	private $ARRAYOBJECT_OPTION = array('prop' => false, );

	function __construct($input = array(), $options = array())
	{
		$this->setFlags(HOF_Class_Array::ARRAY_PROP_BOTH);
		$this->option((array )$options);
		$this->exchangeArray((array )$input);
	}

	public function exchangeArray($input)
	{
		$array = parent::exchangeArray($input);

		if ($this->option('prop'))
		{
			$reflect = new HOF_Class_Reflection_Class($this);
			$props = $reflect->getProperties(HOF_Class_Reflection_Property::IS_PROP | HOF_Class_Reflection_Property::IS_PUBLIC | (int)$this->option('filter'));
			foreach ($props as $prop)
			{
				$k = (string )$prop->getName();

				if ($this->offsetExists($k))
				{
					$this->$k = &$this[$k];
				}
				else
				{
					$this->offsetSet($k, &$this->$k);
				}
			}
		}

		return $array;
	}

	public function option($key)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$this->ARRAYOBJECT_OPTION[(string )$k] = $v;
			}

			return (array )$this->ARRAYOBJECT_OPTION;
		}
		elseif (func_num_args() > 1)
		{
			$this->ARRAYOBJECT_OPTION[$key] = func_get_arg(1);
		}
		else
		{
			return $this->ARRAYOBJECT_OPTION[$key];
		}

		return $this;
	}

	public function toArray()
	{
		return $this->getArrayCopy();
	}

	public function array_walk()
	{
		$args = func_get_args();

		$arr = $this->getArrayCopy();

		array_unshift($args, &$arr);

		$return = call_user_func_array(__FUNCTION__, $args);

		$this->exchangeArray($arr);

		return $return;
	}

	public function array_walk_recursive()
	{
		$args = func_get_args();

		$arr = $this->getArrayCopy();

		array_unshift($args, &$arr);

		$return = call_user_func_array(__FUNCTION__, $args);

		$this->exchangeArray($arr);

		return $return;
	}

	public function array_map()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_merge()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function merge()
	{
		$arr = call_user_func(array($this, 'array_merge'), func_get_args());

		$this->exchangeArray($arr);

		return $this;
	}

	public function array_intersect()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_intersect_assoc()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_intersect_key()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	protected function _array_func($func, $argv)
	{
		$arr = $this->getArrayCopy();

		array_unshift($argv, $arr);

		return call_user_func_array($func, $argv);
	}

	public function array_filter()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_reduce()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_keys()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_diff()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_rand()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_replace_recursive()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_replace()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_reverse()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_search()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_unique()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_values()
	{
		return call_user_func(array($this, '_array_func'), __FUNCTION__, func_get_args());
	}

	public function array_splice()
	{
		$argv = func_get_args();

		$arr = (array )$this->getArrayCopy();

		array_unshift($argv, &$arr);

		$return = call_user_func_array(__FUNCTION__, $argv);

		$this->exchangeArray($arr);

		return $return;
	}

	public function insert($offset, $entry)
	{
		$this->array_splice($offset, 0, array($entry));

		return $this;
	}

	public function prepend($entry)
	{
		$this->array_splice(0, 0, array($entry));

		return $this;
	}

	public function array_shift()
	{
		return $this->array_splice(0, 1);
	}

	public function array_pop()
	{
		return $this->array_splice(-1);
	}

}
