<?php

/**
 * Path 處理輔助類別
 * Path handling helper class
 *
 * 參考 Node.js path 模組實作
 * Reference Node.js path module implementation
 *
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Helper_Path
{
	/**
	 * 路徑分隔符號
	 * Path separator
	 *
	 * @var string
	 */
	public static $sep = DIRECTORY_SEPARATOR;

	/**
	 * 連接多個路徑片段
	 * Join multiple path segments
	 *
	 * @param string $path1 第一個路徑片段
	 * @param string ...$paths 其他路徑片段
	 * @return string 連接後的路徑
	 */
	public static function join()
	{
		$args = func_get_args();
		$result = '';

		foreach ($args as $path)
		{
			if ($path === '')
			{
				continue;
			}

			$path = (string) $path;

			if ($result === '')
			{
				$result = $path;
			}
			else
			{
				// 移除結尾的分隔符
				$result = rtrim($result, self::$sep);
				// 移除開頭的分隔符
				$path = ltrim($path, self::$sep);
				$result .= self::$sep . $path;
			}
		}

		return $result;
	}

	/**
	 * 解析路徑為絕對路徑
	 * Resolve to absolute path
	 *
	 * @param string $path 要解析的路徑
	 * @return string 絕對路徑
	 */
	public static function resolve()
	{
		$args = func_get_args();
		$resolved = '';

		foreach ($args as $path)
		{
			$path = (string) $path;

			if ($path === '')
			{
				continue;
			}

			// 檢查是否為絕對路徑
			if (self::isAbsolute($path))
			{
				$resolved = $path;
			}
			else
			{
				if ($resolved === '')
				{
					$resolved = $path;
				}
				else
				{
					$resolved = rtrim($resolved, self::$sep) . self::$sep . $path;
				}
			}
		}

		// 如果沒有解析出路徑，使用當前目錄
		if ($resolved === '')
		{
			$resolved = '.';
		}

		// 標準化路徑
		return self::normalize($resolved);
	}

	/**
	 * 標準化路徑
	 * Normalize path
	 *
	 * @param string $path 要標準化的路徑
	 * @return string 標準化後的路徑
	 */
	public static function normalize($path)
	{
		$path = (string) $path;

		// 記錄是否為絕對路徑
		$isAbs = self::isAbsolute($path);

		// 處理 . 和 ..
		$parts = explode(self::$sep, $path);
		$result = array();

		foreach ($parts as $part)
		{
			if ($part === '' || $part === '.')
			{
				continue;
			}
			elseif ($part === '..')
			{
				if (!empty($result))
				{
					array_pop($result);
				}
			}
			else
			{
				$result[] = $part;
			}
		}

		$normalized = implode(self::$sep, $result);

		// 保留絕對路徑的前導分隔符
		if ($isAbs && $normalized !== '' && strpos($normalized, self::$sep) !== 0)
		{
			$normalized = self::$sep . $normalized;
		}

		return $normalized;
	}

	/**
	 * 檢查是否為絕對路徑
	 * Check if path is absolute
	 *
	 * @param string $path 要檢查的路徑
	 * @return bool 是否為絕對路徑
	 */
	public static function isAbsolute($path)
	{
		$path = (string) $path;

		// Windows 風格絕對路徑 (C:\)
		if (preg_match('/^[A-Za-z]:' . preg_quote(self::$sep, '/') . '/', $path))
		{
			return true;
		}

		// Unix 風格絕對路徑 (/)
		if (strpos($path, self::$sep) === 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * 取得路徑的目錄名稱
	 * Get directory name from path
	 *
	 * @param string $path 路徑
	 * @return string 目錄名稱
	 */
	public static function dirname($path)
	{
		$path = (string) $path;
		$pos = strrpos($path, self::$sep);

		if ($pos === false)
		{
			return '.';
		}

		return substr($path, 0, $pos);
	}

	/**
	 * 取得路徑的檔案名稱
	 * Get basename from path
	 *
	 * @param string $path 路徑
	 * @param string $ext 可選的副檔名移除
	 * @return string 檔案名稱
	 */
	public static function basename($path, $ext = '')
	{
		$path = (string) $path;
		$pos = strrpos($path, self::$sep);

		if ($pos === false)
		{
			$filename = $path;
		}
		else
		{
			$filename = substr($path, $pos + 1);
		}

		if ($ext !== '' && strlen($filename) > strlen($ext))
		{
			if (substr($filename, -strlen($ext)) === $ext)
			{
				$filename = substr($filename, 0, -strlen($ext));
			}
		}

		return $filename;
	}
}