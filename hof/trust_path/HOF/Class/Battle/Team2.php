<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_Team2 extends HOF_Class_Array_Prop
{

	public $team_name;
	public $team_idx;

	protected $team_self;
	static $cache;

	static $team_count = 0;
	static $char_list = array();

	public function __construct($chars = array())
	{
		$this->team_idx(self::$team_count++);
		$this->team_self = $this;

		$this->option('prop', false);
		//$this->setIteratorClass('HOF_Class_Battle_TeamIterator');

		parent::__construct($chars);
	}

	public function __toString()
	{
		return (string )$this->team_idx;
	}

	public function __clone()
	{
		$this->team_idx(self::$team_count++);
		$this->team_self = $this;

		foreach ($this as $k => $v)
		{
			$char = $v->getClone();
			$this[$k] = $char;
		}
	}

	public function getClone($team_idx, $team_name = null)
	{
		$team = clone $this;

		$team->team_idx = (int)$team_idx;
		$team_name && $team->team_name = (string )$team_name;

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

	public function team_idx($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__ } = (string )$val;
		}

		return $this->{__FUNCTION__ };
	}

	public function offsetSet($k, $char)
	{
		if (!$char instanceof HOF_Class_Char_Abstract)
		{
			throw new Exception('%s not a vaild Char', (string )$char);
		}

		$char->team($this);

		self::$char_list['all'][$char->uniqid()] = $char;

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

	public function pushNameList($cls = false)
	{
		if ($cls || !isset(self::$cache['ord']))
		{
			self::clsNameList();
		}

		foreach ($this as $char)
		{
			self::$char_list['all'][$char->uniqid()] = $char;
			(int)self::$cache['name_list'][$char->Name()]++;
		}
	}

	public function fixCharName($over = false, $pre = '', $append = '')
	{
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

		if ((bool)$over || (int)self::$cache['name_list'][$name] > 1 || (int)self::$cache['overlap'][$name])
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
		$dead = $this->filterState(STATE_DEAD);

		return (int)(count($this) - count($dead));
	}

	/**
	 * 指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	 */
	function CountDead()
	{
		$list = $this->array_filter(HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array($this, '_callback_' . __FUNCTION__ ))->callback());

		return count($list);
	}

	function _callback_CountDead($char)
	{
		return (bool)($char->STATE === STATE_DEAD || $char->SPECIAL["Undead"] == true);
	}

	function CountAliveChars()
	{
		$list = $this->array_filter(HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array($this, '_callback_' . __FUNCTION__ ))->callback());

		return count($list);
	}

	function _callback_CountAliveChars($char)
	{
		return (bool)!($char->STATE === STATE_DEAD || $char->isMon());
	}

	function CountTrueChars()
	{
		$list = $this->array_filter(HOF_Class_Array_Comparer_Callback::newInstance()->comp_func(array($this, '_callback_' . __FUNCTION__ ))->callback());

		return count($list);
	}

	function _callback_CountTrueChars($char)
	{
		return (bool)!($char->isSummon());
	}

}
