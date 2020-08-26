<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class HOF_Class_Crypto_Base implements HOF_Class_Crypto_EncodeInterface
{

	protected $salt;

	public function __construct($salt = null)
	{
		$salt = $this->_salt($salt);

		$this->salt = $salt;
	}

	protected function _salt($salt = null)
	{
		if ($salt === null)
		{
			$salt = HOF_Class_Crypto_Salt::uniqid();
		}

		return $salt;
	}

	public function salt($salt = null)
	{
		if ($salt !== null)
		{
			return self::_salt($salt);
		}

		return $this->salt;
	}

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	public function encode($val)
	{
		$ret = md5(md5($val).$this->salt);

		return $ret;
	}

}
