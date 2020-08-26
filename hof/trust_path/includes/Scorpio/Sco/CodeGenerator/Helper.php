<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_CodeGenerator_Helper
{

	static $tab2space = 4;

	public static function tab2space($s, $n = 4)
	{
		self::$tab2space = $n;

		return preg_replace_callback('/^\t+|\t+$/m', array('Sco_CodeGenerator_Helper', '_tab2space'), (string )$s);
	}

	protected static function _tab2space($matches)
	{
		return str_replace(TAB, str_repeat(SPACE, self::$tab2space), $matches[0]);
	}

	public static function space2tab($s, $n = 4)
	{
		self::$tab2space = $n;

		return preg_replace_callback('/^ +| +$/m', array('Sco_CodeGenerator_Helper', '_space2tab'), (string )$s);
	}

	protected static function _space2tab($matches)
	{
		return str_replace(str_repeat(SPACE, self::$tab2space), TAB, $matches[0]);
	}

}
