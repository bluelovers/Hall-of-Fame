<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_Pattern
{

	protected $char;

	const CHECK_PATTERN = -1;
	const PATTERN_NOLIMIT = -1;
	const PATTERN_MIN = 1;
	const PATTERN_MIN_CHAR = 2;

	public static $pattern_item = array(
		'judge' => 1000,
		'quantity' => 0,
		'action' => 1000,
		);

	public $options;

	protected static $options_default = array('nolimit' => false, );

	protected $cache;

	function __construct(&$char, $options = null)
	{
		$this->char = &$char;

		$this->_pattern_options($options);
	}

	function _pattern_options($options = null)
	{
		if (empty($this->options))
		{
			$this->options = self::$options_default;
		}

		if ($options !== null)
		{
			$this->options = array_merge($this->options, (array )$options);
		}

		return $this->options;
	}

	/**
	 * キャラの指示の数
	 */
	public function pattern_max()
	{
		if ($this->char->isMon() || $this->options['nolimit'])
		{
			return self::PATTERN_NOLIMIT;
		}

		$val = $this->char->int;

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

		$n = self::PATTERN_MIN_CHAR;

		foreach ($map as $v)
		{
			if ($val >= $v)
			{
				$n++;
			}
			else
			{
				break;
			}
		}

		if (29 < $this->char->level)
		{
			$n++;
		}

		return $n;
	}

	function _pattern_plus(&$pattern_new)
	{
		if (!$this->cache['init'] && $this->char->isMon())
		{
			$judge_1000 = array();

			$first_v = reset($pattern_new);

			while ($first_v && $first_v['judge'] == 1000)
			{
				$judge_1000[] = array_shift($pattern_new);

				$first_v = reset($pattern_new);
			}

			/**
			 * skill:3040 蘇生
			 */
			if (!$this->char->isSummon() && !$this->char->isUnion())
			{
				array_splice($pattern_new, 0, 0, array($this->_fix_pattern_item(array(
						1405,
						1,
						9000)), $this->_fix_pattern_item(array(
						1940,
						10,
						3040))));
			}

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
				$pattern = $this->char->pattern;
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

			$this->_pattern_plus(&$pattern_new);

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

			$this->char->pattern = $pattern_new;

			$this->cache['init'] = true;
		}

		return $this->char->pattern;
	}

	public function _fix_pattern_item($v = array(), $check = false)
	{
		if (empty($v) && !$check)
		{
			$v = self::$pattern_item;
		}
		else
		{
			if ((!$v['judge'] || !$v['action']) && is_array($v) && count($v) == 3)
			{
				list($judge, $quantity, $action) = $v;

				$v = array();
				$v['judge'] = $judge;
				$v['quantity'] = $quantity;
				$v['action'] = $action;
			}

			if (!$v['judge'] || !$v['action'])
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
		return (array )$this->char->pattern[$idx];
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
