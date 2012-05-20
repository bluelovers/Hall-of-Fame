<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Char
{

	const FILE_CHAR = 'char.%s';

	const NAME_MAX = 16;
	const NAME_MIN = 2;
	const NAME_MIN_CHAR = 1;

	static function uniqid($seed = null)
	{
		static $uuid;
		if (!isset($uuid)) $uid = md5(HOF::user()->id.HOF::ip());

		return md5(uniqid($uuid . $seed, true));
	}

	function uniqid_birth($t = null)
	{
		// time() . substr(microtime(), 2, 6);

		if (!$t) $t = HOF_Helper_Date::microtime();

		return $t[1].$t[0];
	}

	/**
	 * パスワードを暗号化する
	 */
	function CryptPassword($pass)
	{
		return HOF_Class_Crypto_HOF::newInstance(CRYPT_KEY)->encode($pass);
	}

	function _is_id($val)
	{
		return (bool)(is_string($val) || is_numeric($val));
	}

	function char_is_allow_name($name, $type = 0)
	{
		$name = stripslashes($name);

		if (preg_match('/([\t\r\n\<\>]+)/', $name))
		{
			$name = '';

			return false;
		}

		$name = preg_replace('/^["\'\s\t\r\n]+|[\s\t\r\n"\']+$/', '', $name);
		$name = preg_replace('/\s\s+?/', ' ', $name);

		$name = trim($name, '/\\');

		$len = function_exists('mb_strlen') ? mb_strlen($name) : strlen($name);

		$max = self::NAME_MAX;
		$min = self::NAME_MIN;

		if ($type != 0)
		{
			$min = self::NAME_MIN_CHAR;
		}

		$name = htmlspecialchars($name, ENT_QUOTES);

		if (!empty($name) && $len >= $min && $len <= $max)
		{
			return $name;
		}

		return false;
	}

	function char_file_name($char)
	{
		if ($char && self::_is_id($char))
		{
			$id = (string )$char;
		}
		elseif ($char->id)
		{
			$id = $char->id;
		}
		elseif ($char->birth)
		{
			$id = $char->birth;
		}
		else
		{
			throw new InvalidArgumentException("Char Null.");
		}

		$file = sprintf(self::FILE_CHAR . BASE_EXT, $id);

		return $file;
	}

	function user_file($user, $file)
	{
		$path = self::user_path($user);

		$file = $file;

		return $path . $file;
	}

	function user_path($user)
	{
		if ($user && self::_is_id($user))
		{
			$id = (string )$user;
		}
		elseif ($user->id)
		{
			$id = $user->id;
		}
		else
		{
			throw new InvalidArgumentException("User Null.");
		}

		$path = BASE_PATH_USER . $id . '/';

		return $path;
	}

	function char_file($char, $user)
	{
		$file = self::char_file_name($char);
		$path = self::user_path($user);

		return $path . $file;
	}

	function user_id_by_path($path)
	{
		$m = self::user_path('%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)');

		$path = rtrim($path, '/') . '/';

		if (preg_match('/' . $m . '/', $path, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	function user_id_by_char_file($file)
	{
		$m = self::char_file('%s', '%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)', '.+');

		if (preg_match('/' . $m . '/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	function char_id_by_file($file)
	{
		$m = self::char_file_name('%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)');

		if (preg_match('/' . $m . '/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	function char_list_by_user($user)
	{
		$p = self::char_file('*', $user);

		$list = array();

		foreach (glob($p) as $file)
		{
			if ($no = self::char_id_by_file($file))
			{
				$list[$no] = $file;
			}
		}

		return $list;
	}

	function user_list($all = false)
	{
		$list = array(array(), array());

		foreach (glob(BASE_PATH_USER . '*', GLOB_ONLYDIR) as $path)
		{
			if ($id = self::user_id_by_path($path))
			{
				$list[0][$id] = $path;
			}
			else
			{
				$list[1][] = $path;
			}
		}

		return ($all) ? $list : $list[0];
	}

}
