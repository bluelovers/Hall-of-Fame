<?
include(CLASS_USER);
include(GLOBAL_PHP);
class main extends user {

	var $islogin	= false;

//////////////////////////////////////////////////
//	
	function main() {
		$this->SessionSwitch();
		$this->Set_ID_PASS();
		ob_start();
		$this->Order();
		$content	= ob_get_contents();
		ob_end_clean();

		$this->Head();
		print($content);
		$this->Debug();
		//$this->ShowSession();
		$this->Foot();
	}

//////////////////////////////////////////////////
//	
	function Order() {
		// ����������������˽���������
		// �ޤ��桼���ǡ����ɤ�Ǥޤ���
		switch(true) {
			case($_GET["menu"] === "auction"):
				include(CLASS_AUCTION);
				$ItemAuction	= new Auction(item);
				$ItemAuction->AuctionHttpQuery("auction");
				$ItemAuction->ItemCheckSuccess();// ���䤬��λ������ʪ��Ĵ�٤�
				$ItemAuction->UserSaveData();// �����ʤȶ�ۤ��ID���ۤä���¸����
				break;

			case($_GET["menu"] === "rank"):
				include(CLASS_RANKING);
				$Ranking	= new Ranking();
				break;
		}
		if( true === $message = $this->CheckLogin() ):
		//if( false ):
		// ������
			include_once(DATA_ITEM);
			include(CLASS_CHAR);
			if($this->FirstLogin())
				return 0;

			switch(true) {

				case($this->OptionOrder()):	return false;

				case($_POST["delete"]):
					if($this->DeleteMyData())
						return 0;

				// ����
				case($_SERVER["QUERY_STRING"] === "setting"):
					if($this->SettingProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SettingShow();
					return 0;

				// �����������
				case($_GET["menu"] === "auction"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->AuctionHeader();

					/*
					* �����ѤΥե�����
					* ɽ�����׵ᤷ����礫��
					* ���ʤ˼��Ԥ������ɽ�����롣
					*/
					$ResultExhibit	= $this->AuctionItemExhibitProcess($ItemAuction);
					$ResultBidding	= $this->AuctionItemBiddingProcess($ItemAuction);
					$ItemAuction->ItemSaveData();// �ѹ������ä���������¸���롣

					// ���ʥꥹ�Ȥ�ɽ������
					if($_POST["ExhibitItemForm"]) {
						$this->fpCloseAll();
						$this->AuctionItemExhibitForm($ItemAuction);

					// ���ʤ������������������ϥǡ�������¸����
					} else if($ResultExhibit !== false) {

						if($ResultExhibit === true || $ResultBidding === true)
							$this->SaveData();

						$this->fpCloseAll();
						$this->AuctionItemBiddingForm($ItemAuction);

					// ����ʳ�
					} else {
						$this->fpCloseAll();
						$this->AuctionItemExhibitForm($ItemAuction);
					}

					$this->AuctionFoot($ItemAuction);
					return 0;

				// ���
				case($_SERVER["QUERY_STRING"] === "hunt"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->fpCloseAll();
					$this->HuntShow();
					return 0;

				// ��
				case($_SERVER["QUERY_STRING"] === "town"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->fpCloseAll();
					$this->TownShow();
					return 0;

				// ���ߥ��
				case($_SERVER["QUERY_STRING"] === "simulate"):
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					if($this->SimuBattleProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SimuBattleShow($result);
					return 0;

				// ��˥���
				case($_GET["union"]):
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					include(CLASS_UNION);
					include(DATA_MONSTER);
					if($this->UnionProcess()) {
						// ��Ʈ����
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						// ɽ��
						$this->fpCloseAll();
						$this->UnionShow();
					}
					return 0;

				// ���̥�󥹥���
				case($_GET["common"]):
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					if($this->MonsterBattle()) {
						$this->SaveData();
						$this->fpCloseAll();
					} else {
						$this->fpCloseAll();
						$this->MonsterShow();
					}
					return 0;

				// ����饹��
				case($_GET["char"]):
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					include(DATA_SKILL);
					include(DATA_JUDGE_SETUP);
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->CharStatProcess();
					$this->fpCloseAll();
					$this->CharStatShow();
					return 0;

				// �����ƥ����
				case($_SERVER["QUERY_STRING"] === "item"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					//$this->ItemProcess();
					$this->fpCloseAll();
					$this->ItemShow();
					return 0;

				// ��ϣ
				case($_GET["menu"] === "refine"):
					$this->LoadUserItem();
					$this->SmithyRefineHeader();
					if($this->SmithyRefineProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$result	= $this->SmithyRefineShow();
					return 0;

				// ����
				case($_GET["menu"] === "create"):
					$this->LoadUserItem();
					$this->SmithyCreateHeader();
					include(DATA_CREATE);//����Ǥ����Υǡ�����
					if($this->SmithyCreateProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->SmithyCreateShow();
					return 0;
/*
				// ����å�(�켰:�㤦,���,����Х���)
				case($_SERVER["QUERY_STRING"] === "shop"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					if($this->ShopProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopShow();
					return 0;
*/
				// ����å�(�㤦)
				case($_GET["menu"] === "buy"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->ShopHeader();
					if($this->ShopBuyProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopBuyShow();
					return 0;

				// ����å�(���)
				case($_GET["menu"] === "sell"):
					$this->LoadUserItem();//�����ƥ�ǡ����ɤ�
					$this->ShopHeader();
					if($this->ShopSellProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->ShopSellShow();
					return 0;

				// ����å�(Ư��)
				case($_GET["menu"] === "work"):
					$this->ShopHeader();
					if($this->WorkProcess())
						$this->SaveData();
					$this->fpCloseAll();
					$this->WorkShow();
					return 0;

				// ��󥭥�
				case($_GET["menu"] === "rank"):
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					$RankProcess	= $this->RankProcess($Ranking);

					if ($RankProcess === "BATTLE") {
						$this->SaveData();
						$this->fpCloseAll();
					} else if ($RankProcess === true) {
						$this->SaveData();
						$this->fpCloseAll();
						$this->RankShow($Ranking);
					} else {
						$this->fpCloseAll();
						$this->RankShow($Ranking);
					}
					return 0;

				// ����
				case($_SERVER["QUERY_STRING"] === "recruit"):
					if($this->RecruitProcess())
						$this->SaveData();

					$this->fpCloseAll();
					$this->RecruitShow($result);
					return 0;

				// ����ʳ�(�ȥå�)
				default:
					$this->CharDataLoadAll();//�����ǡ����ɤ�
					$this->fpCloseAll();
					$this->LoginMain();
					return 0;
			}
		else:
		// ��������
			$this->fpCloseAll();
			switch(true) {
				case($this->OptionOrder()):	return false;
				case($_POST["Make"]):
					list($bool,$message) = $this->MakeNewData();
					if( true === $bool ) {
						$this->LoginForm($message);
						return false;
					}
				case($_SERVER["QUERY_STRING"] === "newgame"):
					$this->NewForm($message);	return false;
				default:	$this->LoginForm($message);
			}
		endif;
	}

//////////////////////////////////////////////////
//	UpDate,BBS,Manual��
	function OptionOrder() {
		$this->fpCloseAll();
		switch(true) {
			case($_SERVER["QUERY_STRING"] === "rank"):	RankAllShow();	return true;
			case($_SERVER["QUERY_STRING"] === "update"):	ShowUpDate();	return true;
			case($_SERVER["QUERY_STRING"] === "bbs"):	$this->bbs01();	return true;
			case($_SERVER["QUERY_STRING"] === "manual"):	ShowManual();	return true;
			case($_SERVER["QUERY_STRING"] === "manual2"):	ShowManual2();	return true;
			case($_SERVER["QUERY_STRING"] === "tutorial"):	ShowTutorial();	return true;
			case($_SERVER["QUERY_STRING"] === "log"):
				ShowLogList();
				return true;
			case($_SERVER["QUERY_STRING"] === "clog"): LogShowCommon(); return true;
			case($_SERVER["QUERY_STRING"] === "ulog"): LogShowUnion(); return true;
			case($_SERVER["QUERY_STRING"] === "rlog"): LogShowRanking(); return true;
			case($_GET["gamedata"]):
				ShowGameData();
				return true;
			case($_GET["log"]):
				ShowBattleLog($_GET["log"]);
				return true;
			case($_GET["ulog"]):
				ShowBattleLog($_GET["ulog"],"UNION");
				return true;
			case($_GET["rlog"]):
				ShowBattleLog($_GET["rlog"],"RANK");
				return true;
		}
	}

//////////////////////////////////////////////////
//	Ũ�ο����֤�	������+2(max:5)
	function EnemyNumber($party) {
		$min	= count($party);//�ץ쥤�䡼��PT��
		if($min == 5)//5�ͤʤ�5ɤ
			return 5;
		$max	= $min + ENEMY_INCREASE;// �Ĥޤꡢ+2�ʤ�[1��:1��3ɤ] [2��:2��4ɤ] [3:3-5] [4:4-5] [5:5]
		if($max>5)
			$max	= 5;
		mt_srand();
		return mt_rand($min,$max);
	}
//////////////////////////////////////////////////
//	�и������Ψ����Ũ��������֤�
	function SelectMonster($monster) {
		foreach($monster as $val)
			$max	+= $val[0];//��Ψ�ι��
		$pos	= mt_rand(0,$max);//0����� ������������
		foreach($monster as $monster_no => $val) {
			$upp	+= $val[0];//���λ����Ǥγ�Ψ�ι��
			if($pos <= $upp)//��פ���㤱��С�Ũ�����ꤵ���
				return $monster_no;
		}
	}
//////////////////////////////////////////////////
//	Ũ��PT��������֤�
//	Specify=Ũ����(����)
	function EnemyParty($Amount,$MonsterList,$Specify=false) {

		// �����󥹥���
		if($Specify) {
			$MonsterNumbers	= $Specify;
		}

		// ��󥹥�����Ȥꤢ������������������
		$enemy	= array();
		if(!$Amount)
			return $enemy;
		mt_srand();
		for($i=0; $i<$Amount; $i++)
			$MonsterNumbers[]	= $this->SelectMonster($MonsterList);

		// ��ʣ���Ƥ����󥹥�����Ĵ�٤�
		$overlap	= array_count_values($MonsterNumbers);

		// Ũ������ɤ�����������롣
		include(CLASS_MONSTER);
		foreach($MonsterNumbers as $Number) {
			if(1 < $overlap[$Number])//1ɤ�ʾ�и�����ʤ�̾���˵����Ĥ��롣
				$enemy[]	= new monster(CreateMonster($Number,true));
			else
				$enemy[]	= new monster(CreateMonster($Number));
		}
		return $enemy;
	}
//////////////////////////////////////////////////
//	�����ܺ�ɽ����������줿�ꥯ�����Ȥ��������
//	Ĺ��...(100�ԥ����С�)
	function CharStatProcess() {
		$char	= &$this->char[$_GET["char"]];
		if(!$char) return false;
		switch(true):
			// ���ơ������徺
			case($_POST["stup"]):
				//���ơ������ݥ����Ķ��(�ͤ�Τ����������)
				$Sum	= abs($_POST["upStr"]) + abs($_POST["upInt"]) + abs($_POST["upDex"]) + abs($_POST["upSpd"]) + abs($_POST["upLuk"]);
				if($char->statuspoint < $Sum) {
					ShowError("���ơ������ݥ����Ķ��","margin15");
					return false;
				}

				if($Sum == 0)
					return false;

				$Stat	= array("Str","Int","Dex","Spd","Luk");
				foreach($Stat as $val) {//�����ͤ�Ķ���ʤ��������å�
					if(MAX_STATUS < ($char->{strtolower($val)} + $_POST["up".$val])) {
						ShowError("���祹�ơ�����Ķ��(".MAX_STATUS.")","margin15");
						return false;
					}
				}
				$char->str	+= $_POST["upStr"];//���ơ����������䤹
				$char->int	+= $_POST["upInt"];
				$char->dex	+= $_POST["upDex"];
				$char->spd	+= $_POST["upSpd"];
				$char->luk	+= $_POST["upLuk"];
				$char->SetHpSp();

				$char->statuspoint	-= $Sum;//�ݥ���Ȥ򸺤餹��
				print("<div class=\"margin15\">\n");
				if($_POST["upStr"])
					ShowResult("STR �� <span class=\"bold\">".$_POST[upStr]."</span> �夬�ä���".($char->str - $_POST["upStr"])." -> ".$char->str."<br />\n");
				if($_POST["upInt"])
					ShowResult("INT �� <span class=\"bold\">".$_POST[upInt]."</span> �夬�ä���".($char->int - $_POST["upInt"])." -> ".$char->int."<br />\n");
				if($_POST["upDex"])
					ShowResult("DEX �� <span class=\"bold\">".$_POST[upDex]."</span> �夬�ä���".($char->dex - $_POST["upDex"])." -> ".$char->dex."<br />\n");
				if($_POST["upSpd"])
					ShowResult("SPD �� <span class=\"bold\">".$_POST[upSpd]."</span> �夬�ä���".($char->spd - $_POST["upSpd"])." -> ".$char->spd."<br />\n");
				if($_POST["upLuk"])
					ShowResult("LUK �� <span class=\"bold\">".$_POST[upLuk]."</span> �夬�ä���".($char->luk - $_POST["upLuk"])." -> ".$char->luk."<br />\n");
				print("</div>\n");
				$char->SaveCharData($this->id);
				return true;
			// ���֡�¾����(�ɸ�)
			case($_POST["position"]):
				if($_POST["position"] == "front") {
					$char->position	= FRONT;
					$pos	= "����(Front)";
				} else {
					$char->position	= BACK;
					$pos	= "���(Back)";
				}

				$char->guard	= $_POST["guard"];
				switch($_POST["guard"]) {
					case "never":	$guard	= "��Ҥ���ʤ�"; break;
					case "life25":	$guard	= "���Ϥ� 25%�ʾ�ʤ� ��Ҥ���"; break;
					case "life50":	$guard	= "���Ϥ� 50%�ʾ�ʤ� ��Ҥ���"; break;
					case "life75":	$guard	= "���Ϥ� 75%�ʾ�ʤ� ��Ҥ���"; break;
					case "prob25":	$guard	= "25%�γ�Ψ�� ��Ҥ���"; break;
					case "prob50":	$guard	= "50%�γ�Ψ�� ��Ҥ���"; break;
					case "prob75":	$guard	= "75%�γ�Ψ�� ��Ҥ���"; break;
					default:	$guard	= "ɬ����Ҥ���"; break;
				}
				$char->SaveCharData($this->id);
				ShowResult($char->Name()." �����֤� {$pos} �ˡ�<br />���Ҥλ� {$guard} �褦�����ꡣ\n","margin15");
				return true;
			//��ư����
			case($_POST["ChangePattern"]):
				$max	= $char->MaxPatterns();
				//��������ѥ�����ȵ�������
				for($i=0; $i<$max; $i++) {
					$judge[]	= $_POST["judge".$i];
					$quantity_post	= (int)$_POST["quantity".$i];
					if(4 < strlen($quantity_post)) {
						$quantity_post	= substr($quantity_post,0,4);
					}
					$quantity[]	= $quantity_post;
					$action[]	= $_POST["skill".$i];
				}
				//if($char->ChangePattern($judge,$action)) {
				if($char->PatternSave($judge,$quantity,$action)) {
					$char->SaveCharData($this->id);
					ShowResult("�ѥ�����������¸ ��λ","margin15");
					return true;
				}
				ShowError("���Ԥ����ʤ�ǡ���𤷤ƤߤƤ������� 03050242","margin15");
				return false;
				break;
			//	��ư���� �� �ϵ���
			case($_POST["TestBattle"]):
					$max	= $char->MaxPatterns();
					//��������ѥ�����ȵ�������
					for($i=0; $i<$max; $i++) {
						$judge[]	= $_POST["judge".$i];
						$quantity_post	= (int)$_POST["quantity".$i];
						if(4 < strlen($quantity_post)) {
							$quantity_post	= substr($quantity_post,0,4);
						}
						$quantity[]	= $quantity_post;
						$action[]	= $_POST["skill".$i];
					}
					//if($char->ChangePattern($judge,$action)) {
					if($char->PatternSave($judge,$quantity,$action)) {
						$char->SaveCharData($this->id);
						$this->CharTestDoppel();
					}
				break;
			//	��ư�ѥ�������(��)
			case($_POST["PatternMemo"]):
				if($char->ChangePatternMemo()) {
					$char->SaveCharData($this->id);
					ShowResult("�ѥ������ ��λ","margin15");
					return true;
				}
				break;
			//	����Ԥ��ɲ�
			case($_POST["AddNewPattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->AddPattern($_POST["PatternNumber"])) {
					$char->SaveCharData($this->id);
					ShowResult("�ѥ������ɲ� ��λ","margin15");
					return true;
				}
				break;
			//	����Ԥ���
			case($_POST["DeletePattern"]):
				if(!isset($_POST["PatternNumber"]))
					return false;
				if($char->DeletePattern($_POST["PatternNumber"])) {
					$char->SaveCharData($this->id);
					ShowResult("�ѥ������� ��λ","margin15");
					return true;
				}
				break;
			//	����ս����������Ϥ���
			case($_POST["remove"]):
				if(!$_POST["spot"]) {
					ShowError("������Ϥ����ս꤬���򤵤�Ƥ��ʤ�","margin15");
					return false;
				}
				if(!$char->{$_POST["spot"]}) {// $this �� $char �ζ�����ա�
					ShowError("���ꤵ�줿�ս�ˤ�����̵��","margin15");
					return false;
				}
				$item	= LoadItemData($char->{$_POST["spot"]});
				if(!$item) return false;
				$this->AddItem($char->{$_POST["spot"]});
				$this->SaveUserItem();
				$char->{$_POST["spot"]}	= NULL;
				$char->SaveCharData($this->id);
				SHowResult($char->Name()." �� {$item[name]} �� �Ϥ�������","margin15");
				return true;
				break;
			//	���������Ϥ���
			case($_POST["remove_all"]):
				if($char->weapon || $char->shield || $char->armor || $char->item ) {
					if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
					if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
					if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
					if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
					$this->SaveUserItem();
					$char->SaveCharData($this->id);
					ShowResult($char->Name()." �������� �����������","margin15");
					return true;
				}	break;
			//	����ʪ����������
			case($_POST["equip_item"]):
				$item_no	= $_POST["item_no"];
				if(!$this->item["$item_no"]) {//���Υ����ƥ�������Ƥ��뤫
					ShowError("Item not exists.","margin15");
					return false;
				}

				$JobData	= LoadJobData($char->job);
				$item	= LoadItemData($item_no);//�������褦�Ȥ��Ƥ�ʪ
				if( !in_array( $item["type"], $JobData["equip"]) ) {//���줬�����Բ�ǽ�ʤ�?
					ShowError("{$char->job_name} can't equip {$item[name]}.","margin15");
					return false;
				}

				if(false === $return = $char->Equip($item)) {
					ShowError("Handle Over.","margin15");
					return false;
				} else {
					$this->DeleteItem($item_no);
					foreach($return as $no) {
						$this->AddItem($no);
					}
				}

				$this->SaveUserItem();
				$char->SaveCharData($this->id);
				ShowResult("{$char->name} �� {$item[name]} ����������.","margin15");
				return true;
				break;
			// �����뽬��
			case($_POST["learnskill"]):
				if(!$_POST["newskill"]) {
					ShowError("������̤����","margin15");
					return false;
				}

				$char->SetUser($this->id);
				list($result,$message)	= $char->LearnNewSkill($_POST["newskill"]);
				if($result) {
					$char->SaveCharData();
					ShowResult($message,"margin15");
				} else {
					ShowError($message,"margin15");
				}
				return true;
			// ���饹������(ž��)
			case($_POST["classchange"]):
				if(!$_POST["job"]) {
					ShowError("�� ̤����","margin15");
					return false;
				}
				if($char->ClassChange($_POST["job"])) {
					// �������������
					if($char->weapon || $char->shield || $char->armor || $char->item ) {
						if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
						if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
						if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
						if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
						$this->SaveUserItem();
					}
					// ��¸
					$char->SaveCharData($this->id);
					ShowResult("ž�� ��λ","margin15");
					return true;
				}
				ShowError("failed.","margin15");
				return false;
			//	��̾(ɽ��)
			case($_POST["rename"]):
				$Name	= $char->Name();
				$message = <<< EOD
<form action="?char={$_GET[char]}" method="post" class="margin15">
Ⱦ�ѱѿ�16ʸ�� (����1ʸ��=Ⱦ��2ʸ��)<br />
<input type="text" name="NewName" style="width:160px" class="text" />
<input type="submit" class="btn" name="NameChange" value="Change" />
<input type="submit" class="btn" value="Cancel" />
</form>
EOD;
				print($message);
				return false;
			// ��̾(����)
			case($_POST["NewName"]):
				list($result,$return)	= CheckString($_POST["NewName"],16);
				if($result === false) {
					ShowError($return,"margin15");
					return false;
				} else if($result === true) {
					if($this->DeleteItem("7500",1) == 1) {
						ShowResult($char->Name()." ���� ".$return." �ز�̾���ޤ�����","margin15");
						$char->ChangeName($return);
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
						return true;
					} else {
						ShowError("�����ƥब����ޤ���","margin15");
						return false;
					}
					return true;
				}
			// �Ƽ�ꥻ�åȤ�ɽ��
			case($_POST["showreset"]):
				$Name	= $char->Name();
				print('<div class="margin15">'."\n");
				print("���Ѥ��륢���ƥ�<br />\n");
				print('<form action="?char='.$_GET[char].'" method="post">'."\n");
				print('<select name="itemUse">'."\n");
				$resetItem	= array(7510,7511,7512,7513,7520);
				foreach($resetItem as $itemNo) {
					if($this->item[$itemNo]) {
						$item	= LoadItemData($itemNo);
						print('<option value="'.$itemNo.'">'.$item[name]." x".$this->item[$itemNo].'</option>'."\n");
					}
				}
				print("</select>\n");
				print('<input type="submit" class="btn" name="resetVarious" value="Reset">'."\n");
				print('<input type="submit" class="btn" value="Cancel">'."\n");
				print('</form>'."\n");
				print('</div>'."\n");
				break;

			// �Ƽ�ꥻ�åȤν���
			case($_POST["resetVarious"]):
				switch($_POST["itemUse"]) {
					case 7510:
						$lowLimit	= 1;
						break;
					case 7511:
						$lowLimit	= 30;
						break;
					case 7512:
						$lowLimit	= 50;
						break;
					case 7513:
						$lowLimit	= 100;
						break;
					// skill
					case 7520:
						$skillReset	= true;
						break;
				}
				// �Ф����SPD1���᤹�����ƥ�ˤ���
				if($_POST["itemUse"] == 6000) {
					if($this->DeleteItem(6000) == 0) {
						ShowError("�����ƥब����ޤ���","margin15");
						return false;
					}
					if(1 < $char->spd) {
						$dif	= $char->spd - 1;
						$char->spd	-= $dif;
						$char->statuspoint	+= $dif;
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
						ShowResult("�ݥ���ȴԸ�����","margin15");
						return true;
					}
				}
				if($lowLimit) {
					if(!$this->item[$_POST["itemUse"]]) {
						ShowError("�����ƥब����ޤ���","margin15");
						return false;
					}
					if($lowLimit < $char->str) {$dif = $char->str - $lowLimit; $char->str -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->int) {$dif = $char->int - $lowLimit; $char->int -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->dex) {$dif = $char->dex - $lowLimit; $char->dex -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->spd) {$dif = $char->spd - $lowLimit; $char->spd -= $dif; $pointBack += $dif;}
					if($lowLimit < $char->luk) {$dif = $char->luk - $lowLimit; $char->luk -= $dif; $pointBack += $dif;}
					if($pointBack) {
						if($this->DeleteItem($_POST["itemUse"]) == 0) {
							ShowError("�����ƥब����ޤ���","margin15");
							return false;
						}
						$char->statuspoint	+= $pointBack;
						// �������������
						if($char->weapon || $char->shield || $char->armor || $char->item ) {
							if($char->weapon)	{ $this->AddItem($char->weapon);	$char->weapon	=NULL; }
							if($char->shield)	{ $this->AddItem($char->shield);	$char->shield	=NULL; }
							if($char->armor)	{ $this->AddItem($char->armor);		$char->armor	=NULL; }
							if($char->item)		{ $this->AddItem($char->item);		$char->item		=NULL; }
							ShowResult($char->Name()." �������� �����������","margin15");
						}
						$char->SaveCharData($this->id);
						$this->SaveUserItem();
						ShowResult("�ݥ���ȴԸ�����","margin15");
						return true;
					} else {
						ShowError("�ݥ���ȴԸ�����","margin15");
						return false;
					}
				}
				break;

			// ����ʥ�(ɽ��)
			case($_POST["byebye"]):
				$Name	= $char->Name();
				$message = <<< HTML_BYEBYE
<div class="margin15">
{$Name} �� ��ۤ��ޤ���?<br>
<form action="?char={$_GET[char]}" method="post">
<input type="submit" class="btn" name="kick" value="Yes">
<input type="submit" class="btn" value="No">
</form>
</div>
HTML_BYEBYE;
				print($message);
				return false;
			// ����ʥ�(����)
			case($_POST["kick"]):
				//$this->DeleteChar($char->birth);
				$char->DeleteChar();
				$host  = $_SERVER['HTTP_HOST'];
				$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
				//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$extra = INDEX;
				header("Location: http://$host$uri/$extra");
				exit;
				break;
		endswitch;
	}
//////////////////////////////////////////////////////////////////////////////////////
//	����饯�����ܺ�ɽ���������ѹ��ʤɤʤ�
//	Ĺ������...(200�԰ʾ�)
	function CharStatShow() {
		$char	= &$this->char[$_GET["char"]];
		if(!$char) {
			print("Not exists");
			return false;
		}
		// ��Ʈ���ѿ������ꡣ
		$char->SetBattleVariable();

		// ���ǡ���
		$JobData	= LoadJobData($char->job);

		// ž����ǽ�ʿ�
		if($JobData["change"]) {
			include_once(DATA_CLASSCHANGE);
			foreach($JobData["change"] as $job) {
				if(CanClassChange($char,$job))
					$CanChange[]	= $job;//ž���Ǥ�����䡣
			}
		}

		////// ���ơ�����ɽ�� //////////////////////////////
			?>
<form action="?char=<?=$_GET["char"]?>" method="post" style="padding:5px 0 0 15px"><?
		// ����¾�����
		print('<div style="padding-top:5px">');
		foreach($this->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//ɽ���業��饹���å�
			echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
		}
		print("</div>");
	?>
<h4>Character Status <a href="?manual#charstat" target="_blank" class="a0">?</a></h4><?
		$char->ShowCharDetail();
		// ��̾
		if($this->item["7500"])
			print('<input type="submit" class="btn" name="rename" value="ChangeName">'."\n");
		// ���ơ������ꥻ�åȷ�
		if($this->item["7510"] ||
			$this->item["7511"] ||
			$this->item["7512"] ||
			$this->item["7513"] ||
			$this->item["7520"]) {
			print('<input type="submit" class="btn" name="showreset" value="Reset">'."\n");
		}
?>
<input type="submit" class="btn" name="byebye" value="Kick">
</form><?
	// ���ơ������徺 ////////////////////////////
	if(0 < $char->statuspoint) {
print <<< HTML
	<form action="?char=$_GET[char]" method="post" style="padding:0 15px">
	<h4>Status <a href="?manual#statup" target="_blank" class="a0">?</a></h4>
HTML;

		$Stat	= array("Str","Int","Dex","Spd","Luk");
		print("Point : {$char->statuspoint}<br />\n");
		foreach($Stat as $val) {
			print("{$val}:\n");
			print("<select name=\"up{$val}\" class=\"vcent\">\n");
			for($i=0; $i < $char->statuspoint + 1; $i++)
				print("<option value=\"{$i}\">+{$i}</option>\n");
			print("</select>");
		}
		print("<br />");
		print('<input type="submit" class="btn" name="stup" value="Increase Status">');
		print("\n");

	print("</form>\n");
	}
	?>
	<form action="?char=<?=$_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>Action Pattern <a href="?manual#jdg" target="_blank" class="a0">?</a></h4><?

		// Action Pattern ��ưȽ�� /////////////////////////
		$list	= JudgeList();// ��ưȽ�������
		print("<table cellspacing=\"5\"><tbody>\n");
		for($i=0; $i<$char->MaxPatterns(); $i++) {
			print("<tr><td>");
			//----- No
			print( ($i+1)."</td><td>");
			//----- JudgeSelect(Ƚ��μ���)
			print("<select name=\"judge".$i."\">\n");
			foreach($list as $val) {//Ƚ�Ǥ�option
				$exp	= LoadJudgeData($val);
				print("<option value=\"{$val}\"".($char->judge[$i] == $val ? " selected" : NULL).($exp["css"]?' class="select0"':NULL).">".($exp["css"]?'&nbsp;':'&nbsp;&nbsp;&nbsp;')."{$exp[exp]}</option>\n");
			}
			print("</select>\n");
			print("</td><td>\n");
			//----- ����(��)
			print("<input type=\"text\" name=\"quantity".$i."\" maxlength=\"4\" value=\"".$char->quantity[$i]."\" style=\"width:56px\" class=\"text\">");
			print("</td><td>\n");
			//----- //SkillSelect(���μ���)
			print("<select name=\"skill".$i."\">\n");
			foreach($char->skill as $val) {//����option
				$skill	= LoadSkillData($val);
				print("<option value=\"{$val}\"".($char->action[$i] == $val ? " selected" : NULL).">");
				print($skill["name"].(isset($skill["sp"])?" - (SP:{$skill[sp]})":NULL));
				print("</option>\n");
			}
			print("</select>\n");
			print("</td><td>\n");
			print('<input type="radio" name="PatternNumber" value="'.$i.'">');
			print("</td></tr>\n");
		}
		print("</tbody></table>\n");
	?>
<input type="submit" class="btn" value="Set Pattern" name="ChangePattern">
<input type="submit" class="btn" value="Set & Test" name="TestBattle">
&nbsp;<a href="?simulate">Simulate</a><br />
<input type="submit" class="btn" value="Switch Pattern" name="PatternMemo">
<input type="submit" class="btn" value="Add" name="AddNewPattern">
<input type="submit" class="btn" value="Delete" name="DeletePattern">
</form>
<form action="?char=<?=$_GET["char"]?>" method="post" style="padding:0 15px">
<h4>Position & Guarding <a href="?manual#posi" target="_blank" class="a0">?</a></h4>
<table><tbody>
<tr><td>����(Position) :</td><td><input type="radio" class="vcent" name="position" value="front"<? ($char->position=="front"?print(" checked"):NULL) ?>>����(Front)</td></tr>
<tr><td></td><td><input type="radio" class="vcent" name="position" value="back"<? ($char->position=="back"?print(" checked"):NULL) ?>>���(Backs)</td></tr>
<tr><td>���(Guarding) :</td><td>
<select name="guard"><?

		// ���Ҥλ��θ�Ҽ�� //////////////////////////////
		$option	= array(/*
		"always"=> "Always",
		"never"	=> "Never",
		"life25"	=> "If life more than 25%",
		"life50"	=> "If life more than 50%",
		"life75"	=> "If life more than 75%",
		"prob25"	=> "Probability of 25%",
		"prpb50"	=> "Probability of 50%",
		"prob75"	=> "Probability of 75%",
		*/
		"always"=> "ɬ�����",
		"never"	=> "���ʤ�",
		"life25"	=> "���Ϥ� 25%�ʾ�ʤ� ���",
		"life50"	=> "���Ϥ� 50%�ʾ�ʤ� ���",
		"life75"	=> "���Ϥ� 75%�ʾ�ʤ� ���",
		"prob25"	=> "25%�γ�Ψ�� ���",
		"prpb50"	=> "50%�γ�Ψ�� ���",
		"prob75"	=> "75%�γ�Ψ�� ���",
		);
		foreach($option as $key => $val)
			print("<option value=\"{$key}\"".($char->guard==$key ? " selected" : NULL ).">{$val}</option>");
	?>
	</select>
	</td></tr>
	</tbody></table>
	<input type="submit" class="btn" value="Set">
	</form>
<?
		// �������ʪɽ�� ////////////////////////////////
		$weapon	= LoadItemData($char->weapon);
		$shield	= LoadItemData($char->shield);
		$armor	= LoadItemData($char->armor);
		$item	= LoadItemData($char->item);

		$handle	= 0;
		$handle	= $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item["handle"];
	?>
	<div style="margin:0 15px">
	<h4>Equipment <a href="?manual#equip" target="_blank" class="a0">?</a></h4>
	<div class="bold u">Current Equip's</div>
	<table>
	<tr><td class="dmg" style="text-align:right">Atk :</td><td class="dmg"><?=$char->atk[0]?></td></tr>
	<tr><td class="spdmg" style="text-align:right">Matk :</td><td class="spdmg"><?=$char->atk[1]?></td></tr>
	<tr><td class="recover" style="text-align:right">Def :</td><td class="recover"><?=$char->def[0]." + ".$char->def[1]?></td></tr>
	<tr><td class="support" style="text-align:right">Mdef :</td><td class="support"><?=$char->def[2]." + ".$char->def[3]?></td></tr>
	<tr><td class="charge" style="text-align:right">handle :</td><td class="charge"><?=$handle?> / <?=$char->GetHandle()?></td></tr>
	</table>
	<form action="?char=<?=$_GET["char"]?>" method="post">
	<table>
	<tr><td class="align-right">
	Weapon :</td><td><input type="radio" class="vcent" name="spot" value="weapon"><?ShowItemDetail(LoadItemData($char->weapon));?>
	</td></tr><tr><td class="align-right">
	Shield :</td><td><input type="radio" class="vcent" name="spot" value="shield"><?ShowItemDetail(LoadItemData($char->shield));?>
	</td></tr><tr><td class="align-right">
	Armor :</td><td><input type="radio" class="vcent" name="spot" value="armor"><?ShowItemDetail(LoadItemData($char->armor));?>
	</td></tr><tr><td class="align-right">
	Item :</td><td><input type="radio" class="vcent" name="spot" value="item"><?ShowItemDetail(LoadItemData($char->item));?>
	</td></tr></tbody>
	</table>
	<input type="submit" class="btn" name="remove" value="Remove">
	<input type="submit" class="btn" name="remove_all" value="Remove All">
	</form>
	</div>
<?

		// ������ǽ��ʪɽ�� ////////////////////////////////
		if($JobData["equip"])
			$EquipAllow	= array_flip($JobData["equip"]);//������ǽ��ʪ�ꥹ��(ȿž)
		else
			$EquipAllow	= array();//������ǽ��ʪ�ꥹ��(ȿž)
		$Equips		= array("Weapon"=>"2999","Shield"=>"4999","Armor"=>"5999","Item"=>"9999");

		print("<div style=\"padding:15px 15px 0 15px\">\n");
		print("\t<div class=\"bold u\">Stock & Allowed to Equip</div>\n");
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$EquipList	= new JS_ItemList();
			$EquipList->SetID("equip");
			$EquipList->SetName("type_equip");
			// JS����Ѥ��ʤ���
			if($this->no_JS_itemlist)
				$EquipList->NoJS();
			reset($this->item);//���줬̵���������ѹ�����ɽ������ʤ�
			foreach($this->item as $key => $val) {
				$item	= LoadItemData($key);
				// �����Ǥ��ʤ��ΤǼ�
				if(!isset( $EquipAllow[ $item["type"] ] ))
					continue;
				$head	= '<input type="radio" name="item_no" value="'.$key.'" class="vcent">';
				$head	.= ShowItemDetail($item,$val,true)."<br />";
				$EquipList->AddItem($item,$head);
			}
			print($EquipList->GetJavaScript("list0"));
			print($EquipList->ShowSelect());
			print('<form action="?char='.$_GET["char"].'" method="post">'."\n");
			print('<div id="list0">'.$EquipList->ShowDefault().'</div>'."\n");
			print('<input type="submit" class="btn" name="equip_item" value="Equip">'."\n");
			print("</form>\n");
		} else {
			print("No items.<br />\n");
		}
		print("</div>\n");

		
		/*
		print("\t<table><tbody><tr><td colspan=\"2\">\n");
		print("\t<span class=\"bold u\">Stock & Allowed to Equip</span></td></tr>\n");
		if($this->item):
			reset($this->item);//���줬̵���������ѹ�����ɽ������ʤ�
			foreach($Equips as $key => $val) {
				print("\t<tr><td class=\"align-right\" valign=\"top\">\n");
				print("\t{$key} :</td><td>\n");
				while( substr(key($this->item),0,4) <= $val && substr(current($this->item),0,4) !== false ) {
					$item	= LoadItemData(key($this->item));
					if(!isset( $EquipAllow[ $item["type"] ] )) {
						next($this->item);
						continue;
					}
					print("\t");
					print('<input type="radio" class="vcent" name="item_no" value="'.key($this->item).'">');
					print("\n\t");
					print(current($this->item)."x");
					ShowItemDetail($item);
					print("<br>\n");
					next($this->item);
				}
				print("\t</td></tr>\n");
			}
		else:
			print("<tr><td>No items.</td></tr>");
		endif;
		print("\t</tbody></table>\n");
		*/
	?>
	<form action="?char=<?=$_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>Skill <a href="?manual#skill" target="_blank" class="a0">?</a></h4><?

		// ������ɽ�� //////////////////////////////////////
		//include(DATA_SKILL);//ActionPattern�˰�ư
		include_once(DATA_SKILL_TREE);
		if($char->skill) {
			print('<div class="u bold">Mastered</div>');
			print("<table><tbody>");
			foreach($char->skill as $val) {
				print("<tr><td>");
				$skill	= LoadSkillData($val);
				ShowSkillDetail($skill);
				print("</td></tr>");
			}
			print("</tbody></table>");
			print('<div class="u bold">Learn New</div>');
			print("Skill Point : {$char->skillpoint}");
			print("<table><tbody>");
			$tree	= LoadSkillTree($char);
			foreach(array_diff($tree,$char->skill) as $val) {
				print("<tr><td>");
				$skill	= LoadSkillData($val);
				ShowSkillDetail($skill,1);
				print("</td></tr>");
			}
			print("</tbody></table>");
			//dump($char->skill);
			//dump($tree);
			print('<input type="submit" class="btn" name="learnskill" value="Learn">'."\n");
			print('<input type="hidden" name="learnskill" value="1">'."\n");
		}
		// ž�� ////////////////////////////////////////////
		if($CanChange) {
			?>

	</form>
	<form action="?char=<?=$_GET["char"]?>" method="post" style="padding:0 15px">
	<h4>ClassChange</h4>
	<table><tbody><tr><?
			foreach($CanChange as $job) {
				print("<td valign=\"bottom\" style=\"padding:5px 30px;text-align:center\">");
				$JOB	= LoadJobData($job);
				print('<img src="'.IMG_CHAR.$JOB["img_".($char->gender?"female":"male")].'">'."<br />\n");//����
				print('<input type="radio" value="'.$job.'" name="job">'."<br />\n");
				print($JOB["name_".($char->gender?"female":"male")]);
				print("</td>");
			}
			?>

	</tr></tbody></table>
	<input type="submit" class="btn" name="classchange" value="ClassChange">
	<input type="hidden" name="classchange" value="1"><?
		}
	?>

	</form>
	<?//����¾�����
		print('<div  style="padding:15px">');
		foreach($this->char as $key => $val) {
			//if($key == $_GET["char"]) continue;//ɽ���業��饹���å�
			echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
		}
		print('</div>');
	}
//////////////////////////////////////////////////
//	('A`)...
	function CharTestDoppel() {
		if(!$_POST["TestBattle"]) return 0;

		$char	= $this->char[$_GET["char"]];
		$this->DoppelBattle(array($char));
	}
//////////////////////////////////////////////////
//	�ɥåڥ륲�󥬡����臘��
	function DoppelBattle($party,$turns=10) {
		//$enemy	= $party;
		//���줬̵����PHP4or5 �ǰ㤦��̤ˤʤ��Ǥ�
		//$enemy	= unserialize(serialize($enemy));
		// ��
		foreach($party as $key => $char) {
			$enemy[$key]	= new char();
			$enemy[$key]->SetCharData(get_object_vars($char));
			
		}
		foreach($enemy as $key => $doppel) {
			//$doppel->judge	= array();//�����Ȥ���ȥɥåڥ뤬��ư���ʤ���
			$enemy[$key]->ChangeName("�˥�".$doppel->name);
		}
		//dump($enemy[0]->judge);
		//dump($party[0]->judge);

		include(CLASS_BATTLE);
		$battle	= new battle($party,$enemy);
		$battle->SetTeamName($this->name,"�ɥåڥ�");
		$battle->LimitTurns($turns);//���祿�������10
		$battle->NoResult();
		$battle->Process();//��Ʈ����
		return true;
	}
//////////////////////////////////////////////////
//
	function SimuBattleProcess() {
		if($_POST["simu_battle"]) {
			$this->MemorizeParty();//�ѡ��ƥ�������
			// ��ʬ�ѡ��ƥ���
			foreach($this->char as $key => $val) {//�����å����줿��ĥꥹ��
				if($_POST["char_".$key])
					$MyParty[]	= $this->char[$key];
			}
			if( count($MyParty) === 0) {
				ShowError('��Ʈ����ˤϺ���1��ɬ��',"margin15");
				return false;
			} else if(5 < count($MyParty)) {
				ShowError('��Ʈ�˽Ф��륭����5�ͤޤ�',"margin15");
				return false;
			}
			$this->DoppelBattle($MyParty,50);
			return true;
		}
	}
//////////////////////////////////////////////////
//	
	function SimuBattleShow($message=false) {
		print('<div style="margin:15px">');
		ShowError($message);
		print('<span class="bold">�ϵ���</span>');
		print('<h4>Teams</h4></div>');
		print('<form action="'.INDEX.'?simulate" method="post">');
		$this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" name="simu_battle" value="Battle !">
	<input type="reset" class="btn" value="Reset"><br>
	Save this party:<input type="checkbox" name="memory_party" value="1">
	</div></form>
		<?
	}
//////////////////////////////////////////////////
//	
	function HuntShow() {
		include(DATA_LAND);
		include(DATA_LAND_APPEAR);
		print('<div style="margin:15px">');
		print('<h4>CommonMonster</h4>');
		print('<div style="margin:0 20px">');

		$mapList	= LoadMapAppear($this);
		foreach($mapList as $map) {
			list($land)	= LandInformation($map);
			print("<p><a href=\"?common={$map}\">{$land[name]}</a>");
			//print(" ({$land[proper]})");
			print("</p>");
		}

		// Union
		print("</div>\n");
		$files	= glob(UNION."*");
		if($files) {
			include(CLASS_UNION);
			include(DATA_MONSTER);
			foreach($files as $file) {
				$UnionMons	= new union($file);
				if($UnionMons->is_Alive())
					$Union[]	= $UnionMons;
			}
		}
		if($Union) {
			print('<h4>UnionMonster</h4>');
			$result = $this->CanUnionBattle();
			if($result !== true) {
				$left_minute	= floor($result/60);
				$left_second	= $result%60;
				print('<div style="margin:0 20px">');
				print('Time left to next battle : <span class="bold">'.$left_minute. ":".sprintf("%02d",$left_second)."</span>");
				print("</div>");
			}
			print("</div>");
			$this->ShowCharacters($Union);
		} else {
			print("</div>");
		}

		// union
		print("<div style=\"margin:0 15px\">\n");
		print("<h4>Union Battle Log <a href=\"?ulog\">��ɽ��</a></h4>\n");
		print("<div style=\"margin:0 20px\">\n");
		$log	= @glob(LOG_BATTLE_UNION."*");
		foreach(array_reverse($log) as $file) {
			$limit++;
			BattleLogDetail($file,"UNION");
			if(15 <= $limit)
				break;
		}
		print("</div></div>\n");
	}
//////////////////////////////////////////////////
//	��󥹥�����ɽ��
	function MonsterShow() {
		$land_id	= $_GET["common"];
		include(DATA_LAND);
		include_once(DATA_LAND_APPEAR);
		// �ޤ��Ԥ��ʤ��ޥåפʤΤ˹Ԥ����Ȥ�����
		if(!in_array($_GET["common"],LoadMapAppear($this))) {
			print('<div style="margin:15px">not appeared or not exist</div>');
			return false;
		}
		list($land,$monster_list)	= LandInformation($land_id);
		if(!$land || !$monster_list) {
			print('<div style="margin:15px">fail to load</div>');
			return false;
		}

		print('<div style="margin:15px">');
		ShowError($message);
		print('<span class="bold">'.$land["name"].'</span>');
		print('<h4>Teams</h4></div>');
		print('<form action="'.INDEX.'?common='.$_GET["common"].'" method="post">');
		$this->ShowCharacters($this->char,"CHECKBOX",explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" name="monster_battle" value="Battle !">
	<input type="reset" class="btn" value="Reset"><br>
	Save this party:<input type="checkbox" name="memory_party" value="1">
	</div></form>
<?
		include(DATA_MONSTER);
		include(CLASS_MONSTER);
		foreach($monster_list as $id =>$val) {
			if($val[1])
				$monster[]	= new monster(CreateMonster($id));
		}
		print('<div style="margin:15px"><h4>MonsterAppearance</h4></div>');
		$this->ShowCharacters($monster,"MONSTER",$land["land"]);
	}

//////////////////////////////////////////////////
//	��󥹥����Ȥ���Ʈ
	function MonsterBattle() {
		if($_POST["monster_battle"]) {
			$this->MemorizeParty();//�ѡ��ƥ�������
			// ���Υޥåפ��廊�뤫�ɤ�����ǧ���롣
			include_once(DATA_LAND_APPEAR);
			$land	= LoadMapAppear($this);
			if(!in_array($_GET["common"],$land)) {
				ShowError("�ޥåפ��и�����̵��","margin15");
				return false;
			}

			// Time��­��Ƥ뤫�ɤ�����ǧ����
			if($this->time < NORMAL_BATTLE_TIME) {
				ShowError("Time ��­ (ɬ�� Time:".NORMAL_BATTLE_TIME.")","margin15");
				return false;
			}
			// ��ʬ�ѡ��ƥ���
			foreach($this->char as $key => $val) {//�����å����줿��ĥꥹ��
				if($_POST["char_".$key])
					$MyParty[]	= $this->char[$key];
			}
			if( count($MyParty) === 0) {
				ShowError('��Ʈ����ˤϺ���1��ɬ��',"margin15");
				return false;
			} else if(5 < count($MyParty)) {
				ShowError('��Ʈ�˽Ф��륭����5�ͤޤ�',"margin15");
				return false;
			}
			// Ũ�ѡ��ƥ���(�ޤ��ϰ�ɤ)
			include(DATA_LAND);
			include(DATA_MONSTER);
			list($Land,$MonsterList)	= LandInformation($_GET["common"]);
			$EneNum	= $this->EnemyNumber($MyParty);
			$EnemyParty	= $this->EnemyParty($EneNum,$MonsterList);

			$this->WasteTime(NORMAL_BATTLE_TIME);//���֤ξ���
			include(CLASS_BATTLE);
			$battle	= new battle($MyParty,$EnemyParty);
			$battle->SetBackGround($Land["land"]);//�ط�
			$battle->SetTeamName($this->name,$Land["name"]);
			$battle->Process();//��Ʈ����
			$battle->SaveCharacters();//�����ǡ�����¸
			list($UserMoney)	= $battle->ReturnMoney();//��Ʈ��������׶��
			//��������䤹
			$this->GetMoney($UserMoney);
			//��Ʈ������¸
			if($this->record_btl_log)
				$battle->RecordLog();

			// �����ƥ��������
			if($itemdrop	= $battle->ReturnItemGet(0)) {
				$this->LoadUserItem();
				foreach($itemdrop as $itemno => $amount)
					$this->AddItem($itemno,$amount);
				$this->SaveUserItem();
			}

			//dump($itemdrop);
			//dump($this->item);
			return true;
		}
	}

//////////////////////////////////////////////////
	function ItemProcess() {
	}

//////////////////////////////////////////////////
//	
	function ItemShow() {
		?>
		<div style="margin:15px">
		<h4>Items</h4>
		<div style="margin:0 20px">
<?
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$goods	= new JS_ItemList();
			$goods->SetID("my");
			$goods->SetName("type");
			// JS����Ѥ��ʤ���
			if($this->no_JS_itemlist)
				$goods->NoJS();
			//$goods->ListTable("<table>");
			//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$string	= ShowItemDetail($item,$val,1)."<br />";
				//$string	= "<tr><td>".$no."</td><td>".ShowItemDetail($item,$val,1)."</td></tr>";
				$goods->AddItem($item,$string);
			}
			print($goods->GetJavaScript("list"));
			print($goods->ShowSelect());
			print('<div id="list">'.$goods->ShowDefault().'</div>');
		} else {
			print("No items.");
		}
		print("</div></div>");
	}
//////////////////////////////////////////////////
//	Ź�إå�
	function ShopHeader() {
		?>
<div style="margin:15px">
<h4>Ź</h4>

<div style="width:600px">
<div style="float:left;width:50px;">
<img src="<?=IMG_CHAR?>ori_002.gif" />
</div>
<div style="float:right;width:550px;">
����ä��㤤�ޤ���<br />
<a href="?menu=buy">�㤦</a> / <a href="?menu=sell">���</a><br />
<a href="?menu=work">����Х���</a>
</div>
<div style="clear:both"></div>
</div>

</div><?
	}
//////////////////////////////////////////////////
//
	function ShopProcess() {
		switch(true) {
			case($_POST["partjob"]):
				if($this->WasteTime(100)) {
					$this->GetMoney(500);
					ShowResult("Ư���� ".MoneyFormat(500)." ���äȤ���!","margin15");
					return true;
				} else {
					ShowError("���֤�̵����Ư���ʤ�Ƥ�ä����ʤ���","margin15");
					return false;
				}
			case($_POST["shop_buy"]):
				$ShopList	= ShopList();//��äƤ��Υǡ���
				if($_POST["item_no"] && in_array($_POST["item_no"],$ShopList)) {
					if(ereg("^[0-9]",$_POST["amount"])) {
						$amount	= (int)$_POST["amount"];
						if($amount == 0)
							$amount	= 1;
					} else {
						$amount	= 1;
					}
					$item	= LoadItemData($_POST["item_no"]);
					$need	= $amount * $item["buy"];//������ɬ�פʤ���
					if($this->TakeMoney($need)) {// ���������뤫��Ƚ�ꡣ
						$this->AddItem($_POST["item_no"],$amount);
						$this->SaveUserItem();
						if(1 < $amount) {
							$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
							ShowResult("{$img}{$item[name]} ��{$amount}�� �������� (".MoneyFormat($item["buy"])." x{$amount} = ".MoneyFormat($need).")","margin15");
							return true;
						} else {
							$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
							ShowResult("{$img}{$item[name]} ��������� (".MoneyFormat($need).")","margin15");
							return true;
						}
					} else {//�����­
						ShowError("�����­(Need ".MoneyFormat($need).")","margin15");
						return false;
					}
				}
				break;
			case($_POST["shop_sell"]):
				if($_POST["item_no"] && $this->item[$_POST["item_no"]]) {
					if(ereg("^[0-9]",$_POST["amount"])) {
						$amount	= (int)$_POST["amount"];
						if($amount == 0)
							$amount	= 1;
					} else {
						$amount	= 1;
					}
					// �ä����Ŀ�(Ķ�ᤷ�������Τ��ɤ�)
					$DeletedAmount	= $this->DeleteItem($_POST["item_no"],$amount);
					$item	= LoadItemData($_POST["item_no"]);
					$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
					$this->GetMoney($price*$DeletedAmount);
					$this->SaveUserItem();
					if($DeletedAmount != 1)
						$add	= " x{$DeletedAmount}";
					$img	= "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
					ShowResult("{$img}{$item[name]}{$add} �� ".MoneyFormat($price*$DeletedAmount)." ����ä�","margin15");
					return true;
				}
				break;
		}
	}
//////////////////////////////////////////////////
//	
	function ShopShow($message=NULL) {
		?>
	<div style="margin:15px">
	<?=ShowError($message)?>
	<h4>Goods List</h4>
	<div style="margin:0 20px">
	<?
		include(CLASS_JS_ITEMLIST);
		$ShopList	= ShopList();//��äƤ��Υǡ���

		$goods	= new JS_ItemList();
		$goods->SetID("JS_buy");
		$goods->SetName("type_buy");
		// JS����Ѥ��ʤ���
		if($this->no_JS_itemlist)
			$goods->NoJS();
		foreach($ShopList as $no) {
			$item	= LoadItemData($no);
			$string	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
			$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($item["buy"])."</span>".ShowItemDetail($item,false,1)."<br />";
			$goods->AddItem($item,$string);
		}
		print($goods->GetJavaScript("list_buy"));
		print($goods->ShowSelect());

		print('<form action="?shop" method="post">'."\n");
		print('<div id="list_buy">'.$goods->ShowDefault().'</div>'."\n");
		print('<input type="submit" class="btn" name="shop_buy" value="Buy">'."\n");
		print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)<br />'."\n");
		print('<input type="hidden" name="shop_buy" value="1">');
		print('</form></div>'."\n");

		print("<h4>My Items<a name=\"sell\"></a></h4>\n");//���ʪ���
		print('<div style="margin:0 20px">'."\n");
		if($this->item) {
			$goods	= new JS_ItemList();
			$goods->SetID("JS_sell");
			$goods->SetName("type_sell");
			// JS����Ѥ��ʤ���
			if($this->no_JS_itemlist)
				$goods->NoJS();
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
				$string	= '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
				$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";
				$head	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">'.MoneyFormat($item["buy"]);
				$goods->AddItem($item,$string);
			}
			print($goods->GetJavaScript("list_sell"));
			print($goods->ShowSelect());
	
			print('<form action="?shop" method="post">'."\n");
			print('<div id="list_sell">'.$goods->ShowDefault().'</div>'."\n");
			print('<input type="submit" class="btn" name="shop_sell" value="Sell">');
			print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)'."\n");
			print('<input type="hidden" name="shop_sell" value="1">');
			print('</form>'."\n");
		} else {
			print("No items");
		}
		print("</div>\n");
/*
		if($this->item) {
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
				print('<input type="radio" class="vcent" name="item_no" value="'.$no.'">');
				print(MoneyFormat($price));
				print("&nbsp;&nbsp;&nbsp;{$val}x");
				ShowItemDetail($item);
				print("<br>");
			}
		} else
			print("No items.<br>");
		print('Amount <input type="text" name="amount" style="width:50px" class="text vcent">(input if 2 or more)<br />'."\n");
		print('<input type="submit" class="btn vcent" name="shop_sell" value="Sell">');
		print('<input type="hidden" name="shop_sell" value="1">');
		print('</form>');*/
		?>
<form action="?shop" method="post">
<h4>Work</h4>
<div style="margin:0 20px">
Ź�ǥ���Х��Ȥ��Ƥ�������ޤ�...<br />
<input type="submit" class="btn" name="partjob" value="Work at Shop">
Get <?=MoneyFormat("500")?> for 100Time.
</form></div></div><?
	}

//////////////////////////////////////////////////
	function ShopBuyProcess() {
		//dump($_POST);
		if(!$_POST["ItemBuy"])
			return false;

		print("<div style=\"margin:15px\">");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6" style="text-align:center">����</td>'.
		'<td class="td6" style="text-align:center">��</td>'.
		'<td class="td6" style="text-align:center">��</td>'.
		'<td class="td6" style="text-align:center">�����ƥ�</td></tr>'."\n");
		$moneyNeed	= 0;
		$ShopList	= ShopList();
		foreach($ShopList as $itemNo) {
			if(!$_POST["check_".$itemNo])
				continue;
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			$amount	= (int)$_POST["amount_".$itemNo];
			if($amount < 0)
				$amount	= 0;
			
			//print("$itemNo x $Deleted<br>");
			$buyPrice	= $item["buy"];
			$Total	= $amount * $buyPrice;
			$moneyNeed	+= $Total;
			print("<tr><td class=\"td7\">");
			print(MoneyFormat($buyPrice)."\n");
			print("</td><td class=\"td7\">");
			print("x {$amount}\n");
			print("</td><td class=\"td7\">");
			print("= ".MoneyFormat($Total)."\n");
			print("</td><td class=\"td8\">");
			print(ShowItemDetail($item)."\n");
			print("</td></tr>\n");
			$this->AddItem($itemNo,$amount);
		}
		print("<tr><td colspan=\"4\" class=\"td8\">��� : ".MoneyFormat($moneyNeed)."</td></tr>");
		print("</table>\n");
		print("</div>");
		if($this->TakeMoney($moneyNeed)) {
			$this->SaveUserItem();
			return true;
		} else {
			ShowError("���⤬­��ޤ���","margin15");
			return false;
		}
	}
//////////////////////////////////////////////////
	function ShopBuyShow() {
		print('<div style="margin:15px">'."\n");
		print("<h4>�㤦</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print('<form action="?menu=buy" method="post">'."\n");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6"></td>'.
		'<td style="text-align:center" class="td6">����</td>'.
		'<td style="text-align:center" class="td6">��</td>'.
		'<td style="text-align:center" class="td6">�����ƥ�</td></tr>'."\n");
		$ShopList	= ShopList();
		foreach($ShopList as $itemNo) {
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">'."\n");
			print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// ����
			$price	= $item["buy"];
			print(MoneyFormat($price));
			print("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="1" style="width:60px" class="text">'."\n");
			print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print(ShowItemDetail($item));
			print("</td></tr>\n");
		}
		print("</table>\n");
		print('<input type="submit" name="ItemBuy" value="Buy" class="btn">'."\n");
		print("</form>\n");

		print("</div>\n");
	}
//////////////////////////////////////////////////
	function ShopSellProcess() {
		//dump($_POST);
		if(!$_POST["ItemSell"])
			return false;

		$GetMoney	= 0;
		print("<div style=\"margin:15px\">");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6" style="text-align:center">����</td>'.
		'<td class="td6" style="text-align:center">��</td>'.
		'<td class="td6" style="text-align:center">��</td>'.
		'<td class="td6" style="text-align:center">�����ƥ�</td></tr>'."\n");
		foreach($this->item as $itemNo => $amountHave) {
			if(!$_POST["check_".$itemNo])
				continue;
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			$amount	= (int)$_POST["amount_".$itemNo];
			if($amount < 0)
				$amount	= 0;
			$Deleted	= $this->DeleteItem($itemNo,$amount);
			//print("$itemNo x $Deleted<br>");
			$sellPrice	= ItemSellPrice($item);
			$Total	= $Deleted * $sellPrice;
			$getMoney	+= $Total;
			print("<tr><td class=\"td7\">");
			print(MoneyFormat($sellPrice)."\n");
			print("</td><td class=\"td7\">");
			print("x {$Deleted}\n");
			print("</td><td class=\"td7\">");
			print("= ".MoneyFormat($Total)."\n");
			print("</td><td class=\"td8\">");
			print(ShowItemDetail($item)."\n");
			print("</td></tr>\n");
		}
		print("<tr><td colspan=\"4\" class=\"td8\">��� : ".MoneyFormat($getMoney)."</td></tr>");
		print("</table>\n");
		print("</div>");
		$this->SaveUserItem();
		$this->GetMoney($getMoney);
		return true;
	}
//////////////////////////////////////////////////
	function ShopSellShow() {
		print('<div style="margin:15px">'."\n");
		print("<h4>���</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print('<form action="?menu=sell" method="post">'."\n");
		print("<table cellspacing=\"0\">\n");
		print('<tr><td class="td6"></td>'.
		'<td style="text-align:center" class="td6">����</td>'.
		'<td style="text-align:center" class="td6">��</td>'.
		'<td style="text-align:center" class="td6">�����ƥ�</td></tr>'."\n");
		foreach($this->item as $itemNo => $amount) {
			$item	= LoadItemData($itemNo);
			if(!$item) continue;
			print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">'."\n");
			print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// ����
			$price	= ItemSellPrice($item);
			print(MoneyFormat($price));
			print("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="'.$amount.'" style="width:60px" class="text">'."\n");
			print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print(ShowItemDetail($item,$amount));
			print("</td></tr>\n");
		}
		print("</table>\n");
		print('<input type="submit" name="ItemSell" value="Sell" class="btn" />'."\n");
		print('<input type="hidden" name="ItemSell" value="1" />'."\n");
		print("</form>\n");

		print("</div>\n");
	}
//////////////////////////////////////////////////
//	����Х��Ƚ���
	function WorkProcess() {
		if($_POST["amount"]) {
			$amount	= (int)$_POST["amount"];
			// 1�ʾ�10�ʲ�
			if(0 < $amount && $amount < 11) {
				$time	= $amount * 100;
				$money	= $amount * 500;
				if($this->WasteTime($time)) {
					ShowResult(MoneyFormat($money)." ���äȤ�����","margin15");
					$this->GetMoney($money);
					return true;
				} else {
					ShowError("���֤�­��ޤ���","margin15");
					return false;
				}
			}
		}
	}
//////////////////////////////////////////////////
//	����Х���ɽ��
	function WorkShow() {
		?>
<div style="margin:15px">
<h4>����Х��Ȥ��롪</h4>
<form method="post" action="?menu=work">
<p>1�� 100Time<br />
��Ϳ : <?=MoneyFormat(500)?>/��</p>
<select name="amount">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select><br />
<input type="submit" value="Work" class="btn" />
</form>
</div>
<?
	}
//////////////////////////////////////////////////
	function RankProcess(&$Ranking) {

		// RankBattle
		if($_POST["ChallengeRank"]) {
			if(!$this->party_rank) {
				ShowError("�����ब���ꤵ��Ƥ��ޤ���","margin15");
				return false;
			}
			$result	= $this->CanRankBattle();
			if(is_array($result)) {
				ShowError("�Ե����֤��ޤ��ĤäƤޤ�","margin15");
				return false;
			}

			/*
				$BattleResult = 0;//����
				$BattleResult = 1;//����
				$BattleResult = "d";//��ʬ
			*/
			//list($message,$BattleResult)	= $Rank->Challenge(&$this);
			$Result	= $Ranking->Challenge(&$this);

			//if($Result === "Battle")
			//	$this->RankRecord($BattleResult,"CHALLENGE",false);

			/*
			// ���Ԥˤ�äƼ��ޤǤ���Ʈ�λ��֤����ꤹ��
			//����
			if($BattleResult === 0) {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);

			//����
			} else if($BattleResult === 1) {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			//��ʬ��
			} else if($BattleResult === "d") {
				$this->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

			}
			*/

			return $Result;// ��Ʈ���Ƥ���� $Result = "Battle";
		}

		// ��󥭥��ѤΥ�������Ͽ
		if($_POST["SetRankTeam"]) {
			$now	= time();
			// �ޤ�������֤��ĤäƤ��롣
			if(($now - $this->rank_set_time) < RANK_TEAM_SET_TIME) {
				$left	= RANK_TEAM_SET_TIME - ($now - $this->rank_set_time);
				$day	= floor($left / 3600 / 24);
				$hour	= floor($left / 3600)%24;
				$min	= floor(($left % 3600)/60);
				$sec	= floor(($left % 3600)%60);
				ShowError("�����������ޤ� ���� �Ĥ� {$day}�� �� {$hour}���� {$min}ʬ {$sec}��","margin15");
				return false;
			}
			foreach($this->char as $key => $val) {//�����å����줿��ĥꥹ��
				if($_POST["char_".$key])
					$checked[]	= $key;
			}
			// ���ꥭ������¿�������ʤ�����
			if(count($checked) == 0 || 5 < count($checked)) {
				ShowError("������Ϳ��� 1�Ͱʾ� 5�Ͱʲ� �Ǥʤ��Ȥ����ʤ�","margin15");
				return false;
			}

			$this->party_rank	= implode("<>",$checked);
			$this->rank_set_time	= $now;
			ShowResult("���������� ��λ","margin15");
			return true;
		}
	}
//////////////////////////////////////////////////
//	
	function RankShow(&$Ranking) {

		//$ProcessResult	= $this->RankProcess($Ranking);// array();

		//��Ʈ���Ԥ�줿�Τ�ɽ�����ʤ���
		//if($ProcessResult === "BATTLE")
		//	return true;

		// �����������λĤ���ַ׻�
		$now	= time();
		if( ($now - $this->rank_set_time) < RANK_TEAM_SET_TIME) {
			$left	= RANK_TEAM_SET_TIME - ($now - $this->rank_set_time);
			$hour	= floor($left / 3600);
			$min	= floor(($left % 3600)/60);
			$left_mes	= "<div class=\"bold\">{$hour}Hour {$min}minutes left to set again.</div>\n";
			$disable	= " disabled";
		}
			?>

	<div style="margin:15px">
	<?=ShowError($message)?>
	<form action="?menu=rank" method="post">
	<h4>��󥭥�(Ranking) - <a href="?rank">����󥭥󥰤򸫤�</a>&nbsp;<a href="?manual#ranking" target="_blank" class="a0">?</a></h4>
	<?php
		// ĩ��Ǥ��뤫�ɤ���(���֤ηв��)
		$CanRankBattle	= $this->CanRankBattle();
		if($CanRankBattle !== true) {
			print('<p>Time left to Next : <span class="bold">');
			print($CanRankBattle[0].":".sprintf("%02d",$CanRankBattle[1]).":".sprintf("%02d",$CanRankBattle[2]));
			print("</span></p>\n");
			$disableRB	= " disabled";
		}

		print("<div style=\"width:100%;padding-left:30px\">\n");
		print("<div style=\"float:left;width:50%\">\n");
		print("<div class=\"u\">TOP 5</div>\n");
		$Ranking->ShowRanking(0,4);
		print("</div>\n");
		print("<div style=\"float:right;width:50%\">\n");
		print("<div class=\"u\">NEAR 5</div>\n");
		$Ranking->ShowRankingRange($this->id,5);
		print("</div>\n");
		print("<div style=\"clear:both\"></div>\n");
		print("</div>\n");

		// ������
		//$Rank->dump();
		/*
		print("<table><tbody><tr><td style=\"padding:0 50px 0 0\">\n");
		print("<div class=\"bold u\">RANKING</div>");
		$Rank->ShowRanking(0,10);
		print("</td><td>");
		print("<div class=\"bold u\">Nearly</div>");
		$Rank->ShowNearlyRank($this->id);
		print("</td></tr></tbody></table>\n");
		*/
	?>
	<input type="submit" class="btn" value="challenge!" name="ChallengeRank" style="width:160px"<?=$disableRB?> />
	</form>
	<form action="?menu=rank" method="post">
	<h4>����������(Team Setting)</h4>
	<p>��󥭥����ѤΥ��������ꡣ<br />
	���������ꤷ����������襤�ޤ���</p>
	</div>
	<?$this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_rank));?>

	<div style="margin:15px">
	<?=$left_mes?>
	<input type="submit" class="btn" style="width:160px" value="SetTeam"<?=$disable?> />
	<input type="hidden" name="SetRankTeam" value="1" />
	<p>����塢<?=$reset=floor(RANK_TEAM_SET_TIME/(60*60))?>���֤��ѹ��Ǥ��ޤ���<br />Team setting disabled after <?=$reset?>hours once set.</p>
	</form>
	</div>
<?
	}
//////////////////////////////////////////////////
	function RecruitProcess() {

		// ���ѿ��³�
		if( MAX_CHAR <= count($this->char) )
			return false;

		include(DATA_BASE_CHAR);
		if($_POST["recruit"]) {
			// �����Υ�����
			switch($_POST["recruit_no"]) {
				case "1": $hire = 2000; $charNo	= 1; break;
				case "2": $hire = 2000; $charNo	= 2; break;
				case "3": $hire = 2500; $charNo	= 3; break;
				case "4": $hire = 4000; $charNo	= 4; break;
				default:
					ShowError("����� ̤����","margin15");
					return false;
			}
			// ̾������
			if($_POST["recruit_name"]) {
				if(is_numeric(strpos($_POST["recruit_name"],"\t")))
					return "error.";
				$name	= trim($_POST["recruit_name"]);
				$name	= stripslashes($name);
				$len	= strlen($name);
				if ( 0 == $len || 16 < $len ) {
					ShowError("̾����û�����뤫Ĺ�����Ǥ�","margin15");
					return false;
				}
				$name	= htmlspecialchars($name,ENT_QUOTES);
			} else {
				ShowError("̾��������Ǥ�","margin15");
				return false;
			}
			//����
			if( !isset($_POST["recruit_gend"]) ) {
				ShowError("���� ̤����","margin15");
				return false;
			} else {
				$Gender	= $_POST["recruit_gend"]?"��":"��";
			}
			// �����ǡ����򥯥饹�������
			
			$plus	= array("name"=>"$name","gender"=>$_POST["recruit_gend"]);
			$char	= new char();
			$char->SetCharData(array_merge(BaseCharStatus($charNo),$plus));
			//���Ѷ�
			if($hire <= $this->money) {
				$this->TakeMoney($hire);
			} else {
				ShowError("���⤬­��ޤ���","margin15");
				return false;
			}
			// ��������¸����
			$char->SaveCharData($this->id);
			ShowResult($char->Name()."($char->job_name:{$Gender}) ����֤ˤʤä���","margin15");
			return true;
		}
	}

//////////////////////////////////////////////////
//	
	function RecruitShow() {
		if( MAX_CHAR <= $this->CharCount() ) {
			?>

	<div style="margin:15px">
	<p>Maximum characters.<br>
	Need to make a space to recruit new character.</p>
	<p>���������³���ã���Ƥ��ޤ���<br>
	�����������������ˤ϶�����ɬ�פǤ���</p>
	</div><?
			return false;
		}
		include_once(CLASS_MONSTER);
		$char[0]	= new char();
		$char[0]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
		$char[1]	= new char();
		$char[1]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
		$char[2]	= new char();
		$char[2]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
		$char[3]	= new char();
		$char[3]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));
		$char[4]	= new char();
		$char[4]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"0")));
		$char[5]	= new char();
		$char[5]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"1")));
		$char[6]	= new char();
		$char[6]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"0")));
		$char[7]	= new char();
		$char[7]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"1")));
		?>

	<form action="?recruit" method="post" style="margin:15px">
	<h4>Sort of New Character</h4>
	<table cellspacing="0"><tbody><tr>
	<td class="td1" style="text-align:center">
	<?$char[0]->ShowImage()?><?$char[1]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="1" style="margin:3px"><br>
	<?=MoneyFormat(2000)?></td>
	<td class="td1" style="text-align:center">
	<?$char[2]->ShowImage()?><?$char[3]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="2" style="margin:3px"><br>
	<?=MoneyFormat(2000)?></td>
	<td class="td1" style="text-align:center">
	<?$char[4]->ShowImage()?><?$char[5]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="3" style="margin:3px"><br>
	<?=MoneyFormat(2500)?></td>
	<td class="td1" style="text-align:center">
	<?$char[6]->ShowImage()?><?$char[7]->ShowImage()?><br>
	<input type="radio" name="recruit_no" value="4" style="margin:3px"><br>
	<?=MoneyFormat(4000)?></td>
	</tr><tr>
	<td class="td4" style="text-align:center">
	Warrior</td>
	<td class="td5" style="text-align:center">
	Sorcerer</td>
	<td class="td4" style="text-align:center">
	Priest</td>
	<td class="td5" style="text-align:center">
	Hunter</td>
	</tr>
	</tbody></table>

	<h4>New Character's Name &amp; Gender</h4>
	<table><tbody><tr><td valign="top">
	<input type="text" class="text" name="recruit_name" style="width:160px" maxlength="16"><br>
	<div style="margin:5px 0px">
	<input type="radio" class="vcent" name="recruit_gend" value="0">male
	<input type="radio" class="vcent" name="recruit_gend" value="1" style="margin-left:15px;">female</div>
	<input type="submit" class="btn" name="recruit" value="Recruit">
	<input type="hidden" class="btn" name="recruit" value="Recruit">
	</td><td valign="top">
	<p>1 to 16 letters.<br>
	Japanese characters count as 2.<br>
	���ܸ��1ʸ�� = 2 letter.
	</p>
	</td></tr></tbody></table>
	</form><?
	}
