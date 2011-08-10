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
	 * 保存されているセッション番号を変更する。
	 */
	function SessionSwitch() {
		/**
		 * session消滅の時間(?)
		 * how about "session_set_cookie_params()"?
		 */
		session_cache_expire(COOKIE_EXPIRE/60);
		if($_COOKIE["NO"])//クッキーに保存してあるセッションIDのセッションを呼び出す
			session_id($_COOKIE["NO"]);

		session_start();
		if(!SESSION_SWITCH)//switchしないならここで終了
			return false;
		//print_r($_SESSION);
		//dump($_SESSION);
		$OldID	= session_id();
		$temp	= serialize($_SESSION);

		session_regenerate_id();
		$NewID	= session_id();
		setcookie("NO",$NewID,time()+COOKIE_EXPIRE);
		$_COOKIE["NO"]=$NewID;

		session_id($OldID);
		session_start();

		if($_SESSION):
			/**
			 * session_destroy();//Sleipnirだとおかしい...?(最初期)
			 * unset($_SESSION);//こっちは大丈夫(やっぱりこれは駄目かも)(修正後)
			 * 結局,セッションをforeachでループして1個づつunset(2007/9/14 再修正)
			 */
			foreach($_SESSION as $key => $val)
				unset($_SESSION["$key"]);
		endif;

		session_id($NewID);
		session_start();
		$_SESSION	= unserialize($temp);
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