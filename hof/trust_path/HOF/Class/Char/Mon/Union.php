<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//require_once (CLASS_UNION);

//class HOF_Class_Char_Mon_Union extends union
class HOF_Class_Char_Mon_Union extends HOF_Class_Char
{

	var $file;
	var $fp;

	var $UnionName;
	var $MonsterNumber;
	var $LastDefeated;

	var $Slave;
	var $Union = true;
	var $UnionNo;
	var $UnionLand;
	var $LevelLimit;
	/*
	Unionモンスターはダメージを受けると経験値を渡す。
	なので、全開のHPと差分を取って死亡判定時に経験値を渡すことにする。
	*/
	var $LastHP;

	// モンスター専用の変数
	var $monster = true;
	var $exphold; //経験値
	var $moneyhold; //お金
	var $itemdrop; //落とすアイテム

	/**
	 * コンストラクタ
	 */
	function __construct($file = false)
	{
		$this->_extend_init();

		$this->LoadData($file);
	}

	function _extend_init()
	{
		$this->extend('HOF_Class_Char_Pattern');
		$this->extend('HOF_Class_Char_View');
		$this->extend('HOF_Class_Char_Battle');
	}

	function LoadData($file)
	{
		if (!file_exists($file)) return false;

		list($this->file_name, $this->file_ext) = HOF_Class_File::basename($file);

		$this->file = $file;
		$this->fp = HOF_Class_File::fplock_file($this->file);

		$this->UnionNo = substr(basename($file), 0, 4);

		if ($this->file_ext == '.dat')
		{
			$data = HOF_Class_File::ParseFileFP($this->fp);
		}
		else
		{
			$data = HOF_Class_Yaml::parse(stream_get_contents($this->fp));
		}

		$this->SetCharData($data);

		return true;
	}

	//	キャラの変数をセットする。
	function SetCharData(&$data)
	{
		$this->MonsterNumber = $data["MonsterNumber"];
		$this->LastDefeated = $data["LastDefeated"];

		$monster = HOF_Model_Char::getBaseMonster($this->MonsterNumber);

		$this->UnionName = $monster["UnionName"];

		$this->name = $monster["name"];
		$this->level = $monster["level"];

		if ($monster["img"]) $this->img = $monster["img"];

		$this->str = $monster["str"];
		$this->int = $monster["int"];
		$this->dex = $monster["dex"];
		$this->spd = $monster["spd"];
		$this->luk = $monster["luk"];

		$this->maxhp = $monster["maxhp"];
		$this->hp = $data["HP"];
		$this->maxsp = $monster["maxsp"];
		$this->sp = $data["SP"];

		$this->position = $monster["position"];
		$this->guard = $monster["guard"];

		//モンスター専用
		$this->monster = true;
		$this->exphold = $monster["exphold"];
		$this->moneyhold = $monster["moneyhold"];
		$this->itemdrop = $monster["itemdrop"];
		$this->atk = $monster["atk"];
		$this->def = $monster["def"];
		$this->SPECIAL = $monster["SPECIAL"];

		$this->Slave = $monster["Slave"];
		$this->UnionLand = $monster["land"];
		$this->LevelLimit = $monster["LevelLimit"];

		// 時間が経過して復活する処理。
		$Now = time();
		$Passed = $this->LastDefeated + $monster["cycle"];
		if ($Passed < $Now && !$this->hp)
		{
			$this->hp = $this->maxhp;
			$this->sp = $this->maxsp;
		}
		$this->LastHP = $data["HP"]; //差分を取るためのHP。

		$this->pattern = $monster["pattern"];


	}

