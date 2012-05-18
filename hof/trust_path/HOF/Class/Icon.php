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

	const IMG_IMAGE = 'static/image/';

	const IMG_ICON = 'static/image/icon/';
	const IMG_ITEM = 'static/image/icon/item/';
	const IMG_SKILL = 'static/image/icon/skill/';

	const IMG_CHAR = 'static/image/char/';
	const IMG_CHAR_REV = 'static/image/char_rev/';

	const IMG_OTHER = 'static/image/other/';

	const IMG_LAND = 'static/image/land/';

	static $map_dir = array(
		self::IMG_IMAGE,

		self::IMG_ICON,
		self::IMG_ITEM,
		self::IMG_SKILL,

		self::IMG_CHAR,
		self::IMG_CHAR_REV,

		self::IMG_OTHER,
		self::IMG_LAND,
		);

	static function getRandNo($dir, $default = NO_IMAGE)
	{
		$_dir = BASE_PATH . $dir;

		$files = glob($_dir.'*', GLOB_NOSORT);

		while(!$no)
		{
			$t = (array)array_rand($files, min(count($files) - 1, 5));
			shuffle($t);

			foreach ($t as $v)
			{
				list($name, $ext) = HOF_Class_File::basename($files[$v]);

				if (in_array(ltrim($ext, '.'), self::$map_imgtype))
				{
					$no = $name;
					break 2;
				}
			}

			$no = $default;
		}

		return $no;
	}

	/**
	 * @example HOF_Class_Icon::getImageUrl('ori_003', IMG_CHAR)
	 */
	static function getImageUrl($no, $dir, $return_true = false)
	{
		$file = self::getImage($no, $dir, $return_true);

		return BASE_URL.'/'.$file;
	}

	/**
	 * @example HOF_Class_Icon::getImage('ori_003', IMG_CHAR)
	 */
	static function getImage($no, $dir, $return_true = false)
	{
		$dir = rtrim($dir, '/') . '/';

		$pre = '';

		if (is_array($no))
		{
			$pre = (string )$no[1];
			$no = (string )$no[0];
		}
		elseif (is_string($no) && $dir == self::IMG_LAND)
		{
			$no = explode('_', $no, 2);

			$pre = (string )$no[0].'_';
			$no = (string )$no[1];
		}

		if($dir == self::IMG_LAND) $return_true = true;

		if (!isset(self::$cache[$dir][$pre . $no]))
		{
			$file = false;

			$_dir = BASE_PATH . $dir;

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
