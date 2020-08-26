<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Ticker implements Sco_Ticker_Interface
{

	const VALUE_DEF = 0;
	const RANGE_MIN = -1000;
	const RANGE_MAX = 1000;

	/**
	 * @return string
	 */
	protected $_name;

	/**
	 * @return integer
	 */
	protected $_value;

	protected $_range = array(
		self::RANGE_MIN,
		self::RANGE_MAX,
		);

	protected $_timeout;

	/**
	 * @return Sco_Ticker
	 */
	public function __construct($initial_value = self::VALUE_DEF, $name = null)
	{
		$this->setName($name);
		$this->setTicker($initial_value);

		return $this;
	}

	public function __toString()
	{
		return (string )$this->currentTicker();
	}

	public function currentTicker($args = array())
	{
		return $this->getTicker();
	}

	/**
	 * @return Sco_Ticker
	 */
	public function setName($name)
	{
		$this->_name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
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

	public function isTimeout($max = 0, $sub = false, $reset = true)
	{
		if (!isset($this->_timeout))
		{
			return false;
		}

		if ($this->_value >= $this->_timeout || $this->_timeout <= 0)
		{
			if ($this->_timeout > 0)
			{
				$count = floor($this->_value / $this->_timeout);

				if ($count && $max > 0)
				{
					$count = max(0, min($max, $count));
				}
			}
			else
			{
				$count = true;
			}

			if ($count)
			{
				if ($reset || $count === true)
				{
					$this->resetTicker();
				}
				elseif ($sub)
				{
					$this->subTicker($count * $this->_timeout);
				}

				$this->_timeout = null;
			}
		}
		else
		{
			$count = 0;
		}

		return $count;
	}

	public function setTicker($offset)
	{
		$this->_value = $offset;

		$this->reflashTicker();

		return $this;
	}

	/**
	 * @return integer
	 */
	public function getTicker()
	{
		return $this->_value;
	}

	public function resetTicker()
	{
		$this->_value = self::VALUE_DEF;

		return $this;
	}

	/**
	 * @return integer
	 *
	 * @assert (3) == 3
	 */
	public function addTicker($offset)
	{
		$this->_value += $offset;

		$this->reflashTicker();

		return $this->_value;
	}

	/**
	 * @return integer
	 *
	 * @assert (3) == -3
	 */
	public function subTicker($offset)
	{
		$this->_value -= $offset;

		$this->reflashTicker();

		return $this->_value;
	}

	/*
	public function _test(&$i)
	{
	$i+= 5;
	}
	*/

	public function reflashTicker()
	{
	    $this->_value = $this->_fixRange($this->_value);
	}

	protected function _fixRange($value)
	{
		if (isset($this->_range[1]) && $value >= $this->_range[1])
		{
			$value = $this->_range[1];
		}
		elseif (isset($this->_range[0]) && $value <= $this->_range[0])
		{
			$value = $this->_range[0];
		}

		return $value;
	}

}
