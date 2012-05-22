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

		$files = glob($_dir . '*', GLOB_NOSORT);

		while (!$no)
		{
			$t = (array )array_rand($files, min(count($files) - 1, 5));
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

	function getImageList($dir)
	{
		$_list = HOF::cache()->data('icon_list');

		if (isset($_list[$dir]))
		{
			return $_list[$dir];
		}

		$_dir = BASE_PATH . $dir;

		$map = array_flip(self::$map_imgtype);

		$list = array();

		foreach (glob($_dir . '*.*', GLOB_NOSORT) as $file)
		{
			list($name, $ext) = HOF_Class_File::basename($file);

			$_ext = ltrim($ext, '.');

			if (isset($map[$_ext]) && (!isset($list[$name]) || $map["{$list[$name][ext]}"] > $map[$_ext]))
			{
				$list[$name]['ext'] = $_ext;
				$list[$name]['no'] = $name;
				$list[$name]['file'] = str_replace($_dir, '', $file);
			}
		}

		$_list[$dir] = $list;

		HOF::cache()->data('icon_list', $_list);

		return $_list[$dir];
	}

	/**
	 * @example HOF_Class_Icon::getImageUrl('ori_003', IMG_CHAR)
	 */
	static function getImageUrl($no, $dir, $return_true = false)
	{
		$file = self::getImage($no, $dir, $return_true);

		return BASE_URL . $file;
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
			list($no, $pre) = $no;
		}
		elseif (is_string($no) && strpos($no, '_'))
		{
			list($pre, $no) = explode('_', $no, 2);

			$pre .= '_';
		}

		$_icon_cache = HOF::cache()->data('icon_cache');

		if ($_icon_cache[$dir][$pre . $no])
		{
			return $_icon_cache[$dir][$pre . $no];
		}
		elseif ($dir == self::IMG_LAND && ($_list = self::getImageList($dir)))
		{
			if (!$_list2 = HOF::cache()->data('icon_land_list'))
			{
				$_list2 = array();

				foreach (array_keys($_list) as $_no)
				{
					list($_pre, $_no) = explode('_', $_no, 2);

					$_pre .= '_';

					$k = preg_replace('/[\d]+$/', '', $_no);

					$_list2[$k][$_pre][] = $_no;
				}

				foreach ($_list2 as $k => &$_v)
				{
					foreach ($_v as &$_vv)
					{
						sort($_vv);
					}
				}

				ksort($_list2);

				HOF::cache()->data('icon_land_list', $_list2);
			}

			$k = preg_replace('/[\d]+$/', '', $no);

			if ($_list2[$k][$pre])
			{
				$idx = array_search($no, $_list2[$k][$pre]);

				if ($idx === 0 || $idx > 0)
				{
					$idx = $no;
				}
				else
				{
					$idx = reset($_list2[$k][$pre]);
				}

				$file = $_list[$pre . $idx]['file'];

				$ret = $dir . $file;

				$_icon_cache[$dir][$pre . $no] = $ret;

				HOF::cache()->data('icon_cache', $_icon_cache);

				return $ret;
			}
		}

		if (!isset($_icon_cache[$dir][$pre . $no]))
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

			$_icon_cache[$dir][$pre . $no] = $file;
		}

		if ($_icon_cache[$dir][$pre . $no])
		{
			$ret = $_icon_cache[$dir][$pre . $no];
		}
		else
		{
			$ret = $dir . $pre . ($return_true ? $no : NO_IMAGE) . '.' . reset(self::$map_imgtype);
		}

		HOF::cache()->data('icon_cache', $_icon_cache);

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
