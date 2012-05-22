<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_View
{
	var $output = array();
	var $template = null;
	var $template_file = null;
	var $content = null;
	var $extend = null;
	var $body = null;

	var $controller;

	protected static $_suppressNotFoundWarnings = false;

	function __construct($controller, &$output, $template = null)
	{
		$this->controller = $controller;

		$this->output = &$output;
		$this->template = $template;

		$this->template_file = self::_getTplFile($this->template);
	}

	function __toString()
	{
		return $this->body();
	}

	static function render($controller, $output, $template = null, $content = null)
	{
		$_this = new self($controller, &$output, $template);

		if ($_this->controller) $_this->controller->view['render'][$_this->template][] = $_this;

		$_this->content = $content;

		$content = $_this->_view();

		if ($_this->extend)
		{
			$content = self::render($_this->controller, &$_this->output, $_this->extend, $content);
		}

		$_this->body = $content;

		return $_this;
	}

	function body($source = false, $nolf = false)
	{
		$output = (string )$this->body;

		if (!$source)
		{
			$output = preg_replace('/^[\s\t\n]*|[\s\n\t]*$/is', '', $output);
			$output = preg_replace('/[ \r]*(\t+)[ \r]*/is', "\\1", $output);
			$output = preg_replace('/[ \t\r]+(\n)/is', '\\1', $output);
			$output = preg_replace('/(\n)[ \t\r]+/is', '\\1', $output);
			$output = preg_replace('/(\n)\s+/is', '\\1', $output);
		}

		if ($nolf)
		{
			$output = str_replace("\n", '', $output);
		}

		return $output;
	}

	function output()
	{
		$output = $this->__toString();

		//		$output = preg_replace('/^[\s\n]*|[\s\n]*$/is', '', $output);
		//		$output = preg_replace('/[ \t\r]*(\n)/is', '\\1', $output);
		//		$output = preg_replace('/(\n)[ \t\r]*/is', '\\1', $output);
		//		$output = preg_replace('/[\t\s\r\n]*(\n)[\t\s\r\n]*/is', '\\1', $output);

		echo $output;

		return $this;
	}

	function slot($name, $content = null)
	{
		$view = self::render($this->controller, &$this->output, $name, $content);

		if ($this->controller) $this->controller->view['slot'][$name][] = $view;

		return $view;
	}

	function _getTplFile($name)
	{
		$template = BASE_PATH_TPL . '/' . $name . '.php';

		return $template;
	}

	function exists($name)
	{
		$template = self::_getTplFile($name);

		return file_exists($template);
	}

	function extend($name)
	{
		$this->extend = $name;

		if ($this->controller) $this->controller->view['extend'][$this->extend][] = $this->extend;
	}

	protected function _view()
	{
		ob_start();
		$this->_display($this->output);
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public static function suppressNotFoundWarnings($flag = null)
	{
		if (null !== $flag)
		{
			self::$_suppressNotFoundWarnings = (bool)$flag;
		}

		return self::$_suppressNotFoundWarnings;
	}

	protected function _display($tplcache)
	{
		if (self::$_suppressNotFoundWarnings && !file_exists($this->template_file))
		{

		}
		else
		{
			include ($this->template_file);
		}
	}

	function set($k, $v)
	{
		$this->output[$k] = $v;

		return $this;
	}

	function get($k, $default = null)
	{
		return (!isset($default) || isset($this->output[$k])) ? $this->output[$k] : $default;
	}

	function callMethod($func)
	{
		if ($this->controller && $this->controller->allowCallMethod())
		{
			$args = func_get_args();

			$callback = array_shift($args);

			return call_user_func_array(array($this->controller, $callback), $args);
		}
		else
		{
			throw new Exception("Can't Call Method!");
		}
	}
}
