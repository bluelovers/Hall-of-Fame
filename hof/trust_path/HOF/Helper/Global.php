<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Global
{

	function UserAmount()
	{
		static $amount;

		if ($amount)
		{
			return $amount;
		}
		else
		{
			$amount = count(HOF_Class_File::glob(BASE_PATH_USER));
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

	function ShowResult($message, $add = null)
	{
		if ($add) $add = " " . $add;

		if (is_object($message) && method_exists($message, '__toString'))
		{
			$message = (string )$message;
		}
		elseif (is_array($message))
		{
			$message = implode("\n", $message);
		}

		if ($message)
		{
			print ('<div class="result' . $add . '">' . nl2br($message) . '</div>' . "\n");
		}
	}

	/**
	 * 赤い警告文でエラー表示
	 */
	function ShowError($message, $add = null)
	{
		if ($add) $add = " " . $add;

		if (is_object($message) && method_exists($message, '__toString'))
		{
			$message = (string )$message;
		}
		elseif (is_array($message))
		{
			$message = implode("\n", $message);
		}

		if ($message)
		{
			print ('<div class="error' . $add . '">' . nl2br($message) . '</div>' . "\n");
		}
	}

	function gc_date()
	{
		$_args = func_get_args();

		$_args[1] = ($_args[1] !== null ? $_args[1] : REQUEST_TIME);

		return HOF::date($_args[1], $_args[0]);

		$_args[1] = ($_args[1] ? $_args[1] : time()) + 8 * 3600;

		return call_user_func_array('date', $_args);
	}

	//	期限切れアカウントの一斉削除
	function DeleteAbandonAccount()
	{
		$list = HOF_Class_File::glob(BASE_PATH_USER);
		$now = time();

		// ユーザー一覧を取得する
		foreach ($list as $file)
		{
			if (!is_dir($file)) continue;
			$UserID = substr($file, strrpos($file, "/") + 1);
			$user = new HOF_Class_User($UserID, true);

			// 消されるユーザー
			if ($user->IsAbandoned())
			{

				// ランキングを読む
				if (!isset($Ranking))
				{
					//include_once (CLASS_RANKING);
					$Ranking = new HOF_Class_Ranking();
					$RankChange = false; // ランキングデータが変更されたか
				}

				// ランキングから消す
				if ($Ranking->DeleteRank($UserID))
				{
					$RankChange = true; // 変更された
				}

				self::RecordManage(self::gc_date("Y M d G:i:s", $now) . ": user " . $user->id . " deleted.");
				$user->DeleteUser(false); //ランキングからは消さないようにfalse
			}
			// 消されないユーザー
			else
			{
				$user->fpclose_all();
				unset($user);
			}
		}

		// 一通りユーザチェックが終わったのでランキングをどうするか
		if ($RankChange === true) $Ranking->fpsave();
		else
			if ($RankChange === false) $Ranking->fpclose();

		//print("<pre>".print_r($list,1)."</pre>");
	}

	//	定期的に管理する何か
	function RegularControl($value = null)
	{
		/*
		サーバが重(混み)そうな時間帯は後回しにする。
		PM 7:00 - AM 2:00 は処理しない。
		※時刻は or なのに注意！
		*/
		if (19 <= self::gc_date("H") || self::gc_date("H") <= 1) return false;

		$now = time();

		$fp = HOF_Class_File::fplock_file(CTRL_TIME_FILE, true);
		if (!$fp) return false;
		//$ctrltime	= file_get_contents(CTRL_TIME_FILE);
		$ctrltime = trim(fgets($fp, 1024));
		// 周期がまだなら終了
		if ($now < $ctrltime)
		{
			fclose($fp);
			unset($fp);
			return false;
		}

		// 管理の処理
		self::RecordManage(self::gc_date("Y M d G:i:s", $now) . ": auto regular control by {$value}.");

		self::DeleteAbandonAccount(); //その1 放棄ユーザの掃除

		// 定期管理が終わったら次の管理時刻を書き込んで終了する。
		HOF_Class_File::fpwrite_file($fp, $now + CONTROL_PERIOD);
		fclose($fp);
		unset($fp);
	}

	function RecordManage($string)
	{
		$file = MANAGE_LOG_FILE;

		$fp = @fopen($file, "r+") or die();
		$text = fread($fp, 2048);
		ftruncate($fp, 0);
		rewind($fp);
		fwrite($fp, $string . "\n" . $text);
	}

	/*
	*	入力された文字列を確認する
	*	返り値
	*	成功 = array(true,変換($string));
	*	失敗 = array(false,失敗理由);
	*/
	function CheckString($string, $maxLength = 16)
	{
		$string = trim($string);
		$string = stripslashes($string);
		if (is_numeric(strpos($string, "\t")))
		{
			return array(false, "不正な文字");
		}
		if (is_numeric(strpos($string, "\n")))
		{
			return array(false, "不正な文字");
		}
		if (!$string)
		{
			return array(false, "未入力");
		}
		$length = strlen($string);
		if (0 == $length || $maxLength < $length)
		{
			return array(false, "長すぎか短すぎる");
		}
		$string = htmlspecialchars($string, ENT_QUOTES);
		return array(true, $string);
	}

	//	端末を判断。
	function isMobile()
	{
		if (strstr($_SERVER['HTTP_USER_AGENT'], "DoCoMo"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "Vodafone"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "SoftBank"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "MOT-"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "J-PHONE"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "KDDI"))
		{
			//$env = 'ez';
			$env = 'ez';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "UP.Browser"))
		{
			$env = 'i';
		}
		elseif (strstr($_SERVER['HTTP_USER_AGENT'], "WILLCOM"))
		{
			$env = 'ez';
		}
		else
		{
			$env = 'pc';
		}
		return $env;
	}

}
