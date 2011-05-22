<?php
	//include("./setting.php");
	if(!defined("ADMIN_PASSWORD"))
		exit(1);
	/*
	* 登錄
	*/
	if($_POST["pass"] == ADMIN_PASSWORD || $_COOKIE["adminPass"] == ADMIN_PASSWORD) {
		setcookie ("adminPass", $_POST["pass"]?$_POST["pass"]:$_COOKIE["adminPass"],time()+60*30);
		$login = true;
	}

	/*
	* 註銷
	*/
	if($_POST["logout"]) {
		setcookie ("adminPass");
		$login = false;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="prototype.js"></script>
<title>HoF - 管理後台</title>
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
	* changeData(更改數據)
	*/
	function changeData($file,$text) {
		$fp = @fopen($file,"w") or die("file lock error!");
		flock($fp,LOCK_EX);
		fwrite($fp,stripcslashes($text));
		flock($fp,LOCK_UN);
		fclose($fp);
		print("<span style=\"font-weight:bold\">數據修改</span>");
	}

	/*
	* 菜單
	*/
print <<< MENU
<form action="?" method="post">
<a href="?">管理首頁</a>
<a href="?menu=user">用戶管理</a>
<a href="?menu=data">數據管理</a>
<a href="?menu=other">其他</a>
<input type="submit" value="註銷" name="logout" />
</form>
<hr>
MENU;

	/*
	* 用戶列表
	*/
	if($_GET["menu"] === "user") {
		$userList = glob(USER."*");
		print("<p>全部用戶</p>\n");
		foreach($userList as $user) {
			print('<form action="?" method="post">');
			print('<input type="submit" name="UserData" value=" 管理 ">');
			print('<input type="hidden" name="userID" value="'.basename($user).'">');
			print(basename($user)."\n");
			print("</form>\n");
		}
	}

	/*
	* 用戶數據
	*/
	else if($_POST["UserData"]) {
		$userFileList = glob(USER.$_POST["userID"]."/*");
		print("<p>USER :".$_POST["userID"]."</p>\n");
		foreach($userFileList as $file) {
			print('<form action="?" method="post">');
			print('<input type="submit" name="UserFileDet" value=" 管理 ">');
			print('<input type="hidden" name="userFile" value="'.basename($file).'">');
			print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
			print(basename($file)."\n");
			print("</form>\n");
		}
		print('<br><form action="?" method="post">');
		print('刪除用戶:<input type="text" name="deletePass" size="">');
		print('<input type="submit" name="deleteUser" value="刪除">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* 用戶數據刪除
	*/
	else if($_POST["deleteUser"]) {
		if($_POST["deletePass"] == ADMIN_PASSWORD) {
			include(GLOBAL_PHP);
			include(CLASS_USER);
			$userD = new user($_POST["userID"]);
			$userD->DeleteUser();
			print($_POST["userID"]."被刪除。");
		} else {
			print("沒有密碼。");
		}
	}

	/*
	* 用戶數據(詳細)
	*/
	else if($_POST["UserFileDet"]) {
		$file = USER.$_POST["userID"]."/".$_POST["userFile"];
		// 數據修改
		if($_POST["changeData"]) {
			$fp = @fopen($file,"w") or die("file lock error!");
			flock($fp,LOCK_EX);
			fwrite($fp,$_POST["fileData"]);
			flock($fp,LOCK_UN);
			fclose($fp);
			print("數據修改");
		}

		print("<p>$file</p>\n");
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="userFile" value="'.$_POST["userFile"].'">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print('<input type="hidden" name="UserFileDet" value="1">');
		print("</form>\n");
		print('<form action="?" method="post">');
		print('<input type="submit" name="UserData" value="放棄">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* 數據匯總
	*/
	else if($_GET["menu"] === "data") {
print <<< DATA
<br>
<form action="?" method="post">
<ul>
<li><input type="submit" name="UserDataDetail" value=" 管理 ">(※1)用戶數據匯總表示</li>
<li><input type="submit" name="UserCharDetail" value=" 管理 ">(※1)人物數據匯總表示</li>
<li><input type="submit" name="ItemDataDetail" value=" 管理 ">(※1)道具數據匯總表示</li>
<li><input type="submit" name="UserIpShow" value=" 管理 ">(※1)用戶IP表示</li>
<li><input type="submit" name="searchBroken" value=" 管理 ">(※1)有可能是已損壞的數據<input type="text" name="brokenSize" value="100" size=""></li>
<li><input type="submit" name="adminBattleLog" value=" 管理 ">戰鬥記錄管理</li>
<li><input type="submit" name="adminAuction" value=" 管理 ">拍賣管理</li>
<li><input type="submit" name="adminRanking" value=" 管理 ">排名管理</li>
<li><input type="submit" name="adminTown" value=" 管理 ">廣場管理</li>
<li><input type="submit" name="adminRegister" value=" 管理 ">用戶登錄數據管理</li>
<li><input type="submit" name="adminUserName" value=" 管理 ">用戶名管理</li>
<li><input type="submit" name="adminUpDate" value=" 管理 ">更新數據管理</li>
<li><input type="submit" name="adminAutoControl" value=" 管理 ">自動管理記錄</li>
</ul>
<p>(※1)將會比較消耗性能。<br>
甚至超過增加的數據處理。
</p>
</form>
DATA;
	}

	/*
	* 數據匯總(用戶數據)
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
	* 數據匯總(人物數據)
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
		print("人物總數:".$charAmount."<br>\n");
		print("平均等級 :".$totalLevel/$charAmount."<br>\n");
		print("平均str :".$totalStr/$charAmount."<br>\n");
		print("平均int :".$totalInt/$charAmount."<br>\n");
		print("平均dex :".$totalDex/$charAmount."<br>\n");
		print("平均spd :".$totalSpd/$charAmount."<br>\n");
		print("平均luk :".$totalLuk/$charAmount."<br>\n");
		print("男 :{$totalMale}(".($totalMale/$charAmount*100)."%)<br>\n");
		print("女 :{$totalFemale}(".($totalFemale/$charAmount*100)."%)<br>\n");
		print("--- 職業<br>\n");
		arsort($totalJob);
		include(DATA_JOB);
		foreach($totalJob as $job => $amount) {
			$jobData = LoadJobData($job);
			print($job."({$jobData[name_male]},{$jobData[name_female]})"." : ".$amount."(".($amount/$charAmount*100)."%)<br>\n");
		}
	}

	/*
	* 數據匯總(道具數據)
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
			print($itemno." : ".$amount."(平均:".$amount/$userAmount.")<br>");
		}
	}

	/*
	* 用戶IP
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
		// 重複列表
		print("<p>IP重複列表</p>\n");
		foreach($ipList as $ip => $amount) {
			if(1 < $amount)
				print("$ip : $amount<br>\n");
		}
		print("<table border=\"1\">\n");
		print($tags = "<tr><td>ID</td><td>名字</td><td>IP</td></tr>\n");
		print($html);
		print("</table>\n");
	}

	/*
	* 有可能是已損壞的數據
	*/
	else if($_POST["searchBroken"]) {
		print("<p>可能會損壞文件<br>\n");
		$baseSize = $_POST["brokenSize"]?(int)$_POST["brokenSize"]:100;
		print("※{$baseSize}byte 以下的文件搜索(道具數據除外).</p>");
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
	* 戰鬥記錄管理
	*/
	else if($_POST["adminBattleLog"]) {
		if($_POST["deleteLogCommon"]) {
			$dir = LOG_BATTLE_NORMAL;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>通常戰鬥記錄刪除。</p>\n");
		} else if($_POST["deleteLogUnion"]) {
			$dir = LOG_BATTLE_UNION;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>BOSS戰鬥記錄刪除。</p>\n");
		} else if($_POST["deleteLogRanking"]) {
			$dir = LOG_BATTLE_RANK;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>排行戰鬥記錄刪除。</p>\n");
		}
print <<< DATA
<br>
<form action="?" method="post">
<input type="hidden" name="adminBattleLog" value="1">
<ul>
<li><input type="submit" name="deleteLogCommon" value=" 管理 ">通常戰鬥記錄全部刪除</li>
<li><input type="submit" name="deleteLogUnion" value=" 管理 ">BOSS戰鬥記錄全部刪除</li>
<li><input type="submit" name="deleteLogRanking" value=" 管理 ">排名記錄全部刪除</li>
</ul>
</form>
DATA;
	}

	/*
	* 拍賣管理
	*/
	else if($_POST["adminAuction"]) {
		$file = AUCTION_ITEM;
		print("<p>拍賣管理</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminAuction" value="1">');
		print("</form>\n");
	}

	/*
	* 排名管理
	*/
	else if($_POST["adminRanking"]) {
		$file = RANKING;
		print("<p>排名管理</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminRanking" value="1">');
		print("</form>\n");
	}

	/*廣場管理	*/
	else if($_POST["adminTown"]) {
		$file = BBS_TOWN;
		print("<p>廣場管理</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminTown" value="1">');
		print("</form>\n");
	}

	/*
	* 用戶登錄數據管理
	*/
	else if($_POST["adminRegister"]) {
		$file = REGISTER;
		print("<p>用戶登錄數據管理</p>\n");
		// 數據修正
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminRegister" value="1">');
		print("</form>\n");
	}

	/*	* 用戶名管理
	*/
	else if($_POST["adminUserName"]) {
		$file = USER_NAME;
		print("<p>用戶名管理</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminUserName" value="1">');
		print("</form>\n");
	}

	/*
	* 更新情報管理
	*/
	else if($_POST["adminUpDate"]) {
		$file = UPDATE;
		print("<p>更新情報管理</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminUpDate" value="1">');
		print("</form>\n");
	}

	/*
	* 自動管理記錄
	*/
	else if($_POST["adminAutoControl"]) {
		$file = MANAGE_LOG_FILE;
		print("<p>自動管理記錄</p>\n");
		// 數據修改
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");		print('<input type="submit" name="changeData" value="修改">');
		print('<input type="submit" value="更新">');
		print('<input type="hidden" name="adminAutoControl" value="1">');
		print("</form>\n");
	}

	/*
	* OTHER
	*/
	else if($_GET["menu"] === "other") {
print("
<p>管理</p>\n
<ul>\n
<li><a href=\"".ADMIN_DIR."list_item.php\">道具列表</a></li>\n
<li><a href=\"".ADMIN_DIR."list_enchant.php\">裝備效果列表</a></li>\n
<li><a href=\"".ADMIN_DIR."list_job.php\">職業列表</a></li>\n
<li><a href=\"".ADMIN_DIR."list_judge.php\">判定列表</a></li>\n
<li><a href=\"".ADMIN_DIR."list_monster.php\">怪物列表</a></li>\n
<li><a href=\"".ADMIN_DIR."list_skill3.php\">技能列表</a></li>\n
<li><a href=\"".ADMIN_DIR."set_action2.php\">行動模式設定機</a></li>\n
</ul>\n
");
	}

	/*
	* 其他的
	*/
	else {
print("<p>基本設定</p>\n
<table border=\"1\">\n
<tr><td>定義</td><td>說明</td><td>值</td></tr>
<tr><td>TITLE</td><td>標題</td><td>".TITLE."</td></tr>\n
<tr><td>MAX_TIME</td><td>最大Time</td><td>".MAX_TIME."Time</td></tr>\n
<tr><td>TIME_GAIN_DAY</td><td>每天所給的Time</td><td>".TIME_GAIN_DAY."Time</td></tr>\n
<tr><td>CONTROL_PERIOD</td><td>自動管理週期</td><td>".CONTROL_PERIOD."s(".(CONTROL_PERIOD/60/60)."hour)"."</td></tr>\n
<tr><td>RECORD_IP</td><td>IP記錄(1=ON)</td><td>".RECORD_IP."</td></tr>\n
<tr><td>SELLING_PRICE</td><td>賣值</td><td>".SELLING_PRICE."</td></tr>\n
<tr><td>EXP_RATE</td><td>經驗值倍率</td><td>x".EXP_RATE."</td></tr>\n
<tr><td>MONEY_RATE</td><td>掉錢倍率</td><td>x".MONEY_RATE."</td></tr>\n
<tr><td>AUCTION_MAX</td><td>最大出品數</td><td>".AUCTION_MAX."</td></tr>\n
<tr><td>JUDGE_LIST_AUTO_LOAD</td><td>條件判定列表自動取得(1=自動)</td><td>".JUDGE_LIST_AUTO_LOAD."</td></tr>\n
<tr><td>AUCTION_TOGGLE</td><td>拍賣ON/OFF(1=ON)</td><td>".AUCTION_TOGGLE."</td></tr>\n
<tr><td>AUCTION_EXHIBIT_TOGGLE</td><td>出品ON/OFF(1=ON)</td><td>".AUCTION_EXHIBIT_TOGGLE."</td></tr>\n
<tr><td>RANK_TEAM_SET_TIME</td><td>排名隊伍設定週期</td><td>".RANK_TEAM_SET_TIME."s(".(RANK_TEAM_SET_TIME/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_LOSE</td><td>失敗後再挑戰時間</td><td>".RANK_BATTLE_NEXT_LOSE."s(".(RANK_BATTLE_NEXT_LOSE/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_WIN</td><td>贏得排名站再戰的時間</td><td>".RANK_BATTLE_NEXT_WIN."s</td></tr>\n
<tr><td>NORMAL_BATTLE_TIME</td><td>普通戰鬥消耗時間</td><td>".NORMAL_BATTLE_TIME."Time</td></tr>\n
<tr><td>MAX_BATTLE_LOG</td><td>戰鬥記錄保存數(通常怪)</td><td>".MAX_BATTLE_LOG."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_UNION</td><td>戰鬥記錄保存數(BOSS)</td><td>".MAX_BATTLE_LOG_UNION."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_RANK</td><td>戰鬥記錄保存數(排名)</td><td>".MAX_BATTLE_LOG_RANK."</td></tr>\n
<tr><td>UNION_BATTLE_TIME</td><td>BOSS戰消耗時間</td><td>".UNION_BATTLE_TIME."Time</td></tr>\n
<tr><td>UNION_BATTLE_NEXT</td><td>BOSS戰再挑戰時間</td><td>".UNION_BATTLE_NEXT."s</td></tr>\n
<tr><td>BBS_BOTTOM_TOGGLE</td><td>下邊是否加上bbs鏈接(1=ON)</td><td>".BBS_BOTTOM_TOGGLE."</td></tr>\n
</table>\n
");
	}

print <<< ADMIN
<hr>
<p>請不要頻繁使用管理功能。<br>
用戶數或數據有可能導致錯誤。
</p>
ADMIN;


} else {
print <<< LOGIN
<form action="?" method="post">
切口:<input type="text" name="pass" />
<input type="submit" value="開路" />
</form>
LOGIN;
}

?>
</body>
</html>