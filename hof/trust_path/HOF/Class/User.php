<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (CLASS_USER);

class HOF_Class_User extends user
{

	protected static $instance_user;

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
		if (isset(self::$instance_user[$id]))
		{
			return self::$instance_user[$id];
		}
		else
		{
			return new HOF_Class_User($id, $noExit);
		}
	}

	function __toString()
	{
		$val = (string)$this->id;

		return $val;
	}

	function __destruct()
	{
		if ($this->id)
		{
			$this->_cache_user_()->__destruct();
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
		$diff = $now - $data["last"];
		$data["last"] = $now;
		$gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
		$data["time"] += $gain;
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
				$char = $this->CharDataLoad($no);
				if ($char)
				{
					$party[] = $char;
				}
			}

			if (!empty($party)) {
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
		return (!isset($this->name) || empty($this->name)) ? false : true;
	}

	function _getfilename($path)
	{
		$ok = 0;

		$map = array(".yml", ".dat");

		foreach ($map as $ext)
		{
			$file = $path . $ext;
			if (file_exists($file))
			{
				$ok = 1;

				break;
			}
		}

		if (!$ok)
		{
			$file = $path . reset($map);
		}

		return array($ok, $file);
	}

	/**
	 * 全所持キャラクターをファイルから読んで $this->char に格納
	 */
	function CharDataLoadAll()
	{
		$list = array();

		//配列の初期化だけしておく
		$this->char = array();

		if ($list_char = HOF_Helper_Char::char_list_by_user($this))
		{
			foreach ($list_char as $no => $file)
			{
				$this->char[$no] = HOF_Model_Char::newCharFromFile($file);
				$this->char[$no]->SetUser($this->id);

				$list[$no] = $this->char[$no]->name;
			}
		}

		$this->_cache_user_()->data('char_list', $list);

		$this->_cache_user_()->save('char_list');

	}

	function &_cache_user_()
	{
		if ($this->id && !isset($this->_cache_user_))
		{
			$this->_cache_user_ = new HOF_Class_File_Cache(array(
				'path' => HOF_Helper_Char::user_path($this),
			));
		}

		return $this->_cache_user_;
	}

	/**
	 * 指定の所持キャラクターをファイルから読んで $this->char に格納後 "返す"。
	 */
	function CharDataLoad($CharNo)
	{
		if ($this->char[$CharNo]) return $this->char[$CharNo];

		$file = HOF_Helper_Char::char_file($CharNo, $this);

		if (!file_exists($file)) return false;

		$this->char[$CharNo] = HOF_Model_Char::newCharFromFile($file);

		$this->char[$CharNo]->SetUser($this->id);

		$list = $this->_cache_user_()->data('char_list');

		$list[$CharNo] = $this->char[$CharNo]->name;

		$this->_cache_user_()->data('char_list', $list);

		return $this->char[$CharNo];
	}

	/**
	 * アイテムデータを読む
	 */
	function LoadUserItem()
	{
		// 2重に読むのを防止。
		if (isset($this->item)) return false;

		$file = HOF_Helper_Char::user_file($this, USER_ITEM);

		if (file_exists($file))
		{
			$this->fp_item = HOF_Class_File::fplock_file($file);

			$this->item = HOF_Class_Yaml::load($this->fp_item);

			if ($this->item === false) $this->item = array();
		}
		else
		{
			$this->item = array();
		}
	}

	/**
	 * アイテムデータを保存する
	 */
	function SaveUserItem()
	{
		if (!is_array($this->item)) return false;

		$dir = HOF_Helper_Char::user_path($this);

		if (!is_dir($dir)) return false;

		$file = HOF_Helper_Char::user_file($this, USER_ITEM);

		// アイテムのソート
		ksort($this->item, SORT_STRING);

		foreach($this->item = array_filter($this->item) as $k => $v)
		{
			if (!$k || !$v)
			{
				unset($this->item[$k]);
			}
		}

		$text = HOF_Class_Yaml::dump($this->item);

		if (file_exists($file) && $this->fp_item)
		{
			HOF_Class_File::fpwrite_file($this->fp_item, $text, 1); //$textが空でも保存する
			fclose($this->fp_item);
			unset($this->fp_item);
		}
		else
		{
			// $textが空でも保存する
			HOF_Class_File::WriteFile($file, $text, 1);
		}
	}

	/**
	 * ユーザデータを読む
	 */
	function LoadData($noExit = false)
	{
		$file = HOF_Helper_Char::user_file($this, USER_DATA);

		if (file_exists($file))
		{
			$this->_cache_user_();

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
		$this->_cache_user_()->__destruct();

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

		if (!is_array($this->party_memo))
		{
			$this->party_memo = explode("<>", $this->party_memo);
		}

		if (!is_array($this->party_rank))
		{
			$this->party_rank = explode("<>", $this->party_rank);
		}

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
	function DeleteChar($no)
	{
		$file = HOF_Helper_Char::char_file($no, $this->id);

		if ($this->char[$no])
		{
			$this->char[$no]->fpclose();
		}

		if (file_exists($file)) HOF_Class_File::unlink($file);
	}

	/**
	 * キャラクターを所持してる数をかぞえる。
	 */
	function CharCount()
	{
		$list_char = HOF_Helper_Char::char_list_by_user($this);

		$no = count($list_char);

		return $no;
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

			foreach ((array)$this->char as $key => $var)
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

}
