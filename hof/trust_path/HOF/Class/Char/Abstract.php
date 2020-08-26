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
	 * @var $reward[exphold] 経験値
	 * @var $reward[moneyhold] お金
	 * @var $reward[itemdrop] 落とすアイテム
	 */
	public $reward;

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
			if (!isset($uuid)) $uuid = md5($this->getCharType() . $this->no() . $this->birth . __METHOD__ . HOF::ip());

			$this->uniqid = md5(uniqid($uuid . $this->uniqid . $this->birth, true));
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

		$this->refreshExtend();
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

		$this->id(isset($this->source->id) ? $this->source->id : $this->source->no);

		$this->setCharData(clone $this->source());
	}

	public function setCharData($data_attr, $data = array())
	{
		if ($append = $this->option('append'))
		{
			$data_attr->merge((array )$append);
		}

		if ($strength = $this->option('strength'))
		{
			foreach (array(
				'maxhp',
				'hp',
				'maxsp',
				'sp',
				'str',
				'int',
				'dex',
				'spd',
				'luk') as $k)
			{
				if (!isset($data_attr->{$k})) continue;

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

		if ($this->option('name'))
		{
			$data_attr->name = $this->option('name');
		}

		if ($this->option('level'))
		{
			$data_attr->level = $this->option('level');
		}

		//-----------------------------------------

		if ($data_attr->summon || $this->option('summon'))
		{
			$this->setCharType(HOF_Class_Char::TYPE_SUMMON);
		}

		if (isset($data_attr->icon))
		{
			$this->icon = (string )$data_attr->icon;
		}

		if (isset($data_attr->data))
		{
			$this->data = (array )$data_attr->data;
		}

		$this->level = max(1, (int)$data['level'], (int)$data_attr->level);
		$this->exp = isset($data['exp']) ? (int)$data['exp'] : (int)$data_attr->exp;

		$this->gender = (int)$data_attr->gender;

		$data_attr->img && $this->img = (string )$data_attr->img;

		if (!isset($data_attr->job))
		{
			$data_attr->job = $this->isMon(true) ? 901 : 900;
		}

		if ($data_attr->job)
		{
			$this->job = (string )$data_attr->job;

			if (!$this->hasExtend('HOF_Class_Char_Job'))
			{
				$this->extend('HOF_Class_Char_Job');
			}
		}

		$data_attr->name && $this->name = (string )$data_attr->name;

		if ($this->isSummon())
		{
			$this->reward = array();
		}
		else
		{
			$this->reward = (array )$data_attr->reward;
		}

		$this->skill = (array )$data_attr->skill;

		$this->behavior = (array )$data_attr->behavior;

		/*
		$this->str = (int)$data_attr->str;
		$this->int = (int)$data_attr->int;
		$this->dex = (int)$data_attr->dex;
		$this->spd = (int)$data_attr->spd;
		$this->luk = (int)$data_attr->luk;
		*/

		foreach (array(
			'maxhp',
			'hp',
			'maxsp',
			'sp',
			'str',
			'int',
			'dex',
			'spd',
			'luk') as $k)
		{
			if (!isset($data_attr->{$k})) continue;

			$this->{$k} = $data_attr->{$k};
		}
	}

	public function setBattleVariable()
	{
		$this->_cache_char_['init'][__FUNCTION__ ] = true;

		HOF_Helper_Math::rand_seed();

		$this->isChar(true);
		$this->isMon(true);
		$this->isSummon(true);
		$this->isUnion(true);

		if ($this->isSummon())
		{
			$this->reward = array();
		}
		else
		{
			$this->reward['exphold'] = isset($this->reward['exphold']) ? (int)$this->reward['exphold'] : 1;
			$this->reward['moneyhold'] = isset($this->reward['moneyhold']) ? (int)$this->reward['moneyhold'] : 1;

			if (!$this->isChar())
			{
				$this->reward['exphold'] = round((int)$this->reward['exphold'] * EXP_RATE);
				$this->reward['moneyhold'] = round((int)$this->reward['moneyhold'] * MONEY_RATE);

				/**
				 * 落とすアイテムをもたせる
				 */
				if (is_array($this->reward["itemtable"]))
				{
					$prob = mt_rand(1, 10000);
					$sum = 0;

					foreach ($this->reward["itemtable"] as $itemno => $upp)
					{
						$sum += $upp;
						if ($prob <= $sum)
						{
							$this->reward["itemdrop"] = $itemno;
							break;
						}
					}
				}
			}
		}

		/**
		 * 生存状態にする
		 */
		$this->STATE = STATE_ALIVE;

		/**
		 * 前列後列の設定
		 */
		if (!$this->behavior['position'])
		{
			$this->POSITION = (mt_rand(0, 1) ? POSITION_FRONT : POSITION_BACK);
		}
		else
		{
			$this->POSITION = $this->behavior['position'];
		}

		$this->expect = false;
		$this->ActCount = 0;
		$this->JdgCount = array();

		$this->level_fix();

		$maxhp = $this->maxhp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
		$this->MAXHP = round($maxhp);

		$hp = $this->hp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
		$this->HP = round($hp);

		$maxsp = $this->maxsp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
		$this->MAXSP = round($maxsp);

		$sp = $this->sp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
		$this->SP = round($sp);

		$this->HP = min($this->HP, $this->MAXHP);
		$this->SP = min($this->HP, $this->MAXHP);

		$this->STR = $this->str + $this->P_STR;
		$this->INT = $this->int + $this->P_INT;
		$this->DEX = $this->dex + $this->P_DEX;
		$this->SPD = $this->spd + $this->P_SPD;
		$this->LUK = $this->luk + $this->P_LUK;

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}

	public function saveCharData()
	{
		return false;
	}

	public function options($options = array())
	{
		if (!isset($this->options))
		{
			$this->options = new HOF_Class_Array_Prop((array )self::$default_options);
		}

		if (!empty($options))
		{
			$this->options->merge((array )$options);
		}

		return $this->options;
	}

	public function option($k)
	{
		if (!isset($this->options))
		{
			$this->options();
		}

		if (func_num_args() > 1)
		{
			$this->options->$k = func_get_arg(1);
		}

		return $this->options->$k;
	}

	public function source($over = false)
	{
		if (!isset($this->source) || $over)
		{
			if (!file_exists($this->file()))
			{
				throw new Exception(sprintf('%s:%s not Exists', $this->getCharType(), $this->no()));
			}

			$this->fp = HOF_Class_File::fplock_file($this->file);

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
			$this->{__FUNCTION__ } = $val ? $val : HOF_Class_Char::OWNER_SYSTEM;
			$this->user = $this->{__FUNCTION__ };
		}

		return $this->{__FUNCTION__ };
	}

	public function player($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__ } = $val ? $val : HOF_Class_Char::OWNER_SYSTEM;
		}

		return $this->{__FUNCTION__ };
	}

	public function id($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__ } = $val;
		}

		return $this->{__FUNCTION__ };
	}

	public function no($val = null)
	{
		if ($val !== null)
		{
			$this->{__FUNCTION__ } = $val;
		}

		return $this->{__FUNCTION__ };
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
	    $type ="";
		sscanf(get_class($this), 'HOF_Class_Char_Type_%s', $type);

		$this->CHAR_TYPE = $type;
		$types = explode('_', HOF::putintoPathParts($type));

		$this->CHAR_TYPES = array_fill_keys((array )$types, true);

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
	public function isUnion($over = null)
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}) || $over)
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_UNION);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isSummon($over = null)
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}) || $over)
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_SUMMON);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isMon($over = null)
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}) || $over)
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_MON);
		}

		return $this->{$k};
	}

	/**
	 * @return bool
	 */
	public function isChar($over = null)
	{
		$k = __FUNCTION__;

		if (!isset($this->{$k}) || $over)
		{
			$this->{$k} = $this->hasCharType(HOF_Class_Char::TYPE_CHAR);
		}

		return $this->{$k};
	}

	function level_fix($lv_add = 0)
	{
		if ($this->isChar(true)) return false;

		$old['lv'] = $this->level;

		$this->level = max(1, $this->level + $lv_add);

		$div = bcdiv($this->level, $old['lv'], 3);

		if (0 !== $cmp = bccomp($div, 1))
		{
			if ($cmp > 0 && $div > 10)
			{
				$div = bcsub($div, rand(0, 5), 3);
			}

			foreach (array(
				'str',
				'int',
				'dex',
				'spd',
				'luk'//,

				//'atk',
				//'def',
				) as $k)
			{
				if (!isset($this->{$k})) continue;

				$div2 = ($cmp > 0 && $div > 10) ? bcmul($div, bcdiv(mt_rand(50, 175), 100, 3), 3) : $div;

				//$old[$k] = $this->{$k};

				if (is_array($this->{$k}))
				{
					foreach ($this->{$k} as &$v)
					{
						$v = ceil(bcmul($v, $div2));
					}
				}
				else
				{
					$this->{$k} = ceil(bcmul($this->{$k}, $div2));
				}

				//$new[$k] = $this->{$k};
			}
		}

		//debug($div, $div2, $this->level, $old, $new);

		$this->hpsp(-1);
	}

}
