<?php
include(CLASS_USER);
include(GLOBAL_PHP);
include_once(CLASS_DIR."utils/glob.php");

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
		include_once(CLASS_DIR . 'login.php');

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
		
		if( true === $message = CheckLogin($this) ):
		//if( false ):
		// ログイン
			include_once(DATA_ITEM);
			include(CLASS_CHAR);
			
			if(FirstLogin($this))
				return 0;

			switch(true) {

				case($this->OptionOrder()):	return false;

				case($_POST["delete"]):
					if(DeleteMyData($this))
						return 0;

				// 設定
				case($_SERVER["QUERY_STRING"] === "setting"):
					include_once(CLASS_DIR . 'setting.php');
					if(SettingProcess($this))
						$this->SaveData();

					$this->fpCloseAll();
					SettingShow($this);
					return 0;

				// オ一クション
				case($_GET["menu"] === "auction"):
					include_once(CLASS_DIR . 'auction.php');
					$this->LoadUserItem();//道具デ一タ讀む
					AuctionHeader($this);

					/*
					* 出品用のフォ一ム
					* 表示を要求した場合か、
					* 出品に失敗した場合表示する。
					*/
					$ResultExhibit	= AuctionItemExhibitProcess($this, $ItemAuction);
					$ResultBidding	= AuctionItemBiddingProcess($this, $ItemAuction);
					$ItemAuction->ItemSaveData();// 變更があった場合だけ保存する。
    
					// 出品リストを表示する
					if($_POST["ExhibitItemForm"]) {
						$this->fpCloseAll();
						AuctionItemExhibitForm($this, $ItemAuction);

					// 出品か競標に成功した場合はデ一タを保存する
					} else if($ResultExhibit !== false) {

						if($ResultExhibit === true || $ResultBidding === true)
							$this->SaveData();

						$this->fpCloseAll();
						AuctionItemBiddingForm($this, $ItemAuction);

					// それ以外
					} else {
						$this->fpCloseAll();
						AuctionItemExhibitForm($this, $ItemAuction);
					}

					AuctionFoot($this, $ItemAuction);
					return 0;

				// 狩場
				case($_SERVER["QUERY_STRING"] === "hunt"):
					include_once(CLASS_DIR . 'class.log_viewer.php');
					$this->LoadUserItem();//道具デ一タ讀む
					$this->fpCloseAll();
					HuntShow($this);
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
					include_once(CLASS_DIR . 'smithy.php');
					$this->LoadUserItem();
					SmithyRefineHeader($this);
					if(SmithyRefineProcess($this))
						$this->SaveData();

					$this->fpCloseAll();
					SmithyRefineShow($this);
					return 0;

				// 製作
				case($_GET["menu"] === "create"):
					include_once(CLASS_DIR . 'smithy.php');
					$this->LoadUserItem();
					SmithyCreateHeader($this);
					include(DATA_CREATE);//製作できるものデ一タ等
					if(SmithyCreateProcess($this))
						$this->SaveData();

					$this->fpCloseAll();
					SmithyCreateShow($this);
					return 0;
				// ショップ(舊式:買う,賣る,打工)
				case($_SERVER["QUERY_STRING"] === "shop"):
					include_once(CLASS_DIR . 'shop.php');
					$this->LoadUserItem();//道具デ一タ讀む
					if(ShopProcess($this))
						$this->SaveData();
					$this->fpCloseAll();
					ShopShow($this);
					return 0;
				// ショップ(買う)
				case($_GET["menu"] === "buy"):
					include_once(CLASS_DIR . 'shop.php');
					$this->LoadUserItem();//道具デ一タ讀む
					ShopHeader($this);
					if(ShopBuyProcess($this))
						$this->SaveData();
					$this->fpCloseAll();
					ShopBuyShow($this);
					return 0;

				// ショップ(賣る)
				case($_GET["menu"] === "sell"):
					include_once(CLASS_DIR . 'shop.php');
					$this->LoadUserItem();//道具デ一タ讀む
					ShopHeader($this);
					if(ShopSellProcess($this))
						$this->SaveData();
					$this->fpCloseAll();
					ShopSellShow($this);
					return 0;

				// ショップ(動く)
				case($_GET["menu"] === "work"):
					include_once(CLASS_DIR . 'shop.php');
					ShopHeader($this);
					if(WorkProcess($this))
						$this->SaveData();
					$this->fpCloseAll();
					WorkShow($this);
					return 0;

				// ランキング
				case($_GET["menu"] === "rank"):
					include_once(CLASS_DIR . 'class.log_viewer.php');
					$this->CharDataLoadAll();//キャラデ一タ讀む
					$RankProcess	= $this->RankProcess($Ranking);

					if ($RankProcess === "BATTLE") {
						$this->SaveData();
						$this->fpCloseAll();
					} else if ($RankProcess === true) {
						$this->SaveData();
						$this->fpCloseAll();
						RankShow($this, $Ranking);
					} else {
						$this->fpCloseAll();
						RankShow($this, $Ranking);
					}
					return 0;

				// 僱用
				case($_SERVER["QUERY_STRING"] === "recruit"):
					include_once(CLASS_DIR . 'recruit.php');
					if(RecruitProcess($this))
						$this->SaveData();

					$this->fpCloseAll();
					RecruitShow($this);
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
			include_once(CLASS_DIR . 'login.php');
			switch(true) {
				case($this->OptionOrder()):	return false;
				case($_POST["Make"]):
					list($bool,$message) = MakeNewData($this);
					if( true === $bool ) {
						$this->LoginForm($message);
						return false;
					}
				case($_SERVER["QUERY_STRING"] === "newgame"):
					NewForm($message);	return false;
				default:	$this->LoginForm($message);
			}
		endif;
	}

