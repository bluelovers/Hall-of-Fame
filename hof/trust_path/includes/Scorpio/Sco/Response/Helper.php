<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Response_Helper
{

	/**
	 * Instance of Zend_Controller_Response_Abstract
	 * @var Zend_Controller_Response_Abstract
	 */
	protected static $_response = null;

	protected static $_response_default = 'Zend_Controller_Response_Http';

	public static function setResponse($response)
	{
		if (is_string($response))
		{
			$response = new $response();
		}

		if (!$response instanceof Zend_Controller_Response_Abstract)
		{
			throw new Exception('Invalid response class');
		}

		self::$_response = $response;

		return self::$_response;
	}

	/**
	 * Instance of Zend_Controller_Response_Abstract
	 * @return Zend_Controller_Response_Abstract
	 */
	public static function getResponse($response = null)
	{
		if (!isset(self::$_response))
		{
			self::setResponse((null === $response) ? self::$_response_default : $response);
		}

		return self::$_response;
	}

}
