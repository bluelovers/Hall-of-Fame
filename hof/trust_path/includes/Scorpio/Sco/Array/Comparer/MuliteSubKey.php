<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Comparer_MuliteSubKey
{

	var $keys;
	var $sort_desc = false;
	var $comp_func = 'strcmp';
	protected $cache;

	function __construct($keys)
	{
		if (is_array($keys))
		{
			$this->keys = (array)$keys;
		}
		else
		{
			$this->keys = func_get_args();
		}

		return $this;
	}

	/**
	 * @return self
	 */
	function newInstance($keys)
	{
		if (is_array($keys))
		{
			$keys = (array)$keys;
		}
		else
		{
			$keys = func_get_args();
		}

		return new self($keys);
	}

	/**
	 * array($this, 'compare')
	 *
	 * @return array
	 */
	function callback()
	{
		return array($this, 'compare');
	}

	/**
	 * @return self
	 */
	function sort_desc($sort_desc = null)
	{
		if ($sort_desc !== null)
		{
			$this->sort_desc = $sort_desc;
		}

		return $this;
	}

	function comp_func($func = null)
	{
		if ($func !== null)
		{
			$this->comp_func = $func;
		}

		return $this;
	}

	function compare($a, $b)
	{
		$i = 0;
		$c = count((array)$this->keys);

		if (!isset($this->cache['offsetType']))
		{
			if (is_array($a) || is_object($a) && method_exists($a, 'offsetGet'))
			{
				$this->cache['offsetType'] = 0;
			}
			else
			{
				$this->cache['offsetType'] = 1;
			}
		}

		$cmp = 0;
		while ($cmp == 0 && $i < $c)
		{
			//$cmp = strcmp($a[$this->keys[$i]], $b[$this->keys[$i]]);

			if ($this->cache['offsetType'])
			{
				$cmp = call_user_func($this->comp_func, $a->{$this->keys[$i]}, $b->{$this->keys[$i]});
			}
			else
			{
				$cmp = call_user_func($this->comp_func, $a[$this->keys[$i]], $b[$this->keys[$i]]);
			}

			if ($this->sort_desc)
			{
				$cmp = 0 - $cmp;
			}

			$i++;
		}

		return $cmp;
	}

}
