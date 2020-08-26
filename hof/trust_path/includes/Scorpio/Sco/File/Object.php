<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_File_Object extends SplFileObject
{

	const LOCK_SH = 1;
	const LOCK_EX = 2;
	const LOCK_UN = 3;

	const FILE_APPEND = 8;

	const SEEK_SET = 0;
	const SEEK_CUR = 1;
	const SEEK_END = 2;

	protected $_flock = 0;

	/**
	 * @return bool
	 */
	public function flock($operation, &$wouldblock = 0)
	{
		if (($ret = parent::flock($operation, $wouldblock)) === true)
		{
			$this->_flock = $operation;
		}

		return $ret;
	}

	public function flock_buffer($operation, $retry = 5)
	{
		do
		{
			if ($this->flock($operation))
			{
				return true;
			}

			// 0.01秒
			usleep(10000);
		} while (0 < $retry--);

		return false;
	}

	/**
	 * file_put_contents — 将一个字符串写入文件
	 *
	 * @see http://www.php.net/manual/zh/function.file-put-contents.php
	 */
	public function putContents($contents, $flags = 0)
	{
		if ($flags & LOCK_EX && $this->_flock ^ LOCK_EX)
		{
			$dounlock = $this->flock($this->_flock | LOCK_EX);
		}

		if ($flags ^ FILE_APPEND)
		{
			$this->fseek(0);
			$this->ftruncate(0);
		}
		elseif ($flags & FILE_APPEND)
		{
			$this->fseek(0, SEEK_END);
		}

		$ret = $this->fwrite(is_array($contents) ? implode('', $contents) : $contents);

		if ($dounlock)
		{
			$dounlock = $this->flock(($this->_flock ^ LOCK_EX) | LOCK_UN);
		}

		return $ret;
	}

	public function getContents()
	{
		$contents = '';

		foreach ($this as $k => $line)
		{
			$contents .= $line;
		}

		return $contents;
	}

	protected function _path($path)
	{
		//return str_replace(array('/./', '//'), DIR_SEP, str_replace(array(DIR_SEP_WIN, DIR_SEP_LINUX), DIR_SEP, $path));
		return Sco_File_Format::file($path);
	}

	public function getPath()
	{
		return self::_path(parent::getPath());
	}

	public function getPathname()
	{
		return self::_path(parent::getPathname());
	}

	public function getRealPath()
	{
		return self::_path(parent::getRealPath());
	}

}
