<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

//class battle extends HOF_Class_Skill_Effect
class battle
{
	/*
	* $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
	* $battle->SetTeamName($this->name,$party["name"]);
	* $battle->Process();//戦闘開始
	*/
	// teams
	var $team0, $team1;
	// team name
	var $team0_name, $team1_name;
	// team ave level
	var $team0_ave_lv, $team1_ave_lv;

	// 魔方陣
	var $team0_mc = 0;
	var $team1_mc = 0;

	// 戦闘の最大ターン数(延長される可能性のある)
	var $BattleMaxTurn = BATTLE_MAX_TURNS;
	var $NoExtends = false;

	//
	var $NoResult = false;

	// 戦闘背景
	var $BackGround = "grass";

	// スクロール ( << >> ← これの変数)
	var $Scroll = 0;

	// 総ダメージ
	var $team0_dmg = 0;
	var $team1_dmg = 0;
	// 総行動回数
	var $actions = 0;
	// 戦闘における基準ディレイ
	var $delay;
	// 勝利チーム
	var $result;
	// もらえるお金
	var $team0_money, $team1_money;
	// げっとしたアイテム
	var $team0_item = array(), $team1_item = array();
	var $team0_exp = 0, $team1_exp = 0; // 総経験値。

	// 特殊な変数
	var $ChangeDelay = false; //キャラのSPDが変化した際にDELAYを再計算する。

	var $BattleResultType = 0; // 0=決着着かなければDraw 1=生存者の数で勝敗を決める
	var $UnionBattle; // 残りHP総HPを隠す(????/????)

	function SetResultType($var)
	{
		$this->BattleResultType = $var;
	}

	//	UnionBattleである事にする。
	function SetUnionBattle()
	{
		$this->UnionBattle = true;
	}

	//	背景画像をセットする。
	function SetBackGround($bg)
	{
		$this->BackGround = $bg;
	}
	/*

	//	戦闘にキャラクターを途中参加させる。
	function JoinCharacter($user, $add)
	{
		foreach ($this->team0 as $char)
		{
			if ($user === $char)
			{
				//array_unshift($this->team0,$add);
				$this->team0->addChar($add, TEAM_0);

				//dump($this->team0);
				$this->ChangeDelay();
				return 0;
			}
		}
		foreach ($this->team1 as $char)
		{
			if ($user === $char)
			{
				//array_unshift($this->team1,$add);
				$this->team1->addChar($add, TEAM_1);
				$this->ChangeDelay();
				return 0;
			}
		}
	}
	*/

	//	限界ターン数を決めちゃう。
	function LimitTurns($no)
	{
		$this->BattleMaxTurn = $no;
		$this->NoExtends = true; //これ以上延長はしない。
	}

	//
	function NoResult()
	{
		$this->NoResult = true;
	}

	//	戦闘の最大ターン数を増やす。
	function ExtendTurns($no, $notice = false)
	{
		// 延長しない変数が設定されていれば延長しない。
		if ($this->NoExtends === true) return false;

		$this->BattleMaxTurn += $no;
		if (BATTLE_MAX_EXTENDS < $this->BattleMaxTurn) $this->BattleMaxTurn = BATTLE_MAX_EXTENDS;
		if ($notice)
		{
			echo <<< HTML
	<tr><td colspan="2" class="break break-top bold" style="text-align:center;padding:20px 0;">
	battle turns extended.
	</td></tr>
HTML;
		}
		return true;
	}

	//	戦闘中獲得したアイテムを返す。
	function ReturnItemGet($team)
	{
		if ($team == TEAM_0)
		{
			if (count($this->team0_item) != 0) return $this->team0_item;
			else  return false;
		}
		else
			if ($team == TEAM_1)
			{
				if (count($this->team1_item) != 0) return $this->team1_item;
				else  return false;
			}
	}

	//	挑戦者側が勝利したか？
	function ReturnBattleResult()
	{
		return $this->result;
	}

	//	戦闘後のキャラクター状況を保存する。
	function SaveCharacters()
	{
		//チーム0
		foreach ($this->team0 as $char)
		{
			$char->SaveCharData();
		}
		//チーム1
		foreach ($this->team1 as $char)
		{
			$char->SaveCharData();
		}
	}

	//	総ダメージを加算する
	function AddTotalDamage($team, $dmg)
	{
		if (!is_numeric($dmg)) return false;
		if ($team == $this->team0) $this->team0_dmg += $dmg;
		else
			if ($team == $this->team1) $this->team1_dmg += $dmg;
	}


	//


