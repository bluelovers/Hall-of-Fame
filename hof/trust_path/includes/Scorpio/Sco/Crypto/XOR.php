<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * @see http://baike.baidu.com/view/904005.htm
 */
class Sco_Crypto_XOR extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	function encode($string)
	{
		$string = (string)$string;
		$key = (string)$this->salt;

		for ($i = 0; $i < strlen($string); $i++)
		{
			for ($j = 0; $j < strlen($key); $j++)
			{
				$string[$i] = $string[$i] ^ $key[$j];
			}

		}
		return $string;
	}

	function decode($string)
	{
		$string = (string)$string;
		$key = (string)$this->salt;

		for ($i = 0; $i < strlen($string); $i++)
		{
			for ($j = 0; $j < strlen($key); $j++)
			{
				$string[$i] = $key[$j] ^ $string[$i];
			}
		}
		return $string;
	}

}
