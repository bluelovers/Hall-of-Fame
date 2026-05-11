<?php

/**
 * 角色輔助類別
 * Character helper class
 *
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Helper_Char
{
	/** 角色檔案名稱格式 / Character file name format */
	const FILE_CHAR = 'char.%s';

	/** 角色名稱最大長度 / Maximum character name length */
	const NAME_MAX = 16;
	/** 角色名稱最小長度 / Minimum character name length */
	const NAME_MIN = 2;
	/** 角色名稱最小字元數（特殊類型） / Minimum character name length (special type) */
	const NAME_MIN_CHAR = 1;

	/**
	 * 產生唯一識別碼（基於使用者 ID 和 IP）
	 * Generate unique identifier (based on user ID and IP)
	 *
	 * @param mixed $seed - 產生種子 / Seed for generation
	 * @return string MD5 哈希值 / MD5 hash value
	 */
	static function uniqid($seed = null)
	{
		static $uuid;
		if (!isset($uuid)) $uuid = md5(HOF::user()->id.HOF::ip());

		return md5(uniqid($uuid . $seed, true));
	}

	/**
	 * 產生出生時間戳記
	 * Generate birth timestamp
	 *
	 * @param mixed $t - 時間參數 / Time parameter
	 * @return string 組合後的時間字串 / Combined time string
	 */
	function uniqid_birth($t = null)
	{
		// time() . substr(microtime(), 2, 6);

		if (!$t) $t = HOF_Helper_Date::microtime();

		return $t[1].$t[0];
	}

	/**
	 * パスワードを暗号化する
	 * 密碼加密函式
	 * Password encryption function
	 */
	function CryptPassword($pass)
	{
		return HOF_Class_Crypto_HOF::newInstance(CRYPT_KEY)->encode($pass);
	}

	/**
	 * 檢查值是否為有效 ID
	 * Check if value is valid ID
	 *
	 * @param mixed $val - 待檢查的值 / Value to check
	 * @return bool 是否為有效 ID / Whether is valid ID
	 */
	function _is_id($val)
	{
		return (bool)(is_string($val) || is_numeric($val));
	}

	/**
	 * 檢查並清理角色名稱是否允許使用
	 * Check and clean character name for allowed use
	 *
	 * @param string $name - 角色名稱 / Character name
	 * @param int $type - 名稱類型（0=普通，其他=特殊） / Name type (0=normal, other=special)
	 * @return mixed 清理後的名稱或 false / Cleaned name or false
	 */
	function char_is_allow_name($name, $type = 0)
	{
		/**
		 * 移除名稱中的特殊字元和空白
		 * Remove special characters and spaces from name
		 */
		$name = stripslashes($name);

		/**
		 * 檢查並移除禁止字元（制表符、換行符、小於、大於等）
		 * Check and remove forbidden characters (tabs, newlines, <, >, etc.)
		 */
		if (preg_match('/([\t\r\n\<\>]+)/', $name))
		{
			$name = '';

			return false;
		}

		/**
		 * 清理前後引號和空白，並將多個空白替換為單一空白
		 * Clean quotes and spaces from beginning/end, replace multiple spaces with single space
		 */
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

		/**
		 * 檢查名稱長度是否符合要求
		 * Check if name length meets requirements
		 */
		if (!empty($name) && $len >= $min && $len <= $max)
		{
			return $name;
		}

		return false;
	}

	/**
	 * 取得角色檔案名稱
	 * Get character file name
	 *
	 * @param mixed $char - 角色物件或 ID / Character object or ID
	 * @return string 檔案名稱 / File name
	 * @throws InvalidArgumentException 當角色為空時 / When character is null
	 */
	function char_file_name($char)
	{
		/**
		 * 從不同來源取得角色 ID
		 * Get character ID from different sources
		 */
		if ($char && self::_is_id($char))
		{
			$id = (string )$char;
		}
		elseif ($char->id)
		{
			$id = $char->id;
		}
		elseif ($char->uniqid())
		{
			$id = $char->uniqid();
		}
		else
		{
			throw new InvalidArgumentException("Char Null.");
		}

		$file = sprintf(self::FILE_CHAR . BASE_EXT, $id);

		return $file;
	}

	/**
	 * 取得使用者檔案完整路徑
	 * Get user file full path
	 *
	 * @param mixed $user - 使用者物件或 ID / User object or ID
	 * @param string $file - 檔案名稱 / File name
	 * @return string 完整檔案路徑 / Full file path
	 */
	function user_file($user, $file)
	{
		$path = self::user_path($user);

		$file = $file;

		return $path . $file;
	}

	/**
	 * 取得使用者目錄路徑
	 * Get user directory path
	 *
	 * @param mixed $user - 使用者物件或 ID / User object or ID
	 * @return string 目錄路徑 / Directory path
	 * @throws InvalidArgumentException 當使用者為空時 / When user is null
	 */
	function user_path($user)
	{
		/**
		 * 從不同來源取得使用者 ID
		 * Get user ID from different sources
		 */
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

	/**
	 * 取得角色檔案完整路徑
	 * Get character file full path
	 *
	 * @param mixed $char - 角色物件或 ID / Character object or ID
	 * @param mixed $user - 使用者物件或 ID / User object or ID
	 * @return string 完整檔案路徑 / Full file path
	 */
	function char_file($char, $user)
	{
		$file = self::char_file_name($char);
		$path = self::user_path($user);

		return $path . $file;
	}

	/**
	 * 從路徑中提取使用者 ID
	 * Extract user ID from path
	 *
	 * @param string $path - 目錄路徑 / Directory path
	 * @return string|null 使用者 ID 或 null / User ID or null
	 */
	function user_id_by_path($path)
	{
		/**
		 * 建立路徑匹配模式
		 * Build path matching pattern
		 */
		$m = self::user_path('%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)');

		$path = rtrim($path, '/') . '/';

		/**
		 * 使用正則表達式匹配路徑中的 ID
		 * Use regex to match ID in path
		 */
		if (preg_match('/' . $m . '/', $path, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	/**
	 * 從角色檔案路徑中提取使用者 ID
	 * Extract user ID from character file path
	 *
	 * @param string $file - 角色檔案路徑 / Character file path
	 * @return string|null 使用者 ID 或 null / User ID or null
	 */
	function user_id_by_char_file($file)
	{
		/**
		 * 建立角色檔案匹配模式
		 * Build character file matching pattern
		 */
		$m = self::char_file('%s', '%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)', '.+');

		/**
		 * 使用正則表達式匹配檔案路徑中的 ID
		 * Use regex to match ID in file path
		 */
		if (preg_match('/' . $m . '/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	/**
	 * 從角色檔案名稱中提取角色 ID
	 * Extract character ID from character file name
	 *
	 * @param string $file - 角色檔案名稱 / Character file name
	 * @return string|null 角色 ID 或 null / Character ID or null
	 */
	function char_id_by_file($file)
	{
		/**
		 * 建立角色檔案名稱匹配模式
		 * Build character file name matching pattern
		 */
		$m = self::char_file_name('%s');

		$m = preg_quote($m, '/');

		$m = sprintf($m, '([0-9a-zA-Z]+)');

		/**
		 * 使用正則表達式匹配檔案名稱中的 ID
		 * Use regex to match ID in file name
		 */
		if (preg_match('/' . $m . '/', $file, $match))
		{
			if ($id = $match[1])
			{
				return (string )$id;
			}
		}

		return false;
	}

	/**
	 * 取得使用者的所有角色列表
	 * Get all character list for user
	 *
	 * @param mixed $user - 使用者物件或 ID / User object or ID
	 * @return array 角色列表（ID => 檔案路徑） / Character list (ID => file path)
	 */
	function char_list_by_user($user)
	{
		/**
		 * 建立角色檔案搜尋模式
		 * Build character file search pattern
		 */
		$p = self::char_file('*', $user);

		$list = array();

		/**
		 * 使用 glob 搜尋所有角色檔案並提取 ID
		 * Use glob to search all character files and extract IDs
		 */
		foreach (glob($p) as $file)
		{
			if ($no = self::char_id_by_file($file))
			{
				$list[$no] = $file;
			}
		}

		return $list;
	}

	/**
	 * 取得所有使用者列表
	 * Get all user list
	 *
	 * @param bool $all - 是否包含無效目錄 / Whether include invalid directories
	 * @return array 使用者列表 / User list
	 */
	function user_list($all = false)
	{
		/**
		 * 分類儲存有效和無效的使用者目錄
		 * Store valid and invalid user directories separately
		 */
		$list = array(array(), array());

		/**
		 * 搜尋所有使用者目錄
		 * Search all user directories
		 */
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
