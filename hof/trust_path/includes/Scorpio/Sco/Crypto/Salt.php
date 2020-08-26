<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Crypto_Salt
{

	public static function uniqid($seed = null)
	{
		if ($seed === null)
		{
			$seed = self::random(10);
		}

		return uniqid($seed, true);
	}

	public static function random($length = 32, $numeric = 0)
	{
		$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for ($i = 0; $i < $length; $i++)
		{
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}

	public static function word_table($word, $width = 26, $height = null, $format = true, $sep = NL)
	{
		if ($height === null)
		{
			$height = $width;
		}

		$len = $width * $height;

		$table = substr(str_repeat($word, ceil($len / strlen($word))), 0, $len);

		if (!$format)
		{
			return $table;
		}

		return wordwrap($table, $width, $sep, true);
	}

}
