<?php 
class user {

	// ファイルポインタ
	var $fp;
	var $file;

	var $id, $pass;
	var $name, $last, $login ,$start;
	var $money;
	var $char;
	var $time;
	var $wtime;//總消費時間
	var $ip;//IPアドレス

	var $party_memo;
	var $party_rank;//ランキング用のパ一ティ
	var $rank_set_time;//ランキングPT設定した時間
	var $rank_btl_time;//次のランク戰に挑戰できる時間
	// ランキングの成績
	// = "總戰鬥回數<>勝利數<>敗北數<>引き分け<>首位防衛";
	var $rank_record;
	var $union_btl_time;//次のUnion戰に挑戰できる時間

	// OPTION
	var $record_btl_log;
	var $no_JS_itemlist;
	var $UserColor;

	// ユ一ザ一アイテム用の變數
	var $fp_item;
	var $item;

//////////////////////////////////////////////////
//	對像のIDのユ一ザ一クラスを作成
	function user($id,$noExit=false) {
		if($id)
		{
			$this->id	= $id;
			if($data = $this->LoadData($noExit)) {
				$this->DataUpDate($data);//timeとか增やす
				$this->SetData($data);
			}
		}
	}
//////////////////////////////////////////////////
//	IPを變更
	function SetIp($ip) {
		$this->ip = $ip;
	}
//////////////////////////////////////////////////
//	ユ一ザデ一タを讀む
	function LoadData($noExit=false) {
		$file	= USER.$this->id."/".DATA;
		if(file_exists($file))
		{
			$this->file	= $file;
			$this->fp	= FileLock($file,$noExit);
			if(!$this->fp)
				return false;
			$data	= ParseFileFP($this->fp);
			//$data	= ParseFile($file);// (2007/7/30 追加)
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

//////////////////////////////////////////////////
//	IDが結局のところ存在しているかたしかめる
	function is_exist() {
		if($this->name)
			return true;
		else
			return false;
	}
//////////////////////////////////////////////////
//	名前を返す
	function Name($opt=false) {
		if($this->name) {
			if($opt)
				return '<span class="'.$opt.'">'.$this->name.'</span>';
			else
				return $this->name;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////////
//	名前を變える
	function ChangeName($name) {

		if($this->name == $name)
			return false;

		$this->name	= $name;
		return true;
	}
//////////////////////////////////////////////////
//	Union戰鬥した時間をセット
	function UnionSetTime() {
		$this->union_btl_time	= time();
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

//////////////////////////////////////////////////
//	ランキング挑戰できるか？(無理なら殘り時間を返す)
	function CanRankBattle() {
		$now	= time();
		if($this->rank_btl_time <= $now) {
			return true;
		} else if(!$this->rank_btl_time) {
			return true;
		} else {
			$left	= $this->rank_btl_time - $now;
			$hour		= floor($left/3600);
			$minutes	= floor(($left%3600)/60);
			$seconds	= floor(($left%3600)%60);
			return array($hour,$minutes,$seconds);
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

//////////////////////////////////////////////////
//	時間を消費する(總消費時間の加算)
	function WasteTime($time) {
		if($this->time < $time)
			return false;
		$this->time		-= $time;
		$this->wtime 	+= $time;
		return true;
	}
//////////////////////////////////////////////////
//	キャラクタ一を所持してる數をかぞえる。
	function CharCount() {
		$dir	= USER.$this->id;
		$no		= 0;
		foreach(glob("$dir/*") as $adr) {
			$number	= basename($adr,".dat");
			if(is_numeric($number)) {//キャラデ一タファイル
				$no++;
			}
		}
		return $no;
	}
//////////////////////////////////////////////////
//	全所持キャラクタ一をファイルから讀んで $this->char に格納
	function CharDataLoadAll() {
		$dir	= USER.$this->id;
		$this->char	= array();//配列の初期化だけしておく
		foreach(glob("$dir/*") as $adr) {
			//print("substr:".substr($adr,-20,16)."<br>");//確認用
			//$number	= substr($adr,-20,16);//↓1行と同じ結果
			$number	= basename($adr,".dat");
			if(is_numeric($number)) {//キャラデ一タファイル
				//$chardata	= ParseFile($adr);// (2007/7/30 $adr -> $fp)
				//$this->char[$number]	= new char($chardata);
				$this->char[$number]	= new char($adr);
				$this->char[$number]->SetUser($this->id);//キャラが誰のか設定する
			}
		}
	}
//////////////////////////////////////////////////
//	指定の所持キャラクタ一をファイルから讀んで $this->char に格納後 "返す"。
	function CharDataLoad($CharNo) {
		// 既に讀んでる場合。
		if($this->char[$CharNo])
			return $this->char[$CharNo];
		// 讀んで無い場合。
		$file	= USER.$this->id."/".$CharNo.".dat";
		// そんなキャラいない場合。
		if(!file_exists($file))
			return false;

		// 居る場合。
		//$chardata	= ParseFile($file);
		//$this->char[$CharNo]	= new char($chardata);
		$this->char[$CharNo]	= new char($file);
		$this->char[$CharNo]->SetUser($this->id);//キャラが誰のか設定する
		return $this->char[$CharNo];
	}
//////////////////////////////////////////////////
//	アイテムを追加
	function AddItem($no,$amount=false) {
		if(!isset($this->item))//どうしたもんか…
			$this->LoadUserItem();
		if($amount)
			$this->item[$no]	+= $amount;
		else
			$this->item[$no]++;
	}

//////////////////////////////////////////////////
//	アイテムを削除
	function DeleteItem($no,$amount=false) {
		if(!isset($this->item))//どうしたもんか…
			$this->LoadUserItem();

		// 減らす數。
		if($this->item[$no] < $amount) {
			$amount	= $this->item[$no];
			if(!$amount)
				$amount = 0;
		}
		if(!is_numeric($amount))
			$amount	= 1;

		// 減らす。
		$this->item[$no]	-= $amount;
		if($this->item[$no] < 1)
			unset($this->item[$no]);

		return $amount;
	}

//////////////////////////////////////////////////
//	アイテムデ一タを讀む
	function LoadUserItem() {

		// 2重に讀むのを防止。
		if(isset($this->item))
			return false;

		$file	= USER.$this->id."/".ITEM;

		if(file_exists($file)) {
			$this->fp_item	= FileLock($file);
			$this->item	= ParseFileFP($this->fp_item);
			if($this->item === false)
				$this->item	= array();
		} else {
			$this->item	= array();
		}
	}

//////////////////////////////////////////////////
//	アイテムデ一タを保存する
	function SaveUserItem() {
		$dir	= USER.$this->id;
		if(!file_exists($dir))
			return false;

		$file	= USER.$this->id."/".ITEM;

		if(!is_array($this->item))
			return false;

		// アイテムのソ一ト
		ksort($this->item,SORT_STRING);

		foreach($this->item as $key => $val) {
			$text	.= "$key=$val\n";
		}

		if(file_exists($file) && $this->fp_item) {
			WriteFileFP($this->fp_item,$text,1);//$textが空でも保存する
			fclose($this->fp_item);
			unset($this->fp_item);
		} else {
			// $textが空でも保存する
			WriteFile($file,$text,1);
		}
	}

//////////////////////////////////////////////////
//	時間を經過させる。(Timeの增加)
	function DataUpDate(&$data) {
		$now	= time();
		$diff	= $now - $data["last"];
		$data["last"]	= $now;
		$gain	= $diff / (24*60*60) * TIME_GAIN_DAY;
		$data["time"]	+= $gain;
		if(MAX_TIME < $data["time"])
			$data["time"]	= MAX_TIME;
	}

//////////////////////////////////////////////////
//	デ一タをセットする。
//	※?
	function SetData(&$data) {

		foreach($data as $key => $val) {
			$this->{$key}	= $val;
		}
		/*
		$this->name	= $data["name"];
		$this->login	= $data["login"];
		$this->last	= $data["last"];
		$this->start	= $data["start"];
		*/
	}

//////////////////////////////////////////////////
//	パスワ一ドを暗號化する
	function CryptPassword($pass) {
		return substr(crypt($pass,CRYPT_KEY),strlen(CRYPT_KEY));
	}

//////////////////////////////////////////////////
//	名前を消す
	function DeleteName() {
		$this->name	= NULL;
	}

//////////////////////////////////////////////////
//	デ一タを保存する形式に變換する。(テキスト)
	function DataSavingFormat() {

		$Save	= array(
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
		foreach($Save as $val) {
			if($this->{$val})
				$text	.= "$val=".(is_array($this->{$val}) ? implode("<>",$this->{$val}) : $this->{$val})."\n";
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

//////////////////////////////////////////////////
//	デ一タを保存する
	function SaveData() {
		$dir	= USER.$this->id;
		$file	= USER.$this->id."/".DATA;

		if(file_exists($this->file) && $this->fp) {
			//print("BBB");
			//ftruncate($this->fp,0);
			//rewind($this->fp);
			//$fp	= fopen($file,"w+");
			//flock($fp,LOCK_EX);
			//fputs($this->fp,$this->DataSavingFormat());
			WriteFileFP($this->fp,$this->DataSavingFormat());
			fclose($this->fp);
			unset($this->fp);
			//WriteFile("./user/1234/data2.dat",$this->DataSavingFormat());
			//WriteFile($file,$this->DataSavingFormat());
			//WriteFileFP($this->fp,$this->DataSavingFormat());
			//fclose($this->fp);
		} else {
			if(file_exists($file))
				WriteFile($file,$this->DataSavingFormat());
		}
	}
/////////////////////////////////////////////////
//	デ一タファイル兼キャラファイルのファイルポインタも全部閉じる
	function fpCloseAll() {
		// 基本デ一タ
		if(is_resource($this->fp))
		{
			fclose($this->fp);
			unset($this->fp);
		}

		// アイテムデ一タ
		if(is_resource($this->fp_item))
		{
			fclose($this->fp_item);
			unset($this->fp_item);
		}

		// キャラデ一タ
		if($this->char)
		{
			foreach($this->char as $key => $var)
			{
				if(method_exists($this->char[$key],"fpclose"))
					$this->char[$key]->fpclose();
			}
		}

	}
//////////////////////////////////////////////////
//	ユ一ザ一の削除(全ファイル)
	function DeleteUser($DeleteFromRank=true) {
		//ランキングからまず消す。
		if($DeleteFromRank) {
			include_once(CLASS_RANKING);
			$Ranking	= new Ranking();
			if( $Ranking->DeleteRank($this->id) )
				$Ranking->SaveRanking();
		}

		$dir	= USER.$this->id;
		$files	= glob("$dir/*");
		$this->fpCloseAll();
		foreach($files as $val)
			unlink($val);
		rmdir($dir);
	}
//////////////////////////////////////////////////
//	放棄されているかどうか確かめる
	function IsAbandoned() {
		$now	= time();
		// $this->login がおかしければ終了する。
		if(strlen($this->login) !== 10) {
			return false;
		}
		if( ($this->login + ABANDONED) < $now) {
			return true;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////////
//	キャラデ一タを消す
	function DeleteChar($no) {
		$file	= USER.$this->id."/".$no.".dat";
		if($this->char[$no]) {
			$this->char[$no]->fpclose();
		}
		if(file_exists($file))
			unlink($file);
	}

//////////////////////////////////////////////////
//	
	//function Load

}
?>
