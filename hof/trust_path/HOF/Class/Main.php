<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (CLASS_MAIN);

class HOF_Class_Main extends main
{

	protected static $instance;

	function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;
		}
		else
		{
			die('error!!');
		}

		parent::__construct();
	}

	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

}
