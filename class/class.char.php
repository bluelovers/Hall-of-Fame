<?php 

include(DATA_JOB);

class char{

	// ファイルポインタ
	var $fp;
	var $file;
	var $Number;

	// 誰のキャラか?
	var $user;

	/*
		基本的な情報
		$gender	= (0=male 1=female)
	*/
	var $name, $gender, $job, $job_name, $img, $birth, $level, $exp;
	// ステ一タス
	var $maxhp, $hp, $maxsp, $sp, $str, $int, $dex, $spd, $luk;
	// ステ一タスポイントとか
	var $statuspoint;
	var $skillpoint;
	// 裝備
	var $weapon, $shield, $armor, $item;
	// 戰鬥その他
	var $position, $guard;
	// スキル
	var $skill;
	// 行動(判定、使うスキル)
	var $Pattern;
	var $PatternMemo;
	var $judge, $quantity, $action;

	// 戰鬥用變數(BattleVariable) デ一タには保存されない。
	var $team;
	var $IMG;
	var $MAXHP, $HP, $MAXSP, $SP, $STR, $INT, $DEX, $SPD, $LUK;
	var $STATE;//狀態(0=生存 1=しぼ一 2=毒狀態)
	var $POSITION;
	var $P_MAXHP, $P_MAXSP, $P_STR, $P_INT, $P_DEX, $P_SPD,$P_LUK;//單純なステ一タス補正(plus)
	var $M_MAXHP, $M_MAXSP;//單純なステ一タス補正(multipication)
	var $SPECIAL;// 特殊技能
	/*
		PoisonResist 毒抵抗
		HealBonus .
		Barrier
		Undead
	*/
	var $WEAPON;//武器タイプ
	var $atk, $def;// $atk=array(物理,魔法); $def=array(物理/,物理-,魔法/,魔法-);
	var $delay;//行動までの時間
	var $expect = false;//詠唱完了時に使うスキル
	var $expect_type;//詠唱完了時に使うスキルのタイプ(物理/魔法)
	var $expect_target;//↑のタ一ゲット

