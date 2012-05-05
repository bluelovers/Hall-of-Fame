<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
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

	protected $_data_default_ = array();

	function __construct($input = null)
	{
		if ($input === null) $input = $this->_data_default_;

		$this->setFlags(ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
		$this->exchangeArray($input);
	}

	function toArray()
	{
		return $this->getArrayCopy();
	}

	/**
	 * @return Array
	 */
	function _fixArray($append = array())
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

		return (array)$append;
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
