<?
include(CLASS_SKILL_EFFECT);
class battle extends ClassSkillEffect{
/*
 * $battle	= new battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
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

	// 魔方陣
	var $team0_mc = 0;
	var $team1_mc = 0;

	// 戦闘の最大ターン数(延長される可能性のある)
	var $BattleMaxTurn	= BATTLE_MAX_TURNS;
	var $NoExtends	= false;

	//
	var $NoResult	= false;

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
	var $team0_item=array(), $team1_item=array();
	var $team0_exp=0, $team1_exp=0;// 総経験値。

	// 特殊な変数
	var $ChangeDelay	= false;//キャラのSPDが変化した際にDELAYを再計算する。

	var $BattleResultType	= 0;// 0=決着着かなければDraw 1=生存者の数で勝敗を決める
	var $UnionBattle;// 残りHP総HPを隠す(????/????)
//////////////////////////////////////////////////
//	コンストラクタ。

	//各チームの配列を受けとる。
	function battle($team0,$team1) {
		include(DATA_JUDGE);
		include_once(DATA_SKILL);

		//モンスターが参戦してなくても召喚される場合があるので
		include_once(CLASS_MONSTER);

		$this->team0	= $team0;
		$this->team1	= $team1;

		// 各チームに戦闘専用の変数を設定する(class.char.php)
		// 装備の特殊機能等を計算して設定する。
		// 戦闘専用の変数は大文字英語だったりする。class.char.phpを参照。
		//  $this->team["$key"] で渡すこと.(引数はチーム番号)
		foreach($this->team0 as $key => $char)
			$this->team0["$key"]->SetBattleVariable(TEAM_0);
		foreach($this->team1 as $key => $char)
			$this->team1["$key"]->SetBattleVariable(TEAM_1);
		//dump($this->team0[0]);
		// delay関連
		$this->SetDelay();//ディレイ計算
		$this->DelayResetAll();//初期化
	}
//////////////////////////////////////////////////
//	
	function SetResultType($var) {
		$this->BattleResultType	= $var;
	}
//////////////////////////////////////////////////
//	UnionBattleである事にする。
	function SetUnionBattle() {
		$this->UnionBattle	= true;
	}
//////////////////////////////////////////////////
//	背景画像をセットする。
	function SetBackGround($bg) {
		$this->BackGround	= $bg;
	}
//////////////////////////////////////////////////
//	戦闘にキャラクターを途中参加させる。
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
//	限界ターン数を決めちゃう。
	function LimitTurns($no) {
		$this->BattleMaxTurn	= $no;
		$this->NoExtends		= true;//これ以上延長はしない。
	}
//////////////////////////////////////////////////
//	
	function NoResult() {
		$this->NoResult	= true;
	}
//////////////////////////////////////////////////
//	戦闘の最大ターン数を増やす。
	function ExtendTurns($no,$notice=false) {
		// 延長しない変数が設定されていれば延長しない。
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
//	戦闘中獲得したアイテムを返す。
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
//	挑戦者側が勝利したか？
	function ReturnBattleResult() {
		return $this->result;
	}
//////////////////////////////////////////////////
//	戦闘記録を保存する
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

		// 古いログを消す
		$i	= 0;
		while($logAmount <= count($log) ) {
			unlink($log["$i"]);
			unset($log["$i"]);
			$i++;
		}

		// 新しいログを作る
		$time	= time().substr(microtime(),2,6);
		$file	.= $time.".dat";

		$head	= $time."\n";//開始時間(1行目)
		$head	.= $this->team0_name."<>".$this->team1_name."\n";//参加チーム(2行目)
		$head	.= count($this->team0)."<>".count($this->team1)."\n";//参加人数(3行目)
		$head	.= $this->team0_ave_lv."<>".$this->team1_ave_lv."\n";//平均レベル(4行目)
		$head	.= $this->result."\n";//勝利チーム(5行目)
		$head	.= $this->actions."\n";//総ターン数(6行目)
		$head	.= "\n";// 改行(7行目)

		WriteFile($file,$head.ob_get_contents());
	}
//////////////////////////////////////////////////
//	戦闘処理(これを実行して戦闘が処理される)
	function Process() {
		$this->BattleHeader();

		//戦闘が終わるまで繰り返す
		do {
			if($this->actions % BATTLE_STAT_TURNS == 0)//一定間隔で状況を表示
				$this->BattleState();//状況の表示

			// 行動キャラ
			if(DELAY_TYPE === 0)
				$char	= &$this->NextActer();
			else if(DELAY_TYPE === 1)
				$char	= &$this->NextActerNew();

			$this->Action($char);//行動
			$result	= $this->BattleResult();//↑の行動で戦闘が終了したかどうかの判定

			//技の使用等でSPDが変化した場合DELAYを再計算する。
			if($this->ChangeDelay)
				$this->SetDelay();

		} while(!$result);

		$this->ShowResult($result);//戦闘の結果表示
		$this->BattleFoot();

		//$this->SaveCharacters();
	}
//////////////////////////////////////////////////
//	戦闘後のキャラクター状況を保存する。
	function SaveCharacters() {
		//チーム0
		foreach($this->team0 as $char) {
			$char->SaveCharData();
		}
		//チーム1
		foreach($this->team1 as $char) {
			$char->SaveCharData();
		}
	}

//////////////////////////////////////////////////
//	戦闘終了の判定
//	全員死んでる=draw(?)
	function BattleResult() {
		if(CountAlive($this->team0) == 0)//全員しぼーなら負けにする。
			$team0Lose	= true;
		if(CountAlive($this->team1) == 0)//全員しぼーなら負けにする。
			$team1Lose	= true;
		//勝者のチーム番号か引き分けを返す
		if( $team0Lose && $team1Lose ) {
			$this->result	= DRAW;
			return "draw";
		} else if($team0Lose) {//team1 won
			$this->result	= TEAM_1;
			return "team1";
		} else if($team1Lose) {// team0 won
			$this->result	= TEAM_0;
			return "team0";

		// 両チーム生存していて最大行動数に達した時。
		} else if($this->BattleMaxTurn <= $this->actions) {
			// 生存者数の差。
			/*
				// 生存者数の差が1人以上なら延長
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

			// 決着着かなければただ引き分けにする。
			if($this->BattleResultType == 0) {
				$this->result	= DRAW;//引き分け。
				return "draw";
			// 決着着かなければ生存者の数で勝敗をつける。
			} else if($this->BattleResultType == 1) {
				// とりあえず引き分けに設定
				// (1) 生存者数が多いほうが勝ち
				// (2) (1) が同じなら総ダメージが多いほうが勝ち
				// (3) (2) でも同じなら引き分け…???(or防衛側の勝ち)
	
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
				print("error321708.<br />おかしいので報告してください。");
				return "draw";// エラー回避。
			}

			$this->result	= DRAW;
			print("error321709.<br />おかしいので報告してください。");
			return "draw";// エラー回避。
		}
	}
//////////////////////////////////////////////////
//	戦闘の結果表示
	function ShowResult($result) {

		// 左側のチーム(戦闘を受けた側)
		$TotalAlive2	= 0;
		// 残りHP / 合計HP の 表示
		foreach($this->team1 as $char) {//チーム1
			if($char->STATE !== DEAD)
				$TotalAlive2++;
			$TotalHp2	+= $char->HP;//合計HP
			$TotalMaxHp2	+= $char->MAXHP;//合計最大HP
		}

		// 右側のチーム(戦闘を仕掛けた側)
		$TotalAlive1	= 0;
		foreach($this->team0 as $char) {//チーム0
			if($char->STATE !== DEAD)
				$TotalAlive1++;
			$TotalHp1	+= $char->HP;//合計HP
			$TotalMaxHp1	+= $char->MAXHP;//合計最大HP
		}

		// 結果を表示しない。
		if($this->NoResult) {
			print('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break break-top">');
			//print("<a name=\"s{$this->Scroll}\"></a>");// スクロールの最後
			print("模擬戦終了");
			print("</td></tr>\n");
			print('<tr><td class="teams break">'."\n");
			// 左側チーム
			print("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
			print("Alive : {$TotalAlive2}/".count($this->team1)."<br />\n");
			print("TotalDamage : {$this->team1_dmg}<br />\n");
			// 右側チーム
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
		print("<a name=\"s{$this->Scroll}\"></a>\n");// スクロールの最後
		if($result == "draw") {
			print("<span style=\"font-size:150%\">Draw Game</span><br />\n");
		} else {
			$Team	= &$this->{$result};
			$TeamName	= $this->{$result."_name"};
			print("<span style=\"font-size:200%\">{$TeamName} Wins!</span><br />\n");
		}

		print('<tr><td class="teams">'."\n");
		// Unionとそうでないのでわける
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
		// 左側チーム
		print("Alive : {$TotalAlive2}/".count($this->team1)."<br />\n");
		print("TotalDamage : {$this->team1_dmg}<br />\n");
		if($this->team1_exp)//得た経験値
			print("TotalExp : ".$this->team1_exp."<br />\n");
		if($this->team1_money)//得たお金
			print("Funds : ".MoneyFormat($this->team1_money)."<br />\n");
		if($this->team1_item) {//得たアイテム
			print("<div class=\"bold\">Items</div>\n");
			foreach($this->team0_item as $itemno => $amount) {
				$item	= LoadItemData($itemno);
				print("<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\">");
				print("{$item[name]} x {$amount}<br />\n");
			}
		}

		// 右側チーム
		print('</td><td class="teams">');
		print("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
		print("Alive : {$TotalAlive1}/".count($this->team0)."<br />\n");
		print("TotalDamage : {$this->team0_dmg}<br />\n");
		if($this->team0_exp)//得た経験値
			print("TotalExp : ".$this->team0_exp."<br />\n");
		if($this->team0_money)//得たお金
			print("Funds : ".MoneyFormat($this->team0_money)."<br />\n");
		if($this->team0_item) {//得たアイテム
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
//	キャラの行動
	function Action(&$char) {
		// $char->judge が設定されてなければ飛ばす
		if($char->judge === array()) {
			$char->delay	= $char->SPD;
			return false;
		}

		// チーム0の人はセルの右側に
		// チーム1の人は左側に 行動内容と結果 を表示する
		print("<tr><td class=\"ttd2\">\n");
		if($char->team === TEAM_0)
			print("</td><td class=\"ttd1\">\n");
		// 自分のチームはどちらか?
		foreach($this->team0 as $val) {
			if($val === $char) {
				$MyTeam	= &$this->team0;
				$EnemyTeam	= &$this->team1;
				break;
			}
		}
		//チーム0でないならチーム1
		if(!$MyTeam) {
			$MyTeam	= &$this->team1;
			$EnemyTeam	= &$this->team0;
		}

		//行動の判定(使用する技の判定)
		if($char->expect) {// 詠唱,貯め 完了
			$skill	= $char->expect;
			$return	= &$char->target_expect;
		} else {//待機→判定→スキル
			$JudgeKey	= -1;

			// 持続回復系
			$char->AutoRegeneration();
			// 毒状態ならダメージを受ける。
			$char->PoisonDamage();

			//判定
			do {
				$Keys	= array();//空配列(初期化)
				do {
					$JudgeKey++;
					$Keys[]	= $JudgeKey;
				// 重複判定なら次も加える
				} while($char->action["$JudgeKey"] == 9000 && $char->judge["$JudgeKey"]);

				//$return	= MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam);
				$return	= MultiFactJudge($Keys,$char,$this);

				if($return) {
					$skill	= $char->action["$JudgeKey"];
					foreach($Keys as $no)
						$char->JdgCount[$no]++;//決定した判断のカウントうｐ
					break;
				}
			} while($char->judge["$JudgeKey"]);

			/* // (2007/10/15)
			foreach($char->judge as $key => $judge){
				// $return は true,false,配列のいづれか
				// 配列の場合は判定の条件に一致したキャラが返る(ハズ)。
				$return	=& DecideJudge($judge,$char,$MyTeam,$EnemyTeam,$key);
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

		if($skill) {
			$this->UseSkill($skill,$return,$char,$MyTeam,$EnemyTeam);
		// 行動できなかった場合の処理
		} else {
			print($char->Name(bold)." sunk in thought and couldn't act.<br />(No more patterns)<br />\n");
			$char->DelayReset();
		}

		//ディレイリセット
		//if($ret	!== "DontResetDelay")
		//	$char->DelayReset;

		//echo $char->name." ".$skill."<br>";//確認用
		//セルの終わり
		if($char->team === TEAM_1)
			print("</td><td class=\"ttd1\">&nbsp;\n");
		print("</td></tr>\n");
	}
//////////////////////////////////////////////////
//	総ダメージを加算する
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
		$skill	= LoadSkillData($skill_no);//技データ読む

		// 武器タイプ不一致
		if($skill["limit"] && !$My->monster) {
			if(!$skill["limit"][$My->WEAPON]) {
				print('<span class="u">'.$My->Name(bold));
				print('<span class="dmg"> Failed </span>to ');
				print("<img src=\"".IMG_ICON.$skill["img"]."\" class=\"vcent\"/>");
				print($skill[name]."</span><br />\n");
				//print($My->Name(bold)." Failed to use ".$skill["name"]."<br />\n");
				print("(Weapon type doesnt match)<br />\n");
				$My->DelayReset();// 行動順をリセット
				return true;
			}
		}

		// SP不足
		if($My->SP < $skill["sp"]) {
			print($My->Name(bold)." failed to ".$skill["name"]."(SP shortage)");
			if($My->expect) {//もし詠唱や貯め途中でSPが不足した場合
				$My->ResetExpect();
			}
			$My->DelayReset();// 行動順をリセット
			return true;
		}

		// もし "詠唱" や "貯め" が必要な技なら(+詠唱開始してない場合)→詠唱,貯め開始
		if($skill["charge"]["0"] && $My->expect === false) {
			// こちらは貯めと詠唱を開始する場合 /////////////////////
			// 物理か魔法によって文を変える
			if($skill["type"] == 0) {//物理
				print('<span class="charge">'.$My->Name(bold).' start charging.</span>');
				$My->expect_type	= CHARGE;
			} else {//魔法
				print('<span class="charge">'.$My->Name(bold).' start casting.</span>');
				$My->expect_type	= CAST;
			}
			$My->expect	= $skill_no;//詠唱・貯め完了と同時に使用する技
			// ↓使ってないのでコメントにした。
			//$My->target_expect	= $JudgedTarget;//一応ターゲットも保存
			//詠唱・貯め時間の設定。
			$My->DelayByRate($skill["charge"]["0"],$this->delay,1);
			print("<br />\n");

			// 戦闘の総行動回数を減らす(貯めor詠唱 は行動に入れない)
			$this->actions--;

			return true;//ディレイ変更したからリセットしないように。
		} else {
			// 技を実際に使用する ///////////////////////////////////

			// 行動回数をプラスする
			$My->ActCount++;

			// 行動内容の表示(行動する)
			print('<div class="u">'.$My->Name(bold));
			print("<img src=\"".IMG_ICON.$skill["img"]."\" class=\"vcent\"/>");
			print($skill[name]."</div>\n");

			// 魔法陣を消費(味方)
			if($skill["MagicCircleDeleteTeam"])
			{
				if($this->MagicCircleDelete($My->team,$skill["MagicCircleDeleteTeam"])) {
					print($My->Name(bold).'<span class="charge"> use MagicCircle x'.$skill["MagicCircleDeleteTeam"].'</span><br />'."\n");
				// 魔法陣消費失敗
				} else {
					print('<span class="dmg">failed!(MagicCircle isn\'t enough)</span><br />'."\n");
					$My->DelayReset();// 行動順をリセット
					return true;
				}
			}

			// SPの消費(この位置だと貯め・詠唱完了と同時に消費する)
			$My->SpDamage($skill["sp"],false);

			// チャージ(詠唱)完了と同時に使用する技の情報を消す。
			if($My->expect)
				$My->ResetExpect();

			// HP犠牲技の場合(Sacrifice)
			if($skill["sacrifice"])
				$My->SacrificeHp($skill["sacrifice"]);

		}

		// ターゲットを選ぶ(候補)
		if($skill["target"]["0"] == "friend"):
			$candidate	= &$MyTeam;
		elseif($skill["target"]["0"] == "enemy"):
			$candidate	= &$Enemy;
		elseif($skill["target"]["0"] == "self"):
			$candidate[]	= &$My;
		elseif($skill["target"]["0"] == "all"):
			//$candidate	= $MyTeam + $Enemy;//???
			$candidate	= array_merge_recursive(&$MyTeam,&$Enemy);//結合の後,並びをランダムにした方がいい??
		endif;

		// 候補から使用する対象を選ぶ → (スキル使用)

		// 単体に使用
		if($skill["target"]["1"] == "individual") {
			$target	=& $this->SelectTarget($candidate,$skill);//対象を選択
			if($defender =& $this->Defending($target,$candidate,$skill) )//守りに入るキャラ
				$target	= &$defender;
			for($i=0; $i<$skill["target"]["2"]; $i++) {//単体に複数回実行
				$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
				$this->AddTotalDamage($MyTeam,$dmg);
			}

		// 複数に使用
		} else if($skill["target"]["1"] == "multi") {
			for($i=0; $i<$skill["target"]["2"]; $i++) {
				$target	=& $this->SelectTarget($candidate,$skill);//対象を選択
				if($defender =& $this->Defending($target,$candidate,$skill) )//守りに入るキャラ
					$target	= &$defender;
				$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
				$this->AddTotalDamage($MyTeam,$dmg);
			}

		// 全体に使用
		} else if($skill["target"]["1"] == "all") {
			foreach($candidate as $key => $char) {
				$target	= &$candidate[$key];
				//if($char->STATE === DEAD) continue;//死亡者はパス。
				if($skill["priority"] != "Dead") {//一時的に。
					if($char->STATE === DEAD) continue;//死亡者はパス。
				}
				// 全体攻撃は守りに入れない(とする)
				for($i=0; $i<$skill["target"]["2"]; $i++) {
					$dmg	= $this->SkillEffect($skill,$skill_no,$My,$target);
					$this->AddTotalDamage($MyTeam,$dmg);
				}
			}
		}

		// 使用後使用者に影響する効果等
		if($skill["umove"])
			$My->Move($skill["umove"]);

		// 攻撃対象になったキャラ達がどうなったか確かめる(とりあえずHP=0になったかどうか)。
		if($skill["sacrifice"]) { // Sacri系の技を使った場合。
			$Sacrier[]	= &$My;
			$this->JudgeTargetsDead($Sacrier);
		}
		list($exp,$money,$itemdrop)	= $this->JudgeTargetsDead($candidate);//又、取得する経験値を得る

		$this->GetExp($exp,$MyTeam);
		$this->GetItem($itemdrop,$MyTeam);
		$this->GetMoney($money,$MyTeam);

		// 技の使用等でSPDが変化した場合DELAYを再計算する。
		if($this->ChangeDelay)
			$this->SetDelay();

		// 行動後の硬直(があれば設定する)
		if($skill["charge"]["1"]) {
			$My->DelayReset();
			print($My->Name(bold)." Delayed");
			$My->DelayByRate($skill["charge"]["1"],$this->delay,1);
			print("<br />\n");
			return false;
		}

		// 最後に行動順をリセットする。
		$My->DelayReset();
	}
//////////////////////////////////////////////////
//	経験値を得る
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
	$ExpGet	= ceil($exp/$Alive);//生存者にだけ経験値を分ける。
	print("Alives get {$ExpGet}exps.<br />\n");
	foreach($team as $key => $char) {
		if($char->STATE === 1) continue;//死亡者にはEXPあげない
		if($team[$key]->GetExp($ExpGet))//LvUpしたならtrueが返る
			print("<span class=\"levelup\">".$char->Name()." LevelUp!</span><br />\n");
	}
}
//////////////////////////////////////////////////
//	アイテムを取得する(チームが)
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
//	後衛を守りに入るキャラを選ぶ。
	function &Defending(&$target,&$candidate,$skill) {
		if($target === false) return false;

		if($skill["invalid"])//防御無視できる技。
			return false;
		if($skill["support"])//支援なのでガードしない。
			return false;
		if($target->POSITION == "front")//前衛なら守る必要無し。終わる
			return false;
		// "前衛で尚且つ生存者"を配列に詰める↓
		// 前衛 + 生存者 + HP1以上 に変更 ( 多段系攻撃で死にながら守るので [2007/9/20] )
		foreach($candidate as $key => $char) {
			//print("{$char->POSTION}:{$char->STATE}<br>");
			if($char->POSITION == "front" && $char->STATE !== 1 && 1 < $char->HP )
				$fore[]	= &$candidate["$key"];
		}
		if(count($fore) == 0)//前衛がいなけりゃ守れない。終わる
			return false;
		// 一人づつ守りに入るか入らないかを判定する。
		shuffle($fore);//配列の並びを混ぜる
		foreach($fore as $key => $char) {
			// 判定に使う変数を計算したりする。
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
			// 実際に判定してみる。
			switch($char->guard) {
				case "never":
					continue;
				case "life25":// HP(%)が25%以上なら
					if(25 < $HpRate) $defender	= &$fore["$key"]; break;
				case "life50":// 〃50%〃
					if(50 < $HpRate) $defender	= &$fore["$key"]; break;
				case "life75":// 〃70%〃
					if(75 < $HpRate) $defender	= &$fore["$key"]; break;
				case "prob25":// 25%の確率で
					if($prob < 25) $defender	= &$fore["$key"]; break;
				case "prob50":// 50% 〃
					if($prob < 50) $defender	= &$fore["$key"]; break;
				case "prob75":// 75% 〃
					if($prob < 75) $defender	= &$fore["$key"]; break;
				default:
					$defender	= &$fore["$key"];
			}
			// 誰かが後衛を守りに入ったのでそれを表示する
			if($defender) {
				print('<span class="bold">'.$defender->name.'</span> protected <span class="bold">'.$target->name.'</span>!<br />'."\n");
				return $defender;
			}
		}
	}
//////////////////////////////////////////////////
//	スキル使用後に対象者(候補)がしぼーしたかどうかを確かめる
	function JudgeTargetsDead(&$target) {
		foreach($target as $key => $char) {
			// 与えたダメージの差分で経験値を取得するモンスターの場合。
			if(method_exists($target[$key],'HpDifferenceEXP')) {
				$exp	+= $target[$key]->HpDifferenceEXP();
			}
			if($target[$key]->CharJudgeDead()) {//死んだかどうか
				// 死亡メッセージ
				print("<span class=\"dmg\">".$target[$key]->Name(bold)." down.</span><br />\n");

				//経験値の取得
				$exp	+= $target[$key]->DropExp();

				//お金の取得
				$money	+= $target[$key]->DropMoney();

				// アイテムドロップ
				if($item = $target[$key]->DropItem()) {
					$itemdrop["$item"]++;
					$item	= LoadItemData($item);
					print($char->Name("bold")." dropped");
					print("<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\"/>\n");
					print("<span class=\"bold u\">{$item[name]}</span>.<br />\n");
				}

				//召喚キャラなら消す。
				if($target[$key]->summon === true) {
					unset($target[$key]);
				}

				// 死んだのでディレイを直す。
				$this->ChangeDelay();
			}
		}
		return array($exp,$money,$itemdrop);//取得する経験値を返す
	}
//////////////////////////////////////////////////
//	優先順位に従って候補から一人返す
	function &SelectTarget(&$target_list,$skill) {

		/*
		* 優先はするが、当てはまらなくても最終的にターゲットは要る。
		* 例 : 後衛が居ない→前衛を対象にする。
		*    : 全員がHP100%→誰か てきとう に対象にする。
		*/

