<?
include(CLASS_SKILL_EFFECT);
class battle extends ClassSkillEffect{
/*
 * $battle	= new battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//��Ʈ����
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */
	// teams
	var $team0, $team1;
	// team name
	var $team0_name, $team1_name;
	// team ave level
	var $team0_ave_lv, $team1_ave_lv;

	// ������
	var $team0_mc = 0;
	var $team1_mc = 0;

	// ��Ʈ�κ��祿�����(��Ĺ������ǽ���Τ���)
	var $BattleMaxTurn	= BATTLE_MAX_TURNS;
	var $NoExtends	= false;

	//
	var $NoResult	= false;

	// ��Ʈ�ط�
	var $BackGround = "grass";

	// �������� ( << >> �� ������ѿ�)
	var $Scroll = 0;

	// ����᡼��
	var $team0_dmg = 0;
	var $team1_dmg = 0;
	// ���ư���
	var $actions = 0;
	// ��Ʈ�ˤ�������ǥ��쥤
	var $delay;
	// ����������
	var $result;
	// ��館�뤪��
	var $team0_money, $team1_money;
	// ���äȤ��������ƥ�
	var $team0_item=array(), $team1_item=array();
	var $team0_exp=0, $team1_exp=0;// ��и��͡�

	// �ü���ѿ�
	var $ChangeDelay	= false;//������SPD���Ѳ������ݤ�DELAY��Ʒ׻����롣

	var $BattleResultType	= 0;// 0=�����夫�ʤ����Draw 1=��¸�Ԥο��Ǿ��Ԥ����
	var $UnionBattle;// �Ĥ�HP��HP�򱣤�(????/????)
//////////////////////////////////////////////////
//	���󥹥ȥ饯����

