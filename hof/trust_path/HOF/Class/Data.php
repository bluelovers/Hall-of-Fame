<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Data
{

	var $autocache = true;
	var $data;

	function __construct()
	{

	}

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

		if (!$this->autocache)
		{
			$data = HOF_Class_Yaml::load($this->_filename($_key, $no));
		}
		else
		{
			if (!isset($this->data[$_key][$no]))
			{
				$data = HOF_Class_Yaml::load($this->_filename($_key, $no));
				$this->data[$_key][$no] = $data;
			}

			$data = $this->data[$_key][$no];
		}

		return $data;
	}

	function _save($_key, $no, $data)
	{
		$_key = strtolower($_key);

		return HOF_Class_Yaml::save($this->_filename($_key, $no), $data);
	}

	static function _load_list($_key, $sort = SORT_NUMERIC)
	{
		if ($sort === null) $sort = SORT_NUMERIC;

		$list = array();

		$regex = self::_filename($_key, '*');

		$regex = '/^' . str_replace('\*', '(.+)', preg_quote($regex, '/')) . '$/i';

		foreach (glob(self::_filename($_key, '*')) as $file)
		{
			$list[] = preg_replace($regex, '$1', $file);
		}

		if ($sort)
		{
			sort($list, $sort);
		}

		return $list;
	}

}
