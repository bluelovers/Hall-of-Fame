<?php
	//include("./setting.php");
	if(!defined("ADMIN_PASSWORD"))
		exit(1);
	/*
	* ログイン
	*/
	if($_POST["pass"] == ADMIN_PASSWORD || $_COOKIE["adminPass"] == ADMIN_PASSWORD) {
		setcookie ("adminPass", $_POST["pass"]?$_POST["pass"]:$_COOKIE["adminPass"],time()+60*30);
		$login = true;
	}

	/*
	* ログアウト
	*/
	if($_POST["logout"]) {
		setcookie ("adminPass");
		$login = false;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="prototype.js"></script>
<title>HoF - admin</title>
<style TYPE="text/css">
<!--
form{
margin: 0;
padding: 0;
}
-->
</style>
</head>
<body>
<?php
if($login) {

	/*
	* function dump
	*/
	if(!function_exists("dump")) {
		function dump($var) {
			print("<pre>".print_r($var,1)."</pre>\n");
		}
	}

	/*
	* changeData(データに変更を加える)
	*/
	function changeData($file,$text) {
		$fp = @fopen($file,"w") or die("file lock error!");
		flock($fp,LOCK_EX);
		fwrite($fp,stripcslashes($text));
		flock($fp,LOCK_UN);
		fclose($fp);
		print("<span style=\"font-weight:bold\">データ修正</span>");
	}

	/*
	* メニュー
	*/
print <<< MENU
<form action="?" method="post">
<a href="?">TOP</a>
<a href="?menu=user">USER</a>
<a href="?menu=data">DATA</a>
<a href="?menu=other">OTHER</a>
<input type="submit" value="logout" name="logout" />
</form>
<hr>
MENU;

	/*
	* ユーザ一覧
	*/
	if($_GET["menu"] === "user") {
		$userList = glob(USER."*");
		print("<p>ALL_USER</p>\n");
		foreach($userList as $user) {
			print('<form action="?" method="post">');
			print('<input type="submit" name="UserData" value=" + ">');
			print('<input type="hidden" name="userID" value="'.basename($user).'">');
			print(basename($user)."\n");
			print("</form>\n");
		}
	}

	/*
	* ユーザデータ
	*/
	else if($_POST["UserData"]) {
		$userFileList = glob(USER.$_POST["userID"]."/*");
		print("<p>USER :".$_POST["userID"]."</p>\n");
		foreach($userFileList as $file) {
			print('<form action="?" method="post">');
			print('<input type="submit" name="UserFileDet" value=" + ">');
			print('<input type="hidden" name="userFile" value="'.basename($file).'">');
			print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
			print(basename($file)."\n");
			print("</form>\n");
		}
		print('<br><form action="?" method="post">');
		print('ユーザ削除 :<input type="text" name="deletePass" size="">');
		print('<input type="submit" name="deleteUser" value="削除">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* ユーザデータ削除
	*/
	else if($_POST["deleteUser"]) {
		if($_POST["deletePass"] == ADMIN_PASSWORD) {
			include(GLOBAL_PHP);
			include(CLASS_USER);
			$userD = new user($_POST["userID"]);
			$userD->DeleteUser();
			print($_POST["userID"]."を削除しました。");
		} else {
			print("パスワードちがう。");
		}
	}

	/*
	* ユーザデータ(詳細)
	*/
	else if($_POST["UserFileDet"]) {
		$file = USER.$_POST["userID"]."/".$_POST["userFile"];
		// データの修正
		if($_POST["changeData"]) {
			$fp = @fopen($file,"w") or die("file lock error!");
			flock($fp,LOCK_EX);
			fwrite($fp,$_POST["fileData"]);
			flock($fp,LOCK_UN);
			fclose($fp);
			print("データ修正");
		}

		print("<p>$file</p>\n");
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="userFile" value="'.$_POST["userFile"].'">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print('<input type="hidden" name="UserFileDet" value="1">');
		print("</form>\n");
		print('<form action="?" method="post">');
		print('<input type="submit" name="UserData" value="戻る">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* データ集計
	*/
	else if($_GET["menu"] === "data") {
print <<< DATA
<br>
<form action="?" method="post">
<ul>
<li><input type="submit" name="UserDataDetail" value=" + ">(※1)ユーザデータの集計を表示</li>
<li><input type="submit" name="UserCharDetail" value=" + ">(※1)キャラデータの集計を表示</li>
<li><input type="submit" name="ItemDataDetail" value=" + ">(※1)アイテムデータの集計を表示</li>
<li><input type="submit" name="UserIpShow" value=" + ">(※1)ユーザのIPを表示</li>
<li><input type="submit" name="searchBroken" value=" + ">(※1)壊れてる可能性のあるデータを探す(微妙)<input type="text" name="brokenSize" value="100" size=""></li>
<li><input type="submit" name="adminBattleLog" value=" + ">戦闘ログの管理</li>
<li><input type="submit" name="adminAuction" value=" + ">オークションの管理</li>
<li><input type="submit" name="adminRanking" value=" + ">ランキングの管理</li>
<li><input type="submit" name="adminTown" value=" + ">町広場の管理</li>
<li><input type="submit" name="adminRegister" value=" + ">ユーザ登録情報の管理</li>
<li><input type="submit" name="adminUserName" value=" + ">ユーザ名の管理</li>
<li><input type="submit" name="adminUpDate" value=" + ">更新情報の管理</li>
<li><input type="submit" name="adminAutoControl" value=" + ">自動管理のログ</li>
</ul>
<p>(※1)とてもヘビーな処理です。<br>
データが増えるほど処理も増えます。
</p>
</form>
DATA;
	}

	/*
	* データ集計(ユーザデータ)
	*/
	else if($_POST["UserDataDetail"]) {
		include(GLOBAL_PHP);
		include(CLASS_USER);
		$userFileList = glob(USER."*");
		foreach($userFileList as $user) {
			$user = new user(basename($user,".dat"));
			$totalMoney += $user->money;
		}
		print("UserAmount :".count($userFileList)."<br>\n");
		print("TotalMoney :".MoneyFormat($totalMoney)."<br>\n");
		print("AveMoney :".MoneyFormat($totalMoney/count($userFileList))."<br>\n");
	}

	/*
	* データ集計(キャラデータ)
	*/
	else if($_POST["UserCharDetail"]) {
		include(GLOBAL_PHP);
		$userFileList = glob(USER."*");
		foreach($userFileList as $user) {
			$userDir = glob($user."/*");
			foreach($userDir as $fileName) {
				if(!is_numeric(basename($fileName,".dat"))) continue;
				$charData = ParseFile($fileName);
				$charAmount++;
				$totalLevel += $charData["level"];
				$totalStr += $charData["str"];
				$totalInt += $charData["int"];
				$totalDex += $charData["dex"];
				$totalSpd += $charData["spd"];
				$totalLuk += $charData["luk"];
				if($charData["gender"] === "0")
					$totalMale++;
				else if($charData["gender"] === "1")
					$totalFemale++;
				$totalJob[$charData["job"]]++;

				//print($charData["name"]."<br>");
			}
		}
		print("CharAmount :".$charAmount."<br>\n");
		print("AveLevel :".$totalLevel/$charAmount."<br>\n");
		print("AveStr :".$totalStr/$charAmount."<br>\n");
		print("AveInt :".$totalInt/$charAmount."<br>\n");
		print("AveDex :".$totalDex/$charAmount."<br>\n");
		print("AveSpd :".$totalSpd/$charAmount."<br>\n");
		print("AveLuk :".$totalLuk/$charAmount."<br>\n");
		print("Male :{$totalMale}(".($totalMale/$charAmount*100)."%)<br>\n");
		print("Female :{$totalFemale}(".($totalFemale/$charAmount*100)."%)<br>\n");

		print("--- Job<br>\n");
		arsort($totalJob);
		include(DATA_JOB);
		foreach($totalJob as $job => $amount) {
			$jobData = LoadJobData($job);
			print($job."({$jobData[name_male]},{$jobData[name_female]})"." : ".$amount."(".($amount/$charAmount*100)."%)<br>\n");
		}
	}

	/*
	* データ集計(アイテムデータ)
	*/
	else if($_POST["ItemDataDetail"]) {
		include(GLOBAL_PHP);
		$userFileList = glob(USER."*");
		$userAmount = count($userFileList);
		$items = array();
		foreach($userFileList as $user) {
			if(!$data = ParseFile($user."/item.dat"));
			foreach($data as $itemno => $amount)
				$items[$itemno] += $amount;
		}
		foreach($items as $itemno => $amount) {
			if(strlen($itemno) != 4) continue;
			print($itemno." : ".$amount."(Ave:".$amount/$userAmount.")<br>");
		}
	}

	/*
	* ユーザのIP表示
	*/
	else if($_POST["UserIpShow"]) {
		include(GLOBAL_PHP);
		$userFileList = glob(USER."*");
		$ipList = array();
		foreach($userFileList as $user) {
			$file = $user."/data.dat";
			if(!$data = ParseFile($file)) continue;
			$html .= "<tr><td>".$data["id"]."</td><td>".$data["name"]."</td><td>".$data["ip"]."</td></tr>\n";
			$ipList[$data["ip"]?$data["ip"]:"*UnKnown"]++;
		}
		// 重複リスト
		print("<p>IP重複リスト</p>\n");
		foreach($ipList as $ip => $amount) {
			if(1 < $amount)
				print("$ip : $amount<br>\n");
		}
		print("<table border=\"1\">\n");
		print($tags = "<tr><td>ID</td><td>NAME</td><td>IP</td></tr>\n");
		print($html);
		print("</table>\n");
	}

	/*
	* 壊れてる可能性のあるデータを探す
	*/
	else if($_POST["searchBroken"]) {
		print("<p>壊れている可能性のあるファイル<br>\n");
		$baseSize = $_POST["brokenSize"]?(int)$_POST["brokenSize"]:100;
		print("※{$baseSize}byte 以下のファイルを探しただけ(アイテムデータは除く).</p>");
		$userFileList = glob(USER."*");
		foreach($userFileList as $user) {
			$userDir = glob($user."/*");
			if(filesize($user."/data.dat") < $baseSize)
				print($user."/data.dat"."(".filesize($user."/data.dat").")"."<br>\n");
			foreach($userDir as $fileName) {
				if(!is_numeric(basename($fileName,".dat"))) continue;
				if(filesize($fileName) < $baseSize)
					print($fileName."(".filesize($fileName).")<br>\n");
			}
		}
	}

	/*
	* 戦闘ログの管理
	*/
	else if($_POST["adminBattleLog"]) {
		if($_POST["deleteLogCommon"]) {
			$dir = LOG_BATTLE_NORMAL;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>通常戦闘ログを削除しました。</p>\n");
		} else if($_POST["deleteLogUnion"]) {
			$dir = LOG_BATTLE_UNION;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>ユニオン戦闘ログを削除しました。</p>\n");
		} else if($_POST["deleteLogRanking"]) {
			$dir = LOG_BATTLE_RANK;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>ランキング戦闘ログを削除しました。</p>\n");
		}
print <<< DATA
<br>
<form action="?" method="post">
<input type="hidden" name="adminBattleLog" value="1">
<ul>
<li><input type="submit" name="deleteLogCommon" value=" + ">通常戦闘ログを全部削除する</li>
<li><input type="submit" name="deleteLogUnion" value=" + ">ユニオン戦闘ログを全部削除する</li>
<li><input type="submit" name="deleteLogRanking" value=" + ">ランキングログを全部削除する</li>
</ul>
</form>
DATA;
	}

	/*
	* オークションの管理
	*/
	else if($_POST["adminAuction"]) {
		$file = AUCTION_ITEM;
		print("<p>オークションの管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminAuction" value="1">');
		print("</form>\n");
	}

	/*
	* ランキングの管理
	*/
	else if($_POST["adminRanking"]) {
		$file = RANKING;
		print("<p>ランキングの管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminRanking" value="1">');
		print("</form>\n");
	}

	/*
	* 町広場の管理
	*/
	else if($_POST["adminTown"]) {
		$file = BBS_TOWN;
		print("<p>町広場の管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminTown" value="1">');
		print("</form>\n");
	}

	/*
	* ユーザ登録情報の管理
	*/
	else if($_POST["adminRegister"]) {
		$file = REGISTER;
		print("<p>ユーザ登録情報の管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminRegister" value="1">');
		print("</form>\n");
	}

	/*
	* ユーザ名の管理
	*/
	else if($_POST["adminUserName"]) {
		$file = USER_NAME;
		print("<p>ユーザ名の管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminUserName" value="1">');
		print("</form>\n");
	}

	/*
	* 更新情報の管理
	*/
	else if($_POST["adminUpDate"]) {
		$file = UPDATE;
		print("<p>更新情報の管理</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminUpDate" value="1">');
		print("</form>\n");
	}

	/*
	* 自動管理のログ
	*/
	else if($_POST["adminAutoControl"]) {
		$file = MANAGE_LOG_FILE;
		print("<p>自動管理のログ</p>\n");
		// データの修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="修正">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminAutoControl" value="1">');
		print("</form>\n");
	}

	/*
	* OTHER
	*/
	else if($_GET["menu"] === "other") {
print("
<p>そのた</p>\n
<ul>\n
<li><a href=\"".ADMIN_DIR."list_item.php\">アイテム一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."list_enchant.php\">装備効果一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."list_job.php\">職業一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."list_judge.php\">判定一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."list_monster.php\">モンスター一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."list_skill3.php\">スキル一覧</a></li>\n
<li><a href=\"".ADMIN_DIR."set_action2.php\">パターン設定機</a></li>\n
</ul>\n
");
	}

	/*
	* それ以外
	*/
	else {
print("
<p>基本設定</p>\n
<table border=\"1\">\n
<tr><td>定義</td><td>説明</td><td>値</td></tr>
<tr><td>TITLE</td><td>タイトル</td><td>".TITLE."</td></tr>\n
<tr><td>MAX_TIME</td><td>最大Time</td><td>".MAX_TIME."Time</td></tr>\n
<tr><td>TIME_GAIN_DAY</td><td>1日で増えるTime</td><td>".TIME_GAIN_DAY."Time</td></tr>\n
<tr><td>CONTROL_PERIOD</td><td>自動管理周期</td><td>".CONTROL_PERIOD."s(".(CONTROL_PERIOD/60/60)."hour)"."</td></tr>\n
<tr><td>RECORD_IP</td><td>IPを記録するか(1=ON)</td><td>".RECORD_IP."</td></tr>\n
<tr><td>SELLING_PRICE</td><td>売値</td><td>".SELLING_PRICE."</td></tr>\n
<tr><td>EXP_RATE</td><td>経験値倍率</td><td>x".EXP_RATE."</td></tr>\n
<tr><td>MONEY_RATE</td><td>お金倍率</td><td>x".MONEY_RATE."</td></tr>\n
<tr><td>AUCTION_MAX</td><td>最大出品数</td><td>".AUCTION_MAX."</td></tr>\n
<tr><td>JUDGE_LIST_AUTO_LOAD</td><td>条件判定のリストを自動取得(1=自動)</td><td>".JUDGE_LIST_AUTO_LOAD."</td></tr>\n
<tr><td>AUCTION_TOGGLE</td><td>オークションON/OFF(1=ON)</td><td>".AUCTION_TOGGLE."</td></tr>\n
<tr><td>AUCTION_EXHIBIT_TOGGLE</td><td>出品ON/OFF(1=ON)</td><td>".AUCTION_EXHIBIT_TOGGLE."</td></tr>\n
<tr><td>RANK_TEAM_SET_TIME</td><td>ランキングのチーム設定周期</td><td>".RANK_TEAM_SET_TIME."s(".(RANK_TEAM_SET_TIME/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_LOSE</td><td>負けたときの待ち時間</td><td>".RANK_BATTLE_NEXT_LOSE."s(".(RANK_BATTLE_NEXT_LOSE/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_WIN</td><td>勝利した場合の待ち時間</td><td>".RANK_BATTLE_NEXT_WIN."s</td></tr>\n
<tr><td>NORMAL_BATTLE_TIME</td><td>モンスターとの戦いで消費する時間</td><td>".NORMAL_BATTLE_TIME."Time</td></tr>\n
<tr><td>MAX_BATTLE_LOG</td><td>戦闘ログ保存数(通常モンスター)</td><td>".MAX_BATTLE_LOG."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_UNION</td><td>戦闘ログ保存数(ユニオン)</td><td>".MAX_BATTLE_LOG_UNION."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_RANK</td><td>戦闘ログ保存数(ランキング)</td><td>".MAX_BATTLE_LOG_RANK."</td></tr>\n
<tr><td>UNION_BATTLE_TIME</td><td>ユニオン戦で消費する時間</td><td>".UNION_BATTLE_TIME."Time</td></tr>\n
<tr><td>UNION_BATTLE_NEXT</td><td>ユニオン戦の待ち時間</td><td>".UNION_BATTLE_NEXT."s</td></tr>\n
<tr><td>BBS_BOTTOM_TOGGLE</td><td>下部メニューにある一行掲示板(1=ON)</td><td>".BBS_BOTTOM_TOGGLE."</td></tr>\n
</table>\n
");
	}

print <<< ADMIN
<hr>
<p>この とってつけたような管理機能を過信しないでください。<br>
ユーザ数が0だったりすると一部エラーが出る可能性有り。
</p>
ADMIN;


} else {
print <<< LOGIN
<form action="?" method="post">
PASS :<input type="text" name="pass" />
<input type="submit" value="submit" />
</form>
LOGIN;
}

?>
</body>
</html>