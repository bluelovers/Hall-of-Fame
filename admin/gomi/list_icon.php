<?php
$f	= "../image/icon/";
$files	= array();
$files	= @glob($f."*.png");
foreach($files as $name){
	echo "<img src=\"{$name}\">{$name}<br>";
}
?>