	//�ƥ���������������Ȥ롣
	function battle($team0,$team1) {
		include(DATA_JUDGE);
		include_once(DATA_SKILL);

		//��󥹥��������路�Ƥʤ��Ƥ⾤��������礬����Τ�
		include_once(CLASS_MONSTER);

		$this->team0	= $team0;
		$this->team1	= $team1;

		// �ƥ��������Ʈ���Ѥ��ѿ������ꤹ��(class.char.php)
		// �������ü쵡ǽ����׻��������ꤹ�롣
		// ��Ʈ���Ѥ��ѿ�����ʸ���Ѹ���ä��ꤹ�롣class.char.php�򻲾ȡ�
		//  $this->team["$key"] ���Ϥ�����.(�����ϥ������ֹ�)
		foreach($this->team0 as $key => $char)
			$this->team0["$key"]->SetBattleVariable(TEAM_0);
		foreach($this->team1 as $key => $char)
			$this->team1["$key"]->SetBattleVariable(TEAM_1);
		//dump($this->team0[0]);
		// delay��Ϣ
		$this->SetDelay();//�ǥ��쥤�׻�
		$this->DelayResetAll();//�����
	}
//////////////////////////////////////////////////
//	
	function SetResultType($var) {
		$this->BattleResultType	= $var;
	}
//////////////////////////////////////////////////
//	UnionBattle�Ǥ�����ˤ��롣
	function SetUnionBattle() {
		$this->UnionBattle	= true;
	}
//////////////////////////////////////////////////
//	�طʲ����򥻥åȤ��롣
	function SetBackGround($bg) {
		$this->BackGround	= $bg;
	}
//////////////////////////////////////////////////
//	��Ʈ�˥���饯���������滲�ä����롣
	function JoinCharacter($user,$add) {
		foreach($this->team0 as $char) {
			if($user === $char) {
				//array_unshift($this->team0,$add);
				$add->SetTeam(TEAM_0);
				array_push($this->team0,$add);
				//dump($this->team0);
				$this->ChangeDelay();
				return 0;
			}
		}
		foreach($this->team1 as $char) {
			if($user === $char) {
				//array_unshift($this->team1,$add);
				$add->SetTeam(TEAM_1);
				array_push($this->team1,$add);
				$this->ChangeDelay();
				return 0;
			}
		}
	}
//////////////////////////////////////////////////
//	�³��������������㤦��
	function LimitTurns($no) {
		$this->BattleMaxTurn	= $no;
		$this->NoExtends		= true;//����ʾ��Ĺ�Ϥ��ʤ���
	}
//////////////////////////////////////////////////
//	
	function NoResult() {
		$this->NoResult	= true;
	}
//////////////////////////////////////////////////
//	��Ʈ�κ��祿����������䤹��
	function ExtendTurns($no,$notice=false) {
		// ��Ĺ���ʤ��ѿ������ꤵ��Ƥ���б�Ĺ���ʤ���
		if($this->NoExtends === true) return false;

		$this->BattleMaxTurn	+= $no;
		if(BATTLE_MAX_EXTENDS < $this->BattleMaxTurn)
			$this->BattleMaxTurn	= BATTLE_MAX_EXTENDS;
		if($notice) {
print <<< HTML
	<tr><td colspan="2" class="break break-top bold" style="text-align:center;padding:20px 0;">
	battle turns extended.
	</td></tr>
HTML;
		}
		return true;
	}
//////////////////////////////////////////////////
//	��Ʈ��������������ƥ���֤���
	function ReturnItemGet($team) {
		if($team == TEAM_0) {
			if(count($this->team0_item) != 0)
				return $this->team0_item;
			else
				return false;
		} else if($team == TEAM_1) {
			if(count($this->team1_item) != 0)
				return $this->team1_item;
			else
				return false;
		}
	}
//////////////////////////////////////////////////
//	ĩ���¦��������������
	function ReturnBattleResult() {
		return $this->result;
	}
//////////////////////////////////////////////////
//	��Ʈ��Ͽ����¸����
	function RecordLog($type=false) {
		if($type == "RANK") {
			$file	= LOG_BATTLE_RANK;
			$log	= @glob(LOG_BATTLE_RANK."*");
			$logAmount = MAX_BATTLE_LOG_RANK;
		} else if($type == "UNION") {
			$file	= LOG_BATTLE_UNION;
			$log	= @glob(LOG_BATTLE_UNION."*");
			$logAmount = MAX_BATTLE_LOG_UNION;
		} else {
			$file	= LOG_BATTLE_NORMAL;
			$log	= @glob(LOG_BATTLE_NORMAL."*");
			$logAmount = MAX_BATTLE_LOG;
		}

		// �Ť�����ä�
		$i	= 0;
		while($logAmount <= count($log) ) {
			unlink($log["$i"]);
			unset($log["$i"]);
			$i++;
		}

		// ������������
		$time	= time().substr(microtime(),2,6);
		$file	.= $time.".dat";

		$head	= $time."\n";//���ϻ���(1����)
		$head	.= $this->team0_name."<>".$this->team1_name."\n";//���å�����(2����)
		$head	.= count($this->team0)."<>".count($this->team1)."\n";//���ÿͿ�(3����)
		$head	.= $this->team0_ave_lv."<>".$this->team1_ave_lv."\n";//ʿ�ѥ�٥�(4����)
		$head	.= $this->result."\n";//����������(5����)
		$head	.= $this->actions."\n";//�������(6����)
		$head	.= "\n";// ����(7����)

		WriteFile($file,$head.ob_get_contents());
	}
//////////////////////////////////////////////////
//	��Ʈ����(�����¹Ԥ�����Ʈ�����������)
	function Process() {
		$this->BattleHeader();

		//��Ʈ�������ޤǷ����֤�
		do {
			if($this->actions % BATTLE_STAT_TURNS == 0)//����ֳ֤Ǿ�����ɽ��
				$this->BattleState();//������ɽ��

			// ��ư�����
			if(DELAY_TYPE === 0)
				$char	= &$this->NextActer();
			else if(DELAY_TYPE === 1)
				$char	= &$this->NextActerNew();

			$this->Action($char);//��ư
			$result	= $this->BattleResult();//���ι�ư����Ʈ����λ�������ɤ�����Ƚ��

			//���λ�������SPD���Ѳ��������DELAY��Ʒ׻����롣
			if($this->ChangeDelay)
				$this->SetDelay();

		} while(!$result);

		$this->ShowResult($result);//��Ʈ�η��ɽ��
		$this->BattleFoot();

		//$this->SaveCharacters();
	}
//////////////////////////////////////////////////
//	��Ʈ��Υ���饯������������¸���롣
	function SaveCharacters() {
		//������0
		foreach($this->team0 as $char) {
			$char->SaveCharData();
		}
		//������1
		foreach($this->team1 as $char) {
			$char->SaveCharData();
		}
	}

//////////////////////////////////////////////////
//	��Ʈ��λ��Ƚ��
//	�������Ǥ�=draw(?)
	function BattleResult() {
		if(CountAlive($this->team0) == 0)//�������ܡ��ʤ��餱�ˤ��롣
			$team0Lose	= true;
		if(CountAlive($this->team1) == 0)//�������ܡ��ʤ��餱�ˤ��롣
			$team1Lose	= true;
		//���ԤΥ������ֹ椫����ʬ�����֤�
		if( $team0Lose && $team1Lose ) {
			$this->result	= DRAW;
			return "draw";
		} else if($team0Lose) {//team1 won
			$this->result	= TEAM_1;
			return "team1";
		} else if($team1Lose) {// team0 won
			$this->result	= TEAM_0;
			return "team0";

		// ξ��������¸���Ƥ��ƺ����ư����ã��������
		} else if($this->BattleMaxTurn <= $this->actions) {
			// ��¸�Կ��κ���
			/*
				// ��¸�Կ��κ���1�Ͱʾ�ʤ��Ĺ
			$AliveNumDiff	= abs(CountAlive($this->team0) - CountAlive($this->team1));
			if(0 < $AliveNumDiff && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS) {
			*/
			$AliveNumDiff	= abs(CountAlive($this->team0) - CountAlive($this->team1));
			$Not5	= (CountAlive($this->team0) != 5 && CountAlive($this->team1) != 5);
			//$lessThan4	= ( CountAlive($this->team0) < 5 || CountAlive($this->team1) < 5 );
			//if( ( $lessThan4 || 0 < $AliveNumDiff ) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS ) {
			if( ( $Not5 || 0 < $AliveNumDiff ) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS ) {
				if($this->ExtendTurns(TURN_EXTENDS,1))
					return false;
			}

			// �����夫�ʤ���Ф�������ʬ���ˤ��롣
			if($this->BattleResultType == 0) {
				$this->result	= DRAW;//����ʬ����
				return "draw";
			// �����夫�ʤ������¸�Ԥο��Ǿ��Ԥ�Ĥ��롣
			} else if($this->BattleResultType == 1) {
				// �Ȥꤢ��������ʬ��������
				// (1) ��¸�Կ���¿���ۤ�������
				// (2) (1) ��Ʊ���ʤ�����᡼����¿���ۤ�������
				// (3) (2) �Ǥ�Ʊ���ʤ����ʬ����???(or�ɱ�¦�ξ���)
	
				$team0Alive	= CountAliveChars($this->team0);
				$team1Alive	= CountAliveChars($this->team1);
				if($team1Alive < $team0Alive) {// team0 won
					$this->result	= TEAM_0;
					return "team0";
				} else if($team0Alive < $team1Alive) {// team1 won
					$this->result	= TEAM_1;
					return "team1";
				} else {
					$this->result	= DRAW;
					return "draw";
				}
			} else {
				$this->result	= DRAW;
				print("error321708.<br />���������Τ���𤷤Ƥ���������");
				return "draw";// ���顼����
			}

			$this->result	= DRAW;
			print("error321709.<br />���������Τ���𤷤Ƥ���������");
			return "draw";// ���顼����
		}
	}
//////////////////////////////////////////////////
//	��Ʈ�η��ɽ��
	function ShowResult($result) {

		// ��¦�Υ�����(��Ʈ�������¦)
		$TotalAlive2	= 0;
		// �Ĥ�HP / ���HP �� ɽ��
		foreach($this->team1 as $char) {//������1
			if($char->STATE !== DEAD)
				$TotalAlive2++;
			$TotalHp2	+= $char->HP;//���HP
			$TotalMaxHp2	+= $char->MAXHP;//��׺���HP
		}

		// ��¦�Υ�����(��Ʈ��ųݤ���¦)
		$TotalAlive1	= 0;
		foreach($this->team0 as $char) {//������0
			if($char->STATE !== DEAD)
				$TotalAlive1++;
			$TotalHp1	+= $char->HP;//���HP
			$TotalMaxHp1	+= $char->MAXHP;//��׺���HP
		}

		// ��̤�ɽ�����ʤ���
		if($this->NoResult) {
			print('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break break-top">');
			//print("<a name=\"s{$this->Scroll}\"></a>");// ��������κǸ�
			print("�ϵ��ｪλ");
			print("</td></tr>\n");
			print('<tr><td class="teams break">'."\n");
			// ��¦������
			print("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
			print("Alive : {$TotalAlive2}/".count($this->team1)."<br />\n");
			print("TotalDamage : {$this->team1_dmg}<br />\n");
			// ��¦������
			print('</td><td class="teams break">'."\n");
			print("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
			print("Alive : {$TotalAlive1}/".count($this->team0)."<br />\n");
			print("TotalDamage : {$this->team0_dmg}<br />\n");
			print("</td></tr>\n");
			return false;
		}

		//if($this->actions % BATTLE_STAT_TURNS != 0 || $result == "draw")
		//if(($this->actions + 1) % BATTLE_STAT_TURNS != 0)
		$BreakTop	= " break-top";
		print('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break'.$BreakTop.'">'."\n");
		//print($this->actions."%".BATTLE_STAT_TURNS."<br>");
		print("<a name=\"s{$this->Scroll}\"></a>\n");// ��������κǸ�
		if($result == "draw") {
			print("<span style=\"font-size:150%\">Draw Game</span><br />\n");
		} else {
			$Team	= &$this->{$result};
			$TeamName	= $this->{$result."_name"};
			print("<span style=\"font-size:200%\">{$TeamName} Wins!</span><br />\n");
		}

		print('<tr><td class="teams">'."\n");
		// Union�Ȥ����Ǥʤ��ΤǤ櫓��
		print("HP remain : ");
		print($this->UnionBattle?"????/????":"{$TotalHp2}/{$TotalMaxHp2}");
		print("<br />\n");
/*
		if($this->UnionBattle) {
			print("HP remain : ????/????<br />\n");
		} else {
			print("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
		}
*/
		// ��¦������
		print("Alive : {$TotalAlive2}/".count($this->team1)."<br />\n");
		print("TotalDamage : {$this->team1_dmg}<br />\n");
		if($this->team1_exp)//�����и���
			print("TotalExp : ".$this->team1_exp."<br />\n");
		if($this->team1_money)//��������
			print("Funds : ".MoneyFormat($this->team1_money)."<br />\n");
		if($this->team1_item) {//���������ƥ�
			print("<div class=\"bold\">Items</div>\n");
			foreach($this->team0_item as $itemno => $amount) {
				$item	= LoadItemData($itemno);
				print("<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\">");
				print("{$item[name]} x {$amount}<br />\n");
			}
		}

		// ��¦������
		print('</td><td class="teams">');
		print("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
		print("Alive : {$TotalAlive1}/".count($this->team0)."<br />\n");
		print("TotalDamage : {$this->team0_dmg}<br />\n");
		if($this->team0_exp)//�����и���
			print("TotalExp : ".$this->team0_exp."<br />\n");
		if($this->team0_money)//��������
			print("Funds : ".MoneyFormat($this->team0_money)."<br />\n");
		if($this->team0_item) {//���������ƥ�
			print("<div class=\"bold\">Items</div>\n");
			foreach($this->team0_item as $itemno => $amount) {
				$item	= LoadItemData($itemno);
				print("<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\">");
				print("{$item[name]} x {$amount}<br />\n");
			}
		}
		print("</td></tr>\n");
		//print("</td></tr>\n");//?
	}

//////////////////////////////////////////////////
//	�����ι�ư
	function Action(&$char) {
		// $char->judge �����ꤵ��Ƥʤ�������Ф�
		if($char->judge === array()) {
			$char->delay	= $char->SPD;
			return false;
		}

		// ������0�οͤϥ���α�¦��
		// ������1�οͤϺ�¦�� ��ư���Ƥȷ�� ��ɽ������
		print("<tr><td class=\"ttd2\">\n");
		if($char->team === TEAM_0)
			print("</td><td class=\"ttd1\">\n");
		// ��ʬ�Υ�����Ϥɤ��餫?
		foreach($this->team0 as $val) {
			if($val === $char) {
				$MyTeam	= &$this->team0;
				$EnemyTeam	= &$this->team1;
				break;
			}
		}
		//������0�Ǥʤ��ʤ������1
		if(!$MyTeam) {
			$MyTeam	= &$this->team1;
			$EnemyTeam	= &$this->team0;
		}

		//��ư��Ƚ��(���Ѥ��뵻��Ƚ��)
		if($char->expect) {// �Ӿ�,���� ��λ
			$skill	= $char->expect;
			$return	= &$char->target_expect;
		} else {//�Ե���Ƚ�ꢪ������
			$JudgeKey	= -1;

			// ��³������
			$char->AutoRegeneration();
			// �Ǿ��֤ʤ���᡼��������롣
			$char->PoisonDamage();

			//Ƚ��
			do {
				$Keys	= array();//������(�����)
				do {
					$JudgeKey++;
					$Keys[]	= $JudgeKey;
				// ��ʣȽ��ʤ鼡��ä���
				} while($char->action["$JudgeKey"] == 9000 && $char->judge["$JudgeKey"]);

				//$return	= MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam);
				$return	= MultiFactJudge($Keys,$char,$this);

				if($return) {
					$skill	= $char->action["$JudgeKey"];
					foreach($Keys as $no)
						$char->JdgCount[$no]++;//���ꤷ��Ƚ�ǤΥ�����Ȥ���
					break;
				}
			} while($char->judge["$JudgeKey"]);

			/* // (2007/10/15)
			foreach($char->judge as $key => $judge){
				// $return �� true,false,����Τ��Ť줫
				// ����ξ���Ƚ��ξ��˰��פ�������餬�֤�(�ϥ�)��
				$return	=& DecideJudge($judge,$char,$MyTeam,$EnemyTeam,$key);
				if($return) {
					$skill	= $char->action["$key"];
					$char->JdgCount[$key]++;//���ꤷ��Ƚ�ǤΥ�����Ȥ���
					break;
				}
			}
			*/
		}

		// ��Ʈ�����ư��������䤹��
		$this->actions++;

		if($skill) {
			$this->UseSkill($skill,$return,$char,$MyTeam,$EnemyTeam);
		// ��ư�Ǥ��ʤ��ä����ν���
		} else {
			print($char->Name(bold)." sunk in thought and couldn't act.<br />(No more patterns)<br />\n");
			$char->DelayReset();
		}

		//�ǥ��쥤�ꥻ�å�
		//if($ret	!== "DontResetDelay")
		//	$char->DelayReset;

		//echo $char->name." ".$skill."<br>";//��ǧ��
		//����ν����
		if($char->team === TEAM_1)
			print("</td><td class=\"ttd1\">&nbsp;\n");
		print("</td></tr>\n");
	}
//////////////////////////////////////////////////
//	����᡼����û�����
	function AddTotalDamage($team,$dmg) {
		if(!is_numeric($dmg)) return false;
		if($team == $this->team0)
			$this->team0_dmg	+= $dmg;
		else if($team == $this->team1)
			$this->team1_dmg	+= $dmg;
	}

//////////////////////////////////////////////////
//
	function UseSkill($skill_no,&$JudgedTarget,&$My,&$MyTeam,&$Enemy) {
		$skill	= LoadSkillData($skill_no);//���ǡ����ɤ�

		// ��勵�����԰���
		if($skill["limit"] && !$My->monster) {
			if(!$skill["limit"][$My->WEAPON]) {
				print('<span class="u">'.$My->Name(bold));
				print('<span class="dmg"> Failed </span>to ');
				print("<img src=\"".IMG_ICON.$skill["img"]."\" class=\"vcent\"/>");
				print($skill[name]."</span><br />\n");
				//print($My->Name(bold)." Failed to use ".$skill["name"]."<br />\n");
				print("(Weapon type doesnt match)<br />\n");
				$My->DelayReset();// ��ư���ꥻ�å�
				return true;
			}
		}

		// SP��­
		if($My->SP < $skill["sp"]) {
			print($My->Name(bold)." failed to ".$skill["name"]."(SP shortage)");
			if($My->expect) {//�⤷�Ӿ������������SP����­�������
				$My->ResetExpect();
			}
			$My->DelayReset();// ��ư���ꥻ�å�
			return true;
		}

		// �⤷ "�Ӿ�" �� "����" ��ɬ�פʵ��ʤ�(+�Ӿ����Ϥ��Ƥʤ����)���Ӿ�,���ᳫ��
		if($skill["charge"]["0"] && $My->expect === false) {
			// �����������ȱӾ��򳫻Ϥ����� /////////////////////
			// ʪ������ˡ�ˤ�ä�ʸ���Ѥ���
			if($skill["type"] == 0) {//ʪ��
				print('<span class="charge">'.$My->Name(bold).' start charging.</span>');
				$My->expect_type	= CHARGE;
			} else {//��ˡ
				print('<span class="charge">'.$My->Name(bold).' start casting.</span>');
				$My->expect_type	= CAST;
			}
			$My->expect	= $skill_no;//�Ӿ������ᴰλ��Ʊ���˻��Ѥ��뵻
			// ���ȤäƤʤ��Τǥ����Ȥˤ�����
			//$My->target_expect	= $JudgedTarget;//����������åȤ���¸
			//�Ӿ���������֤����ꡣ
			$My->DelayByRate($skill["charge"]["0"],$this->delay,1);
			print("<br />\n");

			// ��Ʈ�����ư����򸺤餹(����or�Ӿ� �Ϲ�ư������ʤ�)
			$this->actions--;

			return true;//�ǥ��쥤�ѹ���������ꥻ�åȤ��ʤ��褦�ˡ�
		} else {
			// ����ºݤ˻��Ѥ��� ///////////////////////////////////

			// ��ư�����ץ饹����
			$My->ActCount++;

			// ��ư���Ƥ�ɽ��(��ư����)
			print('<div class="u">'.$My->Name(bold));
			print("<img src=\"".IMG_ICON.$skill["img"]."\" class=\"vcent\"/>");
			print($skill[name]."</div>\n");

			// ��ˡ�ؤ����(̣��)
			if($skill["MagicCircleDeleteTeam"])
			{
				if($this->MagicCircleDelete($My->team,$skill["MagicCircleDeleteTeam"])) {
					print($My->Name(bold).'<span class="charge"> use MagicCircle x'.$skill["MagicCircleDeleteTeam"].'</span><br />'."\n");
				// ��ˡ�ؾ�����
				} else {
					print('<span class="dmg">failed!(MagicCircle isn\'t enough)</span><br />'."\n");
					$My->DelayReset();// ��ư���ꥻ�å�
					return true;
				}
			}

			// SP�ξ���(���ΰ��֤������ᡦ�Ӿ���λ��Ʊ���˾��񤹤�)
			$My->SpDamage($skill["sp"],false);

			// ���㡼��(�Ӿ�)��λ��Ʊ���˻��Ѥ��뵻�ξ����ä���
			if($My->expect)
				$My->ResetExpect();

			// HP�������ξ��(Sacrifice)
			if($skill["sacrifice"])
				$My->SacrificeHp($skill["sacrifice"]);

		}

		// �������åȤ�����(����)
		if($skill["target"]["0"] == "friend"):
			$candidate	= &$MyTeam;
		elseif($skill["target"]["0"] == "enemy"):
			$candidate	= &$Enemy;
		elseif($skill["target"]["0"] == "self"):
			$candidate[]	= &$My;
		elseif($skill["target"]["0"] == "all"):
			//$candidate	= $MyTeam + $Enemy;//???
			$candidate	= array_merge_recursive(&$MyTeam,&$Enemy);//���θ�,�¤Ӥ������ˤ�����������??
		endif;

		// ���䤫����Ѥ����оݤ����� �� (���������)

		// ñ�Τ˻���
		if($skill["target"]["1"] == "individual") {
			$target	=& $this->SelectTarget($candidate,$skill);//�оݤ�����
			if($defender =& $this->Defending($target,$candidate,$skill) )//�������륭���
				$target	= &$defender;
			for($i=0; $i<$skill["target"]["2"]; $i++) {//ñ�Τ�ʣ����¹�
				$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
				$this->AddTotalDamage($MyTeam,$dmg);
			}

		// ʣ���˻���
		} else if($skill["target"]["1"] == "multi") {
			for($i=0; $i<$skill["target"]["2"]; $i++) {
				$target	=& $this->SelectTarget($candidate,$skill);//�оݤ�����
				if($defender =& $this->Defending($target,$candidate,$skill) )//�������륭���
					$target	= &$defender;
				$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
				$this->AddTotalDamage($MyTeam,$dmg);
			}

		// ���Τ˻���
		} else if($skill["target"]["1"] == "all") {
			foreach($candidate as $key => $char) {
				$target	= &$candidate[$key];
				//if($char->STATE === DEAD) continue;//��˴�Ԥϥѥ���
				if($skill["priority"] != "Dead") {//���Ū�ˡ�
					if($char->STATE === DEAD) continue;//��˴�Ԥϥѥ���
				}
				// ���ι���ϼ�������ʤ�(�Ȥ���)
				for($i=0; $i<$skill["target"]["2"]; $i++) {
					$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
					$this->AddTotalDamage($MyTeam,$dmg);
				}
			}
		}

		// ���Ѹ���ѼԤ˱ƶ����������
		if($skill["umove"])
			$My->Move($skill["umove"]);

		// �����оݤˤʤä������ã���ɤ��ʤä����Τ����(�Ȥꤢ����HP=0�ˤʤä����ɤ���)��
		if($skill["sacrifice"]) { // Sacri�Ϥε���Ȥä���硣
			$Sacrier[]	= &$My;
			$this->JudgeTargetsDead($Sacrier);
		}
		list($exp,$money,$itemdrop)	= $this->JudgeTargetsDead($candidate);//������������и��ͤ�����

		$this->GetExp($exp,$MyTeam);
		$this->GetItem($itemdrop,$MyTeam);
		$this->GetMoney($money,$MyTeam);

		// ���λ�������SPD���Ѳ��������DELAY��Ʒ׻����롣
		if($this->ChangeDelay)
			$this->SetDelay();

		// ��ư��ι�ľ(����������ꤹ��)
		if($skill["charge"]["1"]) {
			$My->DelayReset();
			print($My->Name(bold)." Delayed");
			$My->DelayByRate($skill["charge"]["1"],$this->delay,1);
			print("<br />\n");
			return false;
		}

		// �Ǹ�˹�ư���ꥻ�åȤ��롣
		$My->DelayReset();
	}
//////////////////////////////////////////////////
//	�и��ͤ�����
function GetExp($exp,&$team) {
	if(!$exp) return false;

	$exp	= round(EXP_RATE * $exp);

	if($team === $this->team0){
		$this->team0_exp	+= $exp;
	} else {
		$this->team1_exp	+= $exp;
	}

	$Alive	= CountAliveChars($team);
	if($Alive === 0) return false;
	$ExpGet	= ceil($exp/$Alive);//��¸�Ԥˤ����и��ͤ�ʬ���롣
	print("Alives get {$ExpGet}exps.<br />\n");
	foreach($team as $key => $char) {
		if($char->STATE === 1) continue;//��˴�Ԥˤ�EXP�����ʤ�
		if($team[$key]->GetExp($ExpGet))//LvUp�����ʤ�true���֤�
			print("<span class=\"levelup\">".$char->Name()." LevelUp!</span><br />\n");
	}
}
//////////////////////////////////////////////////
//	�����ƥ���������(�����ब)
	function GetItem($itemdrop,$MyTeam) {
		if(!$itemdrop) return false;
		if($MyTeam === $this->team0) {
			foreach($itemdrop as $itemno => $amount) {
				$this->team0_item["$itemno"]	+= $amount;
			}
		} else {
			foreach($itemdrop as $itemno => $amount) {
				$this->team1_item["$itemno"]	+= $amount;
			}
		}
	}

//////////////////////////////////////////////////
//	��Ҥ�������륭�������֡�
	function &Defending(&$target,&$candidate,$skill) {
		if($target === false) return false;

		if($skill["invalid"])//�ɸ�̵��Ǥ��뵻��
			return false;
		if($skill["support"])//�ٱ�ʤΤǥ����ɤ��ʤ���
			return false;
		if($target->POSITION == "front")//���Ҥʤ���ɬ��̵���������
			return false;
		// "���ҤǾ������¸��"������˵ͤ�뢭
		// ���� + ��¸�� + HP1�ʾ� ���ѹ� ( ¿�ʷϹ���ǻ�ˤʤ�����Τ� [2007/9/20] )
		foreach($candidate as $key => $char) {
			//print("{$char->POSTION}:{$char->STATE}<br>");
			if($char->POSITION == "front" && $char->STATE !== 1 && 1 < $char->HP )
				$fore[]	= &$candidate["$key"];
		}
		if(count($fore) == 0)//���Ҥ����ʤ������ʤ��������
			return false;
		// ��ͤŤļ������뤫����ʤ�����Ƚ�ꤹ�롣
		shuffle($fore);//������¤Ӥ򺮤���
		foreach($fore as $key => $char) {
			// Ƚ��˻Ȥ��ѿ���׻������ꤹ�롣
			switch($char->guard) {
				case "life25":
				case "life50":
				case "life75":
					$HpRate	= ($char->HP / $char->MAXHP) * 100;
				case "prob25":
				case "prob50":
				case "prob75":
					mt_srand();
					$prob	= mt_rand(1,100);
			}
			// �ºݤ�Ƚ�ꤷ�Ƥߤ롣
			switch($char->guard) {
				case "never":
					continue;
				case "life25":// HP(%)��25%�ʾ�ʤ�
					if(25 < $HpRate) $defender	= &$fore["$key"]; break;
				case "life50":// ��50%��
					if(50 < $HpRate) $defender	= &$fore["$key"]; break;
				case "life75":// ��70%��
					if(75 < $HpRate) $defender	= &$fore["$key"]; break;
				case "prob25":// 25%�γ�Ψ��
					if($prob < 25) $defender	= &$fore["$key"]; break;
				case "prob50":// 50% ��
					if($prob < 50) $defender	= &$fore["$key"]; break;
				case "prob75":// 75% ��
					if($prob < 75) $defender	= &$fore["$key"]; break;
				default:
					$defender	= &$fore["$key"];
			}
			// ï������Ҥ�������ä��ΤǤ����ɽ������
			if($defender) {
				print('<span class="bold">'.$defender->name.'</span> protected <span class="bold">'.$target->name.'</span>!<br />'."\n");
				return $defender;
			}
		}
	}
//////////////////////////////////////////////////
//	��������Ѹ���оݼ�(����)�����ܡ��������ɤ�����Τ����
	function JudgeTargetsDead(&$target) {
		foreach($target as $key => $char) {
			// Ϳ�������᡼���κ�ʬ�Ƿи��ͤ���������󥹥����ξ�硣
			if(method_exists($target[$key],'HpDifferenceEXP')) {
				$exp	+= $target[$key]->HpDifferenceEXP();
			}
			if($target[$key]->CharJudgeDead()) {//�������ɤ���
				// ��˴��å�����
				print("<span class=\"dmg\">".$target[$key]->Name(bold)." down.</span><br />\n");

				//�и��ͤμ���
				$exp	+= $target[$key]->DropExp();

				//����μ���
				$money	+= $target[$key]->DropMoney();

				// �����ƥ�ɥ�å�
				if($item = $target[$key]->DropItem()) {
					$itemdrop["$item"]++;
					$item	= LoadItemData($item);
					print($char->Name("bold")." dropped");
					print("<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\"/>\n");
					print("<span class=\"bold u\">{$item[name]}</span>.<br />\n");
				}

				//���������ʤ�ä���
				if($target[$key]->summon === true) {
					unset($target[$key]);
				}

				// �����Τǥǥ��쥤��ľ����
				$this->ChangeDelay();
			}
		}
		return array($exp,$money,$itemdrop);//��������и��ͤ��֤�
	}
//////////////////////////////////////////////////
//	ͥ���̤˽��äƸ��䤫�����֤�
	function &SelectTarget(&$target_list,$skill) {

		/*
		* ͥ��Ϥ��뤬�����ƤϤޤ�ʤ��Ƥ�ǽ�Ū�˥������åȤ��פ롣
		* �� : ��Ҥ���ʤ������Ҥ��оݤˤ��롣
		*    : ������HP100%��ï�� �Ƥ��Ȥ� ���оݤˤ��롣
		*/

		//�Ĥ�HP(%)�����ʤ��ͤ򥿡����åȤˤ���
		if($skill["priority"] == "LowHpRate") {
			$hp = 2;//���1����礭�������ˡ�����
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD) continue;//���ܡ��Ԥ��оݤˤʤ�ʤ���
				$HpRate	= $char->HP / $char->MAXHP;//HP(%)
				if($HpRate < $hp) {
					$hp	= $HpRate;//�����κǤ�HP(%)���㤤��
					$target	= &$target_list[$key];
				}
			}
			return $target;//�Ǥ�HP���㤤��

		//��Ҥ�ͥ�褹��
		} else if($skill["priority"] == "Back") {
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD) continue;//���ܡ��Ԥ��оݤˤʤ�ʤ���
				if($char->POSITION != FRONT)//��Ҥʤ�
				$target[]	= &$target_list[$key];//����ˤ����
			}
			if($target)
				return $target[array_rand($target)];//�ꥹ�Ȥ��椫��������

		/*
		* ͥ��Ϥ��뤬��
		* ͥ�褹���оݤ����ʤ���л��Ѥϼ��Ԥ���(�ʹ���)
		*/

		//���ܡ��Ԥ��椫���������֤���
		} else if($skill["priority"] == "Dead") {
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD)//���ܡ��ʤ�
				$target[]	= &$target_list[$key];//���ܡ��ԥꥹ��
			}
			if($target)
				return $target[array_rand($target)];//���ܡ��ԥꥹ�Ȥ��椫��������
			else
				return false;//ï�⤤�ʤ����false�֤������ʤ�...(����������Ѽ���)

		// ����������ͥ�褹�롣
		} else if($skill["priority"] == "Summon") {
			foreach($target_list as $key => $char) {
				if($char->summon)//���������ʤ�
					$target[]	= &$target_list[$key];//���������ꥹ��
			}
			if($target)
				return $target[array_rand($target)];//�����������椫��������
			else
				return false;//ï�⤤�ʤ����false�֤������ʤ�...(����������Ѽ���)

		// ���㡼����Υ����
		} else if($skill["priority"] == "Charge") {
			foreach($target_list as $key => $char) {
				if($char->expect)
					$target[]	= &$target_list[$key];
			}
			if($target)
				return $target[array_rand($target)];
			else
				return false;//ï�⤤�ʤ����false�֤������ʤ�...(����������Ѽ���)
		//
		}

		//����ʳ�(������)
		foreach($target_list as $key => $char) {
			if($char->STATE != DEAD)//���ܡ��ʳ��ʤ�
				$target[]	= &$target_list[$key];//���ܡ��ԥꥹ��
		}
		return $target[array_rand($target)];//�������ï�����
	}
