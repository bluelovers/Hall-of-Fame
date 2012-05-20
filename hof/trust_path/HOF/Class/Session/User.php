<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Session_User
{

	protected static $instance;

	protected $id;
	protected $pass;

	protected $crypto;

	public function __construct()
	{
		if (!isset(self::$instance))
		{
			self::$instance = &$this;
		}
		else
		{
			die('error!!');
		}

		$this->crypto = new HOF_Class_Crypto_Discuz(CRYPT_KEY, COOKIE_EXPIRE);

		$this->SessionSwitch();
	}

	/**
	 * @return HOF_Class_Session_User
	 */
	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __call($func, $args)
	{
		if (property_exists($this, $func))
		{
			if ($args[0] !== null)
			{
				$this->$func = $args[0];

				if ($args[1]) $this->session_update();

				return $this;
			}

			if ($args[1]) $this->session_update();

			return $this->$func;
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method '.get_class($this).'::'.$func.'()');
		}
	}

	public function session_update()
	{
		$id = $this->id;
		$pass = $this->pass;

		if ($id)
		{
			$id = $this->crypto()->encode($id);
			$_SESSION['id'] = $id;

			if ($pass)
			{
				$pass = $this->crypto()->encode($pass);
				$_SESSION['pass'] = $pass;
			}
			else
			{
				unset($_SESSION['pass']);

				unset($this->pass);
			}
		}
		else
		{
			unset($_SESSION['id']);
			unset($_SESSION['pass']);

			unset($this->id);
			unset($this->pass);
		}

		$this->cookies_update();

		return $this;
	}

	public function session_delete()
	{
		unset($this->id);
		unset($this->pass);

		$this->session_update();

		setcookie('NO', '');
		unset($_COOKIE['NO']);

		return $this;
	}

	public function cookies_update()
	{
		$session_id = session_id();

		$_COOKIE['NO'] = $session_id;
		setcookie('NO', $session_id, time() + COOKIE_EXPIRE);

		return $this;
	}

	public function session_decode()
	{
		$id = $_SESSION['id'];
		$pass = $_SESSION['pass'];

		if ($id)
		{
			$id = $this->crypto()->decode($id);
			$this->id = $id;

			if ($pass)
			{
				$pass = $this->crypto()->decode($pass);
				$this->pass = $pass;
			}
			else
			{
				unset($_SESSION['pass']);

				unset($this->pass);
			}
		}
		else
		{
			unset($_SESSION['id']);
			unset($_SESSION['pass']);

			unset($this->id);
			unset($this->pass);
		}

		return $this;
	}

	/**
	 * 保存されているセッション番号を変更する。
	 */
	protected function SessionSwitch()
	{
		session_save_path(BASE_PATH_CACHE.'session/');

		/**
		 * session消滅の時間(?)
		 * how about "session_set_cookie_params()"?
		 */
		//session_cache_expire(COOKIE_EXPIRE / 60);
		session_cache_expire(COOKIE_EXPIRE);
		if ($_COOKIE["NO"])
		{
			//クッキーに保存してあるセッションIDのセッションを呼び出す
			session_id($_COOKIE["NO"]);
		}

		session_start();
		if (!SESSION_SWITCH)
		{
			$this->session_decode();

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

		session_unset();

		if ($_SESSION)
		{
			/**
			 * session_destroy();//Sleipnirだとおかしい...?(最初期)
			 * unset($_SESSION);//こっちは大丈夫(やっぱりこれは駄目かも)(修正後)
			 * 結局,セッションをforeachでループして1個づつunset(2007/9/14 再修正)
			 */
			foreach ($_SESSION as $key => $val)
			{
				unset($_SESSION[$key]);
			}
		}

		session_id($NewID);
		session_start();
		$_SESSION = unserialize($temp);

		$this->session_decode();
	}

}