	var $ActCount;//合計行動回數
	var $JdgCount;//決定した判斷の回數=array()
//////////////////////////////////////////////////
	function char($file=false) {

		if(!$file)
			return 0;

		$this->Number	= basename($file,".dat");
		$this->file	= $file;
		$this->fp	= FileLock($file);

		$data	= ParseFileFP($this->fp);
		$this->SetCharData($data);
	}
//////////////////////////////////////////////////
//	ファイルポインタが開かれていれば閉じる
	function fpclose() {
		if(is_resource($this->fp)) {
			//print("who?.".$this->Name()."<br />\n");
			//print("FP閉じた");
			fclose($this->fp);
			unset($this->fp);
		}
	}
//////////////////////////////////////////////////
//	召喚力?召喚した時の召喚モンスタ一の強さ
	function SummonPower() {
		$DEX_PART	= sqrt($this->DEX) * 5;// DEX分の強化分
		$Strength	= 1 + ($DEX_PART + $this->LUK)/250;
		if($this->SPECIAL["Summon"])
			$Strength	*= (100+$this->SPECIAL["Summon"])/100;
		return $Strength;
	}
//////////////////////////////////////////////////
//	HPの犧牲
	function SacrificeHp($rate) {
		if(!$rate) return false;

		$SelfDamage	= ceil( $this->MAXHP*($rate/100) );
		if($this->POSITION != "front")
			$SelfDamage	*= 2;
		print("<span class=\"dmg\">".$this->Name(bold)." sacrifice ");
		print("<span class=\"bold\">$SelfDamage</span> HP</span>\n");
		$this->HpDamage($SelfDamage);
		print("</span><br />\n");
	}
//////////////////////////////////////////////////
//	特殊技能?の追加
	function GetSpecial($name,$value) {
		if(is_bool($value)) {
			$this->SPECIAL["$name"]	= $value;
		} else if (is_array($value)) {
			foreach($value as $key => $val) {
				$this->SPECIAL["$name"]["$key"]	+= $val;
			}
		} else {
			$this->SPECIAL["$name"]	+= $value;
		}
	}
//////////////////////////////////////////////////
//	HPSP持續回復
	function AutoRegeneration() {
		// HP回復
		if($this->SPECIAL["HpRegen"]) {
			$Regen	= round($this->MAXHP * $this->SPECIAL["HpRegen"]/100);
			print('<span class="recover">* </span>'.$this->Name(bold)."<span class=\"recover\"> 自動回復 <span class=\"bold\">".$Regen." HP</span></span> ");
			$this->HpRecover($Regen);
			print("<br />\n");
		}
		// SP回復
		if($this->SPECIAL["SpRegen"]) {
			$Regen	= round($this->MAXSP * $this->SPECIAL["SpRegen"]/100);
			print('<span class="support">* </span>'.$this->Name(bold)."<span class=\"support\"> 自動回復 <span class=\"bold\">".$Regen." SP</span></span> ");
			$this->SpRecover($Regen);
			print("<br />\n");
		}
	}
//////////////////////////////////////////////////
//	キャラステ一タスの一番上のやつ。
	function ShowCharDetail() {
		$P_MAXHP	= round($this->maxhp * $this->M_MAXHP/100) + $this->P_MAXHP;
		$P_MAXSP	= round($this->maxsp * $this->M_MAXSP/100) + $this->P_MAXSP;
		?>
<table>
<tr><td valign="top" style="width:180px"><?php $this->ShowCharLink();?>
</td><td valign="top" style="padding-right:20px">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td style="text-align:right">Exp : </td><td><?php print $this->exp?>/<?php print $this->CalcExpNeed()?></td></tr>
<tr><td style="text-align:right">HP : </td><td><?php print $this->maxhp?><?php if($P_MAXHP) print(" + {$P_MAXHP}");?></td></tr>
<tr><td style="text-align:right">SP : </td><td><?php print $this->maxsp?><?php if($P_MAXSP) print(" + {$P_MAXSP}");?></td></tr>
<tr><td style="text-align:right">STR : </td><td><?php print $this->str?><?php if($this->P_STR) print(" + {$this->P_STR}");?></td></tr>
<tr><td style="text-align:right">INT : </td><td><?php print $this->int?><?php if($this->P_INT) print(" + {$this->P_INT}");?></td></tr>
<tr><td style="text-align:right">DEX : </td><td><?php print $this->dex?><?php if($this->P_DEX) print(" + {$this->P_DEX}");?></td></tr>
<tr><td style="text-align:right">SPD : </td><td><?php print $this->spd?><?php if($this->P_SPD) print(" + {$this->P_SPD}");?></td></tr>
<tr><td style="text-align:right">LUK : </td><td><?php print $this->luk?><?php if($this->P_LUK) print(" + {$this->P_LUK}");?></td></tr>
</table>
</td><td valign="top">
<?php 
	if($this->SPECIAL["PoisonResist"])
		print("毒抵抗 +".$this->SPECIAL["PoisonResist"]."%<br />\n");
	if($this->SPECIAL["Pierce"]["0"])
		print("無視物理防禦傷害 +".$this->SPECIAL["Pierce"]["0"]."<br />\n");
	if($this->SPECIAL["Pierce"]["1"])
		print("無視魔法防禦傷害 +".$this->SPECIAL["Pierce"]["1"]."<br />\n");
	if($this->SPECIAL["Summon"])
		print("召喚力 +".$this->SPECIAL["Summon"]."%<br />\n");
?>
</td></tr></table>
<?php 
	}
//////////////////////////////////////////////////
//	誰のキャラか設定する
	function SetUser($user) {
		$this->user	= $user;
	}
//////////////////////////////////////////////////
//	チャ一ジ(詠唱)中の解除
	function ResetExpect() {
		$this->expect	= false;
		$this->expect_type	= false;
		$this->expect_target	= false;
	}
//////////////////////////////////////////////////
//	前列後列の移動
	function Move($posi) {
		//print($this->POSITION."->".$posi."<br />\n");
		if($posi == "front") {
			if($this->POSITION == "front")
				return false;
			$this->POSITION = "front";
			print($this->Name(bold)." 移到前排.<br />\n");
		} else {
			if($this->POSITION != "front")
				return false;
			$this->POSITION = "back";
			print($this->Name(bold)." 移到後排.<br />\n");
		}
	}

//////////////////////////////////////////////////
//	行動までの距離測定
	function nextDis() {
		if($this->STATE === DEAD)
			return 100;
		$distance	= (100 - $this->delay)/$this->DelayValue();
		return $distance;
	}
//////////////////////////////////////////////////
//	行動順リセット
	function DelayReset() {
		if(DELAY_TYPE === 0) {
			$this->delay	= $this->SPD;
		} else if(DELAY_TYPE === 1) {
			$this->delay	= 0;
		}
	}
//////////////////////////////////////////////////
//	行動を近づかせる。
	function Delay($no) {
		// 死亡中は增えないようにする
		if($this->STATE === DEAD){
			return false;
    }
		if(DELAY_TYPE === 0) {
			$this->delay	+= $no;
		} else if(DELAY_TYPE === 1) {
			$this->delay	+= $no * $this->DelayValue();
			//print("DELAY".$this->delay."<br />\n");
		}
	}
//////////////////////////////////////////////////
//	
	function DelayValue() {
		return sqrt($this->SPD) + DELAY_BASE;
	}
//////////////////////////////////////////////////
//	行動を遲らせる(Rate)
	function DelayByRate($No,$BaseDelay,$Show=false) {
		if(DELAY_TYPE === 0) {
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> >>> </span>');
			}
			$Delay	= ($BaseDelay - $this->SPD) * ($No/100);//遲らせる間隔
			$this->delay	-= $Delay;
			if($Show) {
				print(sprintf("%0.1f",$this->delay)."/".sprintf("%0.1f",$BaseDelay).")");
			}
		} else if(DELAY_TYPE === 1) {
			if($Show) {
				print("(".sprintf("%0.0f",$this->delay));
				print('<span style="font-size:80%"> >>> </span>');
			}
			$Delay	= $No;//遲らせる間隔
			$this->delay	-= $Delay;
			if($Show) {
				print(sprintf("%0.0f",floor($this->delay))."/".sprintf("%d",100).")");
			}
		}
	}
//////////////////////////////////////////////////
//	行動を早送りする(%)
	function DelayCut($No,$BaseDelay,$Show=false) {
		if(DELAY_TYPE === 0) {
			$Delay	= ($BaseDelay - $this->delay) * ($No/100);//早まらせる間隔
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> >>> </span>');
			}
			$this->delay	+= $Delay;
			if($Show) {
				print(sprintf("%0.1f",$this->delay)."/".sprintf("%0.1f",$BaseDelay).")");
			}
		} else if(DELAY_TYPE === 1) {
			$Delay	= (100 - $this->delay) * ($No/100);//早まらせる間隔
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> >>> </span>');
			}
			$this->delay	+= $Delay;
			if($Show) {
				print(sprintf("%0.0f",floor($this->delay))."/".sprintf("%d",100).")");
			}
		}
	}
//////////////////////////////////////////////////
//	即時行動させる。
	function Quick($delay) {
		if(DELAY_TYPE === 0)
			$this->delay	= $delay;
		else if(DELAY_TYPE === 1)
			$this->delay	= 100.1;
	}
//////////////////////////////////////////////////
//	名前を變える。
	function ChangeName($new) {
		$this->name	= $new;
	}
//////////////////////////////////////////////////
//	行動パタ一ンに追加する。
	function AddPattern($no) {
		if(!is_int($no) && $no < 0) return false;

		$this->PatternExplode();
		array_splice($this->judge,$no,0,"1000");
		array_pop($this->judge);
		array_splice($this->quantity,$no,0,"0");
		array_pop($this->quantity);
		array_splice($this->action,$no,0,"1000");
		array_pop($this->action);
		$this->CutPatterns();
		$this->PatternSave($this->judge,$this->quantity,$this->action);
		return true;
	}
