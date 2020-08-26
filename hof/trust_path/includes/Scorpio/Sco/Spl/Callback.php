<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Spl_Callback implements Sco_Spl_Callback_Interface
{
	var $argv;
	var $func;

	public function __construct($callback, $argv = null)
	{
		$argv = func_get_args();
		$this->func(array_shift($argv));

		$this->argv = (array)$argv;

		return $this;
	}

	/**
	 * @return self
	 */
	public static function newInstance($callback, $argv = null)
	{
		$ref = new ReflectionClass(__CLASS__);

		return $ref->newInstanceArgs(func_get_args());
	}

	/**
	 * array($this, 'compare')
	 *
	 * @return callback|array
	 */
	public function callback()
	{
		return array($this, 'exec');
	}

	public function func($func = null)
	{
		if ($func !== null)
		{
			$this->func = $func;
		}

		return $this;
	}

	public function exec()
	{
		$argv = func_num_args() > 0 ? Sco_PHP_Helper::func_get_args() : (array)$this->argv;

		return $this->exec_array($argv);
	}

	public function exec_array($argv = null)
	{
		if ($argv === null)
		{
			$argv = (array )$this->argv;
		}

		return call_user_func_array($this->func, $argv);
	}

	public function create_function($func_name)
	{
		return Sco_Spl_Helper::create_function($func_name, $this->callback());
	}

}
