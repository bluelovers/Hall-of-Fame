<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Base extends HOF_Class_Array
{

	public $file;
	public $fp;

	public $data = array();

	function __construct()
	{
		$data = get_object_vars($this);

		parent::__construct((array)$data);

		if (!$this->_fpinit())
		{
			return false;
		}
	}

	function _fpname()
	{
		return $this->file;
	}

	function __destruct()
	{
		$this->fpclose();
	}

	function fpopen($over = null, $autocreate = false)
	{
		if (!$this->fp || $over)
		{
			$args = func_get_args();
			$ret = call_user_func_array(array($this, '_'.__FUNCTION__), $args);

			$this->fp = HOF_Class_File::fplock_file($this->_fpname(), false, $autocreate);
		}

		return $ret !== null ? $ret : $this->fp;
	}

	function _fpopen($DataType = null)
	{

	}

	function fpread()
	{
		$args = func_get_args();

		$ret = call_user_func_array(array($this, '_'.__FUNCTION__), $args);

		return $ret !== null ? $ret : $this;
	}

	function _fpread()
	{

	}

	function fpsave($not_close = null)
	{
		$args = func_get_args();
		$ret = call_user_func_array(array($this, '_'.__FUNCTION__), $args);

		return ($not_close || $ret !== null) ? $ret : $this->fpclose();
	}

	function _fpsave()
	{

	}

	function fpclose()
	{
		$ret = true;

		if ($this->fp)
		{

			$args = func_get_args();
			$ret = call_user_func_array(array($this, '_'.__FUNCTION__), $args);

			if (!@fclose($this->fp))
			{
				$ret = false;
			}

			unset($this->fp);
		}

		return $ret;
	}

	function _fpclose()
	{

	}

	function dump()
	{
		return print_r($this->toArray(), 1);
	}

}