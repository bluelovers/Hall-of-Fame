<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_member {

	/**
	 * @abstract main
	 */
	var $main;

	function game_member($main) {
		$this->main = &$main;
	}

	/**
	 * pass と id を設定する
	 */
	function Set_ID_PASS() {
		$id	= ($_POST["id"])?$_POST["id"]:$_GET["id"];
		if($id) {
				$this->main->id	= $id;//$_POST["id"];
			// ↓ログイン処理した時だけ
			if ($this->is_registered($this->main->id)) {
				$_SESSION["id"]	= $this->main->id;
			}
		} else if($_SESSION["id"])
			$this->main->id	= $_SESSION["id"];

		$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
		if($pass)
			$this->main->pass	= $pass;//$_POST["pass"];
		else if($_SESSION["pass"])
			$this->main->pass	= $_SESSION["pass"];

		if($this->main->pass)
			$this->main->pass	= $this->main->CryptPassword($this->main->pass);
	}

	/**
	 * $id が過去登録されたかどうか
	 */
	function is_registered($id) {
		if($registered = @file(REGISTER)):
			if(array_search($id."\n",$registered)!==false && !ereg("[\.\/]+",$id) )//改行記号必須
				return true;
			else
				return false;
		endif;
	}

	/**
	 * 入力された情報が型にはまるか判定
	 * → 新規データを作成。
	 */
	function MakeNewData() {
		// 登録者数が限界の場合
		if(MAX_USERS <= count(game_core::glob(USER)))
			return array(false,"Maximum users.<br />登録者数が限界に達してしまった様です。");
		if(isset($_POST["Newid"]))
			trim($_POST["Newid"]);
		if(empty($_POST["Newid"]))
			return array(false,"Enter ID.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["Newid"])||
			ereg("[^0-9a-zA-Z]+",$_POST["Newid"]))//正規表現
			return array(false,"Bad ID");

		if(strlen($_POST["Newid"]) < 4 || 16 < strlen($_POST["Newid"]))//文字制限
			return array(false,"Bad ID");

		if($this->is_registered($_POST["Newid"]))
			return array(false,"This ID has been already used.");

		$file = USER.$_POST["Newid"]."/".DATA;
		// PASS
		//if(isset($_POST["pass1"]))
		//	trim($_POST["pass1"]);
		if(empty($_POST["pass1"]) || empty($_POST["pass2"]))
			return array(false,"Enter both Password.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass1"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass1"]))
			return array(false,"Bad Password 1");
		if(strlen($_POST["pass1"]) < 4 || 16 < strlen($_POST["pass1"]))//文字制限
			return array(false,"Bad Password 1");
		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass2"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass2"]))
			return array(false,"Bad Password 2");
		if(strlen($_POST["pass2"]) < 4 || 16 < strlen($_POST["pass2"]))//文字制限
			return array(false,"Bad Password 2");

		if($_POST["pass1"] !== $_POST["pass2"])
			return array(false,"Password dismatch.");

		$pass = $this->main->CryptPassword($_POST["pass1"]);
		// MAKE
		if(!file_exists($file)){
			mkdir(USER.$_POST["Newid"], 0705);
			$this->main->RecordRegister($_POST["Newid"]);//ID記録
			$fp=fopen("$file","w");
			flock($fp,LOCK_EX);
				$now	= time();
				fputs($fp,"id=$_POST[Newid]\n");
				fputs($fp,"pass=$pass\n");
				fputs($fp,"last=".$now."\n");
				fputs($fp,"login=".$now."\n");
				fputs($fp,"start=".$now.substr(microtime(),2,6)."\n");
				fputs($fp,"money=".START_MONEY."\n");
				fputs($fp,"time=".START_TIME."\n");
				fputs($fp,"record_btl_log=1\n");
			fclose($fp);
			//print("ID:$_POST[Newid] success.<BR>");
			$_SESSION["id"]=$_POST["Newid"];
			setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
			$success	= "<div class=\"recover\">ID : $_POST[Newid] success. Try Login</div>";
			return array(true,$success);//強引...
		}
	}
}

?>