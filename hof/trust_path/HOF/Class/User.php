<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (CLASS_USER);

class HOF_Class_User extends user
{

	function __destruct()
	{
		$this->fpCloseAll();
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
		$dir = USER . $this->id;

		//配列の初期化だけしておく
		$this->char = array();

		foreach (array("/*.yml", "/*") as $m)
		{
			foreach (glob($dir . $m) as $adr)
			{
				list($number, $ext) = HOF_Class_File::basename($adr);

				if (is_numeric($number) && !isset($this->char[$number]))
				{
					// キャラデータファイル
					$this->char[$number] = HOF_Model_Char::newCharFromFile($adr);

					// キャラが誰のか設定する
					$this->char[$number]->SetUser($this->id);
				}
			}
		}
	}

	/**
	 * 指定の所持キャラクターをファイルから読んで $this->char に格納後 "返す"。
	 */
	function CharDataLoad($CharNo)
	{
		// 既に読んでる場合。
		if ($this->char[$CharNo]) return $this->char[$CharNo];

		list($ok, $file) = $this->_getfilename(USER . $this->id . "/" . $CharNo);

		// そんなキャラいない場合。
		if (!$ok) return false;

		// 居る場合。
		//$chardata	= HOF_Class_File::ParseFile($file);
		//$this->char[$CharNo]	= new HOF_Class_Char($chardata);
		/*
		$this->char[$CharNo] = new HOF_Class_Char($file);
		*/
		$this->char[$CharNo] = HOF_Model_Char::newCharFromFile($file);

		// キャラが誰のか設定する
		$this->char[$CharNo]->SetUser($this->id);

		return $this->char[$CharNo];
	}

	/**
	 * アイテムデータを読む
	 */
	function LoadUserItem()
	{
		// 2重に読むのを防止。
		if (isset($this->item)) return false;

		list($ok, $file) = $this->_getfilename(USER . $this->id . "/" . ITEM);

		if ($ok)
		{
			$this->fp_item = HOF_Class_File::FileLock($file);

			if ($ext == '.dat')
			{
				$this->item = HOF_Class_File::ParseFileFP($this->fp_item);
			}
			else
			{
				$this->item = HOF_Class_Yaml::parse(stream_get_contents($this->fp_item));
			}

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
		$dir = USER . $this->id;

		if (!file_exists($dir)) return false;
		if (!is_array($this->item)) return false;

		list($ok, $file) = $this->_getfilename(USER . $this->id . "/" . ITEM);

		list($file_name, $file_ext) = HOF_Class_File::basename($file);

		// アイテムのソート
		ksort($this->item, SORT_STRING);

		if ($file_ext == '.dat')
		{
			foreach ($this->item as $key => $val)
			{
				$text .= "$key=$val\n";
			}
		}
		else
		{
			$text = HOF_Class_Yaml::dump($this->item);
		}

		if (file_exists($file) && $this->fp_item)
		{
			HOF_Class_File::WriteFileFP($this->fp_item, $text, 1); //$textが空でも保存する
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
		list($ok, $file) = $this->_getfilename(USER . $this->id . "/" . DATA);

		if ($ok)
		{
			$this->file = $file;

			list($this->file_name, $this->file_ext) = HOF_Class_File::basename($this->file);

			$this->fp = HOF_Class_File::FileLock($file, $noExit);
			if (!$this->fp) return false;

			if ($this->file_ext == '.dat')
			{
				$data = HOF_Class_File::ParseFileFP($this->fp);
			}
			else
			{
				$data = HOF_Class_Yaml::parse(stream_get_contents($this->fp));
			}

			//$data	= HOF_Class_File::ParseFile($file);// (2007/7/30 追加)
			/*
			$Array	= array("party_memo","party_rank");
			foreach($Array as $val)
			{
			if(!$data["$val"]) continue;
			$data["$val"]	= explode("<>",$data["$val"]);
			}
			*/

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
		$dir = USER . $this->id;

		if (file_exists($this->file) && $this->fp)
		{
			list($this->file_name, $this->file_ext) = HOF_Class_File::basename($this->file);

			//print("BBB");
			//ftruncate($this->fp,0);
			//rewind($this->fp);
			//$fp	= fopen($file,"w+");
			//flock($fp,LOCK_EX);
			//fputs($this->fp,$this->DataSavingFormat());
			HOF_Class_File::WriteFileFP($this->fp, $this->DataSavingFormat());
			fclose($this->fp);
			unset($this->fp);
			//HOF_Class_File::WriteFile("./user/1234/data2.dat",$this->DataSavingFormat());
			//HOF_Class_File::WriteFile($file,$this->DataSavingFormat());
			//HOF_Class_File::WriteFileFP($this->fp,$this->DataSavingFormat());
			//fclose($this->fp);
		}
		else
		{
			list($ok, $file) = $this->_getfilename(USER . $this->id . "/" . DATA);

			list($this->file_name, $this->file_ext) = HOF_Class_File::basename($file);

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

			if ($this->file_ext == '.dat')
			{
				$data[$k] = "$k=" . (is_array($this->{$k}) ? implode("<>", $this->{$k}) : $this->{$k});
			}
			else
			{
				$data[$k] = $this->{$k};
			}
		}

		if ($this->file_ext == '.dat')
		{
			$text = implode("\n", $data);
		}
		else
		{
			$text = HOF_Class_Yaml::dump($data);
		}


		/*
		$Save	= get_object_vars($this);
		unset($Save["char"]);
		unset($Save["item"]);
		unset($Save["islogin"]);
		foreach($Save as $key => $val) {
		$text	.= "$key=".(is_array($val) ? implode("<>",$val) : $val)."\n";
		}
		*/

		//print("<pre>".print_r($AAA,1)."</pre>");

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

		if ($this->file_ext == '.dat' && !is_array($this->rank_record))
		{
			/*
			list($record["all"], $record["win"], $record["lose"], $record["defend"], ) = explode("|", $this->rank_record);
			*/
			list($record["all"], $record["win"], $record["lose"], $record["defend"], ) = explode("<>", $this->rank_record);
		}
		else
		{
			$record = $this->rank_record;
		}

		return $record;
	}

}
