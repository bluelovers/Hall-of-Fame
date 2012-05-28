<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_USER);

//class HOF_Class_User extends user
class HOF_Class_User
{

	protected static $instance_user;

	/**
	 * ファイルポインタ
	 */
	var $fp;
	var $file;

	var $id, $pass;
	var $name, $last, $login, $start;
	var $money;
	var $char;
	var $time;

	/**
	 * 総消費時間
	 */
	var $wtime;

	/**
	 * IPアドレス
	 */
	var $ip;

	var $party_memo;

	/**
	 * ランキング用のパーティ
	 */
	var $party_rank;

	/**
	 * ランキングPT設定した時間
	 */
	var $rank_set_time;

	/**
	 * 次のランク戦に挑戦できる時間
	 */
	var $rank_btl_time;
	/**
	 * ランキングの成績
	 * = "総戦闘回数<>勝利数<>敗北数<>引き分け<>首位防衛";
	 */
	var $rank_record;

	/**
	 * 次のUnion戦に挑戦できる時間
	 */
	var $union_btl_time;

	/**
	 * OPTION
	 */
	/*
	var $record_btl_log;
	var $no_JS_itemlist;
	var $UserColor;
	*/

	/**
	 * ユーザーアイテム用の変数
	 */
	var $fp_item;
	//var $item;

	protected $_user_cache_;

	/**
	 * 対象のIDのユーザークラスを作成
	 */
	function __construct($id, $noExit = false)
	{
		if ($id)
		{
			$this->id = $id;
			if ($data = $this->LoadData($noExit))
			{
				$this->DataUpDate($data); //timeとか増やす
				$this->SetData($data);
			}

			self::$instance_user[$id] = $this;
		}
	}

	static function &getInstance($id, $noExit = false)
	{
		if ($id === HOF::user()->id)
		{
			return HOF::user();
		}

		if (isset(self::$instance_user[$id]))
		{
			return self::$instance_user[$id];
		}
		else
		{
			return new HOF_Class_User($id, $noExit);
		}
	}

	function &cache()
	{
		if (!$this->id) return false;

		if (!isset($this->_user_cache_))
		{
			$this->_user_cache_ = new HOF_Class_File_Cache(array(
				'path' => BASE_PATH_CACHE.'user/'.$this->id.'/',
				'timeout' => 3600,
			));
		}

		return $this->_user_cache_;
	}

	function __toString()
	{
		$val = (string )$this->id;

		return $val;
	}

	function __destruct()
	{
		if ($this->id)
		{
			$this->cache()->__destruct();

			$this->cache()->__destruct();
		}

		$this->fpclose_all();

		self::$instance_user[$this->id] = null;
	}

	/**
	 * 時間を経過させる。(Timeの増加)
	 */
	function DataUpDate(&$data)
	{
		$now = time();
		$diff = $now - $data['timestamp']["last"];
		$data['timestamp']["last"] = $now;
		$gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
		$data["time"] += (int)$gain;
		if (MAX_TIME < $data["time"]) $data["time"] = MAX_TIME;
	}

	/**
	 * ランキング戦用のパーティ編成を返す
	 */
	function RankParty()
	{
		if ($this->is_exist() && !empty($this->party_rank))
		{
			$party = array();

			foreach ($this->party_rank as $no)
			{
				$char = $this->char($no);
				if ($char)
				{
					$party[] = $char;
				}
			}

			if (!empty($party))
			{
				return $party;
			}
		}

		return false;
	}

	/**
	 * IDが結局のところ存在しているかたしかめる
	 */
	function is_exist()
	{
		return (!$this->id || !isset($this->name) || empty($this->name)) ? false : true;
	}

	function char_list($over = null)
	{
		if (!$over && $list = $this->cache()->data('char_list'))
		{
			return $list;
		}

		$list = array();

		if ($list_char = HOF_Helper_Char::char_list_by_user($this))
		{
			foreach ($list_char as $no => $file)
			{
				$char = HOF_Model_Char::newCharFromFile($file);

				$list[$no] = $char->name;
			}
		}

		$this->cache()->data('char_list', $list);

		$this->cache()->save('char_list');

		return $list;
	}

