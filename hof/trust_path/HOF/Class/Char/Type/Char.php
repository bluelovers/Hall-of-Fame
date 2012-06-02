<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (DATA_BASE_CHAR);
//require_once (CLASS_CHAR);

//class HOF_Class_Char_Type_Char extends char
class HOF_Class_Char_Type_Char extends HOF_Class_Char_Abstract
{

	/**
	 * 誰のキャラか?
	 */
	var $user;

	/**
	 * 基本的な情報
	 */
	var $job, $job_name, $birth, $exp;

	/**
	 * ステータスポイントとか
	 */
	var $statuspoint;
	var $skillpoint;

	function no($val = null)
	{
		if ($val !== null)
		{
			if (strpos($val, ':'))
			{
				list($type, $no) = explode(':', $val);

				if (!$no)
				{
					list($type, $no) = array($no, $type);
				}

				$this->option('type', $type);
				$this->option('type_no', $no);

				$no = $this->uniqid(true);
			}
			else
			{
				$no = $val;
			}

			$this->no = $no;
		}

		return $this->no;
	}

	public function file($over = null, $nochk = false)
	{
		if (!isset($this->file) || $over)
		{
			if (!isset($this->id))
			{
				$this->id($this->no());
			}

			$this->file = HOF_Helper_Char::char_file($this, $this->owner());

			if (!$nochk && !file_exists($this->file))
			{
				throw new ErrorException(sprintf('%s:%s not Exists', $this->getCharType(), $this->id));
			}
		}

		return $this->file;
	}

	function source($over = false)
	{
		$data = &$this->_cache_char_[__FUNCTION__ ];

		$type = $this->option('type');

		if (!isset($data[$type]) || $over)
		{
			switch ($type)
			{
				case 'char':
				case 'job':
					$list = HOF_Model_Char::getBaseCharList();
					$no = $this->option('type_no');
					//$append = $this->option('append');

					if (!in_array($no, $list))
					{
						$no = floor($no / 100) * 100;

						if (in_array($no, $list))
						{
							if (!$this->options()->append['job'])
							{
								$this->options()->append['job'] = $this->option('type_no');
							}
						}
						else
						{
							throw new Exception(sprintf('%s:%s not Exists', $this->getCharType(), $this->option('type_no')));
						}
					}

					$data[$type] = HOF_Model_Char::getBaseCharStatus($no);

					$data[$type]['data']['base']['type'] = $type;
					$data[$type]['data']['base']['no'] = $no;

					$data[$type]['id'] = $this->no();

					break;
				case 'mon':
					$no = $this->option('type_no');
					$data[$type] = HOF_Model_Char::getBaseMonster($no);

					$data[$type]['data']['base']['type'] = $type;
					$data[$type]['data']['base']['no'] = $no;

					$data[$type]['id'] = $this->no();

					//$data[$type]['icon'] = $data[$type]['img'];

					break;
				default:
					$this->fp = HOF_Class_File::fplock_file($this->file());

					$data[$type] = HOF_Class_Yaml::load($this->fp);

					if ($data[$type]['id'] != HOF_Helper_Char::char_id_by_file($this->file))
					{
						throw new ErrorException(sprintf('%s:%s Data fail', $this->getCharType(), $this->id));
					}

					break;
			}

			if (!$data[$type]['birth']) $data[$type]['birth'] = HOF_Helper_Char::uniqid_birth();

			$data[$type] = new HOF_Class_Array($data[$type]);
		}

		$this->source = $data[$type];

		/*
		debug($this->source, $this->no(), $this->options());
		exit();
		*/

		return $this->source;
	}

	function _extend_init()
	{
		parent::_extend_init();

		$this->extend('HOF_Class_Char_Job');
		$this->extend('HOF_Class_Skill_Tree');
	}

