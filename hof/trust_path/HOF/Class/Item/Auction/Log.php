<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Item_Auction_Log
{

	protected $AuctionLog;
	protected $AuctionType;

	protected $fp;
	protected $file = AUCTION_ITEM_LOG;

	function __construct(&$parent)
	{
		$this->parent = &$parent;

		$this->AuctionType = &$parent->AuctionType;
	}

	function __destruct()
	{
		$this->close();
	}

	/**
	 * オークション経過ログを読む
	 */
	function load()
	{
		$this->AuctionLog = array();

		if ($this->AuctionType == "item")
		{
			$this->fp = HOF_Class_File::fplock_file($this->file, 0, true);

			while (!feof($this->fp))
			{
				$str = trim(fgets($this->fp));
				if (!$str) continue;
				$this->AuctionLog[] = $str;
			}
		}
	}

	/**
	 * オークション経過ログの保存
	 */
	function save()
	{
		if ($parent->AuctionType == "item")
		{
			if (!empty($this->AuctionLog)) return false;

			// 30行以下に収める
			while (100 < count($this->AuctionLog))
			{
				array_pop($this->AuctionLog);
			}

			foreach ($this->AuctionLog as $log)
			{
				$string .= $log . "\n";
			}

			HOF_Class_File::fpwrite_file($this->fp, $string);
		}
	}

	function close()
	{
		@fclose($this->fp);
		unset($this->fp);
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
