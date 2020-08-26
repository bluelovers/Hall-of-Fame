<?php

/**********************************************************\
|                                                          |
| The implementation of PHPRPC Protocol 3.0                |
|                                                          |
| xxtea.php                                                |
|                                                          |
| Release 3.0.1                                            |
| Copyright by Team-PHPRPC                                 |
|                                                          |
| WebSite:  http://www.phprpc.org/                         |
|           http://www.phprpc.net/                         |
|           http://www.phprpc.com/                         |
|           http://sourceforge.net/projects/php-rpc/       |
|                                                          |
| Authors:  Ma Bingyao <andot@ujn.edu.cn>                  |
|                                                          |
| This file may be distributed and/or modified under the   |
| terms of the GNU General Public License (GPL) version    |
| 2.0 as published by the Free Software Foundation and     |
| appearing in the included file LICENSE.                  |
|                                                          |
\**********************************************************/

/**
 * XXTEA encryption arithmetic library.
 *
 * Copyright: Ma Bingyao <andot@ujn.edu.cn>
 * Version: 1.6
 * LastModified: Apr 12, 2010
 * This library is free.  You can redistribute it and/or modify it under GPL.
 */
class Sco_Crypto_TEA_XXTEA extends Sco_Crypto_Base implements Sco_Crypto_DecodeInterface
{

	/**
	 * @return self
	 */
	public static function newInstance($salt = null)
	{
		return new self($salt);
	}

	/**
	 * @return string
	 */
	public static function long2str($v, $w)
	{
		$len = count($v);
		$n = ($len - 1) << 2;
		if ($w)
		{
			$m = $v[$len - 1];
			if (($m < $n - 3) || ($m > $n)) return false;
			$n = $m;
		}
		$s = array();
		for ($i = 0; $i < $len; $i++)
		{
			$s[$i] = pack('V', $v[$i]);
		}
		if ($w)
		{
			return substr(join('', $s), 0, $n);
		}
		else
		{
			return join('', $s);
		}
	}

	public static function str2long($s, $w)
	{
		$v = unpack('V*', $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w)
		{
			$v[count($v)] = strlen($s);
		}
		return $v;
	}

	/**
	 * @return int
	 */
	public static function int32($n)
	{
		while ($n >= 2147483648) $n -= 4294967296;
		while ($n <= -2147483649) $n += 4294967296;
		return (int)$n;
	}

	/**
	 * @return string
	 */
	public function encode($str, $key = null)
	{
		if ($str == '')
		{
			return '';
		}

		isset($this) && $key = $this->salt;

		$v = self::str2long($str, true);
		$k = self::str2long($key, false);
		if (count($k) < 4)
		{
			for ($i = count($k); $i < 4; $i++)
			{
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;

		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;
		while (0 < $q--)
		{
			$sum = self::int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++)
			{
				$y = $v[$p + 1];
				$mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = self::int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = self::int32($v[$n] + $mx);
		}
		return self::long2str($v, false);
	}

	/**
	 * @return string
	 */
	public function decode($str, $key = null)
	{
		if ($str == '')
		{
			return '';
		}

		isset($this) && $key = $this->salt;

		$v = self::str2long($str, false);
		$k = self::str2long($key, false);
		if (count($k) < 4)
		{
			for ($i = count($k); $i < 4; $i++)
			{
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;

		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = self::int32($q * $delta);
		while ($sum != 0)
		{
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--)
			{
				$z = $v[$p - 1];
				$mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = self::int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = self::int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ self::int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = self::int32($v[0] - $mx);
			$sum = self::int32($sum - $delta);
		}
		return self::long2str($v, true);
	}
}
