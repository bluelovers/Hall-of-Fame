<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>SetAction</title>
<style type="text/css">
<!--
*{
	padding	: 0;
	margin	: 0;
	line-height	: 140%;
	font-family	: Osaka,Verdana,"ＭＳ Ｐゴシック";
	overflow:inherit;
}
body{
  margin:30px;
  background	: #98a0a5/*#bfbfbf*/;
  color	: #bdc8d7;
}
select{
  background-color : #10151b;
  color: #98a0a5;
}
.text{
  background-color : #10151b;
  color: #98a0a5;
}
td{
  white-space: nowrap;
  background-color : #10151b;
  text-align:center;
  padding:4px;
}
.a{
  background-color : #333333;
}
-->
</style></head>
<body>
<?php
// Load
if($_POST["MobNumber"]) {/*
	$no	= $_POST["MobNumber"];
	unset($_POST);
	$_POST["MobNumber"]	= $no;*/
	include("./data.monster.php");
	if($monster	= CreateMonster($_POST["MobNumber"])) {
		echo "<h3 style=\"color:333333\"><img src=\"../image/char/{$monster[img]}\" />$monster[name]({$_POST[MobNumber]})</h3>";
		foreach($monster["judge"] as $key => $val) {
			$_POST["judge".$key]	= $val;
		}
		foreach($monster["action"] as $key => $val) {
			$_POST["skill".$key]	= $val;
		}
	}
}
// 表ｦ
if($_POST["Make"]) {
/*
	$string	.= "\"judge\"	=> array(";
	for($i=0; $i<15; $i++) {
		if($post = $_POST["judge".$i])
			$string	.= "{$post},";
	}
	$string	.= "),\n";
	$string	.= "\"action\"	=> array(";
	for($i=0; $i<15; $i++) {
		if($post = $_POST["skill".$i])
			$string	.= "{$post},";
	}
	$string	.= "),";
*/
	$string	= '"Pattern"	=> "';
	print("<textarea style=\"width:800px;\">$string</textarea>");
}
?>
<form action="?" method="post">
<table border="0" cellspacing="1"><tbody>
<?php
include("./data.skill.php");
include("./data.judge_setup.php");

for($i=0; $i<15; $i++) {
	print("<tr><td>\n");
	print(($i+1));
	print("</td><td>\n");
	/////////////////////////////////////////
	print("<select name=\"judge{$i}\">");
	print("<option></option>\n");
	JudgeSelect($_POST["judge".$i]);
	print("</select>\n");
	print("</td><td>\n");
	////////////////////////////////////////
	print('<input type="text" size="8" class="text" name="quantity'.$i.'" value="'.$_POST["quantity".$i].'" />');
	print("</td><td>\n");
	////////////////////////////////////////
	print("<select name=\"skill{$i}\">\n");
	print("<option></option>\n");
	SkillSelect($_POST["skill".$i]);
	print("</select>\n");
	print("</td></tr>\n");
}
/////////////////////////////////////////////////////////////
function JudgeSelect($selected=false){
	static $judge	= array();

	if(!$judge) {
		for($j=1000; $j<4000; $j++) {
			if($load	= LoadJudgeData($j))
				$judge[$j]	= $load["exp"];
				//"<option value=\"{$j}\">{$judge[exp2]}</option>";
		}
	}

	foreach($judge as $key => $val) {
		if($key == $selected) {
			print("<option value=\"{$key}\" selected>{$val}</option>\n");
			continue;
		}
		print("<option value=\"{$key}\">{$val}</option>\n");
	}
}
/////////////////////////////////////////////////////////////
function SkillSelect($selected=false){
	static $skill	= array();

	if(!$skill) {
		for($j=1000; $j<10000; $j++) {
			if($load	= LoadSkillData($j))
				$skill[$j]	= $load["name"];
				//"<option value=\"{$j}\">{$skill[name]}</option>";
		}
	}

	foreach($skill as $key => $val) {
		if($key == $selected) {
			print("<option value=\"{$key}\" selected>{$val}</option>\n");
			continue;
		}
		print("<option value=\"{$key}\">{$val}</option>\n");
	}
}
?>
</tbody></table>
Load : <input type="text" name="MobNumber"><br />
<input type="submit" value="Make" name="Make"/><input type="reset" value="Reset">
</form>
</body>
</html>