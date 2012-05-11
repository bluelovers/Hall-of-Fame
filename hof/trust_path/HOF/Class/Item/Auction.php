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


}