//////////////////////////////////////////////////
//	���결��ϣ�إå�
	function SmithyRefineHeader() {
	?>
<div style="margin:15px">
<h4>���결(Smithy)</h4>

<div style="width:600px">
<div style="float:left;width:80px;">
<img src="<?=IMG_CHAR?>mon_053r.gif" />
</div>
<div style="float:right;width:520px;">
�����Ǥ�&nbsp;�����ƥ����ϣ���Ǥ��뤼��<br />
��ϣ����ʪ����ϣ���������Ǥ��졣<br />
����������Ƥ���Ǥ�ϻ��Ƥʤ�����<br />
�郎��äƤ� <span class="bold">���˼</span> ��<a href="?menu=create">���å�</a>����
</div>
<div style="clear:both"></div>
</div>
<h4>�����ƥ����ϣ<a name="refine"></a></h4>
<div style="margin:0 20px"><?
	}
//////////////////////////////////////////////////
//	���결����(��ϣ)
	function SmithyRefineProcess() {
		if(!$_POST["refine"])
			return false;
		if(!$_POST["item_no"]) {
			ShowError("Select Item.");
			return false;
		}
		// �����ƥब�ɤ߹���ʤ����
		if(!$item	= LoadItemData($_POST["item_no"])) {
			ShowError("Failed to load item data.");
			return false;
		}
		// �����ƥ�������Ƥ��ʤ����
		if(!$this->item[$_POST["item_no"]]) {
			ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// ��������ꤵ��Ƥ��ʤ����
		if($_POST["timesA"] < $_POST["timesB"])
			$times	= $_POST["timesB"];
		else
			$times	= $_POST["timesA"];
		if(!$times || $times < 1 || (REFINE_LIMIT) < $times ) {
			ShowError("times?");
			return false;
		}
		include(CLASS_SMITHY);
		$obj_item	= new Item($_POST["item_no"]);
		// ���Υ����ƥब��ϣ�Ǥ��ʤ����
		if(!$obj_item->CanRefine()) {
			ShowError("Cant refine \"{$item[name]}\"");
			return false;
		}
		// ����������ϣ��Ϥ�����
		$this->DeleteItem($_POST["item_no"]);// �����ƥ�Ͼä��뤫�Ѳ�����ΤǾä�
		$Price	= round($item["buy"]/2);
		// ������ϣ����Ĵ����
		if( REFINE_LIMIT < ($item["refine"] + $times) ) {
			$times	= REFINE_LIMIT - $item["refine"];
		}
		$Trys	= 0;
		for($i=0; $i<$times; $i++) {
			// ��������
			if($this->TakeMoney($Price)) {
				$MoneySum	+= $Price;
				$Trys++;
				if(!$obj_item->ItemRefine()) {//��ϣ����(false=���ԤʤΤǽ�λ����)
					break;
				}
			// ���⤬����Ǥʤ��ʤä���硣
			} else {
				ShowError("Not enough money.<br />\n");
				$this->AddItem($obj_item->ReturnItem());
				break;
			}
			// ��������ϣ�����������ä���硣
			if($i == ($times - 1)) {
				$this->AddItem($obj_item->ReturnItem());
			}
		}
		print("Money Used : ".MoneyFormat($Price)." x ".$Trys." = ".MoneyFormat($MoneySum)."<br />\n");
		$this->SaveUserItem();
		return true;
		/*// ���⤬­��Ƥ뤫�׻�
		$Price	= round($item["buy"]/2);
		$MoneyNeed	= $times * $Price;
		if($this->money < $MoneyNeed) {
			ShowError("Your request needs ".MoneyFormat($MoneyNeed));
			return false;
		}*/
		
	}
//////////////////////////////////////////////////
//	���결ɽ��
	function SmithyRefineShow() {
		// ����ϣ����
		//$Result	= $this->SmithyRefineProcess();

		// ��ϣ��ǽ��ʪ��ɽ��
		if($this->item) {
			include(CLASS_JS_ITEMLIST);
			$possible	= CanRefineType();
			$possible	= array_flip($possible);
			//�������Ƭ���ͤ�"0"�ʤΤ�1�ˤ���(isset�Ȥ鷺��true�ˤ��뤿��)
			$possible[key($possible)]++;

			$goods	= new JS_ItemList();
			$goods->SetID("my");
			$goods->SetName("type");

			$goods->ListTable("<table cellspacing=\"0\">");// �ơ��֥륿���ΤϤ��ޤ�
			$goods->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">��ϣ��</td><td class=\"align-center td9\">Item</td></tr>"); // �ơ��֥�κǽ�ȺǸ�ιԤ�ɽ���������ġ�

			// JS����Ѥ��ʤ���
			if($this->no_JS_itemlist)
				$goods->NoJS();
			foreach($this->item as $no => $val) {
				$item	= LoadItemData($no);
				// ��ϣ��ǽ��ʪ����ɽ�������롣
				if(!$possible[$item["type"]])
					continue;
				$price	= $item["buy"]/2;
				// NoTable
	//			$string	= '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
	//			$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";

				$string	= '<tr>';
				$string	.= '<td class="td7"><input type="radio" class="vcent" name="item_no" value="'.$no.'">';
				$string	.= '</td><td class="td7">'.MoneyFormat($price).'</td><td class="td8">'.ShowItemDetail($item,$val,1)."<td>";
				$string	.= "</tr>";

				$goods->AddItem($item,$string);
			}
			// JavaScript��ʬ�ν񤭽Ф�
			print($goods->GetJavaScript("list"));
			print('��ϣ��ǽ��ʪ����');
			// ����Υ��쥯�ȥܥå���
			print($goods->ShowSelect());
			print('<form action="?menu=refine" method="post">'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			// ��ϣ����λ���
			print('��� : <select name="timesA">'."\n");
			for($i=1; $i<11; $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			// �ꥹ�Ȥ�ɽ��
			print('<div id="list">'.$goods->ShowDefault().'</div>'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			print('<input type="hidden" value="1" name="refine">'."\n");
			// ��ϣ����λ���
			print('��� : <select name="timesB">'."\n");
			for($i=1; $i<(REFINE_LIMIT+1); $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			print('</form>'."\n");
		} else {
			print("No items<br />\n");
		}
		print("</div>\n");
	?>
	</div>
<?
	}
//////////////////////////////////////////////////
//	���결 ���� �إå�
	function SmithyCreateHeader() {
		?>
<div style="margin:15px">
<h4>���결(Smithy)<a name="sm"></a></h4>
<div style="width:600px">
<div style="float:left;width:80px;">
<img src="<?=IMG_CHAR?>mon_053rz.gif" />
</div>
<div style="float:right;width:520px;">
�����Ǥ�&nbsp;�����ƥ������Ǥ��뤼��<br />
�������󤬻��äƤ��Ǻफ���줽������������뤼��<br />
���̤��Ǻ�����������ü��������뤼��<br />
������äƤ� <span class="bold">��ϣ��˼</span> ��<a href="?menu=refine">���å�</a>����<br />
<a href="#mat">����Ǻ����</a>
</div>
<div style="clear:both"></div>
</div>
<h4>�����ƥ������<a name="refine"></a></h4>
<div style="margin:0 15px"><?
	}
//////////////////////////////////////////////////
//	�������
	function SmithyCreateProcess() {
		if(!$_POST["Create"]) return false;

		// �����ƥब���򤵤�Ƥ��ʤ�
		if(!$_POST["ItemNo"]) {
			ShowError("����륢���ƥ������Ǥ�������");
			return false;
		}

		// �����ƥ���ɤ�
		if(!$item	= LoadItemData($_POST["ItemNo"])) {
			ShowError("error12291703");
			return false;
		}

		// ���륢���ƥफ�ɤ������������
		if(!HaveNeeds($item,$this->item)) {
			ShowError($item["name"]." ��������Ǻब­��ޤ���");
			return false;
		}

		// �ɲ��Ǻ�
		if($_POST["AddMaterial"]) {
			// ������Ƥ��ʤ����
			if(!$this->item[$_POST["AddMaterial"]]) {
				ShowError("�����ɲ��Ǻ�Ϥ���ޤ���");
				return false;
			}
			// �ɲ��Ǻ�Υ����ƥ�ǡ���
			$ADD	= LoadItemData($_POST["AddMaterial"]);
			$this->DeleteItem($_POST["AddMaterial"]);
		}

		// �����ƥ������
		// ����򸺤餹
		//$Price	= $item["buy"];
		$Price	= 0;
		if(!$this->TakeMoney($Price)) {
			ShowError("���⤬­��ޤ���".MoneyFormat($Price)."ɬ�פǤ���");
			return false;
		}
		// �Ǻ�򸺤餹
		foreach($item["need"] as $M_item => $M_amount) {
			$this->DeleteItem($M_item,$M_amount);
		}
		include(CLASS_SMITHY);
		$item	= new item($_POST["ItemNo"]);
		$item->CreateItem();
		// �ղø���
		if($ADD["Add"])
			$item->AddSpecial($ADD["Add"]);
		// �Ǥ��������ƥ����¸����
		$done	= $item->ReturnItem();
		$this->AddItem($done);
		$this->SaveUserItem();

		print("<p>");
		print(ShowItemDetail(LoadItemData($done)));
		
		print("\n<br />���Ǥ�������</p>\n");
		return true;
	}
//////////////////////////////////////////////////
//	����ɽ��
	function SmithyCreateShow() {
		//$result	= $this->SmithyCreateProcess();

		$CanCreate	= CanCreate($this);
		include(CLASS_JS_ITEMLIST);
		$CreateList	= new JS_ItemList();
		$CreateList->SetID("create");
		$CreateList->SetName("type_create");

		$CreateList->ListTable("<table cellspacing=\"0\">");// �ơ��֥륿���ΤϤ��ޤ�
		$CreateList->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">������</td><td class=\"align-center td9\">Item</td></tr>"); // �ơ��֥�κǽ�ȺǸ�ιԤ�ɽ���������ġ�

		// JS����Ѥ��ʤ���
		if($this->no_JS_itemlist)
			$CreateList->NoJS();
		foreach($CanCreate as $item_no) {
			$item	= LoadItemData($item_no);
			if(!HaveNeeds($item,$this->item))// �Ǻ���­�ʤ鼡
				continue;
			// NoTable
			//$head	= '<input type="radio" name="ItemNo" value="'.$item_no.'">'.ShowItemDetail($item,false,1,$this->item)."<br />";
			//$CreatePrice	= $item["buy"];
			$CreatePrice	= 0;//
			$head	= '<tr><td class="td7"><input type="radio" name="ItemNo" value="'.$item_no.'"></td>';
			$head	.= '<td class="td7">'.MoneyFormat($CreatePrice).'</td><td class="td8">'.ShowItemDetail($item,false,1,$this->item)."</td>";
			$CreateList->AddItem($item,$head);
		}
		if($head) {
			print($CreateList->GetJavaScript("list"));
			print($CreateList->ShowSelect());
		?>
<form action="?menu=create" method="post">
<div id="list"><?=$CreateList->ShowDefault()?></div>
<input type="submit" class="btn" name="Create" value="Create">
<input type="reset" class="btn" value="Reset">
<input type="hidden" name="Create" value="1"><br />
<?
		// �ɲ��Ǻ��ɽ��
		print('<div class="bold u" style="margin-top:15px">�ɲ��Ǻ�</div>'."\n");
		for($item_no=7000; $item_no<7200; $item_no++) {
			if(!$this->item["$item_no"])
				continue;
			if($item	= LoadItemData($item_no)) {
				print('<input type="radio" name="AddMaterial" value="'.$item_no.'" class="vcent">');
				print(ShowItemDetail($item,$this->item["$item_no"],1)."<br />\n");
			}
		}
		?>
<input type="submit" class="btn" name="Create" value="Create">
<input type="reset" class="btn" value="Reset">
</form>
<?
		} else {
			print("���󤿤����äƤ��Ǻस�㲿���줽����̵���ʡ�");
		}


		// ����Ǻ����
		print("</div>\n");
		print("<h4>����Ǻ����<a name=\"mat\"></a> <a href=\"#sm\">��</a></h4>");
		print("<div style=\"margin:0 15px\">");
		for($i=6000; $i<7000; $i++) {
			if(!$this->item["$i"])
				continue;
			$item	= LoadItemData($i);
			ShowItemDetail($item,$this->item["$i"]);
			print("<br />\n");
		}
		?>
</div>
</div>
<?
		return $result;
	}
//////////////////////////////////////////////////
//	���С��ˤʤ����
	function AuctionJoinMember() {
		if(!$_POST["JoinMember"])
			return false;
		if($this->item["9000"]) {//���˲��
			//ShowError("You are already a member.\n");
			return false;
		}
		// ���⤬­��ʤ�
		if(!$this->TakeMoney(round(START_MONEY * 1.10))) {
			ShowError("���⤬­��ޤ���<br />\n");
			return false;
		}
		// �����ƥ��­��
		$this->AddItem(9000);
		$this->SaveUserItem();
		$this->SaveData();
		ShowResult("��������������ˤʤ�ޤ�����<br />\n");
		return true;
	}
//////////////////////////////////////////////////
//	
	function AuctionEnter() {
		if($this->item["9000"])//�������������С�������
			return true;
		else
			return false;
	}
//////////////////////////////////////////////////
//	������������ɽ��(header)
	function AuctionHeader() {
		?>
<div style="margin:15px 0 0 15px">
<h4>�����������(Auction)</h4>
<div style="margin-left:20px">

<div style="width:500px">
<div style="float:left;width:50px;">
<img src="<?=IMG_CHAR?>ori_003.gif" />
</div>
<div style="float:right;width:450px;"><?

		$this->AuctionJoinMember();
		if($this->AuctionEnter()) {
			print("�����ͤϲ���ڤ򤪻����Ǥ��͡�<br />\n");
			print("�褦�����������������ء�<br />\n");
			print("<a href=\"#log\">��Ͽ�β���</a>\n");
		} else {
			print("�����������ؤν��ʡ������ˤ�����ɬ�פǤ���<br />\n");
			print("�������&nbsp;".MoneyFormat(round(START_MONEY * 1.10))."&nbsp;�Ǥ���<br />\n");
			print("���񤷤ޤ���?<br />\n");
			print('<form action="" method="post">'."\n");
			print('<input type="submit" value="���񤹤�" name="JoinMember" class="btn"/>'."\n");
			print("</form>\n");
		}
		if(!AUCTION_TOGGLE)
			ShowError("��ǽ�����");
		if(!AUCTION_EXHIBIT_TOGGLE)
			ShowError("���������");
		?>
</div>
<div style="clear:both"></div>
</div>
</div>
<h4>�����ƥ� �����������(Item Auction)</h4>
<div style="margin-left:20px"><?
	}
//////////////////////////////////////////////////
//	������������ɽ��
	function AuctionFoot(&$ItemAuction) {
		?>
</div>
<a name="log"></a>
<h4>������������(AuctionLog)</h4>
<div style="margin-left:20px">
<?$ItemAuction->ShowLog();?>
</div><?
	}
//////////////////////////////////////////////////
//	��������
	function AuctionItemBiddingProcess(&$ItemAuction) {
		if(!$this->AuctionEnter())
			return false;
		if(!isset($_POST["ArticleNo"]))
			return false;

		$ArticleNo	= $_POST["ArticleNo"];
		$BidPrice	= (int)$_POST["BidPrice"];
		if($BidPrice < 1) {
			ShowError("�������ʤ˸�꤬����ޤ���");
			return false;
		}
		// �ޤ������椫�ɤ�����ǧ���롣
		if(!$ItemAuction->ItemArticleExists($ArticleNo)) {
			ShowError("���ζ����ʤν��ʤ���ǧ�Ǥ��ޤ���");
			return false;
		}
		// ��ʬ�������Ǥ���ͤ��ɤ����γ�ǧ
		if(!$ItemAuction->ItemBidRight($ArticleNo,$this->id)) {
			ShowError("No.".$ArticleNo."&nbsp;�������Ѥߤ����ʼԤǤ���");
			return false;
		}
		// �����������ʤ��äƤ��ʤ�����ǧ���롣
		$Bottom	= $ItemAuction->ItemBottomPrice($ArticleNo);
		if($BidPrice < $Bottom) {
			ShowError("�����������ʤ򲼲�äƤ��ޤ���");
			ShowError("����������:".MoneyFormat($BidPrice)."&nbsp;������������:".MoneyFormat($Bottom));
			return false;
		}
		// ����äƤ뤫��ǧ����
		if(!$this->TakeMoney($BidPrice)) {
			ShowError("����⤬­��ʤ��褦�Ǥ���");
			return false;
		}

		// �ºݤ��������롣
		if($ItemAuction->ItemBid($ArticleNo,$BidPrice,$this->id,$this->name)) {
			ShowResult("No:{$ArticleNo}&nbsp;��&nbsp;".MoneyFormat($BidPrice)."&nbsp;���������ޤ�����<br />\n");
			return true;
		}
	}
//////////////////////////////////////////////////
//	�����ƥ४����������ѤΥ��֥������Ȥ��ɤ���֤�
/*
	function AuctionItemLoadData() {
		include(CLASS_AUCTION);
		$ItemAuction	= new Auction(item);
		$ItemAuction->ItemCheckSuccess();// ���䤬��λ������ʪ��Ĵ�٤�
		$ItemAuction->UserSaveData();// �����ʤȶ�ۤ��ID���ۤä���¸����

		return $ItemAuction;
	}
*/
//////////////////////////////////////////////////
//	�����ѥե�����(����)
	function AuctionItemBiddingForm(&$ItemAuction) {

		if(!AUCTION_TOGGLE)
			return false;

		// �����ѥե�����ˤ����ܥ���
		if($this->AuctionEnter()) {
			// ���񤷤Ƥ���硡�����Ǥ���褦��
			$ItemAuction->ItemSortBy($_GET["sort"]);
			$ItemAuction->ItemShowArticle2(true);

			if(AUCTION_EXHIBIT_TOGGLE) {
				print("<form action=\"?menu=auction\" method=\"post\">\n");
				print('<input type="submit" value="Put Auction" name="ExhibitItemForm" class="btn" style="width:160px">'."\n");
				print("</form>\n");
			}

		} else {
			// �����Ǥ��ʤ�
			$ItemAuction->ItemShowArticle2(false);
		}
	}
//////////////////////////////////////////////////
//	�����ƥ���ʽ���
	function AuctionItemExhibitProcess(&$ItemAuction) {

		if(!AUCTION_EXHIBIT_TOGGLE)
			return "BIDFORM";// �������

		// ��¸���ʤ��ǽ��ʥꥹ�Ȥ�ɽ������
		if(!$this->AuctionEnter())
			return "BIDFORM";
		if(!$_POST["PutAuction"])
			return "BIDFORM";

		if(!$_POST["item_no"]) {
			ShowError("Select Item.");
			return false;
		}
		// ���å����ˤ��30�ô֤ν��ʵ���
		$SessionLeft	= 30 - (time() - $_SESSION["AuctionExhibit"]);
		if($_SESSION["AuctionExhibit"] && 0 < $SessionLeft) {
			ShowError("Wait {$SessionLeft}seconds to ReExhibit.");
			return false;
		}
		// Ʊ�����ʿ�������
		if(AUCTION_MAX <= $ItemAuction->ItemAmount()) {
			ShowError("���ʿ����³���ã���Ƥ��ޤ���(".$ItemAuction->ItemAmount()."/".AUCTION_MAX.")");
			return false;
		}
		// ��������
		if(!$this->TakeMoney(500)) {
			ShowError("Need ".MoneyFormat(500)." to exhibit auction.");
			return false;
		}
		// �����ƥब�ɤ߹���ʤ����
		if(!$item	= LoadItemData($_POST["item_no"])) {
			ShowError("Failed to load item data.");
			return false;
		}
		// �����ƥ�������Ƥ��ʤ����
		if(!$this->item[$_POST["item_no"]]) {
			ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// ���Υ����ƥब���ʤǤ��ʤ����
		$possible	= CanExhibitType();
		if(!$possible[$item["type"]]) {
			ShowError("Cant put \"{$item[name]}\" to the Auction");
			return false;
		}
		// ���ʻ��֤γ�ǧ
		if(	!(	$_POST["ExhibitTime"] === '1' ||
				$_POST["ExhibitTime"] === '3' ||
				$_POST["ExhibitTime"] === '6' ||
				$_POST["ExhibitTime"] === '12' ||
				$_POST["ExhibitTime"] === '18' ||
				$_POST["ExhibitTime"] === '24') ) {
			var_dump($_POST);
			ShowError("time?");
			return false;
		}
		// ���̤γ�ǧ
		if(ereg("^[0-9]",$_POST["Amount"])) {
			$amount	= (int)$_POST["Amount"];
			if($amount == 0)
				$amount	= 1;
		} else {
			$amount	= 1;
		}
		// ���餹(��������¿�����ꤵ�줿��礽�ο���Ĵ�᤹��)
		$_SESSION["AuctionExhibit"]	= time();//���å�����2�Ž��ʤ��ɤ�
		$amount	= $this->DeleteItem($_POST["item_no"],$amount);
		$this->SaveUserItem();

		// ���ʤ���
		// $ItemAuction	= new Auction(item);// (2008/2/28:�����Ȳ�)
		$ItemAuction->ItemAddArticle($_POST["item_no"],$amount,$this->id,$_POST["ExhibitTime"],$_POST["StartPrice"],$_POST["Comment"]);
		print($item["name"]."&nbsp;��&nbsp;{$amount}��&nbsp;���ʤ��ޤ�����");
		return true;
	}
//////////////////////////////////////////////////
//	�����ѥե�����
	function AuctionItemExhibitForm() {

		if(!AUCTION_EXHIBIT_TOGGLE)
			return false;

		include(CLASS_JS_ITEMLIST);
		$possible	= CanExhibitType();
		?>
<div class="u bold">������ˡ</div>
<ol>
<li>���ʤ��륢���ƥ�����򤷤ޤ���</li>
<li>2�İʾ���ʤ����硢���̤����Ϥ��ޤ���</li>
<li>���ʤ��Ƥ�����֤�Ĺ������ꤷ�ޤ���</li>
<li>���ϲ��ʤ���ꤷ�ޤ�(����̵�� = 0)</li>
<li>�����Ȥ���������Ϥ��ޤ���</li>
<li>�������롣</li>
</ol>
<div class="u bold">��ջ���</div>
<ul>
<li>���ʤˤ�&nbsp;������Ȥ���$500&nbsp;ɬ�פǤ���</li>
<li>�����Ȥ������Ƥ���ʤ�����</li>
</ul>
<a href="?menu=auction">���������</a>
</div>
<h4>���ʤ���</h4>
<div style="margin-left:20px">
<div class="u bold">���ʲ�ǽ��ʪ����</div>
<?
		if(!$this->item) {
			print("No items<br />\n");
			return false;
		}
		$ExhibitList	= new JS_ItemList();
		$ExhibitList->SetID("auc");
		$ExhibitList->SetName("type_auc");
		// JS����Ѥ��ʤ���
		if($this->no_JS_itemlist)
			$ExhibitList->NoJS();
		foreach($this->item as $no => $amount) {
			$item	= LoadItemData($no);
			if(!$possible[$item["type"]])
				continue;
			$head	= '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
			$head	.= ShowItemDetail($item,$amount,1)."<br />";
			$ExhibitList->AddItem($item,$head);
		}
		print($ExhibitList->GetJavaScript("list"));
		print($ExhibitList->ShowSelect());
		?>
<form action="?menu=auction" method="post">
<div id="list"><?=$ExhibitList->ShowDefault()?></div>
<table><tr><td style="text-align:right">
����(Amount) :</td><td><input type="text" name="Amount" class="text" style="width:60px" value="1" /><br />
</td></tr><tr><td style="text-align:right">
����(Time) :</td><td>
<select name="ExhibitTime">
<option value="24" selected>24 hour</option>
<option value="18">18 hour</option>
<option value="12">12 hour</option>
<option value="6">6 hour</option>
<option value="3">3 hour</option>
<option value="1">1 hour</option>
</select>
</td></tr><tr><td>
���ϲ���(Start Price) :</td><td><input type="text" name="StartPrice" class="text" style="width:240px" maxlength="10"><br />
</td></tr><tr><td style="text-align:right">
������(Comment) :</td><td>
<input type="text" name="Comment" class="text" style="width:240px" maxlength="40">
</td></tr><tr><td></td><td>
<input type="submit" class="btn" value="Put Auction" name="PutAuction" style="width:240px"/>
<input type="hidden" name="PutAuction" value="1">
</td></tr></table>
</form>

<?
		
	}
//////////////////////////////////////////////////
//	Union��󥹥����ν���
	function UnionProcess() {

		if($this->CanUnionBattle() !== true) {
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
			$extra = INDEX;
			header("Location: http://$host$uri/$extra?hunt");
			exit;
		}

		if(!$_POST["union_battle"])
			return false;
		$Union	= new union();
		// �ݤ���Ƥ��뤫��¸�ߤ��ʤ���硣
		if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
			return false;
		}
		// ��˥����󥹥����Υǡ���
		$UnionMob	= CreateMonster($Union->MonsterNumber);
		$this->MemorizeParty();//�ѡ��ƥ�������
		// ��ʬ�ѡ��ƥ���
		foreach($this->char as $key => $val) {//�����å����줿��ĥꥹ��
			if($_POST["char_".$key]) {
				$MyParty[]	= $this->char[$key];
				$TotalLevel	+= $this->char[$key]->level;//��ʬPT�ι�ץ�٥�
			}
		}
		// ��ץ�٥�����
		if($UnionMob["LevelLimit"] < $TotalLevel) {
			ShowError('��ץ�٥륪���С�('.$TotalLevel.'/'.$UnionMob["LevelLimit"].')',"margin15");
			return false;
		}
		if( count($MyParty) === 0) {
			ShowError('��Ʈ����ˤϺ���1��ɬ��',"margin15");
			return false;
		} else if(5 < count($MyParty)) {
			ShowError('��Ʈ�˽Ф��륭����5�ͤޤ�',"margin15");
			return false;
		}
		if(!$this->WasteTime(UNION_BATTLE_TIME)) {
			ShowError('Time Shortage.',"margin15");
			return false;
		}

		// ŨPT��

		// ������Ũ�ѡ��ƥ���
		if($UnionMob["SlaveAmount"])
			$EneNum	= $UnionMob["SlaveAmount"] + 1;//PT���Ф�Ʊ����������
		else
			$EneNum	= 5;// Union�ޤ��5�˸��ꤹ�롣

		if($UnionMob["SlaveSpecify"])
			$EnemyParty	= $this->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);
		else
			$EnemyParty	= $this->EnemyParty($EneNum-1, $Union->Slave, $UnionMob["SlaveSpecify"]);

		// unionMob������Τ��褽����������
		array_splice($EnemyParty,floor(count($EnemyParty)/2),0,array($Union));

		$this->UnionSetTime();

		include(CLASS_BATTLE);
		$battle	= new battle($MyParty,$EnemyParty);
		$battle->SetUnionBattle();
		$battle->SetBackGround($Union->UnionLand);//�ط�
		//$battle->SetTeamName($this->name,"Union:".$Union->Name());
		$battle->SetTeamName($this->name,$UnionMob["UnionName"]);
		$battle->Process();//��Ʈ����

		$battle->SaveCharacters();//�����ǡ�����¸
			list($UserMoney)	= $battle->ReturnMoney();//��Ʈ��������׶��
			$this->GetMoney($UserMoney);//��������䤹
			$battle->RecordLog("UNION");
			// �����ƥ��������
			if($itemdrop	= $battle->ReturnItemGet(0)) {
				$this->LoadUserItem();
				foreach($itemdrop as $itemno => $amount)
					$this->AddItem($itemno,$amount);
				$this->SaveUserItem();
			}

		return true;
	}
//////////////////////////////////////////////////
//	Union��󥹥�����ɽ��
	function UnionShow() {
		if($this->CanUnionBattle() !== true) {
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']));
			$extra = INDEX;
			header("Location: http://$host$uri/$extra?hunt");
			exit;
		}
		//if($Result	= $this->UnionProcess())
		//	return true;
		print('<div style="margin:15px">'."\n");
		print("<h4>Union Monster</h4>\n");
		$Union	= new union();
		// �ݤ���Ƥ��뤫��¸�ߤ��ʤ���硣
		if(!$Union->UnionNumber($_GET["union"]) || !$Union->is_Alive()) {
			ShowError("Defeated or not Exists.");
			return false;
		}
		print('</div>');
		$this->ShowCharacters(array($Union),false,"sea");
		print('<div style="margin:15px">'."\n");
		print("<h4>Teams</h4>\n");
		print("</div>");
		print('<form action="'.INDEX.'?union='.$_GET["union"].'" method="post">');
		$this->ShowCharacters($this->char,CHECKBOX,explode("<>",$this->party_memo));
			?>
	<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" value="Battle !">
	<input type="hidden" name="union_battle" value="1">
	<input type="reset" class="btn" value="Reset"><br>
	Save this party:<input type="checkbox" name="memory_party" value="1">
	</div></form>
<?
	}
//////////////////////////////////////////////////
//	Į��ɽ��
	function TownShow() {
		include(DATA_TOWN);
		print('<div style="margin:15px">'."\n");
		print("<h4>��</h4>");
		print('<div class="town">'."\n");
		print("<ul>\n");
		$PlaceList	= TownAppear($this);
		// Ź
		if($PlaceList["Shop"]) {
			?>
<li>Ź(Shop)
<ul>
<li><a href="?menu=buy">�㤦(Buy)</a></li>
<li><a href="?menu=sell">���(Sell)</a></li>
<li><a href="?menu=work">����Х���</a></li>
</ul>
</li>
<?
		}
		// ������
		if($PlaceList["Recruit"])
			print("<li><p><a href=\"?recruit\">�ͺశ����(Recruit)</a></p></li>");
		// ���결
		if($PlaceList["Smithy"]) {
			?>
<li>���결(Smithy)
<ul>
<li><a href="?menu=refine">��ϣ��˼(Refine)</a></li>
<li><a href="?menu=create">���˼(Create)</a></li>
</ul>
</li>
<?
		}
		// �������������
		if($PlaceList["Auction"] && AUCTION_TOGGLE)
			print("<li><a href=\"?menu=auction\">�������������(Auction)</li>");
		// ��������
		if($PlaceList["Colosseum"])
			print("<li><a href=\"?menu=rank\">��������(Colosseum)</a></li>");
		print("</ul>\n");
		print("</div>\n");
		print("<h4>����</h4>");
		$this->TownBBS();
		print("</div>\n");
	}

//////////////////////////////////////////////////
//	���̤�1�ԷǼ���
	function TownBBS() {
		$file	= BBS_TOWN;
	?>
<form action="?town" method="post">
<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?
		if(!file_exists($file))
			return false;
		$log	= file($file);
		if($_POST["message"] && strlen($_POST["message"]) < 121) {
			$_POST["message"]	= htmlspecialchars($_POST["message"],ENT_QUOTES);
			$_POST["message"]	= stripslashes($_POST["message"]);

			$name	= "<span class=\"bold\">{$this->name}</span>";
			$message	= $name." > ".$_POST["message"];
			if($this->UserColor)
				$message	= "<span style=\"color:{$this->UserColor}\">".$message."</span>";
			$message	.= " <span class=\"light\">(".date("Mj G:i").")</span>\n";
			array_unshift($log,$message);
			while(50 < count($log))
				array_pop($log);
			WriteFile($file,implode(null,$log));
		}
		foreach($log as $mes)
			print(nl2br($mes));
	}
//////////////////////////////////////////////////
	function SettingProcess() {
		if($_POST["NewName"]) {
			$NewName	= $_POST["NewName"];
			if(is_numeric(strpos($NewName,"\t"))) {
				ShowError('error1');
				return false;
			}
			$NewName	= trim($NewName);
			$NewName	= stripslashes($NewName);
			if (!$NewName) {
				ShowError('Name is blank.');
				return false;
			}
			$length	= strlen($NewName);
			if ( 0 == $length || 16 < $length) {
				ShowError('1 to 16 letters?');
				return false;
			}
			$userName	= userNameLoad();
			if(in_array($NewName,$userName)) {
				ShowError("����̾���ϻ��Ѥ���Ƥ��롣","margin15");
				return false;
			}
			if(!$this->TakeMoney(NEW_NAME_COST)) {
				ShowError('money not enough');
				return false;
			}
			$OldName	= $this->name;
			$NewName	= htmlspecialchars($NewName,ENT_QUOTES);
			if($this->ChangeName($NewName)) {
				ShowResult("Name Changed ({$OldName} -> {$NewName})","margin15");
				//return false;
				userNameAdd($NewName);
				return true;
			} else {
				ShowError("?");//̾����Ʊ����
				return false;
			}
		}

		if($_POST["setting01"]) {
			if($_POST["record_battle_log"])
				$this->record_btl_log	= 1;
			else
				$this->record_btl_log	= false;

			if($_POST["no_JS_itemlist"])
				$this->no_JS_itemlist	= 1;
			else
				$this->no_JS_itemlist	= false;
		}
		if($_POST["color"]) {
			if(	strlen($_POST["color"]) != 6 &&
				!ereg("^[0369cf]{6}",$_POST["color"]))
				return "error 12072349";
			$this->UserColor	= $_POST["color"];
			ShowResult("Setting changed.","margin15");
			return true;
		}
	}
//////////////////////////////////////////////////
//	����ɽ������
	function SettingShow() {
		print('<div style="margin:15px">'."\n");
		if($this->record_btl_log) $record_btl_log	= " checked";
		if($this->no_JS_itemlist) $no_JS_itemlist	= " checked";
		?>
<h4>Setting</h4>
<form action="?setting" method="post">
<table><tbody>
<tr><td><input type="checkbox" name="record_battle_log" value="1" <?=$record_btl_log?>></td><td>��Ʈ���ε�Ͽ</td></tr>
<tr><td><input type="checkbox" name="no_JS_itemlist" value="1" <?=$no_JS_itemlist?>></td><td>�����ƥ�ꥹ�Ȥ�JavaScript��Ȥ�ʤ�</td></tr>
</tbody></table>
<!--<tr><td>None</td><td><input type="checkbox" name="none" value="1"></td></tr>-->
Color : <?
		$color	= file(COLOR_FILE);
		print('<select name="color" class="bgcolor">'."\n");
		foreach($color as $value) {
			$value	= trim($value);
			print("<option value=\"{$value}\" style=\"color:{$value}\" ".($this->UserColor == $value?" selected":"").">");
			print("SampleColor</option>\n");
		}
		print('</select>');
	?><br />
<input type="submit" class="btn" name="setting01" value="modify" style="width:100px">
<input type="hidden" name="setting01" value="1">
</form>
<h4>Logout</h4>
<form action="<?=INDEX?>" method="post">
<input type="submit" class="btn" name="logout" value="logout" style="width:100px">
</form>
<h4>������̾���ѹ�</h4>
<form action="?setting" method="post">
���� : <?=MoneyFormat(NEW_NAME_COST)?><br />
16ʸ���ޤ�(����=2ʸ��)<br />
������̾�� : <input type="text" class="text" name="NewName" size="20">
<input type="submit" class="btn" value="change" style="width:100px">
</form>
<h4>æ�и�</h4>
<div class="u">���ǡ����κ��</div>
<form action="?setting" method="post">
PassWord : <input type="text" class="text" name="deletepass" size="20">
<input type="submit" class="btn" name="delete" value="delete" style="width:100px">
</form>
</div><?
		return $Result;
	}
////////// Show //////////////////////////////////////////////////////
/*
 * ShowCharStat
 * ShowHunt
 * ShowItem
 * ShowShop
 * ShowRank
 * ShowRecruit
 * ShowSetting
 */

//////////////////////////////////////////////////
//	��Ʈ�������򤷤����С��򵭲�����
	function MemorizeParty() {
		if($_POST["memory_party"]) {
			//$temp	= $this->party_memo;//���Ū�˵���
			//$this->party_memo	= array();
			foreach($this->char as $key => $val) {//�����å����줿��ĥꥹ��
				if($_POST["char_".$key])
					//$this->party_memo[]	 = $key;
					$PartyMemo[]	= $key;
			}
			//if(5 < count($this->party_memo) )//5�Ͱʾ������
			//	$this->party_memo	= $temp;
			if(0 < count($PartyMemo) && count($PartyMemo) < 6)
				$this->party_memo	= implode("<>",$PartyMemo);
		}
	}

//////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////
//	�����󤷤�����
	function LoginMain() {
		$this->ShowTutorial();
		$this->ShowMyCharacters();
		RegularControl($this->id);
	}
//////////////////////////////////////////////////
//	���奦�ȥꥢ��
	function ShowTutorial() {
		$last	= $this->last;
		$start	= substr($this->start,0,10);
		$term	= 60*60*1;
		if( ($last - $start) < $term) {
			?>
	<div style="margin:5px 15px">
	<a href="?tutorial">���塼�ȥꥢ��</a> - ��Ʈ�δ���(��Ͽ��,1���֤���ɽ������ޤ�)
	</div>

<?
		}
	}

//////////////////////////////////////////////////
//	��ʬ�Υ�����ɽ������
	function ShowMyCharacters($array=NULL) {// $array �� �����������
		if(!$this->char) return false;
		$divide	= (count($this->char)<CHAR_ROW ? count($this->char) : CHAR_ROW);
		$width	= floor(100/$divide);//�ƥ��벣��

		print('<table cellspacing="0" style="width:100%"><tbody><tr>');//����100%
		foreach($this->char as $val) {
			if( $i%CHAR_ROW==0 && $i != 0 )
				print("\t</tr><tr>\n");
			print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//�������˱�����%�ǳƥ���ʬ��
			$val->ShowCharLink($array);
			print("</td>\n");
			$i++;
		}
		print("</tr></tbody></table>");
	}
//////////////////////////////////////////////////
//	������ɽ�Ȥߤ�ɽ������
	function ShowCharacters($characters,$type=null,$checked=null) {
		if(!$characters) return false;
		$divide	= (count($characters)<CHAR_ROW ? count($characters) : CHAR_ROW);
		$width	= floor(100/$divide);//�ƥ��벣��

		if($type == "CHECKBOX") {
print <<< HTML
<script type="text/javascript">
<!--
function toggleCheckBox(id) {
id0 = "box" + id;
\$("box" + id).checked = \$("box" + id).checked?false:true;
Element.toggleClassName("text"+id,'unselect');
}
// -->
</script>
HTML;
		}

		print('<table cellspacing="0" style="width:100%"><tbody><tr>');//����100%
		foreach($characters as $char) {
			if( $i%CHAR_ROW==0 && $i != 0 )
				print("\t</tr><tr>\n");
			print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//�������˱�����%�ǳƥ���ʬ��

			/*-------------------*/
			switch(1) {
				case ($type === MONSTER):
					$char->ShowCharWithLand($checked); break;
				case ($type === CHECKBOX):
					if(!is_array($checked)) $checked = array();
					if(in_array($char->birth,$checked))
						$char->ShowCharRadio($char->birth," checked");
					else
						$char->ShowCharRadio($char->birth);
					break;
				default:
					$char->ShowCharLink();
			}

			print("</td>\n");
			$i++;
		}
		print("</tr></tbody></table>");
	}

//////////////////////////////////////////////////
//	��ʬ�Υǡ����ȥ��å�����ä�
	function DeleteMyData() {
		if($this->pass == $this->CryptPassword($_POST["deletepass"]) ) {
			$this->DeleteUser();
			$this->name	= NULL;
			$this->pass	= NULL;
			$this->id	= NULL;
			$this->islogin= false;
			unset($_SESSION["id"]);
			unset($_SESSION["pass"]);
			setcookie("NO","");
			$this->LoginForm();
			return true;
		}
	}

//////////////////////////////////////////////////
//	�ѿ���ɽ��
	function Debug() {
		if(DEBUG)
			print("<pre>".print_r(get_object_vars($this),1)."</pre>");
	}

//////////////////////////////////////////////////
//	���å��������ɽ�����롣
	function ShowSession() {
		echo "this->id:$this->id<br>";
		echo "this->pass:$this->pass<br>";
		echo "SES[id]:$_SESSION[id]<br>";
		echo "SES[pass]:$_SESSION[pass]<br>";
		echo "SES[pass]:".$this->CryptPassword($_SESSION[pass])."(crypted)<br>";
		echo "CK[NO]:$_COOKIE[NO]<br>";
		echo "SES[NO]:".session_id();
		dump($_COOKIE);
		dump($_SESSION);
	}

//////////////////////////////////////////////////
//	�����󤷤����֤����ꤹ��
	function RenewLoginTime() {
		$this->login	= time();
	}

//////////////////////////////////////////////////
//	�����󤷤��Τ������Ƥ���Τ����������Ȥ����Τ���
	function CheckLogin() {
		//logout
		if(isset($_POST["logout"])) {
		//	$_SESSION["pass"]	= NULL;
		//	echo $_SESSION["pass"];
			unset($_SESSION["pass"]);
		//	session_destroy();
			return false;
		}

		//session
		$file=USER.$this->id."/".DATA;//data.dat
		if ($data = $this->LoadData()) {
			//echo "<div>$data[pass] == $this->pass</div>";
			if($this->pass == NULL)
				return false;
			if ($data["pass"] === $this->pass) {
				//���������
				$this->DataUpDate($data);
				$this->SetData($data);
				if(RECORD_IP)
					$this->SetIp($_SERVER['REMOTE_ADDR']);
				$this->RenewLoginTime();

				$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
				if ($pass) {//���礦�ɺ������󤹤�ʤ�
					$_SESSION["id"]	= $this->id;
					$_SESSION["pass"]	= $pass;
					setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
				}

				$this->islogin	= true;//���������
				return true;
			} else
				return "Wrong password!";
		} else {
			if($_POST["id"])
				return "ID \"{$this->id}\" doesnt exists.";
		}
	}

//////////////////////////////////////////////////
//	$id ����Ͽ�Ѥ�id�Ȥ��Ƶ�Ͽ����
	function RecordRegister($id) {
		$fp=fopen(REGISTER,"a");
		flock($fp,2);
		fputs($fp,"$id\n");
		fclose($fp);
	}

//////////////////////////////////////////////////
//	pass �� id �����ꤹ��
	function Set_ID_PASS() {
		$id	= ($_POST["id"])?$_POST["id"]:$_GET["id"];
		//if($_POST["id"]) {
		if($id) {
				$this->id	= $id;//$_POST["id"];
			// ���������������������
			if (is_registered($_POST["id"])) {
				$_SESSION["id"]	= $this->id;
			}
		} else if($_SESSION["id"])
			$this->id	= $_SESSION["id"];

		$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
		//if($_POST["pass"])
		if($pass)
			$this->pass	= $pass;//$_POST["pass"];
		else if($_SESSION["pass"])
			$this->pass	= $_SESSION["pass"];

		if($this->pass)
			$this->pass	= $this->CryptPassword($this->pass);
	}

//////////////////////////////////////////////////
//	��¸����Ƥ��륻�å�����ֹ���ѹ����롣
	function SessionSwitch() {
		// session���Ǥλ���(?)
		// how about "session_set_cookie_params()"?
		session_cache_expire(COOKIE_EXPIRE/60);
		if($_COOKIE["NO"])//���å�������¸���Ƥ��륻�å����ID�Υ��å�����ƤӽФ�
			session_id($_COOKIE["NO"]);

		session_start();
		if(!SESSION_SWITCH)//switch���ʤ��ʤ餳���ǽ�λ
			return false;
		//print_r($_SESSION);
		//dump($_SESSION);
		$OldID	= session_id();
		$temp	= serialize($_SESSION);

		session_regenerate_id();
		$NewID	= session_id();
		setcookie("NO",$NewID,time()+COOKIE_EXPIRE);
		$_COOKIE["NO"]=$NewID;

		session_id($OldID);
		session_start();

		if($_SESSION):
		//	session_destroy();//Sleipnir���Ȥ�������...?(�ǽ��)
		//	unset($_SESSION);//���ä��������(��äѤꤳ������ܤ���)(������)
			//���,���å�����foreach�ǥ롼�פ���1�ĤŤ�unset(2007/9/14 �ƽ���)
			foreach($_SESSION as $key => $val)
				unset($_SESSION["$key"]);
		endif;

		session_id($NewID);
		session_start();
		$_SESSION	= unserialize($temp);
	}

//////////////////////////////////////////////////
//	���Ϥ��줿���󤬷��ˤϤޤ뤫Ƚ��
//	�� �����ǡ����������

	function MakeNewData() {
		// ��Ͽ�Կ����³��ξ��
		if(MAX_USERS <= count(glob(USER."*")))
			return array(false,"Maximum users.<br />��Ͽ�Կ����³���ã���Ƥ��ޤä��ͤǤ���");
		if(isset($_POST["Newid"]))
			trim($_POST["Newid"]);
		if(empty($_POST["Newid"]))
			return array(false,"Enter ID.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["Newid"])||
			ereg("[^0-9a-zA-Z]+",$_POST["Newid"]))//����ɽ��
			return array(false,"Bad ID");

		if(strlen($_POST["Newid"]) < 4 || 16 < strlen($_POST["Newid"]))//ʸ������
			return array(false,"Bad ID");

		if(is_registered($_POST["Newid"]))
			return array(false,"This ID has been already used.");

		$file = USER.$_POST["Newid"]."/".DATA;
		// PASS
		//if(isset($_POST["pass1"]))
		//	trim($_POST["pass1"]);
		if(empty($_POST["pass1"]) || empty($_POST["pass2"]))
			return array(false,"Enter both Password.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass1"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass1"]))
			return array(false,"Bad Password 1");
		if(strlen($_POST["pass1"]) < 4 || 16 < strlen($_POST["pass1"]))//ʸ������
			return array(false,"Bad Password 1");
		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass2"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass2"]))
			return array(false,"Bad Password 2");
		if(strlen($_POST["pass2"]) < 4 || 16 < strlen($_POST["pass2"]))//ʸ������
			return array(false,"Bad Password 2");

		if($_POST["pass1"] !== $_POST["pass2"])
			return array(false,"Password dismatch.");

		$pass = $this->CryptPassword($_POST["pass1"]);
		// MAKE
		if(!file_exists($file)){
			mkdir(USER.$_POST["Newid"], 0705);
			$this->RecordRegister($_POST["Newid"]);//ID��Ͽ
			$fp=fopen("$file","w");
			flock($fp,LOCK_EX);
				$now	= time();
				fputs($fp,"id=$_POST[Newid]\n");
				fputs($fp,"pass=$pass\n");
				fputs($fp,"last=".$now."\n");
				fputs($fp,"login=".$now."\n");
				fputs($fp,"start=".$now.substr(microtime(),2,6)."\n");
				fputs($fp,"money=".START_MONEY."\n");
				fputs($fp,"time=".START_TIME."\n");
				fputs($fp,"record_btl_log=1\n");
			fclose($fp);
			//print("ID:$_POST[Newid] success.<BR>");
			$_SESSION["id"]=$_POST["Newid"];
			setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
			$success	= "<div class=\"recover\">ID : $_POST[Newid] success. Try Login</div>";
			return array(true,$success);//����...
		}
	}

