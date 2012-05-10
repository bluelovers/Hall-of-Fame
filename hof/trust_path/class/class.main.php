<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}



class main
{



	//	変数の表示
	function Debug()
	{
		if (DEBUG) print ("<pre>" . print_r(get_object_vars($this), 1) . "</pre>");
	}

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