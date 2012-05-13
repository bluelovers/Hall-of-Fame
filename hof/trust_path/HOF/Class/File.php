<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_File
{

	static $data = array();

	public static function fpclose_all()
	{
		foreach ((array )self::$data as $file => $data)
		{
			self::fpclose($data['fp']);
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

	function fpopen($file, $mode = 'r+')
	{
		$fp = fopen($file, $mode);

		//@stream_encoding($fp, HOF::CHARSET);

		$data['fp'] = $fp;
		$data['file'] = $file;
		$data['lock'] = 0;

		self::$data[] = $data;

		return $fp;
	}

	function &_get_cache_by_file($file, $lock = null)
	{
		foreach (self::$data as &$data)
		{
			if ($data['file'] == $file)
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
	function fplock_file($file, $noExit = false, $autocreate = false)
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
				$fp = $data['fp'];
			}
		}
		else
		{
			$fp = self::fpopen($file, ($autocreate && !file_exists($file)) ? 'w+' : 'r+');
		}

		return self::fplock($fp);
	}

	function is_resource_file($fp)
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

	function fplock($fp, $noExit = false)
	{
		if (!$fp || !self::is_resource_file($fp))
		{
			throw new RuntimeException('File Open Error!!');

			die("Error!");

			return false;
		}

		if ($data = self::_get_cache_by_fp($fp))
		{
			if ($data['lock'] > 0) return $fp;
		}
		else
		{
			$_data = null;
			$data = &$_data;

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

	function fpclose($fp)
	{
		$data = self::_get_cache_by_fp($fp);
		unset($data);

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
			case UNION:
				$ret = @glob($path . '*.dat', $flags);
				break;
				/* */
			case USER:
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

}