//////////////////////////////////////////////////
//	����ID�����ѤΥե�����
	function NewForm($error=NULL) {
		if(MAX_USERS <= count(glob(USER."*"))) {
			?>

	<div style="margin:15px">
	Maximum users.<br />
	��Ͽ�Կ����³���ã���Ƥ���褦�Ǥ���
	</div><?
			return false;
		}
		$idset=($_POST["Newid"]?" value=$_POST[Newid]":NULL);
		?>
	<div style="margin:15px">
	<?=ShowError($error);?>
	<h4>�Ȥꤢ���� New Game!</h4>
	<form action="<?=INDEX?>" method="post">

	<table><tbody>
	<tr><td colspan="2">ID & PASS must be 4 to 16 letters.<br />letters allowed a-z,A-Z,0-9<br />
	ID �� PASS�� 4-16 ʸ������ǡ�Ⱦ�ѱѿ�����</td></tr>
	<tr><td><div style="text-align:right">ID:</div></td>
	<td><input type="text" maxlength="16" class="text" name="Newid" style="width:240px"<?=$idset?>></td></tr>
	<tr><td colspan="2"><br />Password,Re-enter.<br />PASS �Ȥ��κ����ϤǤ� ��ǧ�ѡ�</td></tr>
	<tr><td><div style="text-align:right">PASS:</div></td>
	<td><input type="password" maxlength="16" class="text" name="pass1" style="width:240px"></td></tr>

	<tr><td></td>
	<td><input type="password" maxlength="16" class="text" name="pass2" style="width:240px">(verify)</td></tr>

	<tr><td></td><td><input type="submit" class="btn" name="Make" value="Make" style="width:160px"></td></tr>

	</tbody></table>
	</form>
	</div>
<?
	}

