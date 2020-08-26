<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_CodeGenerator_Php_Method extends Zend_CodeGenerator_Php_Method
{

	/**
	 * setParameter()
	 *
	 * @param Zend_CodeGenerator_Php_Parameter|array $parameter
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function setParameter($parameter)
	{
		if (is_array($parameter) || is_string($parameter))
		{
			$parameter = new Sco_CodeGenerator_Php_Parameter($parameter);
			$parameterName = $parameter->getName();
		}
		elseif ($parameter instanceof Zend_CodeGenerator_Php_Parameter)
		{
			$parameterName = $parameter->getName();
		}
		else
		{
			throw new Zend_CodeGenerator_Php_Exception('setParameter() expects either an array of method options or an instance of Zend_CodeGenerator_Php_Parameter');
		}

		$this->_parameters[$parameterName] = $parameter;
		return $this;
	}

}
