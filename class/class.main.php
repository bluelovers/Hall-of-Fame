<?php
include(CLASS_USER);
include(GLOBAL_PHP);
class main extends user {

	var $islogin	= false;

//////////////////////////////////////////////////
//	
	function main() {
		$this->SessionSwitch();
		$this->Set_ID_PASS();
		ob_start();
		$this->Order();
		$content	= ob_get_contents();
		ob_end_clean();

		$this->Head();
		print($content);
		$this->Debug();
		//$this->ShowSession();
		$this->Foot();
	}

//////////////////////////////////////////////////
//	
	function Order() {
		// ログイン處理する前に處理するもの
		// まだユ一ザデ一タ讀んでません
		switch(true) {
			case($_GET["menu"] === "auction"):
				include(CLASS_AUCTION);
				$ItemAuction	= new Auction(item);
				$ItemAuction->AuctionHttpQuery("auction");
				$ItemAuction->ItemCheckSuccess();// 競賣が終了した品物を調べる
				$ItemAuction->UserSaveData();// 競賣品と金額を各IDに配って保存する
				break;

			case($_GET["menu"] === "rank"):
				include(CLASS_RANKING);
				$Ranking	= new Ranking();
				break;
		}
		if( true === $message = $this->CheckLogin() ):
		//if( false ):
		// ログイン
			include_once(DATA_ITEM);
			include(CLASS_CHAR);
			if($this->FirstLogin())
				return 0;

			switch(true) {

				case($this->OptionOrder()):	return false;

				case($_POST["delete"]):
					if($this->DeleteMyData())
						return 0;

				// 設定
				case($_SERVER["QUERY_STRING"] === "setting"):
					if($this->SettingProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SettingShow();
					return 0;

				// オ一クション
				case($_GET["menu"] === "auction"):
					$this->LoadUserItem();//道具デ一タ讀む
					$this->AuctionHeader();

					/*
					* 出品用のフォ一ム
					* 表示を要求した場合か、
					* 出品に失敗した場合表示する。
					*/
					$ResultExhibit	= $this->AuctionItemExhibitProcess($ItemAuction);
					$ResultBidding	= $this->AuctionItemBiddingProcess($ItemAuction);
					$ItemAuction->ItemSaveData();// 變更があった場合だけ保存する。
    
					// 出品リストを表示する
					if($_POST["ExhibitItemForm"]) {
						$this->fpCloseAll();
						$this->AuctionItemExhibitForm($ItemAuction);

					// 出品か競標に成功した場合はデ一タを保存する
					} else if($ResultExhibit !== false) {

						if($ResultExhibit === true || $ResultBidding === true)
							$this->SaveData();

						$this->fpCloseAll();
						$this->AuctionItemBiddingForm($ItemAuction);

					// それ以外
					} else {
						$this->fpCloseAll();
						$this->AuctionItemExhibitForm($ItemAuction);
					}

					$this->AuctionFoot($ItemAuction);
					return 0;

				// 狩場
				case($_SERVER["QUERY_STRING"] === "hunt"):
					$this->LoadUserItem();//道具デ一タ讀む
					$this->fpCloseAll();
					$this->HuntShow();
					return 0;

				// 街
				case($_SERVER["QUERY_STRING"] === "town"):
					$this->LoadUserItem();//道具デ一タ讀む
					$this->fpCloseAll();
					$this->TownShow();
					return 0;

				// シミュれ
				case($_SERVER["QUERY_STRING"] === "simulate"):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					if($this->SimuBattleProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SimuBattleShow($result);
					return 0;

				// ユニオン
				case($_GET["union"]):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					include(CLASS_UNION);
					include(DATA_MONSTER);
					if($this->UnionProcess()) {
						// 戰鬥する
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						// 表示
						$this->fpCloseAll();
						$this->UnionShow();
					}
					return 0;

				// 一般モンスタ一
				case($_GET["common"]):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					$this->LoadUserItem();//道具デ一タ讀む
					if($this->MonsterBattle()) {
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						$this->fpCloseAll();
						$this->MonsterShow();
					}
					return 0;

				// キャラステ
				case($_GET["char"]):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					include(DATA_SKILL);
					include(DATA_JUDGE_SETUP);
					$this->LoadUserItem();//道具デ一タ讀む
					$this->CharStatProcess();
					$this->fpCloseAll();
					$this->CharStatShow();
					return 0;

				// 道具一覽
				case($_SERVER["QUERY_STRING"] === "item"):
					$this->LoadUserItem();//道具デ一タ讀む
					//$this->ItemProcess();
					$this->fpCloseAll();
					$this->ItemShow();
					return 0;

				// 精鍊
				case($_GET["menu"] === "refine"):
					$this->LoadUserItem();
					$this->SmithyRefineHeader();
					if($this->SmithyRefineProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$result	= $this->SmithyRefineShow();
					return 0;

				// 製作
				case($_GET["menu"] === "create"):
					$this->LoadUserItem();
					$this->SmithyCreateHeader();
					include(DATA_CREATE);//製作できるものデ一タ等
					if($this->SmithyCreateProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SmithyCreateShow();
					return 0;
				// ショップ(舊式:買う,賣る,打工)
				case($_SERVER["QUERY_STRING"] === "shop"):
					$this->LoadUserItem();//道具デ一タ讀む
					if($this->ShopProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopShow();
					return 0;
				// ショップ(買う)
				case($_GET["menu"] === "buy"):
					$this->LoadUserItem();//道具デ一タ讀む
					$this->ShopHeader();
					if($this->ShopBuyProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopBuyShow();
					return 0;

				// ショップ(賣る)
				case($_GET["menu"] === "sell"):
					$this->LoadUserItem();//道具デ一タ讀む
					$this->ShopHeader();
					if($this->ShopSellProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopSellShow();
					return 0;

				// ショップ(動く)
				case($_GET["menu"] === "work"):
					$this->ShopHeader();
					if($this->WorkProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->WorkShow();
					return 0;

				// ランキング
				case($_GET["menu"] === "rank"):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					$RankProcess	= $this->RankProcess($Ranking);

					if ($RankProcess === "BATTLE") {
						$this->SaveData();
						$this->fpCloseAll();
					} else if ($RankProcess === true) {
						$this->SaveData();
						$this->fpCloseAll();
						$this->RankShow($Ranking);
					} else {
						$this->fpCloseAll();
						$this->RankShow($Ranking);
					}
					return 0;

				// 僱用
				case($_SERVER["QUERY_STRING"] === "recruit"):
					if($this->RecruitProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->RecruitShow($result);
					return 0;

				// それ以外(トップ)
				default:
					$this->CharDataLoadAll();//キャラデ一タ讀む
					$this->fpCloseAll();
					$this->LoginMain();
					return 0;
			}
		else:
		// ログアウト
			$this->fpCloseAll();
			switch(true) {
				case($this->OptionOrder()):	return false;
				case($_POST["Make"]):
					list($bool,$message) = $this->MakeNewData();
					if( true === $bool ) {
						$this->LoginForm($message);
						return false;
					}
				case($_SERVER["QUERY_STRING"] === "newgame"):
					$this->NewForm($message);	return false;
				default:	$this->LoginForm($message);
			}
		endif;
	}

//////////////////////////////////////////////////
//	UpDate,BBS,Manual等
	function OptionOrder() {
		$this->fpCloseAll();
		switch(true) {
			case($_SERVER["QUERY_STRING"] === "rank"):	RankAllShow();	return true;
			case($_SERVER["QUERY_STRING"] === "update"):	ShowUpDate();	return true;
			case($_SERVER["QUERY_STRING"] === "bbs"):	$this->bbs01();	return true;
			case($_SERVER["QUERY_STRING"] === "manual"):	ShowManual();	return true;
			case($_SERVER["QUERY_STRING"] === "manual2"):	ShowManual2();	return true;
			case($_SERVER["QUERY_STRING"] === "tutorial"):	ShowTutorial();	return true;
			case($_SERVER["QUERY_STRING"] === "log"):
				ShowLogList();
				return true;
			case($_SERVER["QUERY_STRING"] === "clog"): LogShowCommon(); return true;
			case($_SERVER["QUERY_STRING"] === "ulog"): LogShowUnion(); return true;
			case($_SERVER["QUERY_STRING"] === "rlog"): LogShowRanking(); return true;
			case($_GET["gamedata"]):
				ShowGameData();
				return true;
			case($_GET["log"]):
				ShowBattleLog($_GET["log"]);
				return true;
			case($_GET["ulog"]):
				ShowBattleLog($_GET["ulog"],"UNION");
				return true;
			case($_GET["rlog"]):
				ShowBattleLog($_GET["rlog"],"RANK");
				return true;
		}
	}

//////////////////////////////////////////////////
//	敵の數を返す	數～數+2(max:5)
	function EnemyNumber($party) {
		$min	= count($party);//プレイヤ一のPT數
		if($min == 5)//5人なら5匹
			return 5;
		$max	= $min + ENEMY_INCREASE;// つまり、+2なら[1人:1～3匹] [2人:2～4匹] [3:3-5] [4:4-5] [5:5]
		if($max>5)
			$max	= 5;
		mt_srand();
		return mt_rand($min,$max);
	}
//////////////////////////////////////////////////
//	出現する確率から敵を選んで返す
	function SelectMonster($monster) {
		foreach($monster as $val)
			$max	+= $val[0];//確率の合計
		$pos	= mt_rand(0,$max);//0～合計 の中で亂數を取る
		foreach($monster as $monster_no => $val) {
			$upp	+= $val[0];//その時點での確率の合計
			if($pos <= $upp)//合計より低ければ　敵が決定される
				return $monster_no;
		}
	}
//////////////////////////////////////////////////
//	敵のPTを作成、返す
//	Specify=敵指定(配列)
	function EnemyParty($Amount,$MonsterList,$Specify=false) {

		// 指定モンスタ一
		if($Specify) {
			$MonsterNumbers	= $Specify;
		}

		// モンスタ一をとりあえず配列に全部入れる
		$enemy	= array();
		if(!$Amount)
			return $enemy;
		mt_srand();
		for($i=0; $i<$Amount; $i++)
			$MonsterNumbers[]	= $this->SelectMonster($MonsterList);

		// 重複しているモンスタ一を調べる
		$overlap	= array_count_values($MonsterNumbers);

		// 敵情報を讀んで配列に入れる。
		include(CLASS_MONSTER);
		foreach($MonsterNumbers as $Number) {
			if(1 < $overlap[$Number])//1匹以上出現するなら名前に記號をつける。
				$enemy[]	= new monster(CreateMonster($Number,true));
			else
				$enemy[]	= new monster(CreateMonster($Number));
		}
		return $enemy;
	}
//////////////////////////////////////////////////
//	キャラ詳細表示から送られたリクエストを處理する
//	長い...(100行オ一バ一)
	function CharStatProcess() {
		$char	= &$this->char[$_GET["char"]];
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
				$char->SaveCharData($this->id);
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
				$char->SaveCharData($this->id);
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
					$char->SaveCharData($this->id);
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
						$char->SaveCharData($this->id);
						$this->CharTestDoppel();
					}
				break;
			//	行動模式メモ(交換)
			case($_POST["PatternMemo"]):
				if($char->ChangePatternMemo()) {
					$char->SaveCharData($this->id);
					ShowResult("模式交換完成","margin15");
					return true;
				}
				break;
			//	指定行に追加
			case($_POST["AddNewPattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->AddPattern($_POST["PatternNumber"])) {
					$char->SaveCharData($this->id);
					ShowResult("模式追加完成","margin15");
					return true;
				}
				break;
			//	指定行を削除
			case($_POST["DeletePattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->DeletePattern($_POST["PatternNumber"])) {
					$char->SaveCharData($this->id);
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
				if(!$char->{$_POST["spot"]}) {// $this と $char の區別注意！
					ShowError("指定位置沒有裝備","margin15");
					return false;
				}
				$item	= LoadItemData($char->{$_POST["spot"]});
				if(!$item) return false;
				$this->AddItem($char->{$_POST["spot"]});
				$this->SaveUserItem();
				$char->{$_POST["spot"]}	= NULL;
				$char->SaveCharData($this->id);
				SHowResult($char->Name()." 的 {$item[name]} 解除。","margin15");
				return true;
				break;
			//	裝備全部はずす
			case($_POST["remove_all"]):
				if($char->weapon || $char->shield || $char->armor || $char->item ) {
					if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
					if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
					if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
					if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
					$this->SaveUserItem();
					$char->SaveCharData($this->id);
					ShowResult($char->Name()." 的裝備全部解除","margin15");
					return true;
				}	break;
			//	指定物を裝備する
			case($_POST["equip_item"]):
				$item_no	= $_POST["item_no"];
				if(!$this->item["$item_no"]) {//その道具を所持しているか
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
					$this->DeleteItem($item_no);
					foreach($return as $no) {
						$this->AddItem($no);
					}
				}

				$this->SaveUserItem();
				$char->SaveCharData($this->id);
				ShowResult("{$char->name} 的 {$item[name]} 裝備.","margin15");
				return true;
				break;
			// スキル習得
			case($_POST["learnskill"]):
				if(!$_POST["newskill"]) {
					ShowError("沒選定技能","margin15");
					return false;
				}

				$char->SetUser($this->id);
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
						if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
						if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
						if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
						if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
						$this->SaveUserItem();
					}
					// 保存
					$char->SaveCharData($this->id);
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
					if($this->DeleteItem("7500",1) == 1) {
						ShowResult($char->Name()."   ".$return." 改名完成。","margin15");
						$char->ChangeName($return);
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
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
					if($this->item[$itemNo]) {
						$item	= LoadItemData($itemNo);
						print('<option value="'.$itemNo.'">'.$item[name]." x".$this->item[$itemNo].'</option>'."\n");
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
					if($this->DeleteItem(6000) == 0) {
						ShowError("沒有道具。","margin15");
						return false;
					}
					if(1 < $char->spd) {
						$dif	= $char->spd - 1;
						$char->spd	-= $dif;
						$char->statuspoint	+= $dif;
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
						ShowResult("點數歸還","margin15");
						return true;
					}
				}
				if($lowLimit) {
					if(!$this->item[$_POST["itemUse"]]) {
						ShowError("沒有道具。","margin15");
						return false;
					}
					if($lowLimit < $char->str) {$dif = $char->str - $lowLimit; $char->str -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->int) {$dif = $char->int - $lowLimit; $char->int -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->dex) {$dif = $char->dex - $lowLimit; $char->dex -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->spd) {$dif = $char->spd - $lowLimit; $char->spd -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->luk) {$dif = $char->luk - $lowLimit; $char->luk -= $dif; $pointBack += $dif;}
					if($pointBack) {
						if($this->DeleteItem($_POST["itemUse"]) == 0) {
							ShowError("沒有道具。","margin15");
							return false;
						}
						$char->statuspoint	+= $pointBack;
						// 裝備も全部解除
						if($char->weapon || $char->shield || $char->armor || $char->item ) {
							if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
							if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
							if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
							if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
							ShowResult($char->Name()." 的所有裝備解除","margin15");
						}
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
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
				//$this->DeleteChar($char->birth);
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
	function CharStatShow() {
		$char	= &$this->char[$_GET["char"]];
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
		foreach($this->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>  ";
		}
		print("</div>");
	?>
<h4>人物狀態 <a href="?manual#charstat" target="_blank" class="a0">?</a></h4>
<?php 
		$char->ShowCharDetail();
		// 改名
		if($this->item["7500"])
			print('<input type="submit" class="btn" name="rename" value="ChangeName">'."\n");
		// ステ一タスリセット系
		if($this->item["7510"] ||
			$this->item["7511"] ||
			$this->item["7512"] ||
			$this->item["7513"] ||
			$this->item["7520"]) {
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
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$EquipList	= new JS_ItemList();
			$EquipList->SetID("equip");
			$EquipList->SetName("type_equip");
			// JSを使用しない。
			if($this->no_JS_itemlist)
				$EquipList->NoJS();
			reset($this->item);//これが無いと裝備變更時に表示されない
			foreach($this->item as $key => $val) {
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
		if($this->item):
			reset($this->item);//これが無いと裝備變更時に表示されない
			foreach($Equips as $key => $val) {
				print("\t<tr><td class=\"align-right\" valign=\"top\">\n");
				print("\t{$key} :</td><td>\n");
				while( substr(key($this->item),0,4) <= $val && substr(current($this->item),0,4) !== false ) {
					$item	= LoadItemData(key($this->item));
					if(!isset( $EquipAllow[ $item["type"] ] )) {
						next($this->item);
						continue;
					}
					print("\t");
					print('<input type="radio" class="vcent" name="item_no" value="'.key($this->item).'">');
					print("\n\t");
					print(current($this->item)."x");
					ShowItemDetail($item);
					print("<br>\n");
					next($this->item);
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
		foreach($this->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>  ";
		}
		print('</div>');
	}
//////////////////////////////////////////////////
//	('A`)...
	function CharTestDoppel() {
		if(!$_POST["TestBattle"]) return 0;

		$char	= $this->char[$_GET["char"]];
		$this->DoppelBattle(array($char));
	}
//////////////////////////////////////////////////
//	ドッペルゲンガ一と戰う。
	function DoppelBattle($party,$turns=10) {
		//$enemy	= $party;
		//これが無いとPHP4or5 で違う結果になるんです
		//$enemy	= unserialize(serialize($enemy));
		// ↓
		foreach($party as $key => $char) {
			$enemy[$key]	= new char();
			$enemy[$key]->SetCharData(get_object_vars($char));
			
		}
		foreach($enemy as $key => $doppel) {
			//$doppel->judge	= array();//コメントを取るとドッペルが行動しない。
			$enemy[$key]->ChangeName("ニセ".$doppel->name);
		}
		//dump($enemy[0]->judge);
		//dump($party[0]->judge);

		include(CLASS_BATTLE);
		$battle	= new battle($party,$enemy);
		$battle->SetTeamName($this->name,"ドッペル");
		$battle->LimitTurns($turns);//最大タ一ン數は10
		$battle->NoResult();
		$battle->Process();//戰鬥開始
		return true;
	}
//////////////////////////////////////////////////
//
	function SimuBattleProcess() {
		if($_POST["simu_battle"]) {
			$this->MemorizeParty();//パ一ティ一記憶
			// 自分パ一ティ一
			foreach($this->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					$MyParty[]	= $this->char[$key];
			}
			if( count($MyParty) === 0) {
				ShowError('戰鬥至少要一個人參加',"margin15");
				return false;
			} else if(5 < count($MyParty)) {
				ShowError('戰鬥最多只能上五個人',"margin15");
				return false;
			}
			$this->DoppelBattle($MyParty,50);
			return true;
		}
	}
//////////////////////////////////////////////////
//	
	function SimuBattleShow($message=false) {
		print('<div style="margin:15px">');
		ShowError($message);
		print('<span class="bold">模擬戰</span>');
		print('<h4>Teams</h4></div>');
		print('<form action="'.INDEX.'?simulate" method="post">');
		$this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" name="simu_battle" value="戰鬥!">
	<input type="reset" class="btn" value="重置"><br>
	保存此隊伍:<input type="checkbox" name="memory_party" value="1">
	</div></form>
<?php 
	}
//////////////////////////////////////////////////
//	
	function HuntShow() {
		include(DATA_LAND);
		include(DATA_LAND_APPEAR);
		print('<div style="margin:15px">');
		print('<h4>普通怪物</h4>');
		print('<div style="margin:0 20px">');

		$mapList	= LoadMapAppear($this);
		foreach($mapList as $map) {
			list($land)	= LandInformation($map);
			print("<p style='display:inline;margin-right:32px;'><a href=\"?common={$map}\">{$land[name]}</a>");
			//print(" ({$land[proper]})");
			print("</p>");
		}

		// Union
		print("</div>\n");
		$files	= glob(UNION."*");
		if($files) {
			include(CLASS_UNION);
			include(DATA_MONSTER);
			foreach($files as $file) {
				$UnionMons	= new union($file);
				if($UnionMons->is_Alive())
					$Union[]	= $UnionMons;
			}
		}
		if($Union) {
			print('<h4>BOSS</h4>');
			$result = $this->CanUnionBattle();
			if($result !== true) {
				$left_minute	= floor($result/60);
				$left_second	= $result%60;
				print('<div style="margin:0 20px">');
				print('離下次戰鬥還需要 : <span class="bold">'.$left_minute. ":".sprintf("%02d",$left_second)."</span>");
				print("</div>");
			}
			print("</div>");
			$this->ShowCharacters($Union);
		} else {
			print("</div>");
		}

		// union
		print("<div style=\"margin:0 15px\">\n");
		print("<h4>BOSS戰記錄 <a href=\"?ulog\">全表示</a></h4>\n");
		print("<div style=\"margin:0 20px\">\n");
		$log	= @glob(LOG_BATTLE_UNION."*");
		foreach(array_reverse($log) as $file) {
			$limit++;
			BattleLogDetail($file,"UNION");
			if(15 <= $limit)
				break;
		}
		print("</div></div>\n");
	}
//////////////////////////////////////////////////
//	モンスタ一の表示
	function MonsterShow() {
		$land_id	= $_GET["common"];
		include(DATA_LAND);
		include_once(DATA_LAND_APPEAR);
		// まだ行けないマップなのに行こうとした。
		if(!in_array($_GET["common"],LoadMapAppear($this))) {
			print('<div style="margin:15px">not appeared or not exist</div>');
			return false;
		}
		list($land,$monster_list)	= LandInformation($land_id);
		if(!$land || !$monster_list) {
			print('<div style="margin:15px">fail to load</div>');
			return false;
		}

		print('<div style="margin:15px">');
		ShowError($message);
		print('<span class="bold">'.$land["name"].'</span>');
		print('<h4>隊伍</h4></div>');
		print('<form action="'.INDEX.'?common='.$_GET["common"].'" method="post">');
		$this->ShowCharacters($this->char,"CHECKBOX",explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" name="monster_battle" value="戰鬥!">
	<input type="reset" class="btn" value="重置"><br>
	保存此隊伍:<input type="checkbox" name="memory_party" value="1">
	</div></form>
<?php 
		include(DATA_MONSTER);
		include(CLASS_MONSTER);
		foreach($monster_list as $id =>$val) {
			if($val[1])
				$monster[]	= new monster(CreateMonster($id));
		}
		print('<div style="margin:15px"><h4>MonsterAppearance</h4></div>');
		$this->ShowCharacters($monster,"MONSTER",$land["land"]);
	}

//////////////////////////////////////////////////
//	モンスタ一との戰鬥
	function MonsterBattle() {
		if($_POST["monster_battle"]) {
			$this->MemorizeParty();//パ一ティ一記憶
			// そのマップで戰えるかどうか確認する。
			include_once(DATA_LAND_APPEAR);
			$land	= LoadMapAppear($this);
			if(!in_array($_GET["common"],$land)) {
				ShowError("沒有出現地圖","margin15");
				return false;
			}

			// Timeが足りてるかどうか確認する
			if($this->time < NORMAL_BATTLE_TIME) {
				ShowError("Time 不足 (必要 Time:".NORMAL_BATTLE_TIME.")","margin15");
				return false;
			}
			// 自分パ一ティ一
			foreach($this->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					$MyParty[]	= $this->char[$key];
			}
			if( count($MyParty) === 0) {
				ShowError('戰鬥至少要一個人參加',"margin15");
				return false;
			} else if(5 < count($MyParty)) {
				ShowError('戰鬥最多只能上五個人',"margin15");
				return false;
			}
			// 敵パ一ティ一(または一匹)
			include(DATA_LAND);
			include(DATA_MONSTER);
			list($Land,$MonsterList)	= LandInformation($_GET["common"]);
			$EneNum	= $this->EnemyNumber($MyParty);
			$EnemyParty	= $this->EnemyParty($EneNum,$MonsterList);

			$this->WasteTime(NORMAL_BATTLE_TIME);//時間の消費
			include(CLASS_BATTLE);
			$battle	= new battle($MyParty,$EnemyParty);
			$battle->SetBackGround($Land["land"]);//背景
			$battle->SetTeamName($this->name,$Land["name"]);
			$battle->Process();//戰鬥開始
			$battle->SaveCharacters();//キャラデ一タ保存
			list($UserMoney)	= $battle->ReturnMoney();//戰鬥で得た合計金額
			//お金を增やす
			$this->GetMoney($UserMoney);
			//戰鬥ログの保存
			if($this->record_btl_log)
				$battle->RecordLog();

			// 道具を受け取る
			if($itemdrop	= $battle->ReturnItemGet(0)) {
				$this->LoadUserItem();
				foreach($itemdrop as $itemno => $amount)
					$this->AddItem($itemno,$amount);
				$this->SaveUserItem();
			}

			//dump($itemdrop);
			//dump($this->item);
			return true;
		}
	}

//////////////////////////////////////////////////
	function ItemProcess() {
	}

//////////////////////////////////////////////////
//	
	function ItemShow() {
		?>
		<div style="margin:15px">
		<h4>道具</h4>
		<div style="margin:0 20px">
<?php 
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$goods	= new JS_ItemList();
			$goods->SetID("my");
			$goods->SetName("type");
			// JSを使用しない。
			if($this->no_JS_itemlist)
				$goods->NoJS();
			//$goods->ListTable("<table>");
			//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$string	= ShowItemDetail($item,$val,1)."<br />";
				//$string	= "<tr><td>".$no."</td><td>".ShowItemDetail($item,$val,1)."</td></tr>";
				$goods->AddItem($item,$string);
			}
			print($goods->GetJavaScript("list"));
			print($goods->ShowSelect());
			print('<div id="list">'.$goods->ShowDefault().'</div>');
		} else {
			print("No items.");
		}
		print("</div></div>");
	}
//////////////////////////////////////////////////
//	店ヘッダ
	function ShopHeader() {
		?>
<div style="margin:15px">
<h4>店</h4>

<div style="width:600px">
<div style="float:left;width:50px;">
<img src="<?php print IMG_CHAR?>ori_002.gif" />
</div>
<div style="float:right;width:550px;">
歡迎光臨一<br />
<a href="?menu=buy">買</a> / <a href="?menu=sell">賣</a><br />
<a href="?menu=work">打工</a>
</div>
<div style="clear:both"></div>
</div>

</div>
<?php 
	}
//////////////////////////////////////////////////
//
	function ShopProcess() {
		switch(true) {
			case($_POST["partjob"]):
				if($this->WasteTime(100)) {
					$this->GetMoney(500);
					ShowResult("工作".MoneyFormat(500)." げっとした!(?)","margin15");
					return true;
				} else {
					ShowError("時間が無い。動くなんてもったいない。(?)","margin15");
					return false;
				}
			case($_POST["shop_buy"]):
				$ShopList	= ShopList();//賣ってるものデ一タ
				if($_POST["item_no"] && in_array($_POST["item_no"],$ShopList)) {
					if(ereg("^[0-9]",$_POST["amount"])) {
						$amount	= (int)$_POST["amount"];
						if($amount == 0)
							$amount	= 1;
					} else {
						$amount	= 1;
					}
					$item	= LoadItemData($_POST["item_no"]);
					$need	= $amount * $item["buy"];//購入に必要なお金
					if($this->TakeMoney($need)) {// お金を引けるかで判定。
						$this->AddItem($_POST["item_no"],$amount);
						$this->SaveUserItem();
						if(1 < $amount) {
							$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
							ShowResult("{$img}{$item[name]}  {$amount}個 買入 (".MoneyFormat($item["buy"])." x{$amount} = ".MoneyFormat($need).")","margin15");
							return true;
						} else {
							$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
							ShowResult("{$img}{$item[name]}個 買入 (".MoneyFormat($need).")","margin15");
							return true;
						}
					} else {//資金不足
						ShowError("資金不足(需要".MoneyFormat($need).")","margin15");
						return false;
					}
				}
				break;
			case($_POST["shop_sell"]):
				if($_POST["item_no"] && $this->item[$_POST["item_no"]]) {
					if(ereg("^[0-9]",$_POST["amount"])) {
						$amount	= (int)$_POST["amount"];
						if($amount == 0)
							$amount	= 1;
					} else {
						$amount	= 1;
					}
					// 消した個數(超過して賣られるのも防ぐ)
					$DeletedAmount	= $this->DeleteItem($_POST["item_no"],$amount);
					$item	= LoadItemData($_POST["item_no"]);
					$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
					$this->GetMoney($price*$DeletedAmount);
					$this->SaveUserItem();
					if($DeletedAmount != 1)
						$add	= " x{$DeletedAmount}";
					$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
					ShowResult("{$img}{$item[name]}{$add}".MoneyFormat($price*$DeletedAmount)." 出售","margin15");
					return true;
				}
				break;
		}
	}
//////////////////////////////////////////////////
//	
	function ShopShow($message=NULL) {
		?>
	<div style="margin:15px">
	<?php print ShowError($message)?>
	<h4>Goods List</h4>
	<div style="margin:0 20px">
<?php 
		include(CLASS_JS_ITEMLIST);
		$ShopList	= ShopList();//賣ってるものデ一タ

		$goods	= new JS_ItemList();
		$goods->SetID("JS_buy");
		$goods->SetName("type_buy");
		// JSを使用しない。
		if($this->no_JS_itemlist)
			$goods->NoJS();
		foreach($ShopList as $no) {
			$item	= LoadItemData($no);
			$string	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
			$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($item["buy"])."</span>".ShowItemDetail($item,false,1)."<br />";
			$goods->AddItem($item,$string);
		}
		print($goods->GetJavaScript("list_buy"));
		print($goods->ShowSelect());

		print('<form action="?shop" method="post">'."\n");
		print('<div id="list_buy">'.$goods->ShowDefault().'</div>'."\n");
		print('<input type="submit" class="btn" name="shop_buy" value="買">'."\n");
		print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)<br />'."\n");
		print('<input type="hidden" name="shop_buy" value="1">');
		print('</form></div>'."\n");

		print("<h4>My Items<a name=\"sell\"></a></h4>\n");//所持物賣る
		print('<div style="margin:0 20px">'."\n");
		if($this->item) {
			$goods	= new JS_ItemList();
			$goods->SetID("JS_sell");
			$goods->SetName("type_sell");
			// JSを使用しない。
			if($this->no_JS_itemlist)
				$goods->NoJS();
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
				$string	= '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
				$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";
				$head	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">'.MoneyFormat($item["buy"]);
				$goods->AddItem($item,$string);
			}
			print($goods->GetJavaScript("list_sell"));
			print($goods->ShowSelect());
	
			print('<form action="?shop" method="post">'."\n");
			print('<div id="list_sell">'.$goods->ShowDefault().'</div>'."\n");
			print('<input type="submit" class="btn" name="shop_sell" value="Sell">');
			print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)'."\n");
			print('<input type="hidden" name="shop_sell" value="1">');
			print('</form>'."\n");
		} else {
			print("No items");
		}
		print("</div>\n");
/*
		if($this->item) {
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
				print('<input type="radio" class="vcent" name="item_no" value="'.$no.'">');
				print(MoneyFormat($price));
				print("   {$val}x");
				ShowItemDetail($item);
				print("<br>");
			}
		} else
			print("No items.<br>");
		print('Amount <input type="text" name="amount" style="width:50px" class="text vcent">(input if 2 or more)<br />'."\n");
		print('<input type="submit" class="btn vcent" name="shop_sell" value="Sell">');
		print('<input type="hidden" name="shop_sell" value="1">');
		print('</form>');*/
		?>
<form action="?shop" method="post">
<h4>打工</h4>
<div style="margin:0 20px">
店で打工してお金を得ます...<br />
<input type="submit" class="btn" name="partjob" value="打工">
Get <?php print MoneyFormat("500")?> for 100Time.
</form></div></div>
<?php 
	}

//////////////////////////////////////////////////
	function ShopBuyProcess() {
		//dump($_POST);
		if(!$_POST["ItemBuy"])
			return false;

		print("<div style=\"margin:15px\">");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6" style="text-align:center">價格</td>'.
		'<td class="td6" style="text-align:center">數</td>'.
		'<td class="td6" style="text-align:center">共計</td>'.
		'<td class="td6" style="text-align:center">道具</td></tr>'."\n");
		$moneyNeed	= 0;
		$ShopList	= ShopList();
		foreach($ShopList as $itemNo) {
			if(!$_POST["check_".$itemNo])
				continue;
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			$amount	= (int)$_POST["amount_".$itemNo];
			if($amount < 0)
				$amount	= 0;
			
			//print("$itemNo x $Deleted<br>");
			$buyPrice	= $item["buy"];
			$Total	= $amount * $buyPrice;
			$moneyNeed	+= $Total;
			print("<tr><td class=\"td7\">");
			print(MoneyFormat($buyPrice)."\n");
			print("</td><td class=\"td7\">");
			print("x {$amount}\n");
			print("</td><td class=\"td7\">");
			print("= ".MoneyFormat($Total)."\n");
			print("</td><td class=\"td8\">");
			print(ShowItemDetail($item)."\n");
			print("</td></tr>\n");
			$this->AddItem($itemNo,$amount);
		}
		print("<tr><td colspan=\"4\" class=\"td8\">共計 : ".MoneyFormat($moneyNeed)."</td></tr>");
		print("</table>\n");
		print("</div>");
		if($this->TakeMoney($moneyNeed)) {
			$this->SaveUserItem();
			return true;
		} else {
			ShowError("您沒有足夠的錢","margin15");
			return false;
		}
	}
//////////////////////////////////////////////////
	function ShopBuyShow() {
		print('<div style="margin:15px">'."\n");
		print("<h4>購買</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print('<form action="?menu=buy" method="post">'."\n");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6"></td>'.
		'<td style="text-align:center" class="td6">價格</td>'.
		'<td style="text-align:center" class="td6">數</td>'.
		'<td style="text-align:center" class="td6">道具</td></tr>'."\n");
		$ShopList	= ShopList();
		foreach($ShopList as $itemNo) {
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">'."\n");
			print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// 買值
			$price	= $item["buy"];
			print(MoneyFormat($price));
			print("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="1" style="width:60px" class="text">'."\n");
			print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print(ShowItemDetail($item));
			print("</td></tr>\n");
		}
		print("</table>\n");
		print('<input type="submit" name="ItemBuy" value="買" class="btn">'."\n");
		print("</form>\n");

		print("</div>\n");
	}
//////////////////////////////////////////////////
	function ShopSellProcess() {
		//dump($_POST);
		if(!$_POST["ItemSell"])
			return false;

		$GetMoney	= 0;
		print("<div style=\"margin:15px\">");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6" style="text-align:center">價格</td>'.
		'<td class="td6" style="text-align:center">數</td>'.
		'<td class="td6" style="text-align:center">共計</td>'.
		'<td class="td6" style="text-align:center">道具</td></tr>'."\n");
		foreach($this->item as $itemNo => $amountHave) {
			if(!$_POST["check_".$itemNo])
				continue;
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			$amount	= (int)$_POST["amount_".$itemNo];
			if($amount < 0)
				$amount	= 0;
			$Deleted	= $this->DeleteItem($itemNo,$amount);
			//print("$itemNo x $Deleted<br>");
			$sellPrice	= ItemSellPrice($item);
			$Total	= $Deleted * $sellPrice;
			$getMoney	+= $Total;
			print("<tr><td class=\"td7\">");
			print(MoneyFormat($sellPrice)."\n");
			print("</td><td class=\"td7\">");
			print("x {$Deleted}\n");
			print("</td><td class=\"td7\">");
			print("= ".MoneyFormat($Total)."\n");
			print("</td><td class=\"td8\">");
			print(ShowItemDetail($item)."\n");
			print("</td></tr>\n");
		}
		print("<tr><td colspan=\"4\" class=\"td8\">共計 : ".MoneyFormat($getMoney)."</td></tr>");
		print("</table>\n");
		print("</div>");
		$this->SaveUserItem();
		$this->GetMoney($getMoney);
		return true;
	}
//////////////////////////////////////////////////
	function ShopSellShow() {
		print('<div style="margin:15px">'."\n");
		print("<h4>出售</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print('<form action="?menu=sell" method="post">'."\n");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6"></td>'.
		'<td style="text-align:center" class="td6">價格</td>'.
		'<td style="text-align:center" class="td6">數</td>'.
		'<td style="text-align:center" class="td6">道具</td></tr>'."\n");
		foreach($this->item as $itemNo => $amount) {
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">'."\n");
			print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// 價格
			$price	= ItemSellPrice($item);
			print(MoneyFormat($price));
			print("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="'.$amount.'" style="width:60px" class="text">'."\n");
			print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print(ShowItemDetail($item,$amount));
			print("</td></tr>\n");
		}
		print("</table>\n");
		print('<input type="submit" name="ItemSell" value="Sell" class="btn" />'."\n");
		print('<input type="hidden" name="ItemSell" value="1" />'."\n");
		print("</form>\n");

		print("</div>\n");
	}
//////////////////////////////////////////////////
//	打工處理
	function WorkProcess() {
		/*if($_POST["amount"]) {
			$amount	= (int)$_POST["amount"];
			// 1以上10以下
			if(0 < $amount && $amount < 11) {
				$time	= $amount * 100;
				$money	= $amount * 500;
				if($this->WasteTime($time)) {
					ShowResult(MoneyFormat($money)." げっとした！","margin15");
					$this->GetMoney($money);
					return true;
				} else {
					ShowError("您沒有足夠的時間。","margin15");
					return false;
				}
			}
		}*/
	}
//////////////////////////////////////////////////
//	打工表示
	function WorkShow() {
		?>
<div style="margin:15px">
<h4>一份兼職工作！</h4>
<form method="post" action="?menu=work">
<p>1回 100Time<br />
給與 : <?php print MoneyFormat(500)?>/回</p>
<select name="amount">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select><br />
<input type="submit" value="打工" class="btn"/>
</form>
</div>
<?php 
	}
//////////////////////////////////////////////////
	function RankProcess(&$Ranking) {

		// RankBattle
		if($_POST["ChallengeRank"]) {
			if(!$this->party_rank) {
				ShowError("小隊尚未設定","margin15");
				return false;
			}
			$result	= $this->CanRankBattle();
			if(is_array($result)) {
				ShowError("仍需等待時間（？）","margin15");
				return false;
			}

			/*
				$BattleResult = 0;//勝利
				$BattleResult = 1;//敗北
				$BattleResult = "d";//引分
			*/
			//list($message,$BattleResult)	= $Rank->Challenge(&$this);
			$Result	= $Ranking->Challenge(&$this);

			//if($Result === "Battle")
			//	$this->RankRecord($BattleResult,"CHALLENGE",false);

			/*
			// 勝敗によって次までの戰鬥の時間を設定する
			//勝利
			if($BattleResult === 0) {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);

			//敗北
			} else if($BattleResult === 1) {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			//引分け
			} else if($BattleResult === "d") {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			}
			*/

			return $Result;// 戰鬥していれば $Result = "Battle";
		}

		// ランキング用のチ一ム登錄
		if($_POST["SetRankTeam"]) {
			$now	= time();
			// まだ設定時間が殘っている。
			if(($now - $this->rank_set_time) < RANK_TEAM_SET_TIME) {
				$left	= RANK_TEAM_SET_TIME - ($now - $this->rank_set_time);
				$day	= floor($left / 3600 / 24);
				$hour	= floor($left / 3600)%24;
				$min	= floor(($left % 3600)/60);
				$sec	= floor(($left % 3600)%60);
				ShowError("離再設定隊伍還需 {$day}日 と {$hour}小時 {$min}分 {$sec}秒","margin15");
				return false;
			}
			foreach($this->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					$checked[]	= $key;
			}
			// 設定キャラ數が多いか少なすぎる
			if(count($checked) == 0 || 5 < count($checked)) {
				ShowError("隊伍人數應大於1人小於5人","margin15");
				return false;
			}

			$this->party_rank	= implode("<>",$checked);
			$this->rank_set_time	= $now;
			ShowResult("隊伍設定完成","margin15");
			return true;
		}
	}
//////////////////////////////////////////////////
//	
	function RankShow(&$Ranking) {

		//$ProcessResult	= $this->RankProcess($Ranking);// array();

		//戰鬥が行われたので表示しない。
		//if($ProcessResult === "BATTLE")
		//	return true;

		// チ一ム再設定の殘り時間計算
		$now	= time();
		if( ($now - $this->rank_set_time) < RANK_TEAM_SET_TIME) {
			$left	= RANK_TEAM_SET_TIME - ($now - $this->rank_set_time);
			$hour	= floor($left / 3600);
			$min	= floor(($left % 3600)/60);
			$left_mes	= "<div class=\"bold\">{$hour}Hour {$min}minutes left to set again.</div>\n";
			$disable	= " disabled";
		}
			?>

	<div style="margin:15px">
	<?php print ShowError($message)?>
	<form action="?menu=rank" method="post">
	<h4>排行榜(Ranking) - <a href="?rank">查看排名</a> <a href="?manual#ranking" target="_blank" class="a0">?</a></h4>
	<?php
		// 挑戰できるかどうか(時間の經過で)
		$CanRankBattle	= $this->CanRankBattle();
		if($CanRankBattle !== true) {
			print('<p>Time left to Next : <span class="bold">');
			print($CanRankBattle[0].":".sprintf("%02d",$CanRankBattle[1]).":".sprintf("%02d",$CanRankBattle[2]));
			print("</span></p>\n");
			$disableRB	= " disabled";
		}

		print("<div style=\"width:100%;padding-left:30px\">\n");
		print("<div style=\"float:left;width:50%\">\n");
		print("<div class=\"u\">TOP 5</div>\n");
		$Ranking->ShowRanking(0,4);
		print("</div>\n");
		print("<div style=\"float:right;width:50%\">\n");
		print("<div class=\"u\">NEAR 5</div>\n");
		$Ranking->ShowRankingRange($this->id,5);
		print("</div>\n");
		print("<div style=\"clear:both\"></div>\n");
		print("</div>\n");

		// 舊ランク用
		//$Rank->dump();
		/*
		print("<table><tbody><tr><td style=\"padding:0 50px 0 0\">\n");
		print("<div class=\"bold u\">RANKING</div>");
		$Rank->ShowRanking(0,10);
		print("</td><td>");
		print("<div class=\"bold u\">Nearly</div>");
		$Rank->ShowNearlyRank($this->id);
		print("</td></tr></tbody></table>\n");
		*/
	?>
	<input type="submit" class="btn" value="挑戰！" name="ChallengeRank" style="width:160px"<?php print $disableRB?> />
	</form>
	<form action="?menu=rank" method="post">
	<h4>隊伍設置(Team Setting)</h4>
	<p>排名戰隊伍設定。<br />
	這裡設置排名戰隊伍。</p>
	</div>
<?php $this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_rank));?>

	<div style="margin:15px">
	<?php print $left_mes?>
	<input type="submit" class="btn" style="width:160px" value="設定隊伍"<?php print $disable?> />
	<input type="hidden" name="SetRankTeam" value="1" />
	<p>設定後<?php print $reset=floor(RANK_TEAM_SET_TIME/(60*60))?>小時後才能再設置。<br />Team setting disabled after <?php print $reset?>hours once set.</p>
	</form>
	</div>
<?php 
	}
//////////////////////////////////////////////////
	function RecruitProcess() {

		// 僱用數限界
		if( MAX_CHAR <= count($this->char) )
			return false;

		include(DATA_BASE_CHAR);
		if($_POST["recruit"]) {
			// キャラのタイプ
			switch($_POST["recruit_no"]) {
				case "1": $hire = 2000; $charNo	= 1; break;
				case "2": $hire = 2000; $charNo	= 2; break;
				case "3": $hire = 2500; $charNo	= 3; break;
				case "4": $hire = 4000; $charNo	= 4; break;
				default:
					ShowError("未選擇人物","margin15");
					return false;
			}
			// 名前處理
			if($_POST["recruit_name"]) {
				if(is_numeric(strpos($_POST["recruit_name"],"\t")))
					return "error.";
				$name	= trim($_POST["recruit_name"]);
				$name	= stripslashes($name);
				$len	= strlen($name);
				if ( 0 == $len || 16 < $len ) {
					ShowError("名稱太短或太長","margin15");
					return false;
				}
				$name	= htmlspecialchars($name,ENT_QUOTES);
			} else {
				ShowError("名稱不能是空","margin15");
				return false;
			}
			//性別
			if( !isset($_POST["recruit_gend"]) ) {
				ShowError("未選定性別","margin15");
				return false;
			} else {
				$Gender	= $_POST["recruit_gend"]?"♀":"♂";
			}
			// キャラデ一タをクラスに入れる
			
			$plus	= array("name"=>"$name","gender"=>$_POST["recruit_gend"]);
			$char	= new char();
			$char->SetCharData(array_merge(BaseCharStatus($charNo),$plus));
			//僱用金
			if($hire <= $this->money) {
				$this->TakeMoney($hire);
			} else {
				ShowError("您沒有足夠的錢","margin15");
				return false;
			}
			// キャラを保存する
			$char->SaveCharData($this->id);
			ShowResult($char->Name()."($char->job_name:{$Gender}) 加為同伴！","margin15");
			return true;
		}
	}

//////////////////////////////////////////////////
//	
	function RecruitShow() {
		if( MAX_CHAR <= $this->CharCount() ) {
			?>

	<div style="margin:15px">
	<p>Maximum characters.<br>
	Need to make a space to recruit new character.</p>
	<p>人物上限數達到。<br>
	要添加新的空間來僱用新人（？）。</p>
	</div>
<?php 
			return false;
		}
		include_once(CLASS_MONSTER);
		$char[0]	= new char();
		$char[0]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
		$char[1]	= new char();
		$char[1]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
		$char[2]	= new char();
		$char[2]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
		$char[3]	= new char();
		$char[3]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));
		$char[4]	= new char();
		$char[4]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"0")));
		$char[5]	= new char();
		$char[5]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"1")));
		$char[6]	= new char();
		$char[6]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"0")));
		$char[7]	= new char();
		$char[7]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"1")));
		?>

	<form action="?recruit" method="post" style="margin:15px">
	<h4>新人物的職業</h4>
	<table cellspacing="0"><tbody><tr>
	<td class="td1" style="text-align:center">
