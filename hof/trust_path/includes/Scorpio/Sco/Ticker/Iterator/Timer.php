<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Ticker_Iterator_Timer extends Sco_Ticker_Iterator
{

	const NS_START = 'Start';
	const NS_STOP = 'Stop';

	protected $_offsetClass = 'Sco_Ticker_Timer';

	public function __construct($data = array())
	{
		parent::__construct($data);

		$this->offsetGet(self::NS_START);
	}

	/**
	 * Returns the time elapsed betweens two markers.
	 *
	 * @param string $start start marker, defaults to "Start"
	 * @param string $end   end marker, defaults to "Stop"
	 *
	 * @return double  $time_elapsed time elapsed between $start and $end
	 * @access public
	 */
	public function timeElapsed($start = self::NS_START, $end = self::NS_STOP)
	{
		return $this[$end]->getTicker() - $this[$start]->getTicker();
	}

	/**
	 * Set marker.
	 *
	 * @param string $name Name of the marker to be set.
	 *
	 * @see    start(), stop()
	 * @access public
	 * @return void
	 */
	public function setMarker($name, $timestamp = 'now')
	{
		$this[$name]->resetTicker($timestamp);

		return $this;
	}

	public function getMarker($name)
	{
		return $this[$name];
	}

	public function toArrayValues($args = array())
	{
		if (empty($args))
		{
			$args['now'] = self::_getMicrotime();
		}

		return parent::toArrayValues($args);
	}

	/**
	 * Set "Start" marker.
	 *
	 * @see    setMarker(), stop()
	 * @access public
	 * @return void
	 */
	function start()
	{
		$this->setMarker(self::NS_START);
	}

	/**
	 * Set "Stop" marker.
	 *
	 * @see    setMarker(), start()
	 * @access public
	 * @return void
	 */
	function stop()
	{
		$this->setMarker(self::NS_STOP);
	}

	public function _getMicrotime()
	{
		return call_user_func(array($this->_offsetClass, '_getMicrotime'));
	}

}
