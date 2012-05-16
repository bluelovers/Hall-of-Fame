<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Char
{

	const FILE_CHAR = 'char.%s';

	function char_file_name($char)
	{
		if ($char && is_string($char))
		{
			$id = (string )$char;
		}
		elseif ($char->Number)
		{
			$id = $char->Number;
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

		return $path.$file;
	}

	function user_path($user)
	{
		if ($user && is_string($user))
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

		$path = USER . $id . '/';

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

		if (preg_match('/'.$m.'/', $path, $match))
		{
			if ($id = $match[1])
			{
				return (string)$id;
			}
		}

		return false;
	}

	function user_id_by_char_file($file)
	{
		$m = self::char_file('%s', '%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)', '.+');

		if (preg_match('/'.$m.'/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string)$id;
			}
		}

		return false;
	}

	function char_id_by_file($file)
	{
		$m = self::char_file_name('%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)');

		if (preg_match('/'.$m.'/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string)$id;
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

	function user_list()
	{
		$list = array();

		foreach(glob(USER . '*', GLOB_ONLYDIR) as $path)
		{
			if ($id = self::user_id_by_path($path))
			{
				$list[$id] = $path;
			}
		}

		return $list;
	}

}
