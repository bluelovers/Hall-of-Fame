<?php
// まっぷの出現条件判定
function LoadMapAppear($user) {

	$land	= array();

	// 無条件
	array_push($land,"gb0","gb1","gb2");

	// アイテムがあれば行ける。
	if($user->item["8000"])
		array_push($land,"ac0");
	if($user->item["8001"])
		array_push($land,"ac1");
	if($user->item["8002"])
		array_push($land,"ac2");
	if($user->item["8003"])
		array_push($land,"ac3");
	if($user->item["8004"])
		array_push($land,"ac4");

	if($user->item["8009"])
		array_push($land,"snow0");
	if($user->item["8010"])
		array_push($land,"snow1");
	if($user->item["8011"])
		array_push($land,"snow2");
/*
array_push($land,"sea0");
array_push($land,"sea1");
array_push($land,"ocean0");
array_push($land,"sand0");
array_push($land,"swamp0");
array_push($land,"swamp1");
array_push($land,"mt0");
array_push($land,"volc0");
array_push($land,"volc1");
*/
	if(date("H") == 2 && substr(date("i"),0,1)==5)
		array_push($land,"horh");
	return $land;
}
?>