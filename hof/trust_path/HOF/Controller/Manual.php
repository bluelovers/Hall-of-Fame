<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Manual extends HOF_Class_Controller
{

	function _main_init()
	{
		if (empty($this->action) || $this->action == self::DEFAULT_ACTION)
		{
			$this->_main_setup('manual');
		}
	}

}
