<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * Coder's example is basically an explanation of bindec() and decbin(), not pack() and unpack().
 * Here's some code to convert a string binary expression into its binary-string equivalent and vice versa.
 *
 * (Would be even simpler if pack/unpack offered a 'b' format code....)
 *
 * @link http://www.php.net/manual/zh/function.pack.php
 */
class Sco_Crypto_HexStr extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
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

	public function encode($s)
	{
		$r = '';
		$hexes = array(
			'0',
			'1',
			'2',
			'3',
			'4',
			'5',
			'6',
			'7',
			'8',
			'9',
			'a',
			'b',
			'c',
			'd',
			'e',
			'f');
		for ($i = 0; $i < strlen($s); $i++) $r .= ($hexes[(ord($s{$i}) >> 4)] . $hexes[(ord($s{$i}) & 0xf)]);
		return $r;
	}

	public function decode($s)
	{
		$r = '';
		for ($i = 0; $i < strlen($s); $i += 2)
		{
			$x1 = ord($s{$i});
			$x1 = ($x1 >= 48 && $x1 < 58) ? $x1 - 48 : $x1 - 97 + 10;
			$x2 = ord($s{$i + 1});
			$x2 = ($x2 >= 48 && $x2 < 58) ? $x2 - 48 : $x2 - 97 + 10;
			$r .= chr((($x1 << 4) & 0xf0) | ($x2 & 0x0f));
		}
		return $r;
	}

}
