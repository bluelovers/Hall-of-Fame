<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Base_Extend_Extend implements HOF_Class_Base_Extend_RootInterface
{

	/**
	 * jQuery style attr
	 */

	protected $_root_;

	function __construct(&$root_obj)
	{
		$this->_root_ = &$root_obj;
	}

	public function extend($extend) {}
	
	public function __call($func, $argv) {}
}
