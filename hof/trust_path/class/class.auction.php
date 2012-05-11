<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

/*
■オークションメモ
□アイテム
データの保存方法
define("AUCTION_ITEM","./*****");//ココ

.dat の中身について
番号(1行目だけ)
番号<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
番号-1<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
番号-2<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
...........
□キャラ
*/
class Auction
{

	// ファイルポインタ
	var $fp;

	// オークションの種類
	// アイテムorキャラ
	var $AuctionType;

	// 競売品番号
	var $ArticleNo;

	// 出品物(キャラ)リスト
	var $Article = array();

	var $UserName;

	// 落札物や落札金の処理用
	var $TempUser = array();

	// 経過ログ
	var $AuctionLog;

	// データが変更されたか?
	var $DataChange = false;

	var $QUERY;
	var $sort;

	////////////////////////////////
	//
	//////////////////////////////////////////////
	//	GETのクエリー名
	function AuctionHttpQuery($name)
	{
		$this->QUERY = $name;
	}
	//////////////////////////////////////////////
	//
	//////////////////////////////////////////////
	//
	function UserGetNameFromTemp($UserID)
	{
		if ($this->TempUser["$UserID"]["Name"]) return $this->TempUser["$UserID"]["Name"];
		else  return "-";
	}
	//////////////////////////////////////////////
	//	オークションでお金を獲得
	function UserGetMoney($UserID, $Money)
	{
		if (!$this->TempUser["$UserID"]["user"])
		{
			$this->TempUser["$UserID"]["user"] = new HOF_Class_User($UserID);
			$this->TempUser["$UserID"]["Name"] = $this->TempUser["$UserID"]["user"]->Name();
		}

		$this->TempUser["$UserID"]["UserGetTotalMoney"] += $Money;
		$this->TempUser["$UserID"]["Money"] = true; //金が追加されたことを記録
	}
	//////////////////////////////////////////////
	//	オークションでアイテム獲得
	function UserGetItem($UserID, $item, $amount)
	{
		if (!$this->TempUser["$UserID"]["user"])
		{
			$this->TempUser["$UserID"]["user"] = new HOF_Class_User($UserID);
			$this->TempUser["$UserID"]["Name"] = $this->TempUser["$UserID"]["user"]->Name();
		}

		$this->TempUser["$UserID"]["UserGetItem"]["$item"] += $amount;
		$this->TempUser["$UserID"]["item"] = true; //アイテムが追加されたことを記録
	}
	//////////////////////////////////////////////
	//	オークションでキャラクター獲得
	//	(動作確認無し)
	function UserGetChar($UserID, $char)
	{
		$this->TempUser["$UserID"]["char"][] = $char; //
		$this->TempUser["$UserID"]["CharAdd"] = true; //キャラクターが追加されたことを記録
	}
	//////////////////////////////////////////////
	//	オークション処理結果を清算する?
	function UserSaveData()
	{
		foreach ($this->TempUser as $user => $Result)
		{
			// お金を得た
			if ($this->TempUser["$user"]["Money"])
			{
				$this->TempUser["$user"]["user"]->GetMoney($this->TempUser["$user"]["UserGetTotalMoney"]);
				$this->TempUser["$user"]["user"]->SaveData();
			}
			// アイテムを得た
			if ($this->TempUser["$user"]["item"])
			{
				foreach ($this->TempUser["$user"]["UserGetItem"] as $itemNo => $amount)
				{
					$this->TempUser["$user"]["user"]->AddItem($itemNo, $amount);
				}
				$this->TempUser["$user"]["user"]->SaveUserItem();
			}
			// キャラクターを得た
			// (動作確認なし)
			if ($this->TempUser["$user"]["CharAdd"])
			{
				if ($this->TempUser["$user"]["char"])
				{
					foreach ($this->TempUser["$user"]["char"] as $char)
					{
						$char->SaveCharData($user);
					}
				}
			}
			// ユーザが開いた全てのファイルのファイルポインタを閉じる
			$this->TempUser["$user"]["user"]->fpCloseAll();
		}
		unset($this->TempUser);
	}
	//////////////////////////////////////////////
	//	入札する権利があるかどうか返す
	function ItemBidRight($ArticleNo, $UserID)
	{
		if ($this->Article["$ArticleNo"]["bidder"] == $UserID) return false;
		if ($this->Article["$ArticleNo"]["exhibitor"] == $UserID) return false;
		return true;
	}
	//////////////////////////////////////////////
	//	ユーザの名前を呼び出す
	function LoadUserName($id)
	{
		if ($this->UserName["$id"])
		{
			return $this->UserName["$id"];
		}
		else
		{
			$User = new HOF_Class_User($id);
			$Name = $User->Name();
			if ($Name)
			{
				$this->UserName["$id"] = $Name;
			}
			else
			{
				$this->UserName["$id"] = "-";
			}
			return $this->UserName["$id"];
		}
	}


