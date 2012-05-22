<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class HOF_Class_Controller
{

	var $controller;
	var $action;
	var $extra;

	protected $input = array();
	protected $output = array();
	protected $template = null;

	protected $error = null;

	public $_stop = null;

	public $view = null;

	/**
	 * @var HOF_Class_View
	 */
	protected $content = null;

	const DEFAULT_CONTROLLER = 'default';
	const DEFAULT_ACTION = 'default';

	public $options = array(
		'escapeHtml' => true,
		'skip_chk_call' => array(
			'_main_input' => false,
			'_main_after' => false,
			'_view' => false,
			),
		'autoView' => true,
		'autoViewOutput' => true,
		'autoViewOutputRender' => true,

		'method_action_pre' => '_main_action_',

		'defaultAction' => self::DEFAULT_ACTION,

		/**
		 * allowActions
		 * 				-1		only use default action
		 * 				false|0	default or dev
		 * 				true	auto get action list and only use it
		 *
		 * @var array|bool|int
		 */
		'allowActions' => false,

		);

	protected $allowCallMethod = true;

	protected $_controller_cache = array();

	protected static $instance = array();

	public static function &newInstance($controller, $action = null, $extra = null)
	{
		$_s = self::_setup_fix($controller, $action);

		$class = 'HOF_Controller_' . $_s['Controller'];

		if (!class_exists($class))
		{
			die("Invalid Access {$_s[Controller]}::{$_s[Action]}");
		}

		self::$instance[$_s['controller']] = new $class($_s['controller'], $_s['action'], $extra);

		return self::$instance[$_s['controller']];
	}

	public static function &getInstance($controller, $action = null, $extra = null)
	{
		$_s = self::_setup_fix($controller, $action);

		if (isset(self::$instance[$_s['controller']]))
		{
			return self::$instance[$_s['controller']]->_main_setup($_s['action'], $extra);
		}
		else
		{
			return self::newInstance($_s['controller'], $_s['action'], $extra);
		}
	}

	public function __construct($controller, $action = null, $extra = null)
	{
		$_s = self::_setup_fix($controller, $action);

		$this->controller = $_s['controller'];
		$this->action = $_s['action'];
		$this->extra = $extra;

		$this->output = new HOF_Class_Array($this->output);
		$this->input = new HOF_Class_Array($this->input);
		$this->_cache = new HOF_Class_Array($this->_cache);

		$this->_main_call('_main_init');

		$this->__construct_init();

		return $this;

	}

	protected function __construct_init()
	{
		if ($this->action == self::DEFAULT_ACTION && $this->action != $this->options['defaultAction'])
		{
			$this->action = $this->options['defaultAction'];
		}

		if ($this->options['allowActions'] === true)
		{
			$this->options['allowActions'] = $this->_main_list_action();
		}
	}

	public function _setup_fix($_controller = null, $_action = null)
	{
		static $_cache;

		if (!$_cache[$_controller][$_action])
		{

			if (isset($this))
			{
				$controller = $_controller ? $_controller : ($this->controller ? $this->controller : self::DEFAULT_CONTROLLER);
				$action = $_action ? $_action : $this->options['defaultAction'];
			}
			else
			{
				$controller = $_controller ? $_controller : self::DEFAULT_CONTROLLER;
				$action = $_action ? $_action : self::DEFAULT_ACTION;
			}

			$Controller = HOF::putintoClassParts($controller);
			$Action = HOF::putintoClassParts($action);

			$controller = HOF::putintoPathParts($Controller);
			$action = HOF::putintoPathParts($Action);

			$Action[0] = strtolower($Action[0]);

			$ret = array(
				'Controller' => $Controller,
				'Action' => $Action,
				'controller' => $controller,
				'action' => $action,
				);

			//$_cache[$controller][$action] = $ret;
			$_cache[$_controller][$_action] = $ret;
			//$_cache[$Controller][$Action] = $ret;
		}

		return $_cache[$_controller][$_action];
	}

	protected function _main_input()
	{

	}

	protected function _main_call($func)
	{
		$this->_controller_cache['call'][$func]++;

		$args = func_get_args();
		array_shift($args);

		return call_user_func_array(array($this, $func), (array )$args);
	}

	public function _main_stop($flag = null)
	{
		if ($flag !== null)
		{
			$this->_stop = $flag;
		}

		return $this->_stop;
	}

	protected function _main_call_once($func)
	{
		if (!$this->_controller_cache['call'][$func] || $this->options['skip_chk_call'][$func])
		{
			$args = func_get_args();

			call_user_func_array(array($this, '_main_call'), (array )$args);

			return $this->_controller_cache['call'][$func];
		}

		return false;
	}

	public function _main_setup($action = null)
	{
		$_s = self::_setup_fix($this->controller, $action);

		$this->action = $_s['action'];

		$this->_main_stop(false);

		return $this;
	}

	public function _main_exec_once($action = null)
	{
		$_s = self::_setup_fix($this->controller, $action);

		if ($this->_controller_cache['event'][$_s['action']] > 0)
		{
			return $this;
		}

		$args = func_get_args();
		return call_user_func_array(array($this, '_main_exec'), (array )$args);
	}

	public function _main_exec($action = null)
	{
		$this->_main_call('_main_setup', $action);

		$this->_main_call_once('_main_before');

		if ($this->_main_stop())
		{
			return $this;
		}

		$this->_main_call_once('_main_input');

		if ($this->_main_stop())
		{
			return $this;
		}

		$ret = null;

		if (!empty($this->action))
		{
			$this->_main_stop(false);

			if ($this->action != $this->options['defaultAction'] && !empty($this->options['allowActions']) && !in_array($this->action, (array)$this->options['allowActions']))
			{
				$this->action = $this->options['defaultAction'];
			}

			$_action = $this->action;

			$this->_controller_cache['event'][$_action]++;

			//$_method = '_main_action_' . $this->action;
			if ($_method = $this->_main_exists($_action))
			{
				$args = func_get_args();
				array_shift($args);
				array_unshift($args, $_method);

				$ret = call_user_func_array(array($this, '_main_call'), (array )$args);
			}

			$this->_controller_cache['event.func'][$_action] = $_method;
		}

		$this->_main_call('_main_result', $action, $ret);

		if ($this->_main_stop())
		{
			return $this;
		}

		$this->_main_call_once('_main_after');

		return $this;
	}

	function _main_exists($action, $force = false)
	{
		$_s = self::_setup_fix($this->controller, $action);
		$_method = $this->options['method_action_pre'] . $_s['action'];

		if (!$ret = method_exists($this, $_method))
		{
			$_method = $this->options['method_action_pre'] . $_s['Action'];

			if (!$ret = method_exists($this, $_method))
			{

			}
		}

		return ($force || $ret) ? $_method : false;
	}

	function _main_list_action($skip = array())
	{
		$method_pre = $this->options['method_action_pre'];

		$list = array();

		foreach (get_class_methods($this) as $method)
		{
			if (strpos($method, $method_pre) === 0)
			{
				$action = str_replace($method_pre, '', $method);

				$list[$method] = $action;
			}
		}

		if (!empty($skip)) $list = array_diff($list, $skip);

		return $list;
	}

	function _main_result($action, $ret)
	{

	}

	protected function _init()
	{

	}

	protected function _main_init()
	{
		$this->_main_call('_init');
	}

	public function main()
	{
		@$this->_controller_cache['call'][__FUNCTION__ ]++;

		$this->_main_exec_once($this->action);

		return $this;
	}

	protected function _main_before()
	{
		$this->_main_call_once('_main_input');

		return $this;
	}

	protected function _main_after()
	{
		if ($this->options['autoView'])
		{
			$this->_main_call_once('_main_view');
		}

		return $this;
	}

	protected function _escapeHtml(&$vars)
	{
		foreach ($vars as $key => &$var)
		{
			if (is_array($var))
			{
				$this->_escapeHtml($var);
			}
			elseif (!is_object($var))
			{
				$var = HOF::escapeHtml($var);
			}
		}
	}

	/**
	 * @return HOF_Class_View
	 */
	public function view()
	{
		return $this->content;
	}

	protected function _view()
	{
		$this->_main_call('_main_view');

		return $this;
	}

	protected function _main_view()
	{
		if (!$this->template)
		{
			$this->template = $this->controller . '.' . $this->action;
		}

		$this->template = $this->template;

		if ($this->options['escapeHtml'])
		{
			$this->_escapeHtml(&$this->output);
		}

		$content = HOF_Class_View::render($this, &$this->output, $this->template);

		if ($this->options['autoViewOutput'])
		{
			$content->output();
		}

		$this->content = &$content;

		return $this;
	}

	protected function _render($template, $output = null, $content = null)
	{
		$this->_controller_cache['call'][__FUNCTION__ ]++;

		if ($output !== null)
		{
			$content = HOF_Class_View::render($this, $output, $template, $content);
		}
		else
		{
			$content = HOF_Class_View::render($this, &$this->output, $template, $content);
		}

		if ($this->options['autoViewOutputRender'])
		{
			$content->output();
		}

		return $content;
	}

	function allowCallMethod($flag = null)
	{
		$this->_controller_cache['call'][__FUNCTION__ ]++;

		$_attr = __FUNCTION__;

		if (null !== $flag)
		{
			$this->$_attr = (bool)$flag;
		}

		return $this->$_attr;
	}

}
