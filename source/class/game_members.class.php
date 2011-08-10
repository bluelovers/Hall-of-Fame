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

	/**
	 * pass と id を設定する
	 */
	function Set_ID_PASS() {
		$id	= ($_POST["id"])?$_POST["id"]:$_GET["id"];
		//if($_POST["id"]) {
		if($id) {
				$this->main->id	= $id;//$_POST["id"];
			// ↓ログイン処理した時だけ
			if (is_registered($_POST["id"])) {
				$_SESSION["id"]	= $this->main->id;
			}
		} else if($_SESSION["id"])
			$this->main->id	= $_SESSION["id"];

		$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
		//if($_POST["pass"])
		if($pass)
			$this->main->pass	= $pass;//$_POST["pass"];
		else if($_SESSION["pass"])
			$this->main->pass	= $_SESSION["pass"];

		if($this->main->pass)
			$this->main->pass	= $this->main->CryptPassword($this->pass);
	}
}

?>