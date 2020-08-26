<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_Array_Iterator_LockableInterface
{

	function lock();
	function unlock();
	function isLocked();

}
