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
		unset($this->_data_default_);

		parent::__construct(array());

		if (!$this->_init())
		{
			return false;
		}
	}

	function __destruct()
	{
		$this->fpclose();
	}

	function fpopen()
	{
		if (!$this->fp)
		{
			$this->fp = HOF_Class_File::FileLock($this->file);
		}

		return $this->fp;
	}

	function fpread()
	{
		return $this;
	}

	function fpsave($not_close = null)
	{
		!$ret = $this->_fpsave();

		return ($not_close || !$ret) ? $ret : $this->fpclose();
	}

	function _fpsave()
	{
		return true;
	}

	function fpclose()
	{
		$ret = true;

		if ($this->fp)
		{

			if (!@fclose($this->fp))
			{
				$ret = false;
			}

			unset($this->fp);
		}

		return $ret;
	}

	function dump()
	{
		return print_r($this->toArray(), 1);
	}

}