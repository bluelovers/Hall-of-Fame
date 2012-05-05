<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Icon
{
	static $cache = array();
	static $map_imgtype = array(
		'png',
		'gif',
		'jpg',
		'bmp',
		);

	/**
	 * @example HOF_Class_Icon::getIamge('ori_003', IMG_CHAR)
	 */
	function getIamge($no, $dir, $return_true = false)
	{
		$dir = rtrim($dir, '/').'/';

		$pre = '';

		if (is_array($no))
		{
			$pre = (string)$no[1];
			$no = (string)$no[0];
		}

		if (!isset(self::$cache[$dir][$pre.$no]))
		{
			$file = false;

			foreach (self::$map_imgtype as $ext)
			{
				$_file = $dir . $pre.$no . '.' . $ext;
				if (file_exists(BASE_PATH.$_file))
				{
					$file = $_file;
					break;
				}
			}

			self::$cache[$dir][$pre.$no] = $file;
		}

		if (self::$cache[$dir][$pre.$no])
		{
			$ret = self::$cache[$dir][$pre.$no];
		}
		else
		{
			$ret = $dir . $pre.($return_true ? $no : NO_IMAGE) . '.' . reset(self::$map_imgtype);
		}

		return $ret;
	}

}