//////////////////////////////////////////////////
//	�������ѤΥե�����
	function LoginForm($message = NULL) {
		?>
<div style="width:730px;">
<!-- ������ -->
<div style="width:350px;float:right">
<h4 style="width:350px">Login</h4>
<?=$message?>
<form action="<?=INDEX?>" method="post" style="padding-left:20px">
<table><tbody>
<tr>
<td><div style="text-align:right">ID:</div></td>
<td><input type="text" maxlength="16" class="text" name="id" style="width:160px"<?=$_SESSION["id"]?" value=\"$_SESSION[id]\"":NULL?>></td>
</tr>
<tr>
<td><div style="text-align:right">PASS:</div></td>
<td><input type="password" maxlength="16" class="text" name="pass" style="width:160px"></td>
</tr>
<tr><td></td><td>
<input type="submit" class="btn" name="Login" value="login" style="width:80px">&nbsp;
<a href="?newgame">NewGame?</a>
</td></tr>
</tbody></table>
</form>

<h4 style="width:350px">Ranking</h4><?
	include_once(CLASS_RANKING);
	$Rank	= new Ranking();
	$Rank->ShowRanking(0,4);
	?>
</div>
<!-- �� -->
<div style="width:350px;padding:15px;float:left;">
<div style="width:350px;text-align:center">
<img src="./image/top01.gif" style="margin-bottom:20px" />
</div>
<div style="margin-left:20px">
<div class="u">����äƤɤ�ʥ�����?</div>
<ul>
<li>���������Ū�ϥ�󥭥�1�̤ˤʤꡢ<br />1�̤�����Ǥ���</li>
<li>�������ǤϤʤ��Ǥ�����<br />����äȿ�����Ʈ�����ƥब���Ǥ���</li>
</ul>
<div class="u">��Ʈ�Ϥɤ�ʴ���?</div>
<ul>
<li>5�ͤΥ���饯�����ǥѡ��ƥ�����������</li>
<li>�ƥ���餬��ư�ѥ�����������<br />��Ʈ�ξ����˱����Ƶ���Ȥ�ʬ���ޤ���</li>
<li><a href="?log" class="a0">������</a>����Ʈ���������Ǥ��ޤ���</li>
</ul>
</div>
</div>
<div class="c-both"></div>
</div>

<!-- -------------------------------------------------------- -->

<div style="margin:15px">
<h4>info.</h4>
Users : <?=UserAmount()?> / <?=MAX_USERS?><br />
<?
	$Abandon	= ABANDONED;
	print(floor($Abandon/(60*60*24))."���ǡ������Ѳ�̵���ǥǡ����ä��롣");
print("</div>\n");
	}

