<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Char extends HOF_Class_Array
{

	protected static $_instance;

	/**
	 * @return HOF_Model_Char
	 */
	function __construct()
	{
		if (self::$_instance === null)
		{
			parent::__construct();

			self::$_instance = $this;
		}

		return self::$_instance;
	}

	/**
	 * Retrieve singleton instance
	 *
	 * @return HOF_Model_Char
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * $char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $no . '.yml');
	 */
	function getBaseCharStatus($jobNo, $append = array())
	{
		if (!isset(self::getInstance()->job['base'][$jobNo]))
		{
			$char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $jobNo . '.yml');
			self::getInstance()->job['base'][$jobNo] = $char;
		}

		$char = self::getInstance()->job['base'][$jobNo];

		$char['birth'] = time() . substr(microtime(), 2, 6);

		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}

			$char = array_merge($char, (array )$append);
		}

		return $char;
	}

	/**
	 * @return HOF_Class_Char
	 */
	function newBaseChar($jobNo, $append = array())
	{
		$char = self::newChar(self::getBaseCharStatus($jobNo, $append));

		return $char;
	}

	function newChar($append = array())
	{
		$char = new HOF_Class_Char();

		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}

			$char->SetCharData($append);
		}

		return $char;
	}

	function newCharFromFile($file = null)
	{
		$char = new HOF_Class_Char($file);

		return $char;
	}

}