		//残りHP(%)が少ない人をターゲットにする
		if($skill["priority"] == "LowHpRate") {
			$hp = 2;//一応1より大きい数字に・・・
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD) continue;//しぼー者は対象にならない。
				$HpRate	= $char->HP / $char->MAXHP;//HP(%)
				if($HpRate < $hp) {
					$hp	= $HpRate;//現状の最もHP(%)が低い人
					$target	= &$target_list[$key];
				}
			}
			return $target;//最もHPが低い人

		//後衛を優先する
		} else if($skill["priority"] == "Back") {
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD) continue;//しぼー者は対象にならない。
				if($char->POSITION != FRONT)//後衛なら
				$target[]	= &$target_list[$key];//候補にいれる
			}
			if($target)
				return $target[array_rand($target)];//リストの中からランダムで

		/*
		* 優先はするが、
		* 優先する対象がいなければ使用は失敗する(絞込み)
		*/

		//しぼー者の中からランダムで返す。
		} else if($skill["priority"] == "Dead") {
			foreach($target_list as $key => $char) {
				if($char->STATE == DEAD)//しぼーなら
				$target[]	= &$target_list[$key];//しぼー者リスト
			}
			if($target)
				return $target[array_rand($target)];//しぼー者リストの中からランダムで
			else
				return false;//誰もいなけりゃfalse返すしかない...(→スキル使用失敗)

		// 召喚キャラを優先する。
		} else if($skill["priority"] == "Summon") {
			foreach($target_list as $key => $char) {
				if($char->summon)//召喚キャラなら
					$target[]	= &$target_list[$key];//召喚キャラリスト
			}
			if($target)
				return $target[array_rand($target)];//召喚キャラの中からランダムで
			else
				return false;//誰もいなけりゃfalse返すしかない...(→スキル使用失敗)

		// チャージ中のキャラ
		} else if($skill["priority"] == "Charge") {
			foreach($target_list as $key => $char) {
				if($char->expect)
					$target[]	= &$target_list[$key];
			}
			if($target)
				return $target[array_rand($target)];
			else
				return false;//誰もいなけりゃfalse返すしかない...(→スキル使用失敗)
		//
		}

		//それ以外(ランダム)
		foreach($target_list as $key => $char) {
			if($char->STATE != DEAD)//しぼー以外なら
				$target[]	= &$target_list[$key];//しぼー者リスト
		}
		return $target[array_rand($target)];//ランダムに誰か一人
	}