	/**
	 * 全所持キャラクターをファイルから読んで $this->char に格納
	 */
	function char_all()
	{
		//配列の初期化だけしておく
		$this->char = array();

		if ($list_char = $this->char_list())
		{
			foreach (array_keys($list_char) as $no)
			{
				$file = HOF_Helper_Char::char_file($no, $this->id);

				if (!file_exists($file)) continue;

				$this->char[$no] = HOF_Model_Char::newCharFromFile($file);
				$this->char[$no]->SetUser($this->id);
			}
		}

		return $this->char;
	}

	/**
	 * 指定の所持キャラクターをファイルから読んで $this->char に格納後 "返す"。
	 */
	function char($CharNo)
	{
		if ($this->char[$CharNo]) return $this->char[$CharNo];

		$file = HOF_Helper_Char::char_file($CharNo, $this);

		if (!file_exists($file)) return false;

		$this->char[$CharNo] = HOF_Model_Char::newCharFromFile($file);

		$this->char[$CharNo]->SetUser($this->id);

		$list = $this->cache()->data('char_list');

		$list[$CharNo] = $this->char[$CharNo]->name;

		$this->cache()->data('char_list', $list);

		return $this->char[$CharNo];
	}

	function &__get($k)
	{
		if ($k == 'item')
		{
			return $this->$k();
		}
	}

	/*
	function __isset($k)
	{
		return isset($this->$k);
	}
	*/

	/**
	 * アイテムデータを読む
	 */
	function &item($no = null)
	{
		/**
		 * 2重に読むのを防止。
		 */
		if (!isset($this->item) || $no === true)
		{
			$file = HOF_Helper_Char::user_file($this, USER_ITEM);

			$this->fp_item = HOF_Class_File::fplock_file($file, true, true);

			$this->item = HOF_Class_Yaml::load($this->fp_item);

			$this->item = (array)$this->item;
		}

		if ($no !== null && $no !== true && $no !== false)
		{
			return $this->item[$no];;
		}

		return $this->item;
	}

	/**
	 * アイテムデータを保存する
	 */
	function item_save()
	{
		if (!isset($this->item)) return false;

		$dir = HOF_Helper_Char::user_path($this);

		if (!is_dir($dir)) return false;

		// アイテムのソート
		ksort($this->item, SORT_STRING);

		foreach ($this->item = array_filter($this->item) as $k => $v)
		{
			if (!$k || !$v)
			{
				unset($this->item[$k]);
			}
		}

		$file = HOF_Helper_Char::user_file($this, USER_ITEM);
		HOF_Class_Yaml::save($this->fp_item ? $this->fp_item : $file, (array)$this->item);
		unset($this->fp_item);
	}

	/**
	 * ユーザデータを読む
	 */
	function LoadData($noExit = false)
	{
		$file = HOF_Helper_Char::user_file($this, USER_DATA);

		if (file_exists($file))
		{
			$this->cache();

			$this->file = $file;

			$this->fp = HOF_Class_File::fplock_file($file, $noExit);
			if (!$this->fp) return false;

			$data = HOF_Class_Yaml::load($this->fp);

			return $data;
		}
		else
		{
			return false;
		}
	}

	/**
	 * データを保存する
	 */
	function SaveData()
	{
		$this->cache()->__destruct();

		if (file_exists($this->file) && $this->fp)
		{
			HOF_Class_File::fpwrite_file($this->fp, $this->DataSavingFormat());

			fclose($this->fp);
			unset($this->fp);
		}
		else
		{
			$file = HOF_Helper_Char::user_file($this, USER_DATA);

			if (file_exists($file)) HOF_Class_File::WriteFile($file, $this->DataSavingFormat());
		}
	}

