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
		self::IMG_IMAGE,
		self::IMG_ICON,
		self::IMG_CHAR,
		self::IMG_CHAR_REV,
		self::IMG_OTHER,
		);

	/**
	 * @example HOF_Class_Icon::getImageUrl('ori_003', IMG_CHAR)
	 */
	function getImageUrl($no, $dir, $return_true = false)
	{
		$file = self::getImage($no, $dir, $return_true);

		return BASE_URL.'/'.$file;
	}

	/**
	 * @example HOF_Class_Icon::getImage('ori_003', IMG_CHAR)
	 */
	function getImage($no, $dir, $return_true = false)
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
					$_file = $dir . $pre . $no . '.' . $ext;

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

		/*
		if ($no == 'eiyusenki_018')
		{
			var_dump(array(
				$no,
				$dir,
				$_dir,
				$pre,
				$return_true,
				$ret,
				in_array($dir, self::$map_dir),
				BASE_PATH_STATIC,
				BASE_PATH,
			));
			exit();
		}
		*/

		return $ret;
	}

}
