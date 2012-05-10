<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Main extends HOF_Class_Main
{

	/**
	 * HTML終了部分
	 */
	function Foot()
	{

		$unit = array('b','kb','mb','gb','tb','pb');

		$size = memory_get_usage();
		$size = bcdiv($size, pow(1024, ($i = floor(log($size,1024)))), 4).' '.$unit[$i];

		$ios = function_exists('get_included_files') ? count(get_included_files()) : 0;
		$umem = function_exists('memory_get_usage') ? $size : 0;
		$debuginfo = array(
			//'time' => number_format(($mtime[1] + $mtime[0] - $discuz_starttime), 6),
			'ios' => $ios,
			'umem' => $umem,
			);
?>
		</div>
		<div id="foot">
			<a href="?update">UpDate</a> -
			<?php

		if (BBS_BOTTOM_TOGGLE) print ('<a href="?bbs">BBS</a> - ' . "\n");


?>
			<a href="?manual">Manual</a> - <a href="?tutorial">Tutorial</a> - <a href="?gamedata=job">GameData</a> - <a href="#top">Top</a><br>
			Copy Right <a href="http://tekito.kanichat.com/">Tekito</a> 2007-2008.<br>

			, <?= $debuginfo['ios'] ?> ios, <?= $debuginfo['umem'] ?>

		</div>
	</div>
</body>
</html>
<?php

	}

	function FirstLogin($over = false)
	{
		static $flag;

		if (!isset($flag) || $over)
		{
			$flag = !HOF_Class_Controller::getInstance('game', __FUNCTION__ )->main()->_main_stop();
		}

		return $flag;
	}

	/**
	 * ログインしたのか、しているのか、ログアウトしたのか。
	 */
	function CheckLogin($over = false)
	{
		static $flag;

		if (!isset($flag) || $over)
		{
			HOF_Class_Controller::getInstance('game', __FUNCTION__ )->main()->_main_stop();

			$flag = $this->islogin;
		}

		return $flag;
	}

	function __destruct()
	{
		$this->fpCloseAll();

		HOF_Class_File::fpCloseAll();
	}

	function fpCloseAll()
	{
		parent::fpCloseAll();
	}

	function __construct()
	{
		HOF::getInstance();

		parent::__construct();
	}

	function Order()
	{
		// ログイン処理する前に処理するもの
		// まだユーザデータ読んでません
		switch (true)
		{
			case ($_GET["menu"] === "auction"):

				HOF_Class_Controller::newInstance('auction')->main()->_main_stop();
				return 0;
				break;

			case ($_GET["menu"] === "rank"):
			case ($_SERVER["QUERY_STRING"] === "rank"):

				HOF_Class_Controller::newInstance('rank')->main()->_main_stop();
				return 0;
				break;
		}

		if (true === $message = $this->CheckLogin())
		{
			if ($this->FirstLogin())
			{
				return 0;
			}

			switch (true)
			{
				case ($this->OptionOrder()):
					return false;
				case ($_POST["delete"]):
					if (!HOF_Class_Controller::getInstance('game', 'DeleteMyData')->main()->_main_stop())
					{
						return 0;
					}
					// 設定
				case ($_SERVER["QUERY_STRING"] === "setting"):
					HOF_Class_Controller::getInstance('game', $_SERVER["QUERY_STRING"])->main();
					return 0;
					// 狩場
				case ($_SERVER["QUERY_STRING"] === "hunt"):
					HOF_Class_Controller::newInstance('Battle', $_SERVER["QUERY_STRING"])->main();
					return 0;
					// 街
				case ($_SERVER["QUERY_STRING"] === "town"):
					HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();
					return 0;
					// シミュれ
				case ($_SERVER["QUERY_STRING"] === "simulate"):
					HOF_Class_Controller::newInstance('Battle', $_SERVER["QUERY_STRING"])->main();
					return 0;
					// ユニオン
				case ($_GET["union"]):
					HOF_Class_Controller::newInstance('Battle', 'union')->main();
					return 0;
				case ($_GET["common"]):
					HOF_Class_Controller::newInstance('Battle', 'common')->main();
					return 0;
					// アイテム一覧
				case ($_SERVER["QUERY_STRING"] === "item"):
					HOF_Class_Controller::newInstance('item')->main();
					return 0;
				case ($_GET["menu"] === "refine"):
				case ($_GET["menu"] === "create"):
					HOF_Class_Controller::newInstance('Smithy', $_GET["menu"])->main();
					return 0;
				case ($_GET["menu"] === "buy"):
				case ($_GET["menu"] === "sell"):
				case ($_GET["menu"] === "work"):
					HOF_Class_Controller::newInstance('shop', $_GET["menu"])->main();
					return 0;
				case ($_SERVER["QUERY_STRING"] === "recruit"):
					HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();
					return 0;
				case ($_GET["char"]):
				default:
					HOF_Class_Controller::newInstance('char')->main();
					return 0;
			}
		}
		else
		{
			$this->fpCloseAll();

			switch (true)
			{
				case ($this->OptionOrder()):
					return false;
				case ($_POST["Make"]):
				case ($_SERVER["QUERY_STRING"] === "newgame"):
					HOF_Class_Controller::getInstance('game', "newgame")->main();
					return false;
				default:
					HOF_Class_Controller::getInstance('game', "login")->main();
			}
		}
	}

	/**
	 * UpDate,BBS,Manual等
	 */
	function OptionOrder()
	{
		$this->fpCloseAll();

		switch (true)
		{
			case ($_SERVER["QUERY_STRING"] === "update"):
				HOF_Class_Controller::newInstance('log', $_SERVER["QUERY_STRING"])->main();
				return true;
			case ($_SERVER["QUERY_STRING"] === "bbs"):
				HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();
				return true;
			case ($_SERVER["QUERY_STRING"] === "manual"):
			case ($_SERVER["QUERY_STRING"] === "manual2"):
			case ($_SERVER["QUERY_STRING"] === "tutorial"):
				HOF_Class_Controller::newInstance('manual', $_SERVER["QUERY_STRING"])->main();
				return true;
			case ($_SERVER["QUERY_STRING"] === "log"):
			case ($_SERVER["QUERY_STRING"] === "clog"):
			case ($_SERVER["QUERY_STRING"] === "ulog"):
			case ($_SERVER["QUERY_STRING"] === "rlog"):
				HOF_Class_Controller::newInstance('log')->main();
				return true;
			case ($_GET["log"]):
			case ($_GET["clog"]):
			case ($_GET["ulog"]):
			case ($_GET["rlog"]):
				HOF_Class_Controller::newInstance('log', 'log')->main();
				return true;
			case ($_GET["gamedata"]):
				HOF_Class_Controller::newInstance('gamedata', $_GET["gamedata"])->main();
				return true;

		}
	}

}


?>