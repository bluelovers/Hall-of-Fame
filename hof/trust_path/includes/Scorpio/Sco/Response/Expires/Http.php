<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Response_Expires_Http
{

	static $format = 'D, d M Y H:i:s T';

	/**
	 * Sets the amount of time before content expires
	 *
	 * @param   integer Seconds before the content expires
	 * @return  integer Timestamp when the content expires
	 */
	public static function set($seconds = 60, $last_modified = 0, $now = 0)
	{
		$seconds = (int)$seconds;
		$last_modified = (int)$last_modified;
		$now = (int)$now;

		!$now && $now = REQUEST_TIME;
		!$last_modified && $last_modified = $now;

		$expires = $now + $seconds;

		/*
		echo 'Now: ' . gmdate(self::$format, $now) . "<br>";
		echo 'last_modified: ' . gmdate(self::$format, $last_modified) . "<br>";
		*/

		@header('Last-Modified: ' . gmdate(self::$format, $last_modified));

		if ($seconds > 0)
		{
			// HTTP 1.0
			@header('Expires: ' . gmdate(self::$format, $expires));
			//@header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');

			// HTTP 1.1
			@header('Cache-Control: max-age=' . $seconds);
		}
		elseif ($seconds < 0)
		{
			@header('Expires: -1');
			@header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0');
			@header('Pragma: no-cache');
		}

		return $expires;
	}

	/**
	 * Parses the If-Modified-Since header
	 *
	 * @return  integer|boolean Timestamp or FALSE when header is lacking or malformed
	 */
	public static function get()
	{
		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			// Some versions of IE6 append "; length=####"
			if (($strpos = strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], ';')) !== false)
			{
				$mod_time = substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, $strpos);
			}
			else
			{
				$mod_time = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
			}

			return strtotime($mod_time);
		}

		return false;
	}

	function expires($seconds = 60, $last_modified = 0, $now = 0)
	{
		@header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');

		self::set($seconds, $last_modified, time());

		return $ret;
	}

	/**
	 * Checks to see if content should be updated otherwise sends Not Modified status
	 * and exits.
	 *
	 * @uses    exit()
	 * @uses    expires::get()
	 *
	 * @param   integer         Maximum age of the content in seconds
	 * @return  integer|boolean Timestamp of the If-Modified-Since header or FALSE when header is lacking or malformed
	 */
	public static function check($seconds = 60, $last_modified = 0, $now = 0)
	{
		if ($last_modified || $last_modified = self::get())
		{
			$seconds = (int)$seconds;
			$last_modified = (int)$last_modified;
			$now = (int)$now;

			!$now && $now = time();

			$expires = $last_modified + $seconds;
			$max_age = $expires - $now;
		}
		else
		{
			return false;
		}

		return $max_age;
	}

	/**
	 * Check if expiration headers are already set
	 *
	 * @return boolean
	 */
	public static function headers_set()
	{
		foreach (headers_list() as $header)
		{
			if (strncasecmp($header, 'Expires:', 8) === 0 or strncasecmp($header, 'Cache-Control:', 14) === 0 or strncasecmp($header, 'Last-Modified:', 14) === 0)
			{
				return true;
			}
		}

		return false;
	}

} // End expires