//////////////////////////////////////////////////
//	������ɽ��������˥塼��
//	�����󤷤Ƥ���ѤȤ����Ǥʤ��͡�
	function MyMenu() {
		if($this->name && $this->islogin) { // �����󤷤Ƥ����
			print('<div id="menu">'."\n");
			//print('<span class="divide"></span>');//���ڤ�
			print('<a href="'.INDEX.'">Top</a><span class="divide"></span>');
			print('<a href="?hunt">Hunt</a><span class="divide"></span>');
			print('<a href="?item">Item</a><span class="divide"></span>');
			print('<a href="?town">Town</a><span class="divide"></span>');
			print('<a href="?setting">Setting</a><span class="divide"></span>');
			print('<a href="?log">Log</a><span class="divide"></span>');
			if(BBS_OUT)
				print('<a href="'.BBS_OUT.'">BBS</a><span class="divide"></span>'."\n");
			print('</div><div id="menu2">'."\n");
				?>
	<div style="width:100%">
	<div style="width:33%;float:left"><?=$this->name?></div>
	<div style="width:67%;float:right">
	<div style="width:50%;float:left"><span class="bold">Funds</span> : <?=MoneyFormat($this->money)?></div>
	<div style="width:50%;float:right"><span class="bold">Time</span> : <?=floor($this->time)?>/<?=MAX_TIME?></div>
	</div>
	<div class="c-both"></div>
	</div><?
			print('</div>');
		} else if(!$this->name && $this->islogin) {// ��������ο�
			print('<div id="menu">');
			print("First login. Thankyou for the entry.");
			print('</div><div id="menu2">');
			print("fill the blanks. �Ƥ��ȡ������Ƥ���������");
			print('</div>');
		} else { //// �������Ⱦ��֤ο͡�����Ѥ�ɽ��
			print('<div id="menu">');
			print('<a href="'.INDEX.'">�ȥå�</a><span class="divide"></span>'."\n");
			print('<a href="?newgame">����</a><span class="divide"></span>'."\n");
			print('<a href="?manual">�롼��ȥޥ˥奢��</a><span class="divide"></span>'."\n");
			print('<a href="?gamedata=job">������ǡ���</a><span class="divide"></span>'."\n");
			print('<a href="?log">��Ʈ��</a><span class="divide"></span>'."\n");
			if(BBS_OUT)
			print('<a href="'.BBS_OUT.'">���BBS</a><span class="divide"></span>'."\n");
			
			print('</div><div id="menu2">');
			print("Welcome to [ ".TITLE." ]");
			print('</div>');
		}
	}

