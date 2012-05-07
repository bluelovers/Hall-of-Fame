<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * @property array $request
 * @property array $get
 * @property array $post
 * @property array $server
 */
class HOF_Class_Request extends HOF_Class_Array
{

	function __construct($input = null)
	{
		parent::__construct();

		unset($this->_data_default_);

		$this->setup($input);
	}

	function setup($input = null)
	{
		if ($input === null)
		{
			$input = array(
				'get' => (array)$_GET,
				'post' => (array)$_POST,
				'server' => (array)$_SERVER,
			);

			$input['request'] = array_merge_recursive(array(), (array)$_GET, (array)$_POST);
		}

		$this->exchangeArray($input);

		return $this;
	}

	function query()
	{
		return http_build_query($this->request);
	}

	function __toString()
	{
		return $this->query();
	}

}
