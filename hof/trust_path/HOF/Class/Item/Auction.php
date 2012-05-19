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

	var $user;

	/**
	 * オークションの種類
	 * アイテムorキャラ
	 */
	var $AuctionType;

	/**
	 * 競売品番号
	 */
	var $last_article_no;

	/**
	 * 出品物(キャラ)リスト
	 */
	var $article_list = array();

	var $UserName;

	/**
	 * 落札物や落札金の処理用
	 */
	var $temp_user = array();

	/**
	 * 経過ログ
	 */
	var $AuctionLog;

	/**
	 * データが変更されたか?
	 */
	protected $changed = array();

	var $QUERY;
	var $sort;

	var $file = AUCTION_ITEM;

	var $exhibit_cost = 500;

	var $exhibit_time = array(1, 3, 6, 12, 18, 24);

	static $options = array(
		'ip_check' => false,
	);

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
		if (1 || file_exists($this->file))
		{
			//$fp	= fopen($this->file,"r+");
			$this->fp = HOF_Class_File::fplock_file($this->file, 0, 1);
			if (!$this->fp) return false;
			//flock($fp, LOCK_EX);

			/*
			// 競売番号を先読みする
			$this->last_article_no = trim(fgets($this->fp));
			while (!feof($this->fp))
			{
			$str = fgets($this->fp);
			if (!$str) continue;
			$article = explode("<>", $str);
			if (strlen($article["1"]) != 10) continue;
			$this->article_list[$article["0"]] = array(
			"no" => $article["0"], // 競売番号
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

			$this->last_article_no = (int)$data['no'];
			$this->article_list = (array)$data['list'];

			foreach ($this->article_list as $k => $v)
			{
				if ($this->last_article_no >= $k)
				{
					break;
				}

				$this->last_article_no = max($this->last_article_no, $k);
			}

			// ファイルが無い場合
		}
		else
		{
			// 何もしない。
		}
	}

	function fpclose()
	{
		HOF_Class_File::fpclose($this->fp);
		unset($this->fp);
	}

	/**
	 * オークションのデータを保存する
	 */
	function fpsave()
	{
		if (!$this->changed['data'])
		{
			$this->fpclose();

			return false;
		}

		$data = array();

		$data['no'] = $this->last_article_no;

		// アイテム オークションを保存する。
		//$string = $this->last_article_no . "\n";
		foreach ($this->article_list as $val)
		{
			//if(strlen($val["end"]) != 10) continue;
			//$string .= $val["no"] . "<>" . $val["end"] . "<>" . $val["price"] . "<>" . $val["exhibitor"] . "<>" . $val["item"] . "<>" . $val["amount"] . "<>" . $val["TotalBid"] . "<>" . $val["bidder"] . "<>" . $val["latest"] . "<>" . $val["comment"] . "<>" . $val["IP"] . "\n";

			$data['list'][$val['no']] = $val;
		}

		$string = HOF_Class_Yaml::dump($data);

		//print($string);
		if (file_exists($this->file) && $this->fp)
		{
			HOF_Class_File::fpwrite_file($this->fp, $string, true);

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
		//debug($no, $this->article_list);

		if ($this->article_list[$no])
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
	function article_form_query($name)
	{
		$this->QUERY = $name;
	}

	/**
	 * 出品物の数
	 */
	function article_count()
	{
		return count($this->article_list);
	}

	function article_count_check_max()
	{
		if (AUCTION_MAX <= $this->article_count())
		{
			$msg = "出品数が限界に達しています。(" . $this->ItemAuction->article_count() . "/" . AUCTION_MAX . ")";
		}

		return $msg;
	}

	function article_item_sortby($type)
	{
		if (empty($type))
		{
			$type = 'time';
		}

		$key = $type;

		if ($type == 'rprice')
		{
			$key = 'price';
		}
		elseif ($type == 'price')
		{
			$desc = true;
		}
		elseif ($type == 'bid')
		{
			$key = 'TotalBid';
			$desc = true;
		}
		elseif ($type == 'time')
		{
			$key = 'end';
		}


		usort($this->article_list, HOF_Class_Array_Comparer_MuliteSubKey::newInstance($key)->sort_desc($desc)->callback());

		$this->sort = $type;
	}

	/**
	 * 最低価格を返す
	 * 価格の5%が最低値。
	 * 100以下なら100がそれになる。
	 */
	function article_price_bid_min($price)
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
	function article_item_price_bid_min($article_no)
	{
		if ($this->article_list["$article_no"])
		{
			return $this->article_price_bid_min($this->article_list["$article_no"]["price"]);
		}
	}
	/**
	 * 入札する
	 */
	function article_item_bid($article_no, $BidPrice, $Bidder, $BidderName)
	{
		if (!$article_list = $this->article_list["$article_no"]) return false;

		$article_price_bid_min = $this->article_price_bid_min($this->article_list["$article_no"]["price"]);

		// IPが同じ
		if (self::$options['ip_check'] && $article_list["IP"] == HOF::ip())
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
		if ($BidPrice < $article_price_bid_min) return false;

		// 誰かが入札していた場合お金を返金する。
		if ($article_list["bidder"])
		{
			$this->tmpuser_get_money($article_list["bidder"], $article_list["price"]);
			$this->save_user();
		}

		// 入札時間が残り少ないなら延長する。
		$Now = time();
		$left = $this->article_time_left($Now, $article_list["end"], true);
		/* // 残り時間1時間以下なら15分延長する
		if(1 < $left && $left < 3601) {
		$this->article_list["$article_no"]["end"]	+= 900;
		}
		*/

		// 残り時間15分以下なら 15分にする。
		if (0 < $left && $left < 901)
		{
			$dif = 900 - $left;
			$this->article_list["$article_no"]["end"] += $dif;
		}

		$this->article_list["$article_no"]["price"] = $BidPrice;
		$this->article_list["$article_no"]["TotalBid"]++;
		$this->article_list["$article_no"]["bidder"] = $Bidder;
		$this->changed['data']++;

		$this->changed['bid']++;

		$item = HOF_Model_Data::getItemData($article_list["item"]);
		//$this->log->add("no.".$article_list["no"]." <span class=\"bold\">{$item[name]} x{$article_list[amount]}</span>個に ".HOF_Helper_Global::MoneyFormat($BidPrice)." で ".$this->tmpuser_get_name($Bidder)." が<span class=\"support\">入札しました。</span>");
		$this->log->add("no." . $article_list["no"] . " <span class=\"bold\">{$item[name]} x{$article_list[amount]}</span>個に " . HOF_Helper_Global::MoneyFormat($BidPrice) . " で " . $BidderName . " が<span class=\"support\">入札しました。</span>");

		return true;
	}

	/**
	 * 残り時間を返す
	 */
	function article_time_left($now, $end, $int = false)
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
	function article_item_check_success()
	{
		$Now = time();
		foreach ($this->article_list as $no => $article_list)
		{
			// 競売時間が残っているなら次
			if ($this->article_time_left($Now, $article_list["end"])) continue;

			$item = HOF_Model_Data::getItemData($article_list["item"]);
			if ($article_list["bidder"])
			{
				// 落札者がいるならアイテムを渡す。
				// 落札者がいるなら出品者に金を渡す。
				$this->tmpuser_get_item($article_list["bidder"], $article_list["item"], $article_list["amount"]);
				$this->tmpuser_get_money($article_list["exhibitor"], $article_list["price"]);
				// 結果をログに残せ
				$this->log->add("no.{$article_list[no]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM) . "\"><span class=\"bold\">{$item[name]} x{$article_list[amount]}</span>個 を " . $this->tmpuser_get_name_temp($article_list["bidder"]) . " が " . HOF_Helper_Global::MoneyFormat($article_list["price"]) . " で<span class=\"recover\">落札しました。</span>");
			}
			else
			{
				// 入札が無かった場合、出品者に返却。
				$this->tmpuser_get_item($article_list["exhibitor"], $article_list["item"], $article_list["amount"]);
				// 結果をログに残せ
				$this->log->add("no.{$article_list[no]} <img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM) . "\"><span class=\"bold\">{$item[name]} x{$article_list[amount]}</span>個 は<span class=\"dmg\">入札者無しで流れました。</span>");
			}
			// 最後に消す
			unset($this->article_list["$no"]);
			$this->changed['data']++;
		}
	}

	/**
	 * 出品物一覧を表示する(その2) 表示の並びが違うだけ
	 */
	function article_item_show($bidding = false)
	{

		$this->output->article_list = array();

		$this->output->bidding = $bidding;

		$this->output->article_count = $this->article_count();
		$this->output->sort = $this->sort;

		$this->output->{"style_" . $this->sort} = ' class="a0"';

		$this->output->query = '?menu=' . $this->QUERY;

		if ($this->article_list)
		{
			$Now = time();

			foreach ($this->article_list as $article_list)
			{

				$article_list['bidder_name'] = $article_list["bidder"] ? $this->tmpuser_get_name($article_list["bidder"]) : '-';
				$article_list['exhibitor_name'] = $this->tmpuser_get_name($article_list["exhibitor"]);

				$article_list['end_left'] = $this->article_time_left($Now, $article_list["end"]);

				$article_list['item_show'] = HOF_Class_Item::newInstance($article_list["item"])->html($article_list["amount"], 1);

				$article_list['price_bid_min'] = $this->article_price_bid_min($article_list["price"]);

				$this->output->article_list[] = $article_list;
			}
		}

		HOF_Class_View::render(null, $this->output, 'layout/item/auction.article.item')->output();

		return !empty($this->article_list);
	}

	function tmpuser_get_name_temp($UserID)
	{
		if ($this->temp_user["$UserID"]["Name"])
		{
			return $this->temp_user["$UserID"]["Name"];
		}

		return "-";
	}

	function &tmpuset_get($UserID)
	{
		if (!$this->temp_user["$UserID"]["user"])
		{
			$this->temp_user["$UserID"]["user"] = new HOF_Class_User($UserID);
			$this->temp_user["$UserID"]["Name"] = $this->temp_user["$UserID"]["user"]->Name();
		}

		return $this->temp_user["$UserID"];
	}

	/**
	 * オークションでお金を獲得
	 */
	function tmpuser_get_money($UserID, $Money)
	{
		$user = $this->tmpuset_get($UserID);

		$user["UserGetTotalMoney"] += $Money;

		/**
		 * 金が追加されたことを記録
		 */
		$user["Money"] = true;
	}

	/**
	 * オークションでアイテム獲得
	 */
	function tmpuser_get_item($UserID, $item, $amount)
	{
		$user = $this->tmpuset_get($UserID);

		$user["user_get_item"]["$item"] += $amount;

		/**
		 * アイテムが追加されたことを記録
		 */
		$user["item"] = true;
	}

	/**
	 * オークション処理結果を清算する?
	 */
	function save_user()
	{
		foreach ($this->temp_user as $userid => &$user)
		{
			/**
			 * お金を得た
			 */
			if ($user["Money"])
			{
				$user["user"]->GetMoney($user["UserGetTotalMoney"]);
				$user["user"]->SaveData();
			}

			/**
			 * アイテムを得た
			 */
			if ($user["item"])
			{
				foreach ($user["user_get_item"] as $itemNo => $amount)
				{
					$user["user"]->AddItem($itemNo, $amount);
				}
				$user["user"]->SaveUserItem();
			}

			/**
			 * キャラクターを得た
			 * (動作確認なし)
			 */
			if ($user["CharAdd"])
			{
				if ($user["char"])
				{
					foreach ($user["char"] as $char)
					{
						$char->SaveCharData($user);
					}
				}
			}

			/**
			 * ユーザが開いた全てのファイルのファイルポインタを閉じる
			 */
			$user["user"]->fpclose_all();
		}
		unset($this->temp_user);
	}

	/**
	 * 入札する権利があるかどうか返す
	 */
	function article_item_bid_right($article_no, $UserID)
	{
		if ($this->article_list["$article_no"]["bidder"] == $UserID) return false;
		if ($this->article_list["$article_no"]["exhibitor"] == $UserID) return false;
		return true;
	}

	/**
	 * ユーザの名前を呼び出す
	 */
	function tmpuser_get_name($id)
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
	 * アイテムを出品する
	 */
	function article_item_add($item, $amount, $id, $time, $StartPrice, $comment)
	{
		// 終了時刻の計算
		$Now = time();
		$end = $Now + round((60 * 60 * $time));

		// 開始価格のあれ
		$price = max(0, intval($StartPrice));

		// コメント処理
		$comment = str_replace("\t", "", $comment);
		$comment = htmlspecialchars(trim($comment), ENT_QUOTES);
		$comment = stripslashes($comment);

		// 競売品番号
		$this->last_article_no++;
		if (9999 < $this->last_article_no) $this->last_article_no = 0;

		$New = array(
			// 競売品番号
			"no" => $this->last_article_no,
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
			"IP" => HOF::ip(),
			);

		array_unshift($this->article_list, $New);
		$itemData = HOF_Model_Data::getItemData($item);

		$this->log->add("no." . $this->last_article_no . " に <img src=\"" . HOF_Class_Icon::getImageUrl($itemData["img"], HOF_Class_Icon::IMG_ITEM) . "\"><span class=\"bold\">{$itemData[name]} x{$amount}</span>個が<span class=\"charge\">出品されました。</span>");

		$this->changed['data']++;
		$this->changed['add']++;
	}

	function &user($user = null)
	{
		if ($user !== null)
		{
			$this->user = $user;
		}

		if (!isset($this->user))
		{
			throw new RuntimeException('Auction user not exists!');
		}

		return $this->user;
	}

	function article_exhibit_cost($user = null)
	{
		return $this->exhibit_cost;
	}

	/**
	 * 出品時間の確認
	 */
	function article_exhibit_time_check($time)
	{
		if (!in_array($time, $this->exhibit_time))
		{
			$msg = "time?";
		}

		return $msg;
	}

	function article_exhibit_item_check($item)
	{
		/**
		 * そのアイテムが出品できない場合
		 */
		$possible = HOF_Model_Data::getCanExhibitType();

		if (!$possible[$item['type']])
		{
			$msg = "Cant put < {$item[name]} > to the Auction";
		}

		return $msg;
	}

	function article_exhibit_price_item_min($item, $user = null)
	{
		$min = max(1, $item['buy'] * 0.2);

		return $min;
	}

	function article_exhibit_price_check($price, $item)
	{
		$price = max(0, intval($price));

		$min = $this->article_exhibit_price_item_min($item, $this->user());

		if ($price < $min)
		{
			$msg = "< {$item[name]} > 出品価格 ( ".HOF_Helper_Global::MoneyFormat($price).' < '.HOF_Helper_Global::MoneyFormat($min).' )  に誤りがあります。';
		}

		return $msg;
	}

	/**
	 * return msg = fail
	 * return null = true
	 */
	function user_take_exhibit_cost()
	{
		$cost = $this->article_exhibit_cost($this->user());

		if (!$this->user()->TakeMoney($cost))
		{
			$msg = "Need " . HOF_Helper_Global::MoneyFormat($cost) . " to exhibit auction.";
		}

		return $msg;
	}

}
