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

			foreach($input['get'] as $k => $v)
			{
				if (isset($input['post'][$k]))
				{
					$input['get'][$k] = $input['post'][$k];
				}
			}

			$input['request'] = array_merge(array(), (array)$input['get'], (array)$input['post']);

			foreach ($input as $k => $v)
			{
				$input[$k] = new self($v);
			}
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
