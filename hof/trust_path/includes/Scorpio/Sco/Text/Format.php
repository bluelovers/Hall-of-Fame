<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Text_Format
{

	//const REGEX_PRINTF = '/(?<!%)(?<fultext>%+(?:\((?<varname>[a-zA-Z_]\w*)\))?(?<type>\-?[a-zA-Z\d\.]+|%))/';
	//const REGEX_PRINTF = '/(?<!%)(?<fultext>%+(?:\((?<varname>[a-zA-Z_]\w*)\))?(?<varname2>\d+\$)?(?<pad>[ 0]|\'.)?(?<type>[+\-]?[a-zA-Z\d\.]+|%))/';
	//const REGEX_PRINTF = '/(?<!%)(?<fultext>%+(?:\((?<varname>[a-zA-Z_]\w*)\))?(?<varname2>\d+\$)?(?:[ 0]|\'.)?(?:[+\-]?[\d\.]+)?[bcdeEufFgGosxX])/';
	const REGEX_PRINTF = '/(?<!%)(?:%%)*(?<fultext>%(?:\((?<varname>[a-zA-Z_][a-zA-Z_0-9]*)\))?(?<varname2>\d+\$)?(?:[ 0]|\'.)?(?:[+\-]?[\d\.]+)?[bcdeEufFgGosxX])/';
	const REGEX_PRINTF2 = '/(?<!%)(?:%%)*(%(?:(?:\(([a-zA-Z_][a-zA-Z_0-9]*)\))?(\d+\$)?((?:(?:[ 0]|\'.)?[+\-]?[\d\.]+)?[bcdeEufFgGosxX])|[\(\)\$\d\|\\\\\/]))/';

	const TYPE_SPECIFIER = 'bcdeEufFgGosxX';

	const LOSTARGV_VISIBLE = 1;
	const LOSTARGV_PAD = 2;

	/**
	 * Specal Options for handle php bug?
	 */
	const LOSTARGV_PLUS = 4;
	const LOSTARGV_PLUS2 = 8;

	const ERROR_DEBUG = 2;
	const ERROR_WARNING = 4;

	const FORCE_MODE = 1;
	const FORCE_MODE_ALL = 2;

	public static $_suppressArgvWarnings = 0;
	public static $_handleLostArgv = 9;

	public static $_forceMode = false;
	public static $_matchMode = 2;

	public static function suppressArgvWarnings($flag = null)
	{
		if (null === $flag)
		{
			return self::$_suppressArgvWarnings;
		}

		$old = self::$_suppressArgvWarnings;

		self::$_suppressArgvWarnings = $flag;

		return $old;
	}

	public static function handleLostArgv($flag = null)
	{
		if (null === $flag)
		{
			return self::$_handleLostArgv;
		}

		$old = self::$_handleLostArgv;

		self::$_handleLostArgv = $flag;

		return $old;
	}

	public static function forceMode($flag = null)
	{
		if (null === $flag)
		{
			return self::$_forceMode;
		}

		$old = self::$_forceMode;

		self::$_forceMode = $flag;

		return $old;
	}

	public static function matchMode($flag = null)
	{
		if (null === $flag)
		{
			return self::$_matchMode;
		}

		$old = self::$_matchMode;

		self::$_matchMode = $flag;

		return $old;
	}

	/**
	 * python like syntax format
	 * Returns a string produced according to the formatting string format .
	 *
	 * @param string $format
	 * @param array $args
	 * @param mixed $args, mixed $...
	 *
	 * @see http://tw2.php.net/manual/en/function.sprintf.php#94608
	 * @see http://tw2.php.net/manual/en/function.vsprintf.php#89349
	 * @see http://www.php.net/manual/en/function.sprintf.php#93552
	 * @see http://archive.plugins.jquery.com/project/printf
	 *
	 * @example test script
	 * echo '<pre>';
	 *
	 * echo vsprintf('[%-20s] [%20s] %.3f %(num).3f %%s %%%s %%%s%% Hello, %(place)s, how is it hanning at %(place)s? %s works just as well %(name)s: %(value)d %s %d%% %.3f',
	 * array('place' => 'world333', 'sprintf', 'not used', 'num' => 'world666',
	 * 'sprintf', 'not used', 'name' => 'world999', 'sprintf', 'not used', 'value' =>
	 * 'world', 'sprintf', 'not used', 'sprintf', 'not used', 'sprintf', 'not used',
	 * 'sprintf', 'not used', ));
	 * echo "\n";
	 * echo self::sprintf("[%(test1)-20s] [%(test1)20s] [%(test1)020s] [%(test1)'#20s] [%(test1)20.20s]
	 * [%(test2)-20s] [%(test2)20s] [%(test2)020s] [%(test2)'#20s] [%(test2)20.20s]
	 * [%(test3)-20s] [%(test3)20s] [%(test3)020s] [%(test3)'#20s] [%(test3)20.20s]
	 *
	 * [%(test3)20.3s] [%(test3)20.1s] [%(test3)20.5s]
	 *
	 * \n%.3f %(num).3f %%s %%(value)s %(value)s %%%s %%%s%%  %%%%%s%%%% Hello, %(place)s, how is it hanning at %(place)s? %s works just as well %(name)s: %(value)d %s %d%% %.3f",
	 * array('test1' => 'escrzyaie', 'test2' => 'ěščřžýáíé', 'test3' => '姫とボイン',
	 * 'place' => 'world', 'sprintf', 'not used', 'name' => 9999, 'num' =>
	 * 645321.123456));
	 */
	public static function vsprintf($format, $args)
	{
		$args && $args = (array )$args;

		if (self::$_forceMode ^ self::FORCE_MODE_ALL || (self::$_forceMode & self::FORCE_MODE_ALL) && strpos($format, '%') || strpos($format, '%(') !== false)
		{
			if (self::$_matchMode & 2)
			{
				//printf('matchMode: %d'.NL, 2);

				self::_printf_match2($format, $matchs, $args);
			}
			else
			{
				//printf('matchMode: %d'.NL, 1);

				self::_printf_match($format, $matchs, $args);
			}
		}

		if (self::$_suppressArgvWarnings ^ self::ERROR_DEBUG)
		{
			return @vsprintf($format, $args);
		}
		else
		{
			return vsprintf($format, $args);
		}
	}

	/**
	 * python like syntax format
	 * Returns a string produced according to the formatting string format .
	 *
	 * @param string $format
	 * @param array $args
	 * @param mixed $args, mixed $...
	 */
	public static function sprintf($format, $args = null)
	{
		return self::vsprintf($format, array_slice(func_get_args(), 1));
	}

	protected static function _printf_match(&$format, &$matchs, &$args)
	{
		if (preg_match_all(self::REGEX_PRINTF, $format, $matchs))
		{
			self::_printf_filter($format, $matchs, $args);
		}
	}

	protected static function _printf_match2(&$format, &$matchs, &$args)
	{
		$arg_nums = array_flip(array_keys($args));

		$pos = 0;
		$cache = array();

		$idx = 0;

		//var_dump($format, $arg_nums, $args);

		while (preg_match(self::REGEX_PRINTF2, $format, $match, PREG_OFFSET_CAPTURE, $pos))
		{
			$arg_pos = $match[1][1];
			$arg_len = $match[4][1] - $match[1][1] - 1;

			$replace = '';

			if (empty($match[4][0]))
			{
				//var_dump(777, self::$_handleLostArgv & self::LOSTARGV_PLUS, self::$_handleLostArgv & self::LOSTARGV_PLUS2);

				if (self::$_handleLostArgv & self::LOSTARGV_PLUS2)
				{
					$format = substr_replace($format, $replace = '%%', $arg_pos, 1);
				}
				elseif (self::$_handleLostArgv & self::LOSTARGV_PLUS)
				{
					//var_dump($match, $format, $replace = $match[1][0][1], $arg_pos, $arg_len);

					$format = substr_replace($format, $replace = $match[0][0][1], $arg_pos, 2);

					//var_dump('------------------', $format, $replace = $match[1][0][1], $arg_pos, $arg_len);
				}
				else
				{
					$idx++;

					$pos = $arg_pos + 2;
					continue;
				}
			}
			elseif ($match[3][1] > -1)
			{
				$format = substr_replace($format, $replace = $match[3][0], $arg_pos + 1, $arg_len);
			}
			elseif ($match[2][1] > -1)
			{
				$arg_key = $match[2][0];

				if (array_key_exists($arg_key, $arg_nums))
				{
					$k = $arg_nums[$arg_key];
				}
				elseif (self::$_handleLostArgv & self::LOSTARGV_PAD)
				{
					$k = count($arg_nums);
					$arg_nums[$arg_key] = $k;
				}
				elseif (self::$_handleLostArgv & self::LOSTARGV_VISIBLE)
				{
					$replace = '%(' . $arg_key . ')';
				}
				else
				{
					$replace = null;
				}

				//var_dump(self::$_handleLostArgv & self::LOSTARGV_VISIBLE, self::$_handleLostArgv & self::LOSTARGV_PAD);
				//exit();

				$replace !== null && $format = substr_replace($format, ($replace = $replace ? $replace : ($k + 1) . '$'), $arg_pos + 1, $arg_len);
			}
			else
			{
				$idx++;

				if ($idx > count($arg_nums) && self::$_handleLostArgv)
				{
					if (self::$_handleLostArgv & self::LOSTARGV_PAD)
					{
						$arg_nums[$idx] = $idx;
					}
					elseif (self::$_handleLostArgv & self::LOSTARGV_VISIBLE)
					{
						$format = substr_replace($format, $replace = '%%', $arg_pos, 1);
					}
				}
			}

			$pos = $arg_pos + strlen($replace) + strlen($match[4][0]); // skip to end of replacement for next iteration

			//var_dump($format);
			//var_dump($match[1][0], $replace);

			//var_dump($match);
			//var_dump($pos, $replace);
			//var_dump($pos, $arg_pos, $arg_len, $arg_key, $arg_idx);
		}

		//array_shift($arg_nums);
		//var_dump($cache, $arg_nums);

		//var_dump($format, $arg_nums, $args, $idx);
		//exit;

		if (($k = max(count($arg_nums), $idx)) > ($pos = count($args)))
		{

			//var_dump(__LINE__, self::$_suppressArgvWarnings, $k, $pos);

			if (self::$_suppressArgvWarnings)
			{
				if ((self::$_handleLostArgv & self::LOSTARGV_PAD) || $idx > $pos)
				{
					$args = array_pad($args, $k, null);
				}
			}

			if (!self::$_suppressArgvWarnings || self::$_suppressArgvWarnings & self::ERROR_WARNING)
			{
				$err = sprintf('%s(): Too few arguments [ %d:%d ] or lost argument key [ %s ]', __METHOD__, $pos, $k, implode(', ', array_slice(array_keys($arg_nums), $pos)));

				if (self::$_suppressArgvWarnings & self::ERROR_WARNING)
				{
					//echo $err;
					trigger_error($err, E_USER_WARNING);
					//trigger_error($err);
				}
				else
				{
					throw new InvalidArgumentException('Warning: ' . $err);
				}
			}
		}
	}

	protected static function _printf_filter(&$format, &$matchs, &$args)
	{
		//$data = array();
		$k2 = $strtr = array();
		$_lost_args = false;

		$k = 0;
		$count = count($matchs['fultext']);

		$keys = array_flip(array_keys($args));

		for ($i = 0; $i < $count; $i++)
		{
			$fulltext = (string )$matchs['fultext'][$i];

			if (strpos(str_replace('%%', '', $fulltext), '%') !== 0)
			{
				continue;
			}

			$varname = (string )$matchs['varname'][$i];

			/*
			$data['fultext'][] = $fulltext;
			$data['varname'][] = $varname;
			$data['type'][] = $matchs['type'][$i];
			*/

			if ($varname)
			{
				if (array_key_exists($varname, $keys))
				{
					$strtr[$fulltext] = str_replace('(' . $varname . ')', ($keys[$varname] + 1) . '$', str_replace($matchs['varname2'][$i], '', $fulltext));
					$k2[] = ($keys[$varname] + 1) . '$';
				}
				elseif (self::$_handleLostArgv & self::LOSTARGV_PAD)
				{
					$k2[] = ++$k . '$';
					$strtr[$fulltext] = str_replace('(' . $varname . ')', $k . '$', $fulltext);
					$_lost_args[] = $varname;
				}
				else
				{
					$strtr[$fulltext] = '%' . $fulltext;
					$_lost_args[] = $varname;
				}
			}
			elseif ($matchs['varname2'][$i])
			{
				$k2[] = $matchs['varname2'][$i];
			}
			else
			{
				$k2[] = ++$k . '$';
				//$_args[$k] = reset(array_slice($args, $k, 1, true));
			}
		}

		if ($_lost_args || $k2)
		{
			$k2 && $k2 = array_unique($k2);

			if (self::$_suppressArgvWarnings)
			{
				$args = array_pad((array )$args, count((array )$k2), null);
			}
			else
			{
				//var_dump($matchs['fultext'], $args, $k2, $_lost_args, $strtr, implode(', ', (array)$_lost_args));

				throw new InvalidArgumentException(sprintf('Warning: %s(): Too few arguments [ %d ] or lost argument key [ %s ]', __METHOD__, count((array )$k2), implode(', ', (array )$_lost_args)));
			}
		}

		$strtr && $format = strtr($format, $strtr);

		//$args = $_args;

		//var_dump($matchs['fultext'], $args, $k2, $strtr);

		return true;
	}

	public static function sprintf_quote($string, $remove = false)
	{
		return $string = $remove ? str_replace('%%', '%', $string) : str_replace('%', '%%', $string);
	}

	protected static function sprintf_parse($format)
	{
		preg_match('/^(?<pre>%+)?%(?<pad>\'(?<pad2>.)|(?<pad3>[0-9]))?(?<sign>-|\+)?(?<size>[1-9][0-9]*)(?:\.(?<size2>\d+))?(?<type>[a-zA-Z])$/', $format, $match);

		return $match;
	}

	/**
	 * Returns human readable sizes. Based on original functions written by
	 * [Aidan Lister](http://aidanlister.com/repos/v/function.size_readable.php)
	 * and [Quentin Zervaas](http://www.phpriot.com/d/code/strings/filesize-format/).
	 *
	 *		echo scotext::bytes(filesize($file));
	 *
	 * @param		integer  size in bytes
	 * @param		string   a definitive unit
	 * @param		string   the return string format
	 * @param		boolean  whether to use SI prefixes or IEC
	 * @return		string
	 *
	 * @author		Kohana Team
	 * @copyright	(c) 2007-2011 Kohana Team
	 */
	public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{
		// Format string
		$format = ($format === NULL) ? '%01.2f %s' : (string )$format;

		// IEC prefixes (binary)
		if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
		{
			$units = array(
				'B',
				'KiB',
				'MiB',
				'GiB',
				'TiB',
				'PiB');
			$mod = 1024;
		}
		// SI prefixes (decimal)
		else
		{
			$units = array(
				'B',
				'kB',
				'MB',
				'GB',
				'TB',
				'PB');
			$mod = 1000;
		}

		// Determine unit to use
		if (($power = array_search((string )$force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}

	/**
	 * Parses input from a string according to a format
	 *
	 * @see http://www.php.net/manual/zh/function.sscanf.php
	 */
	function vsscanf($str, $format, array $keys)
	{
		$array = array();
		$return = sscanf($str, $format);

		while ($return)
		{
			$key = array_shift($keys);

			if ($key === null)
			{
				$array[] = array_shift($return);
			}
			elseif (array_key_exists($key, $array))
			{
				trigger_error(sprintf('Cannot redeclare array key \'%s\' previously declared', $key), E_USER_WARNING);
			}
			else
			{
				$array[$key] = array_shift($return);
			}
		}

		while ($keys)
		{
			$key = array_shift($keys);

			if (array_key_exists($key, $array))
			{
				trigger_error(sprintf('Cannot redeclare array key \'%s\' previously declared', $key), E_USER_WARNING);
			}
			else
			{
				$array[$key] = null;
			}
		}

		return $array;
	}

}
