<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

if (!function_exists('json_encode'))
{
	function json_encode($data)
	{
		if (1 || class_exists('Zend_Json'))
		{
			return Zend_Json::encode($data);
		}
		elseif (class_exists('Services_JSON'))
		{
			$obj = new Services_JSON();
			return $obj->encodeUnsafe($data);
		}
	}
}

if (!function_exists('json_decode'))
{
	function json_decode($json, $assoc = false)
	{
		if (1 || class_exists('Zend_Json'))
		{
			return Zend_Json::decode($json, $assoc ? Zend_Json::TYPE_ARRAY : Zend_Json::TYPE_OBJECT);
		}
		elseif (class_exists('Services_JSON'))
		{
			$obj = new Services_JSON($assoc ? SERVICES_JSON_LOOSE_TYPE : 0);
			return $obj->decode($json);
		}
	}
}
