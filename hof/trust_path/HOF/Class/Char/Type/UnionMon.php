<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//require_once (CLASS_UNION);

//class HOF_Class_Char_Type_UnionMon extends union
class HOF_Class_Char_Type_UnionMon extends HOF_Class_Char_Abstract
{

	var $file;
	var $fp;

	var $name;
	var $last_death;

	var $servant;

	var $land;
	var $lv_limit;
	/*
	Unionモンスターはダメージを受けると経験値を渡す。
	なので、全開のHPと差分を取って死亡判定時に経験値を渡すことにする。
	*/
	var $LastHP;

	// モンスター専用の変数
	//var $monster = true;
	var $exphold; //経験値
	var $moneyhold; //お金
	var $itemdrop; //落とすアイテム

	public function file($over = null)
	{
		if (!isset($this->file) || $over)
		{
			$this->file = HOF_Model_Char::getUnionFile($this->no);

			if (!file_exists($this->file))
			{
				throw new Exception(sprintf('%s:%s not Exists', $this->getCharType(), $this->no));
			}
		}

		return $this->file;
	}

	//	キャラの変数をセットする。
	function setCharData($data)
	{
		isset($data["no"]) && $this->no = $data["no"];
		$this->last_death = $data["last_death"];

		$data_attr = HOF_Model_Char::getUnionDataMon($this->no);
		parent::setCharData($data_attr);

		$this->team_name = $data['data']['team']['name'];

		$this->level = $data_attr["level"];

		$this->img = $data["img"];

		$this->str = $data_attr["str"];
		$this->int = $data_attr["int"];
		$this->dex = $data_attr["dex"];
		$this->spd = $data_attr["spd"];
		$this->luk = $data_attr["luk"];

		$this->maxhp = $data_attr["maxhp"];
		$this->hp = $data["hp"];
		$this->maxsp = $data_attr["maxsp"];
		$this->sp = $data["sp"];

		$this->position = $data_attr["position"];
		$this->guard = $data_attr["guard"];

		//モンスター専用

		$this->exphold = $data_attr["exphold"];
		$this->moneyhold = $data_attr["moneyhold"];
		$this->itemdrop = $data_attr["itemdrop"];
		$this->atk = $data_attr["atk"];
		$this->def = $data_attr["def"];
		$this->SPECIAL = $data_attr["SPECIAL"];

		$this->servant = $data['data']['team']['servant'];
		$this->land = $data["land"];
		$this->lv_limit = $data['data']['conditions']['lv_limit'];

		// 時間が経過して復活する処理。
		$Now = time();
		$Passed = $this->last_death + $data["cycle"];
		if ($Passed < $Now && !$this->hp)
		{
			$this->hp = $this->maxhp;
			$this->sp = $this->maxsp;
		}
		$this->LastHP = $data["hp"]; //差分を取るためのHP。

		$this->pattern = $data_attr["pattern"];


	}

	//	戦闘用の変数
	function setBattleVariable()
	{
		if ($this->_cache_char_['init'][__FUNCTION__ ]) return false;

		$this->_cache_char_['init'][__FUNCTION__ ] = true;

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
	function saveCharData()
	{
		if (!file_exists($this->file())) return false;

		$data = $this->source();

		$data['last_death'] = $this->last_death;
		$data['hp'] = isset($this->HP) ? $this->HP : $this->hp;
		$data['sp'] = isset($this->SP) ? $this->SP : $this->sp;

		HOF_Class_Yaml::save($this->fp, $data);

		HOF_Class_File::fpclose($this->fp);
		unset($this->fp);
	}

	/**
	 * 戦闘中のキャラ名,HP,SP を色を分けて表示する
	 * それ以外にも必要な物があれば表示するようにした。
	 */
	function ShowHpSp()
	{
		$output = '';

		if ($this->STATE === STATE_DEAD) $sub = " dmg";
		else
			if ($this->STATE === STATE_POISON) $sub = " spdmg";
		//名前
		$output .= "<span class=\"bold{$sub}\">".$this->Name()."</span>\n";
		// チャージor詠唱
		if ($this->expect_type === 0) $output .= '<span class="charge">(charging)</span>' . "\n";
		else
			if ($this->expect_type === 1) $output .= '<span class="charge">(casting)</span>' . "\n";
		// HP,SP
		$output .= "<div class=\"hpsp\">\n";
		$sub = $this->STATE === STATE_DEAD ? "dmg" : "recover";
		//print("<span class=\"{$sub}\">HP : ????/{$this->MAXHP}</span><br />\n");//HP
		$output .= "<span class=\"{$sub}\">HP : ????/????</span><br />\n"; //HP
		$sub = $this->STATE === STATE_DEAD ? "dmg" : "support";
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
			print($this->Name('bold').' <span class="recover">revived</span>!<br />'."\n");
			$this->STATE = 0;
			return true;
			*/
		}
		if ($this->STATE === STATE_POISON)
		{ //毒状態
			if ($mes) print ($this->Name('bold') . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = STATE_ALIVE;
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
		elseif (DELAY_TYPE === 1)
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

	//	ユニオン自体が生きてるかどうか確認する(戦闘外で)
	function is_Alive()
	{
		if (0 < $this->hp) return true;
		else  return false;
	}

	function ShowCharLink()
	{


?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?=

		HOF_Class_Icon::getImageUrl("land_" . $this->source()->land, HOF_Class_Icon::IMG_LAND)


?>);">
	<a href="<?php

		e(HOF::url('battle', 'union', array('union' => $this->id)))


?>"><?php

		$this->ShowImage();


?></a></div>
	<div class="bold dmg"><?php

		e($this->source()->data['team']['name'])


?></div>
	LvLimit:<?php

		e($this->source()->data['conditions']['lv_limit'])


?>
	</div><?php

	}

	function UpMAXHP($no)
	{
		print ($this->Name('bold') . " MAXHP(????) extended to ");
		$this->MAXHP = round($this->MAXHP * (1 + $no / 100));
		print ("????<br />\n");
	}
	function UpMAXSP($no)
	{
		print ($this->Name('bold') . " MAXSP(????) extended to ");
		$this->MAXSP = round($this->MAXSP * (1 + $no / 100));
		print ("????<br />\n");
	}
	function DownMAXHP($no)
	{
		$no /= 2;
		print ($this->Name('bold') . " MAXHP(????) down to ");
		$this->MAXHP = round($this->MAXHP * (1 - $no / 100));
		if ($this->MAXHP < $this->HP) $this->HP = $this->MAXHP;
		print ("????<br />\n");
	}
	function DownMAXSP($no)
	{
		$no /= 2;
		print ($this->Name('bold') . " MAXSP(????) down to ");
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
		if ($this->HP < 1 && $this->STATE !== STATE_DEAD)
		{
			//しぼー
			$this->STATE = STATE_DEAD;
			$this->HP = 0;
			$this->ResetExpect();

			$this->last_death = time();
			return true;
		}
	}

}