	//	経験値を得る
	function GetExp($exp, &$team)
	{
		if (!$exp) return false;

		$exp = round(EXP_RATE * $exp);

		if ($team === $this->team0)
		{
			$this->team0_exp += $exp;
		}
		else
		{
			$this->team1_exp += $exp;
		}

		$Alive = HOF_Class_Battle_Team::CountAliveChars($team);
		if ($Alive === 0) return false;
		$ExpGet = ceil($exp / $Alive); //生存者にだけ経験値を分ける。
		echo("Alives get {$ExpGet}exps.<br />\n");
		foreach ($team as $key => $char)
		{
			if ($char->STATE === 1) continue; //死亡者にはEXPあげない
			if ($team[$key]->GetExp($ExpGet)) //LvUpしたならtrueが返る
 					echo("<span class=\"levelup\">" . $char->Name() . " LevelUp!</span><br />\n");
		}
	}

	//	アイテムを取得する(チームが)
	function GetItem($itemdrop, $MyTeam)
	{
		if (!$itemdrop) return false;
		if ($MyTeam === $this->team0)
		{
			foreach ($itemdrop as $itemno => $amount)
			{
				$this->team0_item["$itemno"] += $amount;
			}
		}
		else
		{
			foreach ($itemdrop as $itemno => $amount)
			{
				$this->team1_item["$itemno"] += $amount;
			}
		}
	}


	//	後衛を守りに入るキャラを選ぶ。
	function &Defending(&$target, &$candidate, $skill)
	{
		if ($target === false) return false;

		if ($skill["invalid"]) //防御無視できる技。
 				return false;
		if ($skill["support"]) //支援なのでガードしない。
 				return false;
		if ($target->POSITION == POSITION_FRONT) //前衛なら守る必要無し。終わる
 				return false;
		// "前衛で尚且つ生存者"を配列に詰める↓
		// 前衛 + 生存者 + HP1以上 に変更 ( 多段系攻撃で死にながら守るので [2007/9/20] )
		foreach ($candidate as $key => $char)
		{
			//echo("{$char->POSTION}:{$char->STATE}<br>");
			if ($char->POSITION == POSITION_FRONT && $char->STATE !== 1 && 1 < $char->HP) $fore[] = &$candidate["$key"];
		}
		if (count($fore) == 0) //前衛がいなけりゃ守れない。終わる
 				return false;
		// 一人づつ守りに入るか入らないかを判定する。
		shuffle($fore); //配列の並びを混ぜる
		foreach ($fore as $key => $char)
		{
			// 判定に使う変数を計算したりする。
			switch ($char->guard)
			{
				case "life25":
				case "life50":
				case "life75":
					$HpRate = ($char->HP / $char->MAXHP) * 100;
				case "prob25":
				case "prob50":
				case "prob75":
					mt_srand();
					$prob = mt_rand(1, 100);
			}
			// 実際に判定してみる。
			switch ($char->guard)
			{
				case "never":
					continue;
				case "life25": // HP(%)が25%以上なら
					if (25 < $HpRate) $defender = &$fore["$key"];
					break;
				case "life50": // 〃50%〃
					if (50 < $HpRate) $defender = &$fore["$key"];
					break;
				case "life75": // 〃70%〃
					if (75 < $HpRate) $defender = &$fore["$key"];
					break;
				case "prob25": // 25%の確率で
					if ($prob < 25) $defender = &$fore["$key"];
					break;
				case "prob50": // 50% 〃
					if ($prob < 50) $defender = &$fore["$key"];
					break;
				case "prob75": // 75% 〃
					if ($prob < 75) $defender = &$fore["$key"];
					break;
				default:
					$defender = &$fore["$key"];
			}
			// 誰かが後衛を守りに入ったのでそれを表示する
			if ($defender)
			{
				echo('<span class="bold">' . $defender->name . '</span> protected <span class="bold">' . $target->name . '</span>!<br />' . "\n");
				return $defender;
			}
		}
	}

	//	スキル使用後に対象者(候補)がしぼーしたかどうかを確かめる
	function JudgeTargetsDead(&$target)
	{
		foreach ($target as $key => $char)
		{
			// 与えたダメージの差分で経験値を取得するモンスターの場合。
			if (method_exists($target[$key], 'HpDifferenceEXP'))
			{
				$exp += $target[$key]->HpDifferenceEXP();
			}
			if ($target[$key]->CharJudgeDead())
			{ //死んだかどうか
				// 死亡メッセージ
				echo("<span class=\"dmg\">" . $target[$key]->Name(bold) . " down.</span><br />\n");

				//経験値の取得
				$exp += $target[$key]->DropExp();

				//お金の取得
				$money += $target[$key]->DropMoney();

				// アイテムドロップ
				if ($item = $target[$key]->DropItem())
				{
					$itemdrop["$item"]++;
					$item = HOF_Model_Data::getItemData($item);
					echo($char->Name("bold") . " dropped");
					echo("<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM) . "\" class=\"vcent\"/>\n");
					echo("<span class=\"bold u\">{$item[name]}</span>.<br />\n");
				}

				//召喚キャラなら消す。
				if ($target[$key]->summon === true)
				{
					unset($target[$key]);
				}

				// 死んだのでディレイを直す。
				$this->ChangeDelay();
			}
		}
		return array(
			$exp,
			$money,
			$itemdrop); //取得する経験値を返す
	}

