<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class Sco_Spl_Callback_Abstract implements Sco_Spl_Callback_Interface
{

	/**
	 * array($this, 'compare')
	 *
	 * @return callback|array
	 */
	public function callback()
	{
		return array($this, 'exec');
	}

	public function exec()
	{
		$argv = func_num_args() > 0 ? func_get_args() : (array)$this->argv;

		return $this->exec_array($argv);
	}

	public function create_function($func_name)
	{
		return Sco_Spl_Helper::create_function($func_name, $this->callback());
	}

	/**
	 * PHP version >= 5.3
	 */
	public function __invoke()
	{
		$argv = func_num_args() > 0 ? func_get_args() : (array)$this->argv;

		return $this->exec_array($argv);
	}

}