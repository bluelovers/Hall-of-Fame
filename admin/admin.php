<?php
	//include("./setting.php");
	if(!defined("ADMIN_PASSWORD"))
		exit(1);
	/*
	* ������
	*/
	if($_POST["pass"] == ADMIN_PASSWORD || $_COOKIE["adminPass"] == ADMIN_PASSWORD) {
		setcookie ("adminPass", $_POST["pass"]?$_POST["pass"]:$_COOKIE["adminPass"],time()+60*30);
		$login = true;
	}

	/*
	* ��������
	*/
	if($_POST["logout"]) {
		setcookie ("adminPass");
		$login = false;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
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
	* changeData(�ǡ������ѹ���ä���)
	*/
	function changeData($file,$text) {
		$fp = @fopen($file,"w") or die("file lock error!");
		flock($fp,LOCK_EX);
		fwrite($fp,stripcslashes($text));
		flock($fp,LOCK_UN);
		fclose($fp);
		print("<span style=\"font-weight:bold\">�ǡ�������</span>");
	}

	/*
	* ��˥塼
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
	* �桼������
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
	* �桼���ǡ���
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
		print('�桼����� :<input type="text" name="deletePass" size="">');
		print('<input type="submit" name="deleteUser" value="���">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* �桼���ǡ������
	*/
	else if($_POST["deleteUser"]) {
		if($_POST["deletePass"] == ADMIN_PASSWORD) {
			include(GLOBAL_PHP);
			include(CLASS_USER);
			$userD = new user($_POST["userID"]);
			$userD->DeleteUser();
			print($_POST["userID"]."�������ޤ�����");
		} else {
			print("�ѥ���ɤ�������");
		}
	}

	/*
	* �桼���ǡ���(�ܺ�)
	*/
	else if($_POST["UserFileDet"]) {
		$file = USER.$_POST["userID"]."/".$_POST["userFile"];
		// �ǡ����ν���
		if($_POST["changeData"]) {
			$fp = @fopen($file,"w") or die("file lock error!");
			flock($fp,LOCK_EX);
			fwrite($fp,$_POST["fileData"]);
			flock($fp,LOCK_UN);
			fclose($fp);
			print("�ǡ�������");
		}

		print("<p>$file</p>\n");
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="userFile" value="'.$_POST["userFile"].'">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print('<input type="hidden" name="UserFileDet" value="1">');
		print("</form>\n");
		print('<form action="?" method="post">');
		print('<input type="submit" name="UserData" value="���">');
		print('<input type="hidden" name="userID" value="'.$_POST["userID"].'">');
		print("</form>\n");
	}

	/*
	* �ǡ�������
	*/
	else if($_GET["menu"] === "data") {
print <<< DATA
<br>
<form action="?" method="post">
<ul>
<li><input type="submit" name="UserDataDetail" value=" + ">(��1)�桼���ǡ����ν��פ�ɽ��</li>
<li><input type="submit" name="UserCharDetail" value=" + ">(��1)�����ǡ����ν��פ�ɽ��</li>
<li><input type="submit" name="ItemDataDetail" value=" + ">(��1)�����ƥ�ǡ����ν��פ�ɽ��</li>
<li><input type="submit" name="UserIpShow" value=" + ">(��1)�桼����IP��ɽ��</li>
<li><input type="submit" name="searchBroken" value=" + ">(��1)����Ƥ��ǽ���Τ���ǡ�����õ��(��̯)<input type="text" name="brokenSize" value="100" size=""></li>
<li><input type="submit" name="adminBattleLog" value=" + ">��Ʈ���δ���</li>
<li><input type="submit" name="adminAuction" value=" + ">�����������δ���</li>
<li><input type="submit" name="adminRanking" value=" + ">��󥭥󥰤δ���</li>
<li><input type="submit" name="adminTown" value=" + ">Į����δ���</li>
<li><input type="submit" name="adminRegister" value=" + ">�桼����Ͽ����δ���</li>
<li><input type="submit" name="adminUserName" value=" + ">�桼��̾�δ���</li>
<li><input type="submit" name="adminUpDate" value=" + ">��������δ���</li>
<li><input type="submit" name="adminAutoControl" value=" + ">��ư�����Υ�</li>
</ul>
<p>(��1)�ȤƤ�إӡ��ʽ����Ǥ���<br>
�ǡ�����������ۤɽ����������ޤ���
</p>
</form>
DATA;
	}

	/*
	* �ǡ�������(�桼���ǡ���)
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
	* �ǡ�������(�����ǡ���)
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
	* �ǡ�������(�����ƥ�ǡ���)
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
	* �桼����IPɽ��
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
		// ��ʣ�ꥹ��
		print("<p>IP��ʣ�ꥹ��</p>\n");
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
	* ����Ƥ��ǽ���Τ���ǡ�����õ��
	*/
	else if($_POST["searchBroken"]) {
		print("<p>����Ƥ����ǽ���Τ���ե�����<br>\n");
		$baseSize = $_POST["brokenSize"]?(int)$_POST["brokenSize"]:100;
		print("��{$baseSize}byte �ʲ��Υե������õ��������(�����ƥ�ǡ����Ͻ���).</p>");
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
	* ��Ʈ���δ���
	*/
	else if($_POST["adminBattleLog"]) {
		if($_POST["deleteLogCommon"]) {
			$dir = LOG_BATTLE_NORMAL;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>�̾���Ʈ���������ޤ�����</p>\n");
		} else if($_POST["deleteLogUnion"]) {
			$dir = LOG_BATTLE_UNION;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>��˥�����Ʈ���������ޤ�����</p>\n");
		} else if($_POST["deleteLogRanking"]) {
			$dir = LOG_BATTLE_RANK;
			$logFile = glob($dir."*");
			foreach($logFile as $file) {
				unlink($file);
			}
			print("<p>��󥭥���Ʈ���������ޤ�����</p>\n");
		}
print <<< DATA
<br>
<form action="?" method="post">
<input type="hidden" name="adminBattleLog" value="1">
<ul>
<li><input type="submit" name="deleteLogCommon" value=" + ">�̾���Ʈ���������������</li>
<li><input type="submit" name="deleteLogUnion" value=" + ">��˥�����Ʈ���������������</li>
<li><input type="submit" name="deleteLogRanking" value=" + ">��󥭥󥰥��������������</li>
</ul>
</form>
DATA;
	}

	/*
	* �����������δ���
	*/
	else if($_POST["adminAuction"]) {
		$file = AUCTION_ITEM;
		print("<p>�����������δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminAuction" value="1">');
		print("</form>\n");
	}

	/*
	* ��󥭥󥰤δ���
	*/
	else if($_POST["adminRanking"]) {
		$file = RANKING;
		print("<p>��󥭥󥰤δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminRanking" value="1">');
		print("</form>\n");
	}

	/*
	* Į����δ���
	*/
	else if($_POST["adminTown"]) {
		$file = BBS_TOWN;
		print("<p>Į����δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminTown" value="1">');
		print("</form>\n");
	}

	/*
	* �桼����Ͽ����δ���
	*/
	else if($_POST["adminRegister"]) {
		$file = REGISTER;
		print("<p>�桼����Ͽ����δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminRegister" value="1">');
		print("</form>\n");
	}

	/*
	* �桼��̾�δ���
	*/
	else if($_POST["adminUserName"]) {
		$file = USER_NAME;
		print("<p>�桼��̾�δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminUserName" value="1">');
		print("</form>\n");
	}

	/*
	* ��������δ���
	*/
	else if($_POST["adminUpDate"]) {
		$file = UPDATE;
		print("<p>��������δ���</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminUpDate" value="1">');
		print("</form>\n");
	}

	/*
	* ��ư�����Υ�
	*/
	else if($_POST["adminAutoControl"]) {
		$file = MANAGE_LOG_FILE;
		print("<p>��ư�����Υ�</p>\n");
		// �ǡ����ν���
		if($_POST["changeData"]) {
			changeData($file,$_POST["fileData"]);
		}
		print('<form action="?" method="post">');
		print('<textarea name="fileData" style="width:800px;height:300px;">');
		print(file_get_contents($file));
		print("</textarea><br>\n");
		print('<input type="submit" name="changeData" value="����">');
		print('<input type="submit" value="����">');
		print('<input type="hidden" name="adminAutoControl" value="1">');
		print("</form>\n");
	}

	/*
	* OTHER
	*/
	else if($_GET["menu"] === "other") {
print("
<p>���Τ�</p>\n
<ul>\n
<li><a href=\"".ADMIN_DIR."list_item.php\">�����ƥ����</a></li>\n
<li><a href=\"".ADMIN_DIR."list_enchant.php\">�������̰���</a></li>\n
<li><a href=\"".ADMIN_DIR."list_job.php\">���Ȱ���</a></li>\n
<li><a href=\"".ADMIN_DIR."list_judge.php\">Ƚ�����</a></li>\n
<li><a href=\"".ADMIN_DIR."list_monster.php\">��󥹥�������</a></li>\n
<li><a href=\"".ADMIN_DIR."list_skill3.php\">���������</a></li>\n
<li><a href=\"".ADMIN_DIR."set_action2.php\">�ѥ��������굡</a></li>\n
</ul>\n
");
	}

	/*
	* ����ʳ�
	*/
	else {
print("
<p>��������</p>\n
<table border=\"1\">\n
<tr><td>���</td><td>����</td><td>��</td></tr>
<tr><td>TITLE</td><td>�����ȥ�</td><td>".TITLE."</td></tr>\n
<tr><td>MAX_TIME</td><td>����Time</td><td>".MAX_TIME."Time</td></tr>\n
<tr><td>TIME_GAIN_DAY</td><td>1����������Time</td><td>".TIME_GAIN_DAY."Time</td></tr>\n
<tr><td>CONTROL_PERIOD</td><td>��ư��������</td><td>".CONTROL_PERIOD."s(".(CONTROL_PERIOD/60/60)."hour)"."</td></tr>\n
<tr><td>RECORD_IP</td><td>IP��Ͽ���뤫(1=ON)</td><td>".RECORD_IP."</td></tr>\n
<tr><td>SELLING_PRICE</td><td>����</td><td>".SELLING_PRICE."</td></tr>\n
<tr><td>EXP_RATE</td><td>�и�����Ψ</td><td>x".EXP_RATE."</td></tr>\n
<tr><td>MONEY_RATE</td><td>������Ψ</td><td>x".MONEY_RATE."</td></tr>\n
<tr><td>AUCTION_MAX</td><td>������ʿ�</td><td>".AUCTION_MAX."</td></tr>\n
<tr><td>JUDGE_LIST_AUTO_LOAD</td><td>���Ƚ��Υꥹ�Ȥ�ư����(1=��ư)</td><td>".JUDGE_LIST_AUTO_LOAD."</td></tr>\n
<tr><td>AUCTION_TOGGLE</td><td>�����������ON/OFF(1=ON)</td><td>".AUCTION_TOGGLE."</td></tr>\n
<tr><td>AUCTION_EXHIBIT_TOGGLE</td><td>����ON/OFF(1=ON)</td><td>".AUCTION_EXHIBIT_TOGGLE."</td></tr>\n
<tr><td>RANK_TEAM_SET_TIME</td><td>��󥭥󥰤Υ������������</td><td>".RANK_TEAM_SET_TIME."s(".(RANK_TEAM_SET_TIME/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_LOSE</td><td>�餱���Ȥ����Ԥ�����</td><td>".RANK_BATTLE_NEXT_LOSE."s(".(RANK_BATTLE_NEXT_LOSE/60/60)."hour)"."</td></tr>\n
<tr><td>RANK_BATTLE_NEXT_WIN</td><td>�������������Ԥ�����</td><td>".RANK_BATTLE_NEXT_WIN."s</td></tr>\n
<tr><td>NORMAL_BATTLE_TIME</td><td>��󥹥����Ȥ��襤�Ǿ��񤹤����</td><td>".NORMAL_BATTLE_TIME."Time</td></tr>\n
<tr><td>MAX_BATTLE_LOG</td><td>��Ʈ����¸��(�̾��󥹥���)</td><td>".MAX_BATTLE_LOG."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_UNION</td><td>��Ʈ����¸��(��˥���)</td><td>".MAX_BATTLE_LOG_UNION."</td></tr>\n
<tr><td>MAX_BATTLE_LOG_RANK</td><td>��Ʈ����¸��(��󥭥�)</td><td>".MAX_BATTLE_LOG_RANK."</td></tr>\n
<tr><td>UNION_BATTLE_TIME</td><td>��˥�����Ǿ��񤹤����</td><td>".UNION_BATTLE_TIME."Time</td></tr>\n
<tr><td>UNION_BATTLE_NEXT</td><td>��˥�������Ԥ�����</td><td>".UNION_BATTLE_NEXT."s</td></tr>\n
<tr><td>BBS_BOTTOM_TOGGLE</td><td>������˥塼�ˤ����ԷǼ���(1=ON)</td><td>".BBS_BOTTOM_TOGGLE."</td></tr>\n
</table>\n
");
	}

print <<< ADMIN
<hr>
<p>���� �ȤäƤĤ����褦�ʴ�����ǽ��΅���ʤ��Ǥ���������<br>
�桼������0���ä��ꤹ��Ȱ������顼���Ф��ǽ��ͭ�ꡣ
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