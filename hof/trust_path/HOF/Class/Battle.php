<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
//class HOF_Class_Battle extends battle implements HOF_Class_Base_Extend_RootInterface
class HOF_Class_Battle extends HOF_Class_Base_Extend_Root
{

	/*
	* $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
	* $battle->SetTeamName($this->name,$party["name"]);
	* $battle->Process();//戦闘開始
	*/

	/**
	 * 戦闘の最大ターン数(延長される可能性のある)
	 */
	var $BattleMaxTurn = BATTLE_MAX_TURNS;
	var $NoExtends = false;

	var $NoResult = false;

	/**
	 * 戦闘背景
	 */
	var $BackGround = "grass";

	/**
	 * スクロール ( << >> ← これの変数)
	 */
	var $Scroll = 0;

	/**
	 * 総行動回数
	 */
	var $actions = 0;
	/**
	 * 戦闘における基準ディレイ
	 */
	var $delay;
	/**
	 * 勝利チーム
	 */
	var $result;

	/**
	 * 特殊な変数
	 * キャラのSPDが変化した際にDELAYを再計算する。
	 */
	var $ChangeDelay = false;

	/**
	 * 0=決着着かなければDraw 1=生存者の数で勝敗を決める
	 */
	var $BattleResultType = 0;
	/**
	 * 残りHP総HPを隠す(????/????)
	 */
	var $UnionBattle;

	public $teams;

	/**
	 * @param $team0 $MyParty
	 * @param $team1 $EnemyParty
	 */
	function __construct($MyTeam, $EnemyTeam)
	{

		$this->_extend_init();

		$this->teams[TEAM_0]['team'] = HOF_Class_Battle_Team2::newInstance($MyTeam, TEAM_0);
		$this->teams[TEAM_1]['team'] = HOF_Class_Battle_Team2::newInstance($EnemyTeam, TEAM_1);

		/**
		 * 各チームに戦闘専用の変数を設定する(class.char.php)
		 * 装備の特殊機能等を計算して設定する。
		 * 戦闘専用の変数は大文字英語だったりする。class.char.phpを参照。
		 */
		foreach ($this->teams as $idx => $data)
		{
			$data['no'] = $idx;
			$data['dmg'] = 0;
			$data['team']->update();

			$data['team']->team_idx($idx);
			$data['team']->pushNameList();

			foreach ($data['team'] as $char)
			{
				$char->setBattleVariable();
			}
		}

		/*
		foreach ($this->teams as $idx => $data)
		{
			$data['team']->fixCharName();
		}
		*/

		// delay関連

		// ディレイ計算
		$this->SetDelay();

		// 初期化
		$this->DelayResetAll();
	}

	protected function _extend_init()
	{
		$this->extend('HOF_Class_Battle_View');
		$this->extend('HOF_Class_Skill_Effect');
		$this->extend('HOF_Class_Battle_Skill');
		$this->extend('HOF_Class_Battle_Judge');
	}

	function teamToggle($myteam)
	{
		$list = array(TEAM_0, TEAM_1);

		if ($myteam instanceof HOF_Class_Battle_Team2)
		{
			$myteam = $myteam->team_idx();
		}

		if (in_array($myteam, $list, true))
		{
			return ($myteam === TEAM_0) ? array(TEAM_0, TEAM_1) : array(TEAM_1, TEAM_0);
		}

		return null;
	}

	function outputImage()
	{
		$output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)->setBg($this->BackGround)->setTeams($this->teams[TEAM_1]['team'], $this->teams[TEAM_0]['team'])->setMagicCircle($this->teams[TEAM_1]['mc'], $this->teams[TEAM_0]['mc'])->exec();

