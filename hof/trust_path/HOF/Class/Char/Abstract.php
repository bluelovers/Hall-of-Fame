<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class HOF_Class_Char_Abstract extends HOF_Class_Base_Extend_Root
{
	// ファイルポインタ
	public $fp;
	public $file;

	public $id;

	public $name;
	public $gender = GENDER_UNKNOW;

	public $img;

	public $level;

	/**
	 * 戦闘用変数(BattleVariable) データには保存されない。
	 */
	public $team;

	/**
	 * スキル
	 */
	public $skill;

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
	 * 戦闘その他
	 */
	public $position, $guard;

	public $POSITION;

	/**
	 * PoisonResist 毒抵抗
	 * HealBonus .
	 * Barrier
	 * Undead
	 */
	public $WEAPON;

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

	/**
	 * 再読み込みを防止できるか?
	 */
	protected $_cache_char_;

	/**
	 * 武器タイプ
	 */

	var $map_equip_allow = array(
		EQUIP_SLOT_MAIN_HAND => true,
		EQUIP_SLOT_OFF_HAND => true,
		EQUIP_SLOT_ARMOR => true,
		EQUIP_SLOT_ITEM => true,
		);

	static $map_equip = array(
		EQUIP_SLOT_MAIN_HAND => true,
		EQUIP_SLOT_OFF_HAND => true,
		EQUIP_SLOT_ARMOR => true,
		EQUIP_SLOT_ITEM => true,
		);

	public $owner = HOF_Class_Char::OWNER_SYSTEM;
	protected $CHAR_TYPE;
	protected $CHAR_TYPES;

	public function __construct($id, $owner)
	{
		$this->initCharType();
		$this->owner($owner);

		$this->_extend_init();
	}

	protected function _extend_init()
	{
		$this->extend('HOF_Class_Char_Attr');
		$this->extend('HOF_Class_Char_Pattern');
		$this->extend('HOF_Class_Char_View');
		$this->extend('HOF_Class_Char_Battle_Effect');
		$this->extend('HOF_Class_Char_Battle');
	}

	public function owner($owner = null)
	{
		if ($owner !== null)
		{
			$this->owner = $owner;
		}

		return $this->owner;
	}

	/**
	 * ファイルポインタが開かれていれば閉じる
	 */
	public function fpclose()
	{
		if ($this->fp)
		{
			HOF_Class_File::fpclose($this->fp);

			unset($this->fp);
		}
	}

	public function __destruct()
	{
		$this->fpclose();
	}

	protected function initCharType()
	{
		sscanf(get_class($this), 'HOF_Class_Char_Type_%s', $type);

		$this->CHAR_TYPE = $type;

		$types = explode('_', HOF::putintoPathParts($type));

		$this->CHAR_TYPES = array_fill_keys((array)$types, true);

		return $this;
	}

	public function setCharType($sub_type)
	{
		$sub_type = strtolower($sub_type);

		$this->CHAR_TYPES[$sub_type] = true;

		return $this;
	}

	public function getCharType($types = null)
	{
		return $types ? $this->CHAR_TYPES : $this->CHAR_TYPE;
	}

	/**
	 * @return bool
	 */
	public function hasCharType($type)
	{
		$types = is_array($type) ? $type : func_get_args();
		$char_type = $this->getCharType(true);

		if (!empty($type) && !empty($char_type))
		{
			$ret = false;

			foreach ($types as $sub_type)
			{
				if (!isset($char_type[$sub_type]) || !$char_type[$sub_type])
				{
					return false;
				}

				$ret = true;
			}

			return (bool)$ret;
		}
	}

	/**
	 * @return bool
	 */
	public function isUnion()
	{
		return $this->hasCharType(HOF_Class_Char::TYPE_UNION);
	}

	/**
	 * @return bool
	 */
	public function isSummon()
	{
		return $this->hasCharType(HOF_Class_Char::TYPE_SUMMON);
	}

	/**
	 * @return bool
	 */
	public function isMon()
	{
		return $this->hasCharType(HOF_Class_Char::TYPE_MON);
	}

}
