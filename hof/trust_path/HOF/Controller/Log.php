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

		// common

		foreach(array(
			LOG_BATTLE_NORMAL,
			LOG_BATTLE_UNION,
			LOG_BATTLE_RANK,
		) as $_k)
		{
			$log = game_core::glob($_k);
			foreach (array_reverse($log) as $file)
			{
				$logs[$_k][] = $this->BattleLogDetail($file);
				$limit++;
				if (30 <= $limit)
				{
					break;
				}
			}
		}

		$this->output['logs'] = $logs;
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

}