	/**
	 * キャラデータの保存
	 */
	function saveCharData()
	{
		if (!$this->owner() || (string )$this->owner() != (string )$this->user()->id)
		{
			throw new RuntimeException('Char User Null!');

			exit('Char User Null!');
		}

		// ユーザーが存在しない場合保存しない
		if (!is_dir(HOF_Helper_Char::user_path($this->owner()))) return false;

		HOF_Class_Yaml::save($this->fp ? $this->fp : $this->file(false, true), $this->DataSavingFormat());
		$this->fpclose();

		/*
		if (file_exists($file) && $this->fp)
		{
		//sleep(10);//ファイルロック確認用
		HOF_Class_File::fpwrite_file($this->fp, $this->DataSavingFormat());
		$this->fpclose();
		}
		else
		{
		HOF_Class_File::WriteFile($file, $this->DataSavingFormat());
		}
		*/
	}

	/**
	 * 誰のキャラか設定する
	 */
	/*
	function SetUser($user)
	{
	$this->user = $user;
	}
	*/

	function DataSavingFormat()
	{
		$Save = array(
			"id",
			"name",
			"gender",
			"job",
			"birth",
			"level",
			"exp",
			"statuspoint",
			"skillpoint",
			//"maxhp","hp","maxsp","sp",// (2007/9/30 保存しなくなった)
			"str",
			"int",
			"dex",
			"spd",
			"luk",

			'equip',

			/*
			"position",
			"guard",
			"pattern",
			*/
			'behavior',

			"skill",
			//"judge","action",

			"pattern_memo",
			//モンスター専用
			//"monster","land","family","monster_message"//保存する必要無くなった

			'user',

			);
		//$Save	= get_object_vars($this);

		$data = array();

		foreach ($Save as $k)
		{
			if (!isset($this->{$k})) continue;

			switch ($k)
			{
				case 'user':
				case 'owner':
				case 'player':
					$data[$k] = (string )$this->{$k};
					break;
				default:
					$data[$k] = $this->{$k};
					break;
			}
		}

		return $data;

		/*
		$text = HOF_Class_Yaml::dump($data);

		return $text;
		*/
	}

	function &user()
	{
		$user = HOF_Class_User::getInstance((string )$this->owner());

		return $user;
	}

	function unequip($slot)
	{

		if ($slot == 'all')
		{
			foreach ($this->equip as $k => $no)
			{
				if ($item = $this->unequip($k))
				{
					$list[] = $item;
				}
			}

			return (array )$list;
		}

		if (!in_array($slot, self::$map_equip))
		{
			return false;
		}

		if ($item = $this->equip->{$slot})
		{
			$this->equip->{$slot} = NULL;
		}

		return $item;
	}

	function hasEquip($slot)
	{
		if ($slot == 'all')
		{
			foreach ($this->equip as $k => $no)
			{
				if (!$no) continue;

				$list[] = $no;
			}

			return (array )$list;
		}

		if ($item = $this->equip->{$slot})
		{
			return $item;
		}

		return null;
	}

	/**
	 * アイテムを装備する(職が装備可能な物かどうかは調べない)
	 */
	function setEquip($item)
	{
		/**
		 * はずした装備
		 */
		$return = array();

		$fail = false;

		/**
		 * 現在の装備を仮に保存しておく。
		 */
		$old = array();

		foreach ($this->equip as $k => $no)
		{
			$v = $this->map_equip_allow[$k];

			if (!$v && $no)
			{
				$return[] = $this->unequip($k);
			}
			elseif ($v && $no)
			{
				$old[$k] = $no;
			}
		}

		/**
		 * 種類別
		 */
		switch ($item["type"])
		{

			case "Sword": //片手武器
			case "Dagger":
			case "Pike":
			case "Hatchet":
			case "Wand":
			case "Mace":
			case "TwoHandSword": //両手武器
			case "Spear":
			case "Axe":
			case "Staff":
			case "Bow":
			case "CrossBow":
			case "Whip":

				$equip_type = EQUIP_SLOT_MAIN_HAND;


				break;
			case "Shield": //盾
			case "MainGauche":
			case "Book":
				$equip_type = EQUIP_SLOT_OFF_HAND;


				break;
			case "Armor": //鎧
			case "Cloth":
			case "Robe":
				$equip_type = EQUIP_SLOT_ARMOR;
				break;
			case "Item":
				$equip_type = EQUIP_SLOT_ITEM;
				break;
			default:
				$fail = true;
				break;
		}

		if (!$fail && $equip_type && $this->map_equip_allow[$equip_type])
		{
			$return[] = $this->unequip($equip_type);

			switch ($equip_type)
			{
				case EQUIP_SLOT_MAIN_HAND:
				case EQUIP_SLOT_OFF_HAND:

					$chk = $equip_type == EQUIP_SLOT_MAIN_HAND ? EQUIP_SLOT_OFF_HAND : EQUIP_SLOT_MAIN_HAND;

					if ($this->equip->{$chk})
					{
						$_item = HOF_Model_Data::newItem($this->equip->{$chk});

						if ($item["dh"] || $_item["dh"])
						{
							$return[] = $this->unequip($chk);
						}
					}

					break;
			}

			$this->equip->{$equip_type} = $item["id"];
		}
		else
		{
			$fail = true;
		}

		if (!$fail)
		{
			if ($this->getHandle() < $this->getHandle(true))
			{
				$fail = true;

				$this->equip->{$equip_type} = null;

				/*
				// handle over
				foreach ($old as $key => $val)
				{
				// 元に戻す。
				$this->{$key} = $val;
				}
				*/

				//return false;
			}
		}

		$return = array_filter($return);

		return array($fail, $return);
	}

