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
					$this->LoadUserItem(); //アイテムデータ読む
					//$this->ItemProcess();
					$this->fpCloseAll();
					$this->ItemShow();
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






	//


	//


	//


	function ItemProcess()
	{
	}


	//
	function ItemShow()
	{


?>
<div style="margin:15px">
<h4>Items</h4>
<div style="margin:0 20px">
	<?php

		if ($this->item)
		{

			$goods = new HOF_Class_Item_Style_List();
			$goods->SetID("my");
			$goods->SetName("type");
			// JSを使用しない。
			if ($this->no_JS_itemlist) $goods->NoJS();
			//$goods->ListTable("<table>");
			//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
			foreach ($this->item as $no => $val)
			{
				$item = HOF_Model_Data::getItemData($no);
				$string = HOF_Class_Item::ShowItemDetail($item, $val, 1) . "<br />";
				//$string	= "<tr><td>".$no."</td><td>".HOF_Class_Item::ShowItemDetail($item,$val,1)."</td></tr>";
				$goods->AddItem($item, $string);
			}
			print ($goods->GetJavaScript("list"));
			print ($goods->ShowSelect());
			print ('<div id="list">' . $goods->ShowDefault() . '</div>');
		}
		else
		{
			print ("No items.");
		}
		print ("</div></div>");
	}

	//

	//
	function ShopProcess()
	{
		switch (true)
		{
			case ($_POST["partjob"]):
				if ($this->WasteTime(100))
				{
					$this->GetMoney(500);
					HOF_Helper_Global::ShowResult("働いて " . HOF_Helper_Global::MoneyFormat(500) . " げっとした!", "margin15");
					return true;
				}
				else
				{
					HOF_Helper_Global::ShowError("時間が無い。働くなんてもったいない。", "margin15");
					return false;
				}
			case ($_POST["shop_buy"]):
				$ShopList = HOF_Model_Data::getShopList(); //売ってるものデータ
				if ($_POST["item_no"] && in_array($_POST["item_no"], $ShopList))
				{
					if (ereg("^[0-9]", $_POST["amount"]))
					{
						$amount = (int)$_POST["amount"];
						if ($amount == 0) $amount = 1;
					}
					else
					{
						$amount = 1;
					}
					$item = HOF_Model_Data::getItemData($_POST["item_no"]);
					$need = $amount * $item["buy"]; //購入に必要なお金
					if ($this->TakeMoney($need))
					{ // お金を引けるかで判定。
						$this->AddItem($_POST["item_no"], $amount);
						$this->SaveUserItem();
						if (1 < $amount)
						{
							$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
							HOF_Helper_Global::ShowResult("{$img}{$item[name]} を{$amount}個 購入した (" . HOF_Helper_Global::MoneyFormat($item["buy"]) . " x{$amount} = " . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
							return true;
						}
						else
						{
							$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
							HOF_Helper_Global::ShowResult("{$img}{$item[name]} を購入した (" . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
							return true;
						}
					}
					else
					{ //資金不足
						HOF_Helper_Global::ShowError("資金不足(Need " . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
						return false;
					}
				}
				break;
			case ($_POST["shop_sell"]):
				if ($_POST["item_no"] && $this->item[$_POST["item_no"]])
				{
					if (ereg("^[0-9]", $_POST["amount"]))
					{
						$amount = (int)$_POST["amount"];
						if ($amount == 0) $amount = 1;
					}
					else
					{
						$amount = 1;
					}
					// 消した個数(超過して売られるのも防ぐ)
					$DeletedAmount = $this->DeleteItem($_POST["item_no"], $amount);
					$item = HOF_Model_Data::getItemData($_POST["item_no"]);
					$price = (isset($item["sell"]) ? $item["sell"] : round($item["buy"] * SELLING_PRICE));
					$this->GetMoney($price * $DeletedAmount);
					$this->SaveUserItem();
					if ($DeletedAmount != 1) $add = " x{$DeletedAmount}";
					$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
					HOF_Helper_Global::ShowResult("{$img}{$item[name]}{$add} を " . HOF_Helper_Global::MoneyFormat($price * $DeletedAmount) . " で売った", "margin15");
					return true;
				}
				break;
		}
	}

	//
	function ShopShow($message = NULL)
	{


?>
	<div style="margin:15px">
		<?=

		HOF_Helper_Global::ShowError($message)


?>
		<h4>Goods List</h4>
		<div style="margin:0 20px">
			<?php

		$ShopList = HOF_Model_Data::getShopList(); //売ってるものデータ

		$goods = new HOF_Class_Item_Style_List();
		$goods->SetID("JS_buy");
		$goods->SetName("type_buy");
		// JSを使用しない。
		if ($this->no_JS_itemlist) $goods->NoJS();
		foreach ($ShopList as $no)
		{
			$item = HOF_Model_Data::getItemData($no);
			$string = '<input type="radio" name="item_no" value="' . $no . '" class="vcent">';
			$string .= "<span style=\"padding-right:10px;width:10ex\">" . HOF_Helper_Global::MoneyFormat($item["buy"]) . "</span>" . HOF_Class_Item::ShowItemDetail($item, false, 1) . "<br />";
			$goods->AddItem($item, $string);
		}
		print ($goods->GetJavaScript("list_buy"));
		print ($goods->ShowSelect());

		print ('<form action="?shop" method="post">' . "\n");
		print ('<div id="list_buy">' . $goods->ShowDefault() . '</div>' . "\n");
		print ('<input type="submit" class="btn" name="shop_buy" value="Buy">' . "\n");
		print ('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)<br />' . "\n");
		print ('<input type="hidden" name="shop_buy" value="1">');
		print ('</form></div>' . "\n");

		print ("<h4>My Items<a name=\"sell\"></a></h4>\n"); //所持物売る
		print ('<div style="margin:0 20px">' . "\n");
		if ($this->item)
		{
			$goods = new HOF_Class_Item_Style_List();
			$goods->SetID("JS_sell");
			$goods->SetName("type_sell");
			// JSを使用しない。
			if ($this->no_JS_itemlist) $goods->NoJS();
			foreach ($this->item as $no => $val)
			{
				$item = HOF_Model_Data::getItemData($no);
				$price = (isset($item["sell"]) ? $item["sell"] : round($item["buy"] * SELLING_PRICE));
				$string = '<input type="radio" class="vcent" name="item_no" value="' . $no . '">';
				$string .= "<span style=\"padding-right:10px;width:10ex\">" . HOF_Helper_Global::MoneyFormat($price) . "</span>" . HOF_Class_Item::ShowItemDetail($item, $val, 1) . "<br />";
				$head = '<input type="radio" name="item_no" value="' . $no . '" class="vcent">' . HOF_Helper_Global::MoneyFormat($item["buy"]);
				$goods->AddItem($item, $string);
			}
			print ($goods->GetJavaScript("list_sell"));
			print ($goods->ShowSelect());

			print ('<form action="?shop" method="post">' . "\n");
			print ('<div id="list_sell">' . $goods->ShowDefault() . '</div>' . "\n");
			print ('<input type="submit" class="btn" name="shop_sell" value="Sell">');
			print ('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)' . "\n");
			print ('<input type="hidden" name="shop_sell" value="1">');
			print ('</form>' . "\n");
		}
		else
		{
			print ("No items");
		}
		print ("</div>\n");
		/*
		if($this->item) {
		foreach($this->item as $no => $val) {
		$item	= HOF_Model_Data::getItemData($no);
		$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
		print('<input type="radio" class="vcent" name="item_no" value="'.$no.'">');
		print(HOF_Helper_Global::MoneyFormat($price));
		print("&nbsp;&nbsp;&nbsp;{$val}x");
		HOF_Class_Item::ShowItemDetail($item);
		print("<br>");
		}
		} else
		print("No items.<br>");
		print('Amount <input type="text" name="amount" style="width:50px" class="text vcent">(input if 2 or more)<br />'."\n");
		print('<input type="submit" class="btn vcent" name="shop_sell" value="Sell">');
		print('<input type="hidden" name="shop_sell" value="1">');
		print('</form>');*/


?>
			<form action="?shop" method="post">
				<h4>Work</h4>
				<div style="margin:0 20px">
				店でアルバイトしてお金を得ます...<br />
				<input type="submit" class="btn" name="partjob" value="Work at Shop">
				Get
				<?=

		HOF_Helper_Global::MoneyFormat("500")


?>
				for 100Time.
			</form>
		</div>
	</div>
	<?php

	}


	//


	//


	//


	//


	////////// Show ////
	/*
	* ShowCharStat
	* ShowHunt
	* ShowItem
	* ShowShop
	* ShowRank
	* ShowRecruit
	* ShowSetting
	*/


	//

	////////////////////


	//

	//


	//


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


	//

	//	普通の1行掲示板
	function bbs01()
	{
		if (!BBS_BOTTOM_TOGGLE) return false;
		$file = BBS_BOTTOM;


?>
<div style="margin:15px">
<h4>one line bbs</h4>
バグ報告,バランスについての意見とかはこちらでどうぞ。
<form action="?bbs" method="post">
	<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
	<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?php

		if (!file_exists($file)) return false;
		$log = file($file);
		if ($_POST["message"] && strlen($_POST["message"]) < 121)
		{
			$_POST["message"] = htmlspecialchars($_POST["message"], ENT_QUOTES);
			$_POST["message"] = stripslashes($_POST["message"]);

			$name = ($this->name ? "<span class=\"bold\">{$this->name}</span>" : "名無し");
			$message = $name . " > " . $_POST["message"];
			if ($this->UserColor) $message = "<span style=\"color:{$this->UserColor}\">" . $message . "</span>";
			$message .= " <span class=\"light\">(" . gc_date("Mj G:i") . ")</span>\n";
			array_unshift($log, $message);
			while (150 < count($log)) // ログ保存行数あ
 					array_pop($log);
			HOF_Class_File::WriteFile($file, implode(null, $log));
		}
		foreach ($log as $mes) print (nl2br($mes));
		print ('</div>');
	}
	//end of class
	////////////////////
}


?>