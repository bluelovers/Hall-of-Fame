<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (CLASS_MAIN);

class HOF_Model_Main extends main
{

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
				ShowUpDate();
				return true;
			case ($_SERVER["QUERY_STRING"] === "bbs"):
				$this->bbs01();
				return true;
			case ($_SERVER["QUERY_STRING"] === "manual"):
			case ($_SERVER["QUERY_STRING"] === "manual2"):
			case ($_SERVER["QUERY_STRING"] === "tutorial"):
				HOF_Class_Controller::newInstance('manual', $_SERVER["QUERY_STRING"])->main();
				;
				return true;

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
			case ($_GET["gamedata"]):
				ShowGameData();
				return true;
			case ($_GET["log"]):
				ShowBattleLog($_GET["log"]);
				return true;
			case ($_GET["ulog"]):
				ShowBattleLog($_GET["ulog"], "UNION");
				return true;
			case ($_GET["rlog"]):
				ShowBattleLog($_GET["rlog"], "RANK");
				return true;
		}
	}

}
