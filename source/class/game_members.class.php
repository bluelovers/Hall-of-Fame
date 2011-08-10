<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_members {

	/**
	 * @abstract main
	 */
	var $main;

	function game_members($main) {
		$this->main = &$main;
	}
}

?>