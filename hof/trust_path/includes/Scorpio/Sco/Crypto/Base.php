<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class Sco_Crypto_Base implements Sco_Crypto_EncodeInterface
{

	protected $salt;

	public function __construct($salt = null)
	{
		$this->setSalt($this->_salt($salt));
	}

	protected function setSalt($salt)
	{
		$this->salt = $salt;
	}

	protected function _salt($salt = null)
	{
		if ($salt === null)
		{
			$salt = Sco_Crypto_Salt::uniqid();
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
		throw new BadMethodCallException(sprintf('Fatal error: Call to undefined method %s()', __METHOD__));
	}

	public function encode($val)
	{
		throw new BadMethodCallException(sprintf('Fatal error: Call to undefined method %s::%s()', get_class($this), __FUNCTION__));
	}

	/*
	public function decode($val)
	{
		throw new BadMethodCallException(sprintf('Fatal error: Call to undefined method %s::%s()', get_class($this), __FUNCTION__));
	}
	*/

}
