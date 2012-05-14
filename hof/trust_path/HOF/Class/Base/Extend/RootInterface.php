<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface HOF_Class_Base_Extend_RootInterface
{

	public function extend($extend);

	public function __call($func, $argv);

}
