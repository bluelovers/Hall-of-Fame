<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_RANKING);

/**
 * 処理手順(ランキング戦)
 * 1. 挑戦者のIDを渡す
 * 2.
 * 1位の人。
 * 戦闘できませんエラー。
 * 2-最下位の人。
 * 1個上の人を探す。
 * ランク外の人。
 * 最下位の人を探す。
 * 3. 自分の相手と戦闘
 * 4. 勝利者、敗者の順位変動
 * 5. 保存。
 * ----------------------------
 * エラー怖いよ、怖いよー
 * 起こりうる全ての(?)事象。
 * ◎|1位が居ない時(ランク自体が無いとき)挑戦者が1位になる。
 * ◎|1位は挑戦できない。
 * ◎|正常な2位-最下位の者が上に挑戦して勝つ。
 * ◎|正常な2位-最下位の者が上に挑戦して負ける。
 * ◎|正常な2位-最下位の者が上に挑戦して1位になる。
 * △|チーム登録されて無い者は挑戦できない。
 * ○|チーム登録はしたけど、ランキングに参加してない者が挑戦する。
 * ◎|挑戦した相手のチームがおかしい(数名欠けている)。
 * ◎|挑戦した相手のチームがおかしい(全員欠けている)。
 * ○|挑戦した相手のID自体が消えている。
 * ○|IDを消したときランキングからも消滅する。
 * △|時間制限がある場合は挑戦できない。
 * ◎|相手が時間制限中(→たぶん無関係)
 */
class HOF_Class_Ranking extends HOF_Class_Base
{

	/**
	 * Ranking
	 *
	 * ファイルから読み込んでランキングを配列にする
	 * $this->data[0][0]= *********;// 首位
	 *
	 * $this->data[1][0]= *********;// 同一 2位
	 * $this->data[1][1]= *********;
	 *
	 * $this->data[2][0]= *********;// 同一 3位
	 * $this->data[2][1]= *********;
	 * $this->data[2][2]= *********;
	 *
	 * $this->data[3][0]= *********;// 同一 4位
	 * $this->data[3][1]= *********;
	 * $this->data[3][2]= *********;
	 * $this->data[3][3]= *********;
	 *
	 * ...........
	 *
	 * @var Array
	 */
	var $data = array();

	var $UserName;
	var $UserRecord;

	var $file = RANKING;

	/**
	 * 相手が既に存在していませんでした(不戦勝)
	 * 受けた側のIDが存在しない
	 */
	const DEFENDER_NO_ID = 'DEFENDER_NO_ID';
	/**
	 * 挑戦側PT無し
	 * 戦うメンバーがいません。
	 */
	const CHALLENGER_NO_PARTY = 'CHALLENGER_NO_PARTY';
	/**
	 * 受けた側PT無し
	 * $Rival->name は対戦キャラが設定されていませんでした<br />(不戦勝)
	 */
	const DEFENDER_NO_PARTY = 'DEFENDER_NO_PARTY';
	/**
	 * 挑戦者勝ち
	 */
	const CHALLENGER_WIN = 'CHALLENGER_WIN';
	/**
	 * 受けた側勝ち
	 */
	const DEFENDER_WIN = 'DEFENDER_WIN';
	/**
	 * 引分け
	 */
	const DRAW_GAME = 'DRAW_GAME';

	const RESULT_TRUE = true;
	const RESULT_FALSE = false;
	const RESULT_BATTLE = 'BATTLE';

	function _fpread()
	{
		$this->data = array();

		$Place = 0;
		while ($line = fgets($this->fp))
		{
			$line = trim($line);
			if ($line == "") continue;
			if (count($this->data[$Place]) === $this->SamePlaceAmount($Place)) $Place++;
			$this->data[$Place][] = $line;
		}

		//$this->data	= file($this->file);

		// 区切って文字列を分割
		foreach ($this->data as $Rank => $SamePlaces)
		{
			if (!is_array($SamePlaces)) continue;
			foreach ($SamePlaces as $key => $val)
			{
				$list = explode("<>", $val);
				$this->data["$Rank"]["$key"] = array();
				$this->data["$Rank"]["$key"]["id"] = $list["0"];
			}
		}
	}

	/**
	 * ランキングを保存する
	 */
	function _fpsave()
	{
		foreach ($this->data as $rank => $val)
		{
			foreach ($val as $key => $val2)
			{
				$ranking .= $val2["id"] . "\n";
			}
		}

		HOF_Class_File::WriteFileFP($this->fp, $ranking);
	}

	function _init()
	{
		if (!$this->fpopen()) return false;

		$this->fpread();

		// 配列が0なら終了
		if (!$this->data) return false;

		//$this->JoinRanking("yqyqqq","last");
		//$this->dump($this->data);

		return $this;
	}