//////////////////////////////////////////////////
//	UpDate,BBS,Manual等
	function OptionOrder() {
		$this->fpCloseAll();
		switch(true) {
			case($_SERVER["QUERY_STRING"] === "rank"):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				RankAllShow();
				return true;
			case($_SERVER["QUERY_STRING"] === "update"):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				showUpDate();
				return true;
			case($_SERVER["QUERY_STRING"] === "bbs"):	$this->bbs01();	return true;
			case($_SERVER["QUERY_STRING"] === "manual"):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				showManual();
				return true;
			case($_SERVER["QUERY_STRING"] === "manual2"):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				showManual2();
				return true;
			case($_SERVER["QUERY_STRING"] === "tutorial"):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				showTutorial();
				return true;
			case($_SERVER["QUERY_STRING"] === "log"):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showLogList();
				return true;
			case($_SERVER["QUERY_STRING"] === "clog"):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showCommonLog();
				return true;
			case($_SERVER["QUERY_STRING"] === "ulog"):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showUnionLog();
				return true;
			case($_SERVER["QUERY_STRING"] === "rlog"):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showRankingLog();
				return true;
			case($_GET["gamedata"]):
				include_once(CLASS_DIR . 'class.content_viewer.php');
				showGameData();
				return true;
			case($_GET["log"]):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showBattleLog($_GET["log"]);
				return true;
			case($_GET["ulog"]):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showBattleLog($_GET["ulog"],"UNION");
				return true;
			case($_GET["rlog"]):
				include_once(CLASS_DIR . 'class.log_viewer.php');
				showBattleLog($_GET["rlog"],"RANK");
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
			$Result	= $Ranking->Challenge($this);

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
			$message	.= " <span class=\"light\">(".date("c").")</span>\n";
			array_unshift($log,$message);
			while(50 < count($log))
				array_pop($log);
			WriteFile($file,implode(null,$log));
		}
		foreach($log as $mes)
			print(nl2br($mes));
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
		// session_start();

		if($_SESSION):
		//	session_destroy();//Sleipnirだとおかしい...?(最初期)
		//	unset($_SESSION);//こっちは大丈夫(やっぱりこれは馱目かも)(修正後)
			//結局,セッションをforeachでル一プして1個づつunset(2007/9/14 再修正)
			foreach($_SESSION as $key => $val)
				unset($_SESSION["$key"]);
		endif;

		session_id($NewID);
		// session_start();
		$_SESSION	= unserialize($temp);
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
			$message	.= " <span class=\"light\">(".date("c").")</span>\n";
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
