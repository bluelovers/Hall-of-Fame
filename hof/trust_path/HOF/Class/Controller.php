<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Controller
{

	var $controller;
	var $action;

	protected $output = array();
	protected $template = null;

	protected $error = null;

	public $allowActions = array();

	public $autoView = true;

	const DEFAULT_CONTROLLER = 'default';
	const DEFAULT_ACTION = 'default';

	public static function &newInstance($controller, $action)
	{
		$controller = $controller ? $controller : self::DEFAULT_CONTROLLER;
		$action = $action ? $action : self::DEFAULT_ACTION;

		$Controller = self::putintoClassParts($controller);
		$Action = self::putintoClassParts($action);

		$controller = self::putintoPathParts($Controller);
		$action = self::putintoPathParts($Action);

		$Action[0] = strtolower($Action[0]);

		$class = 'HOF_Controller_' . $Controller;

		if (!class_exists($class))
		{
			die("Invalid Access");
		}

		$instance = new $class($controller, $action);

		return $instance;
	}

	public static function putintoClassParts($str)
	{
		$str = preg_replace('/[^a-z0-9_]/', '', $str);
		$str = explode('_', $str);
		$str = array_map('trim', $str);
		$str = array_diff($str, array(''));
		$str = array_map('ucfirst', $str);
		$str = implode('', $str);
		return $str;
	}

	public static function putintoPathParts($str)
	{
		$str = preg_replace('/[^a-zA-Z0-9]/', '', $str);
		$str = preg_replace('/([A-Z])/', '_$1', $str);
		$str = strtolower($str);
		$str = substr($str, 1, strlen($str));
		return $str;
	}

	public function __construct($controller, $action)
	{

		$this->controller = $controller;
		$this->action = $action;

		return $this;

	}

	public function main()
	{
		// bluelovers
		$this->_main_before();

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

		$this->_main_after();

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

	function _getTplFile($template)
	{
		return $template;
	}

	protected function _view()
	{

		if (!$this->template)
		{
			$this->template = BASE_PATH_TPL . '/' . $this->controller . '.' . $this->action . '.php';
		}

		// debug new tpl
		$this->template = $this->_getTplFile($this->template);

		$this->_escapeHtml($this->output);

		/*
		ob_start();
		$this->_display($this->output);
		$content = ob_get_contents();
		ob_end_clean();
		*/
		$content = HOF_Class_View::render($this, $this->output, $this->template);

		$content->output();

		return $this;
	}

	protected function _display($dura)
	{
		require $this->template;

		return $this;
	}

	protected function _render($content, $dura)
	{
		echo HOF_Class_View::render($this, $dura, $this->_getTplFile(BASE_PATH_TPL . '/theme.php'), $content);

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
				$var = htmlspecialchars((string )$var, ENT_QUOTES);
			}
		}
	}

}