	//////////////////////////////////////////////
	//	出品物一覧を表示する(その2) 表示の並びが違うだけ
	function ItemShowArticle2($bidding = false)
	{
		if (count($this->Article) == 0)
		{
			print ("競売物無し(No auction)<br />\n");
			return false;
		}
		else
		{
			$Now = time();
			// ソートされている色を変える(可変変数)
			if ($this->sort) ${"Style_" . $this->sort} = ' class="a0"';
			$exp = '<tr><td class="td9"><a href="?menu=' . $this->QUERY . '&sort=no"' . $Style_no . '>No</a></td>' . '<td class="td9"><a href="?menu=' . $this->QUERY . '&sort=time"' . $Style_time . '>残り</td>' . '<td class="td9"><a href="?menu=' . $this->QUERY . '&sort=price"' . $Style_price . '>価格</a>' . '<br /><a href="?menu=' . $this->QUERY . '&sort=rprice"' . $Style_rprice . '>(昇)</a></td>' . '<td class="td9">Item</td>' . '<td class="td9"><a href="?menu=' . $this->QUERY . '&sort=bid"' . $Style_bid .
				'>Bids</a></td>' . '<td class="td9">入札者</td><td class="td9">出品者</td></tr>' . "\n";

			print ("総出品数:" . $this->count() . "\n");
			print ('<table style="width:725px;text-align:center" cellpadding="0" cellspacing="0" border="0">' . "\n");
			print ($exp);
			foreach ($this->Article as $Article)
			{

				// 競売番号
				print ("<tr><td rowspan=\"2\" class=\"td7\">");
				print ($Article["No"]);
				// 終了時刻
				print ("</td><td class=\"td7\">");
				print (AuctionLeftTime($Now, $Article["end"]));
				// 現在入札価格
				print ("</td><td class=\"td7\">");
				print (HOF_Helper_Global::MoneyFormat($Article["price"]));
				// アイテム
				print ('</td><td class="td7" style="text-align:left">');
				$item = HOF_Model_Data::getItemData($Article["item"]);
				print (HOF_Class_Item::ShowItemDetail($item, $Article["amount"], 1));
				// 合計入札数
				print ("</td><td class=\"td7\">");
				print ($Article["TotalBid"]);
				// 入札者
				print ("</td><td class=\"td7\">");
				if (!$Article["bidder"]) $bidder = "-";
				else  $bidder = $this->LoadUserName($Article["bidder"]);
				print ($bidder);
				// 出品者
				print ("</td><td class=\"td8\">");
				$exhibitor = $this->LoadUserName($Article["exhibitor"]);
				print ($exhibitor);
				// コメント
				print ("</td></tr><tr>");
				print ("<td colspan=\"6\" class=\"td8\" style=\"text-align:left\">");
				print ('<form action="?menu=auction" method="post">');
				// 入札フォーム
				if ($bidding)
				{
					print ('<a style="margin:0 10px" href="#" onClick="$(\'#Bid' . $Article["No"] . '\').toggle(); return false;">入札</a>');
					print ('<span style="display:none" id="Bid' . $Article["No"] . '">');
					print ('&nbsp;<input type="text" name="BidPrice" style="width:80px" class="text" value="' . BottomPrice($Article["price"]) . '">');
					print ('<input type="submit" value="Bid" class="btn">');
					print ('<input type="hidden" name="ArticleNo" value="' . $Article["No"] . '">');
					print ('</span>');
				}
				print ($Article["comment"] ? $Article["comment"] : "&nbsp;");
				print ("</form>");
				print ("</td></tr>\n");

				print ("</td></tr>\n");
			}
			print ($exp);
			print ("</table>\n");
			return true;
		}
	}
	//////////////////////////////////////////////////
	//	その番号の競売品が出品されているかたしかめる。
	function ItemArticleExists($no)
	{
		if ($this->Article["$no"])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	//////////////////////////////////////////////
	//	アイテムを出品する
	function ItemAddArticle($item, $amount, $id, $time, $StartPrice, $comment)
	{
		// 終了時刻の計算
		$Now = time();
		$end = $Now + round($now + (60 * 60 * $time));
		// 開始価格のあれ
		if (ereg("^[0-9]", $StartPrice))
		{
			$price = (int)$StartPrice;
		}
		else
		{
			$price = 0;
		}
		// コメント処理
		$comment = str_replace("\t", "", $comment);
		$comment = htmlspecialchars(trim($comment), ENT_QUOTES);
		$comment = stripslashes($comment);
		// 競売品番号
		$this->ArticleNo++;
		if (9999 < $this->ArticleNo) $this->ArticleNo = 0;
		$New = array(
			// 競売品番号
			"No" => $this->ArticleNo,
			// 終了時刻
			"end" => $end,
			// 今の入札価格
			"price" => (int)$price,
			// 出品者id
			"exhibitor" => $id,
			// アイテム
			"item" => $item,
			// 個数
			"amount" => (int)$amount,
			// 合計入札数
			"TotalBid" => 0,
			// 最終入札者id
			"bidder" => NULL,
			// 最終入札時間(使ってない！？使いたければ使ってください)
			"latest" => NULL,
			// コメント
			"comment" => $comment,
			// IP
			"IP" => $_SERVER[REMOTE_ADDR],
			);
		array_unshift($this->Article, $New);
		$itemData = HOF_Model_Data::getItemData($item);
		$this->log->add("No." . $this->ArticleNo . " に <img src=\"" . HOF_Class_Icon::getImageUrl($itemData["img"], IMG_ICON . 'item/') . "\"><span class=\"bold\">{$itemData[name]} x{$amount}</span>個が<span class=\"charge\">出品されました。</span>");
		$this->DataChange = true;
	}

}


?>