//////////////////////////////////////////////////
//	���ι�ư��ï��(�����Ӿ������ˡ��ȯư����Τ�ï��)
//	��ե���󥹤��֤�
	function &NextActer() {
		// �Ǥ�ǥ��쥤���礭���ͤ�õ��
		foreach($this->team0 as $key => $char) {
			if($char->STATE === 1) continue;
			// �ǽ��ï�Ǥ⤤���ΤǤȤꤢ�����ǽ�οͤȤ��롣
			if(!isset($delay)) {
				$delay	= $char->delay;
				$NextChar	= &$this->team0["$key"];
				continue;
			}
			// ����餬���Υǥ��쥤���¿����и���
			if($delay <= $char->delay) {//��ư
				// �⤷�����ȥǥ��쥤��Ʊ���ʤ�50%�Ǹ���
				if($delay == $char->delay) {
					if(mt_rand(0,1))
						continue;
				}
				$delay	= $char->delay;
				$NextChar	= &$this->team0["$key"];
			}
		}
		// ����Ʊ����
		foreach($this->team1 as $key => $char) {
			if($char->STATE === 1) continue;
			if($delay <= $char->delay) {//��ư
				if($delay == $char->delay) {
					if(mt_rand(0,1))
						continue;
				}
				$delay	= $char->delay;
				$NextChar	= &$this->team1["$key"];
			}
		}
		// �����ǥ��쥤����
		$dif	= $this->delay - $NextChar->delay;//��Ʈ���ܥǥ��쥤�ȹ�ư�ԤΥǥ��쥤�κ�ʬ
		if($dif < 0)//�⤷�⺹ʬ��0�ʲ��ˤʤä����
			return $NextChar;
		foreach($this->team0 as $key => $char) {
			$this->team0["$key"]->Delay($dif);
		}
		foreach($this->team1 as $key => $char) {
			$this->team1["$key"]->Delay($dif);
		}
		/*// ���顼���Ф��餳��ǡ�
		if(!is_object($NextChar)) {
			print("AAA");
			dump($NextChar);
			print("BBB");
		}
		*/

		return $NextChar;
	}
