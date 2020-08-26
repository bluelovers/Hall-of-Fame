<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * rc4加密算法
 *
 * @see http://baike.baidu.com/view/904005.htm
 * @see http://en.wikipedia.org/wiki/RC4
 */
class Sco_Crypto_RC4 extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	/**
	 * @param string $pwd 密钥
	 * @param string $data 要加密的数据
	 */
	function encode($data, $pwd = null)
	{
		$key[] = '';
		$box[] = '';

		isset($this) && $pwd = $this->salt;
		$data = (string)$data;

		$pwd_length = strlen($pwd);
		$data_length = strlen($data);

		for ($i = 0; $i < 256; $i++)
		{
			$key[$i] = ord($pwd[$i % $pwd_length]);
			$box[$i] = $i;
		}

		for ($j = $i = 0; $i < 256; $i++)
		{
			$j = ($j + $box[$i] + $key[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $data_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;

			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;

			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}

		return $cipher;
	}

	function decode($data, $pwd = null)
	{
		return self::encode($data, $pwd);
	}

}