	/**
	 * 新ワザを追加する。
	 */
	function skill_add($no)
	{
		$this->skill[] = $no;
		sort($this->skill);
	}

	/**
	 * 名前を変える。
	 */
	function ChangeName($new, $true = false)
	{
		if ($this->name == $new) return false;

		$this->name = $new;

		return true;
	}

	/**
	 * キャラクターを消す
	 */
	function char_delete()
	{
		if (!file_exists($this->file)) return false;

		if ($this->fp)
		{
			fclose($this->fp);
			unset($this->fp);
		}

		HOF_Class_File::unlink($this->file);
	}

	//	パッシブスキルを読み込む
	function skill_passive()
	{
		$passive_list = HOF_Model_Data::getSkillPassiveList();

		// PassiveSkill
		foreach ($this->skill as $no)
		{
			if (!in_array($no, $passive_list)) continue;

			$skill = HOF_Model_Data::getSkill($no);

			//	能力値上昇系
			if ($skill["P_MAXHP"]) $this->P_MAXHP += $skill["P_MAXHP"];
			if ($skill["P_MAXSP"]) $this->P_MAXSP += $skill["P_MAXSP"];
			if ($skill["P_STR"]) $this->P_STR += $skill["P_STR"];
			if ($skill["P_INT"]) $this->P_INT += $skill["P_INT"];
			if ($skill["P_DEX"]) $this->P_DEX += $skill["P_DEX"];
			if ($skill["P_SPD"]) $this->P_SPD += $skill["P_SPD"];
			if ($skill["P_LUK"]) $this->P_LUK += $skill["P_LUK"];

			//	特殊技能など($this->SPECIAL)
			if ($skill["HealBonus"]) $this->SPECIAL["HealBonus"] += $skill["HealBonus"]; //....
		}
	}

	function setBattleVariable()
	{
		if ($this->_cache_char_['init'][__FUNCTION__ ]) return false;

		$this->skill_passive();
		$this->CalcEquips();

		parent::setBattleVariable();
	}

	//	キャラの攻撃力と防御力,装備性能を計算する
	function CalcEquips()
	{
		if ($this->isMon()) return false; //mobは設定せんでいい

		$this->atk = array(0, 0);
		$this->def = array(
			0,
			0,
			0,
			0);

		foreach ($this->equip as $place => $no)
		{
			$allow = $this->map_equip_allow[$place];

			if (!$this->equip->{$place} || !$allow) continue;
			// 武器タイプの記憶

			$item = HOF_Model_Data::getItemData($this->equip->{$place});
			if ($place == EQUIP_SLOT_MAIN_HAND) $this->WEAPON = $item["type"];
			$this->atk[0] += $item[atk][0]; //物理攻撃力
			$this->atk[1] += $item[atk][1]; //魔法〃
			$this->def[0] += $item[def][0]; //物理防御(÷)
			$this->def[1] += $item[def][1]; //〃(－)
			$this->def[2] += $item[def][2]; //魔法防御(÷)
			$this->def[3] += $item[def][3]; //〃(－)

			$this->P_MAXHP += $item["P_MAXHP"];
			$this->M_MAXHP += $item["M_MAXHP"];
			$this->P_MAXSP += $item["P_MAXSP"];
			$this->M_MAXSP += $item["M_MAXSP"];

			$this->P_STR += $item["P_STR"];
			$this->P_INT += $item["P_INT"];
			$this->P_DEX += $item["P_DEX"];
			$this->P_SPD += $item["P_SPD"];
			$this->P_LUK += $item["P_LUK"];

			if ($item["P_SUMMON"]) $this->GetSpecial("Summon", $item["P_SUMMON"]);
			// 防御無視の攻撃力
			if ($item["P_PIERCE"]) $this->GetSpecial("Pierce", $item["P_PIERCE"]);
		}
	}

