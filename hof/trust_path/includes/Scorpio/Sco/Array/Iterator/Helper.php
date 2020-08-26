<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Iterator_Helper
{

	/**
	 * iterator_apply — Call a function for every element in an iterator
	 */
	public static function iterator_apply(Traversable $iterator, $function, $args = array())
	{
		array_unshift($args, $iterator);

		return iterator_apply($iterator, $function, $args);
	}

	/**
	 * iterator_count — Count the elements in an iterator
	 */
	public static function iterator_count(Traversable $iterator)
	{
		return iterator_count($iterator);
	}

	/**
	 * iterator_to_array — Copy the iterator into an array
	 */
	public static function iterator_to_array(Traversable $iterator, $use_keys = true)
	{
		return iterator_to_array($iterator, $use_keys);
	}

}
