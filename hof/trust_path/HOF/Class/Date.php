<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Date extends Zend_Date
{

	function __construct()
	{
		$zone = @date_default_timezone_get();

		$this->setOptions(array('format_type' => 'php'));

		$args = func_get_args();
		call_user_func_array(array('parent', __FUNCTION__), $args);

		if ($zone == $this->getTimezone() && $zone != HOF::$timezone)
		{
			$this->setTimezone(HOF::$timezone);
		}

		if ($this->getLocale() != HOF::$local)
		{
			$this->setLocale(HOF::$local);
		}
	}

	public function toString($format = null, $type = null, $locale = null)
	{
		$args = func_get_args();

		if ($format === null) $args[0] = 'Y-m-d H:i:s';

		return call_user_func_array(array('parent', __FUNCTION__), $args);
	}

}