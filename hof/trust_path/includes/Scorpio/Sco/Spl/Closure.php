<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * Simple closure class; Erling Ellingsen, 2003.
 *
 * @link http://steike.com/PhpClosures
 * @author http://steike.com/PhpClosures
 */
class Sco_Spl_Closure
{
	var $code;
	var $env;

	protected $_this;

	public function __construct($code, $env)
	{
		$this->code = $code;
		$this->env = $env;
	}

	/**
	 * @return Sco_Spl_Closure
	 */
	public static function bind($closure , $newthis, $newscope = 'static')
	{
		return $closure->bindTo($newthis, $newscope);
	}

	/**
	 * @return Sco_Spl_Closure
	 */
	public function bindTo($newthis, $newscope = 'static')
	{
		$null = null;
		$this->_this = &$null;

		$this->_this = $newthis;

		return $this;
	}

	public function call($__args = NULL)
	{
		// $this will probably be clobbered by the next step, so grab our
		// code and environment now
		$__code = $this->code;
		$__env = &$this->env;

		// set up the scope we need
		// extract() doesn't do references, so we can't use that
		foreach (array_keys($__env) as $__key) $$__key = &$__env[$__key];

		return eval($__code);
	}

	function makeGrabber($s)
	{
		$zot = $seen = array();

		// basically, grab everything that looks like a variable reference.
		// noone gets hurt if we happen to grab too much.
		if (preg_match_all('/\$(\w+)/', $s, $m))
			foreach ($m[1] as $var)
				if (!$seen[$var]++) $zot[] = "'$var' => &\$$var";

		$grabber = join(', ', $zot);

		$escaped = preg_replace("/['\\\\]/", "\\$&", $s);

		$grabber = "return new Sco_Spl_Closure('$escaped', array($grabber));";
		return $grabber;
	}
}
