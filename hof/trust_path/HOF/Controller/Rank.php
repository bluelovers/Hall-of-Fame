<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Rank extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_Item_Auction
	 */
	var $Ranking;

	function _init()
	{
		$this->Ranking = new HOF_Class_Ranking();

		$this->user = &HOF_Model_Main::getInstance();
	}

	function _main_before()
	{
		if (true === $message = $this->user->CheckLogin())
		{

			if ($this->user->FirstLogin())
			{
				$this->_main_stop(true);

				return false;
			}

			$this->user->CharDataLoadAll();

			$RankProcess = $this->RankProcess($this->Ranking);

			// チーム再設定の残り時間計算
			$this->_cache['now'] = time();

			$this->output->left_setting = ($this->_cache['now'] - $this->user->rank_set_time) < RANK_TEAM_SET_TIME;

			if ($this->output->left_setting)
			{
				$left = RANK_TEAM_SET_TIME - ($this->_cache['now'] - $this->user->rank_set_time);
				$day = floor($left / 3600 / 24);
				$hour = floor($left / 3600) % 24;
				$min = floor(($left % 3600) / 60);
				$sec = floor(($left % 3600) % 60);

				$this->output->left_left = $left;
				$this->output->left_day = $day;
				$this->output->left_hour = $hour;
				$this->output->left_min = $min;
				$this->output->left_sec = $sec;
			}

			if ($RankProcess === "BATTLE" || $RankProcess === true)
			{
				$this->user->SaveData();
			}

			if ($RankProcess != "BATTLE")
			{
				$this->_RankShow();
			}

			$this->user->fpCloseAll();

		}
		else
		{
			$this->_main_stop(true);

			$c = HOF_Class_Controller::newInstance('game', 'login')->_main_exec('login', $message ? $message : null);
		}

	}

	function _error($s, $a = null)
	{
		$this->output->error[] = array($s, $a);
		$this->error[] = $s;
	}

	function _msg_result()
	{
		$this->output->msg_result[] = array($s, $a);
		$this->msg_result[] = $s;
	}

	function RankProcess(&$Ranking)
	{
		$this->input->ChallengeRank = HOF::$input->post->ChallengeRank;
		$this->input->SetRankTeam = HOF::$input->post->SetRankTeam;

		// RankBattle
		if ($this->input->ChallengeRank)
		{
			if (!$this->user->party_rank)
			{
				$this->_error("チームが設定されていません", "margin15");
				return false;
			}

			$result = $this->user->CanRankBattle();
			if (is_array($result))
			{
				$this->_error("待機時間がまだ残ってます", "margin15");
				return false;
			}

			/*
			$BattleResult = 0;//勝利
			$BattleResult = 1;//敗北
			$BattleResult = "d";//引分
			*/
			//list($message,$BattleResult)	= $Rank->Challenge(&$this->user);
			$Result = $this->Ranking->Challenge(&$this->user);

			//if($Result === "Battle")
			//	$this->user->RankRecord($BattleResult,"CHALLENGE",false);

			/*
			// 勝敗によって次までの戦闘の時間を設定する
			//勝利
			if($BattleResult === 0) {
			$this->user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);

			//敗北
			} else if($BattleResult === 1) {
			$this->user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			//引分け
			} else if($BattleResult === "d") {
			$this->user->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			}
			*/

			return $Result; // 戦闘していれば $Result = "Battle";
		}

		// ランキング用のチーム登録
		if ($this->input->SetRankTeam)
		{

			if ($this->output->left_setting)
			{
				$this->_error("チーム再設定まで あと 残り {$this->output->left_day}日 と {$this->output->left_hour}時間 {$this->output->left_min}分 {$this->output->left_sec}秒", "margin15");

				return false;
			}

			foreach ($this->user->char as $key => $val)
			{
				//チェックされたやつリスト
				if (HOF::$input->post["char_" . $key]) $checked[] = $key;
			}

			// 設定キャラ数が多いか少なすぎる
			if (count($checked) == 0 || 5 < count($checked))
			{
				$this->_error("チーム人数は 1人以上 5人以下 でないといけない", "margin15");
				return false;
			}

			/*
			$this->user->party_rank = implode("<>", $checked);
			*/
			$this->user->party_rank = $checked;
			$this->user->rank_set_time = $this->_cache['now'];

			$this->_msg_result("チーム設定 完了", "margin15");

			return true;
		}
	}

	function _ShowRanking()
	{
		$this->Ranking->ShowRanking(0, 4);
	}

	function _ShowRankingRange()
	{
		$this->Ranking->ShowRankingRange($this->user->id, 5);
	}

	function _RankShow()
	{
		$this->output->show = true;

		if ($this->output->left_setting)
		{
			$this->output->disable = $disable;
		}

		$this->output->reset = floor(RANK_TEAM_SET_TIME / (60 * 60));

		/**
		 * 挑戦できるかどうか(時間の経過で)
		 */
		$CanRankBattle = $this->user->CanRankBattle();

		if ($CanRankBattle !== true)
		{
			$this->output->CanRankBattle_time = $CanRankBattle[0] . ":" . sprintf("%02d", $CanRankBattle[1]) . ":" . sprintf("%02d", $CanRankBattle[2]);

			$this->output->disableRB = " disabled";
		}

	}

}


?>