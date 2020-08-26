<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Crypto_MD5 extends Sco_Crypto_Base
{

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