//////////////////////////////////////////////////
//	行動パタ一ンを削除。
	function DeletePattern($no) {
		if(!is_int($no) && $no < 0) return false;

		$this->PatternExplode();
		array_splice($this->judge,$no,1);
		array_push($this->judge,"1000");
		array_splice($this->quantity,$no,1);
		array_push($this->quantity,"0");
		array_splice($this->action,$no,1);
		array_push($this->action,"1000");
		$this->CutPatterns();
		$this->PatternSave($this->judge,$this->quantity,$this->action);
		return true;
	}
//////////////////////////////////////////////////
//	限界設定數を超えていないか心配なので作った。。
	function CutPatterns() {
		$No	= $this->MaxPatterns();
		while($No < count($this->judge)) {
			array_pop($this->judge);
		}
		while($No < count($this->quantity)) {
			array_pop($this->quantity);
		}
		while($No < count($this->action)) {
			array_pop($this->action);
		}
	}
//////////////////////////////////////////////////
//	メモってあるパタ一ンと交換
	function ChangePatternMemo() {
		$temp	= $this->Pattern;
		$this->Pattern	= $this->PatternMemo;
		$this->PatternMemo	= $temp;
	/*
		//$serial	= serialize(array("judge"=>$this->judge,"action"=>$this->action));
		$serial	= implode("<>",$this->judge)."|".implode("<>",$this->action);

		if(!$this->PatternMemo) {
			$No	= $this->MaxPatterns();
			$judge	= array_fill(0,$No,"1000");
			$action	= array_fill(0,$No,"1000");
		} else {
			list($judge,$action)	= explode("|",$this->PatternMemo);
			$judge	= explode("<>",$judge);
			$action	= explode("<>",$action);
		}
		$this->PatternMemo	= $serial;
		$this->judge	= $judge;
		$this->action	= $action;
	*/
		return true;
	}
//////////////////////////////////////////////////
//	キャラを後衛化させる。
	function KnockBack($no=1) {
		if($this->POSITION == "front") {
			$this->POSITION = "back";
			print($this->Name(bold)."敲到後排!<br />\n");
		}
	}
//////////////////////////////////////////////////
//	
//	ステ一タス強化(+)
	function PlusSTR($no) {
		$this->STR	+= $no;
		print($this->Name(bold)." STR 提升 {$no}<br />\n");
	}
	function PlusINT($no) {
		$this->INT	+= $no;
		print($this->Name(bold)." INT 提升 {$no}<br />\n");
	}
	function PlusDEX($no) {
		$this->DEX	+= $no;
		print($this->Name(bold)." DEX 提升 {$no}<br />\n");
	}
	function PlusSPD($no) {
		$this->SPD	+= $no;
		print($this->Name(bold)." SPD 提升 {$no}<br />\n");
	}
	function PlusLUK($no) {
		$this->LUK	+= $no;
		print($this->Name(bold)." LUK 提升 {$no}<br />\n");
	}
