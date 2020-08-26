<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

interface Sco_Ticker_Interface
{

	function __toString();

	function currentTicker($args = array());
	function resetTicker();

}
