<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_session {

	var $main;

	function game_session($main) {
		$this->main = $main;
	}

	/**
	 * セッション情報を表示する。
	 */
	function ShowSession() {
		echo "this->id:$this->id<br>";
		echo "this->pass:$this->pass<br>";
		echo "SES[id]:$_SESSION[id]<br>";
		echo "SES[pass]:$_SESSION[pass]<br>";
		echo "SES[pass]:".$this->main->CryptPassword($_SESSION[pass])."(crypted)<br>";
		echo "CK[NO]:$_COOKIE[NO]<br>";
		echo "SES[NO]:".session_id();
		dump($_COOKIE);
		dump($_SESSION);
	}
}

?>