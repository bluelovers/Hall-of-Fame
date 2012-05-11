<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//require_once (CLASS_AUCTION);

/**
 * ■オークションメモ
 * □アイテム
 * データの保存方法
 * define("$this->file","./*****");//ココ

 * .dat の中身について
 * 番号(1行目だけ)
 * 番号<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * 番号-1<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * 番号-2<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * ...........
 * □キャラ
 */
//class HOF_Class_Item_Auction extends Auction
class HOF_Class_Item_Auction
{

	var $fp;

	/**
	 * オークションの種類
	 * アイテムorキャラ
	 */
	var $AuctionType;

	/**
	 * 競売品番号
	 */
	var $ArticleNo;

	/**
	 * 出品物(キャラ)リスト
	 */
	var $Article = array();

	var $UserName;

	/**
	 * 落札物や落札金の処理用
	 */
	var $TempUser = array();

	/**
	 * 経過ログ
	 */
	var $AuctionLog;

	/**
	 * データが変更されたか?
	 */
	var $DataChange = false;

	var $QUERY;
	var $sort;

	var $file = AUCTION_ITEM;

	/**
	 * 経過ログ
	 * @var HOF_Class_Item_Auction_Log
	 */
	public $log = null;

	/**
	 * コンストラクタ
	 */
	function __construct($type)
	{
		// アイテムオークション
		if ($type == "item")
		{
			$this->AuctionType = "item";
			$this->fpread();
			// キャラオークション
		}
		elseif ($type == "char")
		{
			$this->AuctionType = "char";
		}

		$this->log = new HOF_Class_Item_Auction_Log(&$this);
	}

	function __destruct()
	{
		$this->fpclose();
	}

	/**
	 * アイテムオークション用のファイルを開いて
	 * データを取り出し,格納
	 */
	function fpread()
	{
		// ファイルがある場合
		if (file_exists($this->file))
		{
			//$fp	= fopen($this->file,"r+");
			$this->fp = HOF_Class_File::FileLock($this->file);
			if (!$this->fp) return false;
			//flock($fp, LOCK_EX);

			/*
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
			*/

			$data = HOF_Class_Yaml::load($this->fp);

			$this->ArticleNo = $data['no'];
			$this->Article = $data['list'];

			// ファイルが無い場合
		}
		else
		{
			// 何もしない。
		}
	}

	function fpclose()
	{
		@fclose($this->fp);
		unset($this->fp);
	}

	/**
	 * オークションのデータを保存する
	 */
	function fpsave()
	{
		if (!$this->DataChange)
		{
			$this->fpclose();

			return false;
		}

		$data = array();

		$data['no'] = $this->ArticleNo;

		// アイテム オークションを保存する。
		//$string = $this->ArticleNo . "\n";
		foreach ($this->Article as $val)
		{
			//if(strlen($val["end"]) != 10) continue;
			//$string .= $val["No"] . "<>" . $val["end"] . "<>" . $val["price"] . "<>" . $val["exhibitor"] . "<>" . $val["item"] . "<>" . $val["amount"] . "<>" . $val["TotalBid"] . "<>" . $val["bidder"] . "<>" . $val["latest"] . "<>" . $val["comment"] . "<>" . $val["IP"] . "\n";

			$data['list'][$val['No']] = $val;
		}

		$string = HOF_Class_Yaml::dump($data);

		//print($string);
		if (file_exists($this->file) && $this->fp)
		{
			HOF_Class_File::WriteFileFP($this->fp, $string, true);

			$this->fpclose();
		}
		else
		{
			HOF_Class_File::WriteFile($this->file, $string, true);
		}

		$this->log->save();
	}

