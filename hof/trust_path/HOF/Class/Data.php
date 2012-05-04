<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Data extends HOF_Class_Array
{

	function _load($_key, $no)
	{
		$_key = strtolower($_key);

		if (!isset(self::getInstance()->data[$_key][$no]))
		{
			$data = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/'.ucfirst($_key).'/'.$_key.'.' . $no . '.yml');
			self::getInstance()->data[$_key][$no] = $data;
		}

		$data = self::getInstance()->data[$_key][$no];

		return $data;
	}

}
