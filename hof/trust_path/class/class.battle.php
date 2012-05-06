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
	//////////////////////////////////////////////////
	//	コンストラクタ。

	//各チームの配列を受けとる。
	function __construct($team0, $team1)
	{
		/*
		include (DATA_JUDGE);
		include_once (DATA_SKILL);

		//モンスターが参戦してなくても召喚される場合があるので
		include_once (CLASS_MONSTER);
		*/

		$this->team0 = $team0;
		$this->team1 = $team1;

		// 各チームに戦闘専用の変数を設定する(class.char.php)
		// 装備の特殊機能等を計算して設定する。
		// 戦闘専用の変数は大文字英語だったりする。class.char.phpを参照。
		//  $this->team["$key"] で渡すこと.(引数はチーム番号)
		foreach ($this->team0 as $key => $char) $this->team0["$key"]->SetBattleVariable(TEAM_0);
		foreach ($this->team1 as $key => $char) $this->team1["$key"]->SetBattleVariable(TEAM_1);
		//dump($this->team0[0]);
		// delay関連
		$this->SetDelay(); //ディレイ計算
		$this->DelayResetAll(); //初期化
	}
	//////////////////////////////////////////////////
	//
	function SetResultType($var)
	{
		$this->BattleResultType = $var;
	}
	//////////////////////////////////////////////////
	//	UnionBattleである事にする。
	function SetUnionBattle()
	{
		$this->UnionBattle = true;
	}
	//////////////////////////////////////////////////
	//	背景画像をセットする。
	function SetBackGround($bg)
	{
		$this->BackGround = $bg;
	}
	/*
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
	//	限界ターン数を決めちゃう。
	function LimitTurns($no)
	{
		$this->BattleMaxTurn = $no;
		$this->NoExtends = true; //これ以上延長はしない。
	}
	//////////////////////////////////////////////////
	//
	function NoResult()
	{
		$this->NoResult = true;
	}
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
	//	挑戦者側が勝利したか？
	function ReturnBattleResult()
	{
		return $this->result;
	}
	//////////////////////////////////////////////////
	//	戦闘記録を保存する
	function RecordLog($type = false)
	{
		if ($type == "RANK")
		{
			$file = LOG_BATTLE_RANK;
			$log = game_core::glob(LOG_BATTLE_RANK);
			$logAmount = MAX_BATTLE_LOG_RANK;
		}
		else
			if ($type == "UNION")
			{
				$file = LOG_BATTLE_UNION;
				$log = game_core::glob(LOG_BATTLE_UNION);
				$logAmount = MAX_BATTLE_LOG_UNION;
			}
			else
			{
				$file = LOG_BATTLE_NORMAL;
				$log = game_core::glob(LOG_BATTLE_NORMAL);
				$logAmount = MAX_BATTLE_LOG;
			}

			// 古いログを消す
			$i = 0;
		while ($logAmount <= count($log))
		{
			unlink($log["$i"]);
			unset($log["$i"]);
			$i++;
		}

		// 新しいログを作る
		$time = time() . substr(microtime(), 2, 6);
		$file .= $time . ".dat";

		$head = $time . "\n"; //開始時間(1行目)
		$head .= $this->team0_name . "<>" . $this->team1_name . "\n"; //参加チーム(2行目)
		$head .= count($this->team0) . "<>" . count($this->team1) . "\n"; //参加人数(3行目)
		$head .= $this->team0_ave_lv . "<>" . $this->team1_ave_lv . "\n"; //平均レベル(4行目)
		$head .= $this->result . "\n"; //勝利チーム(5行目)
		$head .= $this->actions . "\n"; //総ターン数(6行目)
		$head .= "\n"; // 改行(7行目)

		HOF_Class_File::WriteFile($file, $head . ob_get_contents());
	}
	//////////////////////////////////////////////////
	//	戦闘処理(これを実行して戦闘が処理される)
	function Process()
	{
		$this->objs['view']->BattleHeader();

		//戦闘が終わるまで繰り返す
		do
		{
			if ($this->actions % BATTLE_STAT_TURNS == 0) //一定間隔で状況を表示
 					$this->objs['view']->BattleState(); //状況の表示

			// 行動キャラ
			if (DELAY_TYPE === 0) $char = &$this->NextActer();
			else
				if (DELAY_TYPE === 1) $char = &$this->NextActerNew();

			$this->Action($char); //行動
			$result = $this->BattleResult(); //↑の行動で戦闘が終了したかどうかの判定

			//技の使用等でSPDが変化した場合DELAYを再計算する。
			if ($this->ChangeDelay) $this->SetDelay();

		} while (!$result);

		$this->objs['view']->ShowResult($result); //戦闘の結果表示
		$this->objs['view']->BattleFoot();

		//$this->SaveCharacters();
	}
	//////////////////////////////////////////////////
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

	//////////////////////////////////////////////////
	//	戦闘終了の判定
	//	全員死んでる=draw(?)
	function BattleResult()
	{
		if (HOF_Class_Battle_Team::CountAlive($this->team0) == 0) //全員しぼーなら負けにする。
 				$team0Lose = true;
		if (HOF_Class_Battle_Team::CountAlive($this->team1) == 0) //全員しぼーなら負けにする。
 				$team1Lose = true;

		//勝者のチーム番号か引き分けを返す
		if ($team0Lose && $team1Lose)
		{
			$this->result = DRAW;
			return "draw";
		}
		else
			if ($team0Lose)
			{ //team1 won
				$this->result = TEAM_1;
				return "team1";
			}
			else
				if ($team1Lose)
				{ // team0 won
					$this->result = TEAM_0;
					return "team0";

					// 両チーム生存していて最大行動数に達した時。
				}
				else
					if ($this->BattleMaxTurn <= $this->actions)
					{
						// 生存者数の差。
						/*
						// 生存者数の差が1人以上なら延長
						$AliveNumDiff	= abs(HOF_Class_Battle_Team::CountAlive($this->team0) - HOF_Class_Battle_Team::CountAlive($this->team1));
						if(0 < $AliveNumDiff && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS) {
						*/
						$AliveNumDiff = abs(HOF_Class_Battle_Team::CountAlive($this->team0) - HOF_Class_Battle_Team::CountAlive($this->team1));
						$Not5 = (HOF_Class_Battle_Team::CountAlive($this->team0) != 5 && HOF_Class_Battle_Team::CountAlive($this->team1) != 5);
						//$lessThan4	= ( HOF_Class_Battle_Team::CountAlive($this->team0) < 5 || HOF_Class_Battle_Team::CountAlive($this->team1) < 5 );
						//if( ( $lessThan4 || 0 < $AliveNumDiff ) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS ) {
						if (($Not5 || 0 < $AliveNumDiff) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS)
						{
							if ($this->ExtendTurns(TURN_EXTENDS, 1)) return false;
						}

						// 決着着かなければただ引き分けにする。
						if ($this->BattleResultType == 0)
						{
							$this->result = DRAW; //引き分け。
							return "draw";
							// 決着着かなければ生存者の数で勝敗をつける。
						}
						else
							if ($this->BattleResultType == 1)
							{
								// とりあえず引き分けに設定
								// (1) 生存者数が多いほうが勝ち
								// (2) (1) が同じなら総ダメージが多いほうが勝ち
								// (3) (2) でも同じなら引き分け…???(or防衛側の勝ち)

								$team0Alive = HOF_Class_Battle_Team::CountAliveChars($this->team0);
								$team1Alive = HOF_Class_Battle_Team::CountAliveChars($this->team1);
								if ($team1Alive < $team0Alive)
								{ // team0 won
									$this->result = TEAM_0;
									return "team0";
								}
								else
									if ($team0Alive < $team1Alive)
									{ // team1 won
										$this->result = TEAM_1;
										return "team1";
									}
									else
									{
										$this->result = DRAW;
										return "draw";
									}
							}
							else
							{
								$this->result = DRAW;
								echo("error321708.<br />おかしいので報告してください。");
								return "draw"; // エラー回避。
							}

							$this->result = DRAW;
						echo("error321709.<br />おかしいので報告してください。");
						return "draw"; // エラー回避。
					}
	}
	//////////////////////////////////////////////////
	//

	//////////////////////////////////////////////////
	//	キャラの行動
	function Action(&$char)
	{
		// $char->judge が設定されてなければ飛ばす
		if ($char->judge === array())
		{
			$char->delay = $char->SPD;
			return false;
		}

		// チーム0の人はセルの右側に
		// チーム1の人は左側に 行動内容と結果 を表示する
		echo("<tr><td class=\"ttd2\">\n");
		if ($char->team === TEAM_0) echo("</td><td class=\"ttd1\">\n");
		// 自分のチームはどちらか?
		foreach ($this->team0 as $val)
		{
			if ($val === $char)
			{
				$MyTeam = &$this->team0;
				$EnemyTeam = &$this->team1;
				break;
			}
		}
		//チーム0でないならチーム1
		if (empty($MyTeam))
		{
			$MyTeam = &$this->team1;
			$EnemyTeam = &$this->team0;
		}

		//行動の判定(使用する技の判定)
		if ($char->expect)
		{ // 詠唱,貯め 完了
			$skill = $char->expect;
			$return = &$char->target_expect;
		}
		else
		{ //待機→判定→スキル
			$JudgeKey = -1;

			// 持続回復系
			$char->AutoRegeneration();
			// 毒状態ならダメージを受ける。
			$char->PoisonDamage();

			//判定
			do
			{
				$Keys = array(); //空配列(初期化)
				do
				{
					$JudgeKey++;
					$Keys[] = $JudgeKey;
					// 重複判定なら次も加える
				} while ($char->action["$JudgeKey"] == 9000 && $char->judge["$JudgeKey"]);

				//$return	= HOF_Class_Battle_Judge::MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam);
				$return = HOF_Class_Battle_Judge::MultiFactJudge($Keys, $char, $this);

				if ($return)
				{
					$skill = $char->action["$JudgeKey"];
					foreach ($Keys as $no) $char->JdgCount[$no]++; //決定した判断のカウントうｐ
					break;
				}
			} while ($char->judge["$JudgeKey"]);

			/* // (2007/10/15)
			foreach($char->judge as $key => $judge){
			// $return は true,false,配列のいづれか
			// 配列の場合は判定の条件に一致したキャラが返る(ハズ)。
			$return	=& HOF_Class_Battle_Judge::DecideJudge($judge,$char,$MyTeam,$EnemyTeam,$key);
			if($return) {
			$skill	= $char->action["$key"];
			$char->JdgCount[$key]++;//決定した判断のカウントうｐ
			break;
			}
			}
			*/
		}

		// 戦闘の総行動回数を増やす。
		$this->actions++;

		if ($skill)
		{
			$this->UseSkill($skill, $return, $char, $MyTeam, $EnemyTeam);
			// 行動できなかった場合の処理
		}
		else
		{
			echo($char->Name(bold) . " sunk in thought and couldn't act.<br />(No more patterns)<br />\n");
			$char->DelayReset();
		}

		//ディレイリセット
		//if($ret	!== "DontResetDelay")
		//	$char->DelayReset;

		//echo $char->name." ".$skill."<br>";//確認用
		//セルの終わり
		if ($char->team === TEAM_1) echo("</td><td class=\"ttd1\">&nbsp;\n");
		echo("</td></tr>\n");
	}
	//////////////////////////////////////////////////
	//	総ダメージを加算する
	function AddTotalDamage($team, $dmg)
	{
		if (!is_numeric($dmg)) return false;
		if ($team == $this->team0) $this->team0_dmg += $dmg;
		else
			if ($team == $this->team1) $this->team1_dmg += $dmg;
	}

	//////////////////////////////////////////////////
	//
	function UseSkill($skill_no, &$JudgedTarget, &$My, &$MyTeam, &$Enemy)
	{
		$skill = HOF_Model_Data::getSkill($skill_no); //技データ読む

		// 武器タイプ不一致
		if ($skill["limit"] && !$My->monster)
		{
			if (!$skill["limit"][$My->WEAPON])
			{
				echo('<span class="u">' . $My->Name(bold));
				echo('<span class="dmg"> Failed </span>to ');
				echo("<img src=\"" . HOF_Class_Icon::getImageUrl($skill["img"], IMG_ICON. 'skill/') . "\" class=\"vcent\"/>");
				echo($skill[name] . "</span><br />\n");
				//echo($My->Name(bold)." Failed to use ".$skill["name"]."<br />\n");
				echo("(Weapon type doesnt match)<br />\n");
				$My->DelayReset(); // 行動順をリセット
				return true;
			}
		}

		// SP不足
		if ($My->SP < $skill["sp"])
		{
			echo($My->Name(bold) . " failed to " . $skill["name"] . "(SP shortage)");
			if ($My->expect)
			{ //もし詠唱や貯め途中でSPが不足した場合
				$My->ResetExpect();
			}
			$My->DelayReset(); // 行動順をリセット
			return true;
		}

		// もし "詠唱" や "貯め" が必要な技なら(+詠唱開始してない場合)→詠唱,貯め開始
		if ($skill["charge"]["0"] && $My->expect === false)
		{
			// こちらは貯めと詠唱を開始する場合 /////////////////////
			// 物理か魔法によって文を変える
			if ($skill["type"] == 0)
			{ //物理
				echo('<span class="charge">' . $My->Name(bold) . ' start charging.</span>');
				$My->expect_type = CHARGE;
			}
			else
			{ //魔法
				echo('<span class="charge">' . $My->Name(bold) . ' start casting.</span>');
				$My->expect_type = CAST;
			}
			$My->expect = $skill_no; //詠唱・貯め完了と同時に使用する技
			// ↓使ってないのでコメントにした。
			//$My->target_expect	= $JudgedTarget;//一応ターゲットも保存
			//詠唱・貯め時間の設定。
			$My->DelayByRate($skill["charge"]["0"], $this->delay, 1);
			echo("<br />\n");

			// 戦闘の総行動回数を減らす(貯めor詠唱 は行動に入れない)
			$this->actions--;

			return true; //ディレイ変更したからリセットしないように。
		}
		else
		{
			// 技を実際に使用する ///////////////////////////////////

			// 行動回数をプラスする
			$My->ActCount++;

			// 行動内容の表示(行動する)
			echo('<div class="u">' . $My->Name(bold));
			echo("<img src=\"" . HOF_Class_Icon::getImageUrl($skill["img"], IMG_ICON. 'skill/') . "\" class=\"vcent\"/>");
			echo($skill[name] . "</div>\n");

			// 魔法陣を消費(味方)
			if ($skill["MagicCircleDeleteTeam"])
			{
				if ($this->MagicCircleDelete($My->team, $skill["MagicCircleDeleteTeam"]))
				{
					echo($My->Name(bold) . '<span class="charge"> use MagicCircle x' . $skill["MagicCircleDeleteTeam"] . '</span><br />' . "\n");
					// 魔法陣消費失敗
				}
				else
				{
					echo('<span class="dmg">failed!(MagicCircle isn\'t enough)</span><br />' . "\n");
					$My->DelayReset(); // 行動順をリセット
					return true;
				}
			}

			// SPの消費(この位置だと貯め・詠唱完了と同時に消費する)
			$My->SpDamage($skill["sp"], false);

			// チャージ(詠唱)完了と同時に使用する技の情報を消す。
			if ($My->expect) $My->ResetExpect();

			// HP犠牲技の場合(Sacrifice)
			if ($skill["sacrifice"]) $My->SacrificeHp($skill["sacrifice"]);

		}

		// ターゲットを選ぶ(候補)
		if ($skill["target"]["0"] == "friend"):
			$candidate = &$MyTeam;
		elseif ($skill["target"]["0"] == "enemy"):
			$candidate = &$Enemy;
		elseif ($skill["target"]["0"] == "self"):
			$candidate[] = &$My;
		elseif ($skill["target"]["0"] == "all"):
			//$candidate	= $MyTeam + $Enemy;//???
			$candidate = array_merge_recursive(&$MyTeam, &$Enemy); //結合の後,並びをランダムにした方がいい??
		endif;

		// 候補から使用する対象を選ぶ → (スキル使用)

		// 単体に使用
		if ($skill["target"]["1"] == "individual")
		{
			$target = &$this->SelectTarget($candidate, $skill); //対象を選択
			if ($defender = &$this->Defending($target, $candidate, $skill)) //守りに入るキャラ
 					$target = &$defender;
			for ($i = 0; $i < $skill["target"]["2"]; $i++)
			{ //単体に複数回実行
				$dmg = $this->SkillEffect($skill, $skill_no, $My, $target);
				$this->AddTotalDamage($MyTeam, $dmg);
			}

			// 複数に使用
		}
		else
			if ($skill["target"]["1"] == "multi")
			{
				for ($i = 0; $i < $skill["target"]["2"]; $i++)
				{
					$target = &$this->SelectTarget($candidate, $skill); //対象を選択
					if ($defender = &$this->Defending($target, $candidate, $skill)) //守りに入るキャラ
 							$target = &$defender;
					$dmg = $this->SkillEffect($skill, $skill_no, $My, $target);
					$this->AddTotalDamage($MyTeam, $dmg);
				}

				// 全体に使用
			}
			else
				if ($skill["target"]["1"] == "all")
				{
					foreach ($candidate as $key => $char)
					{
						$target = &$candidate[$key];
						//if($char->STATE === DEAD) continue;//死亡者はパス。
						if ($skill["priority"] != "Dead")
						{ //一時的に。
							if ($char->STATE === DEAD) continue; //死亡者はパス。
						}
						// 全体攻撃は守りに入れない(とする)
						for ($i = 0; $i < $skill["target"]["2"]; $i++)
						{
							$dmg = $this->SkillEffect($skill, $skill_no, $My, $target);
							$this->AddTotalDamage($MyTeam, $dmg);
						}
					}
				}

		// 使用後使用者に影響する効果等
		if ($skill["umove"]) $My->Move($skill["umove"]);

		// 攻撃対象になったキャラ達がどうなったか確かめる(とりあえずHP=0になったかどうか)。
		if ($skill["sacrifice"])
		{ // Sacri系の技を使った場合。
			$Sacrier[] = &$My;
			$this->JudgeTargetsDead($Sacrier);
		}
		list($exp, $money, $itemdrop) = $this->JudgeTargetsDead($candidate); //又、取得する経験値を得る

		$this->GetExp($exp, $MyTeam);
		$this->GetItem($itemdrop, $MyTeam);
		$this->GetMoney($money, $MyTeam);

		// 技の使用等でSPDが変化した場合DELAYを再計算する。
		if ($this->ChangeDelay) $this->SetDelay();

		// 行動後の硬直(があれば設定する)
		if ($skill["charge"]["1"])
		{
			$My->DelayReset();
			echo($My->Name(bold) . " Delayed");
			$My->DelayByRate($skill["charge"]["1"], $this->delay, 1);
			echo("<br />\n");
			return false;
		}

		// 最後に行動順をリセットする。
		$My->DelayReset();
	}
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
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

	//////////////////////////////////////////////////
	//	後衛を守りに入るキャラを選ぶ。
	function &Defending(&$target, &$candidate, $skill)
	{
		if ($target === false) return false;

		if ($skill["invalid"]) //防御無視できる技。
 				return false;
		if ($skill["support"]) //支援なのでガードしない。
 				return false;
		if ($target->POSITION == "front") //前衛なら守る必要無し。終わる
 				return false;
		// "前衛で尚且つ生存者"を配列に詰める↓
		// 前衛 + 生存者 + HP1以上 に変更 ( 多段系攻撃で死にながら守るので [2007/9/20] )
		foreach ($candidate as $key => $char)
		{
			//echo("{$char->POSTION}:{$char->STATE}<br>");
			if ($char->POSITION == "front" && $char->STATE !== 1 && 1 < $char->HP) $fore[] = &$candidate["$key"];
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
	//////////////////////////////////////////////////
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
					echo("<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\" class=\"vcent\"/>\n");
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
	//////////////////////////////////////////////////
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
				if ($char->STATE == DEAD) continue; //しぼー者は対象にならない。
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
					if ($char->STATE == DEAD) continue; //しぼー者は対象にならない。
					if ($char->POSITION != FRONT) //後衛なら
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
						if ($char->STATE == DEAD) //しぼーなら
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
			if ($char->STATE != DEAD) //しぼー以外なら
 					$target[] = &$target_list[$key]; //しぼー者リスト
		}
		return $target[array_rand($target)]; //ランダムに誰か一人
	}
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
	//	次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
	//	リファレンスを返す
	function &NextActerNew()
	{

		// 次の行動まで最も距離が短い人を探す。
		$nextDis = 1000;
		foreach ($this->team0 as $key => $char)
		{
			if ($char->STATE === DEAD) continue;
			$charDis = $this->team0[$key]->nextDis();
			if ($charDis == $nextDis)
			{
				$NextChar[] = &$this->team0["$key"];
			}
			else
				if ($charDis <= $nextDis)
				{
					$nextDis = $charDis;
					$NextChar = array(&$this->team0["$key"]);
				}
		}

		// ↑と同じ。
		foreach ($this->team1 as $key => $char)
		{
			if ($char->STATE === DEAD) continue;
			$charDis = $this->team1[$key]->nextDis();
			if ($charDis == $nextDis)
			{
				$NextChar[] = &$this->team1["$key"];
			}
			else
				if ($charDis <= $nextDis)
				{
					$nextDis = $charDis;
					$NextChar = array(&$this->team1["$key"]);
				}
		}

		// 全員ディレイ減少 //////////////////////

		//もしも差分が0以下になったら
		if ($nextDis < 0)
		{
			if (is_array($NextChar))
			{
				return $NextChar[array_rand($NextChar)];
			}
			else  return $NextChar;
		}

		foreach ($this->team0 as $key => $char)
		{
			$this->team0["$key"]->Delay($nextDis);
		}
		foreach ($this->team1 as $key => $char)
		{
			$this->team1["$key"]->Delay($nextDis);
		}
		// エラーが出たらこれでたしかめろ。
		/*
		if(!is_object($NextChar)) {
		echo("AAA");
		dump($NextChar);
		echo("BBB");
		}
		*/

		if (is_array($NextChar)) return $NextChar[array_rand($NextChar)];
		else  return $NextChar;
	}
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
	//	チームの名前を設定
	function SetTeamName($name1, $name2)
	{
		$this->team0_name = $name1;
		$this->team1_name = $name2;
	}
	//////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////
	//

	/*
	//////////////////////////////////////////////////
	//	戦闘画像(画像のみ)
	function ShowGdImage()
	{
	$url = BTL_IMG . "?";

	// HP=0 のキャラの画像(拡張子があればそれを取る)
	$DeadImg = substr(DEAD_IMG, 0, strpos(DEAD_IMG, "."));

	//チーム1
	$f = 1;
	$b = 1; //前衛の数・後衛の数を初期化
	foreach ($this->team0 as $char)
	{
	//画像はキャラに設定されている画像の拡張子までの名前
	if ($char->STATE === 1) $img = $DeadImg;
	else  $img = substr($char->img, 0, strpos($char->img, "."));
	if ($char->POSITION == "front"): //前衛
	$url .= "f2{$f}=$img&";
	$f++;
	else:
	$url .= "b2{$b}=$img&"; //後衛
	$b++;
	endif;
	}
	//チーム0
	$f = 1;
	$b = 1;
	foreach ($this->team1 as $char)
	{
	if ($char->STATE === 1) $img = $DeadImg;
	else  $img = substr($char->img, 0, strpos($char->img, "."));
	if ($char->POSITION == "front"):
	$url .= "f1{$f}=$img&";
	$f++;
	else:
	$url .= "b1{$b}=$img&";
	$b++;
	endif;
	}
	echo('<img src="' . $url . '">'); // ←これが表示されるのみ
	}
	//////////////////////////////////////////////////
	//	CSS戦闘画面
	function ShowCssImage()
	{
	$img = new HOF_Class_Battle_Style();
	$img->SetBackGround($this->BackGround);
	$img->SetTeams($this->team1, $this->team0);
	$img->SetMagicCircle($this->team1_mc, $this->team0_mc);
	if (BTL_IMG_TYPE == 2) $img->NoFlip(); // CSS画像反転無し
	$img->Show();
	}
	*/

	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
	//	ユーザーデータに得る合計金額を渡す
	function ReturnMoney()
	{
		return array($this->team0_money, $this->team1_money);
	}

	//////////////////////////////////////////////////
	//	全体の死者数を数える...(ネクロマンサしか使ってない?)
	function CountDeadAll()
	{
		$dead = 0;
		foreach ($this->team0 as $char)
		{
			if ($char->STATE === DEAD) $dead++;
		}
		foreach ($this->team1 as $char)
		{
			if ($char->STATE === DEAD) $dead++;
		}
		return $dead;
	}

	//////////////////////////////////////////////////
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
			if ($char->STATE === DEAD)
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
	//////////////////////////////////////////////////
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
	//////////////////////////////////////////////////
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
	// end of class. /////////////////////////////////////////////////////
}


?>