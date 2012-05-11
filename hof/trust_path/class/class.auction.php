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
	// コンストラクタ
	function __construct($type)
	{
		// アイテムオークション
		if ($type == "item")
		{
			$this->AuctionType = "item";
			$this->ItemArticleRead();
			// キャラオークション
		}
		else
			if ($type == "char")
			{
				$this->AuctionType = "char";
			}
	}
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

			print ("総出品数:" . $this->ItemAmount() . "\n");
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
		$this->AddLog("No." . $this->ArticleNo . " に <img src=\"" . HOF_Class_Icon::getImageUrl($itemData["img"], IMG_ICON.'item/') . "\"><span class=\"bold\">{$itemData[name]} x{$amount}</span>個が<span class=\"charge\">出品されました。</span>");
		$this->DataChange = true;
	}
	//////////////////////////////////////////////
	//	オークションのデータを保存する
	function ItemSaveData()
	{
		if (!$this->DataChange)
		{
			fclose($this->fp);
			unset($this->fp);
			return false;
		}
		// アイテム オークションを保存する。
		$string = $this->ArticleNo . "\n";
		foreach ($this->Article as $val)
		{
			//if(strlen($val["end"]) != 10) continue;
			$string .= $val["No"] . "<>" . $val["end"] . "<>" . $val["price"] . "<>" . $val["exhibitor"] . "<>" . $val["item"] . "<>" . $val["amount"] . "<>" . $val["TotalBid"] . "<>" . $val["bidder"] . "<>" . $val["latest"] . "<>" . $val["comment"] . "<>" . $val["IP"] . "\n";
		}
		//print($string);
		if (file_exists(AUCTION_ITEM) && $this->fp)
		{
			HOF_Class_File::WriteFileFP($this->fp, $string, true);
			fclose($this->fp);
			unset($this->fp);
		}
		else
		{
			HOF_Class_File::WriteFile(AUCTION_ITEM, $string, true);
		}
		$this->SaveLog();
	}
	//////////////////////////////////////////////
	//

	//////////////////////////////////////////////
	// アイテムオークション用のファイルを開いて
	// データを取り出し,格納
	function ItemArticleRead()
	{
		// ファイルがある場合
		if (file_exists(AUCTION_ITEM))
		{
			//$fp	= fopen(AUCTION_ITEM,"r+");
			$this->fp = HOF_Class_File::FileLock(AUCTION_ITEM);
			//if(!$fp) return false;
			//flock($fp,LOCK_EX);
			// 競売番号を先読みする
			$this->ArticleNo = trim(fgets($this->fp));
			while (!feof($this->fp))
			{
				$str = fgets($this->fp);
				if (!$str) continue;
				$article = explode("<>", $str);
				if (strlen($article["1"]) != 10) continue;
				$this->Article[$article["0"]] = array(
					"No" => $article["0"], // 競売番号
					"end" => $article["1"], // 終了時刻
					"price" => $article["2"], // 今の入札価格
					"exhibitor" => $article["3"], // 出品者id
					"item" => $article["4"], // アイテム
					"amount" => $article["5"], // 個数
					"TotalBid" => $article["6"], // 合計入札数
					"bidder" => $article["7"], // 最終入札者id
					"latest" => $article["8"], // 最終入札時間
					"comment" => trim($article["9"]), // コメント
					"IP" => trim($article["10"]), // IP
					);
			}
			// ファイルが無い場合
		}
		else
		{
			// 何もしない。
		}
	}
	//////////////////////////////////////////////////
	//	出品物の数
	function ItemAmount()
	{
		return count($this->Article);
	}
	//////////////////////////////////////////////////
	//	オークション経過ログを読む
	function LoadLog()
	{
		if ($this->AuctionType == "item")
		{
			if (!file_exists(AUCTION_ITEM_LOG))
			{
				$this->AuctionLog = array();
				return false;
			}
			$fp = fopen(AUCTION_ITEM_LOG, "r+");
			if (!$fp) return false;
			flock($fp, LOCK_EX);
			while (!feof($fp))
			{
				$str = trim(fgets($fp));
				if (!$str) continue;
				$this->AuctionLog[] = $str;
			}
		}
	}
	//////////////////////////////////////////////////
	//	オークション経過ログの保存
	function SaveLog()
	{
		if ($this->AuctionType == "item")
		{
			if (!$this->AuctionLog) return false;
			// 30行以下に収める
			while (100 < count($this->AuctionLog))
			{
				array_pop($this->AuctionLog);
			}
			foreach ($this->AuctionLog as $log)
			{
				$string .= $log . "\n";
			}
			HOF_Class_File::WriteFile(AUCTION_ITEM_LOG, $string);
		}
	}
	//////////////////////////////////////////////////
	//	ログの表示
	function ShowLog()
	{
		if (!$this->AuctionLog) $this->LoadLog();
		if (!$this->AuctionLog) return false;
		foreach ($this->AuctionLog as $log)
		{
			print ("{$log}<br />\n");
		}
	}
	//////////////////////////////////////////////////
	//	ログの追加
	function AddLog($string)
	{
		if (!$this->AuctionLog) $this->LoadLog();
		if (!$this->AuctionLog) $this->AuctionLog = array();
		array_unshift($this->AuctionLog, $string);
	}


}


?>