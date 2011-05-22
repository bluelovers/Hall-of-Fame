<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>admin?</title>
</head>
<body>
<?php
include("../setting.php");
$admin	= "../";// index.php からの距離
$users	= glob($admin.USER."/*");
print("Users : ".count($users)." / ".MAX_USERS."<br />\n");

print("--- ManageLog<br />\n");
foreach(file($admin.MANAGE_LOG_FILE) as $string) {
	print($string."<br />\n");
}
print("--- BBS01<br />\n");
foreach(file($admin.BBS01) as $string) {
	print($string."<br />\n");
}
print("--- TownBBS<br />\n");
foreach(file($admin.BBS02) as $string) {
	print($string."<br />\n");
}
?>
</body>
</html>
