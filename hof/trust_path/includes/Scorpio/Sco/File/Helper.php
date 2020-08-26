<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_File_Helper
{

	public static function fp_get_contents($fp)
	{
		rewind($fp);
		return stream_get_contents($fp);
	}

	public static function fp_put_contents($fp, $data, $flags = 0)
	{
		if ($flags & LOCK_EX)
		{
			$is_locked = flock($fp, LOCK_EX);
		}

		fseek($fp, 0, SEEK_SET);
		ftruncate($fp, 0);
		rewind($fp);
		$return = fputs($fp, $data);

		if ($is_locked) flock($fp, LOCK_UN);

		return $return;
	}

	public static function is_resource_file($fp)
	{
		if (is_resource($fp) && (get_resource_type($fp) == 'file' || get_resource_type($fp) == 'stream'))
		{
			return $fp;
		}

		return false;
	}

}
