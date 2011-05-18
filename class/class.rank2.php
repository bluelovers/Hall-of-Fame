<?php

class Ranking {

	var $fp;

	var $Ranking	= array();
	var $UserName;
	var $UserRecord;

//////////////////////////////////////////////
// �ե����뤫���ɤ߹���ǥ�󥭥󥰤�����ˤ���
/*

	$this->Ranking[0][0]= *********;// ���

	$this->Ranking[1][0]= *********;// Ʊ�� 2��
	$this->Ranking[1][1]= *********;

	$this->Ranking[2][0]= *********;// Ʊ�� 3��
	$this->Ranking[2][1]= *********;
	$this->Ranking[2][2]= *********;

	$this->Ranking[3][0]= *********;// Ʊ�� 4��
	$this->Ranking[3][1]= *********;
	$this->Ranking[3][2]= *********;
	$this->Ranking[3][3]= *********;

	...........

*/
	function Ranking() {
		$file	= RANKING;

		if(!file_exists($file)) return 0;

		// �ե����뤫���ɤ������ˤ����

		$this->fp	= FileLock($file);
		$Place	= 0;
		while($line = fgets($this->fp) ) {
			$line	= trim($line);
			if($line == "") continue;
			if(count($this->Ranking[$Place]) === $this->SamePlaceAmount($Place))
				$Place++;
			$this->Ranking[$Place][]	= $line;
		}
		//$this->Ranking	= file($file);
		// ����0�ʤ齪λ
		if(!$this->Ranking) return 0;
		// ���ڤä�ʸ�����ʬ��
		foreach($this->Ranking as $Rank => $SamePlaces) {
			if(!is_array($SamePlaces))
				continue;
			foreach($SamePlaces as $key => $val) {
				$list	= explode("<>", $val);
				$this->Ranking["$Rank"]["$key"]	= array();
				$this->Ranking["$Rank"]["$key"]["id"]	= $list["0"];
			}
		}
		//$this->JoinRanking("yqyqqq","last");
		//dump($this->Ranking);
	}
//////////////////////////////////////////////
// ��󥭥��魯�롣�臘��
	function Challenge(&$user) {
		// ��󥭥󥰤�̵���Ȥ�(1�̤ˤʤ�)
		if(!$this->Ranking) {
			$this->JoinRanking($user->id);
			$this->SaveRanking();
			print("Rank starts.");
			//return array($message,true);
			return false;
		}
		//��ʬ�ν��
		$MyRank	= $this->SearchID($user->id);

		// 1�̤ξ�硣
		if($MyRank["0"] === 0) {
			SHowError("First place can't challenge.");
			//return array($message,true);
			return false;
		}

		// ��ʬ����󥯳��ʤ� ////////////////////////////////////
		if(!$MyRank)
		{
			$this->JoinRanking($user->id);//��ʬ��ǲ��̤ˤ��롣
			$MyPlace	= count($this->Ranking) - 1;//��ʬ�Υ��(�ǲ���)
			$RivalPlace	= (int)($MyPlace - 1);

			// ��꤬��̤ʤΤ��ɤ���
			if($RivalPlace === 0)
				$DefendMatch	= true;
			else
				$DefendMatch	= false;

			//$MyID	= $id;

			//��ʬ���1�ľ�οͤ���ꡣ
			$RivalRankKey	= array_rand($this->Ranking[$RivalPlace]);
			$RivalID	= $this->Ranking[$RivalPlace][$RivalRankKey]["id"];//���魯������ID
			$Rival	= new user($RivalID);

			/*
			dump($this->Ranking);
			dump($RivalID);
			dump($MyID);
			dump($MyRank);//���顼�Ǥ����ĥ��
			return 0;
			*/

			$Result	= $this->RankBattle($user,$Rival,$MyPlace,$RivalPlace);
			$Return	= $this->ProcessByResult($Result,&$user,&$Rival,$DefendMatch);
			
			return $Return;
			// �����ʤ��̸���
			//if($message == "Battle" && $result === 0) {
			//	$this->ChangePlace($user,$Rival);
			//}

			//$this->SaveRanking();
			//return array($message,$result);
		}

		// 2��-�ǲ��̤οͤν�����////////////////////////////////
		if($MyRank) {
			$RivalPlace	= (int)($MyRank["0"] - 1);//��ʬ����̤�1�ľ�ο͡�

			// ��꤬��̤ʤΤ��ɤ���
			if($RivalPlace === 0)
				$DefendMatch	= true;
			else
				$DefendMatch	= false;

			//��ʬ���1�ľ�οͤ����
			$RivalRankKey	= array_rand($this->Ranking[$RivalPlace]);
			$RivalID	= $this->Ranking[$RivalPlace][$RivalRankKey]["id"];
			$Rival	= new user($RivalID);
			//$MyID		= $this->Ranking[$MyRank["0"]][$MyRank["1"]]["id"];
			//$MyID		= $id;
			//list($message,$result)	= $this->RankBattle($MyID,$RivalID);
			$Result	= $this->RankBattle($user,$Rival,$MyRank["0"],$RivalPlace);
			$Return	= $this->ProcessByResult($Result,&$user,&$Rival,$DefendMatch);
			
			return $Return;
			//if($message != "Battle")
			//	return array($message,$result);

			// ��Ʈ��Ԥäƾ����ʤ��̸���
			/*
			if($message == "Battle" && $result === 0) {
				$this->ChangePlace($MyID,$RivalID);
				//dump($this->Ranking);
				$this->SaveRanking();
			}
			return array($message,$result);
			*/
		}
	}

//////////////////////////////////////////////
// ��碌��
	function RankBattle(&$user,&$Rival,$UserPlace,$RivalPlace) {

		$UserPlace	= "[".($UserPlace+1)."��]";
		$RivalPlace	= "[".($RivalPlace+1)."��]";

		/*
			�� ���Υ桼�����Τ�����¸�ߤ��ʤ����ν���
			��������Ȥ�����������줿���˥�󥭥󥰤����ä���褦�ˤ�������
			����Фʤ����顼���⤷��ʤ���
		*/
		if($Rival->is_exist() == false) {
			ShowError("��꤬����¸�ߤ��Ƥ��ޤ���Ǥ���(���ﾡ)");
			$this->DeleteRank($DefendID);
			$this->SaveRanking();
			//return array(true);
			return "DEFENDER_NO_ID";
		}

		// ���ߤ��Υ�󥭥��ѤΥѡ��ƥ������ɤ߹���
		$Party_Challenger	= $user->RankParty();
		$Party_Defender		= $Rival->RankParty();


		// ����ѥѡ��ƥ���������ޤ��󡪡���
		if($Party_Challenger === false) {
			ShowError("�臘���С������ޤ���");
			return "CHALLENGER_NO_PARTY";
		}

		// ����ѥѡ��ƥ���������ޤ��󡪡���
		if($Party_Defender === false) {
			//$defender->RankRecord(0,"DEFEND",$DefendMatch);
			//$defender->SaveData();
			ShowError($Rival->name." �����省��餬���ꤵ��Ƥ��ޤ���Ǥ���<br />(���ﾡ)");
			return "DEFENDER_NO_PARTY";//���ﾡ�Ȥ���
		}

		//dump($Party_Challenger);
		//dump($Party_Defender);
		include(CLASS_BATTLE);
		$battle	= new battle($Party_Challenger,$Party_Defender);
		$battle->SetBackGround("colosseum");
		$battle->SetResultType(1);// ����Ĥ��ʤ�������¸�Ԥο��Ƿ���褦�ˤ���
		$battle->SetTeamName($user->name.$UserPlace,$Rival->name.$RivalPlace);
		$battle->Process();//��Ʈ����
		$battle->RecordLog("RANK");
		$Result	= $battle->ReturnBattleResult();// ��Ʈ���

		// ��Ʈ�������Ω�ä�¦�����ӤϤ������Ѥ��롣
		//$defender->RankRecord($Result,"DEFEND",$DefendMatch);
		//$defender->SaveData();

		//return array("Battle",$Result);
		if($Result === TEAM_0) {
			return "CHALLENGER_WIN";
		} else if ($Result === TEAM_1) {
			return "DEFENDER_WIN";
		} else if ($Result === DRAW) {
			return "DRAW_GAME";
		} else {
			return "DRAW_GAME";//(���顼)ͽ��ǤϽФʤ����顼(������)
		}
	}
//////////////////////////////////////////////////
//	��̤ˤ�äƽ������Ѥ���
	function ProcessByResult($Result,&$user,&$Rival,$DefendMatch) {
		switch($Result) {

			// ������¦��ID��¸�ߤ��ʤ�
			case "DEFENDER_NO_ID":
				$this->ChangePlace($user->id,$Rival->id);
				$this->DeleteRank($Rival->id);
				$this->SaveRanking();
				return false;
				break;

			// ĩ��¦PT̵��
			case "CHALLENGER_NO_PARTY":
				return false;
				break;

			// ������¦PT̵��
			case "DEFENDER_NO_PARTY":
				$this->ChangePlace($user->id,$Rival->id);
				$this->SaveRanking();
				//$user->RankRecord(0,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return true;
				break;

			// ĩ��Ծ���
			case "CHALLENGER_WIN":
				$this->ChangePlace($user->id,$Rival->id);
				$this->SaveRanking();
				$user->RankRecord(0,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return "BATTLE";
				break;

			// ������¦����
			case "DEFENDER_WIN":
				//$this->SaveRanking();
				$user->RankRecord(1,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);
				$Rival->RankRecord(1,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return "BATTLE";
				break;

			// ��ʬ��
			case "DRAW_GAME":
				//$this->SaveRanking();
				$user->RankRecord("d","CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);
				$Rival->RankRecord("d","DEFEND",$DefendMatch);
				$Rival->SaveData();
				return "BATTLE";
				break;
			default:
				return true;
				break;
		}
	}
//////////////////////////////////////////////////
//	�����ν�� �� Ʊ����̤οͿ�
	function SamePlaceAmount($Place) {
		switch(true) {
			case ($Place == 0): return 1;//1��
			case ($Place == 1): return 2;//2��
			case ($Place == 2): return 3;//3��
			case (2 < $Place):
				return 3;
		}
	}
//////////////////////////////////////////////
// ��󥭥󥰤κǲ��̤˻��ä�����
	function JoinRanking($id) {
		$last	= count($this->Ranking) - 1;
		// ��󥭥󥰤�¸�ߤ��ʤ����
		if(!$this->Ranking) {
			$this->Ranking["0"]["0"]["id"]	= $id;
		// �ǲ��̤ν�̤���������С��ˤʤ���
		} else if(count($this->Ranking[$last]) == $this->SamePlaceAmount($last)) {
			$this->Ranking[$last+1]["0"]["id"]	= $id;
		// �ʤ�ʤ����
		} else {
			$this->Ranking[$last][]["id"]	= $id;
		}
	}
//////////////////////////////////////////////////
// ��󥭥󥰤���ä�
	function DeleteRank($id) {
		$place	= $this->SearchID($id);
		if($place === false) return false;//�������
		unset($this->Ranking[$place[0]][$place[1]]);
		return true;//�������
	}
//////////////////////////////////////////////////
// ��󥭥󥰤���¸����
	function SaveRanking() {
		foreach($this->Ranking as $rank => $val) {
			foreach($val as $key => $val2) {
				$ranking	.= $val2["id"]."\n";
			}
		}

		WriteFileFP($this->fp,$ranking);
		$this->fpclose();
	}
//////////////////////////////////////////////////
//	
	function fpclose() {
		if($this->fp) {
			fclose($this->fp);
			unset($this->fp);
		}
	}
//////////////////////////////////////////////////
//	��̤������ؤ���
	function ChangePlace($id_0,$id_1) {
		$Place_0	= $this->SearchID($id_0);
		$Place_1	= $this->SearchID($id_1);
		$temp	= $this->Ranking[$Place_0["0"]][$Place_0["1"]];
		$this->Ranking[$Place_0["0"]][$Place_0["1"]]	= $this->Ranking[$Place_1["0"]][$Place_1["1"]];
		$this->Ranking[$Place_1["0"]][$Place_1["1"]]	= $temp;
	}
//////////////////////////////////////////////////
// $id �Υ�󥯰��֤�õ��
	function SearchID($id) {
		foreach($this->Ranking as $rank => $val) {
			foreach($val as $key => $val2) {
				if($val2["id"] == $id)
					return array((int)$rank,(int)$key);// ���̵���β����ܤ���
			}
		}
		return false;
	}
//////////////////////////////////////////////////
// ��󥭥󥰤�ɽ��
	function ShowRanking($from=false,$to=false,$bold_id=false) {
		// �ϰϤ�̵����������󥭥󥰤�ɽ��
		if($from === false or $to === false) {
			$from	= 0;//���
			$to		= count($this->Ranking);//�ǲ���
		}

		// �����ˤ�����
		if($bold_id)
			$BoldRank	= $this->SearchID($bold_id);

		$LastPlace	= count($this->Ranking) - 1;// �ǲ���

		print("<table cellspacing=\"0\">\n");
		print("<tr><td class=\"td6\" style=\"text-align:center\">���</td><td  class=\"td6\" style=\"text-align:center\">������</td></tr>\n");
		for($Place=$from; $Place<$to + 1; $Place++) {
			if(!$this->Ranking["$Place"])
				break;
			print("<tr><td class=\"td7\" valign=\"middle\" style=\"text-align:center\">\n");
			// ��̥�������
			switch($Place) {
				case 0:
					print('<img src="'.IMG_ICON.'crown01.png" class="vcent" />'); break;
				case 1:
					print('<img src="'.IMG_ICON.'crown02.png" class="vcent" />'); break;
				case 2:
					print('<img src="'.IMG_ICON.'crown03.png" class="vcent" />'); break;
				default:
					if($Place == $LastPlace)
						print("��");
					else
						print(($Place+1)."��");
			}
			print("</td><td class=\"td8\">\n");
			foreach($this->Ranking["$Place"] as $SubRank => $data) {
				list($Name,$R)	= $this->LoadUserName($data["id"],true);//���Ӥ��ɤ߹���
				$WinProb	= $R[all]?sprintf("%0.0f",($R[win]/$R[all])*100):"--";
				$Record	= "(".($R[all]?$R[all]:"0")."�� ".
						($R[win]?$R[win]:"0")."�� ".
						($R[lose]?$R[lose]:"0")."�� ".
						($R[all]-$R[win]-$R[lose])."�� ".
						($R[defend]?$R[defend]:"0")."�� ".
						"��Ψ".$WinProb.'%'.
						")";
				if(isset($BoldRank) && $BoldRank["0"] == $Place && $BoldRank["1"] == $SubRank) {
					print('<span class="bold u">'.$Name."</span> {$Record}");
				} else {
					print($Name." ".$Record);
				}
				print("<br />\n");
			}
			print("</td></tr>\n");
		}
		print("</table>\n");
	}
//////////////////////////////////////////////
//	�ޥ�� �о�ID
	function ShowRankingRange($id,$Amount) {
		$RankAmount	= count($this->Ranking);
		$Last	= $RankAmount - 1;
		do {
			// ��󥭥󥰤�Amount�ʾ�ʤ��Ȥ�
			if($RankAmount <= $Amount) {
				$start	= 0;
				$end	= $Last;
				break;
			}

			$Rank	= $this->SearchID($id);
			if($Rank === false) {
				print("��󥭥�����");
				return 0;
			}
			$Range	= floor($Amount/2);
			// ��̤˶ᤤ�����
			if( ($Rank[0] - $Range) <= 0 ) {
				$start	= 0;
				$end	= $Amount - 1;
			// �ǲ��̤ˤ��������ǲ���
			} else if( $Last < ($Rank[0] + $Range) ) {
				$start	= $RankAmount - $Amount;
				$end	= $RankAmount;
			// �ϰ���ˤ����ޤ�
			} else {
				$start	= $Rank[0]-$Range;
				$end	= $Rank[0]+$Range;
			}
		} while(0);

		$this->ShowRanking($start,$end,$id);
	}
//////////////////////////////////////////////
//	�桼����̾����ƤӽФ�
	function LoadUserName($id,$rank=false) {

		if(!$this->UserName["$id"]) {
			$User	= new user($id);
			$Name	= $User->Name();
			$Record	= $User->RankRecordLoad();
			if($Name !== false) {
				$this->UserName["$id"]	= $Name;
				$this->UserRecord["$id"]	= $Record;
			} else {
				$this->UserName["$id"]	= "-";

				$this->DeleteRank($id);

				foreach($this->Ranking as $rank => $val) {
					foreach($val as $key => $val2) {
						$ranking	.= $val2["id"]."\n";
					}
				}
		
				WriteFileFP($this->fp,$ranking);
			}
		}

		if($rank)
			return array($this->UserName["$id"],$this->UserRecord["$id"]);
		else
			return $this->UserName["$id"];
	}
//////////////////////////////////////////////////
//	
	function dump() {
		print("<pre>".print_r($this,1)."</pre>\n");
	}
// end of class
}
?>