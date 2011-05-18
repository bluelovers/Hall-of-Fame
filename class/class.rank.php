<?
class Ranking {
/*
�������(��󥭥���)
1. ĩ��Ԥ�ID���Ϥ�
2.
	1�̤ο͡�
		��Ʈ�Ǥ��ޤ��󥨥顼��
	2-�ǲ��̤ο͡�
		1�ľ�οͤ�õ����
	��󥯳��ο͡�
		�ǲ��̤οͤ�õ����
3. ��ʬ��������Ʈ
4. �����ԡ��ԼԤν����ư
5. ��¸��
----------------------------
���顼�ݤ��衢�ݤ��衼
�����ꤦ�����Ƥ�(?)���ݡ�
��|1�̤���ʤ���(��󥯼��Τ�̵���Ȥ�)ĩ��Ԥ�1�̤ˤʤ롣
��|1�̤�ĩ��Ǥ��ʤ���
��|�����2��-�ǲ��̤μԤ����ĩ�路�ƾ��ġ�
��|�����2��-�ǲ��̤μԤ����ĩ�路���餱�롣
��|�����2��-�ǲ��̤μԤ����ĩ�路��1�̤ˤʤ롣
��|��������Ͽ�����̵���Ԥ�ĩ��Ǥ��ʤ���
��|��������Ͽ�Ϥ������ɡ���󥭥󥰤˻��ä��Ƥʤ��Ԥ�ĩ�魯�롣
��|ĩ�路�����Υ����ब��������(��̾�礱�Ƥ���)��
��|ĩ�路�����Υ����ब��������(�����礱�Ƥ���)��
��|ĩ�路������ID���Τ��ä��Ƥ��롣
��|ID��ä����Ȥ���󥭥󥰤������Ǥ��롣
��|�������¤��������ĩ��Ǥ��ʤ���
��|��꤬����������(�����֤�̵�ط�)
*/