	//	handle計算
	function getHandle($equip = false)
	{
		if ($equip)
		{
			$handle = 0;

			foreach ($this->equip as $k => $no)
			{
				if (!$no) continue;

				$_item = HOF_Model_Data::newItem($no);

				$handle += $_item->handle();
			}

			return $handle;
		}

		$handle = 5 + floor($this->level / 10) + floor($this->dex / 5);
		return $handle;
	}

	//	ポイントを消費して技を覚える。
	function skill_learn($no)
	{
		//もし習得済みなら?
		if (in_array($no, $this->skill)) return array(false, "{$skill[name]} は修得済み.");

		//include_once (DATA_SKILL_TREE);
		$tree = $this->skill_tree();

		//習得可能技に覚えようとしてるヤツなけりゃ終了
		if (!in_array($no, $tree)) return array(false, "スキルツリーに無い");

		$skill = HOF_Model_Data::getSkill($no);

		if ($this->skill_point_use($skill["learn"]))
		{
			$this->skill_add($skill["no"]);

			//$this->saveCharData();
			return array(true, $this->Name() . " は {$skill[name]} を修得した。");
		}
		else
		{
			return array(false, "スキルポイント不足");
		}
	}


	//	スキルポイントを消費する
	function skill_point_use($no)
	{
		if ($no <= $this->skillpoint)
		{
			$this->skillpoint -= $no;
			return true;
		}
		return false;
	}

	function id($id = null)
	{
		if (!isset($this->id))
		{
			if ($this->file)
			{
				$this->id = HOF_Helper_Char::char_id_by_file($this->file);
			}
			else
			{
				$this->id = $this->uniqid(true);
			}
		}

		if ($id !== null)
		{
			$this->id = $id ? $id : null;
		}

		return $this->id;
	}

	//	キャラの変数をセットする。
	function setCharData($data_attr)
	{
		parent::setCharData($data_attr);

		if ($this->file)
		{
			$this->id(HOF_Helper_Char::char_id_by_file($this->file));
		}
		else
		{
			$this->id($data_attr["id"] ? $data_attr["id"] : false);
		}

		$this->birth = (string )$data_attr["birth"];

		$this->statuspoint = (int)$data_attr["statuspoint"];
		$this->skillpoint = (int)$data_attr["skillpoint"];

		$this->jobdata();

		if (isset($data_attr["maxhp"]) && isset($data_attr["hp"]) && isset($data_attr["maxsp"]) && isset($data_attr["sp"]))
		{
			/*
			$this->maxhp = (int)$data_attr["maxhp"];
			$this->hp = (int)$data_attr["hp"];
			$this->maxsp = (int)$data_attr["maxsp"];
			$this->sp = (int)$data_attr["sp"];
			*/
		}
		else
		{
			// HPSPを設定。HPSPを回復。そういうゲームだから…
			$this->hpsp(true);

			/*
			$this->hp = (int)$this->maxhp;
			$this->sp = (int)$this->maxsp;
			*/

			//$this->hp = isset($this->hp) ? ($this->hp > 0 ? (int)$this->hp : 0) : (int)$this->maxhp;
			//$this->sp = isset($this->sp) ? ($this->sp > 0 ? (int)$this->sp : 0) : (int)$this->maxsp;
		}

		$this->equip = HOF_Helper_Object::ArrayObject((array )$data_attr["equip"]);

		if ($data_attr["pattern_memo"]) $this->pattern_memo = (array )$data_attr["pattern_memo"];

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}

}
