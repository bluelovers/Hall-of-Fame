<?php

class Ranking {

	var $fp;

	var $Ranking	= array();
	var $UserName;
	var $UserRecord;

//////////////////////////////////////////////
// ファイルから読み込んでランキングを配列にする
/*

	$this->Ranking[0][0]= *********;// 首位

	$this->Ranking[1][0]= *********;// 同一 2位
	$this->Ranking[1][1]= *********;

	$this->Ranking[2][0]= *********;// 同一 3位
	$this->Ranking[2][1]= *********;
	$this->Ranking[2][2]= *********;

	$this->Ranking[3][0]= *********;// 同一 4位
	$this->Ranking[3][1]= *********;
	$this->Ranking[3][2]= *********;
	$this->Ranking[3][3]= *********;

	...........

*/
	function Ranking() {
		$file	= RANKING;

		if(!file_exists($file)) return 0;

		// ファイルから読んで配列にいれる

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
		// 配列が0なら終了
		if(!$this->Ranking) return 0;
		// 区切って文字列を分割
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
// ランキング戦する。戦う。
	function Challenge(&$user) {
		// ランキングが無いとき(1位になる)
		if(!$this->Ranking) {
			$this->JoinRanking($user->id);
			$this->SaveRanking();
			print("Rank starts.");
			//return array($message,true);
			return false;
		}
		//自分の順位
		$MyRank	= $this->SearchID($user->id);

		// 1位の場合。
		if($MyRank["0"] === 0) {
			SHowError("First place can't challenge.");
			//return array($message,true);
			return false;
		}

		// 自分がランク外なら ////////////////////////////////////
		if(!$MyRank)
		{
			$this->JoinRanking($user->id);//自分を最下位にする。
			$MyPlace	= count($this->Ranking) - 1;//自分のランク(最下位)
			$RivalPlace	= (int)($MyPlace - 1);

			// 相手が首位なのかどうか
			if($RivalPlace === 0)
				$DefendMatch	= true;
			else
				$DefendMatch	= false;

			//$MyID	= $id;

			//自分より1個上の人が相手。
			$RivalRankKey	= array_rand($this->Ranking[$RivalPlace]);
			$RivalID	= $this->Ranking[$RivalPlace][$RivalRankKey]["id"];//対戦する相手のID
			$Rival	= new user($RivalID);

			/*
			dump($this->Ranking);
			dump($RivalID);
			dump($MyID);
			dump($MyRank);//エラーでたら頑張れ
			return 0;
			*/

			$Result	= $this->RankBattle($user,$Rival,$MyPlace,$RivalPlace);
			$Return	= $this->ProcessByResult($Result,&$user,&$Rival,$DefendMatch);
			
			return $Return;
			// 勝利なら順位交代
			//if($message == "Battle" && $result === 0) {
			//	$this->ChangePlace($user,$Rival);
			//}

			//$this->SaveRanking();
			//return array($message,$result);
		}

		// 2位-最下位の人の処理。////////////////////////////////
		if($MyRank) {
			$RivalPlace	= (int)($MyRank["0"] - 1);//自分より順位が1個上の人。

			// 相手が首位なのかどうか
			if($RivalPlace === 0)
				$DefendMatch	= true;
			else
				$DefendMatch	= false;

			//自分より1個上の人が相手
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

			// 戦闘を行って勝利なら順位交代
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
// 戦わせる
	function RankBattle(&$user,&$Rival,$UserPlace,$RivalPlace) {

		$UserPlace	= "[".($UserPlace+1)."位]";
		$RivalPlace	= "[".($RivalPlace+1)."位]";

		/*
			■ 相手のユーザ自体が既に存在しない場合の処理
			アカウントが削除処理された時にランキングからも消えるようにしたから
			本来出ないエラーかもしれない。
		*/
		if($Rival->is_exist() == false) {
			ShowError("相手が既に存在していませんでした(不戦勝)");
			$this->DeleteRank($DefendID);
			$this->SaveRanking();
			//return array(true);
			return "DEFENDER_NO_ID";
		}

		// お互いのランキンぐ用のパーティーを読み込む
		$Party_Challenger	= $user->RankParty();
		$Party_Defender		= $Rival->RankParty();


		// ランク用パーティーがありません！！！
		if($Party_Challenger === false) {
			ShowError("戦うメンバーがいません。");
			return "CHALLENGER_NO_PARTY";
		}

		// ランク用パーティーがありません！！！
		if($Party_Defender === false) {
			//$defender->RankRecord(0,"DEFEND",$DefendMatch);
			//$defender->SaveData();
			ShowError($Rival->name." は対戦キャラが設定されていませんでした<br />(不戦勝)");
			return "DEFENDER_NO_PARTY";//不戦勝とする
		}

		//dump($Party_Challenger);
		//dump($Party_Defender);
		include(CLASS_BATTLE);
		$battle	= new battle($Party_Challenger,$Party_Defender);
		$battle->SetBackGround("colosseum");
		$battle->SetResultType(1);// 決着つかない場合は生存者の数で決めるようにする
		$battle->SetTeamName($user->name.$UserPlace,$Rival->name.$RivalPlace);
		$battle->Process();//戦闘開始
		$battle->RecordLog("RANK");
		$Result	= $battle->ReturnBattleResult();// 戦闘結果

		// 戦闘を受けて立った側の成績はここで変える。
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
			return "DRAW_GAME";//(エラー)予定では出ないエラー(回避用)
		}
	}
//////////////////////////////////////////////////
//	結果によって処理を変える
	function ProcessByResult($Result,&$user,&$Rival,$DefendMatch) {
		switch($Result) {

			// 受けた側のIDが存在しない
			case "DEFENDER_NO_ID":
				$this->ChangePlace($user->id,$Rival->id);
				$this->DeleteRank($Rival->id);
				$this->SaveRanking();
				return false;
				break;

			// 挑戦側PT無し
			case "CHALLENGER_NO_PARTY":
				return false;
				break;

			// 受けた側PT無し
			case "DEFENDER_NO_PARTY":
				$this->ChangePlace($user->id,$Rival->id);
				$this->SaveRanking();
				//$user->RankRecord(0,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return true;
				break;

			// 挑戦者勝ち
			case "CHALLENGER_WIN":
				$this->ChangePlace($user->id,$Rival->id);
				$this->SaveRanking();
				$user->RankRecord(0,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return "BATTLE";
				break;

			// 受けた側勝ち
			case "DEFENDER_WIN":
				//$this->SaveRanking();
				$user->RankRecord(1,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);
				$Rival->RankRecord(1,"DEFEND",$DefendMatch);
				$Rival->SaveData();
				return "BATTLE";
				break;

			// 引分け
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
//	引数の順位 と 同じ順位の人数
	function SamePlaceAmount($Place) {
		switch(true) {
			case ($Place == 0): return 1;//1位
			case ($Place == 1): return 2;//2位
			case ($Place == 2): return 3;//3位
			case (2 < $Place):
				return 3;
		}
	}
//////////////////////////////////////////////
// ランキングの最下位に参加させる
	function JoinRanking($id) {
		$last	= count($this->Ranking) - 1;
		// ランキングが存在しない場合
		if(!$this->Ranking) {
			$this->Ranking["0"]["0"]["id"]	= $id;
		// 最下位の順位が定員オーバーになる場合
		} else if(count($this->Ranking[$last]) == $this->SamePlaceAmount($last)) {
			$this->Ranking[$last+1]["0"]["id"]	= $id;
		// ならない場合
		} else {
			$this->Ranking[$last][]["id"]	= $id;
		}
	}
//////////////////////////////////////////////////
// ランキングから消す
	function DeleteRank($id) {
		$place	= $this->SearchID($id);
		if($place === false) return false;//削除失敗
		unset($this->Ranking[$place[0]][$place[1]]);
		return true;//削除成功
	}
//////////////////////////////////////////////////
// ランキングを保存する
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
//	順位を入れ替える
	function ChangePlace($id_0,$id_1) {
		$Place_0	= $this->SearchID($id_0);
		$Place_1	= $this->SearchID($id_1);
		$temp	= $this->Ranking[$Place_0["0"]][$Place_0["1"]];
		$this->Ranking[$Place_0["0"]][$Place_0["1"]]	= $this->Ranking[$Place_1["0"]][$Place_1["1"]];
		$this->Ranking[$Place_1["0"]][$Place_1["1"]]	= $temp;
	}
//////////////////////////////////////////////////
// $id のランク位置を探す
	function SearchID($id) {
		foreach($this->Ranking as $rank => $val) {
			foreach($val as $key => $val2) {
				if($val2["id"] == $id)
					return array((int)$rank,(int)$key);// 順位無いの何番目か。
			}
		}
		return false;
	}
//////////////////////////////////////////////////
// ランキングの表示
	function ShowRanking($from=false,$to=false,$bold_id=false) {
		// 範囲が無い場合は全ランキングを表示
		if($from === false or $to === false) {
			$from	= 0;//首位
			$to		= count($this->Ranking);//最下位
		}

		// 太字にするランク
		if($bold_id)
			$BoldRank	= $this->SearchID($bold_id);

		$LastPlace	= count($this->Ranking) - 1;// 最下位

		print("<table cellspacing=\"0\">\n");
		print("<tr><td class=\"td6\" style=\"text-align:center\">順位</td><td  class=\"td6\" style=\"text-align:center\">チーム</td></tr>\n");
		for($Place=$from; $Place<$to + 1; $Place++) {
			if(!$this->Ranking["$Place"])
				break;
			print("<tr><td class=\"td7\" valign=\"middle\" style=\"text-align:center\">\n");
			// 順位アイコン
			switch($Place) {
				case 0:
					print('<img src="'.IMG_ICON.'crown01.png" class="vcent" />'); break;
				case 1:
					print('<img src="'.IMG_ICON.'crown02.png" class="vcent" />'); break;
				case 2:
					print('<img src="'.IMG_ICON.'crown03.png" class="vcent" />'); break;
				default:
					if($Place == $LastPlace)
						print("底");
					else
						print(($Place+1)."位");
			}
			print("</td><td class=\"td8\">\n");
			foreach($this->Ranking["$Place"] as $SubRank => $data) {
				list($Name,$R)	= $this->LoadUserName($data["id"],true);//成績も読み込む
				$WinProb	= $R[all]?sprintf("%0.0f",($R[win]/$R[all])*100):"--";
				$Record	= "(".($R[all]?$R[all]:"0")."戦 ".
						($R[win]?$R[win]:"0")."勝 ".
						($R[lose]?$R[lose]:"0")."敗 ".
						($R[all]-$R[win]-$R[lose])."引 ".
						($R[defend]?$R[defend]:"0")."防 ".
						"勝率".$WinProb.'%'.
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
//	±ランク 対象ID
	function ShowRankingRange($id,$Amount) {
		$RankAmount	= count($this->Ranking);
		$Last	= $RankAmount - 1;
		do {
			// ランキングがAmount以上ないとき
			if($RankAmount <= $Amount) {
				$start	= 0;
				$end	= $Last;
				break;
			}

			$Rank	= $this->SearchID($id);
			if($Rank === false) {
				print("ランキング不明");
				return 0;
			}
			$Range	= floor($Amount/2);
			// 首位に近いか首位
			if( ($Rank[0] - $Range) <= 0 ) {
				$start	= 0;
				$end	= $Amount - 1;
			// 最下位にちかいか最下位
			} else if( $Last < ($Rank[0] + $Range) ) {
				$start	= $RankAmount - $Amount;
				$end	= $RankAmount;
			// 範囲内におさまる
			} else {
				$start	= $Rank[0]-$Range;
				$end	= $Rank[0]+$Range;
			}
		} while(0);

		$this->ShowRanking($start,$end,$id);
	}
//////////////////////////////////////////////
//	ユーザの名前を呼び出す
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