	//	戦闘用の変数
	function SetBattleVariable($team = false)
	{
		// 再読み込みを防止できる か?
		if (isset($this->IMG)) return false;

		$this->team = $team; //これ必要か?
		$this->IMG = $this->img;
		$this->MAXHP = $this->maxhp;
		$this->HP = $this->hp;
		$this->MAXSP = $this->maxsp;
		$this->SP = $this->sp;
		$this->STR = $this->str + $this->P_STR;
		$this->INT = $this->int + $this->P_INT;
		$this->DEX = $this->dex + $this->P_DEX;
		$this->SPD = $this->spd + $this->P_SPD;
		$this->LUK = $this->luk + $this->P_LUK;
		$this->POSITION = $this->position;
		$this->STATE = STATE_ALIVE; //生存状態にする

		$this->expect = false; //(数値=詠唱中 false=待機中)
		$this->ActCount = 0; //行動回数
		$this->JdgCount = array(); //決定した判断の回数

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}

	/**
	 * キャラデータの保存
	 */
	function SaveCharData()
	{
		if (!file_exists($this->file)) return false;

		$Save = array(
			"MonsterNumber",
			"LastDefeated",
			"HP",
			"SP",
			);

		$data = array();

		foreach ($Save as $k)
		{
			if (!isset($this->{$k})) continue;

			if ($this->file_ext == '.dat')
			{
				$data[$k] = "$k=" . (is_array($this->{$k}) ? implode("<>", $this->{$k}) : $this->{$k});
			}
			else
			{
				$data[$k] = $this->{$k};
			}
		}

		if ($this->file_ext == '.dat')
		{
			$text = implode("\n", $data);
		}
		else
		{
			$text = HOF_Class_Yaml::dump($data);
		}

		HOF_Class_File::fpwrite_file($this->fp, $text);
		fclose($this->fp);
		unset($this->fp);
	}

	/**
	 * 戦闘中のキャラ名,HP,SP を色を分けて表示する
	 * それ以外にも必要な物があれば表示するようにした。
	 */
	function ShowHpSp()
	{
		$output = '';

		if ($this->STATE === 1) $sub = " dmg";
		else
			if ($this->STATE === 2) $sub = " spdmg";
		//名前
		$output .= "<span class=\"bold{$sub}\">{$this->name}</span>\n";
		// チャージor詠唱
		if ($this->expect_type === 0) $output .= '<span class="charge">(charging)</span>' . "\n";
		else
			if ($this->expect_type === 1) $output .= '<span class="charge">(casting)</span>' . "\n";
		// HP,SP
		$output .= "<div class=\"hpsp\">\n";
		$sub = $this->STATE === 1 ? "dmg" : "recover";
		//print("<span class=\"{$sub}\">HP : ????/{$this->MAXHP}</span><br />\n");//HP
		$output .= "<span class=\"{$sub}\">HP : ????/????</span><br />\n"; //HP
		$sub = $this->STATE === 1 ? "dmg" : "support";
		$output .= "<span class=\"{$sub}\">SP : ????/????</span>\n";
		$output .= "</div>\n"; //SP

		return $output;
	}

	//	毒ダメージの公式
	function PoisonDamageFormula($multiply = 1)
	{
		$damage = round($this->HP * 0.01);
		$damage *= mt_rand(50, 150) / 100;
		if (200 < $damage) $damage = 200;
		$damage *= $multiply;
		return round($damage);
	}


