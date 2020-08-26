<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

if (!function_exists('apache_response_headers'))
{
	/**
	 * Fetch all HTTP response headers.
	 * (PHP 4 >= 4.3.0, PHP 5)
	 *
	 * @author http://www.php.net/manual/zh/function.apache-response-headers.php#91174
	 */
	function apache_response_headers()
	{
		$arh = array();
		$headers = headers_list();
		foreach ($headers as $header)
		{
			$header = explode(HEADER_SEP, $header, 2);
			$arh[$header[0]] = trim($header[1]);
		}
		return $arh;
	}
}

if (!function_exists('apache_request_headers'))
{
	/**
	 * Fetches all HTTP request headers from the current request.
	 * (PHP 4 >= 4.3.0, PHP 5)
	 *
	 * @author http://www.php.net/manual/zh/function.apache-request-headers.php#72498
	 */
	function apache_request_headers()
	{
		foreach ($_SERVER as $key => $value)
		{
			if (strpos($key, 'HTTP_') === 0)
			{
				$out[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
			}
		}
		return $out;
	}
}

if (!function_exists('getallheaders'))
{
	function getallheaders()
	{
		return apache_request_headers();
	}
}
