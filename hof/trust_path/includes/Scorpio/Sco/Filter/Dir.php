<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Filter_Dir implements Zend_Filter_Interface
{

	/**
	 * Defined by Zend_Filter_Interface
	 *
	 * Returns dirname($value)
	 *
	 * @param  string $value
	 * @return string
	 */
	public function filter($value)
	{
		return Sco_File_Format::dirname((string )$value, null, 1);
	}

}
