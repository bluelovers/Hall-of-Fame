<title>PoisonInvasion</title>
<?php
$Poison	= 150;
for($int=0; $int<520; $int+=20) {
	//$dmg	= 0.50 + pow($int*0.1,2)/100 + $int/200;
	$dmg	= (log(($int+22)/10) - 0.8)/0.85;
	print($int." : ".($dmg*150)."(".(($dmg*150)/150).")"."<br />\n");
}
?>