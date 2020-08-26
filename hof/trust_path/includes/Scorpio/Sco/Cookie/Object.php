<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Cookie_Object extends ArrayObject implements Sco_Cookie_Interface
{

	protected $_config = array(

		'default_expire' => 0,
		'default_path' => '/',
		'default_domain' => '.',
		'default_secure' => false,
		'default_httponly' => false,

		'autosave' => true,
		'header_remove_cookies' => true,

		);

	protected $_cookies;

	/**
	 * @param string $index
	 * @returns mixed
	 *
	 * Workaround for http://bugs.php.net/bug.php?id=40442 (ZF-960).
	 */
	public function offsetExists($index)
	{
		return array_key_exists($index, $this);
	}

	public function __construct($registry = array())
	{
		parent::__construct($_COOKIE, Sco_Array::ARRAY_PROP_BOTH);

		foreach ((array )$registry as $k => $v)
		{
			if (method_exists($this, ucfirst($k)))
			{
				$this->$k($v);
			}
		}
	}

	public function setConfig($config)
	{
		$this->_config = array_merge($this->_config, $config);

		return $this;
	}

	public function getConfig($config)
	{
		return $this->_config;
	}

	public function setCookies($cookies)
	{
		$this->exchangeArray($cookies);

		return $this;
	}

	public function getCookies($cookies)
	{
		return $this->getArrayCopy();
	}

	public function save()
	{
		if ($this->_config['header_remove_cookies'])
		{
			Sco_Cookie_Helper::header_remove_cookies();
		}

		$map = array(
			'expire',
			'path',
			'domain',
			'secure',
			'httponly',
			);

		foreach ($this as $name => $value)
		{
			$_config = array();

			$_config['name'] = $name;
			$_config['value'] = $value;

			foreach ($map as $k)
			{
				if (isset($this->_cookies[$name][$k]))
				{
					$_config[$k] = $this->_cookies[$name][$k];
				}
				elseif ($k == 'expire' && $this->_config['default_' . $k] > 0)
				{
					$_config[$k] = time() + $this->_config['default_' . $k];
				}
				else
				{
					$_config[$k] = $this->_config['default_' . $k];
				}
			}

			Sco_Cookie::setcookie_array($_config);
		}
	}

	public function __destruct()
	{
		if ($this->_config['autosave'] && $this === Sco_Cookie::getInstance())
		{
			$this->save();
		}

		//var_dump(headers_list());
	}

	public function get($index, $default = null)
	{
		if (!$this->offsetExists($index))
		{
			return $default;
		}

		return $this->offsetGet($index);
	}

	public function set($name, $value, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
	{
		$this->offsetSet($name, $value);

		if (func_num_args() > 2)
		{
			$this->_cookies[$name] = array(
				'name' => $name,
				'value' => $value,
				'expire' => $expire,
				'path' => $path,
				'domain' => $domain,
				'secure' => $secure,
				'httponly' => $httponly,
				);
		}
	}

}