<?php $char[0]->ShowImage()?>
<?php $char[1]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="1" style="margin:3px"><br>
	<?php print MoneyFormat(2000)?></td>
	<td class="td1" style="text-align:center">
<?php $char[2]->ShowImage()?>
<?php $char[3]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="2" style="margin:3px"><br>
	<?php print MoneyFormat(2000)?></td>
	<td class="td1" style="text-align:center">
<?php $char[4]->ShowImage()?>
<?php $char[5]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="3" style="margin:3px"><br>
	<?php print MoneyFormat(2500)?></td>
	<td class="td1" style="text-align:center">
<?php $char[6]->ShowImage()?>
<?php $char[7]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="4" style="margin:3px"><br>
	<?php print MoneyFormat(4000)?></td>
	</tr><tr>
	<td class="td4" style="text-align:center">
	戰士</td>
	<td class="td5" style="text-align:center">
	法師</td>
	<td class="td4" style="text-align:center">
	牧師</td>
	<td class="td5" style="text-align:center">
	獵人</td>
	</tr>
	</tbody></table>

	<h4>新人物的性別</h4>
	<table><tbody><tr><td valign="top">
	<input type="text" class="text" name="recruit_name" style="width:160px" maxlength="16"><br>
	<div style="margin:5px 0px">
	<input type="radio" class="vcent" name="recruit_gend" value="0">男
	<input type="radio" class="vcent" name="recruit_gend" value="1" style="margin-left:15px;">女</div>
	<input type="submit" class="btn" name="recruit" value="僱傭">
	<input type="hidden" class="btn" name="recruit" value="Recruit">
	</td><td valign="top">
	<p>1 to 16 letters.<br>
	Chinese characters count as 2.<br>
	1個漢字 = 2 letter.
	</p>
	</td></tr></tbody></table>
	</form>