//////////////////////////////////////////////////
//	HTML������ʬ
	function Head() {
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head><?$this->HtmlScript();?>
<title><?=TITLE?></title>
</head>
<body><a name="top"></a>
<div id="main_frame">
<div id="title"><img src="./image/title03.gif"></div>
<?$this->MyMenu();?><div id="contents">
<?
	}

//////////////////////////////////////////////////
//	�������륷���ȤȤ���
	function HtmlScript() {
		?>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<link rel="stylesheet" href="./basis.css" type="text/css">
<link rel="stylesheet" href="./style.css" type="text/css">
<script type="text/javascript" src="prototype.js"></script>
<?
	}

//////////////////////////////////////////////////
//	HTML��λ��ʬ
	function Foot() {
		?>
</div>
<div id="foot">
<a href="?update">UpDate</a> - <?
	if(BBS_BOTTOM_TOGGLE)
		print('<a href="?bbs">BBS</a> - '."\n");
		?>
<a href="?manual">Manual</a> - 
<a href="?tutorial">Tutorial</a> - 
<a href="?gamedata=job">GameData</a> - 
<a href="#top">Top</a><br>
Copy Right <a href="http://tekito.kanichat.com/">Tekito</a> 2007-2008.<br>
</div>
</div>
</body>
</html><?
	}

