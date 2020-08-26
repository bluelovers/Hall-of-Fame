<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Request_Helper
{

	/**
	 * Instance of Zend_Controller_Request_Abstract
	 * @var Zend_Controller_Request_Abstract
	 */
	protected static $_request = null;

	protected static $_request_default = 'Zend_Controller_Request_Http';

	public static function setRequest($request)
	{
		if (is_string($request))
		{
			$request = new $request();
		}

		if (!$request instanceof Zend_Controller_Request_Abstract)
		{
			throw new Exception('Invalid request class');
		}

		self::$_request = $request;

		return self::$_request;
	}

	/**
	 * Instance of Zend_Controller_Request_Abstract
	 * @return Zend_Controller_Request_Abstract
	 */
	public static function getRequest($request = null)
	{
		if (!isset(self::$_request))
		{
			self::setRequest((null === $request) ? self::$_request_default : $request);
		}

		return self::$_request;
	}

}