	//	生存状態にする。
	function GetNormal($mes = false)
	{
		if ($this->STATE === STATE_ALIVE) return true;
		if ($this->STATE === STATE_DEAD)
		{ //死亡状態
			// ユニオンは復活しない事とする。
			return true;
			/*
			if($mes)
			print($this->Name(bold).' <span class="recover">revived</span>!<br />'."\n");
			$this->STATE = 0;
			return true;
			*/
		}
		if ($this->STATE === STATE_POISON)
		{ //毒状態
			if ($mes) print ($this->Name(bold) . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = 0;
			return true;
		}
	}

	//	行動を遅らせる(Rate)
	function DelayByRate($No, $BaseDelay, $Show = false)
	{
		if (DELAY_TYPE === 0)
		{
			if ($Show)
			{
				print ("(" . sprintf("%0.1f", $this->delay));
				print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$Delay = ($BaseDelay - $this->SPD) * ($No / 100); //遅らせる間隔
			$this->delay -= $Delay;
			if ($Show)
			{
				print (sprintf("%0.1f", $this->delay) . "/" . sprintf("%0.1f", $BaseDelay) . ")");
			}
		}
		else
			if (DELAY_TYPE === 1)
			{
				if ($Show)
				{
					print ("(" . sprintf("%0.0f", $this->delay));
					print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
				}
				$Delay = round($No / 3); //遅らせる間隔
				$this->delay -= $Delay;
				if ($Show)
				{
					print (sprintf("%0.0f", $this->delay) . "/" . sprintf("%d", 100) . ")");
				}
			}
	}

	//	値の変化を表示する(ダメージ受けた時とか)
	function ShowValueChange()
	{
		print ("(??? &gt; ???)");
	}

	//	番号で呼び出す
	function UnionNumber($no)
	{
		$file = UNION . $no . "_Union.dat";
		if ($this->LoadData($file)) return true;
		else  return false;
	}

	//	ユニオン自体が生きてるかどうか確認する(戦闘外で)
	function is_Alive()
	{
		if (0 < $this->hp) return true;
		else  return false;
	}

	function ShowCharLink()
	{
		// <div class="land_<*=$this->UnionLand*>">



?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?=

		HOF_Class_Icon::getImageUrl("land_" . $this->UnionLand, HOF_Class_Icon::IMG_LAND)


?>);">
	<a href="?union=<?=

		$this->UnionNo


?>"><?php

		$this->ShowImage();


?></a></div>
	<div class="bold dmg"><?=

		$this->UnionName


?></div>LvLimit:<?=

		$this->LevelLimit


?>
	</div><?php

	}

	function UpMAXHP($no)
	{
		print ($this->Name(bold) . " MAXHP(????) extended to ");
		$this->MAXHP = round($this->MAXHP * (1 + $no / 100));
		print ("????<br />\n");
	}
	function UpMAXSP($no)
	{
		print ($this->Name(bold) . " MAXSP(????) extended to ");
		$this->MAXSP = round($this->MAXSP * (1 + $no / 100));
		print ("????<br />\n");
	}
	function DownMAXHP($no)
	{
		$no /= 2;
		print ($this->Name(bold) . " MAXHP(????) down to ");
		$this->MAXHP = round($this->MAXHP * (1 - $no / 100));
		if ($this->MAXHP < $this->HP) $this->HP = $this->MAXHP;
		print ("????<br />\n");
	}
	function DownMAXSP($no)
	{
		$no /= 2;
		print ($this->Name(bold) . " MAXSP(????) down to ");
		$this->MAXSP = round($this->MAXSP * (1 - $no / 100));
		if ($this->MAXSP < $this->SP) $this->SP = $this->MAXSP;
		print ("????<br />\n");
	}
	function DownATK($no)
	{
		$no = round($no / 2);
		return call_user_func(array('parent', __FUNCTION__ ), $no);
	}
	function DownMATK($no)
	{
		$no = round($no / 2);
		return call_user_func(array('parent', __FUNCTION__ ), $no);
	}
	function DownDEF($no)
	{
		$no = round($no / 2);
		return call_user_func(array('parent', __FUNCTION__ ), $no);
	}
	function DownMDEF($no)
	{
		$no = round($no / 2);
		return call_user_func(array('parent', __FUNCTION__ ), $no);
	}

	//	差分経験値
	function HpDifferenceEXP()
	{
		$dif = $this->LastHP - $this->HP;
		$this->LastHP = $this->HP;
		if ($dif < 0) return 0;
		$exp = ceil($this->exphold * ($dif / $this->maxhp));
		return $exp;
	}



	//	しぼーしてるかどうか確認する。
	function CharJudgeDead()
	{
		if ($this->HP < 1 && $this->STATE !== 1)
		{ //しぼー
			$this->STATE = 1;
			$this->HP = 0;
			$this->ResetExpect();

			$this->LastDefeated = time();
			return true;
		}
	}

}