//////////////////////////////////////////////////
//	���������ѤΥե�����
	function FirstLogin() {
		// ����:����Ѥ�=false / ������=true
		if ($this->name)
			return false;

		do {
			if (!$_POST["Done"])
				break;
			if(is_numeric(strpos($_POST["name"],"\t"))) {
				$error	= 'error1';
				break;
			}
			if(is_numeric(strpos($_POST["name"],"\n"))) {
				$error	= 'error';
				break;
			}
			$_POST["name"]	= trim($_POST["name"]);
			$_POST["name"]	= stripslashes($_POST["name"]);
			if (!$_POST["name"]) {
				$error	= 'Name is blank.';
				break;
			}
			$length	= strlen($_POST["name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			$userName	= userNameLoad();
			if(in_array($_POST["name"],$userName)) {
				$error	= '����̾���ϻ��Ѥ���Ƥ��ޤ���';
				break;
			}
			// �ǽ�Υ�����̾��
			$_POST["first_name"]	= trim($_POST["first_name"]);
			$_POST["first_name"]	= stripslashes($_POST["first_name"]);
			if(is_numeric(strpos($_POST["first_name"],"\t"))) {
				$error	= 'error';
				break;
			}
			if(is_numeric(strpos($_POST["first_name"],"\n"))) {
				$error	= 'error';
				break;
			}
			if (!$_POST["first_name"]) {
				$error	= 'Character name is blank.';
				break;
			}
			$length	= strlen($_POST["first_name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			if(!$_POST["fjob"]) {
				$error	= 'Select characters job.';
				break;
			}
			$_POST["name"]	= htmlspecialchars($_POST["name"],ENT_QUOTES);
			$_POST["first_name"]	= htmlspecialchars($_POST["first_name"],ENT_QUOTES);

			$this->name	= $_POST["name"];
			userNameAdd($this->name);
			$this->SaveData();
			switch($_POST["fjob"]){
				case "1":
					$job = 1; $gend = 0; break;
				case "2":
					$job = 1; $gend = 1; break;
				case "3":
					$job = 2; $gend = 0; break;
				default:
					$job = 2; $gend = 1;
			}
			include(DATA_BASE_CHAR);
			$char	= new char();
			$char->SetCharData(array_merge(BaseCharStatus($job),array("name"=>$_POST[first_name],"gender"=>"$gend")));
			$char->SaveCharData($this->id);
			return false;
		}while(0);

		include(DATA_BASE_CHAR);
		$war_male	= new char();
		$war_male->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
		$war_female	= new char();
		$war_female->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
		$sor_male	= new char();
		$sor_male->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
		$sor_female	= new char();
		$sor_female->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));

		?>
	<form action="<?=INDEX?>" method="post" style="margin:15px">
	<?ShowError($error);?>
	<h4>Name of Team</h4>
	<p>Decide the Name of the team.<br />
	It should be more than 1 and less than 16 letters.<br />
	Japanese characters count as 2 letters.</p>
	<p>1-16ʸ���ǥ������̾�����Ƥ���������<br />
	���ܸ�Ǥ�OK��<br />
	���ܸ�� 1ʸ�� = 2 letter</p>
	<div class="bold u">TeamName</div>
	<input class="text" style="width:160px" maxlength="16" name="name"<?print($_POST["name"]?"value=\"$_POST[name]\"":"")?>>
	<h4>First Character</h4>
	<p>Decide the name of Your First Charactor.<br>
	more than 1 and less than 16 letters.</p>
	<p>���������̾����</p>
	<div class="bold u">CharacterName</div>
	<input class="text" type="text" name="first_name" maxlength="16" style="width:160px;margin-bottom:10px">
	<table cellspacing="0" style="width:400px"><tbody>
	<tr><td class="td1" valign="bottom"><div style="text-align:center"><?=$war_male->ShowImage()?><br><input type="radio" name="fjob" value="1" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$war_female->ShowImage()?><br><input type="radio" name="fjob" value="2" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$sor_male->ShowImage()?><br><input type="radio" name="fjob" value="3" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$sor_female->ShowImage()?><br><input type="radio" name="fjob" value="4" style="margin:3px"></div></td></tr>
	<tr><td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td>
	<td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td></tr>
	<tr><td colspan="2" class="td4"><div style="text-align:center">Warrior</div></td><td colspan="2" class="td4"><div style="text-align:center">Socerer</div></td></tr>
	</tbody></table>
	<p>Choose your first character's job &amp; Gender.</p>
	<p>�ǽ�Υ����ο�������</p>
	<input class="btn" style="width:160px" type="submit" value="Done" name="Done">
	<input type="hidden" value="1" name="Done">
	<input class="btn" style="width:160px" type="submit" value="logout" name="logout"></form><?
			return true;
	}
//////////////////////////////////////////////////
//	���̤�1�ԷǼ���
	function bbs01() {
		if(!BBS_BOTTOM_TOGGLE)
			return false;
		$file	= BBS_BOTTOM;
	?>
<div style="margin:15px">
<h4>one line bbs</h4>
�Х����,�Х�󥹤ˤĤ��Ƥΰո��Ȥ��Ϥ�����Ǥɤ�����
<form action="?bbs" method="post">
<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?
		if(!file_exists($file))
			return false;
		$log	= file($file);
		if($_POST["message"] && strlen($_POST["message"]) < 121) {
			$_POST["message"]	= htmlspecialchars($_POST["message"],ENT_QUOTES);
			$_POST["message"]	= stripslashes($_POST["message"]);

			$name	= ($this->name ? "<span class=\"bold\">{$this->name}</span>":"̵̾��");
			$message	= $name." > ".$_POST["message"];
			if($this->UserColor)
				$message	= "<span style=\"color:{$this->UserColor}\">".$message."</span>";
			$message	.= " <span class=\"light\">(".date("Mj G:i").")</span>\n";
			array_unshift($log,$message);
			while(150 < count($log))// ����¸�Կ���
				array_pop($log);
			WriteFile($file,implode(null,$log));
		}
		foreach($log as $mes)
			print(nl2br($mes));
		print('</div>');
	}
//end of class
//////////////////////////////////////////////////////////////////////
}
?>