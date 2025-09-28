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
					include_once(CLASS_DIR . 'view/' . 'view.union.php');
					if(UnionProcess($this)) {
						// 戰鬥する
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						// 表示
						$this->fpCloseAll();
						UnionShow($this);
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
				case(isset($_GET["char"])):
					include_once(CLASS_DIR . 'char_stat.php');
					$this->CharDataLoadAll();//キャラデ一タ讀む
					include(DATA_SKILL);
					include(DATA_JUDGE_SETUP);
					$this->LoadUserItem();//道具デ一タ讀む
					CharStatProcess($this);
					$this->fpCloseAll();
					CharStatShow($this);
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
