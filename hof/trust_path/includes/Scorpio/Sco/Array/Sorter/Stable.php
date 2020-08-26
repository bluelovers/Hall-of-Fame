<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Sorter_Stable
{

	protected $_tmp;
	protected $_tmp_return;

	protected $cmp_function;

	public function sort(&$array)
	{
		return $this->usort($array, 'strcmp');
	}

	public function usort($array, $cmp_function)
	{
		$this->encode($array);
		$this->cmp_function = $cmp_function;

		uasort($this->_tmp, array($this, '_cmp'));

		return $this->decode();
	}

	protected function _cmp($a, $b)
	{
		if ($cmp = call_user_func($this->cmp_function, $a[2], $b[2]))
		{
			return $cmp;
		}
		else
		{
			return ($a[0] < $b[0]) ? CMP_NEXT : CMP_BACK;
		}
	}

	protected function encode($array)
	{
		$this->_tmp = array();
		$idx = 0;

		foreach($array as $key => $entry)
		{
			$this->_tmp[$key] = array($idx++, $key, $entry);
		}
	}

	protected function decode($array = array())
	{
		$array = array();

		foreach ($this->_tmp as $key => &$v)
		{
			$array[$key] = &$v[2];
		}

		unset($this->_tmp);

		return $array;
	}

}
