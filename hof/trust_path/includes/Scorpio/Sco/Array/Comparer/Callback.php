<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Comparer_Callback
{

	var $argv;
	var $comp_func;

	function __construct($argv)
	{
		if (is_array($argv))
		{
			$this->argv = (array)$argv;
		}
		else
		{
			$this->argv = func_get_args();
		}

		return $this;
	}

	/**
	 * @return self
	 */
	function newInstance($argv)
	{
		if (is_array($argv))
		{
			$argv = (array)$argv;
		}
		else
		{
			$argv = func_get_args();
		}

		return new self($argv);
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

	function compare($entry)
	{
		$argv = (array)$this->argv;
		array_unshift($argv, $entry);

		return call_user_func_array($this->comp_func, $argv);
	}

}
