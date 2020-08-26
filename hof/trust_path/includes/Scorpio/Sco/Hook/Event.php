<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Hook_Event extends Sco_Array
{

	protected static $namespace = NS_DEFAULT;

	/**
	 * @var Sco_Hook_Event
	 */
	protected static $_instance;

	/**
	 * Namespace - which namespace this instance of zend-session is saving-to/getting-from
	 *
	 * @var string
	 */
	protected $_namespace = NS_DEFAULT;

	protected $hook_data;

	/**
	 * @return Sco_Hook_Event
	 */
	public function __construct($namespace = null)
	{
		if ($namespace === null)
		{
			$namespace = self::$namespace;
		}

		if (isset(self::$_instance[$namespace]))
		{
			throw new RuntimeException(sprintf('Fatal error: Cannot redeclare Event with Namespace \'%s\' previously declared', $namespace));
		}
		elseif (!self::chkNamespace($namespace))
		{
			throw new InvalidArgumentException(sprintf('Fatal error: Invaild Event Namespace \'%s\'', $namespace));
		}

		self::$_instance[$namespace] = &$this;

		parent::__construct(array(), array('prop' => false));

		return $this;
	}

	/**
	 * @return Sco_Hook_Event
	 */
	public static function &getInstance($namespace = null)
	{
		if ($namespace === null)
		{
			$namespace = self::$namespace;
		}

		if (isset(self::$_instance[$namespace]))
		{
			return self::$_instance[$namespace];
		}

		return new self($namespace);
	}

	/**
	 * @param Sco_Hook $hook_name
	 */
	public function offsetSet($k, $hook_name)
	{
		$hook = null;

		if (is_string($hook_name))
		{
			$k = $hook_name;
			$hook = new Sco_Hook($hook_name);
		}
		else
		{
			$hook = $hook_name;
		}

		if (!self::chkName($k) || !$hook instanceof Sco_Hook)
		{
			throw new InvalidArgumentException(sprintf('index key %s invaild or class %s not instanceof Sco_Hook', $k, get_class($hook)));
		}

		$hook->setEvent($this);

		return parent::offsetSet($k, $hook);
	}

	/**
	 * @return Sco_Hook
	 */
	public function offsetGet($k)
	{
		if (!isset($this[$k]))
		{
			if (self::chkName($k))
			{
				$this->offsetSet($k, $k);
			}
			else
			{
				throw new InvalidArgumentException(sprintf('index key %s invaild or class %s not instanceof Sco_Hook', $k, get_class($hook)));
			}
		}

		return parent::offsetGet($k);
	}

	public static function chkNamespace($v)
	{
		return (bool)!(empty($v) || !is_string($v) || is_numeric($v));
	}

	public static function chkName($v)
	{
		return (bool)!(empty($v) || !is_string($v) || is_numeric($v));
	}

	/**
	 * @return Sco_Hook
	 */
	public function getHook($hook_name, $namespace = null)
	{
		if (isset($this))
		{
			return $this->offsetGet($hook_name);
		}
		else
		{
			return self::getInstance($namespace)->offsetGet($hook_name);
		}
	}

	/**
	 * @return Sco_Hook_Event
	 */
	public function setHook($hook_name, $namespace = null, $hook = array())
	{
		if (isset($this))
		{
			$this->offsetGet($hook_name)->append($namespace);

			return $this;
		}
		else
		{
			$_EVENT = self::getInstance($namespace);

			$_EVENT->offsetGet($hook_name)->append($hook);

			return $_EVENT;
		}
	}

	public function setData($hook_name, $data, $namespace = null)
	{
		if (isset($this))
		{
			$this->hook_data[$hook_name] = &$data;

			return $this;
		}
		else
		{
			$_EVENT = self::getInstance($namespace);

			$_EVENT->hook_data[$hook_name] = &$data;

			return $_EVENT;
		}
	}

	public function getData($hook_name, $namespace = null)
	{
		if (isset($this))
		{
			return $this->offsetGet($hook_name)->getData();
		}
		else
		{
			$_EVENT = self::getInstance($namespace);

			return $_EVENT->offsetGet($hook_name)->getData();
		}
	}

	public function exec($hook_name, $namespace = null)
	{
		if (isset($this))
		{
			$argv = func_num_args() > 1 ? array_slice(func_get_args(), 1) : array();

			return $this->exec_array($hook_name, $argv);
		}
		else
		{
			$argv = func_num_args() > 2 ? array_slice(func_get_args(), 2) : array();

			return self::exec_array($hook_name, $namespace, $argv);
		}
	}

	public function exec_array($hook_name, $namespace = null, $argv = array())
	{
		if (isset($this))
		{
			//var_dump($this);
			$_EVENT = &$this;
			$_HOOK = $_EVENT->offsetGet($hook_name);

			if (isset($_EVENT->hook_data[$hook_name]))
			{
				$data = &$_EVENT->hook_data[$hook_name];
				$null = null;
				$_EVENT->hook_data[$hook_name] = &$null;
			}
			else
			{
				$data = array();
			}

			return $_HOOK->setEvent($_EVENT, $data)->exec_array($namespace);
		}
		else
		{
			$_EVENT = self::getInstance($namespace);
			$_HOOK = $_EVENT->offsetGet($hook_name);

			//var_dump($_EVENT);

			if (isset($_EVENT->hook_data[$hook_name]))
			{
				$data = &$_EVENT->hook_data[$hook_name];
				$null = null;
				$_EVENT->hook_data[$hook_name] = &$null;
			}
			else
			{
				$data = array();
			}

			return $_HOOK->setEvent($_EVENT, $data)->exec_array($argv);
		}
	}

}