	//	優先順位に従って候補から一人返す
	function &SelectTarget(&$target_list, $skill)
	{

		/*
		* 優先はするが、当てはまらなくても最終的にターゲットは要る。
		* 例 : 後衛が居ない→前衛を対象にする。
		*    : 全員がHP100%→誰か てきとう に対象にする。
		*/

		//残りHP(%)が少ない人をターゲットにする
		if ($skill["priority"] == "LowHpRate")
		{
			$hp = 2; //一応1より大きい数字に・・・
			foreach ($target_list as $key => $char)
			{
				if ($char->STATE == STATE_DEAD) continue; //しぼー者は対象にならない。
				$HpRate = $char->HP / $char->MAXHP; //HP(%)
				if ($HpRate < $hp)
				{
					$hp = $HpRate; //現状の最もHP(%)が低い人
					$target = &$target_list[$key];
				}
			}
			return $target; //最もHPが低い人

			//後衛を優先する
		}
		else
			if ($skill["priority"] == "Back")
			{
				foreach ($target_list as $key => $char)
				{
					if ($char->STATE == STATE_DEAD) continue; //しぼー者は対象にならない。
					if ($char->POSITION != POSITION_FRONT) //後衛なら
 							$target[] = &$target_list[$key]; //候補にいれる
				}
				if ($target) return $target[array_rand($target)]; //リストの中からランダムで

				/*
				* 優先はするが、
				* 優先する対象がいなければ使用は失敗する(絞込み)
				*/

				//しぼー者の中からランダムで返す。
			}
			else
				if ($skill["priority"] == "Dead")
				{
					foreach ($target_list as $key => $char)
					{
						if ($char->STATE == STATE_DEAD) //しぼーなら
 								$target[] = &$target_list[$key]; //しぼー者リスト
					}
					if ($target) return $target[array_rand($target)]; //しぼー者リストの中からランダムで
					else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)

					// 召喚キャラを優先する。
				}
				else
					if ($skill["priority"] == "Summon")
					{
						foreach ($target_list as $key => $char)
						{
							if ($char->summon) //召喚キャラなら
 									$target[] = &$target_list[$key]; //召喚キャラリスト
						}
						if ($target) return $target[array_rand($target)]; //召喚キャラの中からランダムで
						else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)

