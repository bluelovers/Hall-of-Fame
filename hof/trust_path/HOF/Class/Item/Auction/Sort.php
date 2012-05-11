<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Item_Auction_Sort
{

	/**
	 * アイテムを番号順に並び替える
	 */
	function sortByNo($a, $b)
	{
		if ($a["No"] == $b["No"]) return 0;
		return ($a["No"] > $b["No"]) ? 1 : -1;
	}

	/**
	 * アイテムを残り時間順に並び変える
	 */
	function sortByTime($a, $b)
	{
		if ($a["end"] == $b["end"]) return 0;
		return ($a["end"] > $b["end"]) ? 1 : -1;
	}

	/**
	 * アイテムを価格順に並び替える
	 */
	function sortByPrice($a, $b)
	{
		if ($a["price"] == $b["price"]) return 0;
		return ($a["price"] > $b["price"]) ? -1 : 1;
	}

	/**
	 * アイテムを価格順に並び替える(ぎゃく)
	 */
	function sortByRPrice($a, $b)
	{
		if ($a["price"] == $b["price"]) return 0;
		return ($a["price"] > $b["price"]) ? 1 : -1;
	}

	/**
	 * アイテムを入札数順に並び替える
	 */
	function sortByTotalBid($a, $b)
	{
		if ($a["TotalBid"] == $b["TotalBid"]) return 0;
		return ($a["TotalBid"] > $b["TotalBid"]) ? -1 : 1;
	}

}
