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

		//

		$args = func_get_args();
		call_user_func_array(array('parent', __FUNCTION__), $args);

		if ($zone == $this->getTimezone() && $zone != HOF::$timezone)
		{
			$this->setTimezone(HOF::$timezone);
		}
	}

	public function toString($format = null, $type = null, $locale = null)
	{
		$args = func_get_args();

		if ($format === null) {
			$args[0] = 'Y-m-d H:i:s';
			$args[1] = 'php';

			//$args[0] = Zend_Locale_Format::convertPhpToIsoFormat($args[0]);
		}

		return call_user_func_array(array('parent', __FUNCTION__), $args);
	}

	function diff($date, $part = self::TIMESTAMP, $locale = null)
	{
		$base = $this->getClone();

		return new HOF_Class_Date_DateInterval($base->sub($date, $part, $locale)->getValue());
	}

	public function getHour($locale = null)
    {
    	$_options = self::setOptions();

    	$orig = $_options['format_type'];
        if ($_options['format_type'] == 'php') {
        	self::setOptions(array('format_type' => 'iso'));
        }

    	$date = parent::getHour($locale);

    	self::setOptions(array('format_type' => $orig));

        return $date;
    }

	function getClone()
	{
		$data = new self($this->toString(Zend_Date::DATETIME_FULL), Zend_Date::DATETIME_FULL, $this->getLocale());

		return $data;
	}

}