<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

class union extends HOF_Class_Char
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


	//


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

	//	戦闘中のキャラ名,HP,SP を色を分けて表示する
	//	それ以外にも必要な物があれば表示するようにした。
	function ShowHpSp()
	{
		if ($this->STATE === 1) $sub = " dmg";
		else
			if ($this->STATE === 2) $sub = " spdmg";
		//名前
		print ("<span class=\"bold{$sub}\">{$this->name}</span>\n");
		// チャージor詠唱
		if ($this->expect_type === 0) print ('<span class="charge">(charging)</span>' . "\n");
		else
			if ($this->expect_type === 1) print ('<span class="charge">(casting)</span>' . "\n");
		// HP,SP
		print ("<div class=\"hpsp\">\n");
		$sub = $this->STATE === 1 ? "dmg" : "recover";
		//print("<span class=\"{$sub}\">HP : ????/{$this->MAXHP}</span><br />\n");//HP
		print ("<span class=\"{$sub}\">HP : ????/????</span><br />\n"); //HP
		$sub = $this->STATE === 1 ? "dmg" : "support";
		print ("<span class=\"{$sub}\">SP : ????/????</span>\n");
		print ("</div>\n"); //SP
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

	//
	function LoadData($file)
	{
		if (!file_exists($file)) return false;

		$this->file = $file;
		$this->fp = HOF_Class_File::fplock_file($this->file);

		$this->UnionNo = substr(basename($file), 0, 4);
		$data = HOF_Class_File::ParseFileFP($this->fp);
		$this->SetCharData($data);
		return true;
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

	//	キャラデータの保存
	function SaveCharData()
	{
		if (!file_exists($this->file)) return false;
		$string = "MonsterNumber=" . $this->MonsterNumber . "\n";
		$string .= "LastDefeated=" . $this->LastDefeated . "\n";
		$string .= "HP=" . $this->HP . "\n";
		$string .= "SP=" . $this->SP . "\n";

		HOF_Class_File::fpwrite_file($this->fp, $string);
		fclose($this->fp);
		unset($this->fp);
	}

}


?>