//////////////////////////////////////////////////
//	次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
//	リファレンスを返す
	function &NextActer() {
		// 最もディレイが大きい人を探す
		foreach($this->team0 as $key => $char) {
			if($char->STATE === 1) continue;
			// 最初は誰でもいいのでとりあえず最初の人とする。
			if(!isset($delay)) {
				$delay	= $char->delay;
				$NextChar	= &$this->team0["$key"];
				continue;
			}
			// キャラが今のディレイより多ければ交代
			if($delay <= $char->delay) {//行動
				// もしキャラとディレイが同じなら50%で交代
				if($delay == $char->delay) {
					if(mt_rand(0,1))
						continue;
				}
				$delay	= $char->delay;
				$NextChar	= &$this->team0["$key"];
			}
		}
		// ↑と同じ。
		foreach($this->team1 as $key => $char) {
			if($char->STATE === 1) continue;
			if($delay <= $char->delay) {//行動
				if($delay == $char->delay) {
					if(mt_rand(0,1))
						continue;
				}
				$delay	= $char->delay;
				$NextChar	= &$this->team1["$key"];
			}
		}
		// 全員ディレイ減少
		$dif	= $this->delay - $NextChar->delay;//戦闘基本ディレイと行動者のディレイの差分
		if($dif < 0)//もしも差分が0以下になったら…
			return $NextChar;
		foreach($this->team0 as $key => $char) {
			$this->team0["$key"]->Delay($dif);
		}
		foreach($this->team1 as $key => $char) {
			$this->team1["$key"]->Delay($dif);
		}
		/*// エラーが出たらこれで。
		if(!is_object($NextChar)) {
			print("AAA");
			dump($NextChar);
			print("BBB");
		}
		*/

		return $NextChar;
	}
