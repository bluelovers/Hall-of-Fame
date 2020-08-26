<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Helper
{

	static function array_shuffle($array)
	{
		$keys = array_keys($array);
		shuffle($keys);

		$old = (array )$array;

		$array = array();
		foreach ($keys as $key)
		{
			$array[$key] = $old[$key];
		}

		return $array;
	}

	/**
	 * Tests if an array is associative or not.
	 *
	 *		// Returns TRUE
	 *		Arr::is_assoc(array('username' => 'john.doe'));
	 *
	 *		// Returns FALSE
	 *		Arr::is_assoc('foo', 'bar');
	 *
	 * @param   array   array to check
	 * @return  boolean
	 */
	public static function is_assoc(array $array)
	{
		// Keys of the array
		$keys = array_keys($array);

		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}

	/**
	 * Test if a value is an array with an additional check for array-like objects.
	 *
	 *		// Returns TRUE
	 *		Arr::is_array(array());
	 *		Arr::is_array(new ArrayObject);
	 *
	 *		// Returns FALSE
	 *		Arr::is_array(FALSE);
	 *		Arr::is_array('not an array!');
	 *		Arr::is_array(Database::instance());
	 *
	 * @param   mixed    value to check
	 * @return  boolean
	 */
	public static function is_array($value, $skip_traversable = false)
	{
		return (bool)(is_array($value) || (!$skip_traversable && $value instanceof Traversable));
	}

	/**
	 * @return array
	 */
	public static function array_unshift_assoc($arr, $key, $val)
	{
		self::array_remove_key($arr, $key);

		$arr = array_merge(array($key => $val), $arr);

		return $arr;
	}

	/**
	 * @return array
	 */
	public static function array_push_assoc($arr, $key, $val)
	{
		self::array_remove_key($arr, $key);

		$arr = array_merge($arr, array($key => $val));

		return $arr;
	}

	/**
	 * safe remove key from array, return old value
	 *
	 * @return mixed|null|array
	 */
	public static function array_remove_key(&$arr, $key)
	{
		$old = null;

		if (is_array($key))
		{
			foreach ($key as $k)
			{
				if (array_key_exists($k, $arr))
				{
					$old[$k] = self::array_remove_key($arr, $k);
				}
			}
		}
		elseif (array_key_exists($key, $arr))
		{
			$old = $arr[$key];

			$null = null;
			$arr[$key] = &$null;
			unset($arr[$key]);
		}

		return $old;
	}

	/**
	 * array_search_match($needle, $haystack)
	 * returns all the keys of the values that match $needle in $haystack
	 *
	 * @return array
	 */
	public static function array_search_match($needle, array $haystack, $strict = false)
	{
		/*
		$array = array();

		foreach ($haystack as $k => $v)
		{
		if (!$strict && $haystack[$k] == $needle)
		{
		$array[] = $k;
		}
		elseif ($strict && $haystack[$k] === $needle)
		{
		$array[] = $k;
		}
		}
		*/
		$array = array_keys($array, $needle, $strict);

		return $array;
	}

	/**
	 * Sco_Array_Helper::seek - Seeks to a position
	 * Seeks to a given position in the iterator.
	 *
	 * @param array $array
	 * @param integer $offset The position to seek to.
	 *
	 * @return mixed
	 *
	 * @assert (array(0, 1, 2, 3, 4, 5), 3) == 3
	 */
	public static function seek(&$array, $offset)
	{
		if ($offset == Sco_Array::SEEK_RESET)
		{
			return reset($array);
		}
		elseif ($offset > Sco_Array::SEEK_RESET)
		{
			if ($offset >= count($array))
			{
				throw new OutOfBoundsException(sprintf('Seek position %d is out of range', $offset));
			}

			reset($array);
			$offset--;
			for ($i = Sco_Array::SEEK_RESET; $i < $offset; $i++)
			{
				next($array);
			}
			return next($array);
		}
		else
		{
			return end($array);
		}
	}

	/**
	 * Sco_Array_Helper::seek - Seeks to a position
	 * Seeks to a given position in the iterator.
	 *
	 * @param array $array
	 * @param integer $offset The position to seek to.
	 *
	 * @return mixed
	 */
	public static function seek_key(&$array, $offset)
	{
		reset($array);

		while ($k = each($array))
		{
			if ($k[0] == $offset)
			{
				return prev($array);
			}
		}

		throw new OutOfBoundsException(sprintf('Seek position \'%s\' is out of range', $offset));
	}

	/**
	 * Exchange the array for another one.
	 *
	 * @param array|ArrarObject|Traversable|ArrayIterator &$array
	 * @param array $exchangeArray
	 *
	 * @return array|ArrarObject|Traversable|ArrayIterator $array
	 */
	public static function array_exchange(&$array, $exchangeArray = array())
	{
		if (is_array($array))
		{
			$array = $exchangeArray;
		}
		elseif (is_object($array))
		{
			if ($array instanceof ArrarObject || method_exists($array, 'exchangeArray'))
			{
				$array->exchangeArray($exchangeArray);
			}
			elseif ($array instanceof Traversable)
			{
				foreach ($array as $k => &$v)
				{
					unset($array[$k]);
				}

				if ($exchangeArray)
				{
					foreach ($exchangeArray as $k => &$v)
					{
						$array[$k] = &$v;
					}
				}
			}
			else
			{
				$thowerror = true;
			}
		}
		else
		{
			$thowerror = true;
		}

		if ($thowerror)
		{
			throw new Exception();
		}

		return $array;
	}

	public static function recursive2array($array, &$return = null, $skip_traversable = false)
	{
		if (func_num_args() <= 3 || !func_get_arg(3))
		{
			$return = array();
		}

		foreach ($array as $entry)
		{
			if (is_array($entry) || (!$skip_traversable && $entry instanceof Traversable))
			{
				self::recursive2array($entry, $return, $skip_traversable, true);
			}
			else
			{
				$return[] = $entry;
			}
		}

		return $return;
	}

}
