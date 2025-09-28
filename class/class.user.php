<?php

/**
 * ユーザークラス
 * User class handling user data and related operations.
 */
class user {

	// ファイルポインタ
	var $fp;
	var $file;

	var $id, $pass;
	var $name, $last, $login, $start;
	var $money;
	var $char;
	var $time;
	var $wtime; // 總消費時間
	var $ip; // IPアドレス

	var $party_memo;
	var $party_rank; // ランキング用のパ一ティ
	var $rank_set_time; // ランキングPT設定した時間
	var $rank_btl_time; // 次のランク戦に挑戦できる時間
	// ランキングの成績
	// = "總戰鬥回數<>勝利數<>敗北數<>引き分け<>首位防衛";
	var $rank_record;
	var $union_btl_time; // 次のUnion戦に挑戦できる時間

	// OPTION
	var $record_btl_log;
	var $no_JS_itemlist;
	var $UserColor;

	// ユ一ザ一アイテム用の変数
	var $fp_item;
	var $item;

	/**
	 * ユーザークラスコンストラクタ
	 * User class constructor to initialize user data.
	 *
	 * @param int $id User ID
	 * @param bool $noExit Prevent file exit on error
	 */
	function user($id, $noExit = false) {
		if ($id) {
			$this->id = $id;
			if ($data = $this->LoadData($noExit)) {
				$this->DataUpDate($data); // timeとか増やす
				$this->SetData($data);
			}
		}
	}

	/**
	 * IPを変更
	 * Set user IP address.
	 *
	 * @param string $ip New IP address
	 */
	function SetIp($ip) {
		$this->ip = $ip;
	}