<?php 
	}
//////////////////////////////////////////////////
//	鍛冶屋精鍊ヘッダ
	function SmithyRefineHeader() {
	?>
<div style="margin:15px">
<h4>精煉工房(Refine)</h4>

<div style="width:600px">
<div style="float:left;width:80px;">
<img src="<?php print IMG_CHAR?>mon_053r.gif" />
</div>
<div style="float:right;width:520px;">
在這裡 可以進行物品的精煉！<br />
選擇需要精練的物品以及精練的次數。<br />
不過加工壞了我們不負責。<br />
弟弟在管理的 <span class="bold">製作工房</span> 在<a href="?menu=create">這邊</a>。
</div>
<div style="clear:both"></div>
</div>
<h4>精煉道具<a name="refine"></a></h4>
<div style="margin:0 20px">
<?php 
	}
//////////////////////////////////////////////////
//	鍛冶屋處理(精鍊)
	function SmithyRefineProcess() {
		if(!$_POST["refine"])
			return false;
		if(!$_POST["item_no"]) {
			ShowError("Select Item.");
			return false;
		}
		// 道具が讀み迂めない場合
		if(!$item	= LoadItemData($_POST["item_no"])) {
			ShowError("Failed to load item data.");
			return false;
		}
		// 道具を所持していない場合
		if(!$this->item[$_POST["item_no"]]) {
			ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// 回數が指定されていない場合
		if($_POST["timesA"] < $_POST["timesB"])
			$times	= $_POST["timesB"];
		else
			$times	= $_POST["timesA"];
		if(!$times || $times < 1 || (REFINE_LIMIT) < $times ) {
			ShowError("times?");
			return false;
		}
		include(CLASS_SMITHY);
		$obj_item	= new Item($_POST["item_no"]);
		// その道具が精鍊できない場合
		if(!$obj_item->CanRefine()) {
			ShowError("Cant refine \"{$item[name]}\"");
			return false;
		}
		// ここから精鍊を始める處理
		$this->DeleteItem($_POST["item_no"]);// 道具は消えるか變化するので消す
		$Price	= round($item["buy"]/2);
		// 最大精鍊數の調整。
		if( REFINE_LIMIT < ($item["refine"] + $times) ) {
			$times	= REFINE_LIMIT - $item["refine"];
		}
		$Trys	= 0;
		for($i=0; $i<$times; $i++) {
			// お金を引く
			if($this->TakeMoney($Price)) {
				$MoneySum	+= $Price;
				$Trys++;
				if(!$obj_item->ItemRefine()) {//精鍊する(false=失敗なので終了する)
					break;
				}
			// お金が途中でなくなった場合。
			} else {
				ShowError("Not enough money.<br />\n");
				$this->AddItem($obj_item->ReturnItem());
				break;
			}
			// 指定回數精鍊を成功しきった場合。
			if($i == ($times - 1)) {
				$this->AddItem($obj_item->ReturnItem());
			}
		}
		print("Money Used : ".MoneyFormat($Price)." x ".$Trys." = ".MoneyFormat($MoneySum)."<br />\n");
		$this->SaveUserItem();
		return true;
		/*// お金が足りてるか計算
		$Price	= round($item["buy"]/2);
		$MoneyNeed	= $times * $Price;
		if($this->money < $MoneyNeed) {
			ShowError("Your request needs ".MoneyFormat($MoneyNeed));
			return false;
		}*/
		
	}
//////////////////////////////////////////////////
//	鍛冶屋表示
	function SmithyRefineShow() {
		// ■精鍊處理
		//$Result	= $this->SmithyRefineProcess();

		// 精鍊可能な物の表示
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$possible	= CanRefineType();
			$possible	= array_flip($possible);
			//配列の先頭の值が"0"なので1にする(isset使わずにtrueにするため)
			$possible[key($possible)]++;

			$goods	= new JS_ItemList();
			$goods->SetID("my");
			$goods->SetName("type");

			$goods->ListTable("<table cellspacing=\"0\">");// テ一ブルタグのはじまり
			$goods->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">精煉費</td><td class=\"align-center td9\">Item</td></tr>"); // テ一ブルの最初と最後の行に表示させるやつ。

			// JSを使用しない。
			if($this->no_JS_itemlist)
				$goods->NoJS();
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				// 精鍊可能な物だけ表示させる。
				if(!$possible[$item["type"]])
					continue;
				$price	= $item["buy"]/2;
				// NoTable
	//			$string	= '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
	//			$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";

				$string	= '<tr>';
				$string	.= '<td class="td7"><input type="radio" class="vcent" name="item_no" value="'.$no.'">';
				$string	.= '</td><td class="td7">'.MoneyFormat($price).'</td><td class="td8">'.ShowItemDetail($item,$val,1)."<td>";
				$string	.= "</tr>";

				$goods->AddItem($item,$string);
			}
			// JavaScript部分の書き出し
			print($goods->GetJavaScript("list"));
			print('可以精煉的名單');
			// 種類のセレクトボックス
			print($goods->ShowSelect());
			print('<form action="?menu=refine" method="post">'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			// 精鍊回數の指定
			print('回數 : <select name="timesA">'."\n");
			for($i=1; $i<11; $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			// リストの表示
			print('<div id="list">'.$goods->ShowDefault().'</div>'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			print('<input type="hidden" value="1" name="refine">'."\n");
			// 精鍊回數の指定
			print('回數 : <select name="timesB">'."\n");
			for($i=1; $i<(REFINE_LIMIT+1); $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			print('</form>'."\n");
		} else {
			print("No items<br />\n");
		}
		print("</div>\n");
	?>
	</div>
<?php 
	}
//////////////////////////////////////////////////
//	鍛冶屋 製作 ヘッダ
	function SmithyCreateHeader() {
		?>
<div style="margin:15px">
<h4>製作工房(Create)<a name="sm"></a></h4>
<div style="width:600px">
<div style="float:left;width:80px;">
<img src="<?php print IMG_CHAR?>mon_053rz.gif" />
</div>
<div style="float:right;width:520px;">
在這裡 可以進行物品的製作！<br />
只要你有素材就可以製作裝備。<br />
加入特殊素材的話可以製作特殊的武器。<br />
哥哥在管理的 <span class="bold">精煉工房</span> 在<a href="?menu=refine">這邊</a>。<br />
<a href="#mat">所持素材一覽</a>
</div>
<div style="clear:both"></div>
</div>
<h4>道具製作<a name="refine"></a></h4>
<div style="margin:0 15px">
<?php 
	}
//////////////////////////////////////////////////
//	製作處理
	function SmithyCreateProcess() {
		if(!$_POST["Create"]) return false;

		// 道具が選擇されていない
		if(!$_POST["ItemNo"]) {
			ShowError("請選擇一個道具製造");
			return false;
		}

		// 道具を讀む
		if(!$item	= LoadItemData($_POST["ItemNo"])) {
			ShowError("error12291703");
			return false;
		}

		// 作れる道具かどうかたしかめる
		if(!HaveNeeds($item,$this->item)) {
			ShowError($item["name"]." 您沒有足夠的原料生產。");
			return false;
		}

		// 追加素材
		if($_POST["AddMaterial"]) {
			// 所持していない場合
			if(!$this->item[$_POST["AddMaterial"]]) {
				ShowError("該素材不能追加。");
				return false;
			}
			// 追加素材の道具デ一タ
			$ADD	= LoadItemData($_POST["AddMaterial"]);
			$this->DeleteItem($_POST["AddMaterial"]);
		}

		// 道具の製作
		// お金を減らす
		//$Price	= $item["buy"];
		$Price	= 0;
		if(!$this->TakeMoney($Price)) {
			ShowError("您沒有足夠的錢。需要".MoneyFormat($Price)."。");
			return false;
		}
		// 素材を減らす
		foreach($item["need"] as $M_item => $M_amount) {
			$this->DeleteItem($M_item,$M_amount);
		}
		include(CLASS_SMITHY);
		$item	= new item($_POST["ItemNo"]);
		$item->CreateItem();
		// 付加效果
		if($ADD["Add"])
			$item->AddSpecial($ADD["Add"]);
		// できた道具を保存する
		$done	= $item->ReturnItem();
		$this->AddItem($done);
		$this->SaveUserItem();

		print("<p>");
		print(ShowItemDetail(LoadItemData($done)));
		
		print("\n<br />好了！</p>\n");
		return true;
	}
//////////////////////////////////////////////////
//	製作表示
	function SmithyCreateShow() {
		//$result	= $this->SmithyCreateProcess();

		$CanCreate	= CanCreate($this);
		include(CLASS_JS_ITEMLIST);
		$CreateList	= new JS_ItemList();
		$CreateList->SetID("create");
		$CreateList->SetName("type_create");

		$CreateList->ListTable("<table cellspacing=\"0\">");// テ一ブルタグのはじまり
		$CreateList->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">製作費用</td><td class=\"align-center td9\">Item</td></tr>"); // テ一ブルの最初と最後の行に表示させるやつ。

		// JSを使用しない。
		if($this->no_JS_itemlist)
			$CreateList->NoJS();
		foreach($CanCreate as $item_no) {
			$item	= LoadItemData($item_no);
			if(!HaveNeeds($item,$this->item))// 素材不足なら次
				continue;
			// NoTable
			//$head	= '<input type="radio" name="ItemNo" value="'.$item_no.'">'.ShowItemDetail($item,false,1,$this->item)."<br />";
			//$CreatePrice	= $item["buy"];
			$CreatePrice	= 0;//
			$head	= '<tr><td class="td7"><input type="radio" name="ItemNo" value="'.$item_no.'"></td>';
			$head	.= '<td class="td7">'.MoneyFormat($CreatePrice).'</td><td class="td8">'.ShowItemDetail($item,false,1,$this->item)."</td>";
			$CreateList->AddItem($item,$head);
		}
		if($head) {
			print($CreateList->GetJavaScript("list"));
			print($CreateList->ShowSelect());
		?>
<form action="?menu=create" method="post">
<div id="list"><?php print $CreateList->ShowDefault()?></div>
<input type="submit" class="btn" name="Create" value="創建">
<input type="reset" class="btn" value="重置">
<input type="hidden" name="Create" value="1"><br />
<?php 
		// 追加素材の表示
		print('<div class="bold u" style="margin-top:15px">追加素材</div>'."\n");
		for($item_no=7000; $item_no<7200; $item_no++) {
			if(!$this->item["$item_no"])
				continue;
			if($item	= LoadItemData($item_no)) {
				print('<input type="radio" name="AddMaterial" value="'.$item_no.'" class="vcent">');
				print(ShowItemDetail($item,$this->item["$item_no"],1)."<br />\n");
			}
		}
		?>
<input type="submit" class="btn" name="Create" value="創建">
<input type="reset" class="btn" value="重置">
</form>
<?php 
		} else {
			print("就目前手上所持有的素材的話什麼也不能作啊。");
		}


		// 所持素材一覽
		print("</div>\n");
		print("<h4>所持素材一覽<a name=\"mat\"></a> <a href=\"#sm\">↑</a></h4>");
		print("<div style=\"margin:0 15px\">");
		for($i=6000; $i<7000; $i++) {
			if(!$this->item["$i"])
				continue;
			$item	= LoadItemData($i);
			ShowItemDetail($item,$this->item["$i"]);
			print("<br />\n");
		}
		?>
</div>
</div>
<?php 
		return $result;
	}
//////////////////////////////////////////////////
//	メンバ一になる處理
	function AuctionJoinMember() {
		if(!$_POST["JoinMember"])
			return false;
		if($this->item["9000"]) {//既に會員
			//ShowError("You are already a member.\n");
			return false;
		}
		// お金が足りない
		if(!$this->TakeMoney(round(START_MONEY * 1.10))) {
			ShowError("您沒有足夠的錢<br />\n");
			return false;
		}
		// 道具を足す
		$this->AddItem(9000);
		$this->SaveUserItem();
		$this->SaveData();
		ShowResult("拍賣會的成員。<br />\n");
		return true;
	}
//////////////////////////////////////////////////
//	
	function AuctionEnter() {
		if($this->item["9000"])//オ一クションメンバ一カ一ド
			return true;
		else
			return false;
	}
//////////////////////////////////////////////////
//	オ一クションの表示(header)
	function AuctionHeader() {
		?>
<div style="margin:15px 0 0 15px">
<h4>拍賣(Auction)</h4>
<div style="margin-left:20px">

<div style="width:500px">
<div style="float:left;width:50px;">
<img src="<?php print IMG_CHAR?>ori_003.gif" />
</div>
<div style="float:right;width:450px;">
<?php 

		$this->AuctionJoinMember();
		if($this->AuctionEnter()) {
			print("您有會員卡麼。<br />\n");
			print("歡迎您到拍賣場。<br />\n");
			print("<a href=\"#log\">回顧記錄</a>\n");
		} else {
			print("想在拍賣會拍賣那您要加入會員啊。<br />\n");
			print("入會費用可要 ".MoneyFormat(round(START_MONEY * 1.10))." 呢。<br />\n");
			print("入會麼?<br />\n");
			print('<form action="" method="post">'."\n");
			print('<input type="submit" value="入會" name="JoinMember" class="btn"/>'."\n");
			print("</form>\n");
		}
		if(!AUCTION_TOGGLE)
			ShowError("功能暫停");
		if(!AUCTION_EXHIBIT_TOGGLE)
			ShowError("暫停拍賣");
		?>
</div>
<div style="clear:both"></div>
</div>
</div>
<h4>道具拍賣(Item Auction)</h4>
<div style="margin-left:20px">
<?php 
	}
//////////////////////////////////////////////////
//	オ一クションの表示
	function AuctionFoot(&$ItemAuction) {
		?>
</div>
<a name="log"></a>
<h4>拍賣紀錄(AuctionLog)</h4>
<div style="margin-left:20px">
<?php $ItemAuction->ShowLog();?>
</div>
<?php 
	}
//////////////////////////////////////////////////
//	競標處理
	function AuctionItemBiddingProcess(&$ItemAuction) {
		if(!$this->AuctionEnter())
			return false;
		if(!isset($_POST["ArticleNo"]))
			return false;

		$ArticleNo	= $_POST["ArticleNo"];
		$BidPrice	= (int)$_POST["BidPrice"];
		if($BidPrice < 1) {
			ShowError("輸入的是個錯誤的價格。");
			return false;
		}
		// まだ出品中かどうか確認する。
		if(!$ItemAuction->ItemArticleExists($ArticleNo)) {
			ShowError("這個拍賣品的賣方無法確認。");
			return false;
		}
		// 自分が競標できる人かどうかの確認
		if(!$ItemAuction->ItemBidRight($ArticleNo,$this->id)) {
			ShowError("No.".$ArticleNo." 賣方是否已經招標");
			return false;
		}
		// 最低競標價格を割っていないか確認する。
		$Bottom	= $ItemAuction->ItemBottomPrice($ArticleNo);
		if($BidPrice < $Bottom) {
			ShowError("低於最低投標價");
			ShowError("目前出價:".MoneyFormat($BidPrice)." 最低出價:".MoneyFormat($Bottom));
			return false;
		}
		// 金持ってるか確認する
		if(!$this->TakeMoney($BidPrice)) {
			ShowError("您的資金不足。");
			return false;
		}

		// 實際に競標する。
		if($ItemAuction->ItemBid($ArticleNo,$BidPrice,$this->id,$this->name)) {
			ShowResult("No:{$ArticleNo}  ".MoneyFormat($BidPrice)." 被收購。<br />\n");
			return true;
		}
	}
//////////////////////////////////////////////////
//	道具オ一クション用のオブジェクトを讀んで返す
/*
	function AuctionItemLoadData() {
		include(CLASS_AUCTION);
		$ItemAuction	= new Auction(item);
		$ItemAuction->ItemCheckSuccess();// 競賣が終了した品物を調べる
		$ItemAuction->UserSaveData();// 競賣品と金額を各IDに配って保存する

		return $ItemAuction;
	}
*/
//////////////////////////////////////////////////
//	競標用フォ一ム(畫面)
	function AuctionItemBiddingForm(&$ItemAuction) {

		if(!AUCTION_TOGGLE)
			return false;

		// 出品用フォ一ムにいくボタン
		if($this->AuctionEnter()) {
		if(AUCTION_EXHIBIT_TOGGLE) {
				print("<form action=\"?menu=auction\" method=\"post\">\n");
				print('<input type="submit" value="拍賣物品" name="ExhibitItemForm" class="btn" style="width:160px">'."\n");
				print("</form>\n");
			}
			// 入會してた場合　競標できるように
			$ItemAuction->ItemSortBy($_GET["sort"]);
			$ItemAuction->ItemShowArticle2(true);

			if(AUCTION_EXHIBIT_TOGGLE) {
				print("<form action=\"?menu=auction\" method=\"post\">\n");
				print('<input type="submit" value="拍賣物品" name="ExhibitItemForm" class="btn" style="width:160px">'."\n");
				print("</form>\n");
			}

		} else {
			// 競標できない
			$ItemAuction->ItemShowArticle2(false);
		}
	}
//////////////////////////////////////////////////
//	道具出品處理
	function AuctionItemExhibitProcess(&$ItemAuction) {

		if(!AUCTION_EXHIBIT_TOGGLE)
			return "BIDFORM";// 出品凍結

		// 保存しないで出品リストを表示する
		if(!$this->AuctionEnter())
			return "BIDFORM";
		if(!$_POST["PutAuction"])
			return "BIDFORM";

		if(!$_POST["item_no"]) {
			ShowError("Select Item.");
			return false;
		}
		// セッションによる30秒間の出品拒否
		$SessionLeft	= 30 - (time() - $_SESSION["AuctionExhibit"]);
		if($_SESSION["AuctionExhibit"] && 0 < $SessionLeft) {
			ShowError("Wait {$SessionLeft}seconds to ReExhibit.");
			return false;
		}
		// 同時出品數の制限
		if(AUCTION_MAX <= $ItemAuction->ItemAmount()) {
			ShowError("拍賣數量已達到極限。(".$ItemAuction->ItemAmount()."/".AUCTION_MAX.")");
			return false;
		}
		// 出品費用
		if(!$this->TakeMoney(500)) {
			ShowError("Need ".MoneyFormat(500)." to exhibit auction.");
			return false;
		}
		// 道具が讀み迂めない場合
		if(!$item	= LoadItemData($_POST["item_no"])) {
			ShowError("Failed to load item data.");
			return false;
		}
		// 道具を所持していない場合
		if(!$this->item[$_POST["item_no"]]) {
			ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// その道具が出品できない場合
		$possible	= CanExhibitType();
		if(!$possible[$item["type"]]) {
			ShowError("Cant put \"{$item[name]}\" to the Auction");
			return false;
		}
		// 出品時間の確認
		if(	!(	$_POST["ExhibitTime"] === '1' ||
				$_POST["ExhibitTime"] === '3' ||
				$_POST["ExhibitTime"] === '6' ||
				$_POST["ExhibitTime"] === '12' ||
				$_POST["ExhibitTime"] === '18' ||
				$_POST["ExhibitTime"] === '24') ) {
			var_dump($_POST);
			ShowError("time?");
			return false;
		}
		// 數量の確認
		if(ereg("^[0-9]",$_POST["Amount"])) {
			$amount	= (int)$_POST["Amount"];
			if($amount == 0)
				$amount	= 1;
		} else {
			$amount	= 1;
		}
		// 減らす(所持數より多く指定された場合その數を調節する)
		$_SESSION["AuctionExhibit"]	= time();//セッションで2重出品を防ぐ
		$amount	= $this->DeleteItem($_POST["item_no"],$amount);
		$this->SaveUserItem();

		// 出品する
		// $ItemAuction	= new Auction(item);// (2008/2/28:コメント化)
		$ItemAuction->ItemAddArticle($_POST["item_no"],$amount,$this->id,$_POST["ExhibitTime"],$_POST["StartPrice"],$_POST["Comment"]);
		print($item["name"]."{$amount}個 展覽品。");
		return true;
	}
//////////////////////////////////////////////////
//	出品用フォ一ム
	function AuctionItemExhibitForm() {

		if(!AUCTION_EXHIBIT_TOGGLE)
			return false;

		include(CLASS_JS_ITEMLIST);
		$possible	= CanExhibitType();
		?>
<div class="u bold">如何參展</div>
<ol>
<li>選擇一種道具，拍賣。</li>
<li>如果要拍賣超過兩個以上是要輸入數量。</li>
<li>指定拍賣的時間。</li>
<li>指定起拍價(不輸入的話為0)</li>
<li>輸入您的描述。</li>
<li>發送。</li>
</ol>
<div class="u bold">注意事項</div>
<ul>
<li>拍賣要交$500的手續費。</li>
<li>負責拍賣工作的人似乎不會認真幫你辦事的樣子</li>
</ul>
<a href="?menu=auction">查看所有拍賣物</a>
</div>
<h4>出售</h4>
<div style="margin-left:20px">
<div class="u bold">可以拍賣的道具</div>
<?php 
		if(!$this->item) {
			print("No items<br />\n");
			return false;
		}
		$ExhibitList	= new JS_ItemList();
		$ExhibitList->SetID("auc");
		$ExhibitList->SetName("type_auc");
		// JSを使用しない。
		if($this->no_JS_itemlist)
			$ExhibitList->NoJS();
		foreach($this->item as $no => $amount) {
			$item	= LoadItemData($no);
			if(!$possible[$item["type"]])
				continue;
			$head	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
			$head	.= ShowItemDetail($item,$amount,1)."<br />";
			$ExhibitList->AddItem($item,$head);
		}
		print($ExhibitList->GetJavaScript("list"));
		print($ExhibitList->ShowSelect());
		?>
<form action="?menu=auction" method="post">
<div id="list"><?php print $ExhibitList->ShowDefault()?></div>
<table><tr><td style="text-align:right">
數量(Amount) :</td><td><input type="text" name="Amount" class="text" style="width:60px" value="1" /><br />
</td></tr><tr><td style="text-align:right">
時間(Time) :</td><td>
<select name="ExhibitTime">
<option value="24" selected>24 hour</option>
<option value="18">18 hour</option>
<option value="12">12 hour</option>
<option value="6">6 hour</option>
<option value="3">3 hour</option>
<option value="1">1 hour</option>
</select>
</td></tr><tr><td>
起拍價(Start Price) :</td><td><input type="text" name="StartPrice" class="text" style="width:240px" maxlength="10"><br />
</td></tr><tr><td style="text-align:right">
描述(Comment) :</td><td>
<input type="text" name="Comment" class="text" style="width:240px" maxlength="40">
</td></tr><tr><td></td><td>
<input type="submit" class="btn" value="Put Auction" name="PutAuction" style="width:240px"/>
<input type="hidden" name="PutAuction" value="1">
</td></tr></table>
</form>

<?php 
		
	}
//////////////////////////////////////////////////
//	Unionモンスタ一の處理
	function UnionProcess() {

		if($this->CanUnionBattle() !== true) {
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
			$extra = INDEX;
			header("Location: http://$host$uri/$extra?hunt");
			exit;
		}

		if(!$_POST["union_battle"])
			return false;
		$Union	= new union();
		// 倒されているか、存在しない場合。
		if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
			return false;
		}
		// ユニオンモンスタ一のデ一タ
		$UnionMob	= CreateMonster($Union->MonsterNumber);
		$this->MemorizeParty();//パ一ティ一記憶
		// 自分パ一ティ一
		foreach($this->char as $key => $val) {//チェックされたやつリスト
			if($_POST["char_".$key]) {
				$MyParty[]	= $this->char[$key];
				$TotalLevel	+= $this->char[$key]->level;//自分PTの合計レベル
			}
		}
		// 合計レベル制限
		if($UnionMob["LevelLimit"] < $TotalLevel) {
			ShowError('合計級別水平('.$TotalLevel.'/'.$UnionMob["LevelLimit"].')',"margin15");
			return false;
		}
		if( count($MyParty) === 0) {
			ShowError('戰鬥至少要一個人參加',"margin15");
			return false;
		} else if(5 < count($MyParty)) {
			ShowError('戰鬥最多只能上五個人',"margin15");
			return false;
		}
		if(!$this->WasteTime(UNION_BATTLE_TIME)) {
			ShowError('Time Shortage.',"margin15");
			return false;
		}

		// 敵PT數

		// ランダム敵パ一ティ一
		if($UnionMob["SlaveAmount"])
			$EneNum	= $UnionMob["SlaveAmount"] + 1;//PTメンバと同じ數だけ。
		else
			$EneNum	= 5;// Union含めて5に固定する。

		if($UnionMob["SlaveSpecify"])
			$EnemyParty	= $this->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);
		else
			$EnemyParty	= $this->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);

		// unionMobを配列のおよそ中央に入れる
		array_splice($EnemyParty,floor(count($EnemyParty)/2),0,array($Union));

		$this->UnionSetTime();

		include(CLASS_BATTLE);
		$battle	= new battle($MyParty,$EnemyParty);
		$battle->SetUnionBattle();
		$battle->SetBackGround($Union->UnionLand);//背景
		//$battle->SetTeamName($this->name,"Union:".$Union->Name());
		$battle->SetTeamName($this->name,$UnionMob["UnionName"]);
		$battle->Process();//戰鬥開始

		$battle->SaveCharacters();//キャラデ一タ保存
			list($UserMoney)	= $battle->ReturnMoney();//戰鬥で得た合計金額
			$this->GetMoney($UserMoney);//お金を增やす
			$battle->RecordLog("UNION");
			// 道具を受け取る
			if($itemdrop	= $battle->ReturnItemGet(0)) {
				$this->LoadUserItem();
				foreach($itemdrop as $itemno => $amount)
					$this->AddItem($itemno,$amount);
				$this->SaveUserItem();
			}

		return true;
	}
