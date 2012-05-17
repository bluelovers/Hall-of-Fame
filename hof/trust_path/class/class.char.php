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

	//	HPの犠牲
	function SacrificeHp($rate)
	{
		if (!$rate) return false;

		$SelfDamage = ceil($this->MAXHP * ($rate / 100));
		if ($this->POSITION != POSITION_FRONT) $SelfDamage *= 2;
		print ("<span class=\"dmg\">" . $this->Name(bold) . " sacrifice ");
		print ("<span class=\"bold\">$SelfDamage</span> HP</span>\n");
		$this->HpDamage($SelfDamage);
		print ("</span><br />\n");
	}

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

	//	HPSP持続回復
	function AutoRegeneration()
	{
		// HP回復
		if ($this->SPECIAL["HpRegen"])
		{
			$Regen = round($this->MAXHP * $this->SPECIAL["HpRegen"] / 100);
			print ('<span class="recover">* </span>' . $this->Name(bold) . "<span class=\"recover\"> Auto Regenerate <span class=\"bold\">" . $Regen . " HP</span></span> ");
			$this->HpRecover($Regen);
			print ("<br />\n");
		}
		// SP回復
		if ($this->SPECIAL["SpRegen"])
		{
			$Regen = round($this->MAXSP * $this->SPECIAL["SpRegen"] / 100);
			print ('<span class="support">* </span>' . $this->Name(bold) . "<span class=\"support\"> Auto Regenerate <span class=\"bold\">" . $Regen . " SP</span></span> ");
			$this->SpRecover($Regen);
			print ("<br />\n");
		}
	}

	//	キャラステータスの一番上のやつ。
	function ShowCharDetail()
	{
		$P_MAXHP = round($this->maxhp * $this->M_MAXHP / 100) + $this->P_MAXHP;
		$P_MAXSP = round($this->maxsp * $this->M_MAXSP / 100) + $this->P_MAXSP;


?>
<table>
<tr><td valign="top" style="width:180px"><?php

		$this->ShowCharLink();


?>
</td><td valign="top" style="padding-right:20px">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td style="text-align:right">Exp :&nbsp;</td><td><?=

		$this->exp


?>/<?=

		$this->CalcExpNeed()


?></td></tr>
<tr><td style="text-align:right">HP :&nbsp;</td><td><?=

		$this->maxhp


?><?php

		if ($P_MAXHP) print (" + {$P_MAXHP}");


?></td></tr>
<tr><td style="text-align:right">SP :&nbsp;</td><td><?=

		$this->maxsp


?><?php

		if ($P_MAXSP) print (" + {$P_MAXSP}");


?></td></tr>
<tr><td style="text-align:right">STR :&nbsp;</td><td><?=

		$this->str


?><?php

		if ($this->P_STR) print (" + {$this->P_STR}");


?></td></tr>
<tr><td style="text-align:right">INT :&nbsp;</td><td><?=

		$this->int


?><?php

		if ($this->P_INT) print (" + {$this->P_INT}");


?></td></tr>
<tr><td style="text-align:right">DEX :&nbsp;</td><td><?=

		$this->dex


?><?php

		if ($this->P_DEX) print (" + {$this->P_DEX}");


?></td></tr>
<tr><td style="text-align:right">SPD :&nbsp;</td><td><?=

		$this->spd


?><?php

		if ($this->P_SPD) print (" + {$this->P_SPD}");


?></td></tr>
<tr><td style="text-align:right">LUK :&nbsp;</td><td><?=

		$this->luk


?><?php

		if ($this->P_LUK) print (" + {$this->P_LUK}");


?></td></tr>
</table>
</td><td valign="top">
<?php

		if ($this->SPECIAL["PoisonResist"]) print ("毒抵抗 +" . $this->SPECIAL["PoisonResist"] . "%<br />\n");
		if ($this->SPECIAL["Pierce"]["0"]) print ("物理防御無視ダメージ +" . $this->SPECIAL["Pierce"]["0"] . "<br />\n");
		if ($this->SPECIAL["Pierce"]["1"]) print ("魔法防御無視ダメージ +" . $this->SPECIAL["Pierce"]["1"] . "<br />\n");
		if ($this->SPECIAL["Summon"]) print ("召喚力 +" . $this->SPECIAL["Summon"] . "%<br />\n");


?>
</td></tr></table>
<?php

	}

	//	誰のキャラか設定する
	function SetUser($user)
	{
		$this->user = $user;
	}

	//	チャージ(詠唱)中の解除
	function ResetExpect()
	{
		$this->expect = false;
		$this->expect_type = false;
		$this->expect_target = false;
	}

	//	前列後列の移動
	function Move($posi)
	{
		//print($this->POSITION."->".$posi."<br />\n");
		if ($posi == POSITION_FRONT)
		{
			if ($this->POSITION == POSITION_FRONT) return false;
			$this->POSITION = POSITION_FRONT;
			print ($this->Name(bold) . " moved to front.<br />\n");
		}
		else
		{
			if ($this->POSITION != POSITION_FRONT) return false;
			$this->POSITION = POSITION_BACK;
			print ($this->Name(bold) . " moved to back.<br />\n");
		}
	}


	//	行動までの距離測定
	function nextDis()
	{
		if ($this->STATE === STATE_DEAD) return 100;
		$distance = (100 - $this->delay) / $this->DelayValue();
		return $distance;
	}

	//	行動順リセット
	function DelayReset()
	{
		if (DELAY_TYPE === 0)
		{
			$this->delay = $this->SPD;
		}
		else
			if (DELAY_TYPE === 1)
			{
				$this->delay = 0;
			}
	}

	//	行動を近づかせる。
	function Delay($no)
	{
		// 死亡中は増えないようにする
		if ($this->STATE === STATE_DEAD) return false;

		if (DELAY_TYPE === 0)
		{
			$this->delay += $no;
		}
		else
			if (DELAY_TYPE === 1)
			{
				$this->delay += $no * $this->DelayValue();
				//print("DELAY".$this->delay."<br />\n");
			}
	}

	//
	function DelayValue()
	{
		return sqrt($this->SPD) + DELAY_BASE;
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
				$Delay = $No; //遅らせる間隔
				$this->delay -= $Delay;
				if ($Show)
				{
					print (sprintf("%0.0f", floor($this->delay)) . "/" . sprintf("%d", 100) . ")");
				}
			}
	}

	//	行動を早送りする(%)
	function DelayCut($No, $BaseDelay, $Show = false)
	{
		if (DELAY_TYPE === 0)
		{
			$Delay = ($BaseDelay - $this->delay) * ($No / 100); //早まらせる間隔
			if ($Show)
			{
				print ("(" . sprintf("%0.1f", $this->delay));
				print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$this->delay += $Delay;
			if ($Show)
			{
				print (sprintf("%0.1f", $this->delay) . "/" . sprintf("%0.1f", $BaseDelay) . ")");
			}
		}
		else
			if (DELAY_TYPE === 1)
			{
				$Delay = (100 - $this->delay) * ($No / 100); //早まらせる間隔
				if ($Show)
				{
					print ("(" . sprintf("%0.1f", $this->delay));
					print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
				}
				$this->delay += $Delay;
				if ($Show)
				{
					print (sprintf("%0.0f", floor($this->delay)) . "/" . sprintf("%d", 100) . ")");
				}
			}
	}

	//	即時行動させる。
	function Quick($delay)
	{
		if (DELAY_TYPE === 0) $this->delay = $delay;
		else
			if (DELAY_TYPE === 1) $this->delay = 100.1;
	}

	//


	//

	//



	//	キャラを後衛化させる。
	function KnockBack($no = 1)
	{
		if ($this->POSITION == POSITION_FRONT)
		{
			$this->POSITION = POSITION_BACK;
			print ($this->Name(bold) . " knock backed!<br />\n");
		}
	}

	//
	//	ステータス強化(+)
	function PlusSTR($no)
	{
		$this->STR += $no;
		print ($this->Name(bold) . " STR rise {$no}<br />\n");
	}
	function PlusINT($no)
	{
		$this->INT += $no;
		print ($this->Name(bold) . " INT rise {$no}<br />\n");
	}
	function PlusDEX($no)
	{
		$this->DEX += $no;
		print ($this->Name(bold) . " DEX rise {$no}<br />\n");
	}
	function PlusSPD($no)
	{
		$this->SPD += $no;
		print ($this->Name(bold) . " SPD rise {$no}<br />\n");
	}
	function PlusLUK($no)
	{
		$this->LUK += $no;
		print ($this->Name(bold) . " LUK rise {$no}<br />\n");
	}

	//	ステータス強化(%)
	function UpMAXHP($no)
	{
		print ($this->Name(bold) . " MAXHP({$this->MAXHP}) extended to ");
		$this->MAXHP = round($this->MAXHP * (1 + $no / 100));
		print ("{$this->MAXHP}<br />\n");
	}
	function UpMAXSP($no)
	{
		print ($this->Name(bold) . " MAXSP({$this->MAXSP}) extended to ");
		$this->MAXSP = round($this->MAXSP * (1 + $no / 100));
		print ("{$this->MAXSP}<br />\n");
	}
	function UpSTR($no)
	{
		$this->STR = round($this->STR * (1 + $no / 100));
		if (($this->str * MAX_STATUS_MAXIMUM / 100) < $this->STR)
		{
			print ($this->Name(bold) . " STR rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->STR = round($this->str * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->Name(bold) . " STR rise {$no}%<br />\n");
		}
	}
	function UpINT($no)
	{
		$this->INT = round($this->INT * (1 + $no / 100));
		if (($this->int * MAX_STATUS_MAXIMUM / 100) < $this->INT)
		{
			print ($this->Name(bold) . " INT rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->INT = round($this->int * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->Name(bold) . " INT rise {$no}%<br />\n");
		}
	}
	function UpDEX($no)
	{
		$this->DEX = round($this->DEX * (1 + $no / 100));
		if (($this->dex * MAX_STATUS_MAXIMUM / 100) < $this->DEX)
		{
			print ($this->Name(bold) . " DEX rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->DEX = round($this->dex * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->Name(bold) . " DEX rise {$no}%<br />\n");
		}
	}
	function UpSPD($no)
	{
		$this->SPD = round($this->SPD * (1 + $no / 100));
		if (($this->spd * MAX_STATUS_MAXIMUM / 100) < $this->SPD)
		{
			print ($this->Name(bold) . " SPD rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->SPD = round($this->spd * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->Name(bold) . " SPD rise {$no}%<br />\n");
		}
	}
	function UpATK($no)
	{
		$this->atk["0"] = round($this->atk["0"] * (1 + $no / 100));
		print ($this->Name(bold) . " ATK rise {$no}%<br />\n");
	}
	function UpMATK($no)
	{
		$this->atk["1"] = round($this->atk["1"] * (1 + $no / 100));
		print ($this->Name(bold) . " MATK rise {$no}%<br />\n");
	}
	function UpDEF($no)
	{
		$up = floor((100 - $this->def["0"]) * ($no / 100));
		$this->def["0"] += $up;
		print ($this->Name(bold) . " DEF rise {$no}%<br />\n");
	}
	function UpMDEF($no)
	{
		$up = floor((100 - $this->def["2"]) * ($no / 100));
		print ($this->Name(bold) . " MDEF rise {$no}%<br />\n");
		$this->def["2"] += $up;
	}
	//	ステータス弱体化(%)
	function DownMAXHP($no)
	{
		print ($this->Name(bold) . " MAXHP({$this->MAXHP}) down to ");
		$this->MAXHP = round($this->MAXHP * (1 - $no / 100));
		if ($this->MAXHP < $this->HP) $this->HP = $this->MAXHP;
		print ("{$this->MAXHP}<br />\n");
	}
	function DownMAXSP($no)
	{
		print ($this->Name(bold) . " MAXSP({$this->MAXSP}) down to ");
		$this->MAXSP = round($this->MAXSP * (1 - $no / 100));
		if ($this->MAXSP < $this->SP) $this->SP = $this->MAXSP;
		print ("{$this->MAXSP}<br />\n");
	}
	function DownSTR($no)
	{
		$this->STR = round($this->STR * (1 - $no / 100));
		print ($this->Name(bold) . " STR down {$no}%<br />\n");
	}
	function DownINT($no)
	{
		$this->INT = round($this->INT * (1 - $no / 100));
		print ($this->Name(bold) . " INT down {$no}%<br />\n");
	}
	function DownDEX($no)
	{
		$this->DEX = round($this->DEX * (1 - $no / 100));
		print ($this->Name(bold) . " DEX down {$no}%<br />\n");
	}
	function DownSPD($no)
	{
		$this->SPD = round($this->SPD * (1 - $no / 100));
		print ($this->Name(bold) . " SPD down {$no}%<br />\n");
	}
	function DownATK($no)
	{
		$this->atk["0"] = round($this->atk["0"] * (1 - $no / 100));
		print ($this->Name(bold) . " ATK down {$no}%<br />\n");
	}
	function DownMATK($no)
	{
		$this->atk["1"] = round($this->atk["1"] * (1 - $no / 100));
		print ($this->Name(bold) . " MATK down {$no}%<br />\n");
	}
	function DownDEF($no)
	{
		$this->def["0"] = round($this->def["0"] * (1 - $no / 100));
		print ($this->Name(bold) . " DEF down {$no}%<br />\n");
	}
	function DownMDEF($no)
	{
		$this->def["2"] = round($this->def["2"] * (1 - $no / 100));
		print ($this->Name(bold) . " MDEF down {$no}%<br />\n");
	}


	//	毒ダメージ
	function PoisonDamage($multiply = 1)
	{
		if ($this->STATE !== 2) return false;

		$poison = $this->PoisonDamageFormula($multiply);
		print ("<span class=\"spdmg\">" . $this->Name(bold) . " got ");
		print ("<span class=\"bold\">$poison</span> damage by poison.\n");
		$this->HpDamage2($poison);
		print ("</span><br />\n");
	}

	//	毒ダメージの公式
	function PoisonDamageFormula($multiply = 1)
	{
		$damage = round($this->MAXHP * 0.10) + ceil($this->level / 2);
		$damage *= $multiply;
		return round($damage);
	}

	//	毒の状態 異常化 処理
	function GetPoison($BePoison)
	{
		if ($this->STATE === 2) return false;
		if ($this->SPECIAL["PoisonResist"])
		{
			$prob = mt_rand(0, 99);
			$BePoison *= (1 - $this->SPECIAL["PoisonResist"] / 100);
			if ($prob < $BePoison)
			{
				$this->STATE = 2;
				return true;
			}
			else
			{
				return "BLOCK";
			}
		}
		$this->STATE = 2;
		return true;
	}

	//	毒耐性を得る
	function GetPoisonResist($no)
	{
		$Add = (100 - $this->SPECIAL["PoisonResist"]) * ($no / 100);
		$Add = round($Add);
		$this->SPECIAL["PoisonResist"] += $Add;
		print ('<span class="support">');
		print ($this->Name(bold) . " got PoisonResist!(" . $this->SPECIAL["PoisonResist"] . "%)");
		print ("</span><br />\n");
	}

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

	//	しぼーしてるかどうか確認する。
	function CharJudgeDead()
	{
		if ($this->HP < 1 && $this->STATE !== STATE_DEAD)
		{ //しぼー
			$this->STATE = STATE_DEAD;
			$this->HP = 0;
			$this->ResetExpect();

			return true;
		}
	}

	//	生存状態にする。
	function GetNormal($mes = false)
	{
		if ($this->STATE === STATE_ALIVE) return true;
		if ($this->STATE === STATE_DEAD)
		{ //死亡状態
			if ($mes) print ($this->Name(bold) . ' <span class="recover">revived</span>!<br />' . "\n");
			$this->STATE = 0;
			return true;
		}
		if ($this->STATE === STATE_POISON)
		{ //毒状態
			if ($mes) print ($this->Name(bold) . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = 0;
			return true;
		}
	}


	//	値の変化を表示する(ダメージ受けた時とか)
	function ShowValueChange($from, $to)
	{
		print ("({$from} &gt; {$to})");
	}

	//	HPへのダメージ
	function HpDamage($damage, $show = true)
	{
		$Before = $this->HP;
		$this->HP -= $damage; // HPを減らす。
		if ($show) $this->ShowValueChange($Before, $this->HP);
	}

	//	HPへのダメージ(0以下になるなら1になる。)
	function HpDamage2($damage)
	{
		$Before = $this->HP;
		$this->HP -= $damage;
		// $DoNotDie=true ならHPが1を下回った場合1にする。
		if ($this->HP < 1) $this->HP = 1;
		$this->ShowValueChange($Before, $this->HP);
	}

	//	HPのパーセント
	function HpPercent()
	{
		if ($this->MAXHP == 0) return 0;
		$p = ($this->HP / $this->MAXHP) * 100;
		return $p;
	}

	//	SPのパーセント
	function SpPercent()
	{
		if ($this->MAXSP == 0) return 0;
		$p = ($this->SP / $this->MAXSP) * 100;
		return $p;
	}

	//	SPへのダメージ(消費)
	function SpDamage($damage, $show = true)
	{
		$Before = $this->SP;
		$this->SP -= $damage;
		if ($this->SP < 1) $this->SP = 0;
		if ($show) $this->ShowValueChange($Before, $this->SP);
	}

	//	HP回復
	function HpRecover($recover)
	{
		$Before = $this->HP;
		$this->HP += $recover;
		if ($this->MAXHP < $this->HP)
		{
			$this->HP = $this->MAXHP;
		}
		$this->ShowValueChange($Before, $this->HP);
	}

	//	SP回復
	function SpRecover($recover)
	{
		$Before = $this->SP;
		$this->SP += $recover;
		if ($this->MAXSP < $this->SP)
		{
			$this->SP = $this->MAXSP;
		}
		$this->ShowValueChange($Before, $this->SP);
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

	function ShowCharWithLand($land)
	{


?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?=

		HOF_Class_Icon::getImageUrl("land_" . $land, HOF_Class_Icon::IMG_LAND)


?>);">
	<?php

		$this->ShowImage()


?>
	</div>
	<?=

		$this->name


?><br>Lv.<?=

		$this->level


?>
	</div><?php

	}


	function ShowChar()
	{
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0) //carpetの並びを交互にする
 				$flag++;


?>
<div class="carpet_frame">
<div class="carpet<?=

		$flag % 2


?>"><?php

		$this->ShowImage();


?></div>
<?=

		$this->name


?><br>Lv.<?=

		$this->level


?>&nbsp;<?=

		$this->job_name


?>
</div><?php

	}


	function ShowCharLink()
	{ //$array=色々
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0) //carpetの並びを交互にする
 				$flag++;


?>
<div class="carpet_frame">
<div class="carpet<?=

		$flag % 2


?>">
<a href="?char=<?=

		$this->Number


?>"><?php

		$this->ShowImage();


?></a></div>
<?=

		$this->name


?><?php

		if ($this->statuspoint) print ('<span class="bold charge">*</span>');


?><br>Lv.<?=

		$this->level


?>&nbsp;<?=

		$this->job_name


?>
</div><?php

	}


	//	checkboxも表示する
	function ShowCharRadio($birth, $checked = null)
	{
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0) //carpetの並びを交互にする
 				$flag++;

		// onclick="Element.toggleClassName(this,'unselect')"



?>
<div class="carpet_frame">
<div class="carpet<?=

		$flag % 2


?>">
<a href="?char=<?=

		$this->birth


?>"><?php

		$this->ShowImage();


?></a>
</div>

<div id="text<?=

		$flag


?>" <?

		print ($checked ? null : ' class="unselect"');


?>>
<?=

		$this->name


?>
<?php

		if ($this->statuspoint) print ('<span class="bold charge">*</span>');


?><br />
Lv.<?=

		$this->level


?>&nbsp;<?=

		$this->job_name


?>

</div>
<input type="checkbox" id="box<?=

		$flag


?>" name="char_<?=

		$birth


?>" value="1"<?=

		$checked


?>>

</div><?php

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