	/**
	 * データを保存する形式に変換する。(テキスト)
	 */
	function DataSavingFormat()
	{

		$Save = array(

			'uniqid',

			"id",
			"pass",
			"ip",
			"name",

			/*
			"last",
			"login",
			"start",
			*/
			'timestamp',
			'options',

			"money",
			"time",
			"wtime",
			"party_memo",
			"party_rank",
			"rank_set_time",
			"rank_btl_time",
			"rank_record",
			"union_btl_time",
			/*
			"record_btl_log",
			"no_JS_itemlist",
			"UserColor",
			*/
			);

		$data = array();

		foreach ($Save as $k)
		{
			if (!isset($this->{$k})) continue;

			$data[$k] = $this->{$k};
		}

		$text = HOF_Class_Yaml::dump($data);

		return $text;
	}

	/**
	 * データをセットする。
	 * ※?
	 */
	function SetData(&$data)
	{

		foreach ($data as $key => $val)
		{
			$this->{$key} = $val;
		}

		/*
		if (!is_array($this->party_memo))
		{
			$this->party_memo = explode("<>", $this->party_memo);
		}

		if (!is_array($this->party_rank))
		{
			$this->party_rank = explode("<>", $this->party_rank);
		}
		*/

		/*
		$this->name	= $data["name"];
		$this->login	= $data["login"];
		$this->last	= $data["last"];
		$this->start	= $data["start"];
		*/
	}

	/**
	 * ランキングの成績
	 * side = ("CHALLENGE","DEFEND")
	 */
	function RankRecord($result, $side, $DefendMatch)
	{
		$record = $this->RankRecordLoad();

		$record["all"]++;
		switch (true)
		{
				// 引き分け
				/*
				case ($result === "d"):
				if($side != "CHALLENGE" && $DefendMatch)
				$record["defend"]++;
				break;
				*/
				// 戦闘結果が挑戦者の勝ち
			case ($result === 0):
				if ($side == "CHALLENGER")
				{
					$record["win"]++;
				}
				else
				{
					$record["lose"]++;
				}
				break;
				// 戦闘結果が挑戦者の負け
			case ($result === 1):
				if ($side == "CHALLENGER")
				{
					$record["lose"]++;
				}
				else
				{
					$record["win"]++;
					if ($DefendMatch) $record["defend"]++;
				}
				break;
			default: // 引き分け
				if ($side != "CHALLENGER" && $DefendMatch) $record["defend"]++;
				break;
		}

		/*
		$this->rank_record = $record["all"] . "|" . $record["win"] . "|" . $record["lose"] . "|" . $record["defend"];
		*/
		$this->rank_record = $record;
	}

	/**
	 * ランキング戦の成績を呼び出す
	 */
	function RankRecordLoad()
	{

		if (!$this->rank_record)
		{
			$record = array(
				"all" => 0,
				"win" => 0,
				"lose" => 0,
				"defend" => 0,
				);
			return $record;
		}

		$record = $this->rank_record;

		return $record;
	}

	/**
	 * キャラデータを消す
	 */
	function char_delete($no)
	{
		$file = HOF_Helper_Char::char_file($no, $this->id);

		if ($this->char[$no])
		{
			$this->char[$no]->fpclose();
		}

		if (file_exists($file)) HOF_Class_File::unlink($file);

		$this->cache()->timeout('char_list', -1);
	}

	/**
	 * キャラクターを所持してる数をかぞえる。
	 */
	function char_count()
	{
		$list_char = $this->char_list(true);

		return count($list_char);
	}

	/**
	 * データファイル兼キャラファイルのファイルポインタも全部閉じる
	 */
	function fpclose_all()
	{
		// 基本データ
		HOF_Class_File::fpclose($this->fp);
		unset($this->fp);

		// アイテムデータ
		HOF_Class_File::fpclose($this->fp_item);
		unset($this->fp_item);


		// キャラデータ

		foreach ((array )$this->char as $key => $var)
		{
			if (method_exists($this->char[$key], "fpclose"))
			{
				$this->char[$key]->fpclose();
			}
		}

	}