//////////////////////////////////////////////////
//	次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
//	リファレンスを返す
	function &NextActerNew() {

		// 次の行動まで最も距離が短い人を探す。
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

		// ↑と同じ。
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

		// 全員ディレイ減少 //////////////////////

		//もしも差分が0以下になったら
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
		// エラーが出たらこれでたしかめろ。
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
//	キャラ全員の行動ディレイを初期化(=SPD)
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
//	ディレイを計算して設定する
//	誰かのSPDが変化した場合呼び直す
//	*** 技の使用等でSPDが変化した際に呼び出す ***
	function SetDelay() {
		if(DELAY_TYPE === 0)
		{
			//SPDの最大値と合計を求める
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
			//平均SPD
			$AverageSPD	= $TotalSPD/( count($this->team0) + count($this->team1) );
			//基準delayとか
			$AveDELAY	= $AverageSPD * DELAY;
			$this->delay	= $MaxSPD + $AveDELAY;//その戦闘の基準ディレイ
			$this->ChangeDelay	= false;//falseにしないと毎回DELAYを計算し直してしまう。
		}
			else if(DELAY_TYPE === 1)
		{
		}
	}
//////////////////////////////////////////////////
//	戦闘の基準ディレイを再計算させるようにする。
//	使う場所は、技の使用でキャラのSPDが変化した際に使う。
//	class.skill_effect.php で使用。
	function ChangeDelay(){
		if(DELAY_TYPE === 0)
		{
			$this->ChangeDelay	= true;
		}
	}
//////////////////////////////////////////////////
//	チームの名前を設定
	function SetTeamName($name1,$name2) {
		$this->team0_name	= $name1;
		$this->team1_name	= $name2;
	}
//////////////////////////////////////////////////
//	戦闘開始した時の平均レベルや合計HP等を計算・表示
//	戦闘の経緯は一つの表で構成されるうっう
	function BattleHeader() {
		foreach($this->team0 as $char) {//チーム0
			$team0_total_lv	+= $char->level;//合計LV
			$team0_total_hp	+= $char->HP;//合計HP
			$team0_total_maxhp	+= $char->MAXHP;//合計最大HP
		}
		$team0_avelv	= round($team0_total_lv/count($this->team0)*10)/10;//チーム0平均LV
		$this->team0_ave_lv	= $team0_avelv;
		foreach($this->team1 as $char) {//チーム1
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
//	戦闘終了時に表示
	function BattleFoot() {
	/*	print("<tr><td>");
		dump($this->team0);
		print("</td></tr>");*/
		?>
</tbody></table>
<?
	}
//////////////////////////////////////////////////
//	戦闘画像・各キャラの残りHP残りSP等を表示
	function BattleState() {
		static $last;
		if($last !== $this->actions)
			$last	= $this->actions;
		else
			return false;

		print("<tr><td colspan=\"2\" class=\"btl_img\">\n");
		// 戦闘ステップ順に自動スクロール
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
				$this->ShowGdImage();//画像
				print('</div>');
				break;
			case 1:
			case 2:
				$this->ShowCssImage();//画像
				break;
		}
		print("</div>");
		print("</td></tr><tr><td class=\"ttd2 break\">\n");

		print("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");// team1-backs

		// 	左側チーム後衛
		foreach($this->team1 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;

			if($char->POSITION != FRONT)
				$char->ShowHpSp();
		}

		// 	左側チーム前衛
		print("</td><td style=\"width:50%\">\n");
		foreach($this->team1 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;

			if($char->POSITION == FRONT)
				$char->ShowHpSp();
		}

		print("</td></tr></tbody></table>\n");

		print("</td><td class=\"ttd1 break\">\n");

		// 	右側チーム前衛
		print("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");
		foreach($this->team0 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == FRONT)
				$char->ShowHpSp();
		}

		// 	右側チーム後衛
		print("</td><td style=\"width:50%\">\n");
		foreach($this->team0 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION != FRONT)
				$char->ShowHpSp();
		}
		print("</td></tr></tbody></table>\n");

		print("</td></tr>\n");
	}
