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

	/**
	 * @var HOF_Class_Request_Query
	 */
	public $get;
	/**
	 * @var HOF_Class_Request_Query
	 */
	public $post;
	/**
	 * @var HOF_Class_Request_Query
	 */
	public $request;
	/**
	 * @var HOF_Class_Request_Query
	 */
	public $server;

	function __construct($input = null)
	{
		parent::__construct();

		$this->setup($input);
	}

	function setup($input = null)
	{
		if ($input === null)
		{
			$input_map = array(
				'get' => '_GET',
				'post' => '_POST',
				'server' => '_SERVER',
				'request' => '_REQUEST'
			);

			$input = array(
				'get' => $_GET,
				'post' => $_POST,
				'server' => $_SERVER,
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
				$input[$k] = new HOF_Class_Request_Query($input_map[$k], $v);
			}
		}

		$this->exchangeArray($input);

		return $this;
	}

	function query()
	{
		return (string)$this->request;
	}

	function __toString()
	{
		return $this->query();
	}

}
