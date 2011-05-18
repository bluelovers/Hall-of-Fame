<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>SetAction02</title>
<style type="text/css">
<!--
*{
	line-height	: 140%;
	font-family	: Osaka,Verdana,"ＭＳ Ｐゴシック";
}
.bg{
background-color:#cccccc;
}
body{
background-color:#666666;
}
option{
background-color:#dddddd;
}
input{background-color:#dddddd;
}
-->
</style>
</head>
<body>
<?php
	function UserAmount() {
		return 1;
	}

	// 行数
	define("ROWS",$_POST["patternNum"]?$_POST["patternNum"]:5);
	define("IMG","../image/char/");

	// Load
	if($_POST["Load"] && $_POST["loadMob"]) {
		include("../data/data.monster.php");
		$monster	= CreateMonster($_POST["loadMob"]);
		if($monster) {
			for($i=0; $i<ROWS; $i++) {
				$_POST["judge".$i]		= $monster["judge"][$i]?$monster["judge"][$i]:NULL;
				$_POST["quantity".$i]	= $monster["quantity"][$i]?$monster["quantity"][$i]:NULL;
				$_POST["skill".$i]		= $monster["action"][$i]?$monster["action"][$i]:NULL;
			}
		}
		print('<span style="font-weight:bold">'.$_POST["loadMob"]." ".$monster["name"].'</span><img src="'.IMG.$monster["img"].'" />');
	}

	// Add
	if($_POST["add"] && isset($_POST["number"])) {
		$number	= $_POST["number"];
		$var	= array("judge","quantity","skill");
		foreach($var as $head) {
			for($i=ROWS; -1<$i; $i--) {
				if($number == $i)
					$_POST[$head.$i]	= NULL;
				else if($number < $i)
					$_POST[$head.$i]	= $_POST[$head.($i-1)];
				else
					break;
			}
		}
	}

	// Delete
	if($_POST["delete"] && isset($_POST["number"])) {
		$number	= $_POST["number"];
		$var	= array("judge","quantity","skill");
		foreach($var as $head) {
			for($i=0; $i<ROWS; $i++) {
				if($number <= $i)
					$_POST[$head.$i]	= $_POST[$head.($i+1)];
			}
		}
	}

	// TEXTAREA
	if($_POST["make"]) {
		$judgeString	= '"judge"	=> array(';
		$quantityString	= '"quantity"	=> array(';
		$skillString	= '"action"	=> array(';
		for($i=0; $i<ROWS; $i++) {
			if($_POST["judge".$i] && $_POST["skill".$i]) {
				$judgeString	.= $_POST["judge".$i].", ";
				$quantityString	.= $_POST["quantity".$i].", ";
				$skillString	.= $_POST["skill".$i].", ";
			}
		}
		$judgeString	.= "),\n";
		$quantityString	.= "),\n";
		$skillString	.= "),\n";
		print('<textarea style="width:800px;height:100px">');
		print($judgeString.$quantityString.$skillString);
		print("</textarea>\n");
	}

	// 判定の種類
	include("../data/data.judge_setup.php");
	for($i=1000; $i<10000; $i++) {
		$judge	= LoadJudgeData($i);
		if(!$judge)
			continue;
		$judgeList["$i"]["exp"]	= $judge["exp"];
		if($judge["css"])
			$judgeList["$i"]["css"]	= true;
	}

	// 全スキル
	include("../data/data.skill.php");
	for($i=1000; $i<10000; $i++) {
		$skill	= LoadSkillData($i);
		if(!$skill)
			continue;
		$skillList["$i"]	= $i." - ".$skill["name"]."(sp:{$skill[sp]})";
	}

	print('<form method="post" action="?">'."\n");
	print("<table>\n");
	for($i=0; $i<ROWS; $i++) {
		print("<tr><td>\n");
		print('<span style="font-weight:bold">'.sprintf("%2s",$i+1)."</span>");
		print("</td><td>\n");
		// 判定リスト
		print('<select name="judge'.$i.'">'."\n");
		print('<option></option>'."\n");
		foreach($judgeList as $key => $exp) {
			$css	= $exp["css"]?' class="bg"':NULL;
			if($_POST["judge".$i] == $key)
				print('<option value="'.$key.'"'.$css.'selected>'.$exp["exp"].'</option>'."\n");
			else
				print('<option value="'.$key.'"'.$css.'>'.$exp["exp"].'</option>'."\n");
		}
		print("</select>\n");
		print("</td><td>\n");
		// 数値
		print('<input type="text" name="quantity'.$i.'" value="'.($_POST["quantity".$i]?$_POST["quantity".$i]:"0").'" size="10" />'."\n");
		print("</td><td>\n");
		// 技
		print('<select name="skill'.$i.'">'."\n");
		print('<option></option>'."\n");
		foreach($skillList as $key => $exp) {
			if($_POST["skill".$i] == $key)
				print('<option value="'.$key.'" selected>'.$exp.'</option>'."\n");
			else
				print('<option value="'.$key.'">'.$exp.'</option>'."\n");
		}
		print("</select>\n");
		print("</td><td>\n");
		print('<input type="radio" name="number" value="'.$i.'">'."\n");
		print("</td></tr>\n");
	}
	print("</table>\n");
	print('PatternNumber : <input type="text" name="patternNum" size="10" value="'.($_POST["patternNum"]?$_POST["patternNum"]:"5").'" /><br />'."\n");
	print('<input type="submit" value="make" name="make">'."\n");
	print('<input type="hidden" value="make" name="make">'."\n");
	print('<input type="submit" value="add" name="add">'."\n");
	print('<input type="submit" value="delete" name="delete"><br />'."\n");
	print('Load : <input type="text" name="loadMob" size="10" /> <input type="submit" value="Load" name="Load" />');
	print("</form>\n");
?>
</body>
</html>