<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * 維吉尼亞密碼
 * only can encode [A-Za-z]
 *
 * @see http://zh.wikipedia.org/zh-hant/%E7%BB%B4%E5%90%89%E5%B0%BC%E4%BA%9A%E5%AF%86%E7%A0%81
 * @see http://www.cxybl.com/html/suanfa/2012011718837.html
 */
class Sco_Crypto_Vigenere extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	const WORD_EN = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	protected function _salt($salt = null)
	{
		if ($salt === null)
		{
			$salt = strtoupper(md5(Sco_Crypto_Salt::uniqid()));
		}

		return $salt;
	}

	protected function setSalt($salt)
	{
		$this->salt = strtoupper($salt);
	}

	public function encode($string)
	{
		$str = strtoupper(str_replace(chr(32), '', $string));

		$keylen = strlen($this->salt);
		$strlen = strlen($str);

		$arr = '';
		for ($i = 0; $i < $strlen; $i++)
		{
			//$arr .= chr(((ord($str[$i]) - 65) + (ord($this->salt[$i % $keylen]) - 65)) % 26 + 65);
			$arr .= chr((ord($str[$i]) + ord($this->salt[$i % $keylen])) % 26 + 65);
		}

		return $arr;
	}

	/**
	 * cannot nor true decode if source string has non [A-Za-z] char
	 */
	public function decode($str)
	{
		$keylen = strlen($this->salt);
		$strlen = strlen($str);

		$arr = '';
		for ($i = 0; $i < $strlen; $i++)
		{
			//$p = ((ord($str[$i]) - 65) - (ord($this->salt[$i % $keylen]) - 65));
			$p = ord($str[$i]) - ord($this->salt[$i % $keylen]);

			if ($p < 0)
			{
				$p += 26;
			}

			$arr .= chr($p % 26 + 65);
		}

		return $arr;
	}

}
