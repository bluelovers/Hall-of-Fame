<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_CodeGenerator_Php_Parameters extends Zend_CodeGenerator_Php_Abstract
{

	/**
	 * @var array
	 */
	protected $_parameters = array();

	function __construct($parameters = array())
	{
		parent::__construct();

		$this->setParameters($parameters);
	}

	/**
	 * setParameters()
	 *
	 * @param array $parameters
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function setParameters($parameters)
	{
		if ($parameters instanceof Sco_CodeGenerator_Php_Parameters)
		{
			$parameters = $parameters->getParameters();
		}

		foreach ($parameters as $parameter)
		{
			$this->setParameter($parameter);
		}
		return $this;
	}

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

	/**
	 * getParameters()
	 *
	 * @return array Array of Zend_CodeGenerator_Php_Parameter
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}

	/**
	 * generate()
	 *
	 * @return string
	 */
	public function generate()
	{
		$output = '';

		$parameters = $this->getParameters();
		if (!empty($parameters))
		{
			foreach ($parameters as $parameter)
			{
				$parameterOuput[] = $parameter->generate();
			}

			$output .= implode(', ', $parameterOuput);
		}

		return $output;
	}

}
