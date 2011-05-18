<?

include(DATA_JOB);

class char{

	// �ե�����ݥ���
	var $fp;
	var $file;
	var $Number;

	// ï�Υ���餫?
	var $user;

	/*
		����Ū�ʾ���
		$gender	= (0=male 1=female)
	*/
	var $name, $gender, $job, $job_name, $img, $birth, $level, $exp;
	// ���ơ�����
	var $maxhp, $hp, $maxsp, $sp, $str, $int, $dex, $spd, $luk;
	// ���ơ������ݥ���ȤȤ�
	var $statuspoint;
	var $skillpoint;
	// ����
	var $weapon, $shield, $armor, $item;
	// ��Ʈ����¾
	var $position, $guard;
	// ������
	var $skill;
	// ��ư(Ƚ�ꡢ�Ȥ�������)
	var $Pattern;
	var $PatternMemo;
	var $judge, $quantity, $action;

	// ��Ʈ���ѿ�(BattleVariable) �ǡ����ˤ���¸����ʤ���
	var $team;
	var $IMG;
	var $MAXHP, $HP, $MAXSP, $SP, $STR, $INT, $DEX, $SPD, $LUK;
	var $STATE;//����(0=��¸ 1=���ܡ� 2=�Ǿ���)
	var $POSITION;
	var $P_MAXHP, $P_MAXSP, $P_STR, $P_INT, $P_DEX, $P_SPD,$P_LUK;//ñ��ʥ��ơ���������(plus)
	var $M_MAXHP, $M_MAXSP;//ñ��ʥ��ơ���������(multipication)
	var $SPECIAL;// �ü쵻ǽ
	/*
		PoisonResist ����
		HealBonus .
		Barrier
		Undead
	*/
	var $WEAPON;//��勵����
	var $atk, $def;// $atk=array(ʪ��,��ˡ); $def=array(ʪ��/,ʪ��-,��ˡ/,��ˡ-);
	var $delay;//��ư�ޤǤλ���
	var $expect = false;//�Ӿ���λ���˻Ȥ�������
	var $expect_type;//�Ӿ���λ���˻Ȥ�������Υ�����(ʪ��/��ˡ)
	var $expect_target;//���Υ������å�

