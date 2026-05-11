<?php

/**
 * 字元指示模式類別
 * Character instruction pattern class
 *
 * 處理字元指示模式相關邏輯，包括模式判斷、執行行動等功能
 * Handles character instruction pattern logic, including pattern judgment, action execution, etc.
 *
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Class_Char_Pattern
{
	/**
	 * 字元引用
	 * Character reference
	 */
	protected $char;

	/**
	 * 檢查模式常數
	 * Check pattern constant
	 */
	const CHECK_PATTERN = -1;

	/**
	 * 無限制模式常數
	 * No limit pattern constant
	 */
	const PATTERN_NOLIMIT = -1;

	/**
	 * 最小模式常數
	 * Minimum pattern constant
	 */
	const PATTERN_MIN = 1;

	/**
	 * 最小字元模式常數
	 * Minimum character pattern constant
	 */
	const PATTERN_MIN_CHAR = 2;

	/**
	 * 預設模式項目設定
	 * Default pattern item settings
	 */
	public static $pattern_item = array(
		'judge' => 1000,
		'quantity' => 0,
		'action' => 1000,
		);

	/**
	 * 選項設定
	 * Options settings
	 */
	public $options;

	/**
	 * 預設選項設定
	 * Default options settings
	 */
	protected static $options_default = array('nolimit' => false, );

	/**
	 * 快取資料
	 * Cache data
	 */
	protected $cache;

	/**
	 * 建構函式
	 * Constructor
	 *
	 * @param mixed $char - 字元引用 / Character reference
	 * @param array $options - 選項設定 / Options settings
	 */
	function __construct(&$char, $options = null)
	{
		/**
		 * 設定字元引用
		 * Set character reference
		 */
		$this->char = &$char;

		/**
		 * 處理模式選項
		 * Process pattern options
		 */
		$this->_pattern_options($options);
	}

	/**
	 * 處理模式選項
	 * Process pattern options
	 *
	 * @param array $options - 選項設定 / Options settings
	 * @return array 處理後的選項 / Processed options
	 */
	function _pattern_options($options = null)
	{
		/**
		 * 如果選項為空，則使用預設選項
		 * If options is empty, use default options
		 */
		if (empty($this->options))
		{
			$this->options = self::$options_default;
		}

		/**
		 * 如果提供了選項，則合併到現有選項
		 * If options provided, merge with existing options
		 */
		if ($options !== null)
		{
			$this->options = array_merge($this->options, (array )$options);
		}

		return $this->options;
	}

	/**
	 * キャラの指示の数
	 *
	 * 取得字元指示的最大數量
	 * Get maximum number of character instructions
	 *
	 * @return int 最大指示數量 / Maximum instruction count
	 */
	public function pattern_max()
	{
		/**
		 * 如果是怪物或啟用無限制模式，則返回無限制
		 * If monster or no limit mode enabled, return unlimited
		 */
		if ($this->char->isMon() || $this->options['nolimit'])
		{
			return self::PATTERN_NOLIMIT;
		}

		/**
		 * 取得字元的智力值
		 * Get character's intelligence value
		 */
		$val = $this->char->int;

		/**
		 * 智力值對應的指示數量映射表
		 * Intelligence value to instruction count mapping table
		 */
		$map = array(
			10,
			15,
			30,
			50,
			80,
			120,
			160,
			200,
			251);

		/**
		 * 最小指示數量
		 * Minimum instruction count
		 */
		$n = self::PATTERN_MIN_CHAR;

			/**
			 * 根據智力值計算最大指示數量
			 * Calculate maximum instruction count based on intelligence
			 */
			foreach ($map as $v)
			{
				/**
				 * 如果智力值大於等於映射值，則增加指示數量
				 * If intelligence >= mapping value, increase instruction count
				 */
				if ($val >= $v)
				{
					$n++;
				}
				else
				{
					/**
					 * 如果智力值小於映射值，則停止計算
					 * If intelligence < mapping value, stop calculation
					 */
					break;
				}
			}

			/**
			 * 如果字元等級超過 29，則額外增加指示數量
			 * If character level > 29, additionally increase instruction count
			 */
			if (29 < $this->char->level)
			{
				$n++;
			}

			return $n;
		}

		/**
		 * 新增模式
		 * Add pattern
		 *
		 * @param array $pattern_new - 要新增的模式 / Pattern to add
		 * @return array 處理後的模式 / Processed pattern
		 */
		function _pattern_plus(&$pattern_new)
		{
			/**
			 * 如果快取未初始化且是怪物，則初始化判斷陣列
			 * If cache not initialized and is monster, initialize judgment array
			 */
			if (!$this->cache['init'] && $this->char->isMon())
			{
				$judge_1000 = array();

				/**
				 * 取得模式的第一個值
				 * Get first value of pattern
				 */
				$first_v = reset($pattern_new);

				/**
				 * 處理判斷值為 1000 的模式項目
				 * Process pattern items with judgment value 1000
				 */
				while ($first_v && $first_v['judge'] == 1000)
				{
					/**
					 * 移除並記錄判斷值為 1000 的項目
					 * Remove and record items with judgment value 1000
					 */
					$judge_1000[] = array_shift($pattern_new);

					/**
					 * 重新取得模式的第一個值
					 * Re-get first value of pattern
					 */
					$first_v = reset($pattern_new);
				}

				/**
				 * 如果不是召喚獸且不是聯盟成員，則添加特殊技能模式
				 * If not summon and not union member, add special skill pattern
				 *
				 * skill:3040 蘇生
				 */
				if (!$this->char->isSummon() && !$this->char->isUnion())
				{
					/**
					 * 插入復活技能模式 (skill:3040 蘇生)
					 * Insert revive skill pattern (skill:3040 revival)
					 */
					array_splice($pattern_new, 0, 0, array($this->_fix_pattern_item(array(
							1405,
							1,
							9000)), $this->_fix_pattern_item(array(
							1940,
							10,
							3040))));
				}

				/**
				 * 處理之前移除的判斷值為 1000 的項目
				 * Process previously removed items with judgment value 1000
				 */
				foreach((array)$judge_1000 as $v)
			{
				array_push($pattern_new, $v);
			}

			//array_push($pattern_new, $this->_fix_pattern_item(array(1001, 0, 1000)));

			array_push($pattern_new, $this->_fix_pattern_item());
		}
	}

	/**
	 * パターン配列を保存する。
	 */
	public function pattern($pattern = null, $skip_chk = false)
	{
		if ($pattern !== null)
		{
			if ($pattern === self::CHECK_PATTERN)
			{
				$pattern = $this->char->behavior['pattern'];
			}

			$pattern_new = array();

			foreach ((array )$pattern as $k => $v)
			{
				if (!$v = $this->_fix_pattern_item($v, true))
				{
					continue;
				}

				array_push($pattern_new, $v);
			}

			//debug($pattern_new);

			$this->_pattern_plus($pattern_new);

			if (!$skip_chk)
			{
				$last_v = null;

				foreach ($pattern_new as $k => $v)
				{
					if ($last_v == $v)
					{
						unset($pattern_new[$k]);
					}

					$last_v = $v;
				}
			}

			//debug($pattern_new);

			if (empty($pattern_new))
			{
				array_push($pattern_new, $this->_fix_pattern_item());
			}

			//debug($pattern_new);

			$max = $this->pattern_max();

			/**
			 * 限界設定数を超えていないか心配なので作った。。
			 */
			if ($max > self::PATTERN_MIN && $max != self::PATTERN_NOLIMIT)
			{
				$pattern_new = array_slice($pattern_new, 0, $this->pattern_max());
			}

			//debug($pattern_new);

			//exit();

			$this->char->behavior['pattern'] = $pattern_new;

			$this->cache['init'] = true;
		}

		return $this->char->behavior['pattern'];
	}

	public function _fix_pattern_item($v = array(), $check = false)
	{
		/**
		 * 修復 BUG-003: 將 HOF_Class_Array 轉換為普通陣列
		 * YAML 載入時 HOF_Class_Array 遞迴轉換巢狀陣列為物件
		 * 導致 is_array($v) 檢查失敗，pattern 被錯誤過濾
		 */
		if ($v instanceof HOF_Class_Array)
		{
			$v = $v->toArray();
		}

		if (empty($v) && !$check)
		{
			$v = self::$pattern_item;
		}
		else
		{
			if (is_array($v) && count($v) == 3 && (!$v['judge'] || !$v['action']))
			{
				list($judge, $quantity, $action) = $v;

				$v = array();
				$v['judge'] = $judge;
				$v['quantity'] = $quantity;
				$v['action'] = $action;
			}

			if (!is_array($v) || !$v['judge'] || !$v['action'])
			{
				return $check ? false : $this->_fix_pattern_item();
			}

			if (empty($v['quantity']))
			{
				$v['quantity'] = 0;
			}
			elseif (4 < strlen($v['quantity']))
			{
				$v['quantity'] = substr($v['quantity'], 0, 4);
			}
		}

		return $v;
	}

	public function pattern_item($idx)
	{
		return (array )$this->char->behavior['pattern'][$idx];
	}

	/**
	 * 行動パターンに追加する。
	 */
	public function pattern_insert($idx, $v = array(), $skip_chk = false)
	{
		if (!is_int($idx) && $idx < 0) return false;

		$pattern = $this->pattern();

		array_splice($pattern, (int)$idx, 0, array($this->_fix_pattern_item($v)));

		return $this->pattern($pattern, $skip_chk);
	}

	/**
	 * 行動パターンを削除。
	 */
	public function pattern_remove($idx)
	{
		if (!is_int($idx) && $idx < 0) return false;

		$pattern = $this->pattern();

		array_splice($pattern, $idx, 1);

		return $this->pattern($pattern);
	}

	public function pattern_memo($pattern = null)
	{
		if ($pattern)
		{
			$this->char->pattern_memo = (array )$pattern;
		}

		return $this->char->pattern_memo;
	}

	public function pattern_switch()
	{
		$temp = $this->pattern();

		$this->pattern($this->pattern_memo());

		$this->pattern_memo($temp);

		return true;
	}

}
