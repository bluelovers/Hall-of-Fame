<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_Spl_MaxIn_RootInterface
{
	//function _extend_init();

	public function extend($extend);

	public function __call($func, $argv);

}
