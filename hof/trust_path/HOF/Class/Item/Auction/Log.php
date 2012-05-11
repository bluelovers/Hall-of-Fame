<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Item_Auction_Log
{

	public $AuctionLog;
	public $AuctionType;

	function __construct(&$parent)
	{
		$this->parent = &$parent;

		$this->AuctionType = &$parent->AuctionType;
	}

	/**
	 * オークション経過ログを読む
	 */
	function load()
	{
		$this->AuctionLog = array();

		if ($this->AuctionType == "item")
		{
			if (!file_exists(AUCTION_ITEM_LOG))
			{
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

		debug($this->AuctionLog);
	}

	/**
	 * オークション経過ログの保存
	 */
	function save()
	{
		if ($parent->AuctionType == "item")
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

	function get()
	{
		if (!isset($this->AuctionLog)) {
			$this->load();
		}

		return $this->AuctionLog;
	}

	/**
	 * ログの追加
	 */
	function add($string)
	{
		$this->get();

		array_unshift($this->AuctionLog, $string);
	}
}