						// チャージ中のキャラ
					}
					else
						if ($skill["priority"] == "Charge")
						{
							foreach ($target_list as $key => $char)
							{
								if ($char->expect) $target[] = &$target_list[$key];
							}
							if ($target) return $target[array_rand($target)];
							else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)
							//
						}

		//それ以外(ランダム)
		foreach ($target_list as $key => $char)
		{
			if ($char->STATE != STATE_DEAD) //しぼー以外なら
 					$target[] = &$target_list[$key]; //しぼー者リスト
		}
		return $target[array_rand($target)]; //ランダムに誰か一人
	}

	//	次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
	//	リファレンスを返す
	function &NextActer()
	{
		// 最もディレイが大きい人を探す
		foreach ($this->team0 as $key => $char)
		{
			if ($char->STATE === 1) continue;
			// 最初は誰でもいいのでとりあえず最初の人とする。
			if (!isset($delay))
			{
				$delay = $char->delay;
				$NextChar = &$this->team0["$key"];
				continue;
			}
			// キャラが今のディレイより多ければ交代
			if ($delay <= $char->delay)
			{ //行動
				// もしキャラとディレイが同じなら50%で交代
				if ($delay == $char->delay)
				{
					if (mt_rand(0, 1)) continue;
				}
				$delay = $char->delay;
				$NextChar = &$this->team0["$key"];
			}
		}
		// ↑と同じ。
		foreach ($this->team1 as $key => $char)
		{
			if ($char->STATE === 1) continue;
			if ($delay <= $char->delay)
			{ //行動
				if ($delay == $char->delay)
				{
					if (mt_rand(0, 1)) continue;
				}
				$delay = $char->delay;
				$NextChar = &$this->team1["$key"];
			}
		}
		// 全員ディレイ減少
		$dif = $this->delay - $NextChar->delay; //戦闘基本ディレイと行動者のディレイの差分
		if ($dif < 0) //もしも差分が0以下になったら…
 				return $NextChar;
		foreach ($this->team0 as $key => $char)
		{
			$this->team0["$key"]->Delay($dif);
		}
		foreach ($this->team1 as $key => $char)
		{
			$this->team1["$key"]->Delay($dif);
		}
		/*// エラーが出たらこれで。
		if(!is_object($NextChar)) {
		echo("AAA");
		dump($NextChar);
		echo("BBB");
		}
		*/

		return $NextChar;
	}

	//

	//	キャラ全員の行動ディレイを初期化(=SPD)
	function DelayResetAll()
	{

		if (DELAY_TYPE === 0 || DELAY_TYPE === 1)
		{
			foreach ($this->team0 as $key => $char)
			{
				$this->team0["$key"]->DelayReset();
			}
			foreach ($this->team1 as $key => $char)
			{
				$this->team1["$key"]->DelayReset();
			}
		}
	}

	//	ディレイを計算して設定する
	//	誰かのSPDが変化した場合呼び直す
	//	*** 技の使用等でSPDが変化した際に呼び出す ***
	function SetDelay()
	{
		if (DELAY_TYPE === 0)
		{
			//SPDの最大値と合計を求める
			foreach ($this->team0 as $key => $char)
			{
				$TotalSPD += $char->SPD;
				if ($MaxSPD < $char->SPD) $MaxSPD = $char->SPD;
			}
			//dump($this->team0);
			foreach ($this->team1 as $char)
			{
				$TotalSPD += $char->SPD;
				if ($MaxSPD < $char->SPD) $MaxSPD = $char->SPD;
			}
			//平均SPD
			$AverageSPD = $TotalSPD / (count($this->team0) + count($this->team1));
			//基準delayとか
			$AveDELAY = $AverageSPD * DELAY;
			$this->delay = $MaxSPD + $AveDELAY; //その戦闘の基準ディレイ
			$this->ChangeDelay = false; //falseにしないと毎回DELAYを計算し直してしまう。
		}
		else
			if (DELAY_TYPE === 1)
			{
			}
	}

	//	戦闘の基準ディレイを再計算させるようにする。
	//	使う場所は、技の使用でキャラのSPDが変化した際に使う。
	//	class.skill_effect.php で使用。
	function ChangeDelay()
	{
		if (DELAY_TYPE === 0)
		{
			$this->ChangeDelay = true;
		}
	}

	//	チームの名前を設定
	function SetTeamName($name1, $name2)
	{
		$this->team0_name = $name1;
		$this->team1_name = $name2;
	}


	//	お金を得る、一時的に変数に保存するだけ。
	//	class内にメソッド作れー
	function GetMoney($money, $team)
	{
		if (!$money) return false;
		$money = ceil($money * MONEY_RATE);
		if ($team === $this->team0)
		{
			echo("{$this->team0_name} Get " . HOF_Helper_Global::MoneyFormat($money) . ".<br />\n");
			$this->team0_money += $money;
		}
		else
			if ($team === $this->team1)
			{
				echo("{$this->team1_name} Get " . HOF_Helper_Global::MoneyFormat($money) . ".<br />\n");
				$this->team1_money += $money;
			}
	}

	//	ユーザーデータに得る合計金額を渡す
	function ReturnMoney()
	{
		return array($this->team0_money, $this->team1_money);
	}


	//	全体の死者数を数える...(ネクロマンサしか使ってない?)
	function CountDeadAll()
	{
		$dead = 0;
		foreach ($this->team0 as $char)
		{
			if ($char->STATE === STATE_DEAD) $dead++;
		}
		foreach ($this->team1 as $char)
		{
			if ($char->STATE === STATE_DEAD) $dead++;
		}
		return $dead;
	}


	//	指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	function CountDead($VarChar)
	{
		$dead = 0;

		if ($VarChar->team == TEAM_0)
		{
			//	echo("A".$VarChar->team."<br>");
			$Team = $this->team0;
		}
		else
		{
			//echo("B".$VarChar->team);
			$Team = $this->team1;
		}

		foreach ($Team as $char)
		{
			if ($char->STATE === STATE_DEAD)
			{
				$dead++;
			}
			else
				if ($char->SPECIAL["Undead"] == true)
				{
					//echo("C".$VarChar->Name()."/".count($Team)."<br>");
					$dead++;
				}
		}
		return $dead;
	}

	//	魔方陣を追加する
	function MagicCircleAdd($team, $amount)
	{
		if ($team == TEAM_0)
		{
			$this->team0_mc += $amount;
			if (5 < $this->team0_mc) $this->team0_mc = 5;
			return true;
		}
		else
		{
			$this->team1_mc += $amount;
			if (5 < $this->team1_mc) $this->team1_mc = 5;
			return true;
		}
	}

	//	魔方陣を削除する
	function MagicCircleDelete($team, $amount)
	{
		if ($team == TEAM_0)
		{
			if ($this->team0_mc < $amount) return false;
			$this->team0_mc -= $amount;
			return true;
		}
		else
		{
			if ($this->team1_mc < $amount) return false;
			$this->team1_mc -= $amount;
			return true;
		}
	}
	// end of class. ///
}


?>