<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * @link http://www.php.net/manual/zh/function.pack.php
 */
class Sco_Crypto_Pack_Str extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	public function __construct()
	{
	}

	/**
	 * @return self
	 */
	public static function newInstance()
	{
		return new self();
	}

	public function encode($str, $len)
	{
		$out_str = '';
		for ($i = 0; $i < $len; $i++)
		{
			$out_str .= pack('c', ord(substr($str, $i, 1)));
		}
		return $out_str;
	}

	public function decode($str, $len)
	{
		$tmp_arr = unpack('c' . $len . 'chars', $str);
		$out_str = '';
		foreach ($tmp_arr as $v)
		{
			if ($v > 0)
			{
				$out_str .= chr($v);
			}
		}

		return $out_str;
	}

}
