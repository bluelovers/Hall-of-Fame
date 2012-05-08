<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Global
{

	/**
	 * 戦闘ログの詳細を表示(リンク)
	 */
	function HOF_Helper_Global::BattleLogDetail($log)
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

	function UserAmount()
	{
		static $amount;

		if ($amount)
		{
			return $amount;
		}
		else
		{
			$amount = count(game_core::glob(USER));
			return $amount;
		}
	}

	/**
	 * お金の表示方式
	 */
	function MoneyFormat($number, $pre = '$&nbsp;')
	{
		return $pre . number_format($number);
	}

	function ShowResult($message, $add = false)
	{
		if ($add) $add = " " . $add;

		if (is_object($message) && method_exists($message, '__toString'))
		{
			$message = (string )$message;
		}
		elseif (is_array($message))
		{
			$message = implode('<p>', $message);
		}

		if ($message)
		{
			print ('<div class="result' . $add . '">' . $message . '</div>' . "\n");
		}
	}

	/**
	 * 赤い警告文でエラー表示
	 */
	function ShowError($message, $add = false)
	{
		if ($add) $add = " " . $add;

		if (is_object($message) && method_exists($message, '__toString'))
		{
			$message = (string )$message;
		}
		elseif (is_array($message))
		{
			$message = implode('<p>', $message);
		}

		if ($message)
		{
			print ('<div class="error' . $add . '">' . $message . '</div>' . "\n");
		}
	}

}
