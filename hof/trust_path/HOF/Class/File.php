<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_File
{

	static $data = array();
	static $opened_files = array();

	public static function fpclose_all()
	{
		foreach ((array )self::$data as $key => $data)
		{
			self::fpclose($data['fp'], $key);
		}
	}

	public static function mkdir($pathname)
	{
		if (is_dir($pathname))
		{
			return true;
		}

		return mkdir($pathname, 0705, true);
	}

	function unlink($file, $notrash = false)
	{
		if (!$notrash && self::trash_mode())
		{
			return self::rename($file, BASE_PATH_TRASH . str_replace(BASE_TRUST_PATH, '', $file), true, true);
		}
		else
		{
			return unlink($file);
		}
	}

	function rename($from, $to, $force = false, $noerror = false)
	{
		self::mkdir(dirname($from));
		self::mkdir(dirname($to));

		if ($force)
		{
			if (is_dir($to))
			{
				self::rmdir($to, true, true);
			}
			else
			{
				if ($noerror)
				{
					@unlink($to);
				}
				else
				{
					unlink($to);
				}
			}
		}

		return rename($from, $to);
	}

	public static function trash_mode()
	{
		if (defined('BASE_PATH_TRASH'))
		{
			return true;
		}
	}

	public static function rmdir($pathname, $force = false, $notrash = false)
	{
		if (!is_dir($pathname) || strpos($pathname, BASE_TRUST_PATH) !== 0)
		{
			throw new RuntimeException('Folder Not Exists: '.$pathname);
		}

		if ($force && ($notrash || !self::trash_mode()))
		{
			foreach (scandir($pathname) as $path)
			{
				if ($path == '.' || $path == '..') continue;

				$subpath = $pathname.'/'.$path;

				if (is_dir($subpath))
				{
					self::rmdir($subpath, $force);
				}
				else
				{
					unlink($subpath);
				}
			}
		}

		if (!$notrash && self::trash_mode())
		{
			self::rename($pathname, BASE_PATH_TRASH . str_replace(BASE_TRUST_PATH, '', $pathname), true, true);
		}
		else
		{
			rmdir($pathname);
		}
	}

	function mvdir($path, $path_put)
	{
		$path = rtrim($path, '/').'/';
		$path_put = rtrim($path_put, '/').'/';

		if (!is_dir($path))
		{
			throw new RuntimeException('Folder Not Exists: '.$path);
		}

		self::mkdir($path_put);

		if ($paths = explode('/', rtrim($path, '/')))
		{
			$pathname = end($paths);

			rename($path, $path_put.$pathname);

			return true;
		}
	}

	static function basename($file)
	{
		$file = basename($file);

		$ext = preg_replace('/^.*(\.[^\.]+)$/', '$1', $file);

		if ($ext == $file)
		{
			$ext = '';
			$name = $file;
		}
		else
		{
			$name = basename($file, $ext);
		}

		return array($name, $ext);
	}

	function opened_files_add($file)
	{
		self::$opened_files[str_replace(BASE_PATH, '', $file)]++;
	}

	function fpopen($file, $mode = 'r+')
	{
		$fp = fopen($file, $mode);

		//@stream_encoding($fp, HOF::CHARSET);

		$data['fp'] = $fp;
		$data['file'] = $file;
		$data['lock'] = 0;

		array_push(self::$data, $data);

		self::opened_files_add($file);

		return $fp;
	}

	function &_get_cache_by_file($file, $lock = null)
	{
		foreach (self::$data as $key => &$data)
		{
			if ($data['file'] == $file)
			{
				if (self::is_resource_file($data['fp']))
				{
					$data['key'] = $key;

					if (!$lock || $lock && $data['lock'] > 0)
					{
						return $data;
					}
				}
				else
				{
					@fclose($data['fp']);
					unset($data);
				}
			}
		}

		return null;
	}

	function &_get_cache_by_fp($fp, $lock = null)
	{
		foreach (self::$data as &$data)
		{
			if ($data['fp'] == $fp)
			{
				if (self::is_resource_file($data['fp']))
				{
					if (!$lock || $lock && $data['lock'] > 0)
					{
						return $data;
					}
				}
				else
				{
					@fclose($data['fp']);
					unset($data);
				}
			}
		}

		return null;
	}

	/**
	 * ファイルロックしたファイルポインタを返す。
	 */
	function &fplock_file($file, $noExit = false, $autocreate = false)
	{
		if (!$autocreate && !file_exists($file))
		{
			if (!$noExit)
			{
				throw new RuntimeException('File Not Exists');
			}

			return false;
		}

		if ($data = self::_get_cache_by_file($file))
		{
			if ($data['lock'] > 0)
			{
				return $data['fp'];
			}
			else
			{
				$key = $data['key'];
				$fp = $data['fp'];
			}
		}
		else
		{
			$dir = dirname($file);

			if (!is_dir($dir))
			{
				self::mkdir($dir);
			}

			$fp = self::fpopen($file, ($autocreate && !file_exists($file)) ? 'w+' : 'r+');
		}

		return self::fplock(array($fp, $key), $noExit);
	}

	static function is_resource_file($fp)
	{
		if (is_resource($fp))
		{
			if (get_resource_type($fp) == 'file' || get_resource_type($fp) == 'stream')
			{
				return $fp;
			}
		}

		return false;
	}

	function &fplock($fp, $noExit = false)
	{
		if (is_array($fp) && self::is_resource_file($fp[0]))
		{
			list($fp, $key) = $fp;
		}

		if (!$fp || !self::is_resource_file($fp))
		{
			throw new RuntimeException('File Open Error!!');

			die("Error!");

			return false;
		}

		if ((isset($key) && $data = self::$data[$key]) || $data = self::_get_cache_by_fp($fp))
		{
			if ($data['lock'] > 0) return $fp;

			$key = $data['key'];
		}
		else
		{
			$_data = null;
			$data = &$_data;

			$_array = array();
			$data = $_array;

			self::$data[] = $data;
		}

		$data['fp'] = $fp;
		$data['lock'] = 0;

		$i = 0;
		do
		{
			if (flock($fp, LOCK_EX | LOCK_NB))
			{
				$data['lock'] = 1;

				stream_set_write_buffer($fp, 0);
				return $fp;
			}
			else
			{
				usleep(10000); //0.01秒
				$i++;
			}
		} while ($i < 5);

		if (!$data['lock'])
		{
			$data['lock'] = -1;
		}

		if ($noExit)
		{
			return false;
		}
		else
		{
			ob_clean();

			if ($data['file'])
			{
				$file = basename($data['file']);
			}
			else
			{
				$file = $fp;
			}

			throw new RuntimeException("file lock error. {$file}");

			exit("file lock error. $file");
		}
		//flock($fp, LOCK_EX);//排他
		//flock($fp, LOCK_SH);//共有ロック
		//flock($fp,LOCK_EX);

		return $fp;
	}

	/**
	 * ファイルに書き込む(引数:ファイルポインタ)
	 */
	function fpwrite_file($fp, $text, $check = false)
	{
		if (!$check && !trim($text))
		{
			// $textが空欄なら終わる
			return false;
		}
		/*if(file_exists($file)):
		ftruncate()
		else:
		$fp	= fopen($file,"w+");*/
		ftruncate($fp, 0);
		rewind($fp);
		//$fp	= fopen($file,"w+");
		//flock($fp,LOCK_EX);
		fputs($fp, $text);
		//print("<br>"."<br>".$text);
	}

	/**
	 * ファイルに書き込む
	 */
	function WriteFile($file, $text, $check = false)
	{
		if (!$check && !$text)
		{
			// $textが空欄なら終わる
			return false;
		}
		/*if(file_exists($file)):
		ftruncate()
		else:
		$fp	= fopen($file,"w+");*/

		self::opened_files_add($file);

		$fp = fopen($file, "w+");
		//@stream_encoding($fp, HOF::CHARSET);
		flock($fp, LOCK_EX);
		fputs($fp, $text);
	}

	/**
	 * ファイルを読んで配列に格納(引数:ファイルポインタ)
	 */
	function ParseFileFP($fp)
	{
		if (!$fp) return false;
		while (!feof($fp))
		{
			$str = fgets($fp);
			$str = trim($str);
			if (!$str) continue;
			$pos = strpos($str, "=");
			if ($pos === false) continue;
			$key = substr($str, 0, $pos);
			$val = substr($str, ++$pos);
			$data[$key] = trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if ($data) return $data;
		else  return false;
	}

	/**
	 * ファイルを読んで配列に格納
	 */
	function ParseFile($file)
	{
		self::opened_files_add($file);

		$fp = @fopen($file, "r+");
		if (!$fp) return false;
		flock($fp, LOCK_EX | LOCK_NB);
		//@stream_encoding($fp, HOF::CHARSET);
		while (!feof($fp))
		{
			$str = fgets($fp);
			$str = trim($str);
			if (!$str) continue;
			$pos = strpos($str, "=");
			if ($pos === false) continue;
			$key = substr($str, 0, $pos);
			$val = substr($str, ++$pos);
			$data[$key] = trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if ($data) return $data;
		else  return false;
	}

	function fpclose($fp, $key = null)
	{
		if ($key !== null)
		{
			unset(self::$data[$key]);
		}
		else
		{
			$data = self::_get_cache_by_fp($fp);
			unset($data);
		}

		@fclose($fp);
	}

	function glob($path, $flags = 0)
	{
		$ret = array();

		switch ($path)
		{
				/* */
			case LOG_BATTLE_NORMAL:
			case LOG_BATTLE_RANK:
			case LOG_BATTLE_UNION:
				/* */
			case BASE_PATH_UNION:
				$ret = @glob($path . '*.dat', $flags);
				break;
				/* */
			case BASE_PATH_USER:
				$ret = @glob($path . '*', $flags | GLOB_ONLYDIR);
				break;
				/* */
			default:
				$ret = @glob($path, $flags);
				break;
		}

		return $ret;
	}

	function glob_del($path)
	{
		if ($ret = self::glob($path))
		{
			foreach ($ret as $file)
			{
				unlink($file);
			}
		}
	}

	function fp_get_contents($fp)
	{
		rewind($fp);
		return stream_get_contents($fp);
	}

	function fpunlock($fp)
	{
		if (self::is_resource_file($fp))
		{
			return flock($fp, LOCK_UN);
		}
	}

}