//////////////////////////////////////////////////
//	Unionモンスタ一の表示
	function UnionShow() {
		if($this->CanUnionBattle() !== true) {
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
			$extra = INDEX;
			header("Location: http://$host$uri/$extra?hunt");
			exit;
		}
		//if($Result	= $this->UnionProcess())
		//	return true;
		print('<div style="margin:15px">'."\n");
		print("<h4>Union Monster</h4>\n");
		$Union	= new union();
		// 倒されているか、存在しない場合。
		if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
			ShowError("Defeated or not Exists.");
			return false;
		}
		print('</div>');
		$this->ShowCharacters(array($Union),false,"sea");
		print('<div style="margin:15px">'."\n");
		print("<h4>Teams</h4>\n");
		print("</div>");
		print('<form action="'.INDEX.'?union='.$_GET["union"].'" method="post">');
		$this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" value="戰鬥!">
	<input type="hidden" name="union_battle" value="1">
	<input type="reset" class="btn" value="重置"><br>
	保存此隊伍:<input type="checkbox" name="memory_party" value="1">
	</div></form>
<?php 
	}
//////////////////////////////////////////////////
//	町の表示
	function TownShow() {
		include(DATA_TOWN);
		print('<div style="margin:15px">'."\n");
		print("<h4>街</h4>");
		print('<div class="town">'."\n");
		print("<ul>\n");
		$PlaceList	= TownAppear($this);
		// 店
		if($PlaceList["Shop"]) {
			?>
<li>店(Shop)
<ul>
<li><a href="?menu=buy">買(Buy)</a></li>
<li><a href="?menu=sell">賣(Sell)</a></li>
<li><a href="?menu=work">打工</a></li>
</ul>
</li>
<?php 
		}
		// 斡旋所
		if($PlaceList["Recruit"])
			print("<li><p><a href=\"?recruit\">人材斡旋所(Recruit)</a></p></li>");
		// 鍛冶屋
		if($PlaceList["Smithy"]) {
			?>
<li>鍛冶屋(Smithy)
<ul>
<li><a href="?menu=refine">精煉工房(Refine)</a></li>
<li><a href="?menu=create">製作工房(Create)</a></li>
</ul>
</li>
<?php 
		}
		// オ一クション會場
		if($PlaceList["Auction"] && AUCTION_TOGGLE)
			print("<li><a href=\"?menu=auction\">拍賣會場(Auction)</li>");
		// コロシアム
		if($PlaceList["Colosseum"])
			print("<li><a href=\"?menu=rank\">競技場(Colosseum)</a></li>");
		print("</ul>\n");
		print("</div>\n");
		print("<h4>廣場</h4>");
		$this->TownBBS();
		print("</div>\n");
	}

