<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Dumper_Helper
{

	public static function toArrayRecursive($obj, $filter_empty = true)
	{
		if ($filter_empty)
		{
			$dump = null;
		}
		else
		{
			$dump = array();
		}

		if (is_array($obj) || $obj instanceof Traversable)
		{
			foreach ($obj as $k => $v)
			{
				if (($filter_empty && is_array($v) && empty($v)) || $v === null)
				{
					continue;
				}
				elseif (is_array($v) || $v instanceof Traversable)
				{
					$_v = self::toArrayRecursive($v, $filter_empty);

					if (!(($filter_empty && is_array($_v) && empty($_v)) || $_v === null))
					{
						$dump[$k] = $_v;
					}
				}
				else
				{
					$dump[$k] = $v;
				}
			}
		}
		else
		{
			$dump = $obj;
		}

		return $dump;
	}

}
