<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Char List(test)</title>
<link rel="stylesheet" href="./basis.css" type="text/css">
<link rel="stylesheet" href="./style.css" type="text/css">
<style type="text/css">
<!--
body{
  margin:30px;
  background	: #98a0a5/*#bfbfbf*/;
  color	: #bdc8d7;
}
td{
  white-space: nowrap;
  background-color : #10151b;
  text-align:left;
  padding:4px;
}
.a{
  background-color : #333333;
}
-->
</style>
</head>
<body>
<?php
include("./setting.php");
include("./class/class.char.php");
include("./class/global.php");
$folder	= glob("./user/*");
//print("<pre>".print_r($folder,1)."</pre>");
foreach($folder as $val) {
	$UserFile	= glob($val."/*.dat");
	foreach($UserFile as $FileName) {
		$file	= basename($FileName,".dat");
		if(is_numeric($file)) {
			$chars[]	= $FileName;
		}
	}
//print("<pre>".print_r($UserFile,1)."</pre>");
}

print('<table border="0" cellspacing="1"><tbody>');
foreach($chars as $file) {
	$char	= new char(ParseFile($file));
	print("<tr><td>");
	$char->ShowImage();
	print("</td><td>");
	print($char->Name(bold));
	print("</td><td>");
	print("Lv:".$char->level);
	print("</td><td>");
	print("Str:{$char->str}<br />Int:{$char->int}<br />Dex:{$char->dex}<br />Spd:{$char->spd}");
	print("</td></tr>");	
}
print("</tbody></table>");
?>
</body>
</html>