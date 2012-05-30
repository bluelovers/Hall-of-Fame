<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

abstract class HOF_Class_Char_Abstract extends HOF_Class_Base_Extend_Root
{
	// ファイルポインタ
	protected $fp;
	protected $file;

	public $id;
	public $no;

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
	public $player = HOF_Class_Char::OWNER_SYSTEM;

	protected $CHAR_TYPE;
	protected $CHAR_TYPES;

	public $uniqid;

	public static $default_options = array();

	public function __construct($no, $options = array(), $owner = null, $player = null)
	{
		$this->initCharType();
		$owner !== null && $this->owner($owner);
		$player !== null && $this->player($player);

		$this->uniqid(true);
		$this->_extend_init();

		$this->init($no, $options);
	}

	protected function init($no, $options = array())
	{
		$this->no($no);

		$this->options($options);

		$this->initCharData($this->options());
	}

	public function uniqid($over = null)
	{
		if (!isset($this->uniqid) || $over)
		{
			static $uuid;
			if (!isset($uuid)) $uuid = md5($this->getCharType().$this->no().$this->birth.__METHOD__.HOF::ip());

			$this->uniqid = md5(uniqid($uuid.$this->uniqid.$this->birth, true));
		}

		return $this->uniqid;
	}

	public function __clone()
	{
		$this->isClone = true;

		$this->source()->birth = $this->birth = HOF_Helper_Char::uniqid_birth();
		$this->uniqid(true);

		$this->fp = false;
		$this->file = false;
	}

	public function getClone($owner = null, $player = null)
	{
		$char = clone $this;

		$char->owner($owner);
		$player !== null && $char->player($player);

		return $char;
	}

	public function isClone()
	{
		return (bool)$this->isClone;
	}

	public function file()
	{
		throw new Exception(sprintf('%s not Exists', __METHOD__));
	}

	protected function initCharData()
	{
		$this->source(true);

		$this->id(isset($this->source()->id) ? $this->source()->id : $this->source()->no);

		$this->setCharData(clone $this->source());
	}

	public function setCharData($data_attr)
	{
		if ($append = $this->option('append'))
		{
			$data_attr->merge((array)$append);
		}

		if ($strength = $this->option('strength'))
		{
			foreach (array('maxhp', 'hp', 'maxsp', 'sp', 'str', 'int', 'dex', 'spd', 'luk') as $k)
			{
				$data_attr->{$k} = round($data_attr->{$k} * $strength);
			}

			$data_attr->atk[0] = round($data_attr->atk[0] * $strength);
			$data_attr->atk[1] = round($data_attr->atk[1] * $strength);
		}

		if ($this->option('job'))
		{
			$data_attr->job = $this->option('job');
		}

		if ($this->option('gender'))
		{
			$data_attr->gender = $this->option('gender');
		}

		if ($this->option('icon'))
		{
			$data_attr->icon = $this->option('icon');
		}

		//-----------------------------------------

		if ($data_attr->summon || $this->option('summon'))
		{
			$this->setCharType(HOF_Class_Char::TYPE_SUMMON);
		}

		if (isset($data_attr->icon))
		{
			$this->icon = (string)$data_attr->icon;
		}

		if (isset($data_attr->data))
		{
			$this->data = $data_attr->data;
		}

		if ($data_attr->job)
		{
			$this->job = (string)$data_attr->job;

			if (!$this->hasExtend('HOF_Class_Char_Job'))
			{
				$this->extend('HOF_Class_Char_Job');
			}
		}

		$data_attr->img && $this->img = (string)$data_attr->img;

		$this->name = (string)$data_attr->name;
		$this->gender = (int)$data_attr->gender;
	}

	public function saveCharData()
	{
		return false;
	}

	public function options($options = array())
	{
		if (!isset($this->options))
		{
			$this->options = new HOF_Class_Array((array)self::$default_options);
		}

		if (!empty($options))
		{
			$this->options->merge((array)$options);
		}

		return $this->options;
	}

	public function option($k)
	{
		if (func_num_args() > 1)
		{
			$this->options()->$k = func_get_arg(1);
		}

		return $this->options()->$k;
	}

	public function source($over = false)
	{
		if (!isset($this->source) || $over)
		{
			if (!file_exists($this->file()))
			{
				throw new Exception(sprintf('%s:%s not Exists', $this->getCharType(), $this->no()));
			}

			$this->fp = HOF_Class_File::fplock_file($this->file());

			$data = HOF_Class_Yaml::load($this->fp);

			$this->source = new HOF_Class_Array($data);
		}

		return $this->source;
	}

	protected function _extend_init()
	{
		$this->extend('HOF_Class_Char_Attr');
		$this->extend('HOF_Class_Char_Pattern');
		$this->extend('HOF_Class_Char_View');
		$this->extend('HOF_Class_Char_Battle_Effect');
		$this->extend('HOF_Class_Char_Battle');
	}

	public function owner($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__} = $val ? $val : HOF_Class_Char::OWNER_SYSTEM;
			$this->user = $this->{__FUNCTION__};
		}

		return $this->{__FUNCTION__};
	}

	public function player($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__} = $val ? $val : HOF_Class_Char::OWNER_SYSTEM;
		}

		return $this->{__FUNCTION__};
	}

	public function id($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__} = $val;
		}

		return $this->{__FUNCTION__};
	}

	public function no($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__} = $val;
		}

		return $this->{__FUNCTION__};
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
				$sub_type = strtolower($sub_type);

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
		$k = __FUNCTION__;

		if (!isset($this->{$k}))
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_UNION);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isSummon()
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}))
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_SUMMON);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isMon()
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}))
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_MON);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isChar()
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}))
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_CHAR);
		}

		return $this->{$k};
	}

}
