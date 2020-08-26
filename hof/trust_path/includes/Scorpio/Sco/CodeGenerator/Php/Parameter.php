<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_CodeGenerator_Php_Parameter extends Zend_CodeGenerator_Php_Parameter
{

	public function __construct($options = array())
	{
		if (is_string($options))
		{
			$options = array('Name' => $options);
		}

		parent::__construct($options);
	}

}
