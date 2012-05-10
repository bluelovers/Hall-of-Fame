<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

include (GLOBAL_PHP);

class main extends HOF_Class_User
{

	var $islogin = false;


	//
	function __construct()
	{
		$this->SessionSwitch();
		$this->Set_ID_PASS();
		ob_start();
		$this->Order();
		$content = ob_get_contents();
		ob_end_clean();

		$this->Head();
		print ($content);
		$this->Debug();
		//$this->ShowSession();
		$this->Foot();
	}


	//
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

	//	変数の表示
	function Debug()
	{
		if (DEBUG) print ("<pre>" . print_r(get_object_vars($this), 1) . "</pre>");
	}


	//	セッション情報を表示する。
	function ShowSession()
	{
		echo "this->id:$this->id<br>";
		echo "this->pass:$this->pass<br>";
		echo "SES[id]:$_SESSION[id]<br>";
		echo "SES[pass]:$_SESSION[pass]<br>";
		echo "SES[pass]:" . $this->CryptPassword($_SESSION[pass]) . "(crypted)<br>";
		echo "CK[NO]:$_COOKIE[NO]<br>";
		echo "SES[NO]:" . session_id();
		dump($_COOKIE);
		dump($_SESSION);
	}


	//	ログインした時間を設定する
	function RenewLoginTime()
	{
		$this->login = time();
	}

	//	pass と id を設定する
	function Set_ID_PASS()
	{
		$id = ($_POST["id"]) ? $_POST["id"] : $_GET["id"];
		//if($_POST["id"]) {
		if ($id)
		{
			$this->id = $id; //$_POST["id"];
			// ↓ログイン処理した時だけ
			if (HOF_Controller_Game::is_registered($_POST["id"]))
			{
				$_SESSION["id"] = $this->id;
			}
		}
		else
			if ($_SESSION["id"]) $this->id = $_SESSION["id"];

		$pass = ($_POST["pass"]) ? $_POST["pass"] : $_GET["pass"];
		//if($_POST["pass"])
		if ($pass) $this->pass = $pass; //$_POST["pass"];
		else
			if ($_SESSION["pass"]) $this->pass = $_SESSION["pass"];

		if ($this->pass) $this->pass = $this->CryptPassword($this->pass);
	}


	//	保存されているセッション番号を変更する。
	function SessionSwitch()
	{
		// session消滅の時間(?)
		// how about "session_set_cookie_params()"?
		session_cache_expire(COOKIE_EXPIRE / 60);
		if ($_COOKIE["NO"]) //クッキーに保存してあるセッションIDのセッションを呼び出す
 				session_id($_COOKIE["NO"]);

		session_start();
		if (!SESSION_SWITCH) //switchしないならここで終了
 				return false;
		//print_r($_SESSION);
		//dump($_SESSION);
		$OldID = session_id();
		$temp = serialize($_SESSION);

		session_regenerate_id();
		$NewID = session_id();
		setcookie("NO", $NewID, time() + COOKIE_EXPIRE);
		$_COOKIE["NO"] = $NewID;

		session_id($OldID);
		session_start();

		if ($_SESSION):
			//	session_destroy();//Sleipnirだとおかしい...?(最初期)
			//	unset($_SESSION);//こっちは大丈夫(やっぱりこれは駄目かも)(修正後)
			//結局,セッションをforeachでループして1個づつunset(2007/9/14 再修正)
			foreach ($_SESSION as $key => $val) unset($_SESSION["$key"]);
		endif;

		session_id($NewID);
		session_start();
		$_SESSION = unserialize($temp);
	}


	//

	//


	//	上部に表示されるメニュー。
	//	ログインしてる人用とそうでない人。
	function MyMenu()
	{
		if ($this->name && $this->islogin)
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

			$this->name


?>
	</div>
	<div style="width:67%;float:right">
		<div style="width:50%;float:left">
			<span class="bold">Funds</span>:
			<?=

			HOF_Helper_Global::MoneyFormat($this->money)


?>
		</div>
		<div style="width:50%;float:right">
			<span class="bold">Time</span>:
			<?=

			floor($this->time)


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
			if (!$this->name && $this->islogin)
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


	//	HTML開始部分
	function Head()
	{


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php

		$this->HtmlScript();


?>
	<title>
	<?=

		TITLE


?>
	</title>
</head>
<body>
	<a name="top"></a>
	<div id="main_frame">
		<div id="title">
			<img src="<?php

		echo HOF_Class_Icon::getImageUrl('title03', './static/image/');


?>">
		</div>
		<?php

		$this->MyMenu();


?>
		<div id="contents">
			<?php

	}


	//	スタイルシートとか。
	function HtmlScript()
	{


?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<link rel="stylesheet" href="./static/style/basis.css" type="text/css">
			<link rel="stylesheet" href="./static/style/style.css" type="text/css">
			<script type="text/javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script>
			<script type="text/javascript" src="./static/js/jquery-core.js"></script>
			<style>

.flip-h {
    -moz-transform: scaleX(-1);
    -o-transform: scaleX(-1);
    -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    filter: FlipH;
    -ms-filter: "FlipH";
}

</style>
			<?php

	}

}


?>