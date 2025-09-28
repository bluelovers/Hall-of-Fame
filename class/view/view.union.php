<?php

/**
 * Union monster processing
 * Unionモンスタ一の處理
 * @param main $main The main object
 * @return bool
 */
function UnionProcess($main) {
	if($main->CanUnionBattle() !== true) {
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(str_replace('\\', '/', rtrim(dirname($_SERVER['PHP_SELF']))), '/');
		
		$extra = INDEX;
		// print("{$_SERVER['HTTP_HOST']}<br>");
		// print("{$uri}<br>");
		// print("{$extra}<br>");
		header("Location: http://$host$uri/$extra?hunt");
		exit;
	}

	if(!$_POST["union_battle"])
		return false;
	$Union	= new union();
	// If defeated or does not exist.
	// 倒されているか、存在しない場合。
	if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
		return false;
	}
	// Union monster data
	// ユニオンモンスタ一のデ一タ
	$UnionMob	= CreateMonster($Union->MonsterNumber);
	$main->MemorizeParty();// Remember the party // パ一ティ一記憶
	// Your party
	// 自分パ一ティ一
	$MyParty = array();
	$TotalLevel = 0;
	foreach($main->char as $key => $val) {// List of checked guys // チェックされたやつリスト
		if($_POST["char_".$key]) {
			$MyParty[]	= $main->char[$key];
			$TotalLevel	+= $main->char[$key]->level;// Total level of your party // 自分PTの合計レベル
		}
	}
	// Total level limit
	// 合計レベル制限
	if($UnionMob["LevelLimit"] < $TotalLevel) {
		ShowError('Total level limit exceeded ('.$TotalLevel.'/'.$UnionMob["LevelLimit"].')',"margin15");
		return false;
	}
	if( count($MyParty) === 0) {
		ShowError('At least one person must participate in the battle',"margin15");
		return false;
	} else if(5 < count($MyParty)) {
		ShowError('Up to five people can participate in the battle',"margin15");
		return false;
	}
	if(!$main->WasteTime(UNION_BATTLE_TIME)) {
		ShowError('Time Shortage.',"margin15");
		return false;
	}

	// Number of enemy PT
	// 敵PT數

	// Random enemy party
	// ランダム敵パ一ティ一
	if($UnionMob["SlaveAmount"])
		$EneNum	= $UnionMob["SlaveAmount"] + 1;// Same number as PT members // PTメンバと同じ數だけ。
	else
		$EneNum	= 5;// Fixed to 5 including Union // Union含めて5に固定する。

	if($UnionMob["SlaveSpecify"])
		$EnemyParty	= $main->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);
	else
		$EnemyParty	= $main->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);

	// Insert unionMob approximately in the center of the array
	// unionMobを配列のおよそ中央に入れる
	array_splice($EnemyParty,floor(count($EnemyParty)/2),0,array($Union));

	$main->UnionSetTime();

	include(CLASS_BATTLE);
	$battle	= new battle($MyParty,$EnemyParty);
	$battle->SetUnionBattle();
	$battle->SetBackGround($Union->UnionLand);// Background // 背景
	//$battle->SetTeamName($main->name,"Union:".$Union->Name());
	$battle->SetTeamName($main->name,$UnionMob["UnionName"]);
	$battle->Process();// Start battle // 戰鬥開始

	$battle->SaveCharacters();// Save character data // キャラデ一タ保存
		list($UserMoney)	= $battle->ReturnMoney();// Total amount of money obtained in battle // 戰鬥で得た合計金額
		$main->GetMoney($UserMoney);// Increase money // お金を增やす
		$battle->RecordLog("UNION");
		// Receive items
		// 道具を受け取る
		if($itemdrop	= $battle->ReturnItemGet(0)) {
			$main->LoadUserItem();
			foreach($itemdrop as $itemno => $amount)
				$main->AddItem($itemno,$amount);
			$main->SaveUserItem();
		}

	return true;
}

/**
 * Display of Union monster
 * Unionモンスタ一の表示
 * @param main $main The main object
 * @return bool
 */
function UnionShow($main) {
	if($main->CanUnionBattle() !== true) {
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
		$extra = INDEX;
		header("Location: http://$host$uri/$extra?hunt");
		exit;
	}
	//if($Result	= UnionProcess($main))
	//	return true;
	print('<div style="margin:15px">'."\n");
	print("<h4>Union Monster</h4>\n");
	$Union	= new union();
	// If defeated or does not exist.
	// 倒されているか、存在しない場合。
	if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
		ShowError("Defeated or not Exists.");
		return false;
	}
	print('</div>');
	$main->ShowCharacters(array($Union),false,"sea");
	print('<div style="margin:15px">'."\n");
	print("<h4>Teams</h4>\n");
	print("</div>");
	print('<form action="'.INDEX.'?union='.$_GET["union"].'" method="post">');
	$main->ShowCharacters($main->char,CHECKBOX,explode("<>",$main->party_memo));
		?>
<div style="margin:15px;text-align:center">
<input type="submit" class="btn" value="戰鬥!">
<input type="hidden" name="union_battle" value="1">
<input type="reset" class="btn" value="重置"><br>
	Save this party:<input type="checkbox" name="memory_party" value="1">
</div></form>
<?php 
}
?>