<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once (CLASS_AUCTION);

/**
 * ■オークションメモ
 * □アイテム
 * データの保存方法
 * define("AUCTION_ITEM","./*****");//ココ

 * .dat の中身について
 * 番号(1行目だけ)
 * 番号<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * 番号-1<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * 番号-2<>競売終了時刻<>今の入札価格<>出品者id<>アイテム<>個数<>合計入札数<>最終入札者id<>最終入札時間<>コメント<>IP
 * ...........
 * □キャラ
 */
class HOF_Class_Item_Auction extends Auction
{

	/**
	 * 経過ログ
	 * @var HOF_Class_Item_Auction_Log
	 */
	public $log = null;

	function __construct($type)
	{
		parent::__construct($type);

		$this->log = new HOF_Class_Item_Auction_Log($this);
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
		$left = AuctionLeftTime($Now, $Article["end"], true);
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
		//$this->AddLog("No.".$Article["No"]." <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個に ".HOF_Helper_Global::MoneyFormat($BidPrice)." で ".$this->LoadUserName($Bidder)." が<span class=\"support\">入札しました。</span>");
		$this->AddLog("No." . $Article["No"] . " <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個に " . HOF_Helper_Global::MoneyFormat($BidPrice) . " で " . $BidderName . " が<span class=\"support\">入札しました。</span>");

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
				$this->AddLog("No.{$Article[No]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個 を " . $this->UserGetNameFromTemp($Article["bidder"]) . " が " . HOF_Helper_Global::MoneyFormat($Article["price"]) . " で<span class=\"recover\">落札しました。</span>");
			}
			else
			{
				// 入札が無かった場合、出品者に返却。
				$this->UserGetItem($Article["exhibitor"], $Article["item"], $Article["amount"]);
				// 結果をログに残せ
				$this->AddLog("No.{$Article[No]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>個 は<span class=\"dmg\">入札者無しで流れました。</span>");
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

}