	/**
	 * その番号の競売品が出品されているかたしかめる。
	 */
	function exists($no)
	{
		//debug($no, $this->Article);

		if ($this->Article[$no])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * GETのクエリー名
	 */
	function AuctionHttpQuery($name)
	{
		$this->QUERY = $name;
	}

	/**
	 * 出品物の数
	 */
	function count()
	{
		return count($this->Article);
	}

	function ItemSortBy($type)
	{
		switch ($type)
		{
			case "no":
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByNo");
				$this->sort = "no";
				break;
			case "time":
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByTime");
				$this->sort = "time";
				break;
			case "price":
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByPrice");
				$this->sort = "price";
				break;
			case "rprice":
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByRPrice");
				$this->sort = "rprice";
				break;
			case "bid":
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByTotalBid");
				$this->sort = "bid";
				break;
			default:
				usort($this->Article, "HOF_Class_Item_Auction_Sort::sortByTime");
				$this->sort = "time";
				break;
		}
	}

	/**
	 * 最低価格を返す
	 * 価格の5%が最低値。
	 * 100以下なら100がそれになる。
	 */
	function BottomPrice($price)
	{
		$bottom = floor($price * 0.10);

		if ($bottom < 101)
		{
			return sprintf("%0.0f", $price + 100);
		}
		else
		{
			return sprintf("%0.0f", $price + $bottom);
		}
	}

	/**
	 * 最低入札価格を返す
	 */
	function ItemBottomPrice($ArticleNo)
	{
		if ($this->Article["$ArticleNo"])
		{
			return $this->BottomPrice($this->Article["$ArticleNo"]["price"]);
		}
	}
	/**
	 * 入札する
	 */
	function ItemBid($ArticleNo, $BidPrice, $Bidder, $BidderName)
	{
		if (!$Article = $this->Article["$ArticleNo"]) return false;

		$BottomPrice = $this->BottomPrice($this->Article["$ArticleNo"]["price"]);

		// IPが同じ
		if ($Article["IP"] == $_SERVER[REMOTE_ADDR])
		{
			HOF_Helper_Global::ShowError("IP制限.");
			return false;
		}

		// 携帯端末を禁止する。
		if (isMobile == "i")
		{
			HOF_Helper_Global::ShowError("mobile forbid.");
			return false;
		}

		// 最低入札価格を割っている場合
		if ($BidPrice < $BottomPrice) return false;

		// 誰かが入札していた場合お金を返金する。
		if ($Article["bidder"])
		{
			$this->UserGetMoney($Article["bidder"], $Article["price"]);
			$this->UserSaveData();
		}

		// 入札時間が残り少ないなら延長する。
		$Now = time();
		$left = $this->AuctionLeftTime($Now, $Article["end"], true);
		/* // 残り時間1時間以下なら15分延長する
		if(1 < $left && $left < 3601) {
		$this->Article["$ArticleNo"]["end"]	+= 900;
		}
		*/

		// 残り時間15分以下なら 15分にする。
		if (0 < $left && $left < 901)
		{
			$dif = 900 - $left;
			$this->Article["$ArticleNo"]["end"] += $dif;
		}

		$this->Article["$ArticleNo"]["price"] = $BidPrice;
		$this->Article["$ArticleNo"]["TotalBid"]++;
		$this->Article["$ArticleNo"]["bidder"] = $Bidder;
		$this->DataChange = true;

		$item = HOF_Model_Data::getItemData($Article["item"]);
		//$this->log->add("No.".$Article["No"]." <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個に ".HOF_Helper_Global::MoneyFormat($BidPrice)." で ".$this->LoadUserName($Bidder)." が<span class=\"support\">入札しました。</span>");
		$this->log->add("No." . $Article["No"] . " <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個に " . HOF_Helper_Global::MoneyFormat($BidPrice) . " で " . $BidderName . " が<span class=\"support\">入札しました。</span>");

		return true;
	}

	/**
	 * 残り時間を返す
	 */
	function AuctionLeftTime($now, $end, $int = false)
	{
		$left = $end - $now;
		// $int=true なら差分だけ返す
		if ($int) return $left;
		if ($left < 1)
		{ // 終了している場合はfalse
			return false;
		}
		if ($left < 601)
		{
			return "{$left}秒";
		}
		else
			if ($left < 3601)
			{
				$minutes = floor($left / 60);
				return "{$minutes}分";
			}
			else
			{
				$hour = floor($left / 3600);
				$minutes = floor(($left % 3600) / 60);
				return "{$hour}時間{$minutes}分";
			}
	}

	/**
	 * 時間が経過して終了した競売品の処理
	 */
	function ItemCheckSuccess()
	{
		$Now = time();
		foreach ($this->Article as $no => $Article)
		{
			// 競売時間が残っているなら次
			if ($this->AuctionLeftTime($Now, $Article["end"])) continue;

			$item = HOF_Model_Data::getItemData($Article["item"]);
			if ($Article["bidder"])
			{
				// 落札者がいるならアイテムを渡す。
				// 落札者がいるなら出品者に金を渡す。
				$this->UserGetItem($Article["bidder"], $Article["item"], $Article["amount"]);
				$this->UserGetMoney($Article["exhibitor"], $Article["price"]);
				// 結果をログに残せ
				$this->log->add("No.{$Article[No]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個 を " . $this->UserGetNameFromTemp($Article["bidder"]) . " が " . HOF_Helper_Global::MoneyFormat($Article["price"]) . " で<span class=\"recover\">落札しました。</span>");
			}
			else
			{
				// 入札が無かった場合、出品者に返却。
				$this->UserGetItem($Article["exhibitor"], $Article["item"], $Article["amount"]);
				// 結果をログに残せ
				$this->log->add("No.{$Article[No]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個 は<span class=\"dmg\">入札者無しで流れました。</span>");
			}
			// 最後に消す
			unset($this->Article["$no"]);
			$this->DataChange = true;
		}
	}

	/**
	 * 出品物一覧を表示する(その1) 表示の並びが違うだけ
	 */
	function ItemShowArticle($bidding = false)
	{
		if (count($this->Article) == 0)
		{
			print ("競売物無し(No auction)<br />\n");
			return false;
		}
		else
		{
			$Now = time();
			$exp = '<tr><td class="td9">番号</td><td class="td9">価格</td><td class="td9">入札者</td><td class="td9">入札数</td><td class="td9">残り</td>' . '<td class="td9">出品者</td><td class="td9">コメント</td></tr>' . "\n";
			print ('<table style="width:725px;text-align:center" cellpadding="0" cellspacing="0" border="0">' . "{$exp}\n");
			foreach ($this->Article as $Article)
			{

				print ("<tr><td class=\"td7\">");
				// 競売番号
				print ($Article["No"]);
				print ("</td><td class=\"td7\">");
				// 現在入札価格
				print (HOF_Helper_Global::MoneyFormat($Article["price"]));
				print ("</td><td class=\"td7\">");
				// 入札者
				if (!$Article["bidder"]) $bidder = "-";
				else  $bidder = $this->LoadUserName($Article["bidder"]);
				print ($bidder);
				print ("</td><td class=\"td7\">");
				// 合計入札数
				print ($Article["TotalBid"]);
				print ("</td><td class=\"td7\">");
				// 終了時刻
				print ($this->AuctionLeftTime($Now, $Article["end"]));
				print ("</td><td class=\"td7\">");
				// 出品者
				$exhibitor = $this->LoadUserName($Article["exhibitor"]);
				print ($exhibitor);
				print ("</td><td class=\"td8\">");
				// コメント
				print ($Article["comment"] ? $Article["comment"] : "&nbsp;");
				print ("</td></tr>\n");
				// アイテム
				print ('<tr><td colspan="7" style="text-align:left;padding-left:15px" class="td6">');
				$item = HOF_Model_Data::getItemData($Article["item"]);
				print ('<form action="?menu=auction" method="post">');
				// 入札フォーム
				if ($bidding)
				{
					print ('<a href="#" onClick="$(\'Bid' . $Article["No"] . '\').toggle()">入札</a>');
					print ('<span style="display:none" id="Bid' . $Article["No"] . '">');
					print ('&nbsp;<input type="text" name="BidPrice" style="width:80px" class="text" value="' . BottomPrice($Article["price"]) . '">');
					print ('<input type="submit" value="Bid" class="btn">');
					print ('<input type="hidden" name="ArticleNo" value="' . $Article["No"] . '">');
					print ('</span>');
				}
				print (HOF_Class_Item::ShowItemDetail($item, $Article["amount"], 1));
				print ("</form>");
				print ("</td></tr>\n");
			}
			print ("{$exp}</table>\n");
			return true;
		}
	}

	function UserGetNameFromTemp($UserID)
	{
		if ($this->TempUser["$UserID"]["Name"]) return $this->TempUser["$UserID"]["Name"];
		else  return "-";
	}

	/**
	 * オークションでお金を獲得
	 */
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
	/**
	 * オークションでアイテム獲得
	 */
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

	/**
	 * オークションでキャラクター獲得
	 * (動作確認無し)
	 */
	function UserGetChar($UserID, $char)
	{
		$this->TempUser["$UserID"]["char"][] = $char; //
		$this->TempUser["$UserID"]["CharAdd"] = true; //キャラクターが追加されたことを記録
	}

	/**
	 * オークション処理結果を清算する?
	 */
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

	/**
	 * 入札する権利があるかどうか返す
	 */
	function ItemBidRight($ArticleNo, $UserID)
	{
		if ($this->Article["$ArticleNo"]["bidder"] == $UserID) return false;
		if ($this->Article["$ArticleNo"]["exhibitor"] == $UserID) return false;
		return true;
	}

	/**
	 * ユーザの名前を呼び出す
	 */
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

	/**
	 * 出品物一覧を表示する(その2) 表示の並びが違うだけ
	 */
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
				print ($this->AuctionLeftTime($Now, $Article["end"]));
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
					print ('&nbsp;<input type="text" name="BidPrice" style="width:80px" class="text" value="' . $this->BottomPrice($Article["price"]) . '">');
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

	/**
	 * アイテムを出品する
	 */
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
