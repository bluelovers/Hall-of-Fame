<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

if (!class_exists('CallbackFilterIterator', false))
{
	/**
	 * 同时执行过滤和回调操作，在找到一个匹配的元素之后会调用回调函数。
	 * (PHP 5 >= 5.4.0)
	 *
	 * @author http://www.php.net/manual/pt_BR/class.callbackfilteriterator.php#108803
	 */
	class CallbackFilterIterator extends FilterIterator
	{

		/**
		 * Callback for CallbackFilterIterator
		 *
		 * @param $current   Current item's value
		 * @param $key       Current item's key
		 * @param $iterator  Iterator being filtered
		 * @return boolean   TRUE to accept the current item, FALSE otherwise
		 */
		protected $callback;

		/**
		 * Create a filtered iterator from another iterator
		 *
		 * @param callback $callback The callback should accept up to three arguments: the current item, the current key and the iterator, respectively.
		 */
		public function __construct(Iterator $iterator, $callback)
		{
			$this->callback = $callback;
			parent::__construct($iterator);
		}

		/**
		 * Calls the callback with the current value, the current key and the inner iterator as arguments
		 */
		public function accept()
		{
			return call_user_func($this->callback, $this->current(), $this->key(), $this->getInnerIterator());
		}

	}
}
