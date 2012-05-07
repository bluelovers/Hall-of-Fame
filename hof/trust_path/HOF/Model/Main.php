<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Main extends HOF_Class_Main
{

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
				ShowBattleLog($_GET["log"]);
				return true;
			case ($_GET["ulog"]):
				ShowBattleLog($_GET["ulog"], "UNION");
				return true;
			case ($_GET["rlog"]):
				ShowBattleLog($_GET["rlog"], "RANK");
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
