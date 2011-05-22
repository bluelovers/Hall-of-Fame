<?php 
include("./data.judge_setup_old.php");
for($i=1000; $i<9999; $i++) {
	$j	= LoadJudgeData($i);
	if($j) {
		print("case {$i}:// {$j[exp2]}<br>");
	}
}
?>