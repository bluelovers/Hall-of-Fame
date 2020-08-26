<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Spl_Callback_Eval extends Sco_Spl_Callback
{

	public function func($func = null)
	{
		if ($func !== null)
		{
			$this->func = $func;
			unset($this->_tmp);
		}

		return $this;
	}

	public function exec()
	{
		if (!isset($this->_tmp))
		{
			$this->_tmp = create_function(implode(',', $this->argv), $this->func);
		}

		$argv = Sco_PHP_Helper::func_get_args();

		return call_user_func_array($this->_tmp, $argv);

		return $this->exec_array(Sco_PHP_Helper::func_get_args());
	}

	public function exec_array($argv)
	{
		if (!isset($this->_tmp))
		{
			$this->_tmp = create_function(implode(',', $this->argv), $this->func);
		}

		return call_user_func_array($this->_tmp, $argv);
	}

}
