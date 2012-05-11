<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 *
 * @method void setFlags
 * @method void exchangeArray
 *
 * @example
 * $a = new Dura_Class_Array();

 * $a->a = 1;
 * $a['b'] = 2;

 * echo '<pre>';
 * var_dump($a);

 * var_dump(array(
 * $a['a'],
 * $a->b,
 * (ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS),
 * ));
 */
class HOF_Class_Array extends ArrayObject
{

	/**
	 * Dura_Class_Array::ARRAY_PROP_BOTH = (ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
	 */
	const ARRAY_PROP_BOTH = 3;

	//protected $_data_default_ = array();

	/**
	 *
	 */
	const ARRAY_RECURSIVE_ALL = 10;

	static $ARRAYOBJECT = 'HOF_Class_Array';

	function __construct($input = null, $deep = 0)
	{
		if ($input === null) $input = (isset($this->_data_default_) && !empty($this->_data_default_)) ? $this->_data_default_ : array();

		$input = $this->_fixArrayRecursive($input, $deep);

		$this->setFlags(self::ARRAY_PROP_BOTH);
		$this->exchangeArray($input);
	}

	function exchangeArray($input)
	{
		$array = parent::exchangeArray($input);

		/**
		 * fix bug when exists class prop
		 */
		foreach (get_class_vars(get_class($this)) as $k => $v)
		{
			if (isset($this[$k]))
			{
				$this->$k = &$this[$k];
			}
		}

		return $array;
	}

	function _toArrayObjectRecursive($append, $loop = 1, $ARRAYOBJECT = null)
	{
		if (!$ARRAYOBJECT) $ARRAYOBJECT = self::$ARRAYOBJECT;

		if (!($append instanceof $ARRAYOBJECT))
		{
			$append = new $ARRAYOBJECT($append);
		}

		if ($loop > 0 && is_array($append) && count($append) > 0)
		{
			foreach($append as $k => $v)
			{
				$append[$k] = self::_toArrayObjectRecursive($v, $loop - 1, $ARRAYOBJECT);
			}
		}

		return $append;
	}

	function toArray()
	{
		return $this->getArrayCopy();
	}

	function _fixArrayRecursive($append, $loop = 1)
	{
		$append = self::_fixArray($append);

		if ($loop > 0 && is_array($append))
		{
			foreach($append as $k => $v)
			{
				$append[$k] = self::_fixArrayRecursive($v, $loop - 1);
			}
		}

		return $append;
	}

	/**
	 * @return Array
	 */
	function _fixArray($append = array(), $debug = false)
	{
		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}
		}

		return $debug ? (is_array($append) ? $append : array($append)) : $append;
	}

	/**
	 * array_splice
	 */
	function insert($offset, $insert)
	{
		$array = $this->toArray();

		$new = array();

		$j = 0;
		$do = true;

		foreach($array as $k => &$v)
		{
			if ($do && ($k == $offset || ($offset == 0 && $j == 0) || $offset === $j))
			{
				$do = false;

				foreach($insert as &$i)
				{
					$new[] = $i;
				}
			}

			$new[] = $v;

			$j++;
		}

		if ($do)
		{
			$do = false;

			foreach($insert as &$i)
			{
				$new[] = $i;
			}
		}

		$this->exchangeArray($new);
	}

}
