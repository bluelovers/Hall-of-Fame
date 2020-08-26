<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

final class Sco
{

	/**
	 * this framework need php version >= 5.2.0
	 */
	const PHP_VERSION = '5.2.0';

	/**
	 * @var Sco
	 */
	private static $_instance;
	/**
	 * @var bool
	 */
	private static $_instanced;

	/**
	 * @var Sco_Registry
	 */
	private $registry;

	public function __construct()
	{
		if (self::$_instanced || isset(self::$_instance))
		{
			self::__construct_error();
			exit();
		}

		self::$_instance = &$this;
		self::$_instanced = true;
	}

	/**
	 * exec registed destruct callback
	 */
	public static function destruct()
	{
		if (isset(self::$_instance->registry) && isset(self::$_instance->registry->destruct_handler) && self::$_instance->registry->destruct_handler instanceof Sco_Spl_Callback_Interface)
		{
			self::$_instance->registry->destruct_handler->exec();
			self::$_instance->registry->destruct_handler->disable(true);
		}
	}

	public static function instance()
	{
		if (!self::$_instanced)
		{
			new self();
		}

		return true;
	}

	public function __destruct()
	{
		if (self::$_instanced && isset(self::$_instance))
		{
			self::destruct();
		}

		self::$_instance = null;
	}

	public function __clone()
	{
		self::__construct_error();
		exit();
	}

	private static function __construct_error()
	{
		self::$_instance = null;
		exit(sprintf('Fatal error: Cannot redeclare %s() previously declared', __CLASS__));
	}

	public static function &registry()
	{
		if (!isset(self::$_instance->registry))
		{
			self::$_instance->registry = new Sco_Registry();

			/**
			 * destruct callback
			 *
			 * @var Sco_Spl_Callback_Iterator
			 */
			self::$_instance->registry->destruct_handler = new Sco_Spl_Callback_Iterator(array());
		}

		return self::$_instance->registry;
	}

}
