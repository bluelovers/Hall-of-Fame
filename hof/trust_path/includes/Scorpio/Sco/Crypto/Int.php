<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * @author http://blog.csdn.net/uuleaf/article/details/7547822
 */
class Sco_Crypto_Int extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	/**
	 * 加密数字方法
	 *
	 * @author uuleaf[<uuleaf#163.com>] 小叶
	 * @param int $int 要加密的数字
	 * @return string 加密后的字符串
	 */
	public function encode($int)
	{
		$str = md5($int.(isset($this) ? $this->salt : ''));
		//$sarr = str_split($str);
		$stai = (ord($str) + 8) % 10;
		if ($stai == 0) $stai = 8;
		$idstr = base_convert($int * $stai, 10, 32);
		$str1 = substr($str, 10, 2);
		$str2 = substr($str, 14, 2);
		$str3 = substr($str, 18, 2);
		return $str1 . $idstr . $str2 . $stai . $str3;
	}

	/**
	 * 解密数字方法
	 *
	 * @author uuleaf[<uuleaf#163.com>] 小叶
	 * @param string $str 要解密的数字
	 * @return int 解密后的数字
	 */
	public function decode($str)
	{
		$idstr = substr(substr($str, 2), 0, -5);
		$ji = base_convert($idstr, 32, 10);
		$si = (int)substr($str, -3, -2);
		return (int)floor($ji / $si);
	}

}
