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

	public $autoView = true;

	public $view = null;

	const DEFAULT_CONTROLLER = 'default';
	const DEFAULT_ACTION = 'default';

	public $options = array(
		'escapeHtml' => true,
	);

	public static function &newInstance($controller, $action = null)
	{
		$controller = $controller ? $controller : self::DEFAULT_CONTROLLER;
		$action = $action ? $action : self::DEFAULT_ACTION;

		$Controller = HOF::putintoClassParts($controller);
		$Action = HOF::putintoClassParts($action);

		$controller = HOF::putintoPathParts($Controller);
		$action = HOF::putintoPathParts($Action);

		$Action[0] = strtolower($Action[0]);

		$class = 'HOF_Controller_' . $Controller;

		if (!class_exists($class))
		{
			die("Invalid Access");
		}

		$instance = new $class($controller, $action);

		return $instance;
	}

	public function __construct($controller, $action = null)
	{

		$this->controller = $controller;
		$this->action = $action;

		$this->output = new HOF_Class_Array($this->output);
		$this->input = new HOF_Class_Array($this->input);

		$this->_init();

		return $this;

	}

	function _init()
	{

	}

	public function main()
	{
		// bluelovers
		$this->_main_before();

		if ($this->_stop)
		{
			return $this;
		}

		if (!empty($this->action))
		{

			if ($this->action != self::DEFAULT_ACTION && !empty($this->allowActions) && !in_array($this->action, $this->allowActions))
			{
				$this->action = self::DEFAULT_ACTION;
			}

			$_method = '_main_action_' . $this->action;

			if (method_exists($this, $_method))
			{
				$this->$_method();
			}
		}

		if ($this->_stop)
		{
			return $this;
		}

		$this->_main_after();

		if ($this->_stop)
		{
			return $this;
		}

		return $this;
	}

	function _main_before()
	{
		return $this;
	}

	function _main_after()
	{
		if ($this->autoView)
		{
			$this->_view();
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

		$content->output();

		return $this;
	}

	protected function _render($template, $output = null, $content = null)
	{
		if ($output !== null)
		{
			$content = HOF_Class_View::render($this, $output, $template, $content);
		}
		else
		{
			$content = HOF_Class_View::render($this, &$this->output, $template, $content);
		}

		$content->output();

		return $this;
	}

}