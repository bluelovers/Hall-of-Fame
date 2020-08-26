<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Crypto_Discuz extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	const DECODE = 'DECODE';
	const ENCODE = 'ENCODE';

	public $expiry;

	function __construct($salt = null, $expiry = 0)
	{
		parent::__construct($salt);

		$this->expiry = $expiry;
	}

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	public function encode($string, $expiry = null)
	{
		if ($expiry === null) $expiry = $this->expiry;

		return $this->authcode($string, self::ENCODE, $this->salt, $expiry);
	}

	public function decode($string)
	{
		return $this->authcode($string, self::DECODE, $this->salt);
	}

	public function authcode($string, $operation = self::DECODE, $key = '', $expiry = 0)
	{
		$ckey_length = 4;
		$key = md5($key != '' ? $key : $this->salt);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == self::DECODE ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for ($i = 0; $i <= 255; $i++)
		{
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for ($j = $i = 0; $i < 256; $i++)
		{
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $string_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if ($operation == self::DECODE)
		{
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16))
			{
				return substr($result, 26);
			}
			else
			{
				return '';
			}
		}
		else
		{
			return $keyc . str_replace('=', '', base64_encode($result));
		}

	}

}