	var $Ranking	= array();

//////////////////////////////////////////////
// �ե����뤫���ɤ߹���ǥ�󥭥󥰤�����ˤ���
	function Ranking() {
		$file	= RANKING;

		if(!file_exists($file)) return 0;

		// �ե����뤫���ɤ������ˤ����
		$fp	= fopen($file,"r");
		flock($fp,LOCK_EX);
		while($line = fgets($fp) ) {
			$line	= trim($line);
			if(trim($line) == "") continue;
				$this->Ranking[]	= $line;
		}
		//$this->Ranking	= file($file);
		// ����0�ʤ齪λ
		if(!$this->Ranking) return 0;
		// ���ڤä�ʸ�����ʬ��
		foreach($this->Ranking as $rank => $val) {
			$list	= explode("<>", $val);
			$this->Ranking["$rank"]	= array();
			$this->Ranking["$rank"]["id"]	= $list["0"];
		}
		//$this->JoinRanking("yqyqqq","last");
		//dump($this->Ranking);
	}

//////////////////////////////////////////////
// ��󥭥��魯�롣�臘��
	function Challenge($id) {
		// ��󥭥󥰤�̵���Ȥ�(1�̤ˤʤ�)
		if(!$this->Ranking) {
			$this->JoinRanking($id);
			$this->SaveRanking();
			$message	= "Rank starts."; 
			return array($message,true);
		}

		$MyRank	= $this->SearchID($id);//��ʬ�ν��
		// 1�̤ξ�硣
		if($MyRank === 0) {
			$message	= "First place can't challenge.";
			return array($message,true);
		}

		// ��ʬ����󥯳��ʤ�
		if(!$MyRank) {
			$this->JoinRanking($id);//��ʬ��ǲ��̤ˤ��롣
			$MyRank	= count($this->Ranking) - 1;//��ʬ�Υ��(�ǲ���)

			$MyID	= $this->Ranking["$MyRank"]["id"];
			$RivalID= $this->Ranking["$MyRank" - 1]["id"];//��ʬ���1�ľ�οͤ���ꡣ
			/*
			dump($this->Ranking);
			dump($RivalID);
			dump($MyID);
			dump($MyRank);//���顼�Ǥ����ĥ��
			return 0;*/
			list($message,$result)	= $this->RankBattle($MyID,$RivalID);
			if($message == "Battle" && $result === true)
				$this->RankUp($MyID);

			$this->SaveRanking();
			return array($message,$result);
		}

		// 2��-�ǲ��̤οͤν�����
		if($MyRank) {
			$rival	= $MyRank - 1;//��ʬ����̤�1�ľ�ο͡�

			$MyID	= $this->Ranking["$MyRank"]["id"];
			$RivalID= $this->Ranking["$rival"]["id"];
			list($message,$result)	= $this->RankBattle($MyID,$RivalID);
			if($message != "Battle")
				return array($message,$result);

			// ��Ʈ��Ԥä�true�ʤ��󥯤���
			if($message == "Battle" && $result === true) {
				$this->RankUp($MyID);
				$this->SaveRanking();
			}
			return array($message,$result);
		}
	}

//////////////////////////////////////////////
// ��碌��
	function RankBattle($ChallengerID,$DefendID) {
		$challenger	= new user($ChallengerID);
		$challenger->CharDataLoadAll();
		$defender	= new user($DefendID);
		$defender->CharDataLoadAll();
		//print($ChallengerID."<br>".$DefendID."<br>");

		$Party_Challenger	= $challenger->RankParty();
		$Party_Defender		= $defender->RankParty();
		if($Party_Defender == "NOID") {//�桼�����Τ�����¸�ߤ��ʤ����
			$message	= "No USER...<br />(win a game by default)";
			$this->DeleteRank($DefendID);
			$this->SaveRanking();
			return array($message,true);
		}

		// ����
		// array(��å�����,��Ʈ�����ä���,����)

		// ����ѥѡ��ƥ���������ޤ��󡪡���
		if($Party_Challenger === false) {
			$message	= "Set Team for Battle!<br />(Your Rank will be removed if challenged by someone)";
			return array($message,true);
		}
		// ����ѥѡ��ƥ���������ޤ��󡪡���
		if($Party_Defender === false) {
			$this->DeleteRank($DefendID);
			$this->SaveRanking();
			$message	= "{$defender->name} has no Teams for Rank<br />(win a game by default)";
			return array($message,true);
		}

		//dump($Party_Challenger);
		//dump($Party_Defender);
		include(CLASS_BATTLE);
		$battle	= new battle($Party_Challenger,$Party_Defender);
		$battle->SetBackGround("colosseum");
		$battle->SetTeamName($challenger->name,$defender->name);
		$battle->Process();//��Ʈ����
		$battle->RecordLog("RANK");
		return array("Battle",$battle->isChallengerWin());
	}

//////////////////////////////////////////////
// ��󥭥󥰤˻��ä����롣
	function JoinRanking($id,$place=false) {
		if(!$place)//�ǲ��̤������
			$place	= count($this->Ranking);
		$data	= array(array("id"=>$id));
		array_splice($this->Ranking, $place, 0, $data);
	}

//////////////////////////////////////////////////
// ��̤������ؤ��롣
	function ChangeRank($id,$id0) {
	
	}

//////////////////////////////////////////////////
// ��̤�夲�롣
	function RankUp($id) {
		$place	= $this->SearchID($id);
		//1�̤�̵�� ���ȡ���󥭥󥰤�1�Ĥξ��(1�̤Τ�)
		$number	= count($this->Ranking);
		if($place === 0 || $number < 2)
			return false;

		$temp	= $this->Ranking["$place"];
		$this->Ranking["$place"]	= $this->Ranking["$place"-1];
		$this->Ranking["$place"-1]	= $temp;
	}

//////////////////////////////////////////////////
// ��̤򲼤��롣
	function RankDown($id) {
		$place	= $this->SearchID($id);
		// �ǲ��̤�̵�� ���ȡ���󥭥󥰤�1�Ĥξ��(1�̤Τ�)
		$number	= count($this->Ranking);
		if($place === ($number - 1) ||  $number < 2)
			return false;

		$temp	= $this->Ranking["$place"];
		$this->Ranking["$place"]	= $this->Ranking["$place"+1];
		$this->Ranking["$place"+1]	= $temp;
	}

//////////////////////////////////////////////////
// ��󥭥󥰤���ä�
	function DeleteRank($id) {
		$place	= $this->SearchID($id);
		if($place === false) return false;//�������
		unset($this->Ranking["$place"]);
		return true;//�������
	}

//////////////////////////////////////////////////
// ��󥭥󥰤���¸����
	function SaveRanking() {
		foreach($this->Ranking as $rank => $val) {
			$ranking	.= $val["id"]."\n";
		}

		WriteFile(RANKING,$ranking);
	}

//////////////////////////////////////////////////
// $id ��õ��
	function SearchID($id) {
		foreach($this->Ranking as $rank => $val) {
			if($val["id"] == $id)
				return (int)$rank;
		}
		return false;
	}

//////////////////////////////////////////////////
// ��󥭥󥰤�ɽ��
	function ShowRanking($from=false,$to=false,$bold=false) {
		$last	= count($this->Ranking) - 1;
		// ��󥭥󥰤�¸�ߤ��ʤ���
		if(count($this->Ranking) < 1) {
			print("<div class=\"bold\">No Ranking.</div>\n");
		// ɽ�����������ꤵ�줿��
		} else if(is_numeric($from) && is_numeric($to)) {
			for($from; $from<$to; $from++) {
				$user	= new user($this->Ranking["$from"]["id"]);
				$place	= ($from==$last?"��(�ǲ���)":"��");
				if($bold === $from) {
					echo ($from+1)."{$place} : <span class=\"u\">".$user->name."</span><br />";
					continue;
				}
				if($this->Ranking["$from"])
					echo ($from+1)."{$place} : ".$user->name."<br />";
				//else break;
			}
		// ɽ�����������ꤵ��ʤ��ä���(��ɽ��)
		} else if(!$no) {
			foreach($this->Ranking as $key => $val) {
				$user	= new user($val["id"]);
				echo ($key+1)."�� : ".$user->name."<br />";
			}
		}
	}

//////////////////////////////////////////////////
// $id���դΥ�󥭥󥰤�ɽ��
	function ShowNearlyRank($id,$no=5) {
		//dump($this->Ranking);
		$MyRank	= $this->SearchID($id);
		//print("aaa".$MyRank.":".$id."<br>");
		$lowest	= count($this->Ranking);
		// �ǲ��̤˶ᤤ�ΤǷ���夲��ɽ��
		if( $lowest < ($MyRank+$no) ) {
			$moveup	= $no - ($lowest - $MyRank);
			$this->ShowRanking($MyRank-$moveup-5,$lowest,$MyRank);
			return 0;
		}
		// ��˶ᤤ�ΤǷ��겼����ɽ��
		if( ($MyRank-$no) < 0 ) {
			$this->ShowRanking(0,$no+5,$MyRank);
			return 0;
		}
		// ���
		$this->ShowRanking($MyRank-$no,$MyRank+$no,$MyRank);
	}

// end of class
}
?>