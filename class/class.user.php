<?
class user {

	// �ե�����ݥ���
	var $fp;
	var $file;

	var $id, $pass;
	var $name, $last, $login ,$start;
	var $money;
	var $char;
	var $time;
	var $wtime;//��������
	var $ip;//IP���ɥ쥹

	var $party_memo;
	var $party_rank;//��󥭥��ѤΥѡ��ƥ�
	var $rank_set_time;//��󥭥�PT���ꤷ������
	var $rank_btl_time;//���Υ�����ĩ��Ǥ������
	// ��󥭥󥰤�����
	// = "����Ʈ���<>������<>���̿�<>����ʬ��<>����ɱ�";
	var $rank_record;
	var $union_btl_time;//����Union���ĩ��Ǥ������

	// OPTION
	var $record_btl_log;
	var $no_JS_itemlist;
	var $UserColor;

	// �桼���������ƥ��Ѥ��ѿ�
	var $fp_item;
	var $item;

//////////////////////////////////////////////////
//	�оݤ�ID�Υ桼�������饹�����
	function user($id,$noExit=false) {
		if($id)
		{
			$this->id	= $id;
			if($data = $this->LoadData($noExit)) {
				$this->DataUpDate($data);//time�Ȥ����䤹
				$this->SetData($data);
			}
		}
	}
//////////////////////////////////////////////////
//	IP���ѹ�
	function SetIp($ip) {
		$this->ip = $ip;
	}
//////////////////////////////////////////////////
//	�桼���ǡ������ɤ�
	function LoadData($noExit=false) {
		$file	= USER.$this->id."/".DATA;
		if(file_exists($file))
		{
			$this->file	= $file;
			$this->fp	= FileLock($file,$noExit);
			if(!$this->fp)
				return false;
			$data	= ParseFileFP($this->fp);
			//$data	= ParseFile($file);// (2007/7/30 �ɲ�)
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
//	ID����ɤΤȤ���¸�ߤ��Ƥ��뤫���������
	function is_exist() {
		if($this->name)
			return true;
		else
			return false;
	}
//////////////////////////////////////////////////
//	̾�����֤�
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
//	̾�����Ѥ���
	function ChangeName($name) {

		if($this->name == $name)
			return false;

		$this->name	= $name;
		return true;
	}
//////////////////////////////////////////////////
//	Union��Ʈ�������֤򥻥å�
	function UnionSetTime() {
		$this->union_btl_time	= time();
	}
//////////////////////////////////////////////////
//	UnionBattle���Ǥ��뤫�ɤ�����ǧ���롣
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
//	��󥭥����ѤΥѡ��ƥ��������֤�
	function RankParty() {
		if(!$this->name)
			return "NOID";//Ķ���顼�����⤽��桼������¸�ߤ��ʤ���硣
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
//	��󥭥󥰤�����
// side = ("CHALLENGE","DEFEND")
	function RankRecord($result,$side,$DefendMatch) {
		$record	= $this->RankRecordLoad();

		$record["all"]++;
		switch(true) {
			// ����ʬ��
			/*
			case ($result === "d"):
				if($side != "CHALLENGE" && $DefendMatch)
					$record["defend"]++;
				break;
			*/
			// ��Ʈ��̤�ĩ��Ԥξ���
			case ($result === 0):
				if($side == "CHALLENGER") {
					$record["win"]++;
				} else {
					$record["lose"]++;
				}
				break;
			// ��Ʈ��̤�ĩ��Ԥ��餱
			case ($result === 1):
				if($side == "CHALLENGER") {
					$record["lose"]++;
				} else {
					$record["win"]++;
					if($DefendMatch)
						$record["defend"]++;
				}
				break;
			default:// ����ʬ��
				if($side != "CHALLENGER" && $DefendMatch)
					$record["defend"]++;
				break;
		}

		$this->rank_record	= $record["all"]."|".$record["win"]."|".$record["lose"]."|".$record["defend"];
	}
//////////////////////////////////////////////////
//	��󥭥�������Ӥ�ƤӽФ�
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
//	���Υ�����ĩ��Ǥ�����֤�Ͽ���롣
	function SetRankBattleTime($time) {
		$this->rank_btl_time	= $time;
	}

//////////////////////////////////////////////////
//	��󥭥�ĩ��Ǥ��뤫��(̵���ʤ�Ĥ���֤��֤�)
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
//	��������䤹
	function GetMoney($no) {
		$this->money	+= $no;
	}

//////////////////////////////////////////////////
//	����򸺤餹
	function TakeMoney($no) {
		if($this->money < $no) {
			return false;
		} else {
			$this->money	-= $no;
			return true;
		}
	}

//////////////////////////////////////////////////
//	���֤���񤹤�(�������֤βû�)
	function WasteTime($time) {
		if($this->time < $time)
			return false;
		$this->time		-= $time;
		$this->wtime 	+= $time;
		return true;
	}
//////////////////////////////////////////////////
//	����饯�����������Ƥ���򤫤����롣
	function CharCount() {
		$dir	= USER.$this->id;
		$no		= 0;
		foreach(glob("$dir/*") as $adr) {
			$number	= basename($adr,".dat");
			if(is_numeric($number)) {//�����ǡ����ե�����
				$no++;
			}
		}
		return $no;
	}
//////////////////////////////////////////////////
//	���������饯������ե����뤫���ɤ�� $this->char �˳�Ǽ
	function CharDataLoadAll() {
		$dir	= USER.$this->id;
		$this->char	= array();//����ν�����������Ƥ���
		foreach(glob("$dir/*") as $adr) {
			//print("substr:".substr($adr,-20,16)."<br>");//��ǧ��
			//$number	= substr($adr,-20,16);//��1�Ԥ�Ʊ�����
			$number	= basename($adr,".dat");
			if(is_numeric($number)) {//�����ǡ����ե�����
				//$chardata	= ParseFile($adr);// (2007/7/30 $adr -> $fp)
				//$this->char[$number]	= new char($chardata);
				$this->char[$number]	= new char($adr);
				$this->char[$number]->SetUser($this->id);//����餬ï�Τ����ꤹ��
			}
		}
	}
//////////////////////////////////////////////////
//	����ν������饯������ե����뤫���ɤ�� $this->char �˳�Ǽ�� "�֤�"��
	function CharDataLoad($CharNo) {
		// �����ɤ�Ǥ��硣
		if($this->char[$CharNo])
			return $this->char[$CharNo];
		// �ɤ��̵����硣
		$file	= USER.$this->id."/".$CharNo.".dat";
		// ����ʥ���餤�ʤ���硣
		if(!file_exists($file))
			return false;

		// ����硣
		//$chardata	= ParseFile($file);
		//$this->char[$CharNo]	= new char($chardata);
		$this->char[$CharNo]	= new char($file);
		$this->char[$CharNo]->SetUser($this->id);//����餬ï�Τ����ꤹ��
		return $this->char[$CharNo];
	}
//////////////////////////////////////////////////
//	�����ƥ���ɲ�
	function AddItem($no,$amount=false) {
		if(!isset($this->item))//�ɤ�������󤫡�
			$this->LoadUserItem();
		if($amount)
			$this->item[$no]	+= $amount;
		else
			$this->item[$no]++;
	}

//////////////////////////////////////////////////
//	�����ƥ����
	function DeleteItem($no,$amount=false) {
		if(!isset($this->item))//�ɤ�������󤫡�
			$this->LoadUserItem();

		// ���餹����
		if($this->item[$no] < $amount) {
			$amount	= $this->item[$no];
			if(!$amount)
				$amount = 0;
		}
		if(!is_numeric($amount))
			$amount	= 1;

		// ���餹��
		$this->item[$no]	-= $amount;
		if($this->item[$no] < 1)
			unset($this->item[$no]);

		return $amount;
	}

//////////////////////////////////////////////////
//	�����ƥ�ǡ������ɤ�
	function LoadUserItem() {

		// 2�Ť��ɤ�Τ��ɻߡ�
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
//	�����ƥ�ǡ�������¸����
	function SaveUserItem() {
		$dir	= USER.$this->id;
		if(!file_exists($dir))
			return false;

		$file	= USER.$this->id."/".ITEM;

		if(!is_array($this->item))
			return false;

		// �����ƥ�Υ�����
		ksort($this->item,SORT_STRING);

		foreach($this->item as $key => $val) {
			$text	.= "$key=$val\n";
		}

		if(file_exists($file) && $this->fp_item) {
			WriteFileFP($this->fp_item,$text,1);//$text�����Ǥ���¸����
			fclose($this->fp_item);
			unset($this->fp_item);
		} else {
			// $text�����Ǥ���¸����
			WriteFile($file,$text,1);
		}
	}

//////////////////////////////////////////////////
//	���֤�вᤵ���롣(Time������)
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
//	�ǡ����򥻥åȤ��롣
//	��?
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
//	�ѥ���ɤ�Ź沽����
	function CryptPassword($pass) {
		return substr(crypt($pass,CRYPT_KEY),strlen(CRYPT_KEY));
	}

//////////////////////////////////////////////////
//	̾����ä�
	function DeleteName() {
		$this->name	= NULL;
	}

//////////////////////////////////////////////////
//	�ǡ�������¸����������Ѵ����롣(�ƥ�����)
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
//	�ǡ�������¸����
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
//	�ǡ����ե�����󥭥��ե�����Υե�����ݥ��󥿤������Ĥ���
	function fpCloseAll() {
		// ���ܥǡ���
		if(is_resource($this->fp))
		{
			fclose($this->fp);
			unset($this->fp);
		}

		// �����ƥ�ǡ���
		if(is_resource($this->fp_item))
		{
			fclose($this->fp_item);
			unset($this->fp_item);
		}

		// �����ǡ���
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
//	�桼�����κ��(���ե�����)
	function DeleteUser($DeleteFromRank=true) {
		//��󥭥󥰤���ޤ��ä���
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
//	��������Ƥ��뤫�ɤ����Τ����
	function IsAbandoned() {
		$now	= time();
		// $this->login ������������н�λ���롣
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
//	�����ǡ�����ä�
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