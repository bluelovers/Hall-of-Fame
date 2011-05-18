<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JudgeLists</title>
</head>
<body>
<?
include("../data/data.judge_setup.php");
for($i=1000; $i<9999; $i++) {
	$j	= LoadJudgeData($i);
	if($j) {
		print("case {$i}:// {$j[exp]}<br />");
		$list[]	= $i;
	}
}
print("array(<br />\n");
foreach($list as $var) {
	$A++;
	print("$var, ");
	if($A%5==0)
		print("<br />\n");
}
print("<br />\n);");
?>
</body>
</html>