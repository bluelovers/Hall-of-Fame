<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

class char extends HOF_Class_Char_Base
{

	// ファイルポインタ
	var $fp;
	var $file;
	var $Number;

	// 誰のキャラか?
	var $user;

	/*
	基本的な情報
	*/
	var $name, $gender, $job, $job_name, $img, $birth, $level, $exp;

	// ステータスポイントとか
	var $statuspoint;
	var $skillpoint;
	// 装備
	var $weapon, $shield, $armor, $item;
	// 戦闘その他
	var $position, $guard;
	// スキル
	var $skill;

	// 戦闘用変数(BattleVariable) データには保存されない。
	var $team;
	var $IMG;

	var $POSITION;

	/*
	PoisonResist 毒抵抗
	HealBonus .
	Barrier
	Undead
	*/
	var $WEAPON; //武器タイプ


	//


	//	特殊技能?の追加
	function GetSpecial($name, $value)
	{
		if (is_bool($value))
		{
			$this->SPECIAL["$name"] = $value;
		}
		else
			if (is_array($value))
			{
				foreach ($value as $key => $val)
				{
					$this->SPECIAL["$name"]["$key"] += $val;
				}
			}
			else
			{
				$this->SPECIAL["$name"] += $value;
			}
	}


	//	誰のキャラか設定する
	function SetUser($user)
	{
		$this->user = $user;
	}


	//


	//	名前を返す
	function Name($string = false)
	{
		if ($string) return "<span class=\"{$string}\">{$this->name}</span>";
		else  return $this->name;
	}

	//

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
		// 再読み込みを防止できるか?
		if (isset($this->IMG)) return false;

		//$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);

		// パッシブスキルを読む
		$this->LoadPassiveSkills();
		$this->CalcEquips();

		$this->team = $team;
		$this->IMG = $this->img;
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


	//	戦闘時のチームを設定(あんまり使ってない)
	function SetTeam($no)
	{
		$this->team = $no;
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
		include_once (DATA_SKILL_TREE);
		$tree = LoadSkillTree($this);

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

	//	経験値を出す(モンスターだけ?)
	function DropExp()
	{
		if (isset($this->exphold))
		{
			$exp = $this->exphold;
			$this->exphold = round($exp / 2);
			return $exp;
		}
		else
		{
			return false;
		}
	}

	//	お金を出す(モンスターだけ?)
	function DropMoney()
	{
		if (isset($this->moneyhold))
		{
			$money = $this->moneyhold;
			$this->moneyhold = 0;
			return $money;
		}
		else
		{
			return false;
		}
	}

	//	アイテムを落とす(モンスターだけ?)
	function DropItem()
	{
		if ($this->itemdrop)
		{
			$item = $this->itemdrop;
			// 一度落としたアイテムは消す
			$this->itemdrop = false;
			return $item;
		}
		else
		{
			return false;
		}
	}


	//

	//	キャラの変数をセットする。
	function SetCharData(&$data)
	{
		$this->name = $data["name"];
		$this->gender = $data["gender"];
		$this->birth = $data["birth"];
		$this->level = $data["level"];
		$this->exp = $data["exp"];
		$this->statuspoint = $data["statuspoint"];
		$this->skillpoint = $data["skillpoint"];

		$this->job = $data["job"];
		$this->jobdata();

		if ($data["img"]) $this->img = $data["img"];

		$this->str = $data["str"];
		$this->int = $data["int"];
		$this->dex = $data["dex"];
		$this->spd = $data["spd"];
		$this->luk = $data["luk"];

		if (isset($data["maxhp"]) && isset($data["hp"]) && isset($data["maxsp"]) && isset($data["sp"]))
		{
			$this->maxhp = $data["maxhp"];
			$this->hp = $data["hp"];
			$this->maxsp = $data["maxsp"];
			$this->sp = $data["sp"];
		}
		else
		{
			// HPSPを設定。HPSPを回復。そういうゲームだから…
			$this->hpsp();
			$this->hp = $this->maxhp;
			$this->sp = $this->maxsp;
		}

		$this->weapon = $data["weapon"];
		$this->shield = $data["shield"];
		$this->armor = $data["armor"];
		$this->item = $data["item"];

		$this->position = $data["position"];
		$this->guard = $data["guard"];

		$this->skill = (is_array($data["skill"]) ? $data["skill"] : explode("<>", $data["skill"]));

		$this->pattern = $data["pattern"];

		if ($data["pattern_memo"]) $this->pattern_memo = $data["pattern_memo"];

		//モンスター専用
		if ($this->monster = $data["monster"])
		{
			$this->exphold = $data["exphold"];
			$this->moneyhold = $data["moneyhold"];
			$this->itemdrop = $data["itemdrop"];
			$this->atk = $data["atk"];
			$this->def = $data["def"];
			$this->SPECIAL = $data["SPECIAL"];
		}
		if ($data["summon"]) $this->summon = $data["summon"];

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}
}


?>