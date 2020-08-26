<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_IdentityCard_Interface
{

	public function generate();
	public function valid($value);

}
