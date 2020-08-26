<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Sorter_Helper
{

	public static function merge_sort(&$array, $cmp_function = null)
	{
		$count = count($array);

		// Arrays of size < 2 require no action.
		if ($count < 2) return;

		// Split the array in half
		$halfway = (int)($count / 2);
		$array1 = array_slice($array, 0, $halfway);
		$array2 = array_slice($array, $halfway);

		if (!isset($cmp_function))
		{
			$cmp_function = array('Sco_Array_Comparer_Helper', 'cmp');
		}

		// Recurse to sort the two halves
		self::merge_sort($array1, $cmp_function);
		self::merge_sort($array2, $cmp_function);

		// If all of $array1 is <= all of $array2, just append them.
		if (call_user_func($cmp_function, end($array1), reset($array2)) < 1)
		{
			$array = array_merge($array1, $array2);
			return $array;
		}

		// Merge the two sorted arrays into a single sorted array
		$array = array();
		$ptr1 = $ptr2 = 0;
		while ($ptr1 < count($array1) && $ptr2 < count($array2))
		{
			if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2]) < 1)
			{
				$array[] = $array1[$ptr1++];
			}
			else
			{
				$array[] = $array2[$ptr2++];
			}
		}

		// Merge the remainder
		while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
		while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];

		return $array;
	}

	public static function merge_sort_assoc(&$array, $cmp_function = null)
	{
		$count = count($array);

		// Arrays of size < 2 require no action.
		if ($count < 2) return;

		// Split the array in half
		$halfway = (int)($count / 2);
		$array1 = array_slice($array, 0, $halfway, true);
		$array2 = array_slice($array, $halfway, $count - $halfway, true);

		if (!isset($cmp_function))
		{
			$cmp_function = array('Sco_Array_Comparer_Helper', 'cmp');
		}

		// Recurse to sort the two halves
		self::merge_sort_assoc($array1, $cmp_function);
		self::merge_sort_assoc($array2, $cmp_function);

		// If all of $array1 is <= all of $array2, just append them.
		if (call_user_func($cmp_function, end($array1), reset($array2)) < 1)
		{
			//$array = array_merge($array1, $array2);
			$array = $array1 + $array2;
			return $array;
		}

		// Merge the two sorted arrays into a single sorted array
		$array = array();
		$ptr1 = $ptr2 = 0;

		$count1 = count($array1);
		$count2 = count($array2);

		reset($array1);
		reset($array2);

		$entry1 = each($array1);
		$entry2 = each($array2);

		while (!empty($entry1) && !empty($entry2))
		{
			if (call_user_func($cmp_function, $entry1[1], $entry2[1]) < 1)
			{
				$array[$entry1[0]] = $entry1[1];
				$entry1 = each($array1);
			}
			else
			{
				$array[$entry2[0]] = $entry2[1];
				$entry2 = each($array2);
			}
		}

		// Merge the remainder
		while ($entry1)
		{
			$array[$entry1[0]] = $entry1[1];
			$entry1 = each($array1);
		}

		while ($entry2)
		{
			$array[$entry2[0]] = $entry2[1];
			$entry2 = each($array2);
		}

		return $array;
	}

	public static function stable_asort($array, $sort_flags = 0)
	{
		$arr = $array;
		asort($arr, $sort_flags);

		$arr2 = array();

		foreach ($arr as $k => $v)
		{
			if (array_key_exists($k, $arr2)) continue;

			foreach (array_keys($array, $v, true) as $k)
			{
				$arr2[$k] = &$array[$k];
			}
		}

		$array = $arr2;

		return $array;
	}

	public static function stable_asort2($array, $sort_flags = 0)
	{
		$keys = array_keys($array);
		asort($array, $sort_flags);

		$return = $list = array();

		foreach ($array as $key => &$value)
		{
			if ($value !== $last)
			{
				if ($list)
				{
					ksort($list);

					foreach ($list as $idx)
					{
						$return[] = $idx;
					}
				}

				$list = array();
			}

			$list[array_search($key, $keys)] = $key;

			$last = $value;
		}

		if ($list)
		{
			ksort($list);

			foreach ($list as $idx)
			{
				$return[] = $idx;
			}
		}

		$list = array();

		foreach ($return as $k)
		{
			$list[$k] = $array[$k];
		}

		return $list;
	}

}