//////////////////////////////////////////////////
//	普通の1行揭示板
	function TownBBS() {
		$file	= BBS_TOWN;
	?>
<form action="?town" method="post">
<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?php 
		if(!file_exists($file))
			return false;
		$log	= file($file);
		if($_POST["message"] && strlen($_POST["message"]) < 121) {
			$_POST["message"]	= htmlspecialchars($_POST["message"],ENT_QUOTES);
			$_POST["message"]	= stripslashes($_POST["message"]);

			$name	= "<span class=\"bold\">{$this->name}</span>";
			$message	= $name." > ".$_POST["message"];
			if($this->UserColor)
				$message	= "<span style=\"color:{$this->UserColor}\">".$message."</span>";
			$message	.= " <span class=\"light\">(".date("Mj G:i").")</span>\n";
			array_unshift($log,$message);
			while(50 < count($log))
				array_pop($log);
			WriteFile($file,implode(null,$log));
		}
		foreach($log as $mes)
			print(nl2br($mes));
	}
//////////////////////////////////////////////////
	function SettingProcess() {
		if($_POST["NewName"]) {
			$NewName	= $_POST["NewName"];
			if(is_numeric(strpos($NewName,"\t"))) {
				ShowError('error1');
				return false;
			}
			$NewName	= trim($NewName);
			$NewName	= stripslashes($NewName);
			if (!$NewName) {
				ShowError('Name is blank.');
				return false;
			}
			$length	= strlen($NewName);
			if ( 0 == $length || 16 < $length) {
				ShowError('1 to 16 letters?');
				return false;
			}
			$userName	= userNameLoad();
			if(in_array($NewName,$userName)) {
				ShowError("該名稱已被使用。","margin15");
				return false;
			}
			if(!$this->TakeMoney(NEW_NAME_COST)) {
				ShowError('money not enough');
				return false;
			}
			$OldName	= $this->name;
			$NewName	= htmlspecialchars($NewName,ENT_QUOTES);
			if($this->ChangeName($NewName)) {
				ShowResult("Name Changed ({$OldName} -> {$NewName})","margin15");
				//return false;
				userNameAdd($NewName);
				return true;
			} else {
				ShowError("?");//名前が同じ？
				return false;
			}
		}

		if($_POST["setting01"]) {
			if($_POST["record_battle_log"])
				$this->record_btl_log	= 1;
			else
				$this->record_btl_log	= false;

			if($_POST["no_JS_itemlist"])
				$this->no_JS_itemlist	= 1;
			else
				$this->no_JS_itemlist	= false;
		}
		if($_POST["color"]) {
			if(	strlen($_POST["color"]) != 6 &&
				!ereg("^[0369cf]{6}",$_POST["color"]))
				return "error 12072349";
			$this->UserColor	= $_POST["color"];
			ShowResult("Setting changed.","margin15");
			return true;
		}
	}