	/**
	 * ランキング戦する。戦う。
	 */
	function Challenge(&$user)
	{
		// ランキングが無いとき(1位になる)
		if (!$this->data)
		{
			$this->JoinRanking($user->id);
			$this->fpsave();
			/*
			print ("Rank starts.");
			//return array($message,true);
			return false;
			*/

			return array(-1, "Rank starts.");
		}

		//自分の順位
		$MyRank = $this->SearchID($user->id);

		// 1位の場合。
		if ($MyRank["0"] === 0)
		{
			/*
			HOF_Helper_Global::SHowError("First place can't challenge.");
			//return array($message,true);
			return false;
			*/
			return array(false, "First place can't challenge.");
		}

		// 自分がランク外なら ////////////////////////////////////
		if (!$MyRank)
		{
			$this->JoinRanking($user->id); //自分を最下位にする。
			$MyPlace = count($this->data) - 1; //自分のランク(最下位)
			$RivalPlace = (int)($MyPlace - 1);

			// 相手が首位なのかどうか
			if ($RivalPlace === 0) $DefendMatch = true;
			else  $DefendMatch = false;

			//$MyID	= $id;

			//自分より1個上の人が相手。
			$RivalRankKey = array_rand($this->data[$RivalPlace]);
			$RivalID = $this->data[$RivalPlace][$RivalRankKey]["id"]; //対戦する相手のID
			$Rival = new HOF_Class_User($RivalID);

			/*
			$this->dump($this->data);
			$this->dump($RivalID);
			$this->dump($MyID);
			$this->dump($MyRank);//エラーでたら頑張れ
			return 0;
			*/

			$Result = $this->RankBattle($user, $Rival, $MyPlace, $RivalPlace);
			$Return = $this->ProcessByResult($Result, &$user, &$Rival, $DefendMatch);

			return array($Return);
			// 勝利なら順位交代
			//if($message == "Battle" && $result === 0) {
			//	$this->ChangePlace($user,$Rival);
			//}

			//$this->fpsave();
			//return array($message,$result);
		}

		// 2位-最下位の人の処理。////////////////////////////////
		if ($MyRank)
		{
			$RivalPlace = (int)($MyRank["0"] - 1); //自分より順位が1個上の人。

			// 相手が首位なのかどうか
			if ($RivalPlace === 0) $DefendMatch = true;
			else  $DefendMatch = false;

			//自分より1個上の人が相手
			$RivalRankKey = array_rand($this->data[$RivalPlace]);
			$RivalID = $this->data[$RivalPlace][$RivalRankKey]["id"];
			$Rival = new HOF_Class_User($RivalID);
			//$MyID		= $this->data[$MyRank["0"]][$MyRank["1"]]["id"];
			//$MyID		= $id;
			//list($message,$result)	= $this->RankBattle($MyID,$RivalID);
			$Result = $this->RankBattle($user, $Rival, $MyRank["0"], $RivalPlace);
			$Return = $this->ProcessByResult($Result, &$user, &$Rival, $DefendMatch);

			return array($Return);
			//if($message != "Battle")
			//	return array($message,$result);

			// 戦闘を行って勝利なら順位交代
			/*
			if($message == "Battle" && $result === 0) {
			$this->ChangePlace($MyID,$RivalID);
			//$this->dump($this->data);
			$this->fpsave();
			}
			return array($message,$result);
			*/
		}
	}

	/**
	 * 戦わせる
	 */
	function RankBattle(&$user, &$Rival, $UserPlace, $RivalPlace)
	{

		$UserPlace = "[" . ($UserPlace + 1) . "位]";
		$RivalPlace = "[" . ($RivalPlace + 1) . "位]";

		/*
		■ 相手のユーザ自体が既に存在しない場合の処理
		アカウントが削除処理された時にランキングからも消えるようにしたから
		本来出ないエラーかもしれない。
		*/
		if ($Rival->is_exist() == false)
		{
			HOF_Helper_Global::ShowError("相手が既に存在していませんでした(不戦勝)");
			$this->DeleteRank($DefendID);
			$this->fpsave();
			//return array(true);
			return self::DEFENDER_NO_ID;
		}

		// お互いのランキンぐ用のパーティーを読み込む
		$Party_Challenger = $user->RankParty();
		$Party_Defender = $Rival->RankParty();


		// ランク用パーティーがありません！！！
		if ($Party_Challenger === false)
		{
			HOF_Helper_Global::ShowError("戦うメンバーがいません。");
			return self::CHALLENGER_NO_PARTY;
		}

		// ランク用パーティーがありません！！！
		if ($Party_Defender === false)
		{
			//$defender->RankRecord(0,"DEFEND",$DefendMatch);
			//$defender->SaveData();
			HOF_Helper_Global::ShowError($Rival->name . " は対戦キャラが設定されていませんでした<br />(不戦勝)");
			return self::DEFENDER_NO_PARTY; //不戦勝とする
		}

		//$this->dump($Party_Challenger);
		//$this->dump($Party_Defender);

		$battle = new HOF_Class_Battle($Party_Challenger, $Party_Defender);
		$battle->SetBackGround("colosseum");
		$battle->SetResultType(1); // 決着つかない場合は生存者の数で決めるようにする
		$battle->SetTeamName($user->name . $UserPlace, $Rival->name . $RivalPlace);
		$battle->Process(); //戦闘開始
		$battle->RecordLog("RANK");
		$Result = $battle->ReturnBattleResult(); // 戦闘結果

		// 戦闘を受けて立った側の成績はここで変える。
		//$defender->RankRecord($Result,"DEFEND",$DefendMatch);
		//$defender->SaveData();

		//return array("Battle",$Result);
		if ($Result === TEAM_0)
		{
			return self::CHALLENGER_WIN;
		}
		else
			if ($Result === TEAM_1)
			{
				return self::DEFENDER_WIN;
			}
			else
				if ($Result === DRAW)
				{
					return self::DRAW_GAME;
				}
				else
				{
					return self::DRAW_GAME; //(エラー)予定では出ないエラー(回避用)
				}
	}

