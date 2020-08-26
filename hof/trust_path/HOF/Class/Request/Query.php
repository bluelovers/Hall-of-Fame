<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Request_Query extends HOF_Class_Array
{

	protected $SOURCE_NAME;
	protected $SOURCE_DATA;

	function __construct($source_name, $source_data)
	{
		parent::__construct((array)$source_data);

		$this->SOURCE_DATA = $source_data;
		$this->SOURCE_NAME = $source_name;
	}

	function query()
	{
		return http_build_query($this->toArray(true, true));
	}

	function __toString()
	{
		return $this->query();
	}

}