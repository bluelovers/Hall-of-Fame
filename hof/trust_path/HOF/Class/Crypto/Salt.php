<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Crypto_Salt
{

	public static function uniqid($seed = null)
	{
		if ($seed === null)
		{
			self::random(10);
		}

		return uniqid($seed, true);
	}

	public static function random($length, $numeric = 0) {
		$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $seed{mt_rand(0, $max)};
		}
		return $hash;
	}

}