		echo $output;
	}

	/**
	 * 魔方陣を追加する
	 *
	 * @param bool|$del 魔方陣を削除する
	 */
	function changeMagicCircle($team, $amount, $del = 0)
	{
		$amount *= ($del ? -1 : 1);

		$team_mc = &$this->teams[$team]['mc'];

		if ($del)
		{
			if ($team_mc < $amount) return false;
		}

		$team_mc += $amount;

		$team_mc = abs(max(0, min(5, $team_mc)));

		return true;
	}

	/**
	 * 指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	 */
	function CountDead($team)
	{
		return $team->CountDead();

		//return HOF_Class_Battle_Team::CountDead($who);
	}

	/**
	 * 全体の死者数を数える...(ネクロマンサしか使ってない?)
	 */
	function CountDeadAll()
	{
		$count = 0;

		foreach ($this->teams as &$data)
		{
			$count += $data['team']->CountDead();
		}

		return $count;
	}

	/**
	 * 戦闘にキャラクターを途中参加させる。
	 *
	 * @param HOF_Class_Char_Type_Char|$char
	 * @param HOF_Class_Char_Type_Char|$add
	 */
	function JoinCharacter($char, $add)
	{
		list($my) = $this->teamToggle($char->team());

		if ($my !== null)
		{
			$this->teams[$my]['team']->append($add);
			$this->ChangeDelay();

			return true;
		}
	}

	/**
	 * 戦闘記録を保存する
	 */
	function RecordLog($type = false)
	{
		$log = array();

		if ($type == "RANK")
		{
			$file = LOG_BATTLE_RANK;
			$log = HOF_Class_File::glob(LOG_BATTLE_RANK);
			$logAmount = MAX_BATTLE_LOG_RANK;
		}
		elseif ($type == "BASE_PATH_UNION")
		{
			$file = LOG_BATTLE_UNION;
			$log = HOF_Class_File::glob(LOG_BATTLE_UNION);
			$logAmount = MAX_BATTLE_LOG_UNION;
		}
		else
		{
			$file = LOG_BATTLE_NORMAL;
			$log = HOF_Class_File::glob(LOG_BATTLE_NORMAL);
			$logAmount = MAX_BATTLE_LOG;
		}

		// 古いログを消す
		$i = 0;
		while ($logAmount <= count($log))
		{
			HOF_Class_File::unlink($log["$i"], 1);
			unset($log["$i"]);
			$i++;
		}

		// 新しいログを作る
		//$time = time() . substr(microtime(), 2, 6);

		$time = HOF_Helper_Char::uniqid_birth();

		$file .= $time . ".dat";

		$head = $time . "\n"; //開始時間(1行目)
		$head .= $this->teams[TEAM_0]['name'] . "<>" . $this->teams[TEAM_1]['name'] . "\n"; //参加チーム(2行目)
		$head .= count($this->teams[TEAM_0]['team']) . "<>" . count($this->teams[TEAM_1]['team']) . "\n"; //参加人数(3行目)
		$head .= $this->teams[TEAM_0]['ave_lv'] . "<>" . $this->teams[TEAM_1]['ave_lv'] . "\n"; //平均レベル(4行目)
		$head .= $this->result . "\n"; //勝利チーム(5行目)
		$head .= $this->actions . "\n"; //総ターン数(6行目)
		$head .= "\n"; // 改行(7行目)

		HOF_Class_File::mkdir(dirname($file));

		HOF_Class_File::WriteFile($file, $head . ob_get_contents());
	}

	/**
	 * キャラの行動
	 */
	function Action(&$char)
	{
		// $char->judge が設定されてなければ飛ばす
		if (empty($char->pattern))
		{
			$char->delay = $char->SPD;

			throw new RuntimeException($char->Name()." doesn't have any pattern.");

			return false;
		}

		// チーム0の人はセルの右側に
		// チーム1の人は左側に 行動内容と結果 を表示する
		echo ("<tr><td class=\"ttd2\">\n");

		if ($char->team()->team_idx() === TEAM_0)
		{
			echo ("</td><td class=\"ttd1\">\n");
		}

		list($_my, $_enemy) = $this->teamToggle($char->team());

		$MyTeam = &$this->teams[$_my]['team'];
		$EnemyTeam = &$this->teams[$_enemy]['team'];

		//行動の判定(使用する技の判定)
		if ($char->expect)
		{
			// 詠唱,貯め 完了
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
				} while ($char->pattern[$JudgeKey]['action'] == 9000 && $char->pattern[$JudgeKey]['judge']);

				$return = $this->MultiFactJudge($Keys, $char);

				if ($return)
				{
					$skill = $char->pattern[$JudgeKey]['action'];
					foreach ($Keys as $no) $char->JdgCount[$no]++; //決定した判断のカウントうｐ
					break;
				}
			} while ($char->pattern[$JudgeKey]['judge']);

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

		//debug(__LINE__, count($MyTeam), count($EnemyTeam));

		if ($skill)
		{
			//$this->UseSkill($skill, &$return, &$char, &$MyTeam, &$EnemyTeam);
			$this->UseSkill($skill, &$return, &$char);
			// 行動できなかった場合の処理
		}
		else
		{
			echo ($char->Name('bold') . " sunk in thought and couldn't act.<br />(No more patterns)<br />\n");
			$char->DelayReset();
		}

		//ディレイリセット
		//if($ret	!== "DontResetDelay")
		//	$char->DelayReset;

		//echo $char->name." ".$skill."<br>";//確認用
		//セルの終わり
		if ($char->team()->team_idx() === TEAM_1)
		{
			echo ("</td><td class=\"ttd1\">&nbsp;\n");
		}

		echo ("</td></tr>\n");
	}

	/**
	 * 戦闘終了の判定
	 * 全員死んでる=draw(?)
	 */
	function BattleResult()
	{
		if ($this->teams[TEAM_0]['team']->CountAlive() == 0)
		{
			//全員しぼーなら負けにする。
			$team0Lose = true;
		}

		if ($this->teams[TEAM_1]['team']->CountAlive() == 0)
		{
			//全員しぼーなら負けにする。
			$team1Lose = true;
		}

		//勝者のチーム番号か引き分けを返す
		if ($team0Lose && $team1Lose)
		{
			$this->result = BATTLE_DRAW;
			return $this->result;
		}
		elseif ($team0Lose)
		{ //team1 won
			$this->result = TEAM_1;
			return $this->result;
		}
		elseif ($team1Lose)
		{ // team0 won
			$this->result = TEAM_0;
			return $this->result;

			// 両チーム生存していて最大行動数に達した時。
		}
		elseif ($this->BattleMaxTurn <= $this->actions)
		{
			// 生存者数の差。
			/*
			// 生存者数の差が1人以上なら延長
			$AliveNumDiff	= abs($this->teams[TEAM_0]['team']->CountAlive() - $this->teams[TEAM_1]['team']->CountAlive());
			if(0 < $AliveNumDiff && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS) {
			*/
			$AliveNumDiff = abs($this->teams[TEAM_0]['team']->CountAlive() - $this->teams[TEAM_1]['team']->CountAlive());
			$Not5 = ($this->teams[TEAM_0]['team']->CountAlive() != 5 && $this->teams[TEAM_1]['team']->CountAlive() != 5);
			//$lessThan4	= ( $this->teams[TEAM_0]['team']->CountAlive() < 5 || $this->teams[TEAM_1]['team']->CountAlive() < 5 );
			//if( ( $lessThan4 || 0 < $AliveNumDiff ) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS ) {
			if (($Not5 || 0 < $AliveNumDiff) && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS)
			{
				if ($this->ExtendTurns(TURN_EXTENDS, 1)) return false;
			}

			// 決着着かなければただ引き分けにする。
			if ($this->BattleResultType == 0)
			{
				$this->result = BATTLE_DRAW; //引き分け。
				return $this->result;
				// 決着着かなければ生存者の数で勝敗をつける。
			}
			elseif ($this->BattleResultType == 1)
			{
				// とりあえず引き分けに設定
				// (1) 生存者数が多いほうが勝ち
				// (2) (1) が同じなら総ダメージが多いほうが勝ち
				// (3) (2) でも同じなら引き分け…???(or防衛側の勝ち)

				$team0Alive = $this->teams[TEAM_0]['team']->CountAliveChars();
				$team1Alive = $this->teams[TEAM_1]['team']->CountAliveChars();
				if ($team1Alive < $team0Alive)
				{
					// team0 won
					$this->result = TEAM_0;
					return $this->result;
				}
				elseif ($team0Alive < $team1Alive)
				{
					// team1 won
					$this->result = TEAM_1;
					return $this->result;
				}
				else
				{
					$this->result = BATTLE_DRAW;
					return $this->result;
				}
			}
			else
			{
				$this->result = BATTLE_DRAW;
				echo ("error321708.<br />おかしいので報告してください。");
				return $this->result; // エラー回避。
			}

			$this->result = BATTLE_DRAW;
			echo ("error321709.<br />おかしいので報告してください。");
			return $this->result; // エラー回避。
		}

		return false;
	}

	function initEnterBattlefield()
	{
		$list_name = $list = array();

		foreach ($this->teams as $idx => $data)
		{
			foreach ($data['team'] as $char)
			{
				//$list[] = array('dis' => $char->DelayValue(), 'char' => $char);
				$list[] = $char;

				$char->isUnion();
			}
		}

		usort($list, HOF_Class_Array_Comparer_MuliteSubKey::newInstance('isUnion', 'SPD')->comp_func('bccomp')->sort_desc(true)->callback());

		foreach ($list as $char)
		{
			HOF_Class_Battle_Team2::_callback_fixCharName($char);

			$this->showEnterBattlefield($char);
		}
	}

	function showEnterBattlefield($char, $mode = true)
	{
		echo ("<tr><td class=\"ttd2\">\n");

		if ($char->team()->team_idx() === TEAM_0)
		{
			echo ("</td><td class=\"ttd1\">\n");
		}

		//debug(__LINE__, __METHOD__, $char->name, $char->name, $char->Name());
		$char->enterBattlefield();

		if ($char->team()->team_idx() === TEAM_1)
		{
			echo ("</td><td class=\"ttd1\">&nbsp;\n");
		}

		echo ("</td></tr>\n");
	}

	//	戦闘処理(これを実行して戦闘が処理される)
	function Process()
	{
		$this->BattleHeader();

		$this->initEnterBattlefield();

		//戦闘が終わるまで繰り返す
		do
		{
			if ($this->actions % BATTLE_STAT_TURNS == 0)
			{
				//一定間隔で状況を表示
				$this->BattleState(); //状況の表示
			}

			// 行動キャラ
			if (DELAY_TYPE === 0)
			{
				$char = &$this->NextActer();
			}
			elseif (DELAY_TYPE === 1)
			{
				$char = &$this->NextActerNew();
			}

			$this->Action($char); //行動
			$result = $this->BattleResult(); //↑の行動で戦闘が終了したかどうかの判定

			//技の使用等でSPDが変化した場合DELAYを再計算する。
			if ($this->ChangeDelay)
			{
				$this->SetDelay();
			}

		} while ($result === null || $result === false);

		$this->ShowResult($result); //戦闘の結果表示
		$this->BattleFoot();

		//$this->SaveCharacters();
	}

	/**
	 * 次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
	 * リファレンスを返す
	 */
	function &NextActerNew()
	{

		/**
		 * 次の行動まで最も距離が短い人を探す。
		 */
		$nextDis = 1000;

		foreach ($this->teams as $idx => $data)
		{
			foreach ($data['team'] as $char)
			{
				if ($char->STATE === STATE_DEAD) continue;

				$charDis = $char->nextDis();

				$cmp = bccomp($charDis, $nextDis);

				if ($cmp == 0)
				{
					$NextChar[] = $char;
				}
				elseif ($cmp < 0)
				{
					$nextDis = $charDis;
					$NextChar = array($char);
				}
			}
		}

		/**
		 * もしも差分が0以下になったら
		 */
		if ($nextDis >= 0)
		{
			/**
			 * 全員ディレイ減少
			 */
			foreach ($this->teams as $idx => &$data)
			{
				foreach ($data['team'] as &$char)
				{
					$char->Delay($nextDis);
				}
			}
		}

		if (count($NextChar) > 1)
		{
			HOF_Helper_Math::rand_seed();

			$next = $NextChar[array_rand($NextChar)];
		}
		else
		{
			$next = reset($NextChar);
		}

		$next->DelayByRate(1, $this->delay);

		return $next;
	}

	function SetResultType($var)
	{
		$this->BattleResultType = $var;
	}

	/**
	 * UnionBattleである事にする。
	 */
	function SetUnionBattle()
	{
		$this->UnionBattle = true;
	}

	/**
	 * 背景画像をセットする。
	 */
	function SetBackGround($bg)
	{
		$this->BackGround = $bg;
	}

	/**
	 * 限界ターン数を決めちゃう。
	 */
	function LimitTurns($no)
	{
		$this->BattleMaxTurn = $no;
		$this->NoExtends = true; //これ以上延長はしない。
	}

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
		if (count($this->teams[$team]['item']) != 0)
		{
			return $this->teams[$team]['item'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * 挑戦者側が勝利したか？
	 */
	function ReturnBattleResult()
	{
		return $this->result;
	}

	/**
	 * 戦闘後のキャラクター状況を保存する。
	 */
	function SaveCharacters()
	{
		foreach ($this->teams as $idx => &$data)
		{
			foreach ($data['team'] as &$char)
			{
				$char->saveCharData();
			}
		}
	}

	/**
	 * 総ダメージを加算する
	 */
	function AddTotalDamage($team, $dmg)
	{
		if (!is_numeric($dmg)) return false;

		list($_my) = $this->teamToggle($team);

		$this->teams[$_my]['dmg'] += $dmg;
	}

	/**
	 * 経験値を得る
	 */
	function GetExp($exp, $team)
	{
		if (!$exp) return false;

		$exp = round(EXP_RATE * $exp);

		$this->teams[$team]['exp'] += $exp;

		$Alive = $this->teams[$team]['team']->CountTrueChars();

		if ($Alive == 0) return false;

		/**
		 * 生存者にだけ経験値を分ける
		 */
		$ExpGet = ceil($exp / $Alive);
		echo ("Alives get {$ExpGet}exps.<br />\n");

		foreach ($this->teams[$team]['team'] as $key => &$char)
		{
			/**
			 * 死亡者にはEXPあげない
			 */
			if ($char->STATE === STATE_DEAD) continue;

			/**
			 * LvUpしたならtrueが返る
			 */
			if ($char->GetExp($ExpGet))
			{
				echo ("<span class=\"levelup\">" . $char->Name() . " LevelUp!</span><br />\n");
			}
		}
	}

	/**
	 * アイテムを取得する(チームが)
	 */
	function GetItem($itemdrop, $MyTeam)
	{
		if (!$itemdrop) return false;

		foreach ($itemdrop as $itemno => $amount)
		{
			$this->teams[$MyTeam]['item']["$itemno"] += $amount;
		}
	}

	/**
	 * 後衛を守りに入るキャラを選ぶ。
	 */
	function &Defending(&$target, &$candidate, $skill)
	{
		if ($target === false) return false;

		if ($skill["invalid"]) //防御無視できる技。
 				return false;
		if ($skill["support"]) //支援なのでガードしない。
 				return false;
		if ($target->POSITION == POSITION_FRONT) //前衛なら守る必要無し。終わる
 				return false;

		/**
		 * "前衛で尚且つ生存者"を配列に詰める↓
		 * 前衛 + 生存者 + HP1以上 に変更 ( 多段系攻撃で死にながら守るので [2007/9/20] )
		 */
		foreach ($candidate as $key => &$char)
		{
			if ($char->POSITION == POSITION_FRONT && $char->STATE !== STATE_DEAD && 1 < $char->HP) $fore[] = &$char;
		}
		if (count($fore) == 0) //前衛がいなけりゃ守れない。終わる
 				return false;
		// 一人づつ守りに入るか入らないかを判定する。
		shuffle($fore); //配列の並びを混ぜる
		foreach ($fore as $key => &$char)
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
					if (25 < $HpRate) $defender = &$char;
					break;
				case "life50": // 〃50%〃
					if (50 < $HpRate) $defender = &$char;
					break;
				case "life75": // 〃70%〃
					if (75 < $HpRate) $defender = &$char;
					break;
				case "prob25": // 25%の確率で
					if ($prob < 25) $defender = &$char;
					break;
				case "prob50": // 50% 〃
					if ($prob < 50) $defender = &$char;
					break;
				case "prob75": // 75% 〃
					if ($prob < 75) $defender = &$char;
					break;
				default:
					$defender = &$char;
			}
			// 誰かが後衛を守りに入ったのでそれを表示する
			if ($defender)
			{
				echo ('<span class="bold">' . $defender->Name() . '</span> protected <span class="bold">' . $target->Name() . '</span>!<br />' . "\n");
				return $defender;
			}
		}
	}

	/**
	 * スキル使用後に対象者(候補)がしぼーしたかどうかを確かめる
	 */
	function JudgeTargetsDead(&$target)
	{
		foreach ($target as $key => $char)
		{
			/**
			 * 与えたダメージの差分で経験値を取得するモンスターの場合。
			 */
			if (method_exists($target[$key], 'HpDifferenceEXP'))
			{
				$exp += $target[$key]->HpDifferenceEXP();
			}
			if ($target[$key]->CharJudgeDead())
			{
				/**
				 * 死んだかどうか
				 * 死亡メッセージ
				 */
				echo ("<span class=\"dmg\">" . $target[$key]->Name('bold') . " down.</span><br />\n");

				/**
				 * 経験値の取得
				 */
				$exp += $target[$key]->DropExp();

				/**
				 * お金の取得
				 */
				$money += $target[$key]->DropMoney();

				/**
				 * アイテムドロップ
				 */
				if ($item = $target[$key]->DropItem())
				{
					$itemdrop["$item"]++;
					$item = HOF_Model_Data::getItemData($item);
					echo ($char->Name("bold") . " dropped");
					echo ("<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM) . "\" class=\"vcent\"/>\n");
					echo ("<span class=\"bold u\">{$item[name]}</span>.<br />\n");
				}

				/**
				 * 召喚キャラなら消す。
				 */
				if ($target[$key]->isSummon())
				{
					unset($target[$key]);
				}

				/**
				 * 死んだのでディレイを直す。
				 */
				$this->ChangeDelay();
			}
		}

		/**
		 * 取得する経験値を返す
		 */
		return array(
			$exp,
			$money,
			$itemdrop);
	}

	/**
	 * 優先順位に従って候補から一人返す
	 */
	function &SelectTarget(&$target_list, $skill)
	{

		/**
		 * 優先はするが、当てはまらなくても最終的にターゲットは要る。
		 * 例 : 後衛が居ない→前衛を対象にする。
		 *    : 全員がHP100%→誰か てきとう に対象にする。
		 */
		if ($skill["priority"] == "LowHpRate")
		{
			// 残りHP(%)が少ない人をターゲットにする

			// 一応1より大きい数字に・・・
			$hp = 2;
			foreach ($target_list as $key => &$char)
			{
				if ($char->STATE == STATE_DEAD) continue; //しぼー者は対象にならない。
				$HpRate = $char->HP / $char->MAXHP; //HP(%)
				if ($HpRate < $hp)
				{
					$hp = $HpRate; //現状の最もHP(%)が低い人
					$target = &$char;
				}
			}
			return $target; //最もHPが低い人


		}
		elseif ($skill["priority"] == "Back")
		{
			// 後衛を優先する
			foreach ($target_list as $key => &$char)
			{
				if ($char->STATE == STATE_DEAD) continue; //しぼー者は対象にならない。
				if ($char->POSITION != POSITION_FRONT) //後衛なら
 						$target[] = &$char; //候補にいれる
			}
			if ($target) return $target[array_rand($target)]; //リストの中からランダムで

			/**
			 * 優先はするが、
			 * 優先する対象がいなければ使用は失敗する(絞込み)
			 */
		}
		elseif ($skill["priority"] == "Dead")
		{
			// しぼー者の中からランダムで返す。
			foreach ($target_list as $key => &$char)
			{
				if ($char->STATE == STATE_DEAD) //しぼーなら
 						$target[] = &$char; //しぼー者リスト
			}
			if ($target) return $target[array_rand($target)]; //しぼー者リストの中からランダムで
			else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)
		}
		elseif ($skill["priority"] == "Summon")
		{
			// 召喚キャラを優先する。
			foreach ($target_list as $key => &$char)
			{
				if ($char->isSummon()) //召喚キャラなら
 						$target[] = &$char; //召喚キャラリスト
			}
			if ($target) return $target[array_rand($target)]; //召喚キャラの中からランダムで
			else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)
		}
		elseif ($skill["priority"] == "Charge")
		{
			// チャージ中のキャラ
			foreach ($target_list as $key => &$char)
			{
				if ($char->expect) $target[] = &$char;
			}
			if ($target) return $target[array_rand($target)];
			else  return false; //誰もいなけりゃfalse返すしかない...(→スキル使用失敗)
			//
		}

		// それ以外(ランダム)
		foreach ($target_list as $key => &$char)
		{
			if ($char->STATE != STATE_DEAD) //しぼー以外なら
 					$target[] = &$char; //しぼー者リスト
		}

		return $target[array_rand($target)]; //ランダムに誰か一人
	}

	/**
	 * 次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
	 * リファレンスを返す
	 */
	function &NextActer()
	{

		foreach ($this->teams as $idx => &$data)
		{
			foreach ($data['team'] as &$char)
			{
				if ($char->STATE === STATE_DEAD) continue;

				// 最初は誰でもいいのでとりあえず最初の人とする。
				if (!isset($delay))
				{
					$delay = $char->delay;
					$NextChar = &$char;
					continue;
				}

				// キャラが今のディレイより多ければ交代
				if ($delay <= $char->delay)
				{
					//行動
					// もしキャラとディレイが同じなら50%で交代
					if ($delay == $char->delay)
					{
						if (mt_rand(0, 1)) continue;
					}
					$delay = $char->delay;
					$NextChar = &$char;
				}
			}
		}

		// 全員ディレイ減少
		$dif = $this->delay - $NextChar->delay; //戦闘基本ディレイと行動者のディレイの差分

		if ($dif >= 0)
		{
			foreach ($this->teams as $idx => &$data)
			{
				foreach ($data['team'] as &$char)
				{
					$char->Delay($dif);
				}
			}
		}

		return $NextChar;
	}

	/**
	 * キャラ全員の行動ディレイを初期化(=SPD)
	 */
	function DelayResetAll()
	{

		if (DELAY_TYPE === 0 || DELAY_TYPE === 1)
		{
			foreach ($this->teams as $idx => &$data)
			{
				foreach ($data['team'] as &$char)
				{
					$char->DelayReset();
				}
			}
		}
	}

	/**
	 * ディレイを計算して設定する
	 * 誰かのSPDが変化した場合呼び直す
	 * 技の使用等でSPDが変化した際に呼び出す
	 */
	function SetDelay()
	{
		if (DELAY_TYPE === 0)
		{
			/**
			 * SPDの最大値と合計を求める
			 */
			foreach ($this->teams as $idx => &$data)
			{
				foreach ($data['team'] as &$char)
				{
					$TotalSPD += $char->SPD;
					if ($MaxSPD < $char->SPD) $MaxSPD = $char->SPD;
				}
			}


			/**
			 * 平均SPD
			 */
			$AverageSPD = $TotalSPD / (count($this->teams[TEAM_0]['team']) + count($this->teams[TEAM_1]['team']));
			/**
			 * 基準delayとか
			 */
			$AveDELAY = $AverageSPD * DELAY;

			/**
			 * その戦闘の基準ディレイ
			 */
			$this->delay = $MaxSPD + $AveDELAY;

			/**
			 * falseにしないと毎回DELAYを計算し直してしまう。
			 */
			$this->ChangeDelay = false;
		}
		elseif (DELAY_TYPE === 1)
		{

		}
	}

	/**
	 * 戦闘の基準ディレイを再計算させるようにする。
	 * 使う場所は、技の使用でキャラのSPDが変化した際に使う。
	 * class.skill_effect.php で使用。
	 */
	function ChangeDelay()
	{
		if (DELAY_TYPE === 0)
		{
			$this->ChangeDelay = true;
		}
	}

	/**
	 * チームの名前を設定
	 */
	function SetTeamName($name1, $name2)
	{
		$this->teams[TEAM_0]['name'] = $name1;
		$this->teams[TEAM_1]['name'] = $name2;

		$this->teams[TEAM_0]['team']->team_name($name1);
		$this->teams[TEAM_1]['team']->team_name($name2);
	}


	/**
	 * お金を得る、一時的に変数に保存するだけ。
	 * class内にメソッド作れー
	 */
	function GetMoney($money, $team)
	{
		if (!$money) return false;
		$money = ceil($money * MONEY_RATE);

		echo ("{$this->teams[$team]['name']} Get " . HOF_Helper_Global::MoneyFormat($money) . ".<br />\n");
		$this->teams[$team]['money'] += $money;
	}

	/**
	 * ユーザーデータに得る合計金額を渡す
	 */
	function ReturnMoney()
	{
		return array($this->teams[TEAM_0]['money'], $this->teams[TEAM_1]['money']);
	}

	/**
	 * 魔方陣を追加する
	 */
	function MagicCircleAdd($team, $amount)
	{
		$this->teams[$team]['mc'] = HOF_Helper_Math::minmax($this->teams[$team]['mc'] + $amount, 0, 5);

		return true;
	}

	/**
	 * 魔方陣を削除する
	 */
	function MagicCircleDelete($team, $amount)
	{
		if ($this->teams[$team]['mc'] < $amount) return false;
		$this->teams[$team]['mc'] -= $amount;

		return true;
	}

}
