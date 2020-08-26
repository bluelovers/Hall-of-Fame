<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Loader_Autoloader extends Zend_Loader_Autoloader
{
	protected static $_instance;

	const NS_EMPTY = '*';

	static $error = null;

	/**
	 * @var array Supported namespaces 'Zend' and 'ZendX' by default.
	 */
	protected $_namespaces = array();

	/**
	 * @var array Default autoloader callback
	 */
	protected $_defaultAutoloader = array('Zend_Loader', 'loadClass');

	/**
	 * @var bool Whether or not to suppress file not found warnings
	 */
	protected $_suppressNotFoundWarnings = false;

	/**
	 * Constructor
	 *
	 * Registers instance with spl_autoload stack
	 *
	 * @return void
	 */
	protected function __construct()
	{
		Zend_Loader_Autoloader::getInstance()->unshiftAutoloader(array(__CLASS__, 'autoload'));

		$this->_internalAutoloader = array($this, '_autoload');
	}

	/**
	 * Internal autoloader implementation
	 *
	 * @param  string $class
	 * @return bool
	 */
	protected function _autoload($class, $autoloader = null, $ns = null, $noerror = false)
	{
		$callback = $this->getDefaultAutoloader();
		try
		{
			if ($this->suppressNotFoundWarnings())
			{
				@call_user_func($callback, $class, $autoloader, $ns, null, $noerror);
			}
			else
			{
				call_user_func($callback, $class, $autoloader, $ns, null, $noerror);
			}

			if (class_exists($class, false) || interface_exists($class, false))
			{
				return $class;
			}
			else
			{
				return false;
			}
		}
		catch (Zend_Exception $e)
		{
			return false;
		}
	}

	/**
	 * Retrieve singleton instance
	 *
	 * @return Dura_Autoloader
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Reset the singleton instance
	 *
	 * @return void
	 */
	public static function resetInstance()
	{
		self::$_instance = null;
	}

	/**
	 * Autoload a class
	 *
	 * @param  string $class
	 * @return bool
	 */
	public static function autoload($class)
	{
		$self = self::getInstance();

		$autoloaders = array();

		foreach ($self->getRegisteredNamespaces() as $ns)
		{
			if (0 === strpos($class, $ns))
			{
				$autoloaders = $self->getNamespaceAutoloaders($ns);
				break;
			}
		}

		if (empty($autoloaders) || empty($ns))
		{
			if (!$autoloaders = $self->getNamespaceAutoloaders('*'))
			{
				return false;
			}

			$ns = null;
		}

		$count = count($autoloaders);

		foreach ($autoloaders as $autoloader)
		{
			if ($autoloader instanceof Zend_Loader_Autoloader_Interface)
			{
				if ($autoloader->autoload($class))
				{
					return true;
				}
			}
			elseif (is_callable($autoloader))
			{
				if (call_user_func($autoloader, $class, $ns))
				{
					return true;
				}
			}
			elseif (is_string($autoloader))
			{
				$skip = true;

				if (call_user_func($self->_internalAutoloader, $class, $autoloader, $ns, ($ns === null || $count > 1)))
				{
					return true;
				}
			}

			$count--;
		}

		if (!$skip && !in_array($self->_internalAutoloader, $autoloaders))
		{
			if (call_user_func($self->_internalAutoloader, $class, $autoloader, $ns))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Set autoloaders for a specific namespace
	 *
	 * @param  array $autoloaders
	 * @param  string $namespace
	 * @return Zend_Loader_Autoloader
	 */
	protected function _setNamespaceAutoloaders(array $autoloaders, $namespace = '')
	{
		parent::_setNamespaceAutoloaders($autoloaders, $namespace);

		$this->registerNamespace($namespace);

		return $this;
	}

	protected function _fixNamespace(&$namespace, $allow_self = false, $_append = '_')
	{
		if ($allow_self && $_append)
		{
			$add = array();

			$namespace = (array )$namespace;

			$add[] = &$namespace;

			foreach ($namespace as $ns)
			{
				if (substr($ns, $i = 0 - strlen($_append)) === $_append)
				{
					$add[] = substr($ns, 0, $i);
				}
			}

			call_user_func_array('array_push', $add);
		}

		return $namespace;
	}

	public function pushAutoloader($callback, $namespace = '', $allow_self = false, $_append = '_')
	{
		self::_fixNamespace($namespace, $allow_self, $_append);

		return call_user_func(array('parent', __FUNCTION__ ), $callback, $namespace);
	}

	public function unshiftAutoloader($callback, $namespace = '', $allow_self = false, $_append = '_')
	{
		self::_fixNamespace($namespace, $allow_self, $_append);

		return call_user_func(array('parent', __FUNCTION__ ), $callback, $namespace);
	}

}
