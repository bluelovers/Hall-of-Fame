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

	/**
	 * UpDate,BBS,Manual等
	 */
	function OptionOrder()
	{
		$this->fpCloseAll();

		switch (true)
		{
			case ($_SERVER["QUERY_STRING"] === "rank"):
				RankAllShow();
				return true;
			case ($_SERVER["QUERY_STRING"] === "update"):
				/*
				ShowUpDate();
				*/
				HOF_Class_Controller::newInstance('log', $_SERVER["QUERY_STRING"])->main();
				return true;
			case ($_SERVER["QUERY_STRING"] === "bbs"):
				/*
				$this->bbs01();
				*/
				HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();
				return true;
			case ($_SERVER["QUERY_STRING"] === "manual"):
			case ($_SERVER["QUERY_STRING"] === "manual2"):
			case ($_SERVER["QUERY_STRING"] === "tutorial"):
				HOF_Class_Controller::newInstance('manual', $_SERVER["QUERY_STRING"])->main();
				;
				return true;

				/*
				case ($_SERVER["QUERY_STRING"] === "log"):
				ShowLogList();
				return true;
				case ($_SERVER["QUERY_STRING"] === "clog"):
				LogShowCommon();
				return true;
				case ($_SERVER["QUERY_STRING"] === "ulog"):
				LogShowUnion();
				return true;
				case ($_SERVER["QUERY_STRING"] === "rlog"):
				LogShowRanking();
				return true;
				case ($_GET["log"]):
				HOF_Helper_Global::getLogBattle($_GET["log"]);
				return true;
				case ($_GET["ulog"]):
				HOF_Helper_Global::getLogBattle($_GET["ulog"], "UNION");
				return true;
				case ($_GET["rlog"]):
				HOF_Helper_Global::getLogBattle($_GET["rlog"], "RANK");
				return true;
				case ($_GET["gamedata"]):
				ShowGameData();
				return true;
				*/
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