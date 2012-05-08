<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_File
{

	static $data = array();

	public static function fpCloseAll()
	{
		foreach ((array)self::$data as $file => $data)
		{
			self::fileClose($data['fp']);
		}
	}

	function basename($file)
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

	function _get_cache_fp($file, $lock = null)
	{
		$cache = self::$data[$file];

		if (
			is_resource($cache['fp'])
			&& (!$lock || ($lock && $cache['lock']))
		)
		{
			return self::$data[$file]['fp'];
		}

		return false;
	}

	/**
	 * ファイルロックしたファイルポインタを返す。
	 */
	function FileLock($file, $noExit = false)
	{
		if (!file_exists($file)) return false;

		if ($fp = self::_get_cache_fp($file, 1))
		{
			return $fp;
		}

		$fp = @fopen($file, "r+") or die("Error!");
		if (!$fp) return false;

		self::$data[$file] = array(
			'fp' => $fp,
			'file' => $file,
		);

		self::$data[$file]['lock'] = 0;

		$i = 0;
		do
		{
			if (flock($fp, LOCK_EX | LOCK_NB))
			{
				self::$data[$file]['lock'] = 1;

				stream_set_write_buffer($fp, 0);
				return $fp;
			}
			else
			{
				usleep(10000); //0.01秒
				$i++;
			}
		} while ($i < 5);

		if (!self::$data[$file]['lock'])
		{
			self::$data[$file]['lock'] = -1;
		}

		if ($noExit)
		{
			return false;
		}
		else
		{
			ob_clean();
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
	function WriteFileFP($fp, $text, $check = false)
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

	function &_findFp($fp)
	{
		foreach (self::$data as &$data)
		{
			if ($data['fp'] == $fp)
			{
				return $data;
			}
		}

		return false;
	}

	function fileClose($fp)
	{
		$data = self::_findFp($fp);
		$data['fclose'] = true;

		if (is_resource($fp))
		{
			@fclose($fp);
		}
	}

}
