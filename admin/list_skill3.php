<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../basis.css" type="text/css">
<link rel="stylesheet" href="../style.css" type="text/css">
<title>skl_list3</title>
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
include("../data/data.skill.php");
include("../class/global.php");
define("IMG_ICON","../image/icon/");


print("<div style=\"text-align:left;padding:5px\" class=\"bgcolor\">\n");
for($no=1000; $no<9999; $no++) {
	$skill = LoadSkillData($no);
	if(!$skill) continue;
	print("$no:");
	print('<span class="bold">'.$skill["learn"].'</span>pt');
	ShowSkilldetail($skill);
	print("<br />\n");
}
print("</div>\n");
?>
</body>
</html>