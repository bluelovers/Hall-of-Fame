<?php
include(CLASS_USER);
include(GLOBAL_PHP);
include_once(CLASS_DIR."utils/glob.php");

// 引入重構後的模組檔案
include_once(CLASS_DIR."session_manager.php");
include_once(CLASS_DIR."display_manager.php");
include_once(CLASS_DIR."monster_system.php");
include_once(CLASS_DIR."battle_system.php");
include_once(CLASS_DIR."form_handlers.php");
include_once(CLASS_DIR."logging_system.php");

class main extends user {

	var $islogin	= false;

/**
 * 主構造函數
 * Main constructor
 *
 * 初始化應用程式的主要流程
 * Initialize the main application flow
 */
function main() {
    SessionSwitch($this);
    Set_ID_PASS($this);
    ob_start();
    $this->Order();
    $content    = ob_get_contents();
    ob_end_clean();

    Head($this);
    print($content);
    Debug($this);
    //$this->ShowSession();
    Foot();
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
					include_once(CLASS_DIR . 'view/view.town.php');
					$this->LoadUserItem();//道具デ一タ讀む
					$this->fpCloseAll();
					TownShow($this);
					return 0;

				// シミュれ
				case($_SERVER["QUERY_STRING"] === "simulate"):
					$this->CharDataLoadAll();//キャラデ一タ讀む
					if(SimuBattleProcess($this))
						$this->SaveData();

					$this->fpCloseAll();
					SimuBattleShow($this, $result);
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
					if(MonsterBattle($this)) {
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						$this->fpCloseAll();
						MonsterShow($this);
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
					include_once(CLASS_DIR . 'view/view.item.php');
					$this->LoadUserItem();//道具デ一タ讀む
					//ItemProcess($this);
					$this->fpCloseAll();
					ItemShow($this);
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
					$RankProcess	= RankProcess($this, $Ranking);

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
					LoginMain($this);
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
			case($_SERVER["QUERY_STRING"] === "bbs"):	bbs01($this);	return true;
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







//////////////////////////////////////////////////////////////////////











//end of class
//////////////////////////////////////////////////////////////////////
}
?>