	var $ActCount;//��׹�ư���
	var $JdgCount;//���ꤷ��Ƚ�Ǥβ��=array()
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
//	�ե�����ݥ��󥿤�������Ƥ�����Ĥ���
	function fpclose() {
		if(is_resource($this->fp)) {
			//print("who?.".$this->Name()."<br />\n");
			//print("FP�Ĥ���");
			fclose($this->fp);
			unset($this->fp);
		}
	}
//////////////////////////////////////////////////
//	������?�����������ξ�����󥹥����ζ���
	function SummonPower() {
		$DEX_PART	= sqrt($this->DEX) * 5;// DEXʬ�ζ���ʬ
		$Strength	= 1 + ($DEX_PART + $this->LUK)/250;
		if($this->SPECIAL["Summon"])
			$Strength	*= (100+$this->SPECIAL["Summon"])/100;
		return $Strength;
	}
//////////////////////////////////////////////////
//	HP�ε���
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
//	�ü쵻ǽ?���ɲ�
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
//	HPSP��³����
	function AutoRegeneration() {
		// HP����
		if($this->SPECIAL["HpRegen"]) {
			$Regen	= round($this->MAXHP * $this->SPECIAL["HpRegen"]/100);
			print('<span class="recover">* </span>'.$this->Name(bold)."<span class=\"recover\"> Auto Regenerate <span class=\"bold\">".$Regen." HP</span></span> ");
			$this->HpRecover($Regen);
			print("<br />\n");
		}
		// SP����
		if($this->SPECIAL["SpRegen"]) {
			$Regen	= round($this->MAXSP * $this->SPECIAL["SpRegen"]/100);
			print('<span class="support">* </span>'.$this->Name(bold)."<span class=\"support\"> Auto Regenerate <span class=\"bold\">".$Regen." SP</span></span> ");
			$this->SpRecover($Regen);
			print("<br />\n");
		}
	}
//////////////////////////////////////////////////
//	����饹�ơ������ΰ��־�Τ�ġ�
	function ShowCharDetail() {
		$P_MAXHP	= round($this->maxhp * $this->M_MAXHP/100) + $this->P_MAXHP;
		$P_MAXSP	= round($this->maxsp * $this->M_MAXSP/100) + $this->P_MAXSP;
		?>
<table>
<tr><td valign="top" style="width:180px"><?$this->ShowCharLink();?>
</td><td valign="top" style="padding-right:20px">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td style="text-align:right">Exp :&nbsp;</td><td><?=$this->exp?>/<?=$this->CalcExpNeed()?></td></tr>
<tr><td style="text-align:right">HP :&nbsp;</td><td><?=$this->maxhp?><?if($P_MAXHP) print(" + {$P_MAXHP}");?></td></tr>
<tr><td style="text-align:right">SP :&nbsp;</td><td><?=$this->maxsp?><?if($P_MAXSP) print(" + {$P_MAXSP}");?></td></tr>
<tr><td style="text-align:right">STR :&nbsp;</td><td><?=$this->str?><?if($this->P_STR) print(" + {$this->P_STR}");?></td></tr>
<tr><td style="text-align:right">INT :&nbsp;</td><td><?=$this->int?><?if($this->P_INT) print(" + {$this->P_INT}");?></td></tr>
<tr><td style="text-align:right">DEX :&nbsp;</td><td><?=$this->dex?><?if($this->P_DEX) print(" + {$this->P_DEX}");?></td></tr>
<tr><td style="text-align:right">SPD :&nbsp;</td><td><?=$this->spd?><?if($this->P_SPD) print(" + {$this->P_SPD}");?></td></tr>
<tr><td style="text-align:right">LUK :&nbsp;</td><td><?=$this->luk?><?if($this->P_LUK) print(" + {$this->P_LUK}");?></td></tr>
</table>
</td><td valign="top">
<?
	if($this->SPECIAL["PoisonResist"])
		print("���� +".$this->SPECIAL["PoisonResist"]."%<br />\n");
	if($this->SPECIAL["Pierce"]["0"])
		print("ʪ���ɸ�̵����᡼�� +".$this->SPECIAL["Pierce"]["0"]."<br />\n");
	if($this->SPECIAL["Pierce"]["1"])
		print("��ˡ�ɸ�̵����᡼�� +".$this->SPECIAL["Pierce"]["1"]."<br />\n");
	if($this->SPECIAL["Summon"])
		print("������ +".$this->SPECIAL["Summon"]."%<br />\n");
?>
</td></tr></table>
<?
	}
//////////////////////////////////////////////////
//	ï�Υ���餫���ꤹ��
	function SetUser($user) {
		$this->user	= $user;
	}
//////////////////////////////////////////////////
//	���㡼��(�Ӿ�)��β��
	function ResetExpect() {
		$this->expect	= false;
		$this->expect_type	= false;
		$this->expect_target	= false;
	}
//////////////////////////////////////////////////
//	�������ΰ�ư
	function Move($posi) {
		//print($this->POSITION."->".$posi."<br />\n");
		if($posi == "front") {
			if($this->POSITION == "front")
				return false;
			$this->POSITION = "front";
			print($this->Name(bold)." moved to front.<br />\n");
		} else {
			if($this->POSITION != "front")
				return false;
			$this->POSITION = "back";
			print($this->Name(bold)." moved to back.<br />\n");
		}
	}

//////////////////////////////////////////////////
//	��ư�ޤǤε�Υ¬��
	function nextDis() {
		if($this->STATE === DEAD)
			return 100;
		$distance	= (100 - $this->delay)/$this->DelayValue();
		return $distance;
	}
//////////////////////////////////////////////////
//	��ư��ꥻ�å�
	function DelayReset() {
		if(DELAY_TYPE === 0) {
			$this->delay	= $this->SPD;
		} else if(DELAY_TYPE === 1) {
			$this->delay	= 0;
		}
	}
//////////////////////////////////////////////////
//	��ư���Ť����롣
	function Delay($no) {
		// ��˴��������ʤ��褦�ˤ���
		if($this->STATE === DEAD)
			return false;

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
//	��ư���٤餻��(Rate)
	function DelayByRate($No,$BaseDelay,$Show=false) {
		if(DELAY_TYPE === 0) {
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$Delay	= ($BaseDelay - $this->SPD) * ($No/100);//�٤餻��ֳ�
			$this->delay	-= $Delay;
			if($Show) {
				print(sprintf("%0.1f",$this->delay)."/".sprintf("%0.1f",$BaseDelay).")");
			}
		} else if(DELAY_TYPE === 1) {
			if($Show) {
				print("(".sprintf("%0.0f",$this->delay));
				print('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$Delay	= $No;//�٤餻��ֳ�
			$this->delay	-= $Delay;
			if($Show) {
				print(sprintf("%0.0f",floor($this->delay))."/".sprintf("%d",100).")");
			}
		}
	}
//////////////////////////////////////////////////
//	��ư�������ꤹ��(%)
	function DelayCut($No,$BaseDelay,$Show=false) {
		if(DELAY_TYPE === 0) {
			$Delay	= ($BaseDelay - $this->delay) * ($No/100);//��ޤ餻��ֳ�
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$this->delay	+= $Delay;
			if($Show) {
				print(sprintf("%0.1f",$this->delay)."/".sprintf("%0.1f",$BaseDelay).")");
			}
		} else if(DELAY_TYPE === 1) {
			$Delay	= (100 - $this->delay) * ($No/100);//��ޤ餻��ֳ�
			if($Show) {
				print("(".sprintf("%0.1f",$this->delay));
				print('<span style="font-size:80%"> &gt;&gt;&gt; </span>');
			}
			$this->delay	+= $Delay;
			if($Show) {
				print(sprintf("%0.0f",floor($this->delay))."/".sprintf("%d",100).")");
			}
		}
	}
//////////////////////////////////////////////////
//	¨����ư�����롣
	function Quick($delay) {
		if(DELAY_TYPE === 0)
			$this->delay	= $delay;
		else if(DELAY_TYPE === 1)
			$this->delay	= 100.1;
	}
//////////////////////////////////////////////////
//	̾�����Ѥ��롣
	function ChangeName($new) {
		$this->name	= $new;
	}
//////////////////////////////////////////////////
//	��ư�ѥ�������ɲä��롣
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
//	��ư�ѥ����������
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
//	�³��������Ķ���Ƥ��ʤ������ۤʤΤǺ�ä�����
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
//	���äƤ���ѥ�����ȸ�
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
//	�������Ҳ������롣
	function KnockBack($no=1) {
		if($this->POSITION == "front") {
			$this->POSITION = "back";
			print($this->Name(bold)." knock backed!<br />\n");
		}
	}
//////////////////////////////////////////////////
//	
//	���ơ���������(+)
	function PlusSTR($no) {
		$this->STR	+= $no;
		print($this->Name(bold)." STR rise {$no}<br />\n");
	}
	function PlusINT($no) {
		$this->INT	+= $no;
		print($this->Name(bold)." INT rise {$no}<br />\n");
	}
	function PlusDEX($no) {
		$this->DEX	+= $no;
		print($this->Name(bold)." DEX rise {$no}<br />\n");
	}
	function PlusSPD($no) {
		$this->SPD	+= $no;
		print($this->Name(bold)." SPD rise {$no}<br />\n");
	}
	function PlusLUK($no) {
		$this->LUK	+= $no;
		print($this->Name(bold)." LUK rise {$no}<br />\n");
	}
//////////////////////////////////////////////////
//	���ơ���������(%)
	function UpMAXHP($no) {
		print($this->Name(bold)." MAXHP({$this->MAXHP}) extended to ");
		$this->MAXHP	= round($this->MAXHP * (1 + $no/100));
		print("{$this->MAXHP}<br />\n");
	}
	function UpMAXSP($no) {
		print($this->Name(bold)." MAXSP({$this->MAXSP}) extended to ");
		$this->MAXSP	= round($this->MAXSP * (1 + $no/100));
		print("{$this->MAXSP}<br />\n");
	}
	function UpSTR($no) {
		$this->STR	= round($this->STR * (1 + $no/100));
		if(($this->str * MAX_STATUS_MAXIMUM/100) < $this->STR) {
			print($this->Name(bold)." STR rise to the maximum(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->STR = round($this->str * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." STR rise {$no}%<br />\n");
		}
	}
	function UpINT($no) {
		$this->INT	= round($this->INT * (1 + $no/100));
		if(($this->int * MAX_STATUS_MAXIMUM/100) < $this->INT) {
			print($this->Name(bold)." INT rise to the maximum(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->INT = round($this->int * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." INT rise {$no}%<br />\n");
		}
	}
	function UpDEX($no) {
		$this->DEX	= round($this->DEX * (1 + $no/100));
		if(($this->dex * MAX_STATUS_MAXIMUM/100) < $this->DEX) {
			print($this->Name(bold)." DEX rise to the maximum(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->DEX = round($this->dex * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." DEX rise {$no}%<br />\n");
		}
	}
	function UpSPD($no) {
		$this->SPD	= round($this->SPD * (1 + $no/100));
		if(($this->spd * MAX_STATUS_MAXIMUM/100) < $this->SPD) {
			print($this->Name(bold)." SPD rise to the maximum(".MAX_STATUS_MAXIMUM."%).<br />\n");
			$this->SPD = round($this->spd * MAX_STATUS_MAXIMUM/100);
		} else {
			print($this->Name(bold)." SPD rise {$no}%<br />\n");
		}
	}
	function UpATK($no) {
		$this->atk["0"]	= round($this->atk["0"] * (1 + $no/100));
		print($this->Name(bold)." ATK rise {$no}%<br />\n");
	}
	function UpMATK($no) {
		$this->atk["1"]	= round($this->atk["1"] * (1 + $no/100));
		print($this->Name(bold)." MATK rise {$no}%<br />\n");
	}
	function UpDEF($no) {
		$up	= floor((100 - $this->def["0"]) * ($no/100) );
		$this->def["0"]	+= $up;
		print($this->Name(bold)." DEF rise {$no}%<br />\n");
	}
	function UpMDEF($no) {
		$up	= floor((100 - $this->def["2"]) * ($no/100) );
		print($this->Name(bold)." MDEF rise {$no}%<br />\n");
		$this->def["2"]	+= $up;
	}
//	���ơ��������β�(%)
	function DownMAXHP($no) {
		print($this->Name(bold)." MAXHP({$this->MAXHP}) down to ");
		$this->MAXHP	= round($this->MAXHP * (1 - $no/100));
		if($this->MAXHP < $this->HP)
			$this->HP	= $this->MAXHP;
		print("{$this->MAXHP}<br />\n");
	}
	function DownMAXSP($no) {
		print($this->Name(bold)." MAXSP({$this->MAXSP}) down to ");
		$this->MAXSP	= round($this->MAXSP * (1 - $no/100));
		if($this->MAXSP < $this->SP)
			$this->SP	= $this->MAXSP;
		print("{$this->MAXSP}<br />\n");
	}
	function DownSTR($no) {
		$this->STR	= round($this->STR * (1 - $no/100));
		print($this->Name(bold)." STR down {$no}%<br />\n");
	}
	function DownINT($no) {
		$this->INT	= round($this->INT * (1 - $no/100));
		print($this->Name(bold)." INT down {$no}%<br />\n");
	}
	function DownDEX($no) {
		$this->DEX	= round($this->DEX * (1 - $no/100));
		print($this->Name(bold)." DEX down {$no}%<br />\n");
	}
	function DownSPD($no) {
		$this->SPD	= round($this->SPD * (1 - $no/100));
		print($this->Name(bold)." SPD down {$no}%<br />\n");
	}
	function DownATK($no) {
		$this->atk["0"]	= round($this->atk["0"] * (1 - $no/100));
		print($this->Name(bold)." ATK down {$no}%<br />\n");
	}
	function DownMATK($no) {
		$this->atk["1"]	= round($this->atk["1"] * (1 - $no/100));
		print($this->Name(bold)." MATK down {$no}%<br />\n");
	}
	function DownDEF($no) {
		$this->def["0"]	= round($this->def["0"] * (1 - $no/100));
		print($this->Name(bold)." DEF down {$no}%<br />\n");
	}
	function DownMDEF($no) {
		$this->def["2"]	= round($this->def["2"] * (1 - $no/100));
		print($this->Name(bold)." MDEF down {$no}%<br />\n");
	}
//////////////////////////////////////////////////
//	�����λؼ��ο�
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
//	��ư�ѥ�������ѹ���
	function ChangePattern($judge,$action) {
		$this->judge	= array();
		$this->action	= array();
		$max	= $this->MaxPatterns();//����Ƚ�ǿ�
		$judge_list	= array_flip(JudgeList());
		$skill_list	= array_flip($this->skill);
		for($i=0; $i<$max; $i++) {//Ƚ�ǽ�˵���
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
//	�ǥ��᡼��
	function PoisonDamage($multiply=1) {
		if($this->STATE !== 2) return false;

		$poison	= $this->PoisonDamageFormula($multiply);
		print("<span class=\"spdmg\">".$this->Name(bold)." got ");
		print("<span class=\"bold\">$poison</span> damage by poison.\n");
		$this->HpDamage2($poison);
		print("</span><br />\n");
	}
//////////////////////////////////////////////////
//	�ǥ��᡼���θ���
	function PoisonDamageFormula($multiply=1) {
		$damage	= round($this->MAXHP * 0.10) + ceil($this->level/2);
		$damage	*= $multiply;
		return round($damage);
	}
//////////////////////////////////////////////////
//	�Ǥξ��� �۾ﲽ ����
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
//	������������
	function GetPoisonResist($no) {
		$Add	= (100 - $this->SPECIAL["PoisonResist"]) * ($no/100);
		$Add	= round($Add);
		$this->SPECIAL["PoisonResist"]	+= $Add;
		print('<span class="support">');
		print($this->Name(bold)." got PoisonResist!(".$this->SPECIAL["PoisonResist"]."%)");
		print("</span><br />\n");
	}
//////////////////////////////////////////////////
//	̾�����֤�
	function Name($string=false) {
		if($string)
			return "<span class=\"{$string}\">{$this->name}</span>";
		else
			return $this->name;
	}
//////////////////////////////////////////////////
//	ɬ�׷и���
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
//	�и��ͤ�����
	function GetExp($exp) {
		if($this->monster) return false;//��󥹥����Ϸи��ͤ����ʤ�
		if(MAX_LEVEL <= $this->level) return false;//�����٥�ξ��и��ͤ����ʤ�

		$this->exp	+= $exp;
		$need	= $this->CalcExpNeed($this->level);// ɬ�פʷи���
		if($need <= $this->exp) {
			$this->LevelUp();
			return true;
		}
	}
//////////////////////////////////////////////////
//	��٥뤢������ν���
	function LevelUp() {
		$this->exp	= 0;
		$this->level++;
		$this->statuspoint	+= GET_STATUS_POINT;//���ƥݤ��館�롣
		$this->skillpoint	+= GET_SKILL_POINT;
	}
//////////////////////////////////////////////////
//	���饹������(ž��)
//	������Ϥ�����
	function ClassChange($job) {
		include_once(DATA_CLASSCHANGE);
		if(CanClassChange($this,$job)) {
			$this->job = $job;
			$this->SetJobData();
			$this->SetHpSp();
			//��������
			return true;
		}
		return false;
	}
//////////////////////////////////////////////////
//	�����ƥ����������(����������ǽ��ʪ���ɤ�����Ĵ�٤ʤ�)
	function Equip($item) {
		$old	= array(//���ߤ������򲾤���¸���Ƥ�����
			"weapon"=> $this->weapon,
			"shield"=> $this->shield,
			"armor"	=> $this->armor,
			"item"	=> $this->item
			);

		$return	= array();//�Ϥ�����������

		switch($item["type"]) {//������
			case "Sword"://�Ҽ����
			case "Dagger":
			case "Pike":
			case "Hatchet":
			case "Wand":
			case "Mace":
			case "TwoHandSword"://ξ�����
			case "Spear":
			case "Axe":
			case "Staff":
			case "Bow":
			case "CrossBow":
			case "Whip":
				// �����������Ƥ������ϤϤ�����
				if($this->weapon)
					$return[]	= $this->weapon;
				if($item["dh"] && $this->shield) {//ξ����������ξ�硣
					//����������Ƥ�����Ϥ�����
					$return[]	= $this->shield;
					$this->shield	= NULL;
				}
				$this->weapon	= $item["no"];
				break;
			case "Shield"://��
			case "MainGauche":
			case "Book":
				if($this->weapon) {//ξ�����ʤ餽��ϤϤ���
					$weapon	= LoadItemData($this->weapon);
					if($weapon["dh"]) {
						$return[]	= $this->weapon;
						$this->weapon	= NULL;
					}
				}
				if($this->shield)//���������Ƥ���л���ʪ�˲ä���
					$return[]	= $this->shield;
				$this->shield	= $item["no"];
				break;
			case "Armor"://��
			case "Cloth":
			case "Robe":
				if($this->armor)
					$return[]	= $this->armor;
				$this->armor	= $item["no"];
				break;
			case "Item":
				if($this->item)
					$return[]	= $this->item;
				$this->item	= $item["no"];
				break;
			default: return false;
		}

		// handle�η׻���
		$weapon	= LoadItemData($this->weapon);
		$shield	= LoadItemData($this->shield);
		$armor	= LoadItemData($this->armor);
		$item2	= LoadItemData($this->item);// item2*

		$handle	= 0;
		$handle	= $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item2["handle"];
		if($this->GetHandle() < $handle) {//handle over
			foreach($old as $key => $val)//�����᤹��
				$this->{$key}	= $val;
			return false;
		}

		return $return;
	}
//////////////////////////////////////////////////
//	���ܡ����Ƥ뤫�ɤ�����ǧ���롣
	function CharJudgeDead() {
		if($this->HP < 1 && $this->STATE !== DEAD) {//���ܡ�
			$this->STATE	= DEAD;
			$this->HP	= 0;
			$this->ResetExpect();

			return true;
		}
	}
//////////////////////////////////////////////////
//	��¸���֤ˤ��롣
	function GetNormal($mes=false) {
		if($this->STATE === ALIVE)
			return true;
		if($this->STATE === DEAD) {//��˴����
			if($mes)
				print($this->Name(bold).' <span class="recover">revived</span>!<br />'."\n");
			$this->STATE = 0;
			return true;
		}
		if($this->STATE === POISON) {//�Ǿ���
			if($mes)
				print($this->Name(bold)."'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = 0;
			return true;
		}
	}
//////////////////////////////////////////////////
//	��Ʈ��Υ����̾,HP,SP �򿧤�ʬ����ɽ������
//	����ʳ��ˤ�ɬ�פ�ʪ�������ɽ������褦�ˤ�����
	function ShowHpSp() {
		if($this->STATE === 1)
			$sub	= " dmg";
		else if($this->STATE === 2)
			$sub	= " spdmg";
		//̾��
		print("<span class=\"bold{$sub}\">{$this->name}</span>\n");
		// ���㡼��or�Ӿ�
		if($this->expect_type === 0)
			print('<span class="charge">(charging)</span>'."\n");
		else if($this->expect_type === 1)
			print('<span class="charge">(casting)</span>'."\n");
		// HP,SP
		print("<div class=\"hpsp\">\n");
		$sub	= $this->STATE === 1 ? "dmg":"recover";
		print("<span class=\"{$sub}\">HP : {$this->HP}/{$this->MAXHP}</span><br />\n");//HP
		$sub	= $this->STATE === 1 ? "dmg":"support";
		print("<span class=\"{$sub}\">SP : {$this->SP}/{$this->MAXSP}</span>\n");
		print("</div>\n");//SP
	}
//////////////////////////////////////////////////
//	�ͤ��Ѳ���ɽ������(���᡼�����������Ȥ�)
	function ShowValueChange($from,$to) {
		print("({$from} &gt; {$to})");
	}
//////////////////////////////////////////////////
//	HP�ؤΥ��᡼��
	function HpDamage($damage,$show=true) {
		$Before	= $this->HP;
		$this->HP	-= $damage;// HP�򸺤餹��
		if($show)
			$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	HP�ؤΥ��᡼��(0�ʲ��ˤʤ�ʤ�1�ˤʤ롣)
	function HpDamage2($damage) {
		$Before	= $this->HP;
		$this->HP	-= $damage;
		// $DoNotDie=true �ʤ�HP��1�򲼲�ä����1�ˤ��롣
		if($this->HP < 1)
			$this->HP	= 1;
		$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	HP�Υѡ������
	function HpPercent() {
		if($this->MAXHP == 0)
			return 0;
		$p	= ($this->HP/$this->MAXHP)*100;
		return $p;
	}
//////////////////////////////////////////////////
//	SP�Υѡ������
	function SpPercent() {
		if($this->MAXSP == 0)
			return 0;
		$p	= ($this->SP/$this->MAXSP)*100;
		return $p;
	}
//////////////////////////////////////////////////
//	SP�ؤΥ��᡼��(����)
	function SpDamage($damage,$show=true) {
		$Before	= $this->SP;
		$this->SP	-= $damage;
		if($this->SP < 1)
			$this->SP	= 0;
		if($show)
		$this->ShowValueChange($Before,$this->SP);
	}
//////////////////////////////////////////////////
//	HP����
	function HpRecover($recover) {
		$Before	= $this->HP;
		$this->HP	+= $recover;
		if($this->MAXHP < $this->HP) {
			$this->HP	= $this->MAXHP;
		}
		$this->ShowValueChange($Before,$this->HP);
	}
//////////////////////////////////////////////////
//	SP����
	function SpRecover($recover) {
		$Before	= $this->SP;
		$this->SP	+= $recover;
		if($this->MAXSP < $this->SP) {
			$this->SP	= $this->MAXSP;
		}
		$this->ShowValueChange($Before,$this->SP);
	}
//////////////////////////////////////////////////
//	�ѥå��֥�������ɤ߹���
	function LoadPassiveSkills() {
		// PassiveSkill
		foreach($this->skill as $no) {
			if($no < 7000 || 8000 <= $no) continue;

			$skill	= LoadSkillData($no);
			//	ǽ���;徺��
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

			//	�ü쵻ǽ�ʤ�($this->SPECIAL)
			if($skill["HealBonus"])
				$this->SPECIAL["HealBonus"]	+= $skill["HealBonus"];//....
		}
	}
//////////////////////////////////////////////////
	function SetBattleVariable($team=false) {
		// ���ɤ߹��ߤ��ɻߤǤ��뤫?
		if(isset($this->IMG))
			return false;

		$this->PatternExplode();
		$this->CutPatterns();

		// �ѥå��֥�������ɤ�
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
		$this->STATE	= 0;//��¸���֤ˤ���

		$this->expect	= false;//(����=�Ӿ��� false=�Ե���)
		$this->ActCount	= 0;//��ư���
		$this->JdgCount	= array();//���ꤷ��Ƚ�Ǥβ��
	}
//////////////////////////////////////////////////
//	�����ι����Ϥ��ɸ���,������ǽ��׻�����
	function CalcEquips() {
		if($this->monster) return false;//mob�����ꤻ��Ǥ���
		$equip	= array("weapon","shield","armor","item");//�����ս�
		$this->atk	= array(0,0);
		$this->def	= array(0,0,0,0);
		foreach($equip as $place) {
			if(!$this->{$place}) continue;
			// ��勵���פε���

			$item	= LoadItemData($this->{$place});
			if($place == "weapon")
					$this->WEAPON	= $item["type"];
			$this->atk[0]	+= $item[atk][0];//ʪ��������
			$this->atk[1]	+= $item[atk][1];//��ˡ��
			$this->def[0]	+= $item[def][0];//ʪ���ɸ�(��)
			$this->def[1]	+= $item[def][1];//��(��)
			$this->def[2]	+= $item[def][2];//��ˡ�ɸ�(��)
			$this->def[3]	+= $item[def][3];//��(��)

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
			// �ɸ�̵��ι�����
			if($item["P_PIERCE"])
				$this->GetSpecial("Pierce",$item["P_PIERCE"]);
		}
	}
//////////////////////////////////////////////////
	function ShowCharWithLand($land) {
		?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?=IMG_OTHER."land_".$land.".gif"?>);">
	<?$this->ShowImage()?>
	</div>
	<?=$this->name?><br>Lv.<?=$this->level?>
	</div><?
	}

//////////////////////////////////////////////////
//	�����ǡ�������¸
	function SaveCharData($id=false) {
		// ��󥹥�������¸���ʤ���
		//if($this->monster)	return false;

		if($id) {
			$dir	= USER.$id;
		} else {
			if(!$this->user) return false;
			$dir	= USER.$this->user;
		}
		// �桼������¸�ߤ��ʤ������¸���ʤ�
		if(!file_exists($dir))
			return false;

		if(isset($this->file))
			$file	= $this->file;
		else
			$file	= $dir."/".$this->birth.".dat";

		if(file_exists($file) && $this->fp) {
			//sleep(10);//�ե������å���ǧ��
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
		//"maxhp","hp","maxsp","sp",// (2007/9/30 ��¸���ʤ��ʤä�)
		"str","int","dex","spd","luk",
		"weapon","shield","armor","item",
		"position","guard",
		"skill",
		//"judge","action",
		"Pattern",
		"PatternMemo",
		//��󥹥�������
		//"monster","land","family","monster_message"//��¸����ɬ��̵���ʤä�
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
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpet���¤Ӥ��ߤˤ���
			$flag++;
		?>
<div class="carpet_frame">
<div class="carpet<?=$flag%2?>"><?$this->ShowImage();?></div>
<?=$this->name?><br>Lv.<?=$this->level?>&nbsp;<?=$this->job_name?>
</div><?
	}

//////////////////////////////////////////////////
	function ShowCharLink() {//$array=����
		static $flag = 0;

		$flag++;
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpet���¤Ӥ��ߤˤ���
			$flag++;
		?>
<div class="carpet_frame">
<div class="carpet<?=$flag%2?>">
<a href="?char=<?=$this->Number?>"><?$this->ShowImage();?></a></div>
<?=$this->name?><?if($this->statuspoint)print('<span class="bold charge">*</span>');?><br>Lv.<?=$this->level?>&nbsp;<?=$this->job_name?>
</div><?
	}

//////////////////////////////////////////////////
//	checkbox��ɽ������
	function ShowCharRadio($birth,$checked=null) {
		static $flag = 0;

		$flag++;
		if( CHAR_ROW%2==0 && $flag%(CHAR_ROW+1)==0 )//carpet���¤Ӥ��ߤˤ���
			$flag++;

// onclick="Element.toggleClassName(this,'unselect')"

		?>
<div class="carpet_frame">
<div class="carpet<?=$flag%2?>">
<a href="?char=<?=$this->birth?>"><?$this->ShowImage();?></a>
</div>

<div onClick="toggleCheckBox('<?=$flag?>')" id="text<?=$flag?>" <?print($checked?null:' class="unselect"');?>>
<?=$this->name?>
<?if($this->statuspoint)print('<span class="bold charge">*</span>');?><br />
Lv.<?=$this->level?>&nbsp;<?=$this->job_name?>

</div>
<input type="checkbox" onclick="Element.toggleClassName('text<?=$flag?>','unselect')" id="box<?=$flag?>" name="char_<?=$birth?>" value="1"<?=$checked?>>

</div><?
	}
//////////////////////////////////////////////////
//	��Ʈ���Υ����������(����ޤ�ȤäƤʤ�)
	function SetTeam($no) {
		$this->team	= $no;
	}
//////////////////////////////////////////////////
//	IMG�����ǲ�����ɽ������Τ�
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
//	IMG�����ǲ�����ɽ������Τ�
	function ShowImage($class=false) {
		$url = $this->GetImageURL(IMG_CHAR);
		if($class)
			print('<img src="'.$url.'" class="'.$class.'">');
		else
			print('<img src="'.$url.'">');
	}
//////////////////////////////////////////////////
//	HP��SP��׻��������ꤹ��
	function SetHpSp()
	// $coe=array(HP,SP����);
	{
		$MaxStatus	= MAX_STATUS;//�ǹ⥹�ơ�����(����ʤ��Ƥ⤤���Ǥ�)

		$jobdata		= LoadJobData($this->job);// 2���ɤ߹���Ǥ뤫��ľ���٤�
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
//	handle�׻�
	function GetHandle() {
		$handle	= 5 + floor($this->level / 10) + floor($this->dex / 5);
		return $handle;
	}
//////////////////////////////////////////////////
//	�ݥ���Ȥ���񤷤Ƶ���Ф��롣
	function LearnNewSkill($no) {
		include_once(DATA_SKILL_TREE);
		$tree	= LoadSkillTree($this);

		//������ǽ���˳Ф��褦�Ȥ��Ƥ��Ĥʤ���㽪λ
		if(!in_array($_POST["newskill"],$tree))
			return array(false,"������ĥ꡼��̵��");
		$skill	= LoadSKillData($no);
		//�⤷�����Ѥߤʤ�?
		if(in_array($no,$this->skill))
			return array(false,"{$skill[name]} �Ͻ����Ѥ�.");
		if($this->UseSkillPoint($skill["learn"])) {
			$this->GetNewSkill($skill["no"]);
			//$this->SaveCharData();
			return array(true,$this->Name()." �� {$skill[name]} ����������");
		} else
			return array(false,"������ݥ������­");
	}
//////////////////////////////////////////////////
//	���略���ɲä��롣
	function GetNewSkill($no) {
		$this->skill[]	= $no;
		asort($this->skill);
	}
//////////////////////////////////////////////////
//	������ݥ���Ȥ���񤹤�
	function UseSKillPoint($no) {
		if($no <= $this->skillpoint) {
			$this->skillpoint	-= $no;
			return true;
		}
		return false;
	}
//////////////////////////////////////////////////
//	�и��ͤ�Ф�(��󥹥�������?)
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
//	�����Ф�(��󥹥�������?)
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
//	�����ƥ����Ȥ�(��󥹥�������?)
	function DropItem() {
		if($this->itemdrop) {
			$item	= $this->itemdrop;
			// ������Ȥ��������ƥ�Ͼä�
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
//	�ѥ�����ʸ���������ˤ��롣
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
//	�ѥ������������¸���롣
	function PatternSave($judge,$quantity,$action) {
		$this->Pattern	= implode("<>",$judge)."|".implode("<>",$quantity)."|".implode("<>",$action);
		return true;
	}
//////////////////////////////////////////////////
//	����饯������ä�
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
//	�������ѿ��򥻥åȤ��롣
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
			// HPSP�����ꡣHPSP���������������������������
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

		// ��󥹥����Τ��ᡩ
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

		//��󥹥�������
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