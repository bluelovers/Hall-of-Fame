<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Ticker_TimerTicker
{

	const NS_DEF = 'TimerTicker';

	private static $_instance;

	protected $namespace;

	protected function __construct($namespace)
	{
		$this->namespace = $namespace;
	}

	/**
	 * @return Sco_Ticker_TimerTicker
	 */
	public function &getInstance($namespace = self::NS_DEF)
	{
		if (!isset(self::$_instance[$namespace]))
		{
			self::$_instance[$namespace] = new self($namespace);
		}

		return self::$_instance[$namespace];
	}

	/**
	 * @return int
	 */
	public function getTimestamp()
	{
		return time();
	}

	/**
	 * @return float
	 */
	public function getMicrotime()
	{
		return microtime(true);
	}

}