//////////////////////////////////////////////////
//	���ι�ư��ï��(�����Ӿ������ˡ��ȯư����Τ�ï��)
//	��ե���󥹤��֤�
	function &NextActerNew() {

		// ���ι�ư�ޤǺǤ��Υ��û���ͤ�õ����
		$nextDis	= 1000;
		foreach($this->team0 as $key => $char) {
			if($char->STATE === DEAD) continue;
			$charDis	= $this->team0[$key]->nextDis();
			if($charDis == $nextDis) {
				$NextChar[]	= &$this->team0["$key"];
			} else if($charDis <= $nextDis) {
				$nextDis	= $charDis;
				$NextChar	= array(&$this->team0["$key"]);
			}
		}

		// ����Ʊ����
		foreach($this->team1 as $key => $char) {
			if($char->STATE === DEAD) continue;
			$charDis	= $this->team1[$key]->nextDis();
			if($charDis == $nextDis) {
				$NextChar[]	= &$this->team1["$key"];
			} else if($charDis <= $nextDis) {
				$nextDis	= $charDis;
				$NextChar	= array(&$this->team1["$key"]);
			}
		}

		// �����ǥ��쥤���� //////////////////////

		//�⤷�⺹ʬ��0�ʲ��ˤʤä���
		if($nextDis < 0) {
			if(is_array($NextChar)) {
				return $NextChar[array_rand($NextChar)];
			} else
				return $NextChar;
		}

		foreach($this->team0 as $key => $char) {
			$this->team0["$key"]->Delay($nextDis);
		}
		foreach($this->team1 as $key => $char) {
			$this->team1["$key"]->Delay($nextDis);
		}
		// ���顼���Ф��餳��Ǥ��������
		/*
		if(!is_object($NextChar)) {
			print("AAA");
			dump($NextChar);
			print("BBB");
		}
		*/

		if(is_array($NextChar))
			return $NextChar[array_rand($NextChar)];
		else
			return $NextChar;
	}
