<title>time</title>
<?php
$now	= time();
for($i=-24; $i<24; $i++) {
	if($i==0)
		print("Now : ".($now-60*60*$i)."<br />\n");
	else
		print("+{$i} : ".($now-60*60*$i)."<br />\n");
}
?>