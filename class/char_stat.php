<?php


//	キャラ詳細表示から送られたリクエストを處理する
//	長い...(100行オ一バ一)
	function CharStatProcess($main) {
		$char	= &$main->char[$_GET["char"]];
		if(!$char) return false;
		switch(true):
			// ステ一タス上昇
			case($_POST["stup"]):
				//ステ一タスポイント超過(ねんのための絕對值)
				$Sum	= abs($_POST["upStr"]) + abs($_POST["upInt"]) + abs($_POST["upDex"]) + abs($_POST["upSpd"]) + abs($_POST["upLuk"]);
				if($char->statuspoint < $Sum) {
					ShowError("狀態點數過多","margin15");
					return false;
				}

				if($Sum == 0)
					return false;

				$Stat	= array("Str","Int","Dex","Spd","Luk");
				foreach($Stat as $val) {//最大值を超えないかチェック
					if(MAX_STATUS < ($char->{strtolower($val)} + $_POST["up".$val])) {
						ShowError("超過最大狀態(".MAX_STATUS.")","margin15");
						return false;
					}
				}
				$char->str	+= $_POST["upStr"];//ステ一タスを增やす
				$char->int	+= $_POST["upInt"];
				$char->dex	+= $_POST["upDex"];
				$char->spd	+= $_POST["upSpd"];
				$char->luk	+= $_POST["upLuk"];
				$char->SetHpSp();

				$char->statuspoint	-= $Sum;//ポイントを減らす。
				print("<div class=\"margin15\">\n");
				if($_POST["upStr"])
					ShowResult("STR <span class=\"bold\">".$_POST[upStr]."</span> 上升。".($char->str - $_POST["upStr"])." -> ".$char->str."<br />\n");
				if($_POST["upInt"])
					ShowResult("INT <span class=\"bold\">".$_POST[upInt]."</span> 上升。".($char->int - $_POST["upInt"])." -> ".$char->int."<br />\n");
				if($_POST["upDex"])
					ShowResult("DEX <span class=\"bold\">".$_POST[upDex]."</span> 上升。".($char->dex - $_POST["upDex"])." -> ".$char->dex."<br />\n");
				if($_POST["upSpd"])
					ShowResult("SPD <span class=\"bold\">".$_POST[upSpd]."</span> 上升。".($char->spd - $_POST["upSpd"])." -> ".$char->spd."<br />\n");
				if($_POST["upLuk"])
					ShowResult("LUK <span class=\"bold\">".$_POST[upLuk]."</span> 上升。".($char->luk - $_POST["upLuk"])." -> ".$char->luk."<br />\n");
				print("</div>\n");
				$char->SaveCharData($main->id);
				return true;
			// 配置?他設定(防禦)
			case($_POST["position"]):
				if($_POST["position"] == "front") {
					$char->position	= FRONT;
					$pos	= "前衛(Front)";
				} else {
					$char->position	= BACK;
					$pos	= "後衛(Back)";
				}

				$char->guard	= $_POST["guard"];
				switch($_POST["guard"]) {
					case "never":	$guard	= "放棄後衛"; break;
					case "life25":	$guard	= "體力25%以上時保護後衛"; break;
					case "life50":	$guard	= "體力50%以上時保護後衛"; break;
					case "life75":	$guard	= "體力75%以上時保護後衛"; break;
					case "prob25":	$guard	= "25%的概率保護後衛"; break;
					case "prob50":	$guard	= "50%的概率保護後衛"; break;
					case "prob75":	$guard	= "75%的概率保護後衛"; break;
					default:	$guard	= "必定保護後衛"; break;
				}
				$char->SaveCharData($main->id);
				ShowResult($char->Name()." 的配置 {$pos} 。<br />作為前衛時 設置為{$guard} 。\n","margin15");
				return true;
			//行動設定
			case($_POST["ChangePattern"]):
				$max	= $char->MaxPatterns();
				//記憶する模式と技の配列。
				for($i=0; $i<$max; $i++) {
					$judge[]	= $_POST["judge".$i];
					$quantity_post	= (int)$_POST["quantity".$i];
					if(4 < strlen($quantity_post)) {
						$quantity_post	= substr($quantity_post,0,4);
					}
					$quantity[]	= $quantity_post;
					$action[]	= $_POST["skill".$i];
				}
				//if($char->ChangePattern($judge,$action)) {
				if($char->PatternSave($judge,$quantity,$action)) {
					$char->SaveCharData($main->id);
					ShowResult("戰鬥設置保存完成","margin15");
					return true;
				}
				ShowError("保存失敗？請嘗試報告03050242","margin15");
				return false;
				break;
			//	行動設定 兼 模擬戰
			case($_POST["TestBattle"]):
					$max	= $char->MaxPatterns();
					//記憶する模式と技の配列。
					for($i=0; $i<$max; $i++) {
						$judge[]	= $_POST["judge".$i];
						$quantity_post	= (int)$_POST["quantity".$i];
						if(4 < strlen($quantity_post)) {
							$quantity_post	= substr($quantity_post,0,4);
						}
						$quantity[]	= $quantity_post;
						$action[]	= $_POST["skill".$i];
					}
					//if($char->ChangePattern($judge,$action)) {
					if($char->PatternSave($judge,$quantity,$action)) {
						$char->SaveCharData($main->id);
						$main->CharTestDoppel();
					}
				break;
			//	行動模式メモ(交換)
			case($_POST["PatternMemo"]):
				if($char->ChangePatternMemo()) {
					$char->SaveCharData($main->id);
					ShowResult("模式交換完成","margin15");
					return true;
				}
				break;
			//	指定行に追加
			case($_POST["AddNewPattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->AddPattern($_POST["PatternNumber"])) {
					$char->SaveCharData($main->id);
					ShowResult("模式追加完成","margin15");
					return true;
				}
				break;
			//	指定行を削除
			case($_POST["DeletePattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->DeletePattern($_POST["PatternNumber"])) {
					$char->SaveCharData($main->id);
					ShowResult("模式削除完成","margin15");
					return true;
				}
				break;
			//	指定箇所だけ裝備をはずす
			case($_POST["remove"]):
				if(!$_POST["spot"]) {
					ShowError("沒有選擇需要去掉的裝備","margin15");
					return false;
				}
				if(!$char->{$_POST["spot"]}) {// $main と $char の區別注意！
					ShowError("指定位置沒有裝備","margin15");
					return false;
				}
				$item	= LoadItemData($char->{$_POST["spot"]});
				if(!$item) return false;
				$main->AddItem($char->{$_POST["spot"]});
				$main->SaveUserItem();
				$char->{$_POST["spot"]}	= NULL;
				$char->SaveCharData($main->id);
				SHowResult($char->Name()." 的 {$item[name]} 解除。","margin15");
				return true;
				break;
			//	裝備全部はずす
			case($_POST["remove_all"]):
				if($char->weapon || $char->shield || $char->armor || $char->item ) {
					if($char->weapon)	{ $main->AddItem($char->weapon);	$char->weapon	=NULL; }
					if($char->shield)	{ $main->AddItem($char->shield);	$char->shield	=NULL; }
					if($char->armor)	{ $main->AddItem($char->armor);		$char->armor	=NULL; }
					if($char->item)		{ $main->AddItem($char->item);		$char->item		=NULL; }
					$main->SaveUserItem();
					$char->SaveCharData($main->id);
					ShowResult($char->Name()." 的裝備全部解除","margin15");
					return true;
				}	break;
			//	指定物を裝備する
			case($_POST["equip_item"]):
				$item_no	= $_POST["item_no"];
				if(!$main->item["$item_no"]) {//その道具を所持しているか
					ShowError("Item not exists.","margin15");
					return false;
				}

				$JobData	= LoadJobData($char->job);
				$item	= LoadItemData($item_no);//裝備しようとしてる物
				if( !in_array( $item["type"], $JobData["equip"]) ) {//それが裝備不可能なら?
					ShowError("{$char->job_name} can't equip {$item[name]}.","margin15");
					return false;
				}

				if(false === $return = $char->Equip($item)) {
					ShowError("裝備過重（handle不足）.","margin15");
					return false;
				} else {
					$main->DeleteItem($item_no);
					foreach($return as $no) {
						$main->AddItem($no);
					}
				}

				$main->SaveUserItem();
				$char->SaveCharData($main->id);
				ShowResult("{$char->name} 的 {$item[name]} 裝備.","margin15");
				return true;
				break;
			// スキル習得
			case($_POST["learnskill"]):
				if(!$_POST["newskill"]) {
					ShowError("沒選定技能","margin15");
					return false;
				}

				$char->SetUser($main->id);
				list($result,$message)	= $char->LearnNewSkill($_POST["newskill"]);
				if($result) {
					$char->SaveCharData();
					ShowResult($message,"margin15");
				} else {
					ShowError($message,"margin15");
				}
				return true;
			// クラスチェンジ(轉職)
			case($_POST["classchange"]):
				if(!$_POST["job"]) {
					ShowError("沒選定職業","margin15");
					return false;
				}
				if($char->ClassChange($_POST["job"])) {
					// 裝備を全部解除
					if($char->weapon || $char->shield || $char->armor || $char->item ) {
						if($char->weapon)	{ $main->AddItem($char->weapon);	$char->weapon	=NULL; }
						if($char->shield)	{ $main->AddItem($char->shield);	$char->shield	=NULL; }
						if($char->armor)	{ $main->AddItem($char->armor);		$char->armor	=NULL; }
						if($char->item)		{ $main->AddItem($char->item);		$char->item		=NULL; }
						$main->SaveUserItem();
					}
					// 保存
					$char->SaveCharData($main->id);
					ShowResult("轉職完成","margin15");
					return true;
				}
				ShowError("failed.","margin15");
				return false;
			//	改名(表示)
			case($_POST["rename"]):
				$Name	= $char->Name();
				$message = <<< EOD
<form action="?char={$_GET[char]}" method="post" class="margin15">
半角英數16文字 (全角1文字=半角2文字)<br />
<input type="text" name="NewName" style="width:160px" class="text" />
<input type="submit" class="btn" name="NameChange" value="Change" />
<input type="submit" class="btn" value="Cancel" />
</form>
EOD;
				print($message);
				return false;
			// 改名(處理)
			case($_POST["NewName"]):
				list($result,$return)	= CheckString($_POST["NewName"],16);
				if($result === false) {
					ShowError($return,"margin15");
					return false;
				} else if($result === true) {
					if($main->DeleteItem("7500",1) == 1) {
						ShowResult($char->Name()."   ".$return." 改名完成。","margin15");
						$char->ChangeName($return);
						$char->SaveCharData($main->id);
						$main->SaveUserItem();
						return true;
					} else {
						ShowError("沒有道具。","margin15");
						return false;
					}
					return true;
				}
			// 各種リセットの表示
			case($_POST["showreset"]):
				$Name	= $char->Name();
				print('<div class="margin15">'."\n");
				print("使用道具<br />\n");
				print('<form action="?char='.$_GET[char].'" method="post">'."\n");
				print('<select name="itemUse">'."\n");
				$resetItem	= array(7510,7511,7512,7513,7520);
				foreach($resetItem as $itemNo) {
					if($main->item[$itemNo]) {
						$item	= LoadItemData($itemNo);
						print('<option value="'.$itemNo.'">'.$item[name]." x".$main->item[$itemNo].'</option>'."\n");
					}
				}
				print("</select>\n");
				print('<input type="submit" class="btn" name="resetVarious" value="重置">'."\n");
				print('<input type="submit" class="btn" value="取消">'."\n");
				print('</form>'."\n");
				print('</div>'."\n");
				break;

			// 各種リセットの處理
			case($_POST["resetVarious"]):
				switch($_POST["itemUse"]) {
					case 7510:
						$lowLimit	= 1;
						break;
					case 7511:
						$lowLimit	= 30;
						break;
					case 7512:
						$lowLimit	= 50;
						break;
					case 7513:
						$lowLimit	= 100;
						break;
					// skill
					case 7520:
						$skillReset	= true;
						break;
				}
				// 石ころをSPD1に戾す道具にする
				if($_POST["itemUse"] == 6000) {
					if($main->DeleteItem(6000) == 0) {
						ShowError("沒有道具。","margin15");
						return false;
					}
					if(1 < $char->spd) {
						$dif	= $char->spd - 1;
						$char->spd	-= $dif;
						$char->statuspoint	+= $dif;
						$char->SaveCharData($main->id);
						$main->SaveUserItem();
						ShowResult("點數歸還","margin15");
						return true;
					}
				}
				if($lowLimit) {
					if(!$main->item[$_POST["itemUse"]]) {
						ShowError("沒有道具。","margin15");
						return false;
					}
					if($lowLimit < $char->str) {$dif = $char->str - $lowLimit; $char->str -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->int) {$dif = $char->int - $lowLimit; $char->int -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->dex) {$dif = $char->dex - $lowLimit; $char->dex -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->spd) {$dif = $char->spd - $lowLimit; $char->spd -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->luk) {$dif = $char->luk - $lowLimit; $char->luk -= $dif; $pointBack += $dif;}
					if($pointBack) {
						if($main->DeleteItem($_POST["itemUse"]) == 0) {
							ShowError("沒有道具。","margin15");
							return false;
						}
						$char->statuspoint	+= $pointBack;
						// 裝備も全部解除
						if($char->weapon || $char->shield || $char->armor || $char->item ) {
							if($char->weapon)	{ $main->AddItem($char->weapon);	$char->weapon	=NULL; }
							if($char->shield)	{ $main->AddItem($char->shield);	$char->shield	=NULL; }
							if($char->armor)	{ $main->AddItem($char->armor);		$char->armor	=NULL; }
							if($char->item)		{ $main->AddItem($char->item);		$char->item		=NULL; }
							ShowResult($char->Name()." 的所有裝備解除","margin15");
						}
						$char->SaveCharData($main->id);
						$main->SaveUserItem();
						ShowResult("點數歸還成功","margin15");
						return true;
					} else {
						ShowError("點數歸還失敗","margin15");
						return false;
					}
				}
				break;

			// サヨナラ(表示)
			case($_POST["byebye"]):
				$Name	= $char->Name();
				$message = <<< HTML_BYEBYE
<div class="margin15">
{$Name} 解雇?<br>
<form action="?char={$_GET[char]}" method="post">
<input type="submit" class="btn" name="kick" value="Yes">
<input type="submit" class="btn" value="No">
</form>
</div>
HTML_BYEBYE;
				print($message);
				return false;
			// サヨナラ(處理)
			case($_POST["kick"]):
				//$main->DeleteChar($char->birth);
				$char->DeleteChar();
				$host  = $_SERVER['HTTP_HOST'];
				$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
				//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$extra = INDEX;
				header("Location: http://$host$uri/$extra");
				exit;
				break;
		endswitch;
	}
//////////////////////////////////////////////////////////////////////////////////////
//	キャラクタ一詳細表示?裝備變更などなど
//	長すぎる...(200行以上)
	function CharStatShow(&$main) {
		$char	= $main->char[$_GET["char"]];
		if(!$char) {
			print("Not exists");
			return false;
		}
		// 戰鬥用變數の設定。
		$char->SetBattleVariable();

		// 職デ一タ
		$JobData	= LoadJobData($char->job);

		// 轉職可能な職
		if($JobData["change"]) {
			include_once(DATA_CLASSCHANGE);
			foreach($JobData["change"] as $job) {
				if(CanClassChange($char,$job))
					$CanChange[]	= $job;//轉職できる候補。
			}
		}

		////// ステ一タス表示 //////////////////////////////
			?>
<form action="?char=<?php print $_GET["char"]?>" method="post" style="padding:5px 0 0 15px">
<?php 
		// その他キャラ
		print('<div style="padding-top:5px">');
		foreach($main->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>  ";
		}
		print("</div>");
	?>
<h4>人物狀態 <a href="?manual#charstat" target="_blank" class="a0">?</a></h4>
<?php 
		$char->ShowCharDetail();
		// 改名
		if($main->item["7500"])
			print('<input type="submit" class="btn" name="rename" value="ChangeName">'."\n");
		// ステ一タスリセット系
		if($main->item["7510"] ||
			$main->item["7511"] ||
			$main->item["7512"] ||
			$main->item["7513"] ||
			$main->item["7520"]) {
			print('<input type="submit" class="btn" name="showreset" value="重置">'."\n");
		}
?>
<input type="submit" class="btn" name="byebye" value="剔除">
</form>
<?php 
	// ステ一タス上昇 ////////////////////////////
	if(0 < $char->statuspoint) {
print <<< HTML
	<form action="?char=$_GET[char]" method="post" style="padding:0 15px">
	<h4>Status <a href="?manual#statup" target="_blank" class="a0">?</a></h4>
HTML;

		$Stat	= array("Str","Int","Dex","Spd","Luk");
		print("Point : {$char->statuspoint}<br />\n");
		foreach($Stat as $val) {
			print("{$val}:\n");
			print("<select name=\"up{$val}\" class=\"vcent\">\n");
			for($i=0; $i < $char->statuspoint + 1; $i++)
				print("<option value=\"{$i}\">+{$i}</option>\n");
			print("</select>");
		}
		print("<br />");
		print('<input type="submit" class="btn" name="stup" value="升值">');
		print("\n");

	print("</form>\n");
	}
	?>
	<form action="?char=<?php print $_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>行動模式 <a href="?manual#jdg" target="_blank" class="a0">?</a></h4>
<?php 

		// Action Pattern 行動判定 /////////////////////////
		$list	= JudgeList();// 行動判定條件一覽
		print("<table cellspacing=\"5\"><tbody>\n");
		for($i=0; $i<$char->MaxPatterns(); $i++) {
			print("<tr><td>");
			//----- No
			print( ($i+1)."</td><td>");
			//----- JudgeSelect(判定の種類)
			print("<select name=\"judge".$i."\">\n");
			foreach($list as $val) {//判斷のoption
				$exp	= LoadJudgeData($val);
				print("<option value=\"{$val}\"".($char->judge[$i] == $val ? " selected" : NULL).($exp["css"]?' class="select0"':NULL).">".($exp["css"]?' ':'   ')."{$exp[exp]}</option>\n");
			}
			print("</select>\n");
			print("</td><td>\n");
			//----- 數值(量)
			print("<input type=\"text\" name=\"quantity".$i."\" maxlength=\"4\" value=\"".$char->quantity[$i]."\" style=\"width:56px\" class=\"text\">");
			print("</td><td>\n");
			//----- //SkillSelect(技の種類)
			print("<select name=\"skill".$i."\">\n");
			foreach($char->skill as $val) {//技のoption
				$skill	= LoadSkillData($val);
				print("<option value=\"{$val}\"".($char->action[$i] == $val ? " selected" : NULL).">");
				print($skill["name"].(isset($skill["sp"])?" - (SP:{$skill[sp]})":NULL));
				print("</option>\n");
			}
			print("</select>\n");
			print("</td><td>\n");
			print('<input type="radio" name="PatternNumber" value="'.$i.'">');
			print("</td></tr>\n");
		}
		print("</tbody></table>\n");
	?>
<input type="submit" class="btn" value="確定模式" name="ChangePattern">
<input type="submit" class="btn" value="設置 & 測試" name="TestBattle">
 <a href="?simulate">Simulate</a><br />
<input type="submit" class="btn" value="切換模式" name="PatternMemo">
<input type="submit" class="btn" value="添加" name="AddNewPattern">
<input type="submit" class="btn" value="刪除" name="DeletePattern">
</form>
<form action="?char=<?php print $_GET["char"]?>" method="post" style="padding:0 15px">
<h4>位置 & 保護<a href="?manual#posi" target="_blank" class="a0">?</a></h4>
<table><tbody>
<tr><td>位置(Position) :</td><td><input type="radio" class="vcent" name="position" value="front"
<?php  ($char->position=="front"?print(" checked"):NULL) ?>>前衛(Front)</td></tr>
<tr><td></td><td><input type="radio" class="vcent" name="position" value="back"
<?php  ($char->position=="back"?print(" checked"):NULL) ?>>後衛(Backs)</td></tr>
<tr><td>護衛(Guarding) :</td><td>
<select name="guard">
<?php 

		// 前衛の時の後衛守り //////////////////////////////
		$option	= array(/*
		"always"=> "Always",
		"never"	=> "Never",
		"life25"	=> "If life more than 25%",
		"life50"	=> "If life more than 50%",
		"life75"	=> "If life more than 75%",
		"prob25"	=> "Probability of 25%",
		"prpb50"	=> "Probability of 50%",
		"prob75"	=> "Probability of 75%",
		*/
		"always"=> "必定保護",
		"never"	=> "不保護",
		"life25"	=> "體力25%以上時保護",
		"life50"	=> "體力50%以上時保護",
		"life75"	=> "體力75%以上時保護",
		"prob25"	=> "25%的概率保護",
		"prpb50"	=> "50%的概率保護",
		"prob75"	=> "75%的概率保護",
		);
		foreach($option as $key => $val)
			print("<option value=\"{$key}\"".($char->guard==$key ? " selected" : NULL ).">{$val}</option>");
	?>
	</select>
	</td></tr>
	</tbody></table>
	<input type="submit" class="btn" value="設置">
	</form>
<?php 
		// 裝備中の物表示 ////////////////////////////////
		$weapon	= LoadItemData($char->weapon);
		$shield	= LoadItemData($char->shield);
		$armor	= LoadItemData($char->armor);
		$item	= LoadItemData($char->item);

		$handle	= 0;
		$handle	= $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item["handle"];
	?>
	<div style="margin:0 15px">
	<h4>裝備<a href="?manual#equip" target="_blank" class="a0">?</a></h4>
	<div class="bold u">Current Equip's</div>
	<table>
	<tr><td class="dmg" style="text-align:right">Atk :</td><td class="dmg"><?php print $char->atk[0]?></td></tr>
	<tr><td class="spdmg" style="text-align:right">Matk :</td><td class="spdmg"><?php print $char->atk[1]?></td></tr>
	<tr><td class="recover" style="text-align:right">Def :</td><td class="recover"><?php print $char->def[0]." + ".$char->def[1]?></td></tr>
	<tr><td class="support" style="text-align:right">Mdef :</td><td class="support"><?php print $char->def[2]." + ".$char->def[3]?></td></tr>
	<tr><td class="charge" style="text-align:right">handle :</td><td class="charge"><?php print $handle?> / <?php print $char->GetHandle()?></td></tr>
	</table>
	<form action="?char=<?php print $_GET["char"]?>" method="post">
	<table>
	<tr><td class="align-right">
	武器:</td><td><input type="radio" class="vcent" name="spot" value="weapon">
<?php ShowItemDetail(LoadItemData($char->weapon));?>
	</td></tr><tr><td class="align-right">
	盾:</td><td><input type="radio" class="vcent" name="spot" value="shield">
<?php ShowItemDetail(LoadItemData($char->shield));?>
	</td></tr><tr><td class="align-right">
	甲:</td><td><input type="radio" class="vcent" name="spot" value="armor">
<?php ShowItemDetail(LoadItemData($char->armor));?>
	</td></tr><tr><td class="align-right">
	道具:</td><td><input type="radio" class="vcent" name="spot" value="item">
<?php ShowItemDetail(LoadItemData($char->item));?>
	</td></tr></tbody>
	</table>
	<input type="submit" class="btn" name="remove" value="卸下">
	<input type="submit" class="btn" name="remove_all" value="全卸">
	</form>
	</div>
<?php 

		// 裝備可能な物表示 ////////////////////////////////
		if($JobData["equip"])
			$EquipAllow	= array_flip($JobData["equip"]);//裝備可能な物リスト(反轉)
		else
			$EquipAllow	= array();//裝備可能な物リスト(反轉)
		$Equips		= array("Weapon"=>"2999","Shield"=>"4999","Armor"=>"5999","Item"=>"9999");

		print("<div style=\"padding:15px 15px 0 15px\">\n");
		print("\t<div class=\"bold u\">擁有的 & 容許裝備的</div>\n");
		if($main->item) {
			include(CLASS_JS_ITEMLIST);
			$EquipList	= new JS_ItemList();
			$EquipList->SetID("equip");
			$EquipList->SetName("type_equip");
			// JSを使用しない。
			if($main->no_JS_itemlist)
				$EquipList->NoJS();
			reset($main->item);//これが無いと裝備變更時に表示されない
			foreach($main->item as $key => $val) {
				$item	= LoadItemData($key);
				// 裝備できないので次
				if(!isset( $EquipAllow[ $item["type"] ] ))
					continue;
				$head	= '<input type="radio" name="item_no" value="'.$key.'" class="vcent">';
				$head	.= ShowItemDetail($item,$val,true)."<br />";
				$EquipList->AddItem($item,$head);
			}
			print($EquipList->GetJavaScript("list0"));
			print($EquipList->ShowSelect());
			print('<form action="?char='.$_GET["char"].'" method="post">'."\n");
			print('<div id="list0">'.$EquipList->ShowDefault().'</div>'."\n");
			print('<input type="submit" class="btn" name="equip_item" value="裝備">'."\n");
			print("</form>\n");
		} else {
			print("暫無道具.<br />\n");
		}
		print("</div>\n");

		
		/*
		print("\t<table><tbody><tr><td colspan=\"2\">\n");
		print("\t<span class=\"bold u\">Stock & Allowed to Equip</span></td></tr>\n");
		if($main->item):
			reset($main->item);//これが無いと裝備變更時に表示されない
			foreach($Equips as $key => $val) {
				print("\t<tr><td class=\"align-right\" valign=\"top\">\n");
				print("\t{$key} :</td><td>\n");
				while( substr(key($main->item),0,4) <= $val && substr(current($main->item),0,4) !== false ) {
					$item	= LoadItemData(key($main->item));
					if(!isset( $EquipAllow[ $item["type"] ] )) {
						next($main->item);
						continue;
					}
					print("\t");
					print('<input type="radio" class="vcent" name="item_no" value="'.key($main->item).'">');
					print("\n\t");
					print(current($main->item)."x");
					ShowItemDetail($item);
					print("<br>\n");
					next($main->item);
				}
				print("\t</td></tr>\n");
			}
		else:
			print("<tr><td>No items.</td></tr>");
		endif;
		print("\t</tbody></table>\n");
		*/
	?>
	<form action="?char=<?php print $_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>技能<a href="?manual#skill" target="_blank" class="a0">?</a></h4>
<?php 

		// スキル表示 //////////////////////////////////////
		//include(DATA_SKILL);//ActionPatternに移動
		include_once(DATA_SKILL_TREE);
		if($char->skill) {
			print('<div class="u bold">已掌握的</div>');
			print("<table><tbody>");
			foreach($char->skill as $val) {
				print("<tr><td>");
				$skill	= LoadSkillData($val);
				ShowSkillDetail($skill);
				print("</td></tr>");
			}
			print("</tbody></table>");
			print('<div class="u bold">新技能</div>');
			print("技能點 : {$char->skillpoint}");
			print("<table><tbody>");
			$tree	= LoadSkillTree($char);
			foreach(array_diff($tree,$char->skill) as $val) {
				print("<tr><td>");
				$skill	= LoadSkillData($val);
				ShowSkillDetail($skill,1);
				print("</td></tr>");
			}
			print("</tbody></table>");
			//dump($char->skill);
			//dump($tree);
			print('<input type="submit" class="btn" name="learnskill" value="習得">'."\n");
			print('<input type="hidden" name="learnskill" value="1">'."\n");
		}
		// 轉職 ////////////////////////////////////////////
		if($CanChange) {
			?>

	</form>
	<form action="?char=<?php print $_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>轉職</h4>
	<table><tbody><tr>
<?php 
			foreach($CanChange as $job) {
				print("<td valign=\"bottom\" style=\"padding:5px 30px;text-align:center\">");
				$JOB	= LoadJobData($job);
				print('<img src="'.IMG_CHAR.$JOB["img_".($char->gender?"female":"male")].'">'."<br />\n");//畫像
				print('<input type="radio" value="'.$job.'" name="job">'."<br />\n");
				print($JOB["name_".($char->gender?"female":"male")]);
				print("</td>");
			}
			?>

	</tr></tbody></table>
	<input type="submit" class="btn" name="classchange" value="轉職">
	<input type="hidden" name="classchange" value="1">
<?php 
		}
	?>

	</form>
<?php //その他キャラ
		print('<div  style="padding:15px">');
		foreach($main->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>  ";
		}
		print('</div>');
	}