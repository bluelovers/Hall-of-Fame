<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Ticker_Iterator extends ArrayObject
{

	protected $_offsetClass = 'Sco_Ticker';

	public function __construct($data = array())
	{
		parent::__construct(array(), self::ARRAY_AS_PROPS);

		$data && $this->exchangeArray($data);
	}

	public function offsetSet($offset, $value)
	{

		if (!is_object($value) && $value !== null)
		{
			return parent::offsetSet($offset, new $this->_offsetClass($value));
		}

		if (!$value instanceof Sco_Ticker_Interface || !$value instanceof $this->_offsetClass)
		{
			throw new InvalidArgumentException();
		}

		return parent::offsetSet($offset, $value);
	}

	public function offsetGet($offset)
	{
		if (!isset($this[$offset]))
		{
			$this->offsetSet($offset, new $this->_offsetClass);
		}

		return parent::offsetGet($offset);
	}

	protected function _exchangeArray($array)
	{
		return parent::exchangeArray($array);
	}

	public function exchangeArray($array)
	{
		$old = parent::exchangeArray(array());

		foreach ($array as $offset => $value)
		{
			$this->offsetSet($offset, $value);
		}

		return $old;
	}

	public function sort()
	{
		$arr2 = $arr = array();

		foreach ($this as $k => &$v)
		{
			$arr[(string )$v][$k] = &$v;
		}

		ksort($arr);

		foreach ($arr as &$list)
		{
			$arr2 += $list;

			/*
			foreach ($list as $k => &$v)
			{
			$arr2[$k] = $v;
			}
			*/
		}

		$this->_exchangeArray($arr2);

		return $this;
	}

	public function usort($cmp_function)
	{
		$arr2 = $arr = array();

		foreach ($this as $k => &$v)
		{
			$arr[(string )$v][$k] = &$v;
		}

		uksort($arr, $cmp_function);

		foreach ($arr as &$list)
		{
			$arr2 += $list;

			/*
			foreach ($list as $k => &$v)
			{
			$arr2[$k] = $v;
			}
			*/
		}

		$this->_exchangeArray($arr2);

		return $this;
	}

	public function toArrayValues($args = array())
	{
		$array = array();

		foreach ($this as $k => &$v)
		{
			$array[$k] = $v->currentTicker($args);
		}

		return $array;
	}

	/**
	 * iterator_apply — Call a function for every element in an iterator
	 */
	public function apply_exec($method)
	{
		return $this->apply($method, array_slice(func_get_args(), 1));
	}

	/**
	 * iterator_apply — Call a function for every element in an iterator
	 */
	public function apply($method, $args = array())
	{
		if (!method_exists($this->_offsetClass, $method))
		{
			throw new BadMethodCallException();
		}

		foreach ($this as $ticker)
		{
			call_user_func_array(array($ticker, $method), $args);
		}

		return $args;
	}

}
