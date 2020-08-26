<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

if (!class_exists('DateInterval', false))
{
	/**
	 * Representation of date interval.
	 * A date interval stores either a fixed amount of time (in years, months, days, hours etc)
	 * or a relative time string in the format that DateTime's constructor supports.
	 *
	 * (PHP 5 >= 5.3.0)
	 */
	class DateInterval extends Sco_Date_Interval
	{

		/**
		 * Creates new DateInterval object
		 */
		public function __construct($interval_spec)
		{
			$this->setOptions(array(
				'spec_microtime' => false,
				'php_week' => true,
				));

			parent::__construct($interval_spec);
		}

	}
}
