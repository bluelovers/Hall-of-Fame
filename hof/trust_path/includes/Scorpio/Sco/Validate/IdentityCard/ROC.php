<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Validate_IdentityCard_ROC extends Zend_Validate_Abstract
{

	const ERROR = 'error';

	protected $_messageTemplates = array(self::ERROR => "'%value%' is not a valid ROC Identity Card value");

	protected $_vaild_result;

	public function isValid($value)
	{
		$this->_setValue($value);
		$this->_vaild_result = null;

		if (!$this->_vaild_result = Sco_IdentityCard_ROC::valid($value))
		{
			$this->_error(self::ERROR);
			return false;
		}

		return true;
	}

	public function getResultCity()
	{
		return $this->_vaild_result[1];
	}

	public function getResultGender()
	{
		return $this->_vaild_result[2];
	}

}
