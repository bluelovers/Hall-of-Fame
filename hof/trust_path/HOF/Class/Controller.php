<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Controller
{

	var $controller;
	var $action;

	protected $input = array();
	protected $output = array();
	protected $template = null;

	protected $error = null;

	public $_stop = null;

	public $allowActions = array();

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
		);

	protected $allowCallMethod = true;

	protected $_controller_cache = array();

	protected static $instance = array();

	public static function &newInstance($controller, $action = null)
	{
		$_s = self::_setup_fix($controller, $action);

		$class = 'HOF_Controller_' . $_s['Controller'];

		if (!class_exists($class))
		{
			die("Invalid Access {$_s[Controller]}::{$_s[Action]}");
		}

		self::$instance[$_s['controller']] = new $class($_s['controller'], $_s['action']);

		return self::$instance[$_s['controller']];
	}

	public static function &getInstance($controller, $action = null)
	{
		$_s = self::_setup_fix($controller, $action);

		if (isset(self::$instance[$_s['controller']]))
		{
			return self::$instance[$_s['controller']]->_main_setup($_s['action']);
		}
		else
		{
			return self::newInstance($_s['controller'], $_s['action']);
		}
	}

	public function __construct($controller, $action = null)
	{
		$_s = self::_setup_fix($controller, $action);

		$this->controller = $_s['controller'];
		$this->action = $_s['action'];

		$this->output = new HOF_Class_Array($this->output);
		$this->input = new HOF_Class_Array($this->input);
		$this->_cache = new HOF_Class_Array($this->_cache);

		$this->_main_call('_main_init');

		return $this;

	}

	public function _setup_fix($controller = null, $action = null)
	{
		$controller = $controller ? $controller : self::DEFAULT_CONTROLLER;
		$action = $action ? $action : self::DEFAULT_ACTION;

		$Controller = HOF::putintoClassParts($controller);
		$Action = HOF::putintoClassParts($action);

		$controller = HOF::putintoPathParts($Controller);
		$action = HOF::putintoPathParts($Action);

		$Action[0] = strtolower($Action[0]);

		return array(
			'Controller' => $Controller,
			'Action' => $Action,
			'controller' => $controller,
			'action' => $action,
			);
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

		if (!empty($this->action))
		{
			$this->_main_stop(false);

			if ($this->action != self::DEFAULT_ACTION && !empty($this->allowActions) && !in_array($this->action, $this->allowActions))
			{
				$this->action = self::DEFAULT_ACTION;
			}

			$args = func_get_args();
			array_shift($args);

			$_method = '_main_action_' . $this->action;

			array_unshift($args, $_method);

			$this->_controller_cache['event'][$this->action]++;

			if (method_exists($this, $_method))
			{
				call_user_func_array(array($this, '_main_call'), (array )$args);
			}
		}

		if ($this->_main_stop())
		{
			return $this;
		}

		$this->_main_call_once('_main_after');

		return $this;
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
			$this->_main_call_once('_view');
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
