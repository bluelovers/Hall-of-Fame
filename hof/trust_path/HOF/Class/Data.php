<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Data extends HOF_Class_Array
{

	/**
	 * $_key = strtolower($_key);
	 * BASE_TRUST_PATH . '/HOF/Resource/'.ucfirst($_key).'/'.$_key.'.' . $no . '.yml'
	 */
	function _filename($_key, $no)
	{
		$_key = strtolower($_key);

		$ret = BASE_TRUST_PATH . '/HOF/Resource/' . ucfirst($_key) . '/' . $_key . '.' . $no . '.yml';

		return $ret;
	}

	/**
	 * load data from yaml
	 */
	function _load($_key, $no)
	{
		$_key = strtolower($_key);

		if (!isset($this->data[$_key][$no]))
		{
			$data = HOF_Class_Yaml::load($this->_filename($_key, $no));
			$this->data[$_key][$no] = $data;
		}

		$data = $this->data[$_key][$no];

		return $data;
	}

	/**
	 * @return Array
	 */
	function _fixArray($append = array())
	{
		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}
		}

		return (array)$append;
	}

}
