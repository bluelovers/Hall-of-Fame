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

	const IMG_IMAGE = 'image/';
	const IMG_ICON = 'image/icon/';
	const IMG_CHAR = 'image/char/';
	const IMG_CHAR_REV = 'image/char_rev/';
	const IMG_OTHER = 'image/other/';

	static $map_dir = array(
		IMG_IMAGE,
		IMG_ICON,
		IMG_CHAR,
		IMG_CHAR_REV,
		IMG_OTHER,
		);

	function getImageUrl($no, $dir, $return_true = false)
	{
		$file = self::getIamge($no, $dir, $return_true);

		return BASE_URL.'/'.$file;
	}

	/**
	 * @example HOF_Class_Icon::getIamge('ori_003', IMG_CHAR)
	 */
	function getIamge($no, $dir, $return_true = false)
	{
		$dir = rtrim($dir, '/') . '/';

		$pre = '';

		if (is_array($no))
		{
			$pre = (string )$no[1];
			$no = (string )$no[0];
		}

		if (!isset(self::$cache[$dir][$pre . $no]))
		{
			$file = false;

			$_dir = $dir;
			if (in_array($dir, self::$map_dir))
			{
				$_dir = BASE_PATH_STATIC . $_dir;
			}
			else
			{
				$_dir = BASE_PATH . $_dir;
			}

			foreach (self::$map_imgtype as $ext)
			{
				$_file = $_dir . $pre . $no . '.' . $ext;
				if (file_exists($_file))
				{
					$file = $_file;
					break;
				}
			}

			self::$cache[$dir][$pre . $no] = $file;
		}

		if (self::$cache[$dir][$pre . $no])
		{
			$ret = self::$cache[$dir][$pre . $no];
		}
		else
		{
			$ret = $dir . $pre . ($return_true ? $no : NO_IMAGE) . '.' . reset(self::$map_imgtype);
		}

		return $ret;
	}

}