//////////////////////////////////////////////////
//	����������ι�ư�ǥ��쥤������(=SPD)
	function DelayResetAll() {
		if(DELAY_TYPE === 0 || DELAY_TYPE === 1)
		{
			foreach($this->team0 as $key => $char) {
				$this->team0["$key"]->DelayReset();
			}
			foreach($this->team1 as $key => $char) {
				$this->team1["$key"]->DelayReset();
			}
		}
	}
//////////////////////////////////////////////////
//	�ǥ��쥤��׻��������ꤹ��
//	ï����SPD���Ѳ��������Ƥ�ľ��
//	*** ���λ�������SPD���Ѳ������ݤ˸ƤӽФ� ***
	function SetDelay() {
		if(DELAY_TYPE === 0)
		{
			//SPD�κ����ͤȹ�פ����
			foreach($this->team0 as $key => $char) {
				$TotalSPD	+= $char->SPD;
				if($MaxSPD < $char->SPD)
					$MaxSPD	= $char->SPD;
			}
			//dump($this->team0);
			foreach($this->team1 as $char) {
				$TotalSPD	+= $char->SPD;
				if($MaxSPD < $char->SPD)
					$MaxSPD	= $char->SPD;
			}
			//ʿ��SPD
			$AverageSPD	= $TotalSPD/( count($this->team0) + count($this->team1) );
			//���delay�Ȥ�
			$AveDELAY	= $AverageSPD * DELAY;
			$this->delay	= $MaxSPD + $AveDELAY;//������Ʈ�δ��ǥ��쥤
			$this->ChangeDelay	= false;//false�ˤ��ʤ������DELAY��׻���ľ���Ƥ��ޤ���
		}
			else if(DELAY_TYPE === 1)
		{
		}
	}