//////////////////////////////////////////////////
//	設定表示畫面
	function SettingShow() {
		print('<div style="margin:15px">'."\n");
		if($this->record_btl_log) $record_btl_log	= " checked";
		if($this->no_JS_itemlist) $no_JS_itemlist	= " checked";
		?>
<h4>設置</h4>
<form action="?setting" method="post">
<table><tbody>
<tr><td><input type="checkbox" name="record_battle_log" value="1" <?php print $record_btl_log?>></td><td>戰鬥記錄</td></tr>
<tr><td><input type="checkbox" name="no_JS_itemlist" value="1" <?php print $no_JS_itemlist?>></td><td>道具列表不使用javascript</td></tr>
</tbody></table>
<!--<tr><td>None</td><td><input type="checkbox" name="none" value="1"></td></tr>-->
顏色: 
<SELECT class=bgcolor name=color> <OPTION style="COLOR: #ffffff" value=ffffff selected>SampleColor</OPTION> <OPTION style="COLOR: #ffffcc" value=ffffcc>SampleColor</OPTION> <OPTION style="COLOR: #ffff99" value=ffff99>SampleColor</OPTION> <OPTION style="COLOR: #ffff66" value=ffff66>SampleColor</OPTION> <OPTION style="COLOR: #ffff33" value=ffff33>SampleColor</OPTION> <OPTION style="COLOR: #ffff00" value=ffff00>SampleColor</OPTION> <OPTION style="COLOR: #ffccff" value=ffccff>SampleColor</OPTION> <OPTION style="COLOR: #ffcccc" value=ffcccc>SampleColor</OPTION> <OPTION style="COLOR: #ffcc99" value=ffcc99>SampleColor</OPTION> <OPTION style="COLOR: #ffcc66" value=ffcc66>SampleColor</OPTION> <OPTION style="COLOR: #ffcc33" value=ffcc33>SampleColor</OPTION> <OPTION style="COLOR: #ffcc00" value=ffcc00>SampleColor</OPTION> <OPTION style="COLOR: #ff99ff" value=ff99ff>SampleColor</OPTION> <OPTION style="COLOR: #ff99cc" value=ff99cc>SampleColor</OPTION> <OPTION style="COLOR: #ff9999" value=ff9999>SampleColor</OPTION> <OPTION style="COLOR: #ff9966" value=ff9966>SampleColor</OPTION> <OPTION style="COLOR: #ff9933" value=ff9933>SampleColor</OPTION> <OPTION style="COLOR: #ff9900" value=ff9900>SampleColor</OPTION> <OPTION style="COLOR: #ff66ff" value=ff66ff>SampleColor</OPTION> <OPTION style="COLOR: #ff66cc" value=ff66cc>SampleColor</OPTION> <OPTION style="COLOR: #ff6699" value=ff6699>SampleColor</OPTION> <OPTION style="COLOR: #ff6666" value=ff6666>SampleColor</OPTION> <OPTION style="COLOR: #ff6633" value=ff6633>SampleColor</OPTION> <OPTION style="COLOR: #ff6600" value=ff6600>SampleColor</OPTION> <OPTION style="COLOR: #ff33ff" value=ff33ff>SampleColor</OPTION> <OPTION style="COLOR: #ff33cc" value=ff33cc>SampleColor</OPTION> <OPTION style="COLOR: #ff3399" value=ff3399>SampleColor</OPTION> <OPTION style="COLOR: #ff3366" value=ff3366>SampleColor</OPTION> <OPTION style="COLOR: #ff3333" value=ff3333>SampleColor</OPTION> <OPTION style="COLOR: #ff3300" value=ff3300>SampleColor</OPTION> <OPTION style="COLOR: #ff00ff" value=ff00ff>SampleColor</OPTION> <OPTION style="COLOR: #ff00cc" value=ff00cc>SampleColor</OPTION> <OPTION style="COLOR: #ff0099" value=ff0099>SampleColor</OPTION> <OPTION style="COLOR: #ff0066" value=ff0066>SampleColor</OPTION> <OPTION style="COLOR: #ff0033" value=ff0033>SampleColor</OPTION> <OPTION style="COLOR: #ff0000" value=ff0000>SampleColor</OPTION> <OPTION style="COLOR: #ccffff" value=ccffff>SampleColor</OPTION> <OPTION style="COLOR: #ccffcc" value=ccffcc>SampleColor</OPTION> <OPTION style="COLOR: #ccff99" value=ccff99>SampleColor</OPTION> <OPTION style="COLOR: #ccff66" value=ccff66>SampleColor</OPTION> <OPTION style="COLOR: #ccff33" value=ccff33>SampleColor</OPTION> <OPTION style="COLOR: #ccff00" value=ccff00>SampleColor</OPTION> <OPTION style="COLOR: #ccccff" value=ccccff>SampleColor</OPTION> <OPTION style="COLOR: #cccccc" value=cccccc>SampleColor</OPTION> <OPTION style="COLOR: #cccc99" value=cccc99>SampleColor</OPTION> <OPTION style="COLOR: #cccc66" value=cccc66>SampleColor</OPTION> <OPTION style="COLOR: #cccc33" value=cccc33>SampleColor</OPTION> <OPTION style="COLOR: #cccc00" value=cccc00>SampleColor</OPTION> <OPTION style="COLOR: #cc99ff" value=cc99ff>SampleColor</OPTION> <OPTION style="COLOR: #cc99cc" value=cc99cc>SampleColor</OPTION> <OPTION style="COLOR: #cc9999" value=cc9999>SampleColor</OPTION> <OPTION style="COLOR: #cc9966" value=cc9966>SampleColor</OPTION> <OPTION style="COLOR: #cc9933" value=cc9933>SampleColor</OPTION> <OPTION style="COLOR: #cc9900" value=cc9900>SampleColor</OPTION> <OPTION style="COLOR: #cc66ff" value=cc66ff>SampleColor</OPTION> <OPTION style="COLOR: #cc66cc" value=cc66cc>SampleColor</OPTION> <OPTION style="COLOR: #cc6699" value=cc6699>SampleColor</OPTION> <OPTION style="COLOR: #cc6666" value=cc6666>SampleColor</OPTION> <OPTION style="COLOR: #cc6633" value=cc6633>SampleColor</OPTION> <OPTION style="COLOR: #cc6600" value=cc6600>SampleColor</OPTION> <OPTION style="COLOR: #cc33ff" value=cc33ff>SampleColor</OPTION> <OPTION style="COLOR: #cc33cc" value=cc33cc>SampleColor</OPTION> <OPTION style="COLOR: #cc3399" value=cc3399>SampleColor</OPTION> <OPTION style="COLOR: #cc3366" value=cc3366>SampleColor</OPTION> <OPTION style="COLOR: #cc3333" value=cc3333>SampleColor</OPTION> <OPTION style="COLOR: #cc3300" value=cc3300>SampleColor</OPTION> <OPTION style="COLOR: #cc00ff" value=cc00ff>SampleColor</OPTION> <OPTION style="COLOR: #cc00cc" value=cc00cc>SampleColor</OPTION> <OPTION style="COLOR: #cc0099" value=cc0099>SampleColor</OPTION> <OPTION style="COLOR: #cc0066" value=cc0066>SampleColor</OPTION> <OPTION style="COLOR: #cc0033" value=cc0033>SampleColor</OPTION> <OPTION style="COLOR: #cc0000" value=cc0000>SampleColor</OPTION> <OPTION style="COLOR: #99ffff" value=99ffff>SampleColor</OPTION> <OPTION style="COLOR: #99ffcc" value=99ffcc>SampleColor</OPTION> <OPTION style="COLOR: #99ff99" value=99ff99>SampleColor</OPTION> <OPTION style="COLOR: #99ff66" value=99ff66>SampleColor</OPTION> <OPTION style="COLOR: #99ff33" value=99ff33>SampleColor</OPTION> <OPTION style="COLOR: #99ff00" value=99ff00>SampleColor</OPTION> <OPTION style="COLOR: #99ccff" value=99ccff>SampleColor</OPTION> <OPTION style="COLOR: #99cccc" value=99cccc>SampleColor</OPTION> <OPTION style="COLOR: #99cc99" value=99cc99>SampleColor</OPTION> <OPTION style="COLOR: #99cc66" value=99cc66>SampleColor</OPTION> <OPTION style="COLOR: #99cc33" value=99cc33>SampleColor</OPTION> <OPTION style="COLOR: #99cc00" value=99cc00>SampleColor</OPTION> <OPTION style="COLOR: #9999ff" value=9999ff>SampleColor</OPTION> <OPTION style="COLOR: #9999cc" value=9999cc>SampleColor</OPTION> <OPTION style="COLOR: #999999" value=999999>SampleColor</OPTION> <OPTION style="COLOR: #999966" value=999966>SampleColor</OPTION> <OPTION style="COLOR: #999933" value=999933>SampleColor</OPTION> <OPTION style="COLOR: #999900" value=999900>SampleColor</OPTION> <OPTION style="COLOR: #9966ff" value=9966ff>SampleColor</OPTION> <OPTION style="COLOR: #9966cc" value=9966cc>SampleColor</OPTION> <OPTION style="COLOR: #996699" value=996699>SampleColor</OPTION> <OPTION style="COLOR: #996666" value=996666>SampleColor</OPTION> <OPTION style="COLOR: #996633" value=996633>SampleColor</OPTION> <OPTION style="COLOR: #996600" value=996600>SampleColor</OPTION> <OPTION style="COLOR: #9933ff" value=9933ff>SampleColor</OPTION> <OPTION style="COLOR: #9933cc" value=9933cc>SampleColor</OPTION> <OPTION style="COLOR: #993399" value=993399>SampleColor</OPTION> <OPTION style="COLOR: #993366" value=993366>SampleColor</OPTION> <OPTION style="COLOR: #993333" value=993333>SampleColor</OPTION> <OPTION style="COLOR: #993300" value=993300>SampleColor</OPTION> <OPTION style="COLOR: #9900ff" value=9900ff>SampleColor</OPTION> <OPTION style="COLOR: #9900cc" value=9900cc>SampleColor</OPTION> <OPTION style="COLOR: #990099" value=990099>SampleColor</OPTION> <OPTION style="COLOR: #990066" value=990066>SampleColor</OPTION> <OPTION style="COLOR: #990033" value=990033>SampleColor</OPTION> <OPTION style="COLOR: #990000" value=990000>SampleColor</OPTION> <OPTION style="COLOR: #66ffff" value=66ffff>SampleColor</OPTION> <OPTION style="COLOR: #66ffcc" value=66ffcc>SampleColor</OPTION> <OPTION style="COLOR: #66ff99" value=66ff99>SampleColor</OPTION> <OPTION style="COLOR: #66ff66" value=66ff66>SampleColor</OPTION> <OPTION style="COLOR: #66ff33" value=66ff33>SampleColor</OPTION> <OPTION style="COLOR: #66ff00" value=66ff00>SampleColor</OPTION> <OPTION style="COLOR: #66ccff" value=66ccff>SampleColor</OPTION> <OPTION style="COLOR: #66cccc" value=66cccc>SampleColor</OPTION> <OPTION style="COLOR: #66cc99" value=66cc99>SampleColor</OPTION> <OPTION style="COLOR: #66cc66" value=66cc66>SampleColor</OPTION> <OPTION style="COLOR: #66cc33" value=66cc33>SampleColor</OPTION> <OPTION style="COLOR: #66cc00" value=66cc00>SampleColor</OPTION> <OPTION style="COLOR: #6699ff" value=6699ff>SampleColor</OPTION> <OPTION style="COLOR: #6699cc" value=6699cc>SampleColor</OPTION> <OPTION style="COLOR: #669999" value=669999>SampleColor</OPTION> <OPTION style="COLOR: #669966" value=669966>SampleColor</OPTION> <OPTION style="COLOR: #669933" value=669933>SampleColor</OPTION> <OPTION style="COLOR: #669900" value=669900>SampleColor</OPTION> <OPTION style="COLOR: #6666ff" value=6666ff>SampleColor</OPTION> <OPTION style="COLOR: #6666cc" value=6666cc>SampleColor</OPTION> <OPTION style="COLOR: #666699" value=666699>SampleColor</OPTION> <OPTION style="COLOR: #666666" value=666666>SampleColor</OPTION> <OPTION style="COLOR: #666633" value=666633>SampleColor</OPTION> <OPTION style="COLOR: #666600" value=666600>SampleColor</OPTION> <OPTION style="COLOR: #6633ff" value=6633ff>SampleColor</OPTION> <OPTION style="COLOR: #6633cc" value=6633cc>SampleColor</OPTION> <OPTION style="COLOR: #663399" value=663399>SampleColor</OPTION> <OPTION style="COLOR: #663366" value=663366>SampleColor</OPTION> <OPTION style="COLOR: #663333" value=663333>SampleColor</OPTION> <OPTION style="COLOR: #663300" value=663300>SampleColor</OPTION> <OPTION style="COLOR: #6600ff" value=6600ff>SampleColor</OPTION> <OPTION style="COLOR: #6600cc" value=6600cc>SampleColor</OPTION> <OPTION style="COLOR: #660099" value=660099>SampleColor</OPTION> <OPTION style="COLOR: #660066" value=660066>SampleColor</OPTION> <OPTION style="COLOR: #660033" value=660033>SampleColor</OPTION> <OPTION style="COLOR: #660000" value=660000>SampleColor</OPTION> <OPTION style="COLOR: #33ffff" value=33ffff>SampleColor</OPTION> <OPTION style="COLOR: #33ffcc" value=33ffcc>SampleColor</OPTION> <OPTION style="COLOR: #33ff99" value=33ff99>SampleColor</OPTION> <OPTION style="COLOR: #33ff66" value=33ff66>SampleColor</OPTION> <OPTION style="COLOR: #33ff33" value=33ff33>SampleColor</OPTION> <OPTION style="COLOR: #33ff00" value=33ff00>SampleColor</OPTION> <OPTION style="COLOR: #33ccff" value=33ccff>SampleColor</OPTION> <OPTION style="COLOR: #33cccc" value=33cccc>SampleColor</OPTION> <OPTION style="COLOR: #33cc99" value=33cc99>SampleColor</OPTION> <OPTION style="COLOR: #33cc66" value=33cc66>SampleColor</OPTION> <OPTION style="COLOR: #33cc33" value=33cc33>SampleColor</OPTION> <OPTION style="COLOR: #33cc00" value=33cc00>SampleColor</OPTION> <OPTION style="COLOR: #3399ff" value=3399ff>SampleColor</OPTION> <OPTION style="COLOR: #3399cc" value=3399cc>SampleColor</OPTION> <OPTION style="COLOR: #339999" value=339999>SampleColor</OPTION> <OPTION style="COLOR: #339966" value=339966>SampleColor</OPTION> <OPTION style="COLOR: #339933" value=339933>SampleColor</OPTION> <OPTION style="COLOR: #339900" value=339900>SampleColor</OPTION> <OPTION style="COLOR: #3366ff" value=3366ff>SampleColor</OPTION> <OPTION style="COLOR: #3366cc" value=3366cc>SampleColor</OPTION> <OPTION style="COLOR: #336699" value=336699>SampleColor</OPTION> <OPTION style="COLOR: #336666" value=336666>SampleColor</OPTION> <OPTION style="COLOR: #336633" value=336633>SampleColor</OPTION> <OPTION style="COLOR: #336600" value=336600>SampleColor</OPTION> <OPTION style="COLOR: #3333ff" value=3333ff>SampleColor</OPTION> <OPTION style="COLOR: #3333cc" value=3333cc>SampleColor</OPTION> <OPTION style="COLOR: #333399" value=333399>SampleColor</OPTION> <OPTION style="COLOR: #333366" value=333366>SampleColor</OPTION> <OPTION style="COLOR: #333333" value=333333>SampleColor</OPTION> <OPTION style="COLOR: #333300" value=333300>SampleColor</OPTION> <OPTION style="COLOR: #3300ff" value=3300ff>SampleColor</OPTION> <OPTION style="COLOR: #3300cc" value=3300cc>SampleColor</OPTION> <OPTION style="COLOR: #330099" value=330099>SampleColor</OPTION> <OPTION style="COLOR: #330066" value=330066>SampleColor</OPTION> <OPTION style="COLOR: #330033" value=330033>SampleColor</OPTION> <OPTION style="COLOR: #330000" value=330000>SampleColor</OPTION> <OPTION style="COLOR: #00ffff" value=00ffff>SampleColor</OPTION> <OPTION style="COLOR: #00ffcc" value=00ffcc>SampleColor</OPTION> <OPTION style="COLOR: #00ff99" value=00ff99>SampleColor</OPTION> <OPTION style="COLOR: #00ff66" value=00ff66>SampleColor</OPTION> <OPTION style="COLOR: #00ff33" value=00ff33>SampleColor</OPTION> <OPTION style="COLOR: #00ff00" value=00ff00>SampleColor</OPTION> <OPTION style="COLOR: #00ccff" value=00ccff>SampleColor</OPTION> <OPTION style="COLOR: #00cccc" value=00cccc>SampleColor</OPTION> <OPTION style="COLOR: #00cc99" value=00cc99>SampleColor</OPTION> <OPTION style="COLOR: #00cc66" value=00cc66>SampleColor</OPTION> <OPTION style="COLOR: #00cc33" value=00cc33>SampleColor</OPTION> <OPTION style="COLOR: #00cc00" value=00cc00>SampleColor</OPTION> <OPTION style="COLOR: #0099ff" value=0099ff>SampleColor</OPTION> <OPTION style="COLOR: #0099cc" value=0099cc>SampleColor</OPTION> <OPTION style="COLOR: #009999" value=009999>SampleColor</OPTION> <OPTION style="COLOR: #009966" value=009966>SampleColor</OPTION> <OPTION style="COLOR: #009933" value=009933>SampleColor</OPTION> <OPTION style="COLOR: #009900" value=009900>SampleColor</OPTION> <OPTION style="COLOR: #0066ff" value=0066ff>SampleColor</OPTION> <OPTION style="COLOR: #0066cc" value=0066cc>SampleColor</OPTION> <OPTION style="COLOR: #006699" value=006699>SampleColor</OPTION> <OPTION style="COLOR: #006666" value=006666>SampleColor</OPTION> <OPTION style="COLOR: #006633" value=006633>SampleColor</OPTION> <OPTION style="COLOR: #006600" value=006600>SampleColor</OPTION> <OPTION style="COLOR: #0033ff" value=0033ff>SampleColor</OPTION> <OPTION style="COLOR: #0033cc" value=0033cc>SampleColor</OPTION> <OPTION style="COLOR: #003399" value=003399>SampleColor</OPTION> <OPTION style="COLOR: #003366" value=003366>SampleColor</OPTION> <OPTION style="COLOR: #003333" value=003333>SampleColor</OPTION> <OPTION style="COLOR: #003300" value=003300>SampleColor</OPTION> <OPTION style="COLOR: #0000ff" value=0000ff>SampleColor</OPTION> <OPTION style="COLOR: #0000cc" value=0000cc>SampleColor</OPTION> <OPTION style="COLOR: #000099" value=000099>SampleColor</OPTION> <OPTION style="COLOR: #000066" value=000066>SampleColor</OPTION> <OPTION style="COLOR: #000033" value=000033>SampleColor</OPTION> <OPTION style="COLOR: #000000" value=000000>SampleColor</OPTION></SELECT><br />
<input type="submit" class="btn" name="setting01" value="修改" style="width:100px">
<input type="hidden" name="setting01" value="1">
</form>
<h4>註銷</h4>
<form action="<?php print INDEX?>" method="post">
<input type="submit" class="btn" name="logout" value="註銷" style="width:100px">
</form>
<h4>變更隊伍名</h4>
<form action="?setting" method="post">
費用 : <?php print MoneyFormat(NEW_NAME_COST)?><br />
16個字符(全角=2字符)<br />
新的名稱 : <input type="text" class="text" name="NewName" size="20">
<input type="submit" class="btn" value="變更" style="width:100px">
</form>
<h4>世界盡頭</h4>
<div class="u">※自殺用</div>
<form action="?setting" method="post">
PassWord : <input type="text" class="text" name="deletepass" size="20">
<input type="submit" class="btn" name="delete" value="我要自殺了..." style="width:100px">
</form>
</div>
<?php 
		return $Result;
	}
