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

	/**
	 * Binary representation of a binary-string
	 *
	 * @example var_dump(bstr2bin('ABC'));
	 * Returns string(24) "010000010100001001000011"
	 */
	public function encode($input)
	{
		// Sanity check
		if (!is_string($input)) return null;

		// Unpack as a hexadecimal string
		$value = unpack('H*', $input);

		// Output binary representation
		return base_convert($value[1], 16, 2);
	}

	/**
	 * Convert a binary expression (e.g., "100111") into a binary-string
	 *
	 * @example var_dump(bin2bstr('01000001 01000010 01000011'));
	 * Returns string(3) "ABC"
	 */
	public function decode($input)
	{
		// Sanity check
		if (!is_string($input)) return null;

		// Pack into a string
		return pack('H*', base_convert($input, 2, 16));
	}

}