//////////////////////////////////////////////////
//	ステ一タス強化(%)
	function UpMAXHP($no) {
		print($this->Name(bold)." MAXHP({$this->MAXHP}) 提升到 ");
		$this->MAXHP	= round($this->MAXHP * (1 + $no/100));
		print("{$this->MAXHP}<br />\n");
	}
	function UpMAXSP($no) {
		print($this->Name(bold)." MAXSP({$this->MAXSP}) 提升到 ");
		$this->MAXSP	= round($this->MAXSP * (1 + $no/100));
		print("{$this->MAXSP}<br />\n");
	}
	function UpSTR($no) {
		$this->STR	= round($this->STR * (1 + $no/100));
		if(($this->str * MAX_STATUS_MAXIMUM/100) < $this->STR) {
			print($this->Name(bold)." STR 提升到最大 (".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->STR = round($this->str * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." STR 提升 {$no}%<br />\n");
		}
	}
	function UpINT($no) {
		$this->INT	= round($this->INT * (1 + $no/100));
		if(($this->int * MAX_STATUS_MAXIMUM/100) < $this->INT) {
			print($this->Name(bold)." INT 提升到最大(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->INT = round($this->int * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." INT 提升 {$no}%<br />\n");
		}
	}
	function UpDEX($no) {
		$this->DEX	= round($this->DEX * (1 + $no/100));
		if(($this->dex * MAX_STATUS_MAXIMUM/100) < $this->DEX) {
			print($this->Name(bold)." DEX 提升到最大(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->DEX = round($this->dex * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." DEX 提升 {$no}%<br />\n");
		}
	}
	function UpSPD($no) {
		$this->SPD	= round($this->SPD * (1 + $no/100));
		if(($this->spd * MAX_STATUS_MAXIMUM/100) < $this->SPD) {
			print($this->Name(bold)." SPD 提升到最大(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->SPD = round($this->spd * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." SPD 提升 {$no}%<br />\n");
		}
	}
	function UpATK($no) {
		$this->atk["0"]	= round($this->atk["0"] * (1 + $no/100));
		print($this->Name(bold)." ATK 提升 {$no}%<br />\n");
	}
	function UpMATK($no) {
		$this->atk["1"]	= round($this->atk["1"] * (1 + $no/100));
		print($this->Name(bold)." MATK 提升 {$no}%<br />\n");
	}
	function UpDEF($no) {
		$up	= floor((100 - $this->def["0"]) * ($no/100) );
		$this->def["0"]	+= $up;
		print($this->Name(bold)." DEF 提升 {$no}%<br />\n");
	}
	function UpMDEF($no) {
		$up	= floor((100 - $this->def["2"]) * ($no/100) );
		print($this->Name(bold)." MDEF 提升 {$no}%<br />\n");
		$this->def["2"]	+= $up;
	}
//	ステ一タス弱體化(%)
	function DownMAXHP($no) {
		print($this->Name(bold)." MAXHP({$this->MAXHP}) 下降到 ");
		$this->MAXHP	= round($this->MAXHP * (1 - $no/100));
		if($this->MAXHP < $this->HP)
			$this->HP	= $this->MAXHP;
		print("{$this->MAXHP}<br />\n");
	}
	function DownMAXSP($no) {
		print($this->Name(bold)." MAXSP({$this->MAXSP}) 下降到 ");
		$this->MAXSP	= round($this->MAXSP * (1 - $no/100));
		if($this->MAXSP < $this->SP)
			$this->SP	= $this->MAXSP;
		print("{$this->MAXSP}<br />\n");
	}
	function DownSTR($no) {
		$this->STR	= round($this->STR * (1 - $no/100));
		print($this->Name(bold)." STR 下降 {$no}%<br />\n");
	}
	function DownINT($no) {
		$this->INT	= round($this->INT * (1 - $no/100));
		print($this->Name(bold)." INT 下降 {$no}%<br />\n");
	}
	function DownDEX($no) {
		$this->DEX	= round($this->DEX * (1 - $no/100));
		print($this->Name(bold)." DEX 下降 {$no}%<br />\n");
	}
	function DownSPD($no) {
		$this->SPD	= round($this->SPD * (1 - $no/100));
		print($this->Name(bold)." SPD 下降 {$no}%<br />\n");
	}
	function DownATK($no) {
		$this->atk["0"]	= round($this->atk["0"] * (1 - $no/100));
		print($this->Name(bold)." ATK 下降 {$no}%<br />\n");
	}
	function DownMATK($no) {
		$this->atk["1"]	= round($this->atk["1"] * (1 - $no/100));
		print($this->Name(bold)." MATK 下降 {$no}%<br />\n");
	}
	function DownDEF($no) {
		$this->def["0"]	= round($this->def["0"] * (1 - $no/100));
		print($this->Name(bold)." DEF 下降 {$no}%<br />\n");
	}
	function DownMDEF($no) {
		$this->def["2"]	= round($this->def["2"] * (1 - $no/100));
		print($this->Name(bold)." MDEF 下降 {$no}%<br />\n");
	}
//////////////////////////////////////////////////
//	キャラの指示の數
	function MaxPatterns() {
		if($this->int < 10)//1-9
			$no	= 2;
		else if($this->int < 15)//10-14
			$no	= 3;
		else if($this->int < 30)//15-29
			$no	= 4;
		else if($this->int < 50)//30-49
			$no	= 5;
		else if($this->int < 80)//50-79
			$no	= 6;
		else if($this->int < 120)//80-119
			$no	= 7;
		else if($this->int < 160)//120-159
			$no	= 8;
		else if($this->int < 200)//160-199
			$no	= 9;
		else if($this->int < 251)//
			$no	= 10;

		if(29 < $this->level)
			$no++;
		return $no;
	}

//////////////////////////////////////////////////
//	行動パタ一ンの變更。
	function ChangePattern($judge,$action) {
		$this->judge	= array();
		$this->action	= array();
		$max	= $this->MaxPatterns();//最低判斷數
		$judge_list	= array_flip(JudgeList());
		$skill_list	= array_flip($this->skill);
		for($i=0; $i<$max; $i++) {//判斷順に記憶
			if(!$judge["$i"])	$this->judge[$i]	= 1000;
			if(!$action["$i"])	$this->action[$i]	= 1000;

			if( isset($judge_list[$judge[$i]]) && isset($skill_list[$action[$i]]) ) {
				$this->judge[$i]	= $judge[$i];
				$this->action[$i]	= $action[$i];
			}
		}
		if($max < count($this->judge))
			return false;
		return true;
	}
//////////////////////////////////////////////////
//	毒ダメ一ジ
	function PoisonDamage($multiply=1) {
		if($this->STATE !== 2) return false;

		$poison	= $this->PoisonDamageFormula($multiply);
		print("<span class=\"spdmg\">".$this->Name(bold)." 由於中毒受到 ");
		print("<span class=\"bold\">$poison</span> 傷害.\n");
		$this->HpDamage2($poison);
		print("</span><br />\n");
	}
//////////////////////////////////////////////////
//	毒ダメ一ジの公式
	function PoisonDamageFormula($multiply=1) {
		$damage	= round($this->MAXHP * 0.10) + ceil($this->level/2);
		$damage	*= $multiply;
		return round($damage);
	}
//////////////////////////////////////////////////
//	毒の狀態 異常化 處理
	function GetPoison($BePoison) {
		if($this->STATE === 2)
			return false;
		if($this->SPECIAL["PoisonResist"]) {
			$prob	= mt_rand(0,99);
			$BePoison	*= (1 - $this->SPECIAL["PoisonResist"]/100);
			if($prob < $BePoison) {
				$this->STATE = 2;
				return true;
			} else {
				return "BLOCK";
			}
		}
		$this->STATE = 2;
		return true;
	}
//////////////////////////////////////////////////
//	毒耐性を得る
	function GetPoisonResist($no) {
		$Add	= (100 - $this->SPECIAL["PoisonResist"]) * ($no/100);
		$Add	= round($Add);
		$this->SPECIAL["PoisonResist"]	+= $Add;
		print('<span class="support">');
		print($this->Name(bold)." got PoisonResist!(".$this->SPECIAL["PoisonResist"]."%)");
		print("</span><br />\n");
	}
//////////////////////////////////////////////////
//	名前を返す
	function Name($string=false) {
		if($string)
			return "<span class=\"{$string}\">{$this->name}</span>";
		else
			return $this->name;
	}
//////////////////////////////////////////////////
//	必要經驗值
	function CalcExpNeed() {
		switch($this->level) {
			case 40:	$no	= 30000; break;
			case 41:	$no	= 40000; break;
			case 42:	$no	= 50000; break;
			case 43:	$no	= 60000; break;
			case 44:	$no	= 70000; break;
			case 45:	$no	= 80000; break;
			case 46:	$no	= 100000; break;
			case 47:	$no	= 250000; break;
			case 48:	$no	= 500000; break;
			case 49:	$no	= 999990; break;
			case 50:
			case (50 <= $this->level):
				$no	= "MAX"; break;
			case(21 < $this->level):
				$no	= 2*pow($this->level,3)+100*$this->level+100;
				$no	-= substr($no,-2);
				$no /= 5;
				break;
			default:
				$no	= pow($this->level-1,2)/2*100+100;
				$no /= 5;
				break;
		}
		return $no;
	}
//////////////////////////////////////////////////
//	經驗值を得る
	function GetExp($exp) {
		if($this->monster) return false;//モンスタ一は經驗值を得ない
		if(MAX_LEVEL <= $this->level) return false;//最大レベルの場合經驗值を得ない

		$this->exp	+= $exp;
		$need	= $this->CalcExpNeed($this->level);// 必要な經驗值
		if($need <= $this->exp) {
			$this->LevelUp();
			return true;
		}
	}
//////////////////////////////////////////////////
//	レベルあげる時の處理
	function LevelUp() {
		$this->exp	= 0;
		$this->level++;
		$this->statuspoint	+= GET_STATUS_POINT;//ステポをもらえる。
		$this->skillpoint	+= GET_SKILL_POINT;
	}
//////////////////////////////////////////////////
//	クラスチェンジ(轉職)
//	裝備をはずす。
	function ClassChange($job) {
		include_once(DATA_CLASSCHANGE);
		if(CanClassChange($this,$job)) {
			$this->job = $job;
			$this->SetJobData();
			$this->SetHpSp();
			//裝備を解除
			return true;
		}
		return false;
	}
//////////////////////////////////////////////////
//	アイテムを裝備する(職が裝備可能な物かどうかは調べない)
	function Equip($item) {
		$old	= array(//現在の裝備を反に保存しておく。
			"weapon"=> $this->weapon,
			"shield"=> $this->shield,
			"armor"	=> $this->armor,
			"item"	=> $this->item
			);

		$return	= array();//はずした裝備。

		switch($item["type"]) {//種類別
			case "劍"://片手武器
			case "匕首":
			case "矛":
			case "短柄斧":
			case "魔杖":
			case "錘":
			case "雙手劍"://兩手武器
			case "槍":
			case "斧":
			case "杖":
			case "弓":
			case "十字弓":
			case "鞭":
				// 既に裝備してある武器ははずす。
				if($this->weapon)
					$return[]	= $this->weapon;
				if($item["dh"] && $this->shield) {//兩手持ちの武器の場合。
					//盾を裝備していたらはずす。
					$return[]	= $this->shield;
					$this->shield	= NULL;
				}
				$this->weapon	= $item["no"];
				break;
			case "盾"://盾
			case "MainGauche":
			case "書":
				if($this->weapon) {//兩手武器ならそれははずす
					$weapon	= LoadItemData($this->weapon);
					if($weapon["dh"]) {
						$return[]	= $this->weapon;
						$this->weapon	= NULL;
					}
				}
				if($this->shield)//盾裝備していれば持ち物に加える
					$return[]	= $this->shield;
				$this->shield	= $item["no"];
				break;
			case "甲"://鎧
			case "衣服":
			case "長袍":
				if($this->armor)
					$return[]	= $this->armor;
				$this->armor	= $item["no"];
				break;
			case "道具":
				if($this->item)
					$return[]	= $this->item;
				$this->item	= $item["no"];
				break;
			default: return false;
		}

		// handleの計算。
		$weapon	= LoadItemData($this->weapon);
		$shield	= LoadItemData($this->shield);
		$armor	= LoadItemData($this->armor);
		$item2	= LoadItemData($this->item);// item2*

		$handle	= 0;
		$handle	= $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item2["handle"];
		if($this->GetHandle() < $handle) {//handle over
			foreach($old as $key => $val)//元に戾す。
				$this->{$key}	= $val;
			return false;
		}

		return $return;
	}
//////////////////////////////////////////////////
//	しぼ一してるかどうか確認する。
	function CharJudgeDead() {
		if($this->HP < 1 && $this->STATE !== DEAD) {//しぼ一
			$this->STATE	= DEAD;
			$this->HP	= 0;
			$this->ResetExpect();

			return true;
		}
	}
//////////////////////////////////////////////////
//	生存狀態にする。
	function GetNormal($mes=false) {
		if($this->STATE === ALIVE)
			return true;
		if($this->STATE === DEAD) {//死亡狀態
			if($mes)
				print($this->Name(bold).' <span class="recover">復活</span>!<br />'."\n");
			$this->STATE = 0;
			return true;
		}
		if($this->STATE === POISON) {//毒狀態
			if($mes)
				print($this->Name(bold)."的 <span class=\"spdmg\">中毒</span> 被治癒.<br />\n");
			$this->STATE = 0;
			return true;
		}
	}
//////////////////////////////////////////////////
//	戰鬥中のキャラ名,HP,SP を色を分けて表示する
//	それ以外にも必要な物があれば表示するようにした。
	function ShowHpSp() {
		if($this->STATE === 1)
			$sub	= " dmg";
		else if($this->STATE === 2)
			$sub	= " spdmg";
		//名前
		print("<span class=\"bold{$sub}\">{$this->name}</span>\n");
		// チャ一ジor詠唱
		if($this->expect_type === 0)
			print('<span class="charge">(蓄力)</span>'."\n");
		else if($this->expect_type === 1)
			print('<span class="charge">(詠唱)</span>'."\n");
		// HP,SP
		print("<div class=\"hpsp\">\n");
		$sub	= $this->STATE === 1 ? "dmg":"recover";
		print("<span class=\"{$sub}\">HP : {$this->HP}/{$this->MAXHP}</span><br />\n");//HP
		$sub	= $this->STATE === 1 ? "dmg":"support";
		print("<span class=\"{$sub}\">SP : {$this->SP}/{$this->MAXSP}</span>\n");
		print("</div>\n");//SP
	}
//////////////////////////////////////////////////
//	值の變化を表示する(ダメ一ジ受けた時とか)
	function ShowValueChange($from,$to) {
		print("({$from} > {$to})");
	}
//////////////////////////////////////////////////
//	HPへのダメ一ジ
	function HpDamage($damage,$show=true) {
		$Before	= $this->HP;
		$this->HP	-= $damage;// HPを減らす。
		if($show)
			$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	HPへのダメ一ジ(0以下になるなら1になる。)
	function HpDamage2($damage) {
		$Before	= $this->HP;
		$this->HP	-= $damage;
		// $DoNotDie=true ならHPが1を下回った場合1にする。
		if($this->HP < 1)
			$this->HP	= 1;
		$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	HPのパ一セント
	function HpPercent() {
		if($this->MAXHP == 0)
			return 0;
		$p	= ($this->HP/$this->MAXHP)*100;
		return $p;
	}
//////////////////////////////////////////////////
//	SPのパ一セント
	function SpPercent() {
		if($this->MAXSP == 0)
			return 0;
		$p	= ($this->SP/$this->MAXSP)*100;
		return $p;
	}
//////////////////////////////////////////////////
//	SPへのダメ一ジ(消費)
	function SpDamage($damage,$show=true) {
		$Before	= $this->SP;
		$this->SP	-= $damage;
		if($this->SP < 1)
			$this->SP	= 0;
		if($show)
		$this->ShowValueChange($Before,$this->SP);
	}
//////////////////////////////////////////////////
//	HP回復
	function HpRecover($recover) {
		$Before	= $this->HP;
		$this->HP	+= $recover;
		if($this->MAXHP < $this->HP) {
			$this->HP	= $this->MAXHP;
		}
		$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	SP回復
	function SpRecover($recover) {
		$Before	= $this->SP;
		$this->SP	+= $recover;
		if($this->MAXSP < $this->SP) {
			$this->SP	= $this->MAXSP;
		}
		$this->ShowValueChange($Before,$this->SP);
	}
//////////////////////////////////////////////////
//	パッシブスキルを讀み迂む
	function LoadPassiveSkills() {
		// PassiveSkill
		foreach($this->skill as $no) {
			if($no < 7000 || 8000 <= $no) continue;

			$skill	= LoadSkillData($no);
			//	能力值上昇系
			if($skill["P_MAXHP"])
				$this->P_MAXHP	+= $skill["P_MAXHP"];
			if($skill["P_MAXSP"])
				$this->P_MAXSP	+= $skill["P_MAXSP"];
			if($skill["P_STR"])
				$this->P_STR	+= $skill["P_STR"];
			if($skill["P_INT"])
				$this->P_INT	+= $skill["P_INT"];
			if($skill["P_DEX"])
				$this->P_DEX	+= $skill["P_DEX"];
			if($skill["P_SPD"])
				$this->P_SPD	+= $skill["P_SPD"];
			if($skill["P_LUK"])
				$this->P_LUK	+= $skill["P_LUK"];

			//	特殊技能など($this->SPECIAL)
			if($skill["HealBonus"])
				$this->SPECIAL["HealBonus"]	+= $skill["HealBonus"];//....
		}
	}
//////////////////////////////////////////////////
	function SetBattleVariable($team=false) {
		// 再讀み迂みを防止できるか?
		if(isset($this->IMG))
			return false;

		$this->PatternExplode();
		$this->CutPatterns();

		// パッシブスキルを讀む
		$this->LoadPassiveSkills();
		$this->CalcEquips();

		$this->team		= $team;
		$this->IMG		= $this->img;
		$maxhp	+= $this->maxhp * (1 + ($this->M_MAXHP/100)) + $this->P_MAXHP;
		$this->MAXHP	= round($maxhp);
		$hp		+= $this->hp * (1 + ($this->M_MAXHP/100)) + $this->P_MAXHP;
		$this->HP		= round($hp);
		$maxsp	+= $this->maxsp * (1 + ($this->M_MAXSP/100)) + $this->P_MAXSP;
		$this->MAXSP	= round($maxsp);
		$sp		+= $this->sp * (1 + ($this->M_MAXSP/100)) + $this->P_MAXSP;
		$this->SP		= round($sp);
		$this->STR		= $this->str + $this->P_STR;
		$this->INT		= $this->int + $this->P_INT;
		$this->DEX		= $this->dex + $this->P_DEX;
		$this->SPD		= $this->spd + $this->P_SPD;
		$this->LUK		= $this->luk + $this->P_LUK;
		$this->POSITION	= $this->position;
		$this->STATE	= 0;//生存狀態にする

		$this->expect	= false;//(數值=詠唱中 false=待機中)
		$this->ActCount	= 0;//行動回數
		$this->JdgCount	= array();//決定した判斷の回數
	}
//////////////////////////////////////////////////
//	キャラの攻擊力と防禦力,裝備性能を計算する
	function CalcEquips() {
		if($this->monster) return false;//mobは設定せんでいい
		$equip	= array("weapon","shield","armor","item");//裝備箇所
		$this->atk	= array(0,0);
		$this->def	= array(0,0,0,0);
		foreach($equip as $place) {
			if(!$this->{$place}) continue;
			// 武器タイプの記憶

			$item	= LoadItemData($this->{$place});
			if($place == "weapon")
					$this->WEAPON	= $item["type"];
			$this->atk[0]	+= $item[atk][0];//物理攻擊力
			$this->atk[1]	+= $item[atk][1];//魔法〃
			$this->def[0]	+= $item[def][0];//物理防禦(÷)
			$this->def[1]	+= $item[def][1];//〃(－)
			$this->def[2]	+= $item[def][2];//魔法防禦(÷)
			$this->def[3]	+= $item[def][3];//〃(－)

			$this->P_MAXHP	+= $item["P_MAXHP"];
			$this->M_MAXHP	+= $item["M_MAXHP"];
			$this->P_MAXSP	+= $item["P_MAXSP"];
			$this->M_MAXSP	+= $item["M_MAXSP"];

			$this->P_STR	+= $item["P_STR"];
			$this->P_INT	+= $item["P_INT"];
			$this->P_DEX	+= $item["P_DEX"];
			$this->P_SPD	+= $item["P_SPD"];
			$this->P_LUK	+= $item["P_LUK"];

			if($item["P_SUMMON"])
				$this->GetSpecial("Summon",$item["P_SUMMON"]);
			// 防禦無視の攻擊力
			if($item["P_PIERCE"])
				$this->GetSpecial("Pierce",$item["P_PIERCE"]);
		}
	}
//////////////////////////////////////////////////
	function ShowCharWithLand($land) {
		?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?php print IMG_OTHER."land_".$land.".gif"?>);">
	<?php $this->ShowImage()?>
	</div>
	<?php print $this->name?><br>Lv.<?php print $this->level?>
	</div><?php 
	}

//////////////////////////////////////////////////
//	キャラデ一タの保存
	function SaveCharData($id=false) {
		// モンスタ一は保存しない。
		//if($this->monster)	return false;

		if($id) {
			$dir	= USER.$id;
		} else {
			if(!$this->user) return false;
			$dir	= USER.$this->user;
		}
		// ユ一ザ一が存在しない場合保存しない
		if(!file_exists($dir))
			return false;

		if(isset($this->file))
			$file	= $this->file;
		else
			$file	= $dir."/".$this->birth.".dat";

		if(file_exists($file) && $this->fp) {
			//sleep(10);//ファイルロック確認用
			WriteFileFP($this->fp,$this->DataSavingFormat());
			$this->fpclose();
		} else {
			WriteFile($file,$this->DataSavingFormat());
		}

	}

//////////////////////////////////////////////////
	function DataSavingFormat() {
		$Save	= array("name","gender","job","birth","level","exp",
		"statuspoint","skillpoint",
		//"maxhp","hp","maxsp","sp",// (2007/9/30 保存しなくなった)
		"str","int","dex","spd","luk",
		"weapon","shield","armor","item",
		"position","guard",
		"skill",
		//"judge","action",
		"Pattern",
		"PatternMemo",
		//モンスタ一專用
		//"monster","land","family","monster_message"//保存する必要無くなった
		);
		//$Save	= get_object_vars($this);
		foreach($Save as $val) {
			if (!isset($this->{$val})) continue;
			$text	.= "$val=".(is_array($this->{$val}) ? implode("<>",$this->{$val}) : $this->{$val})."\n";
		}
		return $text;
	}

//////////////////////////////////////////////////
	function ShowChar() {
		static $flag = 0;

		$flag++;
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpetの並びを交互にする
			$flag++;
		?>
<div class="carpet_frame">
<div class="carpet<?php print $flag%2?>"><?php $this->ShowImage();?></div>
<?php print $this->name?><br>Lv.<?php print $this->level?> <?php print $this->job_name?>
</div><?php 
	}

//////////////////////////////////////////////////
	function ShowCharLink() {//$array=色々
		static $flag = 0;

		$flag++;
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpetの並びを交互にする
			$flag++;
		?>
<div class="carpet_frame">
<div class="carpet<?php print $flag%2?>">
<a href="?char=<?php print $this->Number?>"><?php $this->ShowImage();?></a></div>
<?php print $this->name?><?php if($this->statuspoint)print('<span class="bold charge">*</span>');?><br>Lv.<?php print $this->level?> <?php print $this->job_name?>
</div><?php 
	}

//////////////////////////////////////////////////
//	checkboxも表示する
	function ShowCharRadio($birth,$checked=null) {
		static $flag = 0;

		$flag++;
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpetの並びを交互にする
			$flag++;

// onclick="Element.toggleClassName(this,'unselect')"

		?>
<div class="carpet_frame">
<div class="carpet<?php print $flag%2?>">
<a href="?char=<?php print $this->birth?>"><?php $this->ShowImage();?></a>
</div>

<div onClick="toggleCheckBox('<?php print $flag?>')" id="text<?php print $flag?>" <?php print($checked?null:' class="unselect"');?>>
<?php print $this->name?>
<?php if($this->statuspoint)print('<span class="bold charge">*</span>');?><br />
Lv.<?php print $this->level?> <?php print $this->job_name?>

</div>
<input type="checkbox" onclick="Element.toggleClassName('text<?php print $flag?>','unselect')" id="box<?php print $flag?>" name="char_<?php print $birth?>" value="1"<?php print $checked?>>

</div><?php 
	}
//////////////////////////////////////////////////
//	戰鬥時のチ一ムを設定(あんまり使ってない)
	function SetTeam($no) {
		$this->team	= $no;
	}
//////////////////////////////////////////////////
//	IMGタグで畫像を表示するのみ
	function GetImageURL($dir) {
		if(file_exists(IMG_CHAR.$this->img)) {
			if($this->STATE === DEAD) {
				$img = $dir.$this->img;
				if(!file_exists($img)) {
					return $dir.CHAR_NO_IMAGE;
				}
			}
			return $dir.$this->img;
		} else {
			return $dir.CHAR_NO_IMAGE;
		}
	}
//////////////////////////////////////////////////
//	IMGタグで畫像を表示するのみ
	function ShowImage($class=false) {
		$url = $this->GetImageURL(IMG_CHAR);
		if($class)
			print('<img src="'.$url.'" class="'.$class.'">');
		else
			print('<img src="'.$url.'">');
	}
//////////////////////////////////////////////////
//	HPとSPを計算して設定する
	function SetHpSp()
	// $coe=array(HP,SP係數);
	{
		$MaxStatus	= MAX_STATUS;//最高ステ一タス(じゃなくてもいいです)

		$jobdata		= LoadJobData($this->job);// 2回讀み迂んでるから直すべき
		$coe	= $jobdata["coe"];

		$div		= $MaxStatus * $MaxStatus;
		$RevStr		= $MaxStatus - $this->str;
		$RevInt		= $MaxStatus - $this->int;

		$this->maxhp	= 100 * $coe[0] * (1 + ($this->level - 1)/49) * (1 + ($div - $RevStr*$RevStr)/$div);
		$this->maxsp	= 100 * $coe[1] * (1 + ($this->level - 1)/49) * (1 + ($div - $RevInt*$RevInt)/$div);

		$this->maxhp	= round($this->maxhp);
		$this->maxsp	= round($this->maxsp);
	}
//////////////////////////////////////////////////
//	handle計算
	function GetHandle() {
		$handle	= 5 + floor($this->level / 10) + floor($this->dex / 5);
		return $handle;
	}
//////////////////////////////////////////////////
//	ポイントを消費して技を覺える。
	function LearnNewSkill($no) {
		include_once(DATA_SKILL_TREE);
		$tree	= LoadSkillTree($this);

		//習得可能技に覺えようとしてるヤツなけりゃ終了
		if(!in_array($_POST["newskill"],$tree))
			return array(false,"沒有技能樹");
		$skill	= LoadSKillData($no);
		//もし習得濟みなら?
		if(in_array($no,$this->skill))
			return array(false,"{$skill[name]} 已經習得.");
		if($this->UseSkillPoint($skill["learn"])) {
			$this->GetNewSkill($skill["no"]);
			//$this->SaveCharData();
			return array(true,$this->Name()."  {$skill[name]} 已經習得。");
		} else
			return array(false,"技能點數不足");
	}
//////////////////////////////////////////////////
//	新ワザを追加する。
	function GetNewSkill($no) {
		$this->skill[]	= $no;
		asort($this->skill);
	}
//////////////////////////////////////////////////
//	スキルポイントを消費する
	function UseSKillPoint($no) {
		if($no <= $this->skillpoint) {
			$this->skillpoint	-= $no;
			return true;
		}
		return false;
	}
//////////////////////////////////////////////////
//	經驗值を出す(モンスタ一だけ?)
	function DropExp() {
		if(isset($this->exphold)) {
			$exp	= $this->exphold;
			$this->exphold	= round($exp/2);
			return $exp;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////////
//	お金を出す(モンスタ一だけ?)
	function DropMoney() {
		if(isset($this->moneyhold)) {
			$money	= $this->moneyhold;
			$this->moneyhold	= 0;
			return $money;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////////
//	アイテムを落とす(モンスタ一だけ?)
	function DropItem() {
		if($this->itemdrop) {
			$item	= $this->itemdrop;
			// 一度落としたアイテムは消す
			$this->itemdrop	= false;
			return $item;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////////
//	
	function SetJobData() {
		if($this->job) {
			$jobdata		= LoadJobData($this->job);
			$this->job_name	= ($this->gender ? $jobdata["name_female"] : $jobdata["name_male"]);
			$this->img		= ($this->gender ? $jobdata["img_female"] : $jobdata["img_male"]);
		}
	}
//////////////////////////////////////////////////
//	パタ一ン文字列を配列にする。
//	****<>****<>****|****<>****<>****|****<>****<>****
	function PatternExplode() {
		//dump($this->judge);
		if($this->judge)
			return false;
		$Pattern	= explode("|",$this->Pattern);
		$this->judge	= explode("<>",$Pattern["0"]);
		$this->quantity	= explode("<>",$Pattern["1"]);
		$this->action	= explode("<>",$Pattern["2"]);
	}
//////////////////////////////////////////////////
//	パタ一ン配列を保存する。
	function PatternSave($judge,$quantity,$action) {
		$this->Pattern	= implode("<>",$judge)."|".implode("<>",$quantity)."|".implode("<>",$action);
		return true;
	}
//////////////////////////////////////////////////
//	キャラクタ一を消す
	function DeleteChar() {
		if(!file_exists($this->file))
			return false;
		if($this->fp) {
			fclose($this->fp);
			unset($this->fp);
		}
		unlink($this->file);
	}
//////////////////////////////////////////////////
//	キャラの變數をセットする。
	function SetCharData(&$data) {
		$this->name	= $data["name"];
		$this->gender	= $data["gender"];
		$this->birth	= $data["birth"];
		$this->level	= $data["level"];
		$this->exp		= $data["exp"];
		$this->statuspoint	= $data["statuspoint"];
		$this->skillpoint	= $data["skillpoint"];

		$this->job		= $data["job"];
		$this->SetJobData();

		if ($data["img"])
			$this->img		= $data["img"];

		$this->str		= $data["str"];
		$this->int		= $data["int"];
		$this->dex		= $data["dex"];
		$this->spd		= $data["spd"];
		$this->luk		= $data["luk"];

		if (isset($data["maxhp"]) &&
			isset($data["hp"]) &&
			isset($data["maxsp"]) &&
			isset($data["sp"]) ) {
			$this->maxhp	= $data["maxhp"];
			$this->hp		= $data["hp"];
			$this->maxsp	= $data["maxsp"];
			$this->sp		= $data["sp"];
		} else {
			// HPSPを設定。HPSPを回復。そういうゲ一ムだから…
			$this->SetHpSp();
			$this->hp		= $this->maxhp;
			$this->sp		= $this->maxsp;
		}

		$this->weapon	= $data["weapon"];
		$this->shield	= $data["shield"];
		$this->armor	= $data["armor"];
		$this->item		= $data["item"];

		$this->position	= $data["position"];
		$this->guard	= $data["guard"];

		$this->skill	= (is_array($data["skill"]) ? $data["skill"] : explode("<>",$data["skill"]) );

		$this->Pattern	= $data["Pattern"];

		if($data["PatternMemo"])
			$this->PatternMemo	= $data["PatternMemo"];

		// モンスタ一のため？
		if(is_array($data["judge"]))
			$this->judge	= $data["judge"];
		//else
		//	$this->judge	= explode("<>",$data["judge"]);
		if(is_array($data["quantity"]))
			$this->quantity	= $data["quantity"];
		//else
		//	$this->quantity	= explode("<>",$data["quantity"]);
		if(is_array($data["action"]))
			$this->action	= $data["action"];
		//else
		//	$this->action	= explode("<>",$data["action"]);

		//モンスタ一專用
		if($this->monster	= $data["monster"]) {
			$this->exphold		= $data["exphold"];
			$this->moneyhold	= $data["moneyhold"];
			$this->itemdrop		= $data["itemdrop"];
			$this->atk	= $data["atk"];
			$this->def	= $data["def"];
			$this->SPECIAL	= $data["SPECIAL"];
		}
		if($data["summon"])
			$this->summon		= $data["summon"];
	}
}
?>
