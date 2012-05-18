<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Main extends HOF_Class_User
{
	var $islogin = false;

	protected static $instance;

	function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = $this;
		}
		else
		{
			die('error!!');
		}

		$this->SessionSwitch();
		$this->Set_ID_PASS();
	}

	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * 保存されているセッション番号を変更する。
	 */
	function SessionSwitch()
	{
		/**
		 * session消滅の時間(?)
		 * how about "session_set_cookie_params()"?
		 */
		session_cache_expire(COOKIE_EXPIRE / 60);
		if ($_COOKIE["NO"])
		{
			//クッキーに保存してあるセッションIDのセッションを呼び出す
			session_id($_COOKIE["NO"]);
		}

		session_start();
		if (!SESSION_SWITCH)
		{
			//switchしないならここで終了
			return false;
		}
		//print_r($_SESSION);
		//dump($_SESSION);
		$OldID = session_id();
		$temp = serialize($_SESSION);

		session_regenerate_id();
		$NewID = session_id();
		setcookie("NO", $NewID, time() + COOKIE_EXPIRE);
		$_COOKIE["NO"] = $NewID;

		session_id($OldID);
		session_start();

		if ($_SESSION)
		{
			/**
			 * session_destroy();//Sleipnirだとおかしい...?(最初期)
			 * unset($_SESSION);//こっちは大丈夫(やっぱりこれは駄目かも)(修正後)
			 * 結局,セッションをforeachでループして1個づつunset(2007/9/14 再修正)
			 */
			foreach ($_SESSION as $key => $val)
			{
				unset($_SESSION["$key"]);
			}
		}

		session_id($NewID);
		session_start();
		$_SESSION = unserialize($temp);
	}

	/**
	 * pass と id を設定する
	 */
	function Set_ID_PASS()
	{
		$id = HOF::$input->post->id;

		if ($id)
		{
			$this->id = $id; //$_POST["id"];
			// ↓ログイン処理した時だけ
			if (HOF_Controller_Game::is_registered($_POST["id"]))
			{
				$_SESSION["id"] = $this->id;
			}
		}
		elseif ($_SESSION["id"])
		{
			$this->id = $_SESSION["id"];
		}

		$pass = HOF::$input->post->pass;
		//if($_POST["pass"])
		if ($pass)
		{
			$this->pass = $pass; //$_POST["pass"];
		}
		elseif ($_SESSION["pass"])
		{
			$this->pass = $_SESSION["pass"];
		}

		if ($this->pass) $this->pass = HOF_Helper_Char::CryptPassword($this->pass);
	}

	/**
	 * セッション情報を表示する。
	 */
	function ShowSession()
	{
		echo "this->id:$this->id<br>";
		echo "this->pass:$this->pass<br>";

		echo "SES[id]:$_SESSION[id]<br>";
		echo "SES[pass]:$_SESSION[pass]<br>";
		echo "SES[pass]:" . HOF_Helper_Char::CryptPassword($_SESSION[pass]) . "(crypted)<br>";

		echo "CK[NO]:$_COOKIE[NO]<br>";
		echo "SES[NO]:" . session_id();

		dump($_COOKIE);
		dump($_SESSION);
	}

	/**
	 * ログインした時間を設定する
	 */
	function RenewLoginTime()
	{
		$this->login = time();
	}

	function FirstLogin($over = false)
	{
		static $flag;

		if (!isset($flag) || $over)
		{
			$flag = !HOF_Class_Controller::getInstance('game', __FUNCTION__ )->main()->_main_stop();
		}

		return $flag;
	}

	/**
	 * ログインしたのか、しているのか、ログアウトしたのか。
	 */
	function CheckLogin($over = false)
	{
		static $flag;

		if (!isset($flag) || $over)
		{
			HOF_Class_Controller::getInstance('game', __FUNCTION__ )->main()->_main_stop();

			$flag = $this->islogin;
		}

		return $flag;
	}

}