//////////////////////////////////////////////////
//	��Ʈ�δ��ǥ��쥤��Ʒ׻�������褦�ˤ��롣
//	�Ȥ����ϡ����λ��Ѥǥ�����SPD���Ѳ������ݤ˻Ȥ���
//	class.skill_effect.php �ǻ��ѡ�
	function ChangeDelay(){
		if(DELAY_TYPE === 0)
		{
			$this->ChangeDelay	= true;
		}
	}
//////////////////////////////////////////////////
//	�������̾��������
	function SetTeamName($name1,$name2) {
		$this->team0_name	= $name1;
		$this->team1_name	= $name2;
	}
//////////////////////////////////////////////////
//	��Ʈ���Ϥ�������ʿ�ѥ�٥����HP����׻���ɽ��
//	��Ʈ�ηаޤϰ�Ĥ�ɽ�ǹ�������뤦�ä�
	function BattleHeader() {
		foreach($this->team0 as $char) {//������0
			$team0_total_lv	+= $char->level;//���LV
			$team0_total_hp	+= $char->HP;//���HP
			$team0_total_maxhp	+= $char->MAXHP;//��׺���HP
		}
		$team0_avelv	= round($team0_total_lv/count($this->team0)*10)/10;//������0ʿ��LV
		$this->team0_ave_lv	= $team0_avelv;
		foreach($this->team1 as $char) {//������1
			$team1_total_lv	+= $char->level;
			$team1_total_hp	+= $char->HP;
			$team1_total_maxhp	+= $char->MAXHP;
		}
		$team1_avelv	= round($team1_total_lv/count($this->team1)*10)/10;
		$this->team1_ave_lv	= $team1_avelv;
		if($this->UnionBattle) {
			$team1_total_hp		= '????';
			$team1_total_maxhp	= '????';
		}
		?>
<table style="width:100%;" cellspacing="0"><tbody>
<tr><td class="teams"><div class="bold"><?=$this->team1_name?></div>
Total Lv : <?=$team1_total_lv?><br>
Average Lv : <?=$team1_avelv?><br>
Total HP : <?=$team1_total_hp?>/<?=$team1_total_maxhp?>
</td><td class="teams ttd1"><div class="bold"><?=$this->team0_name?></div>
Total Lv : <?=$team0_total_lv?><br>
Average Lv : <?=$team0_avelv?><br>
Total HP : <?=$team0_total_hp?>/<?=$team0_total_maxhp?>
</td></tr><?
	}