//////////////////////////////////////////////////
//	戦闘画像(画像のみ)
	function ShowGdImage() {
		$url	= BTL_IMG."?";

		// HP=0 のキャラの画像(拡張子があればそれを取る)
		$DeadImg	= substr(DEAD_IMG,0,strpos(DEAD_IMG,"."));

		//チーム1
		$f	= 1;
		$b	= 1;//前衛の数・後衛の数を初期化
		foreach($this->team0 as $char) {
			//画像はキャラに設定されている画像の拡張子までの名前
			if($char->STATE === 1)
				$img	= $DeadImg;
			else
				$img	= substr($char->img,0,strpos($char->img,"."));
			if($char->POSITION == "front")://前衛
				$url	.= "f2{$f}=$img&";
				$f++;
			else:
				$url	.= "b2{$b}=$img&";//後衛
				$b++;
			endif;
		}
		//チーム0
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
		print('<img src="'.$url.'">');// ←これが表示されるのみ
	}
//////////////////////////////////////////////////
//	CSS戦闘画面
	function ShowCssImage() {
		include_once(BTL_IMG_CSS);
		$img	= new cssimage();
		$img->SetBackGround($this->BackGround);
		$img->SetTeams($this->team1,$this->team0);
		$img->SetMagicCircle($this->team1_mc, $this->team0_mc);
		if(BTL_IMG_TYPE == 2)
			$img->NoFlip();// CSS画像反転無し
		$img->Show();
	}
