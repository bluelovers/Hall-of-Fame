<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_Team extends HOF_Class_Array_Prop
{

	public $team_name;
	public $team_idx;

	protected static $cache;

	//protected static $team_count = 0;
	protected static $char_list = array();

	protected $temp;
	public $data;

	public function __construct($chars = array(), $team_idx = null, $team_name = null)
	{
		//$this->team_idx(self::$team_count++);

		$this->option('prop', false);
		$this->setFlags(HOF_Class_Array::STD_PROP_LIST);

		//$this->setIteratorClass('HOF_Class_Battle_TeamIterator');

		$this->team_idx($team_idx);
		$this->team_name($team_name);

		$this->data = new HOF_Class_Array_Prop();

		//$this->data = new HOF_Class_Array_Prop();

		parent::__construct($chars);

		$this->update();
	}

	/**
	 * @return self
	 */
	public static function &newInstance($team = array(), $team_idx = null, $team_name = null)
	{
		if ($team instanceof self)
		{

			$team->team_idx($team_idx);
			$team->team_name($team_name);

			return $team;
		}

		return new self($team, $team_idx, $team_name);
	}

	public function __toString()
	{
		return (string )$this->team_idx();
	}

	public function __clone()
	{
		//$this->team_idx(self::$team_count++);
		$this->team_idx = null;

		foreach ($this as $k => $v)
		{
			$char = $v->getClone();
			$this[$k] = $char;
		}
	}

	public function getClone($team_idx = null, $team_name = null)
	{
		$team = clone $this;

		$team->team_idx($team_idx);
		$team->team_name($team_name);

		$team->update();

		return $team;
	}

	/*
	function exchangeArray($input)
	{
		$array = parent::exchangeArray($input);

		$this->update();

		return $array;
	}
	*/

	public function update()
	{
		foreach ($this as $k => &$v)
		{
			$this[$k] = $v;
		}
	}

	public function team_name($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__ } = (string )$val;
		}

		return $this->{__FUNCTION__ };
	}

	public function team_idx($val = null, $chk = false)
	{
		if ($chk)
		{
			return (bool)($this->{__FUNCTION__ } === $val);
		}

		if ($val !== null)
		{
			$this->{__FUNCTION__ } = (string )$val;
		}

		return $this->{__FUNCTION__ };
	}

	public function data($key, $val = null)
	{
		if ($key === null) return $this->{__FUNCTION__ };

		if ($val !== null)
		{
			$this->{__FUNCTION__ }[$key] = $val;
		}

		return $this->{__FUNCTION__ }[$key];
	}

	public function offsetSet($k, $char)
	{
		if (!$char instanceof HOF_Class_Char_Abstract)
		{
			throw new Exception(sprintf('%s not a vaild Char', (string )$char));
		}

		$char->team($this);

		self::$char_list['all'][$char->uniqid()] = &$char;

		if (self::$cache['fixCharName'])
		{
			self::$cache['name_list'][$char->Name()]++;

			$this->_callback_fixCharName(&$char);
		}

		parent::offsetSet($k, $char);
	}

	public static function clsNameList()
	{
		self::$cache['name_list'] = array();
		self::$cache['overlap'] = array();
		self::$cache['ord'] = ord('A');

		self::$char_list = array();

		self::$cache['fixCharName'] = false;
	}

	public function pushNameList($nochk = false, $cls = false)
	{
		if ($cls || !self::$cache['ord'])
		{
			self::clsNameList();

			$nochk = true;
		}

		if (!$nochk && $this->temp['pushNameList']) return true;

		foreach ($this as $char)
		{
			self::$char_list['all'][$char->uniqid()] = &$char;
			(int)self::$cache['name_list'][$char->Name()]++;
		}

		$this->temp['pushNameList'] = true;
	}

	public function fixCharName($over = false, $pre = '', $append = '')
	{
		if (!$this->temp['pushNameList'])
		{
			$this->pushNameList();
		}

		$this->array_walk(array($this, '_callback_' . __FUNCTION__ ), array(
			(bool)$over,
			(string )$pre,
			(string )$append));

		self::$cache['fixCharName'] = true;
	}

	function _callback_fixCharName(&$entry, $key = null, $over = false, $pre = '', $append = '')
	{
		if (is_array($over))
		{
			list($over, $pre, $append) = $over;
		}

		$name = $entry->Name();

		if ((!$entry->isUnion() || (int)self::$cache['overlap'][$name]) && ((bool)$over || (int)self::$cache['name_list'][$name] > 1 || (int)self::$cache['overlap'][$name]))
		{
			$letter = chr(self::$cache['ord'] + (int)self::$cache['overlap'][$name]);

			$append .= "({$letter})";
		}

		if ($name == $entry->name)
		{
			(int)self::$cache['overlap'][$name]++;
		}

		$entry->NAME = (string )$pre . $name . (string )$append;
	}

	function filterState($STATE)
	{
		$list = $this->array_filter(HOF_Class_Array_Comparer_Callback::newInstance($STATE)->comp_func(array($this, '_callback_' . __FUNCTION__ ))->callback());

		return $list;
	}

	function _callback_filterState($char, $STATE)
	{
		$ret = ($char->STATE === $STATE);

		return (bool)$ret;
	}

	/**
	 * 生存者数を数えて返す
	 */
	function CountAlive()
	{
		/*
		$dead = $this->filterState(STATE_DEAD);

		return (int)(count($this) - count($dead));
		*/
		$i = 0;

		foreach ($this as $char)
		{
			($char->STATE !== STATE_DEAD) && $i++;
		}

		return $i;
	}

	/**
	 * 指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	 */
	function CountDead()
	{
		static $callback;
		!isset($callback) && $callback = HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array(get_class($this), '_callback_' . __FUNCTION__ ))->callback();

		$list = $this->array_filter($callback);

		return count($list);
	}

	function _callback_CountDead($char)
	{
		return (bool)($char->STATE === STATE_DEAD || $char->SPECIAL["Undead"] == true);
	}

	function CountAliveChars()
	{
		static $callback;
		!isset($callback) && $callback = HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array(get_class($this), '_callback_' . __FUNCTION__ ))->callback();

		$list = $this->array_filter($callback);

		return count($list);
	}

	function _callback_CountAliveChars($char)
	{
		return (bool)!($char->STATE === STATE_DEAD || $char->isMon());
	}

	function CountTrueChars()
	{
		static $callback;
		!isset($callback) && $callback = HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array(get_class($this), '_callback_' . __FUNCTION__ ))->callback();

		$list = $this->array_filter($callback);

		return count($list);
	}

	function _callback_CountTrueChars($char)
	{
		return (bool)(!$char->isSummon());
	}

	public function pickList($amount = null, $pick_list = null)
	{
		if ($amount === null) $amount = (int)$this->data('amount');
		if ($pick_list === null) $pick_list = $this->data('pick_list');

		$list = array();

		if ($amount > 0 && !empty($pick_list))
		{
			for ($i = 0; $i < $amount; $i++)
			{
				$list[] = $this->pick((array )$pick_list);
			}
		}

		return $list;
	}

	/**
	 * 出現する確率から敵を選んで返す
	 */
	public function pick($pick_list = null)
	{
		if ($pick_list === null) $pick_list = $this->data('pick_list');

		/**
		 * 確率の合計
		 */
		foreach ($pick_list as $val) $max += $val[0];

		HOF_Helper_Math::rand_seed();

		/**
		 * 0～合計 の中で乱数を取る
		 */
		$pos = mt_rand(0, $max);

		$list = HOF_Helper_Array::array_shuffle($pick_list);

		foreach ($list as $no => $val)
		{
			/**
			 * その時点での確率の合計
			 */
			$upp += $val[0];

			/**
			 * 合計より低ければ　敵が決定される
			 */
			if ($pos <= $upp)
			{
				return $no;
			}
		}

		return array_rand($list);
	}

}