//////////////////////////////////////////////////
//	��Ʈ��λ����ɽ��
	function BattleFoot() {
	/*	print("<tr><td>");
		dump($this->team0);
		print("</td></tr>");*/
		?>
</tbody></table>
<?
	}
//////////////////////////////////////////////////
//	��Ʈ�������ƥ����λĤ�HP�Ĥ�SP����ɽ��
	function BattleState() {
		static $last;
		if($last !== $this->actions)
			$last	= $this->actions;
		else
			return false;

		print("<tr><td colspan=\"2\" class=\"btl_img\">\n");
		// ��Ʈ���ƥå׽�˼�ư��������
		print("<a name=\"s".$this->Scroll."\"></a>\n");
		print("<div style=\"width:100%;hight:100%;position:relative;\">\n");
		print('<div style="position:absolute;bottom:0px;right:0px;">'."\n");
		if($this->Scroll)
			print("<a href=\"#s".($this->Scroll - 1)."\">&lt;&lt;</a>\n");
		else
			print("&lt;&lt;" );
		print("<a href=\"#s".(++$this->Scroll)."\">&gt;&gt;</a>\n");
		print('</div>');

		switch(BTL_IMG_TYPE) {
			case 0:
				print('<div style="text-align:center">');
				$this->ShowGdImage();//����
				print('</div>');
				break;
			case 1:
			case 2:
				$this->ShowCssImage();//����
				break;
		}
		print("</div>");
		print("</td></tr><tr><td class=\"ttd2 break\">\n");

		print("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");// team1-backs

		// 	��¦��������
		foreach($this->team1 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;

			if($char->POSITION != FRONT)
				$char->ShowHpSp();
		}

		// 	��¦����������
		print("</td><td style=\"width:50%\">\n");
		foreach($this->team1 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;

			if($char->POSITION == FRONT)
				$char->ShowHpSp();
		}

		print("</td></tr></tbody></table>\n");

		print("</td><td class=\"ttd1 break\">\n");

		// 	��¦����������
		print("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");
		foreach($this->team0 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == FRONT)
				$char->ShowHpSp();
		}

		// 	��¦��������
		print("</td><td style=\"width:50%\">\n");
		foreach($this->team0 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION != FRONT)
				$char->ShowHpSp();
		}
		print("</td></tr></tbody></table>\n");

		print("</td></tr>\n");
	}
