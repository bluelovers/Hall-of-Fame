<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Array_Iterator_Lockable extends ArrayIterator implements Sco_Array_Iterator_LockableInterface
{

	/**
	 * @var bool
	 */
	protected $_lock = false;

	protected $_name;

	public function __construct($array = array(), $flags = Sco_Array::ARRAY_PROP_BOTH)
	{
		parent::__construct($array, $flags);
	}

	public function setName($value)
	{
		$this->_name = $value;
	}

	public function getName()
	{
		return $this->_name;
	}

	/**
	 * lock() - mark a Iterator as readonly
	 *
	 * @return Sco_Array_Iterator_Lockable
	 */
	public function lock()
	{
		$this->_lock = true;

		return $this;
	}


	/**
	 * unlock() - unmark a Iterator to enable read & write
	 *
	 * @return Sco_Array_Iterator_Lockable
	 */
	public function unlock()
	{
		$this->_lock = false;

		return $this;
	}

	/**
	 * isLocked() - return lock status, true if, and only if, read-only
	 *
	 * @return bool
	 */
	public function isLocked()
	{
		return (bool)$this->_lock;
	}

	/**
	 * @return Sco_Array_Iterator_Lockable
	 */
	public function offsetSet($index, $newval)
	{
		$this->_chkLock();

		parent::offsetSet($index, $newval);

		return $this;
	}

	/**
	 * @return Sco_Array_Iterator_Lockable
	 */
	public function offsetUnset($index)
	{
		$this->_chkLock();

		parent::offsetUnset($index);

		return $this;
	}

	/**
	 * @return Sco_Array_Iterator_Lockable
	 */
	public function append($value)
	{
		$this->_chkLock();

		parent::append($value);

		return $this;
	}

	protected function _chkLock()
	{
		if ($this->_lock)
		{
			throw new Exception('This Iterator has been marked as read-only.');
		}
	}

}
