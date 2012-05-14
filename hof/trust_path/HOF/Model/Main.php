<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (GLOBAL_PHP);

class HOF_Model_Main extends HOF_Class_Array
{
	public $ip;
	public $user_name;

	protected static $instance;

	public static function &getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	function __destruct()
	{
		HOF::user()->fpclose_all();
	}

	function fpclose_all()
	{
		parent::fpclose_all();
	}

	function __construct()
	{

		if (!isset(self::$instance))
		{
			self::$instance = $this;
		}
		else
		{
			die('error!!');
		}

		parent::__construct();

		HOF::getInstance();

		$this->ip = HOF::ip();

		$this->user = &HOF::user();

		$this->user_name = &$this->user->id;

		ob_start();
		$this->Order();
		$content = ob_get_clean();

		HOF_Class_View::render(null, array(), 'layout/layout.body', $content)->output();
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

		if (true === $message = HOF::user()->CheckLogin())
		{
			if (HOF::user()->FirstLogin())
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
			Copy Right <a href="http://tekito.kanichat.com/">Tekito</a> 2007-2008. Fork (c) 2012 bluelovers<br>

			, <?= $debuginfo['ios'] ?> ios, <?= $debuginfo['umem'] ?>

		</div>
<?php

	}

	//	変数の表示
	function Debug()
	{
		if (DEBUG) print ("<pre>" . print_r(get_object_vars($this), 1) . "</pre>");
	}

	//	上部に表示されるメニュー。
	//	ログインしてる人用とそうでない人。
	function MyMenu()
	{
		if ($this->user->name && $this->user->islogin)
		{ // ログインしてる人用
			print ('<div id="menu">' . "\n");
			//print('<span class="divide"></span>');//区切り
			print ('<a href="' . INDEX . '">Top</a><span class="divide"></span>');
			print ('<a href="?hunt">Hunt</a><span class="divide"></span>');
			print ('<a href="?item">Item</a><span class="divide"></span>');
			print ('<a href="?town">Town</a><span class="divide"></span>');
			print ('<a href="?setting">Setting</a><span class="divide"></span>');
			print ('<a href="?log">Log</a><span class="divide"></span>');
			if (BBS_OUT) print ('<a href="' . BBS_OUT . '">BBS</a><span class="divide"></span>' . "\n");
			print ('</div><div id="menu2">' . "\n");


?>
<div style="width:100%">
	<div style="width:33%;float:left">
		<?=

			$this->user->name


?>
	</div>
	<div style="width:67%;float:right">
		<div style="width:50%;float:left">
			<span class="bold">Funds</span>:
			<?=

			HOF_Helper_Global::MoneyFormat($this->user->money)


?>
		</div>
		<div style="width:50%;float:right">
			<span class="bold">Time</span>:
			<?=

			floor($this->user->time)


?>
			/
			<?=

			MAX_TIME


?>
		</div>
	</div>
	<div class="c-both">
	</div>
</div>
<?php

			print ('</div>');
		}
		else
			if (!$this->user->name && $this->user->islogin)
			{ // 初回ログインの人
				print ('<div id="menu">');
				print ("First login. Thankyou for the entry.");
				print ('</div><div id="menu2">');
				print ("fill the blanks. てきとーに埋めてください。");
				print ('</div>');
			}
			else
			{ //// ログアウト状態の人、来客用の表示
				print ('<div id="menu">');
				print ('<a href="' . INDEX . '">トップ</a><span class="divide"></span>' . "\n");
				print ('<a href="?newgame">新規</a><span class="divide"></span>' . "\n");
				print ('<a href="?manual">ルールとマニュアル</a><span class="divide"></span>' . "\n");
				print ('<a href="?gamedata=job">ゲームデータ</a><span class="divide"></span>' . "\n");
				print ('<a href="?log">戦闘ログ</a><span class="divide"></span>' . "\n");
				if (BBS_OUT) print ('<a href="' . BBS_OUT . '">総合BBS</a><span class="divide"></span>' . "\n");

				print ('</div><div id="menu2">');
				print ("Welcome to [ " . TITLE . " ]");
				print ('</div>');
			}
	}

}


?>