//////////////////////////////////////////////////
//	��Ʈ����(�����Τ�)
	function ShowGdImage() {
		$url	= BTL_IMG."?";

		// HP=0 �Υ����β���(��ĥ�Ҥ�����Ф������)
		$DeadImg	= substr(DEAD_IMG,0,strpos(DEAD_IMG,"."));

		//������1
		$f	= 1;
		$b	= 1;//���Ҥο�����Ҥο�������
		foreach($this->team0 as $char) {
			//�����ϥ��������ꤵ��Ƥ�������γ�ĥ�ҤޤǤ�̾��
			if($char->STATE === 1)
				$img	= $DeadImg;
			else
				$img	= substr($char->img,0,strpos($char->img,"."));
			if($char->POSITION == "front")://����
				$url	.= "f2{$f}=$img&";
				$f++;
			else:
				$url	.= "b2{$b}=$img&";//���
				$b++;
			endif;
		}
		//������0
		$f	= 1;
		$b	= 1;
		foreach($this->team1 as $char) {
			if($char->STATE === 1)
				$img	= $DeadImg;
			else
				$img	= substr($char->img,0,strpos($char->img,"."));
			if($char->POSITION == "front"):
				$url	.= "f1{$f}=$img&";
				$f++;
			else:
				$url	.= "b1{$b}=$img&";
				$b++;
			endif;
		}
		print('<img src="'.$url.'">');// �����줬ɽ�������Τ�
	}
//////////////////////////////////////////////////
//	CSS��Ʈ����
	function ShowCssImage() {
		include_once(BTL_IMG_CSS);
		$img	= new cssimage();
		$img->SetBackGround($this->BackGround);
		$img->SetTeams($this->team1,$this->team0);
		$img->SetMagicCircle($this->team1_mc, $this->team0_mc);
		if(BTL_IMG_TYPE == 2)
			$img->NoFlip();// CSS����ȿž̵��
		$img->Show();
	}
//////////////////////////////////////////////////
//	��������롢���Ū���ѿ�����¸���������
//	class��˥᥽�åɺ�졼
	function GetMoney($money,$team) {
		if(!$money) return false;
		$money	= ceil($money * MONEY_RATE);
		if($team === $this->team0) {
			print("{$this->team0_name} Get ".MoneyFormat($money).".<br />\n");
			$this->team0_money	+= $money;
		} else if($team === $this->team1) {
			print("{$this->team1_name} Get ".MoneyFormat($money).".<br />\n");
			$this->team1_money	+= $money;
		}
	}
//////////////////////////////////////////////////
//	�桼�����ǡ����������׶�ۤ��Ϥ�
	function ReturnMoney() {
		return array($this->team0_money,$this->team1_money);
	}

//////////////////////////////////////////////////
//	���Τλ�Կ��������...(�ͥ���ޥ󥵤����ȤäƤʤ�?)
	function CountDeadAll() {
		$dead	= 0;
		foreach($this->team0 as $char) {
			if($char->STATE === DEAD)
				$dead++;
		}
		foreach($this->team1 as $char) {
			if($char->STATE === DEAD)
				$dead++;
		}
		return $dead;
	}

//////////////////////////////////////////////////
//	���ꥭ���Υ�����λ�Կ��������(����Υ�����)�ͥ���ޥ󥵤����ȤäƤʤ�?
	function CountDead($VarChar) {
		$dead	= 0;

		if($VarChar->team == TEAM_0) {
		//	print("A".$VarChar->team."<br>");
			$Team	= $this->team0;
		} else {
			//print("B".$VarChar->team);
			$Team	= $this->team1;
		}

		foreach($Team as $char) {
			if($char->STATE === DEAD) {
				$dead++;
			} else if($char->SPECIAL["Undead"] == true) {
				//print("C".$VarChar->Name()."/".count($Team)."<br>");
				$dead++;
			}
		}
		return $dead;
	}
//////////////////////////////////////////////////
//	�����ؤ��ɲä���
	function MagicCircleAdd($team,$amount) {
		if($team == TEAM_0) {
			$this->team0_mc	+= $amount;
			if(5 < $this->team0_mc)
				$this->team0_mc	= 5;
			return true;
		} else {
			$this->team1_mc	+= $amount;
			if(5 < $this->team1_mc)
				$this->team1_mc	= 5;
			return true;
		}
	}
//////////////////////////////////////////////////
//	�����ؤ�������
	function MagicCircleDelete($team,$amount) {
		if($team == TEAM_0) {
			if($this->team0_mc < $amount)
				return false;
			$this->team0_mc	-= $amount;
			return true;
		} else {
			if($this->team1_mc < $amount)
				return false;
			$this->team1_mc	-= $amount;
			return true;
		}
	}
// end of class. /////////////////////////////////////////////////////
}

//////////////////////////////////////////////////
//	��¸�Կ���������֤�
function CountAlive($team) {
	$no	= 0;//�����
	foreach($team as $char) {
		if($char->STATE !== 1)
			$no++;
	}
	return $no;
}

//////////////////////////////////////////////////
//	����������¸����������֤�
function CountAliveChars($team) {
	$no	= 0;//�����
	foreach($team as $char) {
		if($char->STATE === 1)
			continue;
		if($char->monster)
			continue;
		$no++;
	}
	return $no;
}
//////////////////////////////////////////////////
//	���Էϥ�����ǸƤФ줿��󥹥�����
	function CreateSummon($no,$strength=false) {
		include_once(DATA_MONSTER);
		$monster	= CreateMonster($no,1);

		$monster["summon"]	= true;
		// ������󥹥����ζ�����
		if($strength) {
			$monster["maxhp"]	= round($monster["maxhp"]*$strength);
			$monster["hp"]	= round($monster["hp"]*$strength);
			$monster["maxsp"]	= round($monster["maxsp"]*$strength);
			$monster["sp"]	= round($monster["sp"]*$strength);
			$monster["str"]	= round($monster["str"]*$strength);
			$monster["int"]	= round($monster["int"]*$strength);
			$monster["dex"]	= round($monster["dex"]*$strength);
			$monster["spd"]	= round($monster["spd"]*$strength);
			$monster["luk"]	= round($monster["luk"]*$strength);

			$monster["atk"]["0"]	= round($monster["atk"]["0"]*$strength);
			$monster["atk"]["1"]	= round($monster["atk"]["1"]*$strength);
		}

		$monster	= new monster($monster);
		$monster->SetBattleVariable();
		return $monster;
	}
//////////////////////////////////////////////////
//	ʣ����Ƚ�����ǤǤ�Ƚ��
//function MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam) {
function MultiFactJudge($Keys,$char,$classBattle) {
	foreach($Keys as $no) {

		//$return	= DecideJudge($no,$char,$MyTeam,$EnemyTeam);
		$return	= DecideJudge($no,$char,$classBattle);

		// Ƚ�꤬�ݤǤ��ä���罪λ��
		if(!$return)
			return false;

		// �������Ӥ��ƶ��̹��ܤ�Ĥ�(�ۤ��ѻߤ�������)
		/*
		if(!$compare && is_array($return))
			$compare	= $return;
		else if(is_array($return))
			$compare	= array_intersect($intersect,$return);
		*/

	}

	/*
	if($compare == array())
		$compare	= true;
	return $compare;
	*/
	return true;
}
?>