////////// Show //////////////////////////////////////////////////////
/*
 * ShowCharStat
 * ShowHunt
 * ShowItem
 * ShowShop
 * ShowRank
 * ShowRecruit
 * ShowSetting
 */

//////////////////////////////////////////////////
//	戰鬥時に選擇したメンバ一を記憶する
	function MemorizeParty() {
		if($_POST["memory_party"]) {
			//$temp	= $this->party_memo;//一時的に記憶
			//$this->party_memo	= array();
			foreach($this->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					//$this->party_memo[]	 = $key;
					$PartyMemo[]	= $key;
			}
			//if(5 < count($this->party_memo) )//5人以上は馱目
			//	$this->party_memo	= $temp;
			if(0 < count($PartyMemo) && count($PartyMemo) < 6)
				$this->party_memo	= implode("<>",$PartyMemo);
		}
	}

//////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////
//	ログインした畫面
	function LoginMain() {
		$this->ShowTutorial();
		$this->ShowMyCharacters();
		RegularControl($this->id);
	}
//////////////////////////////////////////////////
//	チュウトリアル
	function ShowTutorial() {
		$last	= $this->last;
		$start	= substr($this->start,0,10);
		$term	= 60*60*1;
		if( ($last - $start) < $term) {
			?>
	<div style="margin:5px 15px">
	<a href="?tutorial">教程</a> - 戰鬥的基本(登錄後一個小時內顯示)
	</div>

<?php 
		}
	}

//////////////////////////////////////////////////
//	自分のキャラを表示する
	function ShowMyCharacters($array=NULL) {// $array ← 色々受け取る
		if(!$this->char) return false;
		$divide	= (count($this->char)<CHAR_ROW ? count($this->char) : CHAR_ROW);
		$width	= floor(100/$divide);//各セル橫幅

		print('<table cellspacing="0" style="width:100%"><tbody><tr>');//橫幅100%
		foreach($this->char as $val) {
			if( $i%CHAR_ROW==0 && $i != 0 )
				print("\t</tr><tr>\n");
			print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//キャラ數に應じて%で各セル分割
			$val->ShowCharLink($array);
			print("</td>\n");
			$i++;
		}
		print("</tr></tbody></table>");
	}
//////////////////////////////////////////////////
//	キャラを表組みで表示する
	function ShowCharacters($characters,$type=null,$checked=null) {
		if(!$characters) return false;
		$divide	= (count($characters)<CHAR_ROW ? count($characters) : CHAR_ROW);
		$width	= floor(100/$divide);//各セル橫幅

		if($type == "CHECKBOX") {
print <<< HTML
<script type="text/javascript">
<!--
function toggleCheckBox(id) {
id0 = "box" + id;
\$("box" + id).checked = \$("box" + id).checked?false:true;
Element.toggleClassName("text"+id,'unselect');
}
// -->
</script>
HTML;
		}

		print('<table cellspacing="0" style="width:100%"><tbody><tr>');//橫幅100%
		foreach($characters as $char) {
			if( $i%CHAR_ROW==0 && $i != 0 )
				print("\t</tr><tr>\n");
			print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//キャラ數に應じて%で各セル分割

			/*-------------------*/
			switch(1) {
				case ($type === MONSTER):
					$char->ShowCharWithLand($checked); break;
				case ($type === CHECKBOX):
					if(!is_array($checked)) $checked = array();
					if(in_array($char->birth,$checked))
						$char->ShowCharRadio($char->birth," checked");
					else
						$char->ShowCharRadio($char->birth);
					break;
				default:
					$char->ShowCharLink();
			}

			print("</td>\n");
			$i++;
		}
		print("</tr></tbody></table>");
	}

//////////////////////////////////////////////////
//	自分のデ一タとクッキ一を消す
	function DeleteMyData() {
		if($this->pass == $this->CryptPassword($_POST["deletepass"]) ) {
			$this->DeleteUser();
			$this->name	= NULL;
			$this->pass	= NULL;
			$this->id	= NULL;
			$this->islogin= false;
			unset($_SESSION["id"]);
			unset($_SESSION["pass"]);
			setcookie("NO","");
			$this->LoginForm();
			return true;
		}
	}

//////////////////////////////////////////////////
//	變數の表示
	function Debug() {
		if(DEBUG)
			print("<pre>".print_r(get_object_vars($this),1)."</pre>");
	}

//////////////////////////////////////////////////
//	セッション情報を表示する。
	function ShowSession() {
		echo "this->id:$this->id<br>";
		echo "this->pass:$this->pass<br>";
		echo "SES[id]:$_SESSION[id]<br>";
		echo "SES[pass]:$_SESSION[pass]<br>";
		echo "SES[pass]:".$this->CryptPassword($_SESSION[pass])."(crypted)<br>";
		echo "CK[NO]:$_COOKIE[NO]<br>";
		echo "SES[NO]:".session_id();
		dump($_COOKIE);
		dump($_SESSION);
	}

//////////////////////////////////////////////////
//	ログインした時間を設定する
	function RenewLoginTime() {
		$this->login	= time();
	}

//////////////////////////////////////////////////
//	ログインしたのか、しているのか、ログアウトしたのか。
	function CheckLogin() {
		//logout
		if(isset($_POST["logout"])) {
		//	$_SESSION["pass"]	= NULL;
		//	echo $_SESSION["pass"];
			unset($_SESSION["pass"]);
		//	session_destroy();
			return false;
		}

		//session
		$file=USER.$this->id."/".DATA;//data.dat
		if ($data = $this->LoadData()) {
			//echo "<div>$data[pass] == $this->pass</div>";
			if($this->pass == NULL)
				return false;
			if ($data["pass"] === $this->pass) {
				//ログイン狀態
				$this->DataUpDate($data);
				$this->SetData($data);
				if(RECORD_IP)
					$this->SetIp($_SERVER['REMOTE_ADDR']);
				$this->RenewLoginTime();

				$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
				if ($pass) {//ちょうど今ログインするなら
					$_SESSION["id"]	= $this->id;
					$_SESSION["pass"]	= $pass;
					setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
				}

				$this->islogin	= true;//ログイン狀態
				return true;
			} else
				return "Wrong password!";
		} else {
			if($_POST["id"])
				return "ID \"{$this->id}\" doesnt exists.";
		}
	}

//////////////////////////////////////////////////
//	$id を登錄濟みidとして記錄する
	function RecordRegister($id) {
		$fp=fopen(REGISTER,"a");
		flock($fp,2);
		fputs($fp,"$id\n");
		fclose($fp);
	}

//////////////////////////////////////////////////
//	pass と id を設定する
	function Set_ID_PASS() {
		$id	= ($_POST["id"])?$_POST["id"]:$_GET["id"];
		//if($_POST["id"]) {
		if($id) {
				$this->id	= $id;//$_POST["id"];
			// ↓ログイン處理した時だけ
			if (is_registered($_POST["id"])) {
				$_SESSION["id"]	= $this->id;
			}
		} else if($_SESSION["id"])
			$this->id	= $_SESSION["id"];

		$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
		//if($_POST["pass"])
		if($pass)
			$this->pass	= $pass;//$_POST["pass"];
		else if($_SESSION["pass"])
			$this->pass	= $_SESSION["pass"];

		if($this->pass)
			$this->pass	= $this->CryptPassword($this->pass);
	}

//////////////////////////////////////////////////
//	保存されているセッション番號を變更する。
	function SessionSwitch() {
		// session消滅の時間(?)
		// how about "session_set_cookie_params()"?
		session_cache_expire(COOKIE_EXPIRE/60);
		if($_COOKIE["NO"])//クッキ一に保存してあるセッションIDのセッションを呼び出す
			session_id($_COOKIE["NO"]);

		session_start();
		if(!SESSION_SWITCH)//switchしないならここで終了
			return false;
		//print_r($_SESSION);
		//dump($_SESSION);
		$OldID	= session_id();
		$temp	= serialize($_SESSION);

		session_regenerate_id();
		$NewID	= session_id();
		setcookie("NO",$NewID,time()+COOKIE_EXPIRE);
		$_COOKIE["NO"]=$NewID;

		session_id($OldID);
		session_start();

		if($_SESSION):
		//	session_destroy();//Sleipnirだとおかしい...?(最初期)
		//	unset($_SESSION);//こっちは大丈夫(やっぱりこれは馱目かも)(修正後)
			//結局,セッションをforeachでル一プして1個づつunset(2007/9/14 再修正)
			foreach($_SESSION as $key => $val)
				unset($_SESSION["$key"]);
		endif;

		session_id($NewID);
		session_start();
		$_SESSION	= unserialize($temp);
	}

