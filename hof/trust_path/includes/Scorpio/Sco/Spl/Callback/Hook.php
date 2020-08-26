<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Spl_Callback_Hook extends Sco_Spl_Callback
{

	protected $hook_name;
	protected $hook_data;

	public $changed;

	public $callback_name;
	protected $callback_argv;

	public function func($func = null)
	{
		if ($func !== null)
		{
			$this->func = null;

			$this->callback_name = null;
			$this->callback_argv = null;

			$this->hook_data = $func;

			$this->changed = true;
		}

		return $this;
	}

	function _func()
	{
		if (!$this->changed)
		{
			return false;
		}

		$callback = null;

		$object = null;
		$method = null;
		$func = null;
		$data = null;
		$have_data = false;
		$closure = false;

		if (is_array($this->hook_data))
		{
			if (count($this->hook_data) < 1)
			{
				if (Sco_Hook::$throw_exception)
				{
					throw new Exception('Empty array in hooks for ' . $this->hook_name . "\n");
				}
			}
			elseif (is_object($this->hook_data[0]))
			{
				$object = $this->hook_data[0];
				if (class_exists('Closure', false) && $object instanceof Closure)
				{
					$closure = true;
					if (count($this->hook_data) > 1)
					{
						$data = $this->hook_data[1];
						$have_data = true;
					}
				}
				elseif ($object instanceof Sco_Spl_Callback_Interface)
				{
					$method = 'callback';

					if (count($this->hook_data) > 1)
					{
						$data = $this->hook_data[1];
						$have_data = true;
					}
				}
				else
				{
					if (count($this->hook_data) < 2)
					{
						$method = 'on' . $this->hook_name;
					}
					else
					{
						$method = $this->hook_data[1];
						if (count($this->hook_data) > 2)
						{
							$data = $this->hook_data[2];
							$have_data = true;
						}
					}
				}

				// bluelovers
			}
			elseif (is_string($this->hook_data[0]) && $this->hook_data[0] == 'func' && count($this->hook_data) == 3)
			{
				// 追加 $_EVENT 來允許存取 Sco_Event::instance($this->hook_name)->data)
				$func = create_function('$_EVENT, $_ARGV, ' . $this->hook_data[1], $this->hook_data[2]);

				$have_eval = true;
				// bluelovers

			}
			elseif (is_string($this->hook_data[0]))
			{
				$func = $this->hook_data[0];
				if (count($this->hook_data) > 1)
				{
					$data = $this->hook_data[1];
					$have_data = true;
				}
			}
			else
			{
				if (Sco_Hook::$throw_exception)
				{
					throw new Exception('Unknown datatype in hooks for ' . $this->hook_name . "\n");
				}
			}
		}
		elseif (is_string($this->hook_data))
		{
			# functions look like strings, too
			$func = $this->hook_data;
		}
		elseif (is_object($this->hook_data))
		{
			$object = $this->hook_data;
			if (class_exists('Closure', false) && $object instanceof Closure)
			{
				$closure = true;
			}
			elseif ($object instanceof Sco_Spl_Callback_Interface)
			{
				$method = 'callback';
			}
			else
			{
				$method = 'on' . $this->hook_name;
			}
		}
		else
		{
			if (Sco_Hook::$throw_exception)
			{
				throw new Exception('Unknown datatype in hooks for ' . $this->hook_name . "\n");
			}
		}

		/* We put the first data element on, if needed. */
		if ($have_data)
		{
			$hook_args = $data;
			//$hook_args = array_merge(array($data), self::$args[$event]);
		}
		else
		{
			$hook_args = array();
			//$hook_args = self::$args[$event];
		}

		if ($closure)
		{
			$callback = $object;
			$func = "hook-$event-closure";
		}
		elseif (isset($object))
		{
			$func = get_class($object) . '::' . $method;
			$callback = array($object, $method);
		}
		elseif (false !== strpos($func, '::'))
		{
			// 5.1 compat code
			// mediawiki 已經不使用以下這一段代碼來相容 PHP 5.1
			$callback = explode($func, '::', 2);
			// 5.1 compat code
		}
		else
		{
			$callback = $func;
		}

		// Run autoloader (workaround for call_user_func_array bug)
		if (!is_callable($callback, false, $callback_name))
		{
			throw new Exception(sprintf('Fatal error: Call to undefined callback %s()', $callback_name));
		}

		$this->func = $callback;

		$this->callback_name = $callback_name;
		$this->callback_argv = $hook_args;

		$this->changed = false;

		return true;
	}

	public function exec_array($argv = null)
	{
		$this->_func();

		if ($argv === null)
		{
			$argv = (array )$this->argv;
		}

		array_splice($argv, 1, 0, array($this->callback_argv));

		return call_user_func_array($this->func, $argv);
	}

}
