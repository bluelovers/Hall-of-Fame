<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_party {
	/**
	 * @abstract main
	 */
	var $main;

	function game_party($main) {
		$this->main = &$main;
	}
}

?>