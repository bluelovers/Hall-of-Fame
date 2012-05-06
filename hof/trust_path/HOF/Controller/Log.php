<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Log extends HOF_Class_Controller
{

	function _main_action_update()
	{
		if ($_POST["updatetext"])
		{
			$update = htmlspecialchars($_POST["updatetext"], ENT_QUOTES);
			$update = stripslashes($update);
		}
		else  $update = @file_get_contents(UPDATE);

		if ($_POST["updatepass"] == UP_PASS && $_POST["updatetext"])
		{
			$fp = fopen(UPDATE, "w");
			$text = htmlspecialchars($_POST["updatetext"], ENT_QUOTES);
			$text = stripslashes($text);
			flock($fp, 2);
			fputs($fp, $text);
			fclose($fp);
		}

		$this->output['update'] = $update;
		$this->output['updatepass'] = ($_POST["updatepass"] == UP_PASS) ? true : false;
	}

	/**
	 * 戦闘ログの表示
	 */
	function _main_action_default()
	{
		$logs = array();

		$idx = $_SERVER["QUERY_STRING"];

		if (in_array($idx, array(
			'clog',
			'ulog',
			'rlog',
			)))
		{
			$this->output->full_log = true;

			$map = array();

			if ($idx == 'ulog')
			{
				$map[] = LOG_BATTLE_UNION;
			}
			elseif ($idx == 'rlog')
			{
				$map[] = LOG_BATTLE_RANK;
			}
			else
			{
				$map[] = LOG_BATTLE_NORMAL;
			}
		}
		else
		{
			$map = array(
				LOG_BATTLE_NORMAL,
				LOG_BATTLE_UNION,
				LOG_BATTLE_RANK,
				);
		}

		// common

		foreach ($map as $_k)
		{
			$log = game_core::glob($_k);
			foreach (array_reverse($log) as $file)
			{
				$logs[$_k][] = $this->BattleLogDetail($file);
				$limit++;
				if (!$this->output->full_log && 30 <= $limit)
				{
					break;
				}
			}
		}

		$this->output->idx = $idx;

		$this->output['logs'] = $logs;
	}

	function _main_action_log()
	{
		$map = array(
			'log' => LOG_BATTLE_NORMAL,
			'clog' => LOG_BATTLE_NORMAL,
			'ulog' => LOG_BATTLE_UNION,
			'rlog' => LOG_BATTLE_RANK,
			);

		foreach ($map as $_k => $_v)
		{
			if ($_GET[$_k])
			{
				break;
			}
		}

		if ($_k == 'log' || $_k == 'clog')
		{
			$_k = 'clog';
		}

		$this->output->idx = $_k;

		list($this->output->log, $this->output->time) = $this->ShowBattleLog($_GET[$_k], $_v);

		$this->options['escapeHtml'] = false;
	}

	/**
	 * 戦闘ログの詳細を表示(リンク)
	 */
	function BattleLogDetail($log)
	{
		$fp = fopen($log, "r");

		// 数行だけ読み込む。
		$time = fgets($fp); //開始時間 1行目
		$team = explode("<>", fgets($fp)); //チーム名 2行目
		$number = explode("<>", trim(fgets($fp))); //人数 3行目
		$avelv = explode("<>", trim(fgets($fp))); //平均レベル 4行目
		$win = trim(fgets($fp)); // 勝利チーム 5行目
		$act = trim(fgets($fp)); // 総行動数 6行目
		fclose($fp);

		$date = gc_date("m/d H:i:s", substr($time, 0, 10));
		// 勝利チームによって色を分けて表示

		return array(

			$time,
			$team,
			$number,
			$avelv,
			$win,
			$act,
			$date,

			);
	}

	/**
	 * 戦闘ログを回覧する
	 */
	function ShowBattleLog($no, $type = false)
	{
		if ($type == LOG_BATTLE_RANK)
		{
			$file = LOG_BATTLE_RANK . $no . ".dat";
		}
		elseif ($type == LOG_BATTLE_UNION)
		{
			$file = LOG_BATTLE_UNION . $no . ".dat";
		}
		else
		{
			$file = LOG_BATTLE_NORMAL . $no . ".dat";
		}

		if (!file_exists($file))
		{
			//ログが無い
			return "log doesnt exists";
		}

		$log = file($file);
		// ログの何行目から書き出すか?
		$row = 6;
		$time = substr($log[0], 0, 10);

		//print('<table style="width:100%;text-align:center" class="break"><tr><td>'."\n");

		return array($log, $time);
	}

}
