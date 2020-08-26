<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Ticker_Timer implements Sco_Ticker_Interface
{

	protected $_timestamp;
	protected $_timestamp_stop;

	public function __construct($timestamp = null)
	{
		$this->resetTicker($timestamp);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('%0.8f', $this->currentTicker());
	}

	/**
	 * @param array $args
	 * @return float
	 */
	public function currentTicker($args = array())
	{
		$now = $this->_timestamp_stop !== null ? $this->_timestamp_stop : (($args['now']) ? $args['now'] : self::_getMicrotime());

		return $now - $this->_timestamp;
	}

	/**
	 * @param float|string|null $timestamp
	 */
	public function resetTicker($timestamp = null)
	{
		if ($timestamp === null || $timestamp === 'now')
		{
			$this->_timestamp = self::_getMicrotime();
		}
		else
		{
			$this->_timestamp = $timestamp;
		}

		$this->_timestamp_stop = null;
	}

	/**
	 * @return float
	 */
	public function getTicker()
	{
		return $this->_timestamp;
	}

	public function stop($flag = true, $timestamp = null)
	{
		if ($flag)
		{
			if ($timestamp === null || $timestamp === 'now')
			{
				$this->_timestamp_stop = self::_getMicrotime();
			}
			else
			{
				$this->_timestamp_stop = $timestamp;
			}
		}
		else
		{
			$this->_timestamp_stop = null;
		}

		return $this;
	}

	public static function _getMicrotime()
	{
		return Sco_Ticker_TimerTicker::getInstance()->getMicrotime();
	}

	/**
	 * @return Sco_Ticker
	 */
	public function setTimeout($timeout, $offset = true)
	{
		if (isset($timeout))
		{
			if ($timeout > 0 && $offset)
			{
				$timeout = $this->_value + $timeout;
			}

			$this->_timeout = isset($this->_range[1]) ? min($timeout, $this->_range[1]) : $timeout;
		}
		else
		{
			$this->_timeout = null;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTimeout()
	{
		return $this->_timeout;
	}

	public function isTimeout($now = null, $stop = false, $reset = true)
	{
		if (!isset($this->_timeout))
		{
			return false;
		}

		if ($now === null || $now === 'now')
		{
			$now = self::_getMicrotime();
		}

		if ($now >= $this->_timeout || $this->_timeout <= 0)
		{
			if ($stop)
			{
				$this->stop(true, $now);
			}

			if ($reset)
			{
				$this->resetTicker($now);
			}

			return true;
		}

		return false;
	}

}
