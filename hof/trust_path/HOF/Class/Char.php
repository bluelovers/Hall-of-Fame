<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (DATA_BASE_CHAR);
//require_once (CLASS_CHAR);

//class HOF_Class_Char extends char
class HOF_Class_Char extends HOF_Class_Char_Base
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

	/**
	 * 装備
	 */
	var $weapon, $shield, $armor, $item;

	function __construct($file = false)
	{
		$this->_extend_init();

		if (!$file) return 0;

		$this->file = $file;

		$this->id = HOF_Helper_Char::char_id_by_file($this->file);

		$this->fp = HOF_Class_File::fplock_file($this->file);

		$data = HOF_Class_Yaml::load($this->fp);

		$this->SetCharData($data);
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
	function SaveCharData($id = false)
	{
		$id = $id ? $id : $this->user;

		$dir = HOF_Helper_Char::user_path($id);

		// ユーザーが存在しない場合保存しない
		if (!is_dir($dir)) return false;

		$file = HOF_Helper_Char::char_file($this, $id);

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
	}

	/**
	 * 誰のキャラか設定する
	 */
	function SetUser($user)
	{
		$this->user = $user;
	}

	function DataSavingFormat()
	{
		$Save = array(
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
			"weapon",
			"shield",
			"armor",
			"item",
			"position",
			"guard",
			"skill",
			//"judge","action",
			"pattern",
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

			$data[$k] = $this->{$k};
		}

		$text = HOF_Class_Yaml::dump($data);

		return $text;
	}

	function &user()
	{
		$user = HOF_Class_User::getInstance($this->user);

		return $user;
	}

	function unequip($spot)
	{

		if ($spot == 'all')
		{
			foreach (array_keys(self::$map_equip) as $k)
			{
				if ($item = $this->unequip($k))
				{
					$list[] = $item;
				}
			}

			return (array )$list;
		}

		if (!in_array($spot, self::$map_equip))
		{
			return false;
		}

		if ($item = $this->{$spot})
		{
			$this->{$spot} = NULL;
		}

		return $item;
	}

	/**
	 * アイテムを装備する(職が装備可能な物かどうかは調べない)
	 */
	function Equip($item)
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

		foreach (array_keys(self::$map_equip) as $k)
		{
			$v = $this->map_equip_allow[$k];

			if (!$v && $this->{$k})
			{
				$return[] = $this->unequip($k);
			}
			elseif ($v && $this->{$k})
			{
				$old[$k] = $this->{$k};
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

				$equip_type = 'weapon';


				break;
			case "Shield": //盾
			case "MainGauche":
			case "Book":
				$equip_type = 'shield';


				break;
			case "Armor": //鎧
			case "Cloth":
			case "Robe":
				$equip_type = 'armor';
				break;
			case "Item":
				$equip_type = 'item';
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
				case 'weapon':

					if ($item["dh"] && $this->shield)
					{
						/**
						 * 両手持ちの武器の場合。
						 * 盾を装備していたらはずす。
						 */
						$return[] = $this->unequip('shield');
					}

					break;

				case 'shield':

					if ($this->weapon)
					{
						//両手武器ならそれははずす
						$weapon = HOF_Model_Data::newItem($this->weapon);

						if ($weapon["dh"])
						{
							$return[] = $this->unequip('weapon');
						}
					}

					break;
			}

			$this->{$equip_type} = $item["id"];
		}
		else
		{
			$fail = true;
		}

		if (!$fail)
		{
			$handle = 0;

			foreach (array_keys(self::$map_equip) as $k)
			{
				$_item = HOF_Model_Data::newItem($this->{$k});

				$handle += $_item->handle();
			}

			if ($this->GetHandle() < $handle)
			{
				$fail = true;

				// handle over
				foreach ($old as $key => $val)
				{
					// 元に戻す。
					$this->{$key} = $val;
				}

				//return false;
			}
		}

		$return = array_filter($return);

		return array($fail, $return);
	}

	/**
	 * 必要経験値
	 */
	function CalcExpNeed()
	{
		switch ($this->level)
		{
			case 40:
				$no = 30000;
				break;
			case 41:
				$no = 40000;
				break;
			case 42:
				$no = 50000;
				break;
			case 43:
				$no = 60000;
				break;
			case 44:
				$no = 70000;
				break;
			case 45:
				$no = 80000;
				break;
			case 46:
				$no = 100000;
				break;
			case 47:
				$no = 250000;
				break;
			case 48:
				$no = 500000;
				break;
			case 49:
				$no = 999990;
				break;
			case 50:
			case (50 <= $this->level):
				$no = "MAX";
				break;
			case (21 < $this->level):
				$no = 2 * pow($this->level, 3) + 100 * $this->level + 100;
				$no -= substr($no, -2);
				$no /= 5;
				break;
			default:
				$no = pow($this->level - 1, 2) / 2 * 100 + 100;
				$no /= 5;
				break;
		}

		return $no;
	}

	/**
	 * 新ワザを追加する。
	 */
	function GetNewSkill($no)
	{
		$this->skill[] = $no;
		asort($this->skill);
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
	function DeleteChar()
	{
		if (!file_exists($this->file)) return false;

		if ($this->fp)
		{
			fclose($this->fp);
			unset($this->fp);
		}

		HOF_Class_File::unlink($this->file);
	}

	//	経験値を得る
	function GetExp($exp)
	{
		if ($this->monster) return false; //モンスターは経験値を得ない
		if (MAX_LEVEL <= $this->level) return false; //最大レベルの場合経験値を得ない

		$this->exp += $exp;
		$need = $this->CalcExpNeed($this->level); // 必要な経験値
		if ($need <= $this->exp)
		{
			$this->LevelUp();
			return true;
		}
	}

	//	レベルあげる時の処理
	function LevelUp()
	{
		$this->exp = 0;
		$this->level++;
		$this->statuspoint += GET_STATUS_POINT; //ステポをもらえる。
		$this->skillpoint += GET_SKILL_POINT;
	}


	//	パッシブスキルを読み込む
	function LoadPassiveSkills()
	{
		// PassiveSkill
		foreach ($this->skill as $no)
		{
			if ($no < 7000 || 8000 <= $no) continue;

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

	function SetBattleVariable($team = false)
	{
		if ($this->_cache_char_['init'][__FUNCTION__ ]) return false;

		$this->_cache_char_['init'][__FUNCTION__ ] = true;

		// パッシブスキルを読む
		$this->LoadPassiveSkills();
		$this->CalcEquips();

		$this->team = $team;

		$maxhp += $this->maxhp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
		$this->MAXHP = round($maxhp);
		$hp += $this->hp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
		$this->HP = round($hp);
		$maxsp += $this->maxsp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
		$this->MAXSP = round($maxsp);
		$sp += $this->sp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
		$this->SP = round($sp);
		$this->STR = $this->str + $this->P_STR;
		$this->INT = $this->int + $this->P_INT;
		$this->DEX = $this->dex + $this->P_DEX;
		$this->SPD = $this->spd + $this->P_SPD;
		$this->LUK = $this->luk + $this->P_LUK;
		$this->POSITION = $this->position;
		$this->STATE = 0; //生存状態にする

		$this->expect = false; //(数値=詠唱中 false=待機中)
		$this->ActCount = 0; //行動回数
		$this->JdgCount = array(); //決定した判断の回数
	}

	//	キャラの攻撃力と防御力,装備性能を計算する
	function CalcEquips()
	{
		if ($this->monster) return false; //mobは設定せんでいい
		$equip = array(
			"weapon",
			"shield",
			"armor",
			"item"); //装備箇所
		$this->atk = array(0, 0);
		$this->def = array(
			0,
			0,
			0,
			0);
		foreach ($equip as $place)
		{
			if (!$this->{$place}) continue;
			// 武器タイプの記憶

			$item = HOF_Model_Data::getItemData($this->{$place});
			if ($place == "weapon") $this->WEAPON = $item["type"];
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
	function GetHandle()
	{
		$handle = 5 + floor($this->level / 10) + floor($this->dex / 5);
		return $handle;
	}

	//	ポイントを消費して技を覚える。
	function LearnNewSkill($no)
	{
		//include_once (DATA_SKILL_TREE);
		$tree = $this->skill_tree();

		//習得可能技に覚えようとしてるヤツなけりゃ終了
		if (!in_array($_POST["newskill"], $tree)) return array(false, "スキルツリーに無い");
		$skill = HOF_Model_Data::getSkill($no);
		//もし習得済みなら?
		if (in_array($no, $this->skill)) return array(false, "{$skill[name]} は修得済み.");
		if ($this->UseSkillPoint($skill["learn"]))
		{
			$this->GetNewSkill($skill["no"]);
			//$this->SaveCharData();
			return array(true, $this->Name() . " は {$skill[name]} を修得した。");
		}
		else  return array(false, "スキルポイント不足");
	}


	//	スキルポイントを消費する
	function UseSKillPoint($no)
	{
		if ($no <= $this->skillpoint)
		{
			$this->skillpoint -= $no;
			return true;
		}
		return false;
	}

	//	キャラの変数をセットする。
	function SetCharData(&$data_attr)
	{
		$this->name = $data_attr["name"];
		$this->gender = $data_attr["gender"];
		$this->birth = $data_attr["birth"];
		$this->level = $data_attr["level"];
		$this->exp = $data_attr["exp"];
		$this->statuspoint = $data_attr["statuspoint"];
		$this->skillpoint = $data_attr["skillpoint"];

		$this->job = $data_attr["job"];
		$this->jobdata();

		if ($data_attr["img"]) $this->img = $data_attr["img"];

		$this->str = $data_attr["str"];
		$this->int = $data_attr["int"];
		$this->dex = $data_attr["dex"];
		$this->spd = $data_attr["spd"];
		$this->luk = $data_attr["luk"];

		if (isset($data_attr["maxhp"]) && isset($data_attr["hp"]) && isset($data_attr["maxsp"]) && isset($data_attr["sp"]))
		{
			$this->maxhp = $data_attr["maxhp"];
			$this->hp = $data_attr["hp"];
			$this->maxsp = $data_attr["maxsp"];
			$this->sp = $data_attr["sp"];
		}
		else
		{
			// HPSPを設定。HPSPを回復。そういうゲームだから…
			$this->hpsp();
			$this->hp = $this->maxhp;
			$this->sp = $this->maxsp;
		}

		$this->weapon = $data_attr["weapon"];
		$this->shield = $data_attr["shield"];
		$this->armor = $data_attr["armor"];
		$this->item = $data_attr["item"];

		$this->position = $data_attr["position"];
		$this->guard = $data_attr["guard"];

		$this->skill = (is_array($data_attr["skill"]) ? $data_attr["skill"] : explode("<>", $data_attr["skill"]));

		$this->pattern = $data_attr["pattern"];

		if ($data_attr["pattern_memo"]) $this->pattern_memo = $data_attr["pattern_memo"];

		if ($data_attr["summon"]) $this->summon = $data_attr["summon"];

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}

}