//////////////////////////////////////////////////
//	入力された情報が型にはまるか判定
//	→ 新規デ一タを作成。

	function MakeNewData() {
		// 登錄者數が限界の場合
		if(MAX_USERS <= count(glob(USER."*")))
			return array(false,"Maximum users.<br />已達到最大用戶數量。");
		if(isset($_POST["Newid"]))
			trim($_POST["Newid"]);
		if(empty($_POST["Newid"]))
			return array(false,"Enter ID.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["Newid"])||
			ereg("[^0-9a-zA-Z]+",$_POST["Newid"]))//正規表現
			return array(false,"Bad ID");

		if(strlen($_POST["Newid"]) < 4 || 16 < strlen($_POST["Newid"]))//文字制限
			return array(false,"Bad ID");

		if(is_registered($_POST["Newid"]))
			return array(false,"This ID has been already used.");

		$file = USER.$_POST["Newid"]."/".DATA;
		// PASS
		//if(isset($_POST["pass1"]))
		//	trim($_POST["pass1"]);
		if(empty($_POST["pass1"]) || empty($_POST["pass2"]))
			return array(false,"Enter both Password.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass1"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass1"]))
			return array(false,"Bad Password 1");
		if(strlen($_POST["pass1"]) < 4 || 16 < strlen($_POST["pass1"]))//文字制限
			return array(false,"Bad Password 1");
		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass2"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass2"]))
			return array(false,"Bad Password 2");
		if(strlen($_POST["pass2"]) < 4 || 16 < strlen($_POST["pass2"]))//文字制限
			return array(false,"Bad Password 2");

		if($_POST["pass1"] !== $_POST["pass2"])
			return array(false,"Password dismatch.");

		$pass = $this->CryptPassword($_POST["pass1"]);
		// MAKE
		if(!file_exists($file)){
			mkdir(USER.$_POST["Newid"], 0705);
			$this->RecordRegister($_POST["Newid"]);//ID記錄
			$fp=fopen("$file","w");
			flock($fp,LOCK_EX);
				$now	= time();
				fputs($fp,"id=$_POST[Newid]\n");
				fputs($fp,"pass=$pass\n");
				fputs($fp,"last=".$now."\n");
				fputs($fp,"login=".$now."\n");
				fputs($fp,"start=".$now.substr(microtime(),2,6)."\n");
				fputs($fp,"money=".START_MONEY."\n");
				fputs($fp,"time=".START_TIME."\n");
				fputs($fp,"record_btl_log=1\n");
			fclose($fp);
			//print("ID:$_POST[Newid] success.<BR>");
			$_SESSION["id"]=$_POST["Newid"];
			setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
			$success	= "<div class=\"recover\">ID : $_POST[Newid] 註冊成功. 請登錄吧</div>";
			return array(true,$success);//強引...
		}
	}

//////////////////////////////////////////////////
//	新規ID作成用のフォ一ム
	function NewForm($error=NULL) {
		if(MAX_USERS <= count(glob(USER."*"))) {
			?>

	<div style="margin:15px">
	Maximum users.<br />
	用戶數已達到最大。
	</div>
<?php 
			return false;
		}
		$idset=($_POST["Newid"]?" value=$_POST[Newid]":NULL);
		?>
	<div style="margin:15px">
	<?php print ShowError($error);?>
	<h4>註冊!</h4>
	<form action="<?php print INDEX?>" method="post">

	<table><tbody>
	<tr><td colspan="2">ID & PASS must be 4 to 16 letters.<br />letters allowed a-z,A-Z,0-9<br />
	ID 和 PASS在 4-16 個字以內。半角英數字。</td></tr>
	<tr><td><div style="text-align:right">ID:</div></td>
	<td><input type="text" maxlength="16" class="text" name="Newid" style="width:240px"<?php print $idset?>></td></tr>
	<tr><td colspan="2"><br />Password,Re-enter.<br />PASS 以及再輸入 確認用。</td></tr>
	<tr><td><div style="text-align:right">PASS:</div></td>
	<td><input type="password" maxlength="16" class="text" name="pass1" style="width:240px"></td></tr>

	<tr><td></td>
	<td><input type="password" maxlength="16" class="text" name="pass2" style="width:240px">(verify)</td></tr>

	<tr><td></td><td><input type="submit" class="btn" name="Make" value="確定" style="width:160px"></td></tr>

	</tbody></table>
	</form>
	</div>
<?php 
	}
	function LoginForm($message = NULL) {
		?>
<div style="width:730px;">
<!-- ログイン -->
<div style="width:350px;float:right">
<h4 style="width:350px">登錄</h4>
<?php print $message?>
<form action="<?php print INDEX?>" method="post" style="padding-left:20px">
<table><tbody>
<tr>
<td><div style="text-align:right">ID:</div></td>
<td><input type="text" maxlength="16" class="text" name="id" style="width:160px"<?php print $_SESSION["id"]?" value=\"$_SESSION[id]\"":NULL?>></td>
</tr>
<tr>
<td><div style="text-align:right">PASS:</div></td>
<td><input type="password" maxlength="16" class="text" name="pass" style="width:160px"></td>
</tr>
<tr><td></td><td>
<input type="submit" class="btn" name="Login" value="登錄" style="width:80px"> 
<a href="?newgame">新玩家?</a>
</td></tr>
</tbody></table>
</form>

<h4 style="width:350px">排行榜</h4>
<?php 
	include_once(CLASS_RANKING);
	$Rank	= new Ranking();
	$Rank->ShowRanking(0,4);
	?>
</div>
<!-- 飾 -->
<div style="width:350px;padding:5px;float:left;">
<div style="width:350px;text-align:center">
<img src="./image/top01.gif" style="margin-bottom:20px" />
</div>
<div style="margin-left:20px">
<DIV class=u>這到底是什麼遊戲?</DIV>
<UL>
<LI>遊戲的目的是得到第一、<BR>並且保持住第一的位置。 
<LI>雖然沒有冒險的要素、<BR>但有點深奧的戰鬥系統。 </LI></UL>
<DIV class=u>戰鬥的感覺是什麼?</DIV>
<UL>
<LI>5人的人物構成隊伍 。 
<LI>各人物各持不同模式、<BR>根據戰鬥的狀況來使用技能。 
<LI><A class=a0 href="?log">這邊</A>可以回覽戰鬥記錄。 </LI></UL></DIV></DIV>

<div class="c-both"></div>
</div>

<!-- -------------------------------------------------------- -->

<div style="margin:15px">
<h4>提示</h4>
用戶數: <?php print UserAmount()?> / <?php print MAX_USERS?><br />
<?php 
	$Abandon	= ABANDONED;
	print(floor($Abandon/(60*60*24))."日中數據沒變化的話數據將消失。");
print("</div>\n");
	}

//////////////////////////////////////////////////
//	上部に表示されるメニュ一。
//	ログインしてる人用とそうでない人。
	function MyMenu() {
		if($this->name && $this->islogin) { // ログインしてる人用
			print('<div id="menu">'."\n");
			//print('<span class="divide"></span>');//區切り
			print('<a href="'.INDEX.'">首頁</a><span class="divide"></span>');
			print('<a href="?hunt">狩獵</a><span class="divide"></span>');
			print('<a href="?item">道具</a><span class="divide"></span>');
			print('<a href="?town">城鎮</a><span class="divide"></span>');
			print('<a href="?setting">設置</a><span class="divide"></span>');
			print('<a href="?log">記錄</a><span class="divide"></span>');
			if(BBS_OUT)
				print('<a href="'.BBS_OUT.'" target="_balnk">BBS</a><span class="divide"></span>'."\n");
			print('</div><div id="menu2">'."\n");
				?>
	<div style="width:100%">
	<div style="width:30%;float:left"><?php print $this->name?></div>
	<div style="width:60%;float:right">
	<div style="width:40%;float:left"><span class="bold">資金</span> : <?php print MoneyFormat($this->money)?></div>
	<div style="width:40%;float:right"><span class="bold">時間</span> : <?php print floor($this->time)?>/<?php print MAX_TIME?></div>
	</div>
	<div class="c-both"></div>
	</div>
<?php 
			print('</div>');
		} else if(!$this->name && $this->islogin) {// 初回ログインの人
			print('<div id="menu">');
			print("First login. Thankyou for the entry.");
			print('</div><div id="menu2">');
			print("fill the blanks. 來吧，請填寫。");
			print('</div>');
		} else { //// ログアウト狀態の人、來客用の表示
			print('<div id="menu">');
			print('<a href="'.INDEX.'">首頁</a><span class="divide"></span>'."\n");
			print('<a href="?newgame">新註冊</a><span class="divide"></span>'."\n");
			print('<a href="?manual">規則和手冊</a><span class="divide"></span>'."\n");
			print('<a href="?gamedata=job">遊戲數據</a><span class="divide"></span>'."\n");
			print('<a href="?log">戰鬥記錄</a><span class="divide"></span>'."\n");
			if(BBS_OUT)
			print('<a href="'.BBS_OUT.'" target="_balnk">BBS</a><span class="divide"></span>'."\n");			
			print('</div><div id="menu2">');
			print("歡迎來到 [ ".TITLE." ]");
			print('</div>');
		}
	}

//////////////////////////////////////////////////
//	HTML開始部分
	function Head() {
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php $this->HtmlScript();?>
<title><?php print TITLE?></title>
</head>
<body><a name="top"></a>
<div id="main_frame">
<div id="title"><img src="./image/title03.gif"></div>
<?php $this->MyMenu();?><div id="contents">
<?php 
	}

//////////////////////////////////////////////////
//	スタイルシ一トとか。
	function HtmlScript() {
		?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="./basis.css" type="text/css">
<link rel="stylesheet" href="./style.css" type="text/css">
<script type="text/javascript" src="prototype.js"></script>
<?php 
	}

//////////////////////////////////////////////////
//	HTML終了部分
	function Foot() {
		?>
</div>
<div style="clear: both;"></div>
<div id="foot">
<a href="?update">UpDate</a> - 
<?php 
	if(BBS_BOTTOM_TOGGLE)
		print('<a href="'.BBS_OUT.'" target="_blank">BBS</a> - '."\n");
		?>
<a href="?manual">手冊</a> - 
<a href="?tutorial">教學</a> - 
<a href="?gamedata=job">遊戲數據</a> - 
<a href="#top">Top</a><br>
Copy Right <a href="http://tekito.kanichat.com/">Tekito</a> 2007-2008.<br>
漢化 By <a href="http://www.firingsquad.com.cn/">FiringSquad中文網</a> 2006-2008.<br>
</div>
</div>
</body>
</html>
<?php 
	}

//////////////////////////////////////////////////
//	初回ログイン用のフォ一ム
	function FirstLogin() {
		// 返值:設定濟み=false / 非設定=true
		if ($this->name)
			return false;

		do {
			if (!$_POST["Done"])
				break;
			if(is_numeric(strpos($_POST["name"],"\t"))) {
				$error	= 'error1';
				break;
			}
			if(is_numeric(strpos($_POST["name"],"\n"))) {
				$error	= 'error';
				break;
			}
			$_POST["name"]	= trim($_POST["name"]);
			$_POST["name"]	= stripslashes($_POST["name"]);
			if (!$_POST["name"]) {
				$error	= 'Name is blank.';
				break;
			}
			$length	= strlen($_POST["name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			$userName	= userNameLoad();
			if(in_array($_POST["name"],$userName)) {
				$error	= '該名字已被使用。';
				break;
			}
			// 最初のキャラの名前
			$_POST["first_name"]	= trim($_POST["first_name"]);
			$_POST["first_name"]	= stripslashes($_POST["first_name"]);
			if(is_numeric(strpos($_POST["first_name"],"\t"))) {
				$error	= 'error';
				break;
			}
			if(is_numeric(strpos($_POST["first_name"],"\n"))) {
				$error	= 'error';
				break;
			}
			if (!$_POST["first_name"]) {
				$error	= 'Character name is blank.';
				break;
			}
			$length	= strlen($_POST["first_name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			if(!$_POST["fjob"]) {
				$error	= 'Select characters job.';
				break;
			}
			$_POST["name"]	= htmlspecialchars($_POST["name"],ENT_QUOTES);
			$_POST["first_name"]	= htmlspecialchars($_POST["first_name"],ENT_QUOTES);

			$this->name	= $_POST["name"];
			userNameAdd($this->name);
			$this->SaveData();
			switch($_POST["fjob"]){
				case "1":
					$job = 1; $gend = 0; break;
				case "2":
					$job = 1; $gend = 1; break;
				case "3":
					$job = 2; $gend = 0; break;
				default:
					$job = 2; $gend = 1;
			}
			include(DATA_BASE_CHAR);
			$char	= new char();
			$char->SetCharData(array_merge(BaseCharStatus($job),array("name"=>$_POST[first_name],"gender"=>"$gend")));
			$char->SaveCharData($this->id);
			return false;
		}while(0);

		include(DATA_BASE_CHAR);
		$war_male	= new char();
		$war_male->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
		$war_female	= new char();
		$war_female->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
		$sor_male	= new char();
		$sor_male->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
		$sor_female	= new char();
		$sor_female->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));

		?>
	<form action="<?php print INDEX?>" method="post" style="margin:15px">
<?php ShowError($error);?>
	<h4>Name of Team</h4>
	<p>Decide the Name of the team.<br />
	It should be more than 1 and less than 16 letters.<br />
	Japanese characters count as 2 letters.</p>
	<p>1-16字符的隊伍名。<br /></p>
	<div class="bold u">TeamName</div>
	<input class="text" style="width:160px" maxlength="16" name="name"
<?php print($_POST["name"]?"value=\"$_POST[name]\"":"")?>>
	<h4>First Character</h4>
	<p>Decide the name of Your First Charactor.<br>
	more than 1 and less than 16 letters.</p>
	<p>第一個人物的名稱。</p>
	<div class="bold u">CharacterName</div>
	<input class="text" type="text" name="first_name" maxlength="16" style="width:160px;margin-bottom:10px">
	<table cellspacing="0" style="width:400px"><tbody>
	<tr><td class="td1" valign="bottom"><div style="text-align:center"><?php print $war_male->ShowImage()?><br><input type="radio" name="fjob" value="1" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $war_female->ShowImage()?><br><input type="radio" name="fjob" value="2" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $sor_male->ShowImage()?><br><input type="radio" name="fjob" value="3" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $sor_female->ShowImage()?><br><input type="radio" name="fjob" value="4" style="margin:3px"></div></td></tr>
	<tr><td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td>
	<td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td></tr>
	<tr><td colspan="2" class="td4"><div style="text-align:center">Warrior</div></td><td colspan="2" class="td4"><div style="text-align:center">Socerer</div></td></tr>
	</tbody></table>
	<p>Choose your first character's job & Gender.</p>
	<p>最初的人物性別與職業</p>
	<input class="btn" style="width:160px" type="submit" value="Done" name="Done">
	<input type="hidden" value="1" name="Done">
	<input class="btn" style="width:160px" type="submit" value="logout" name="logout"></form>
<?php 
			return true;
	}
//////////////////////////////////////////////////
//	普通の1行揭示板
	function bbs01() {
		if(!BBS_BOTTOM_TOGGLE)
			return false;
		$file	= BBS_BOTTOM;
	?>
<div style="margin:15px">
<h4>one line bbs</h4>
錯誤報告或意見，對這裡的開發建議
<form action="?bbs" method="post">
<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?php 
		if(!file_exists($file))
			return false;
		$log	= file($file);
		if($_POST["message"] && strlen($_POST["message"]) < 121) {
			$_POST["message"]	= htmlspecialchars($_POST["message"],ENT_QUOTES);
			$_POST["message"]	= stripslashes($_POST["message"]);

			$name	= ($this->name ? "<span class=\"bold\">{$this->name}</span>":"無名");
			$message	= $name." > ".$_POST["message"];
			if($this->UserColor)
				$message	= "<span style=\"color:{$this->UserColor}\">".$message."</span>";
			$message	.= " <span class=\"light\">(".date("Mj G:i").")</span>\n";
			array_unshift($log,$message);
			while(150 < count($log))// ログ保存行數あ
				array_pop($log);
			WriteFile($file,implode(null,$log));
		}
		foreach($log as $mes)
			print(nl2br($mes));
		print('</div>');
	}
//end of class
//////////////////////////////////////////////////////////////////////
}
?>