//////////////////////////////////////////////////
//	お金を得る、一時的に変数に保存するだけ。
//	class内にメソッド作れー
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
//	ユーザーデータに得る合計金額を渡す
	function ReturnMoney() {
		return array($this->team0_money,$this->team1_money);
	}

//////////////////////////////////////////////////
//	全体の死者数を数える...(ネクロマンサしか使ってない?)
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
//	指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
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
//	魔方陣を追加する
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
//	魔方陣を削除する
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
//	生存者数を数えて返す
function CountAlive($team) {
	$no	= 0;//初期化
	foreach($team as $char) {
		if($char->STATE !== 1)
			$no++;
	}
	return $no;
}

//////////////////////////////////////////////////
//	初期キャラ生存数を数えて返す
function CountAliveChars($team) {
	$no	= 0;//初期化
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
//	召還系スキルで呼ばれたモンスター。
	function CreateSummon($no,$strength=false) {
		include_once(DATA_MONSTER);
		$monster	= CreateMonster($no,1);

		$monster["summon"]	= true;
		// 召喚モンスターの強化。
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
//	複数の判断要素での判定
//function MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam) {
function MultiFactJudge($Keys,$char,$classBattle) {
	foreach($Keys as $no) {

		//$return	= DecideJudge($no,$char,$MyTeam,$EnemyTeam);
		$return	= DecideJudge($no,$char,$classBattle);

		// 判定が否であった場合終了。
		if(!$return)
			return false;

		// 配列を比較して共通項目を残す(ほぼ廃止の方向へ)
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