	/**
	 * ユーザーの削除(全ファイル)
	 */
	function DeleteUser($DeleteFromRank = true)
	{
		//ランキングからまず消す。
		if ($DeleteFromRank)
		{
			$Ranking = new HOF_Class_Ranking();
			if ($Ranking->DeleteRank($this->id)) $Ranking->fpsave(1);
		}

		$this->fpclose_all();

		$dir = HOF_Helper_Char::user_path($this);

		HOF_Model_Main::addUserDelList($this->id, $this->name);

		HOF_Class_File::rmdir($dir, true);

		/*
		$files = glob($dir.'*');

		foreach ($files as $val)
		{
		unlink($val);
		}

		rmdir($dir);
		*/
	}

	/**
	 * 名前を変える。
	 */
	function ChangeName($new)
	{
		if ($this->name == $new) return false;

		$this->name = $new;

		HOF_Model_Main::addUserList($this->id, $new);

		return true;
	}

	/**
	 * 放棄されているかどうか確かめる
	 */
	function IsAbandoned()
	{
		$now = time();
		// $this->login がおかしければ終了する。
		if (strlen($this->timestamp['login']) !== 10)
		{
			return false;
		}
		if (($this->timestamp['login'] + ABANDONED) < $now)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * IPを変更
	 */
	function SetIp($ip)
	{
		$this->ip = $ip;
	}

	/**
	 * 名前を返す
	 */
	function Name($opt = false)
	{
		if ($this->name)
		{
			if ($opt) return '<span class="' . $opt . '">' . $this->name . '</span>';
			else  return $this->name;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Union戦闘した時間をセット
	 */
	function UnionSetTime()
	{
		$this->union_btl_time = time();
	}

	/**
	 * UnionBattleができるかどうか確認する。
	 */
	function CanUnionBattle()
	{
		$Now = time();
		$Past = $this->union_btl_time + UNION_BATTLE_NEXT;
		if ($Past <= $Now)
		{
			return true;
		}
		else
		{
			return abs($Now - $Past);
		}
	}

	/**
	 * 次のランク戦に挑戦できる時間を記録する。
	 */
	function SetRankBattleTime($time)
	{
		$this->rank_btl_time = $time;
	}


	/**
	 * ランキング挑戦できるか？(無理なら残り時間を返す)
	 */
	function CanRankBattle()
	{
		$now = time();
		if ($this->rank_btl_time <= $now)
		{
			return true;
		}
		else
			if (!$this->rank_btl_time)
			{
				return true;
			}
			else
			{
				$left = $this->rank_btl_time - $now;
				$hour = floor($left / 3600);
				$minutes = floor(($left % 3600) / 60);
				$seconds = floor(($left % 3600) % 60);
				return array(
					$hour,
					$minutes,
					$seconds);
			}
	}


	/**
	 * お金を増やす
	 */
	function GetMoney($no)
	{
		$this->money += $no;
	}


	/**
	 * お金を減らす
	 */
	function TakeMoney($no)
	{
		if ($this->money < $no)
		{
			return false;
		}
		else
		{
			$this->money -= $no;
			return true;
		}
	}


	/**
	 * 時間を消費する(総消費時間の加算)
	 */
	function WasteTime($time)
	{
		if ($this->time < $time) return false;
		$this->time -= $time;
		$this->wtime += $time;
		return true;
	}

	/**
	 * アイテムを追加
	 */
	function item_add($no, $amount = false)
	{
		if ($amount) $this->item[$no] += $amount;
		else  $this->item[$no]++;
	}


	/**
	 * アイテムを削除
	 */
	function item_remove($no, $amount = false)
	{
		// 減らす数。
		if ($this->item[$no] < $amount)
		{
			$amount = $this->item[$no];
			if (!$amount) $amount = 0;
		}
		if (!is_numeric($amount)) $amount = 1;

		// 減らす。
		$this->item[$no] -= $amount;
		if ($this->item[$no] < 1) unset($this->item[$no]);

		return $amount;
	}

}
