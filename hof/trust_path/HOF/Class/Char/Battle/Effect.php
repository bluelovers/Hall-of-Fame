<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_Battle_Effect
{

	protected $chat;

	function __construct($char)
	{
		$this->char = $char;
	}

	/**
	 * 戦闘中のキャラ名,HP,SP を色を分けて表示する
	 * それ以外にも必要な物があれば表示するようにした。
	 */
	function ShowHpSp()
	{
		$output = '';

		if ($this->char->STATE === STATE_DEAD) $sub = " dmg";
		elseif ($this->char->STATE === STATE_POISON) $sub = " spdmg";

		/**
		 * 名前
		 */
		$output .= "<span class=\"bold{$sub}\">" . $this->char->Name() . "</span>\n";

		/**
		 * チャージor詠唱
		 */
		if ($this->char->expect_type === EXPECT_CHARGE) $output .= '<span class="charge">(charging)</span>' . "\n";
		elseif ($this->char->expect_type === EXPECT_CAST) $output .= '<span class="charge">(casting)</span>' . "\n";

		// HP,SP
		$output .= "<div class=\"hpsp\">\n";
		$sub = $this->char->STATE === STATE_DEAD ? "dmg" : "recover";
		$output .= "<span class=\"{$sub}\">HP : {$this->char->HP}/{$this->char->MAXHP}</span><br />\n"; //HP
		$sub = $this->char->STATE === STATE_DEAD ? "dmg" : "support";
		$output .= "<span class=\"{$sub}\">SP : {$this->char->SP}/{$this->char->MAXSP}</span>\n";
		$output .= "</div>\n"; //SP

		return $output;
	}

	//	HPの犠牲
	function SacrificeHp($rate)
	{
		if (!$rate) return false;

		$SelfDamage = ceil($this->char->MAXHP * ($rate / 100));
		if ($this->char->POSITION != POSITION_FRONT) $SelfDamage *= 2;
		print ("<span class=\"dmg\">" . $this->char->Name('bold') . " sacrifice ");
		print ("<span class=\"bold\">$SelfDamage</span> HP</span>\n");
		$this->char->HpDamage($SelfDamage);
		print ("</span><br />\n");
	}

	//	HPSP持続回復
	function AutoRegeneration()
	{
		// HP回復
		if ($this->char->SPECIAL["HpRegen"])
		{
			$Regen = round($this->char->MAXHP * $this->char->SPECIAL["HpRegen"] / 100);
			print ('<span class="recover">* </span>' . $this->char->Name('bold') . "<span class=\"recover\"> Auto Regenerate <span class=\"bold\">" . $Regen . " HP</span></span> ");
			$this->char->HpRecover($Regen);
			print ("<br />\n");
		}
		// SP回復
		if ($this->char->SPECIAL["SpRegen"])
		{
			$Regen = round($this->char->MAXSP * $this->char->SPECIAL["SpRegen"] / 100);
			print ('<span class="support">* </span>' . $this->char->Name('bold') . "<span class=\"support\"> Auto Regenerate <span class=\"bold\">" . $Regen . " SP</span></span> ");
			$this->char->SpRecover($Regen);
			print ("<br />\n");
		}
	}

	//	チャージ(詠唱)中の解除
	function ResetExpect()
	{
		$this->char->expect = false;
		$this->char->expect_type = false;
		$this->char->expect_target = false;
	}

	//	前列後列の移動
	function Move($posi)
	{
		//print($this->char->POSITION."->".$posi."<br />\n");
		if ($posi == POSITION_FRONT)
		{
			if ($this->char->POSITION == POSITION_FRONT) return false;
			$this->char->POSITION = POSITION_FRONT;
			print ($this->char->Name('bold') . " moved to front.<br />\n");
		}
		else
		{
			if ($this->char->POSITION != POSITION_FRONT) return false;
			$this->char->POSITION = POSITION_BACK;
			print ($this->char->Name('bold') . " moved to back.<br />\n");
		}
	}

	//	行動までの距離測定
	function nextDis()
	{
		if ($this->char->STATE === STATE_DEAD) return 100;

		$distance = bcdiv(bcsub(100, $this->char->delay), $this->char->DelayValue());

		return $distance;
	}

	//	行動順リセット
	function DelayReset()
	{
		if (DELAY_TYPE === 0)
		{
			$this->char->delay = $this->char->SPD;
		}
		elseif (DELAY_TYPE === 1)
		{
			$this->char->delay = 0;
		}
	}

	//	行動を近づかせる。
	function Delay($no)
	{
		// 死亡中は増えないようにする
		if ($this->char->STATE === STATE_DEAD) return false;

		if (DELAY_TYPE === 0)
		{
			$this->char->delay = bcadd($this->char->delay, $no);
		}
		elseif (DELAY_TYPE === 1)
		{
			$this->char->delay = bcadd($this->char->delay, bcmul($no, $this->char->DelayValue()));
			//print("DELAY".$this->char->delay."<br />\n");
		}
	}

	function DelayValue()
	{
		return bcadd(sqrt($this->char->SPD), DELAY_BASE);
	}

	//	行動を遅らせる(Rate)
	function DelayByRate($No, $BaseDelay, $Show = false)
	{

		if ($Show)
		{
			print (sprintf("(%0.0f", $this->char->delay));
			print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
		}

		if (DELAY_TYPE === 0)
		{
			$Delay = ($BaseDelay - $this->char->SPD) * ($No / 100); //遅らせる間隔
			//$this->char->delay -= $Delay;
		}
		elseif (DELAY_TYPE === 1)
		{
			$Delay = $No; //遅らせる間隔
			//$this->char->delay -= $Delay;
		}

		$this->char->delay = bcsub($this->char->delay, $Delay);

		if ($Show)
		{
			print (sprintf("%0.0f/%0.0f)", $this->char->delay,  isset($BaseDelay) ? $BaseDelay : 100));
		}
	}

	//	行動を早送りする(%)
	function DelayCut($No, $BaseDelay, $Show = false)
	{
		if (DELAY_TYPE === 0)
		{
			$Delay = ($BaseDelay - $this->char->delay) * ($No / 100); //早まらせる間隔
			if ($Show)
			{
				print ("(" . sprintf("%0.1f", $this->char->delay));
				print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$this->char->delay += $Delay;
			if ($Show)
			{
				print (sprintf("%0.1f", $this->char->delay) . "/" . sprintf("%0.1f", $BaseDelay) . ")");
			}
		}
		elseif (DELAY_TYPE === 1)
		{
			$Delay = bcmul(bcsub(100, $this->char->delay), bcdiv($No, 100)); //早まらせる間隔
			if ($Show)
			{
				print ("(" . sprintf("%0.1f", $this->char->delay));
				print ('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$this->char->delay += $Delay;
			if ($Show)
			{
				print (sprintf("%0.0f", floor($this->char->delay)) . "/" . sprintf("%d", 100) . ")");
			}
		}
	}

	//	即時行動させる。
	function Quick($delay)
	{
		if (DELAY_TYPE === 0) $this->char->delay = $delay;
		elseif (DELAY_TYPE === 1) $this->char->delay = 100.1;
	}

	//	キャラを後衛化させる。
	function KnockBack($no = 1)
	{
		if ($this->char->POSITION == POSITION_FRONT)
		{
			$this->char->POSITION = POSITION_BACK;
			print ($this->char->Name('bold') . " knock backed!<br />\n");
		}
	}

	//	ステータス強化(+)
	function PlusSTR($no)
	{
		$this->char->STR += $no;
		print ($this->char->Name('bold') . " STR rise {$no}<br />\n");
	}
	function PlusINT($no)
	{
		$this->char->INT += $no;
		print ($this->char->Name('bold') . " INT rise {$no}<br />\n");
	}
	function PlusDEX($no)
	{
		$this->char->DEX += $no;
		print ($this->char->Name('bold') . " DEX rise {$no}<br />\n");
	}
	function PlusSPD($no)
	{
		$this->char->SPD += $no;
		print ($this->char->Name('bold') . " SPD rise {$no}<br />\n");
	}
	function PlusLUK($no)
	{
		$this->char->LUK += $no;
		print ($this->char->Name('bold') . " LUK rise {$no}<br />\n");
	}

	//	ステータス強化(%)
	function UpMAXHP($no)
	{
		print ($this->char->Name('bold') . " MAXHP({$this->char->MAXHP}) extended to ");
		$this->char->MAXHP = round($this->char->MAXHP * (1 + $no / 100));
		print ("{$this->char->MAXHP}<br />\n");
	}
	function UpMAXSP($no)
	{
		print ($this->char->Name('bold') . " MAXSP({$this->char->MAXSP}) extended to ");
		$this->char->MAXSP = round($this->char->MAXSP * (1 + $no / 100));
		print ("{$this->char->MAXSP}<br />\n");
	}
	function UpSTR($no)
	{
		$this->char->STR = round($this->char->STR * (1 + $no / 100));
		if (($this->char->str * MAX_STATUS_MAXIMUM / 100) < $this->char->STR)
		{
			print ($this->char->Name('bold') . " STR rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->char->STR = round($this->char->str * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->char->Name('bold') . " STR rise {$no}%<br />\n");
		}
	}
	function UpINT($no)
	{
		$this->char->INT = round($this->char->INT * (1 + $no / 100));
		if (($this->char->int * MAX_STATUS_MAXIMUM / 100) < $this->char->INT)
		{
			print ($this->char->Name('bold') . " INT rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->char->INT = round($this->char->int * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->char->Name('bold') . " INT rise {$no}%<br />\n");
		}
	}
	function UpDEX($no)
	{
		$this->char->DEX = round($this->char->DEX * (1 + $no / 100));
		if (($this->char->dex * MAX_STATUS_MAXIMUM / 100) < $this->char->DEX)
		{
			print ($this->char->Name('bold') . " DEX rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->char->DEX = round($this->char->dex * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->char->Name('bold') . " DEX rise {$no}%<br />\n");
		}
	}
	function UpSPD($no)
	{
		$this->char->SPD = round($this->char->SPD * (1 + $no / 100));
		if (($this->char->spd * MAX_STATUS_MAXIMUM / 100) < $this->char->SPD)
		{
			print ($this->char->Name('bold') . " SPD rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
			$this->char->SPD = round($this->char->spd * MAX_STATUS_MAXIMUM / 100);
		}
		else
		{
			print ($this->char->Name('bold') . " SPD rise {$no}%<br />\n");
		}
	}
	function UpATK($no)
	{
		$this->char->atk["0"] = round($this->char->atk["0"] * (1 + $no / 100));
		print ($this->char->Name('bold') . " ATK rise {$no}%<br />\n");
	}
	function UpMATK($no)
	{
		$this->char->atk["1"] = round($this->char->atk["1"] * (1 + $no / 100));
		print ($this->char->Name('bold') . " MATK rise {$no}%<br />\n");
	}
	function UpDEF($no)
	{
		$up = floor((100 - $this->char->def["0"]) * ($no / 100));
		$this->char->def["0"] += $up;
		print ($this->char->Name('bold') . " DEF rise {$no}%<br />\n");
	}
	function UpMDEF($no)
	{
		$up = floor((100 - $this->char->def["2"]) * ($no / 100));
		print ($this->char->Name('bold') . " MDEF rise {$no}%<br />\n");
		$this->char->def["2"] += $up;
	}
	//	ステータス弱体化(%)
	function DownMAXHP($no)
	{
		print ($this->char->Name('bold') . " MAXHP({$this->char->MAXHP}) down to ");
		$this->char->MAXHP = round($this->char->MAXHP * (1 - $no / 100));
		if ($this->char->MAXHP < $this->char->HP) $this->char->HP = $this->char->MAXHP;
		print ("{$this->char->MAXHP}<br />\n");
	}
	function DownMAXSP($no)
	{
		print ($this->char->Name('bold') . " MAXSP({$this->char->MAXSP}) down to ");
		$this->char->MAXSP = round($this->char->MAXSP * (1 - $no / 100));
		if ($this->char->MAXSP < $this->char->SP) $this->char->SP = $this->char->MAXSP;
		print ("{$this->char->MAXSP}<br />\n");
	}
	function DownSTR($no)
	{
		$this->char->STR = round($this->char->STR * (1 - $no / 100));
		print ($this->char->Name('bold') . " STR down {$no}%<br />\n");
	}
	function DownINT($no)
	{
		$this->char->INT = round($this->char->INT * (1 - $no / 100));
		print ($this->char->Name('bold') . " INT down {$no}%<br />\n");
	}
	function DownDEX($no)
	{
		$this->char->DEX = round($this->char->DEX * (1 - $no / 100));
		print ($this->char->Name('bold') . " DEX down {$no}%<br />\n");
	}
	function DownSPD($no)
	{
		$this->char->SPD = round($this->char->SPD * (1 - $no / 100));
		print ($this->char->Name('bold') . " SPD down {$no}%<br />\n");
	}
	function DownATK($no)
	{
		$this->char->atk["0"] = round($this->char->atk["0"] * (1 - $no / 100));
		print ($this->char->Name('bold') . " ATK down {$no}%<br />\n");
	}
	function DownMATK($no)
	{
		$this->char->atk["1"] = round($this->char->atk["1"] * (1 - $no / 100));
		print ($this->char->Name('bold') . " MATK down {$no}%<br />\n");
	}
	function DownDEF($no)
	{
		$this->char->def["0"] = round($this->char->def["0"] * (1 - $no / 100));
		print ($this->char->Name('bold') . " DEF down {$no}%<br />\n");
	}
	function DownMDEF($no)
	{
		$this->char->def["2"] = round($this->char->def["2"] * (1 - $no / 100));
		print ($this->char->Name('bold') . " MDEF down {$no}%<br />\n");
	}


	//	毒ダメージ
	function PoisonDamage($multiply = 1)
	{
		if ($this->char->STATE !== STATE_POISON) return false;

		$poison = $this->char->PoisonDamageFormula($multiply);
		print ("<span class=\"spdmg\">" . $this->char->Name('bold') . " got ");
		print ("<span class=\"bold\">$poison</span> damage by poison.\n");
		$this->char->HpDamage2($poison);
		print ("</span><br />\n");
	}

	//	毒ダメージの公式
	function PoisonDamageFormula($multiply = 1)
	{
		$damage = round($this->char->MAXHP * 0.10) + ceil($this->char->level / 2);
		$damage *= $multiply;
		return round($damage);
	}

	//	毒の状態 異常化 処理
	function GetPoison($BePoison)
	{
		if ($this->char->STATE === STATE_POISON) return false;
		if ($this->char->SPECIAL["PoisonResist"])
		{
			$prob = mt_rand(0, 99);
			$BePoison *= (1 - $this->char->SPECIAL["PoisonResist"] / 100);
			if ($prob < $BePoison)
			{
				$this->char->STATE = STATE_POISON;
				return true;
			}
			else
			{
				return "BLOCK";
			}
		}
		$this->char->STATE = STATE_POISON2;
		return true;
	}

	//	毒耐性を得る
	function GetPoisonResist($no)
	{
		$Add = (100 - $this->char->SPECIAL["PoisonResist"]) * ($no / 100);
		$Add = round($Add);
		$this->char->SPECIAL["PoisonResist"] += $Add;
		print ('<span class="support">');
		print ($this->char->Name('bold') . " got PoisonResist!(" . $this->char->SPECIAL["PoisonResist"] . "%)");
		print ("</span><br />\n");
	}

	//	しぼーしてるかどうか確認する。
	function CharJudgeDead()
	{
		if ($this->char->HP < 1 && $this->char->STATE !== STATE_DEAD)
		{ //しぼー
			$this->char->STATE = STATE_DEAD;
			$this->char->HP = 0;
			$this->char->ResetExpect();

			return true;
		}
	}

	//	生存状態にする。
	function GetNormal($mes = false)
	{
		if ($this->char->STATE === STATE_ALIVE) return true;
		if ($this->char->STATE === STATE_DEAD)
		{
			//死亡状態
			if ($mes) print ($this->char->Name('bold') . ' <span class="recover">revived</span>!<br />' . "\n");
			$this->char->STATE = STATE_ALIVE;
			return true;
		}
		if ($this->char->STATE === STATE_POISON)
		{
			//毒状態
			if ($mes) print ($this->char->Name('bold') . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->char->STATE = STATE_ALIVE;
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
		$Before = $this->char->HP;
		$this->char->HP -= $damage; // HPを減らす。
		if ($show) $this->char->ShowValueChange($Before, $this->char->HP);
	}

	//	HPへのダメージ(0以下になるなら1になる。)
	function HpDamage2($damage)
	{
		$Before = $this->char->HP;
		$this->char->HP -= $damage;
		// $DoNotDie=true ならHPが1を下回った場合1にする。
		if ($this->char->HP < 1) $this->char->HP = 1;
		$this->char->ShowValueChange($Before, $this->char->HP);
	}

	//	HPのパーセント
	function HpPercent()
	{
		if ($this->char->MAXHP == 0) return 0;
		$p = ($this->char->HP / $this->char->MAXHP) * 100;
		return $p;
	}

	//	SPのパーセント
	function SpPercent()
	{
		if ($this->char->MAXSP == 0) return 0;
		$p = ($this->char->SP / $this->char->MAXSP) * 100;
		return $p;
	}

	//	SPへのダメージ(消費)
	function SpDamage($damage, $show = true)
	{
		$Before = $this->char->SP;
		$this->char->SP -= $damage;
		if ($this->char->SP < 1) $this->char->SP = 0;
		if ($show) $this->char->ShowValueChange($Before, $this->char->SP);
	}

	//	HP回復
	function HpRecover($recover)
	{
		$Before = $this->char->HP;
		$this->char->HP += $recover;
		if ($this->char->MAXHP < $this->char->HP)
		{
			$this->char->HP = $this->char->MAXHP;
		}
		$this->char->ShowValueChange($Before, $this->char->HP);
	}

	//	SP回復
	function SpRecover($recover)
	{
		$Before = $this->char->SP;
		$this->char->SP += $recover;
		if ($this->char->MAXSP < $this->char->SP)
		{
			$this->char->SP = $this->char->MAXSP;
		}
		$this->char->ShowValueChange($Before, $this->char->SP);
	}

	function enterBattlefield($leave = false)
	{
		printf('<span class="%s">%s Lv.%d %s the Battlefield.</span>', $leave ? 'dmg' : 'result', $this->char->Name('blod'), $this->char->level, $leave ? 'leave' : 'enter');
	}

}
