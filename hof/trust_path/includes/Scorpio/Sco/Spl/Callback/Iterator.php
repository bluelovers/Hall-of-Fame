<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Spl_Callback_Iterator extends Sco_Array implements Sco_Spl_Callback_Interface
{

	public $argv;
	public $func;

	public $result;

	/**
	 * @return bool
	 */
	public $disable;

	public function __construct($callback, $argv = null)
	{
		parent::__construct(array(), array('prop' => false));

		$argv = func_get_args();
		$this->func(array_shift($argv));

		$this->argv = (array )$argv;

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
			$this->exchangeArray((array )$func);
		}

		return $this;
	}

	public function exec()
	{
		if ($this->disable) return;

		$argv = func_num_args() > 0 ? Sco_PHP_Helper::func_get_args() : (array )$this->argv;

		return $this->exec_array($argv);
	}

	public function exec_array($argv = null)
	{
		if ($this->disable) return;

		if ($argv === null)
		{
			$argv = (array )$this->argv;
		}

		$this->result = null;

		//array_unshift($argv, &$this->result);

		foreach ($this as $func)
		{
			$this->func = $func;

			//var_dump($func, $this->result);

			$this->result[] = call_user_func_array($this->func, $argv);
		}

		return $this->result;
	}

	public function create_function($func_name)
	{
		return Sco_Spl_Helper::create_function($func_name, $this->callback());
	}

	public function result()
	{
		return $this->result;
	}

	public function disable($disable = true)
	{
		$this->disable = (bool)$disable;

		return $this;
	}

}
