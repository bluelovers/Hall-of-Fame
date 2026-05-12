<?php

/**
 * 陣列操作類別
 * Array operation class
 *
 * 繼承自 ArrayObject，提供陣列處理功能
 * Inherits from ArrayObject, provides array processing functionality
 *
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
 *
 * $a->a = 1;
 * $a['b'] = 2;
 *
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
	 * 陣列屬性類型：標準屬性列表 + 陣列作為屬性
	 * Array property type: standard property list + array as properties
	 */
	const ARRAY_PROP_BOTH = 3;

	/** 陣列遞迴處理類型：全部處理 / Array recursive processing type: process all */
	const ARRAY_RECURSIVE_ALL = 10;

	/** 預設的陣列類別 / Default array class */
	static $ARRAYOBJECT = 'HOF_Class_Array';

	/** 自動轉換標誌 / Auto conversion flag */
	protected $ARRAYOBJECT_AUTO = 0;
	/** 陣列選項配置 / Array options configuration */
	protected $ARRAYOBJECT_OPTIONS = array();

	/**
	 * 建構函式
	 * Constructor
	 *
	 * @param mixed $input - 輸入陣列 / Input array
	 * @param int $deep - 遞迴深度 / Recursion depth
	 * @param int $loop - 迴圈次數 / Loop count
	 */
	function __construct($input = null, $deep = 0, $loop = 0)
	{
		/**
		 * 如果輸入為空，則使用預設資料或空陣列
		 * If input is null, use default data or empty array
		 */
		if ($input === null) $input = (isset($this->_data_default_) && !empty($this->_data_default_)) ? $this->_data_default_ : array();

		/**
		 * 修正遞迴陣列結構
		 * Fix recursive array structure
		 */
		$input = $this->_fixArrayRecursive($input, $deep);

		/**
		 * 設定陣列屬性類型並交換陣列內容
		 * Set array property type and exchange array content
		 */
		$this->setFlags(self::ARRAY_PROP_BOTH);
		$this->exchangeArray($input);

		/**
		 * 如果迴圈次數大於 0，則設定自動轉換標誌
		 * If loop count > 0, set auto conversion flag
		 */
		if ($loop > 0)
		{
			$this->ARRAYOBJECT_AUTO = $loop;
		}

		/**
		 * 將物件遞迴轉換為 ArrayObject
		 * Recursively convert object to ArrayObject
		 */
		$this->_toArrayObjectRecursive($this, $loop);
	}

	/**
	 * 交換陣列內容
	 * Exchange array content
	 *
	 * @param mixed $input - 新的陣列內容 / New array content
	 * @return array 舊的陣列內容 / Old array content
	 */
	function exchangeArray($input)
	{
		$array = parent::exchangeArray($input);

		/*
		if (empty($input))
		{
			$keep = true;
		}
		*/

		/**
		 * 修復當存在類別屬性時的 bug
		 * Fix bug when exists class properties
		 */
		$reflect = new HOF_Class_Reflection_Class($this);
		$props = $reflect->getProperties();
		foreach ($props as $prop)
		{
			$k = $prop->getName();

			/**
			 * 跳過靜態屬性和私有屬性
			 * Skip static and private properties
			 */
			if ($prop->isStatic() || $prop->isPrivate())
			{
				continue;
			}

			/**
			 * 如果陣列中存在該鍵，則建立引用關係
			 * If key exists in array, create reference relationship
			 */
			if ($this->offsetExists($k))
			{
				$this->$k = &$this[$k];
			}
			/**
			 * 如果鍵名不是 ARRAYOBJECT 開頭，則設定陣列值
			 * If key name doesn't start with ARRAYOBJECT, set array value
			 */
			elseif (strpos($k, 'ARRAYOBJECT') === false)
			{
				$this->offsetSet($k, $this->$k);
			}
		}

		/*
		foreach (get_class_vars(get_class($this)) as $k => $v)
		{
			if (0 && $keep && $v !== null)
			{
				$this[$k] = $this->$k;
			}
			elseif (isset($this[$k]))
			{
				$this->$k = &$this[$k];
			}
			elseif (0 && property_exists($this, $k))
			{
				self::offsetSet($k, $this->$k);
			}
		}
		*/

		return $array;
	}

	/**
	 * 遞迴轉換為 ArrayObject
	 * Recursively convert to ArrayObject
	 *
	 * @param mixed $append - 要附加的資料 / Data to append
	 * @param int $loop - 迴圈次數 / Loop count
	 * @param string $ARRAYOBJECT - ArrayObject 類別名稱 / ArrayObject class name
	 * @return mixed 轉換後的資料 / Converted data
	 */
	function _toArrayObjectRecursive($append, $loop = 1, $ARRAYOBJECT = null)
	{
		/**
		 * 如果未指定 ArrayObject 類別，則使用預設類別
		 * If no ArrayObject class specified, use default class
		 */
		if (!$ARRAYOBJECT) $ARRAYOBJECT = self::$ARRAYOBJECT;

		/**
		 * 如果存在當前實例
		 * If current instance exists
		 */
		if (isset($this))
		{
			/**
			 * 如果是陣列且不是 ArrayObject 實例，則轉換為 ArrayObject
			 * If is array and not ArrayObject instance, convert to ArrayObject
			 */
			if (is_array($append) && !($append instanceof $ARRAYOBJECT))
			{
				$append = new $ARRAYOBJECT($append, 0, $loop);
			}

			/**
			 * 如果迴圈次數大於 0 且是 ArrayObject 實例且有內容，則處理每個元素
			 * If loop count > 0 and is ArrayObject instance and has content, process each element
			 */
			if ($loop > 0 && ($append instanceof $ARRAYOBJECT) && count($append) > 0)
			{
				foreach ($append as &$v)
				{
					/**
					 * 如果元素是陣列，則遞迴轉換為 ArrayObject
					 * If element is array, recursively convert to ArrayObject
					 */
					if (is_array($v))
					{
						$v = new $ARRAYOBJECT($v, 0, $loop - 1);
					}
				}
			}

			return $append;
		}

		/**
		 * 如果是陣列且不是 ArrayObject 實例，則轉換為 ArrayObject
		 * If is array and not ArrayObject instance, convert to ArrayObject
		 */
		if (is_array($append) && !($append instanceof $ARRAYOBJECT))
		{
			$append = new $ARRAYOBJECT($append);
		}

		/**
		 * 如果迴圈次數大於 0 且是 ArrayObject 實例且有內容，則遞迴處理每個元素
		 * If loop count > 0 and is ArrayObject instance and has content, recursively process each element
		 */
		if ($loop > 0 && ($append instanceof $ARRAYOBJECT) && count($append) > 0)
		{
			foreach ($append as $k => $v)
			{
				/**
				 * 遞迴轉換每個元素
				 * Recursively convert each element
				 */
				$append[$k] = self::_toArrayObjectRecursive($v, $loop - 1, $ARRAYOBJECT);
			}
		}

		return $append;
	}

	/**
	 * 轉換為陣列
	 * Convert to array
	 *
	 * @param bool $public - 是否只包含公開屬性 / Whether to include only public properties
	 * @param bool $fix - 是否修正陣列結構 / Whether to fix array structure
	 * @return array 轉換後的陣列 / Converted array
	 */
	function toArray($public = false, $fix = false)
	{
		/**
		 * 取得當前層級的陣列副本
		 * Get array copy of current level
		 */
		$data = $this->getArrayCopy();

		/**
		 * 遞迴處理子元素
		 * Recursively process child elements
		 */
		foreach ($data as $k => $v)
		{
			if ($v instanceof self)
			{
				$data[$k] = $v->toArray($public, $fix);
			}
			elseif (is_array($v) || $v instanceof ArrayObject)
			{
				/**
				 * 確保一般的陣列或 ArrayObject 也能被處理
				 * Ensure regular arrays or ArrayObjects are also processed
				 */
				$v = new self($v);
				$data[$k] = $v->toArray($public, $fix);
			}
		}

		/**
		 * 如果需要只包含公開屬性
		 * If need to include only public properties
		 */
		if ($public)
		{
			$reflect = new ReflectionClass($this);
			$props = $reflect->getProperties();
			$list = array();

			foreach ($props as $prop)
			{
				if ($prop->isStatic() || $prop->isPrivate() || $prop->isPublic())
				{
					continue;
				}
				$list[$prop->getName()] = 1;
			}

			$data = array_diff_key($data, $list);
		}

		if ($fix)
		{
			$data = self::_fixArrayRecursive($data, self::ARRAY_RECURSIVE_ALL);
		}

		return $data;
	}

	/**
	 * 遞迴修正陣列結構
	 * Recursively fix array structure
	 *
	 * @param mixed $append - 要修正的資料 / Data to fix
	 * @param int $loop - 迴圈次數 / Loop count
	 * @return mixed 修正後的資料 / Fixed data
	 */
	function _fixArrayRecursive($append, $loop = 1)
	{
		/**
		 * 修正陣列結構
		 * Fix array structure
		 */
		$append = self::_fixArray($append);

		/**
		 * 如果迴圈次數大於 0 且是陣列，則遞迴處理每個元素
		 * If loop count > 0 and is array, recursively process each element
		 */
		if ($loop > 0 && is_array($append))
		{
			foreach ($append as $k => $v)
			{
				/**
				 * 遞迴修正每個元素
				 * Recursively fix each element
				 */
				$append[$k] = self::_fixArrayRecursive($v, $loop - 1);
			}
		}

		return $append;
	}

	/**
	 * 修正陣列結構
	 * Fix array structure
	 *
	 * @param array $append - 要修正的陣列 / Array to fix
	 * @param bool $debug - 是否除錯模式 / Whether debug mode
	 * @return array 修正後的陣列 / Fixed array
	 */
	function _fixArray($append = array(), $debug = false)
	{
		/**
		 * 如果資料非空
		 * If data is not empty
		 */
		if (!empty($append))
		{
			/**
			 * 如果是 HOF_Class_Array 實例，則轉換為陣列
			 * If is HOF_Class_Array instance, convert to array
			 */
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			/**
			 * 如果是 ArrayObject 實例，則轉換為陣列
			 * If is ArrayObject instance, convert to array
			 */
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}
		}

		return $debug ? (is_array($append) ? $append : array($append)) : $append;
	}

	/**
	 * 在指定位置插入元素（類似 array_splice）
	 * Insert elements at specified position (similar to array_splice)
	 *
	 * @param mixed $offset - 插入位置偏移量 / Insert position offset
	 * @param array $insert - 要插入的元素陣列 / Elements array to insert
	 */
	function insert($offset, $insert)
	{
		/**
		 * 取得當前陣列副本
		 * Get current array copy
		 */
		$array = $this->toArray();

		/**
		 * 建立新的陣列容器
		 * Create new array container
		 */
		$new = array();

		/**
		 * 迴圈計數器
		 * Loop counter
		 */
		$j = 0;
		$do = true;

		/**
		 * 遍歷原陣列並在指定位置插入新元素
		 * Traverse original array and insert new elements at specified position
		 */
		foreach ($array as $k => &$v)
		{
			/**
			 * 檢查是否達到插入位置
			 * Check if insertion position is reached
			 */
			if ($do && ($k == $offset || ($offset == 0 && $j == 0) || $offset === $j))
			{
				/**
				 * 標記為已插入，避免重複插入
				 * Mark as inserted to avoid duplicate insertion
				 */
				$do = false;

				/**
				 * 插入新元素到新陣列
				 * Insert new elements to new array
				 */
				foreach ($insert as &$i)
				{
					$new[] = $i;
				}
			}

			/**
			 * 將原陣列元素複製到新陣列
			 * Copy original array elements to new array
			 */
			$new[] = $v;

			/**
			 * 增加迴圈計數器
			 * Increment loop counter
			 */
			$j++;
		}

		/**
		 * 如果插入位置超過陣列長度，則在末尾插入
		 * If insertion position exceeds array length, append at end
		 */
		if ($do)
		{
			/**
			 * 標記為已插入
			 * Mark as inserted
			 */
			$do = false;

			/**
			 * 在陣列末尾插入新元素
			 * Insert new elements at array end
			 */
			foreach ($insert as &$i)
			{
				$new[] = $i;
			}
		}

		/**
		 * 交換陣列內容
		 * Exchange array content
		 */
		$this->exchangeArray($new);
	}

	/**
	 * 合併陣列
	 * Merge array
	 *
	 * @param array $arr - 要合併的陣列 / Array to merge
	 */
	function merge($arr)
	{
		/**
		 * 遍歷要合併的陣列並合併到當前實例
		 * Traverse array to merge and merge into current instance
		 */
		foreach ($arr as $k => &$v)
		{
			/**
			 * 將鍵值對應到當前實例的屬性
			 * Map key-value pairs to current instance properties
			 */
			$this->$k = $v;
		}
	}

	/*
	public function offsetSet($name, $value)
	{
		if ($this->ARRAYOBJECT_AUTO && is_array($value))
		{
			$ARRAYOBJECT = self::$ARRAYOBJECT;
			$value = new $ARRAYOBJECT($value, 0, $this->ARRAYOBJECT_AUTO - 1);

			//var_dump(array(__FUNCTION__, $value));
		}

		return parent::offsetSet($name, $value);
	}
	*/

}
