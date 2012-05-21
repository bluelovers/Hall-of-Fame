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

	/**
	 * @return Zend_Session_Namespace
	 */
	protected $session;

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

		$this->crypto = new HOF_Class_Crypto_Discuz(md5(CRYPT_KEY . $_SERVER["HTTP_USER_AGENT"] . HOF::session()->getId()), COOKIE_EXPIRE);

		$this->session = HOF::session()->getNamespace('HOF_User_Auth');

		$this->session_decode();

		//debug($this->id, $this->pass, $this->session->id, $this->session->pass);
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
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

	public function session_update()
	{
		$id = $this->id;
		$pass = $this->pass;

		if ($id)
		{
			$id = $this->crypto()->encode($id);
			$this->session->id = $id;

			if ($pass)
			{
				$pass = $this->crypto()->encode($pass);
				$this->session->pass = $pass;
			}
			else
			{
				unset($this->session->pass);

				unset($this->pass);
			}
		}
		else
		{
			unset($this->session->id);
			unset($this->session->pass);

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

		setcookie('NO', '', -1, BASE_URL_ROOT);
		unset($_COOKIE['NO']);

		HOF::session()->forgetMe();

		return $this;
	}

	public function cookies_update()
	{
		$session_id = HOF::session()->getId();

		$_COOKIE['NO'] = $session_id;
		setcookie('NO', $session_id, REQUEST_TIME + COOKIE_EXPIRE, BASE_URL_ROOT);

		return $this;
	}

	public function session_decode()
	{
		$id = $this->session->id;
		$pass = $this->session->pass;

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
				unset($this->session->pass);

				unset($this->pass);
			}
		}
		else
		{
			unset($this->session->id);
			unset($this->session->pass);

			unset($this->id);
			unset($this->pass);
		}

		return $this;
	}

}
