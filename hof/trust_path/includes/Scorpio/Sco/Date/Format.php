<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Date_Format
{

	/**
	 * Converts a format string from ISO to PHP format
	 * reverse the functionality of Zend's convertPhpToIsoFormat()
	 *
	 * @link http://www.phpkode.com/source/p/tine/release.php
	 *
	 * @param  string  $format  Format string in ISO format
	 * @return string           Format string in PHP's date format
	 */
	function convertIsoToPhpFormat($format)
	{
		$convert = array(
			'c' => '/yyyy-MM-ddTHH:mm:ssZZZZ/',
			'$1j$2' => '/([^d])d([^d])/',
			'j$1' => '/^d([^d])/',
			'$1j' => '/([^d])d$/',
			't' => '/ddd/',
			'd' => '/dd/',
			'l' => '/EEEE/',
			'D' => '/EEE/',
			'S' => '/SS/',
			'w' => '/eee/',
			'N' => '/e/',
			'z' => '/D/',
			'W' => '/w/',
			'$1n$2' => '/([^M])M([^M])/',
			'n$1' => '/^M([^M])/',
			'$1n' => '/([^M])M$/',
			'F' => '/MMMM/',
			'M' => '/MMM/',
			'm' => '/MM/',
			'L' => '/l/',
			'o' => '/YYYY/',
			'Y' => '/yyyy/',
			'y' => '/yy/',
			'a' => '/a/',
			'A' => '/a/',
			'B' => '/B/',
			'h' => '/hh/',
			'g' => '/h/',
			'$1G$2' => '/([^H])H([^H])/',
			'G$1' => '/^H([^H])/',
			'$1G' => '/([^H])H$/',
			'H' => '/HH/',
			'i' => '/mm/',
			's' => '/ss/',
			'e' => '/zzzz/',
			'I' => '/I/',
			'P' => '/ZZZZ/',
			'O' => '/Z/',
			'T' => '/z/',
			'Z' => '/X/',
			'r' => '/r/',
			'U' => '/U/',
			);

		//echo "pre:".$format."\n";

		$patterns = array_values($convert);
		$replacements = array_keys($convert);
		$format = preg_replace($patterns, $replacements, $format);

		//echo "post:".$format."\n";
		//echo "---\n";

		return $format;
	}

}