	/**
	 * 結果によって処理を変える
	 */
	function ProcessByResult($Result, &$user, &$Rival, $DefendMatch)
	{
		switch ($Result)
		{

				// 受けた側のIDが存在しない
			case self::DEFENDER_NO_ID:
				$this->ChangePlace($user->id, $Rival->id);
				$this->DeleteRank($Rival->id);
				$this->fpsave();
				return self::RESULT_FALSE;
				break;

				// 挑戦側PT無し
			case self::CHALLENGER_NO_PARTY:
				return self::RESULT_FALSE;
				break;

				// 受けた側PT無し
			case self::DEFENDER_NO_PARTY:
				$this->ChangePlace($user->id, $Rival->id);
				$this->fpsave();
				//$user->RankRecord(0,"CHALLENGER",$DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0, "DEFEND", $DefendMatch);
				$Rival->SaveData();
				return self::RESULT_TRUE;
				break;

				// 挑戦者勝ち
			case self::CHALLENGER_WIN:
				$this->ChangePlace($user->id, $Rival->id);
				$this->fpsave();
				$user->RankRecord(0, "CHALLENGER", $DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);
				$Rival->RankRecord(0, "DEFEND", $DefendMatch);
				$Rival->SaveData();
				return self::RESULT_BATTLE;
				break;

				// 受けた側勝ち
			case self::DEFENDER_WIN:
				//$this->fpsave();
				$user->RankRecord(1, "CHALLENGER", $DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);
				$Rival->RankRecord(1, "DEFEND", $DefendMatch);
				$Rival->SaveData();
				return self::RESULT_BATTLE;
				break;

				// 引分け
			case self::DRAW_GAME:
				//$this->fpsave();
				$user->RankRecord("d", "CHALLENGER", $DefendMatch);
				$user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);
				$Rival->RankRecord("d", "DEFEND", $DefendMatch);
				$Rival->SaveData();
				return self::RESULT_BATTLE;
				break;
			default:
				return true;
				break;
		}
	}

	/**
	 * 引数の順位 と 同じ順位の人数
	 */
	function SamePlaceAmount($Place)
	{
		return HOF_Helper_Math::minmax($Place + 1, 1, 3);
	}

	/**
	 * ランキングの最下位に参加させる
	 */
	function JoinRanking($id)
	{
		$last = count($this->data) - 1;

		// ランキングが存在しない場合
		if (!$this->data)
		{
			// 最下位の順位が定員オーバーになる場合
			$this->data["0"]["0"]["id"] = $id;

		}
		else
		{
			if (count($this->data[$last]) == $this->SamePlaceAmount($last))
			{
				// ならない場合
				$this->data[$last + 1]["0"]["id"] = $id;

			}
			else
			{
				$this->data[$last][]["id"] = $id;
			}
		}
	}

	/**
	 * ランキングから消す
	 */
	function DeleteRank($id)
	{
		$place = $this->SearchID($id);
		if ($place === false) return false; //削除失敗
		unset($this->data[$place[0]][$place[1]]);
		return true; //削除成功
	}

	/**
	 * 順位を入れ替える
	 */
	function ChangePlace($id_0, $id_1)
	{
		$Place_0 = $this->SearchID($id_0);
		$Place_1 = $this->SearchID($id_1);
		$temp = $this->data[$Place_0["0"]][$Place_0["1"]];
		$this->data[$Place_0["0"]][$Place_0["1"]] = $this->data[$Place_1["0"]][$Place_1["1"]];
		$this->data[$Place_1["0"]][$Place_1["1"]] = $temp;
	}

	/**
	 * $id のランク位置を探す
	 */
	function SearchID($id)
	{
		foreach ($this->data as $rank => $val)
		{
			foreach ($val as $key => $val2)
			{
				if ($val2["id"] == $id) return array((int)$rank, (int)$key); // 順位無いの何番目か。
			}
		}
		return false;
	}

	/**
	 * ランキングの表示
	 */
	function ShowRanking($from = false, $to = false, $bold_id = false)
	{
		// 範囲が無い場合は全ランキングを表示
		if ($from === false or $to === false)
		{
			$from = 0; //首位
			$to = count($this->data); //最下位
		}

		// 太字にするランク
		if ($bold_id) $BoldRank = $this->SearchID($bold_id);

		$LastPlace = count($this->data) - 1; // 最下位

		print ("<table cellspacing=\"0\">\n");
		print ("<tr><td class=\"td6\" style=\"text-align:center\">順位</td><td  class=\"td6\" style=\"text-align:center\">チーム</td></tr>\n");
		for ($Place = $from; $Place < $to + 1; $Place++)
		{
			if (!$this->data["$Place"]) break;
			print ("<tr><td class=\"td7\" valign=\"middle\" style=\"text-align:center\">\n");
			// 順位アイコン
			switch ($Place)
			{
				case 0:
					print ('<img src="' . HOF_Class_Icon::getImageUrl('crown01', IMG_ICON) . '" class="vcent" />');
					break;
				case 1:
					print ('<img src="' . HOF_Class_Icon::getImageUrl('crown02', IMG_ICON) . '" class="vcent" />');
					break;
				case 2:
					print ('<img src="' . HOF_Class_Icon::getImageUrl('crown03', IMG_ICON) . '" class="vcent" />');
					break;
				default:
					if ($Place == $LastPlace) print ("底");
					else  print (($Place + 1) . "位");
			}
			print ("</td><td class=\"td8\">\n");
			foreach ($this->data["$Place"] as $SubRank => $data)
			{
				list($Name, $R) = $this->LoadUserName($data["id"], true); //成績も読み込む
				$WinProb = $R[all] ? sprintf("%0.0f", ($R[win] / $R[all]) * 100) : "--";
				$Record = "(" . ($R[all] ? $R[all] : "0") . "戦 " . ($R[win] ? $R[win] : "0") . "勝 " . ($R[lose] ? $R[lose] : "0") . "敗 " . ($R[all] - $R[win] - $R[lose]) . "引 " . ($R[defend] ? $R[defend] : "0") . "防 " . "勝率" . $WinProb . '%' . ")";
				if (isset($BoldRank) && $BoldRank["0"] == $Place && $BoldRank["1"] == $SubRank)
				{
					print ('<span class="bold u">' . $Name . "</span> {$Record}");
				}
				else
				{
					print ($Name . " " . $Record);
				}
				print ("<br />\n");
			}
			print ("</td></tr>\n");
		}
		print ("</table>\n");
	}

	/**
	 * ±ランク 対象ID
	 */
	function ShowRankingRange($id, $Amount)
	{
		$RankAmount = count($this->data);
		$Last = $RankAmount - 1;
		do
		{
			// ランキングがAmount以上ないとき
			if ($RankAmount <= $Amount)
			{
				$start = 0;
				$end = $Last;
				break;
			}

			$Rank = $this->SearchID($id);
			if ($Rank === false)
			{
				print ("ランキング不明");
				return 0;
			}
			$Range = floor($Amount / 2);

			// 首位に近いか首位
			if (($Rank[0] - $Range) <= 0)
			{
				$start = 0;
				$end = $Amount - 1;
				// 最下位にちかいか最下位
			}
			else
			{
				if ($Last < ($Rank[0] + $Range))
				{
					$start = $RankAmount - $Amount;
					$end = $RankAmount;
					// 範囲内におさまる
				}
				else
				{
					$start = $Rank[0] - $Range;
					$end = $Rank[0] + $Range;
				}
			}
		} while (0);

		$this->ShowRanking($start, $end, $id);
	}

	/**
	 * ユーザの名前を呼び出す
	 */
	function LoadUserName($id, $rank = false)
	{

		if (!$this->UserName["$id"])
		{
			$User = new HOF_Class_User($id);
			$Name = $User->Name();
			$Record = $User->RankRecordLoad();
			if ($Name !== false)
			{
				$this->UserName["$id"] = $Name;
				$this->UserRecord["$id"] = $Record;
			}
			else
			{
				$this->UserName["$id"] = "-";

				$this->DeleteRank($id);

				$this->fpsave(true);
			}
		}

		if ($rank)
		{
			return array($this->UserName["$id"], $this->UserRecord["$id"]);
		}
		else
		{
			return $this->UserName["$id"];
		}
	}

}
