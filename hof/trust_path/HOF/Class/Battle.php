<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
class HOF_Class_Battle extends battle
{

	function __construct($team0, $team1)
	{

		$team0 = HOF_Class_Battle_Team::newInstance($team0);
		$team1 = HOF_Class_Battle_Team::newInstance($team1);

		parent::__construct($team0, $team1);

		$this->objs['view'] = new HOF_Class_Battle_View(&$this);

		$this->teams[0]['team'] = &$this->team0;
		$this->teams[1]['team'] = &$this->team1;

		$this->teams[0]['mc'] = &$this->team0_mc;
		$this->teams[1]['mc'] = &$this->team1_mc;

		$this->teams[0]['name'] = &$this->team0_name;
		$this->teams[1]['name'] = &$this->team1_name;

		$this->teams[0]['no'] = TEAM_0;
		$this->teams[1]['no'] = TEAM_1;

		$this->teams[0]['team']->update();
		$this->teams[1]['team']->update();
	}

	function outputImage()
	{
		$output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)->setBg($this->BackGround)->setTeams($this->team1, $this->team0)->setMagicCircle($this->team1_mc, $this->team0_mc)->exec();

		echo $output;
	}

	function SkillEffect($skill, $skill_no, &$user, &$target)
	{
		if (!isset($this->objs['SkillEffect']))
		{
			$this->objs['SkillEffect'] = new HOF_Class_Skill_Effect(&$this);
		}

		return $this->objs['SkillEffect']->SkillEffect($skill, $skill_no, &$user, &$target);
	}

	function UseSkill($skill_no, &$JudgedTarget, &$My, &$MyTeam, &$Enemy)
	{
		$_key = 'Battle_Skill';

		if (!isset($this->objs[$_key]))
		{
			$this->objs[$_key] = new HOF_Class_Battle_Skill(&$this);
		}

		return $this->objs[$_key]->UseSkill($skill_no, &$JudgedTarget, &$My, &$MyTeam, &$Enemy);
	}

	/**
	 * 魔方陣を追加する
	 *
	 * @param bool|$del 魔方陣を削除する
	 */
	function changeMagicCircle($team, $amount, $del = 0)
	{
		$amount *= ($del ? -1 : 1);

		if ($team == TEAM_0)
		{
			$team_mc = &$this->team0_mc;
		}
		else
		{
			$team_mc = &$this->team1_mc;
		}

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
	function CountDead($who)
	{
		return HOF_Class_Battle_Team::CountDead($who);
	}

	/**
	 * 全体の死者数を数える...(ネクロマンサしか使ってない?)
	 */
	function CountDeadAll()
	{
		$count = 0;

		$count += HOF_Class_Battle_Team::CountDead($this->team0);
		$count += HOF_Class_Battle_Team::CountDead($this->team1);

		return $count;
	}

	/**
	 * 戦闘にキャラクターを途中参加させる。
	 *
	 * @param HOF_Class_Char|$user
	 * @param HOF_Class_Char|$add
	 */
	function JoinCharacter($user, $add)
	{
		foreach ($this->teams as &$team)
		{
			foreach ($team['team'] as $char)
			{
				if ($user === $char)
				{
					$team['team']->addChar($add, $team['no']);
					$this->ChangeDelay();

					return true;
				}
			}
		}
	}

	/**
	 * 戦闘記録を保存する
	 */
	function RecordLog($type = false)
	{
		if ($type == "RANK")
		{
			$file = LOG_BATTLE_RANK;
			$log = HOF_Class_File::glob(LOG_BATTLE_RANK);
			$logAmount = MAX_BATTLE_LOG_RANK;
		}
		else
			if ($type == "UNION")
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

	/**
	 * キャラの行動
	 */
	function Action(&$char)
	{
		// $char->judge が設定されてなければ飛ばす
		if (empty($char->pattern))
		{
			$char->delay = $char->SPD;
			return false;
		}

		// チーム0の人はセルの右側に
		// チーム1の人は左側に 行動内容と結果 を表示する
		echo ("<tr><td class=\"ttd2\">\n");
		if ($char->team === TEAM_0) echo ("</td><td class=\"ttd1\">\n");
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
				} while ($char->pattern[$JudgeKey]['action'] == 9000 && $char->pattern[$JudgeKey]['judge']);

				//$return	= HOF_Class_Battle_Judge::MultiFactJudge($Keys,$char,$MyTeam,$EnemyTeam);
				$return = HOF_Class_Battle_Judge::MultiFactJudge($Keys, $char, $this);

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

		if ($skill)
		{
			$this->UseSkill($skill, $return, $char, $MyTeam, $EnemyTeam);
			// 行動できなかった場合の処理
		}
		else
		{
			echo ($char->Name(bold) . " sunk in thought and couldn't act.<br />(No more patterns)<br />\n");
			$char->DelayReset();
		}

		//ディレイリセット
		//if($ret	!== "DontResetDelay")
		//	$char->DelayReset;

		//echo $char->name." ".$skill."<br>";//確認用
		//セルの終わり
		if ($char->team === TEAM_1) echo ("</td><td class=\"ttd1\">&nbsp;\n");
		echo ("</td></tr>\n");
	}

	/**
	 * 次の行動は誰か(又、詠唱中の魔法が発動するのは誰か)
	 * リファレンスを返す
	 */
	function &NextActerNew()
	{

		// 次の行動まで最も距離が短い人を探す。
		$nextDis = 1000;
		foreach ($this->team0 as $key => $char)
		{
			if ($char->STATE === STATE_DEAD) continue;
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
			if ($char->STATE === STATE_DEAD) continue;
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

//		debug($key, $char->name, $nextDis, $NextChar);
//		exit();

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



}
