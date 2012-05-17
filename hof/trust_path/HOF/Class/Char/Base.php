<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_Base implements HOF_Class_Base_Extend_RootInterface
{
	public $id;

	public $name;
	public $gender = GENDER_UNKNOW;

	/**
	 * ステータス
	 */
	public $maxhp, $hp, $maxsp, $sp, $str, $int, $dex, $spd, $luk;

	public $MAXHP, $HP, $MAXSP, $SP, $STR, $INT, $DEX, $SPD, $LUK;

	/**
	 * 単純なステータス補正(plus)
	 */
	public $P_MAXHP, $P_MAXSP, $P_STR, $P_INT, $P_DEX, $P_SPD, $P_LUK;
	/**
	 * 単純なステータス補正(multipication)
	 */
	public $M_MAXHP, $M_MAXSP;
	/**
	 * 特殊技能
	 */
	public $SPECIAL;

	/**
	 * 生存状態にする
	 * 状態(0=生存 1=しぼー 2=毒状態)
	 */
	public $STATE = STATE_ALIVE;

	/**
	 * $atk=array(物理,魔法); $def=array(物理/,物理-,魔法/,魔法-);
	 */
	public $atk, $def;

	/**
	 * 行動までの時間
	 */
	public $delay;

	/**
	 * 行動(判定、使うスキル)
	 */
	public $pattern, $pattern_memo;

	/**
	 * (数値=詠唱中 false=待機中)
	 * 詠唱完了時に使うスキル
	 * @var bool
	 */
	public $expect = false;
	/**
	 * 詠唱完了時に使うスキルのタイプ(物理/魔法)
	 */
	public $expect_type;
	/**
	 * ↑のターゲット
	 */
	public $expect_target;

	/**
	 * 行動回数
	 */
	public $ActCount = 0;

	/**
	 * 決定した判断の回数=array()
	 * @var array
	 */
	public $JdgCount = array();

	protected $_extends_ = array();
	protected $_extends_method_ = array();

	protected $_extends_method_invalids_ = array();

	public function extend_remove($extend, $idx = null)
	{
		if (is_object($extend))
		{
			$class = get_class($extend);
		}
		else
		{
			$class = $extend;
		}

		if (!$idx) $idx = $class;

		foreach ((array )$this->_extends_[$idx]['method'] as $method)
		{
			unset($this->_extends_method_[$method]);
		}

		unset($this->_extends_[$idx]);

		return array($idx, $class);
	}

	public function extend($extend, $idx = null)
	{
		list($class, $idx) = $this->extend_remove($extend, $idx);

		$this->_extends_[$idx]['idx'] = $idx;

		if (is_object($extend))
		{
			$this->_extends_[$idx]['obj'] = &$extend;
		}
		else
		{
			$this->_extends_[$idx]['obj'] = null;
		}

		$this->_extends_[$idx]['class'] = $class;

		$methods = HOF_Helper_Object::get_public_methods($class, $this->_extends_method_invalids_);

		$this->_extends_[$idx]['method'] = $methods;

		foreach ($methods as $method)
		{
			$this->_extends_method_[$method] = $idx;
		}

		return $this;
	}

	public function __call($func, $argv)
	{
		if (!empty($this->_extends_method_[$func]))
		{
			$idx = $this->_extends_method_[$func];
			$class = $this->_extends_[$idx]['class'];

			if (empty($this->_extends_[$idx]['callback'][$func]))
			{
				if (!is_object($this->_extends_[$idx]['obj']))
				{
					$this->_extends_[$idx]['obj'] = new $class(&$this);
				}

				$this->_extends_[$idx]['callback'][$func] = array(&$this->_extends_[$idx]['obj'], $func);
			}

			return call_user_func_array($this->_extends_[$idx]['callback'][$func], $argv);
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

	/**
	 * ファイルポインタが開かれていれば閉じる
	 */
	function fpclose()
	{
		HOF_Class_File::fpclose($this->fp);

		unset($this->fp);
	}

	static function uniqid()
	{
		return md5(uniqid(HOF::ip(), true));
	}

}
