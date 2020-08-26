<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Cookie_Helper
{

	/**
	 * remove all cookies from header send
	 */
	public static function header_remove_cookies()
	{
		foreach (headers_list() as $header)
		{
			if (strpos($header, 'Set-Cookie') === 0)
			{
				header_remove('Set-Cookie');
			}
		}
	}

}