	/**
	 * ユーザーデータを読み込む
	 * Load user data from file.
	 *
	 * @param bool $noExit Prevent file exit on error
	 * @return mixed User data or false if file does not exist
	 */
	function LoadData($noExit = false) {
		$file = USER . $this->id . "/" . DATA;
		if (file_exists($file)) {
			$this->file = $file;
			$this->fp = FileLock($file, $noExit);
			if (!$this->fp)
				return false;
			$data = ParseFileFP($this->fp);
			// $data = ParseFile($file); // (2007/7/30 追加)
			/*
			$Array = array("party_memo", "party_rank");
			foreach ($Array as $val) {
				if (!$data["$val"]) continue;
				$data["$val"] = explode("<>", $data["$val"]);
			}
			*/
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * IDが結局のところ存在しているかどうか確認
	 * Check if the user exists by checking the name.
	 *
	 * @return bool True if user exists, false otherwise
	 */
	function is_exist() {
		if ($this->name)
			return true;
		else
			return false;
	}

	/**
	 * 名前を返す
	 * Return user's name with optional HTML class.
	 *
	 * @param mixed $opt Optional HTML class for the name
	 * @return mixed User's name or false if not set
	 */
	function Name($opt = false) {
		if ($this->name) {
			if ($opt)
				return '<span class="' . $opt . '">' . $this->name . '</span>';
			else
				return $this->name;
		} else {
			return false;
		}
	}

	/**
	 * 名前を変更
	 * Change user's name.
	 *
	 * @param string $name New name for the user
	 * @return bool True if name changed, false otherwise
	 */
	function ChangeName($name) {
		if ($this->name == $name)
			return false;
		$this->name = $name;
		return true;
	}

	/**
	 * Union戦闘した時間をセット
	 * Set the time for Union battle.
	 */
	function UnionSetTime() {
		$this->union_btl_time = time();
	}

//////////////////////////////////////////////////
//	UnionBattleができるかどうか確認する。
	function CanUnionBattle() {
		$Now	= time();
		$Past	= $this->union_btl_time	+ UNION_BATTLE_NEXT;
		if($Past <= $Now) {
			return true;
		} else {
			return abs($Now - $Past);
		}
	}
//////////////////////////////////////////////////
//	ランキング戰用のパ一ティ編成を返す
	function RankParty() {
		if(!$this->name)
			return "NOID";//超エラ一。そもそもユ一ザ一が存在しない場合。
		if(!$this->party_rank)
			return false;

		$PartyRank	= explode("<>",$this->party_rank);
		foreach($PartyRank as $no) {
			$char	= $this->CharDataLoad($no);
			if($char)
				$party[]	= $char;
			//if($this->char[$no])
			//	$party[]	= $this->char[$no];
		}

		if($party)
			return $party;
		else
			return false;
	}
//////////////////////////////////////////////////
//	ランキングの成績
// side = ("CHALLENGE","DEFEND")
	function RankRecord($result,$side,$DefendMatch) {
		$record	= $this->RankRecordLoad();

		$record["all"]++;
		switch(true) {
			// 引き分け
			/*
			case ($result === "d"):
				if($side != "CHALLENGE" && $DefendMatch)
					$record["defend"]++;
				break;
			*/
			// 戰鬥結果が挑戰者の勝ち
			case ($result === 0):
				if($side == "CHALLENGER") {
					$record["win"]++;
				} else {
					$record["lose"]++;
				}
				break;
			// 戰鬥結果が挑戰者の負け
			case ($result === 1):
				if($side == "CHALLENGER") {
					$record["lose"]++;
				} else {
					$record["win"]++;
					if($DefendMatch)
						$record["defend"]++;
				}
				break;
			default:// 引き分け
				if($side != "CHALLENGER" && $DefendMatch)
					$record["defend"]++;
				break;
		}

		$this->rank_record	= $record["all"]."|".$record["win"]."|".$record["lose"]."|".$record["defend"];
	}
//////////////////////////////////////////////////
//	ランキング戰の成績を呼び出す
	function RankRecordLoad() {

		if(!$this->rank_record) {
			$record	= array(
						"all" => 0,
						"win" => 0,
						"lose" => 0,
						"defend" => 0,
						);
			return $record;
		}

		list(
			$record["all"],
			$record["win"],
			$record["lose"],
			$record["defend"],
		)	= explode("|",$this->rank_record);
		return $record;
	}
//////////////////////////////////////////////////
//	次のランク戰に挑戰できる時間を記錄する。
	function SetRankBattleTime($time) {
		$this->rank_btl_time	= $time;
	}

	/**
	 * UnionBattleができるかどうか確認する。
	 * Check if user can perform a Union battle.
	 *
	 * @return mixed True if able to battle, remaining time otherwise
	 */
	function CanRankBattle() {
		$now = time();
		if ($this->rank_btl_time <= $now) {
			return true;
		} else if (!$this->rank_btl_time) {
			return true;
		} else {
			$left = $this->rank_btl_time - $now;
			$hour = floor($left / 3600);
			$minutes = floor(($left % 3600) / 60);
			$seconds = floor(($left % 3600) % 60);
			return array($hour, $minutes, $seconds);
		}
	}

//////////////////////////////////////////////////
//	お金を增やす
	function GetMoney($no) {
		$this->money	+= $no;
	}

//////////////////////////////////////////////////
//	お金を減らす
	function TakeMoney($no) {
		if($this->money < $no) {
			return false;
		} else {
			$this->money	-= $no;
			return true;
		}
	}

	/**
	 * 時間を消費する(總消費時間の加算)
	 * Waste time from user's available time.
	 *
	 * @param int $time Time to be wasted
	 * @return bool True if successful, false otherwise
	 */
	function WasteTime($time) {
		if ($this->time < $time)
			return false;
		$this->time -= $time;
		$this->wtime += $time;
		return true;
	}

	/**
	 * キャラクターを所持している数をカウントする。
	 * Count the number of characters owned by the user.
	 *
	 * @return int Number of characters
	 */
	function CharCount() {
		$dir = USER . $this->id;
		$no = 0;
		foreach (glob("$dir/*") as $adr) {
			$number = basename($adr, ".dat");
			if (is_numeric($number)) { // キャラクターデータファイル
				$no++;
			}
		}
		return $no;
	}

	/**
	 * 全所持キャラクターをファイルから読み込んで $this->char に格納。
	 * Load all owned characters from files into $this->char.
	 */
	function CharDataLoadAll() {
		$dir = USER . $this->id;
		$this->char = array(); // 配列の初期化
		foreach (glob("$dir/*") as $adr) {
			$number = basename($adr, ".dat");
			if (is_numeric($number)) { // キャラクターデータファイル
				$this->char[$number] = new char($adr);
				$this->char[$number]->SetUser($this->id); // キャラクターが誰かを設定する
			}
		}
	}

	/**
	 * 指定の所持キャラクターをファイルから読み込んで $this->char に格納後 "返す"。
	 * Load a specific character from file into $this->char and return it.
	 *
	 * @param int $CharNo Character number
	 * @return mixed Loaded character or false if not found
	 */
	function CharDataLoad($CharNo) {
		// 既に読み込んだ場合。
		if ($this->char[$CharNo])
			return $this->char[$CharNo];
		// 読み込んでいない場合。
		$file = USER . $this->id . "/" . $CharNo . ".dat";
		// そんなキャラクターがない場合。
		if (!file_exists($file))
			return false;
		// 存在する場合。
		$this->char[$CharNo] = new char($file);
		$this->char[$CharNo]->SetUser($this->id); // キャラクターが誰かを設定する
		return $this->char[$CharNo];
	}

	/**
	 * アイテムを追加
	 * Add item to user's inventory.
	 *
	 * @param int $no Item number
	 * @param mixed $amount Optional amount of the item to add
	 */
	function AddItem($no, $amount = false) {
		if (!isset($this->item)) // どうしたもんか…
			$this->LoadUserItem();
		if ($amount)
			$this->item[$no] += $amount;
		else
			$this->item[$no]++;
	}

	/**
	 * アイテムを削除
	 * Remove item from user's inventory.
	 *
	 * @param int $no Item number
	 * @param mixed $amount Optional amount of the item to remove
	 * @return int Amount removed
	 */
	function DeleteItem($no, $amount = false) {
		if (!isset($this->item)) // どうしたもんか…
			$this->LoadUserItem();

		// 減らす数。
		if ($this->item[$no] < $amount) {
			$amount = $this->item[$no];
			if (!$amount)
				$amount = 0;
		}
		if (!is_numeric($amount))
			$amount = 1;

		// 減らす。
		$this->item[$no] -= $amount;
		if ($this->item[$no] < 1)
			unset($this->item[$no]);

		return $amount;
	}

	/**
	 * アイテムデータを読み込む
	 * Load user's item data.
	 */
	function LoadUserItem() {
		// 2重に読み込むのを防ぐ。
		if (isset($this->item))
			return false;

		$file = USER . $this->id . "/" . ITEM;

		if (file_exists($file)) {
			$this->fp_item = FileLock($file);
			$this->item = ParseFileFP($this->fp_item);
			if ($this->item === false)
				$this->item = array();
		} else {
			$this->item = array();
		}
	}

	/**
	 * アイテムデータを保存する
	 * Save user's item data.
	 *
	 * @return bool True if successful, false otherwise
	 */
	function SaveUserItem() {
		$dir = USER . $this->id;
		if (!file_exists($dir))
			return false;

		$file = USER . $this->id . "/" . ITEM;

		if (!is_array($this->item))
			return false;

		// アイテムのソート
		ksort($this->item, SORT_STRING);

		foreach ($this->item as $key => $val) {
			$text .= "$key=$val\n";
		}

		if (file_exists($file) && $this->fp_item) {
			WriteFileFP($this->fp_item, $text, 1); // $textが空でも保存する
			fclose($this->fp_item);
			unset($this->fp_item);
		} else {
			// $textが空でも保存する
			WriteFile($file, $text, 1);
		}
	}

	/**
	 * 時間を経過させる。(Timeの増加)
	 * Update user's time data.
	 *
	 * @param array &$data Reference to user data
	 */
	function DataUpDate(&$data) {
		$now = time();
		$diff = $now - $data["last"];
		$data["last"] = $now;
		$gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
		$data["time"] += $gain;
		if (MAX_TIME < $data["time"])
			$data["time"] = MAX_TIME;
	}

	/**
	 * データをセットする。
	 * Set user data from an array.
	 *
	 * @param array &$data Reference to user data
	 */
	function SetData(&$data) {
		foreach ($data as $key => $val) {
			$this->{$key} = $val;
		}
	}

	/**
	 * パスワードを暗号化する
	 * Encrypt user's password.
	 *
	 * @param string $pass User's password
	 * @return string Encrypted password
	 */
	function CryptPassword($pass) {
		return substr(crypt($pass, CRYPT_KEY), strlen(CRYPT_KEY));
	}

	/**
	 * 名前を消す
	 * Delete user's name.
	 */
	function DeleteName() {
		$this->name = NULL;
	}

	/**
	 * データを保存する形式に変換する。(テキスト)
	 * Convert user data to a text format for saving.
	 *
	 * @return string Formatted user data
	 */
	function DataSavingFormat() {
		$Save = array(
			"id",
			"pass",
			"ip",
			"name",
			"last",
			"login",
			"start",
			"money",
			"time",
			"wtime",
			"party_memo",
			"party_rank",
			"rank_set_time",
			"rank_btl_time",
			"rank_record",
			"union_btl_time",
			// opt
			"record_btl_log",
			"no_JS_itemlist",
			"UserColor",
		);
		foreach ($Save as $val) {
			if ($this->{$val})
				$text .= "$val=" . (is_array($this->{$val}) ? implode("<>", $this->{$val}) : $this->{$val}) . "\n";
		}
		return $text;
	}

	/**
	 * データを保存する
	 * Save user data to file.
	 *
	 * @return bool True if successful, false otherwise
	 */
	function SaveData() {
		$dir = USER . $this->id;
		$file = USER . $this->id . "/" . DATA;

		if (file_exists($this->file) && $this->fp) {
			WriteFileFP($this->fp, $this->DataSavingFormat());
			fclose($this->fp);
			unset($this->fp);
		} else {
			if (file_exists($file))
				WriteFile($file, $this->DataSavingFormat());
		}
	}

	/**
	 * データファイル兼キャラクターファイルのファイルポインタも全部閉じる
	 * Close all file pointers used by user data.
	 */
	function fpCloseAll() {
		if (is_resource($this->fp)) {
			fclose($this->fp);
			unset($this->fp);
		}
		if (is_resource($this->fp_item)) {
			fclose($this->fp_item);
			unset($this->fp_item);
		}
		if ($this->char) {
			foreach ($this->char as $key => $var) {
				if (method_exists($this->char[$key], "fpclose"))
					$this->char[$key]->fpclose();
			}
		}
	}

	/**
	 * ユーザーの削除(全ファイル)
	 * Delete user and all related files.
	 *
	 * @param bool $DeleteFromRank True to delete from ranking
	 */
	function DeleteUser($DeleteFromRank = true) {
		if ($DeleteFromRank) {
			include_once(CLASS_RANKING);
			$Ranking = new Ranking();
			if ($Ranking->DeleteRank($this->id))
				$Ranking->SaveRanking();
		}
		$dir = USER . $this->id;
		$files = glob("$dir/*");
		$this->fpCloseAll();
		foreach ($files as $val)
			unlink($val);
		rmdir($dir);
	}

	/**
	 * 放棄されているかどうか確かめる
	 * Check if the user is abandoned.
	 *
	 * @return bool True if abandoned, false otherwise
	 */
	function IsAbandoned() {
		$now = time();
		if (strlen($this->login) !== 10)
			return false;
		if (($this->login + ABANDONED) < $now)
			return true;
		else
			return false;
	}

	/**
	 * キャラクターデータを消す
	 * Delete a character's data.
	 *
	 * @param int $no Character number
	 */
	function DeleteChar($no) {
		$file = USER . $this->id . "/" . $no . ".dat";
		if ($this->char[$no]) {
			$this->char[$no]->fpclose();
		}
		if (file_exists($file))
			unlink($file);
	}

}

?>