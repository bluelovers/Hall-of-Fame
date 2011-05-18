<?php
/*
���������������
�������ƥ�
	�ǡ�������¸��ˡ
	define("AUCTION_ITEM","./*****");//����

	.dat ����ȤˤĤ���
	�ֹ�(1���ܤ���)
	�ֹ�<>���佪λ����<>������������<>���ʼ�id<>�����ƥ�<>�Ŀ�<>���������<>�ǽ�������id<>�ǽ���������<>������<>IP
	�ֹ�-1<>���佪λ����<>������������<>���ʼ�id<>�����ƥ�<>�Ŀ�<>���������<>�ǽ�������id<>�ǽ���������<>������<>IP
	�ֹ�-2<>���佪λ����<>������������<>���ʼ�id<>�����ƥ�<>�Ŀ�<>���������<>�ǽ�������id<>�ǽ���������<>������<>IP
	...........
�������
*/
class Auction {

	// �ե�����ݥ���
	var $fp;

	// �����������μ���
	// �����ƥ�or�����
	var $AuctionType;

	// �������ֹ�
	var $ArticleNo;

	// ����ʪ(�����)�ꥹ��
	var $Article = array();

	var $UserName;

	// �ʪ�����ν�����
	var $TempUser	= array();

	// �в��
	var $AuctionLog;

	// �ǡ������ѹ����줿��?
	var $DataChange	= false;

	var $QUERY;
	var $sort;

////////////////////////////////
// ���󥹥ȥ饯��
	function Auction($type) {
		// �����ƥ४���������
		if($type == "item") {
			$this->AuctionType = "item";
			$this->ItemArticleRead();
		// ����饪���������
		} else if($type == "char") {
			$this->AuctionType = "char";
		}
	}
//////////////////////////////////////////////
//	GET�Υ����꡼̾
	function AuctionHttpQuery($name) {
		$this->QUERY	= $name;
	}
//////////////////////////////////////////////
//	���֤��вᤷ�ƽ�λ���������ʤν���
	function ItemCheckSuccess() {
		$Now	= time();
		foreach($this->Article as $no => $Article) {
			// ������֤��ĤäƤ���ʤ鼡
			if(AuctionLeftTime($Now,$Article["end"]))
				continue;
			if(!function_exists("LoadItemData"))
				include(DATA_ITEM);
			$item	= LoadItemData($Article["item"]);
			if($Article["bidder"]) {
				// ��Ԥ�����ʤ饢���ƥ���Ϥ���
				// ��Ԥ�����ʤ���ʼԤ˶���Ϥ���
				$this->UserGetItem($Article["bidder"],$Article["item"],$Article["amount"]);
				$this->UserGetMoney($Article["exhibitor"],$Article["price"]);
				// ��̤���˻Ĥ�
				$this->AddLog("No.{$Article[No]} <img src=\"".IMG_ICON.$item["img"]."\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>�� �� ".$this->UserGetNameFromTemp($Article["bidder"])." �� ".MoneyFormat($Article["price"])." ��<span class=\"recover\">����ޤ�����</span>");
			} else {
				// ������̵���ä���硢���ʼԤ��ֵѡ�
				$this->UserGetItem($Article["exhibitor"],$Article["item"],$Article["amount"]);
				// ��̤���˻Ĥ�
				$this->AddLog("No.{$Article[No]} <img src=\"".IMG_ICON.$item["img"]."\"><span class=\"bold\">{$item[name]} x{$Article[amount]}</span>�� ��<span class=\"dmg\">������̵����ή��ޤ�����</span>");
			}
			// �Ǹ�˾ä�
			unset($this->Article["$no"]);
			$this->DataChange	= true;
		}
	}
//////////////////////////////////////////////
//	
	function UserGetNameFromTemp($UserID) {
		if($this->TempUser["$UserID"]["Name"])
			return $this->TempUser["$UserID"]["Name"];
		else
			return "-";
	}
//////////////////////////////////////////////
//	�����������Ǥ�������
	function UserGetMoney($UserID,$Money) {
		if(!$this->TempUser["$UserID"]["user"])
		{
			$this->TempUser["$UserID"]["user"]	= new user($UserID);
			$this->TempUser["$UserID"]["Name"]	= $this->TempUser["$UserID"]["user"]->Name();
		}

		$this->TempUser["$UserID"]["UserGetTotalMoney"]	+= $Money;
		$this->TempUser["$UserID"]["Money"]	= true;//�⤬�ɲä��줿���Ȥ�Ͽ
	}
//////////////////////////////////////////////
//	�����������ǥ����ƥ����
	function UserGetItem($UserID,$item,$amount) {
		if(!$this->TempUser["$UserID"]["user"])
		{
			$this->TempUser["$UserID"]["user"]	= new user($UserID);
			$this->TempUser["$UserID"]["Name"]	= $this->TempUser["$UserID"]["user"]->Name();
		}

		$this->TempUser["$UserID"]["UserGetItem"]["$item"]	+= $amount;
		$this->TempUser["$UserID"]["item"]	= true;//�����ƥब�ɲä��줿���Ȥ�Ͽ
	}
//////////////////////////////////////////////
//	�����������ǥ���饯��������
//	(ư���ǧ̵��)
	function UserGetChar($UserID,$char) {
		$this->TempUser["$UserID"]["char"][]	= $char;//
		$this->TempUser["$UserID"]["CharAdd"]	= true;//����饯�������ɲä��줿���Ȥ�Ͽ
	}
//////////////////////////////////////////////
//	����������������̤���������?
	function UserSaveData() {
		foreach($this->TempUser as $user => $Result) {
			// ���������
			if($this->TempUser["$user"]["Money"]) {
				$this->TempUser["$user"]["user"]->GetMoney($this->TempUser["$user"]["UserGetTotalMoney"]);
				$this->TempUser["$user"]["user"]->SaveData();
			}
			// �����ƥ������
			if($this->TempUser["$user"]["item"]) {
				foreach($this->TempUser["$user"]["UserGetItem"] as $itemNo => $amount) {
					$this->TempUser["$user"]["user"]->AddItem($itemNo,$amount);
				}
				$this->TempUser["$user"]["user"]->SaveUserItem();
			}
			// ����饯����������
			// (ư���ǧ�ʤ�)
			if($this->TempUser["$user"]["CharAdd"]) {
				if($this->TempUser["$user"]["char"]) {
					foreach($this->TempUser["$user"]["char"] as $char) {
						$char->SaveCharData($user);
					}
				}
			}
			// �桼�������������ƤΥե�����Υե�����ݥ��󥿤��Ĥ���
			$this->TempUser["$user"]["user"]->fpCloseAll();
		}
		unset($this->TempUser);
	}
//////////////////////////////////////////////
//	�������븢�������뤫�ɤ����֤�
	function ItemBidRight($ArticleNo,$UserID) {
		if($this->Article["$ArticleNo"]["bidder"] == $UserID)
			return false;
		if($this->Article["$ArticleNo"]["exhibitor"] == $UserID)
			return false;
		return true;
	}
//////////////////////////////////////////////
//	�桼����̾����ƤӽФ�
	function LoadUserName($id) {
		if($this->UserName["$id"]) {
			return $this->UserName["$id"];
		} else {
			$User	= new user($id);
			$Name	= $User->Name();
			if($Name) {
				$this->UserName["$id"]	= $Name;
			} else {
				$this->UserName["$id"]	= "-";
			}
			return $this->UserName["$id"];
		}
	}
//////////////////////////////////////////////
//	�����������ʤ��֤�
	function ItemBottomPrice($ArticleNo) {
		if($this->Article["$ArticleNo"]) {
			return BottomPrice($this->Article["$ArticleNo"]["price"]);
		}
	}
//////////////////////////////////////////////
//	��������
	function ItemBid($ArticleNo,$BidPrice,$Bidder,$BidderName) {
		if(!$Article	= $this->Article["$ArticleNo"])
			return false;
		$BottomPrice	= BottomPrice($this->Article["$ArticleNo"]["price"]);
		// IP��Ʊ��
		if($Article["IP"] == $_SERVER[REMOTE_ADDR]) {
			ShowError("IP����.");
			return false;
		}
		// ����ü����ػߤ��롣
		if(isMobile == "i") {
			ShowError("mobile forbid.");
			return false;
		}
		// �����������ʤ��äƤ�����
		if($BidPrice < $BottomPrice)
			return false;

		// ï�����������Ƥ�����礪����ֶ⤹�롣
		if($Article["bidder"]) {
			$this->UserGetMoney($Article["bidder"],$Article["price"]);
			$this->UserSaveData();
		}

		// �������֤��Ĥ꾯�ʤ��ʤ��Ĺ���롣
		$Now	= time();
		$left	= AuctionLeftTime($Now,$Article["end"],true);
		/* // �Ĥ����1���ְʲ��ʤ�15ʬ��Ĺ����
		if(1 < $left && $left < 3601) {
			$this->Article["$ArticleNo"]["end"]	+= 900;
		}
		*/
		// �Ĥ����15ʬ�ʲ��ʤ� 15ʬ�ˤ��롣
		if(0 < $left && $left < 901) {
			$dif	= 900 - $left;
			$this->Article["$ArticleNo"]["end"]	+= $dif;
		}
		

		$this->Article["$ArticleNo"]["price"]	= $BidPrice;
		$this->Article["$ArticleNo"]["TotalBid"]++;
		$this->Article["$ArticleNo"]["bidder"]	= $Bidder;
		$this->DataChange	= true;
		$item	= LoadItemData($Article["item"]);
		//$this->AddLog("No.".$Article["No"]." <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>�Ĥ� ".MoneyFormat($BidPrice)." �� ".$this->LoadUserName($Bidder)." ��<span class=\"support\">�������ޤ�����</span>");
		$this->AddLog("No.".$Article["No"]." <span class=\"bold\">{$item[name]} x{$Article[amount]}</span>�Ĥ� ".MoneyFormat($BidPrice)." �� ".$BidderName." ��<span class=\"support\">�������ޤ�����</span>");
		return true;
	}
//////////////////////////////////////////////
//	����ʪ������ɽ������(����1) ɽ�����¤Ӥ��㤦����
	function ItemShowArticle($bidding=false) {
		if(count($this->Article) == 0) {
			print("����ʪ̵��(No auction)<br />\n");
			return false;
		} else {
			$Now	= time();
			$exp	= '<tr><td class="td9">�ֹ�</td><td class="td9">����</td><td class="td9">������</td><td class="td9">������</td><td class="td9">�Ĥ�</td>'.
					'<td class="td9">���ʼ�</td><td class="td9">������</td></tr>'."\n";
			print('<table style="width:725px;text-align:center" cellpadding="0" cellspacing="0" border="0">'."{$exp}\n");
			foreach($this->Article as $Article) {

				print("<tr><td class=\"td7\">");
				// �����ֹ�
				print($Article["No"]);
				print("</td><td class=\"td7\">");
				// ������������
				print(MoneyFormat($Article["price"]));
				print("</td><td class=\"td7\">");
				// ������
				if(!$Article["bidder"])
					$bidder	= "-";
				else
					$bidder	= $this->LoadUserName($Article["bidder"]);
				print($bidder);
				print("</td><td class=\"td7\">");
				// ���������
				print($Article["TotalBid"]);
				print("</td><td class=\"td7\">");
				// ��λ����
				print(AuctionLeftTime($Now,$Article["end"]));
				print("</td><td class=\"td7\">");
				// ���ʼ�
				$exhibitor	= $this->LoadUserName($Article["exhibitor"]);
				print($exhibitor);
				print("</td><td class=\"td8\">");
				// ������
				print($Article["comment"]?$Article["comment"]:"&nbsp;");
				print("</td></tr>\n");
				// �����ƥ�
				print('<tr><td colspan="7" style="text-align:left;padding-left:15px" class="td6">');
				$item	= LoadItemData($Article["item"]);
				print('<form action="?menu=auction" method="post">');
				// �����ե�����
				if($bidding) {
					print('<a href="#" onClick="Element.toggle(\'Bid'.$Article["No"].'\';return false;)">����</a>');
					print('<span style="display:none" id="Bid'.$Article["No"].'">');
					print('&nbsp;<input type="text" name="BidPrice" style="width:80px" class="text" value="'.BottomPrice($Article["price"]).'">');
					print('<input type="submit" value="Bid" class="btn">');
					print('<input type="hidden" name="ArticleNo" value="'.$Article["No"].'">');
					print('</span>');
				}
				print(ShowItemDetail($item,$Article["amount"],1));
				print("</form>");
				print("</td></tr>\n");
			}
			print("{$exp}</table>\n");
			return true;
		}
	}
//////////////////////////////////////////////
//	����ʪ������ɽ������(����2) ɽ�����¤Ӥ��㤦����
	function ItemShowArticle2($bidding=false) {
		if(count($this->Article) == 0) {
			print("����ʪ̵��(No auction)<br />\n");
			return false;
		} else {
			$Now	= time();
			// �����Ȥ���Ƥ��뿧���Ѥ���(�����ѿ�)
			if($this->sort)
				${"Style_".$this->sort}	= ' class="a0"';
			$exp	= '<tr><td class="td9"><a href="?menu='.$this->QUERY.'&sort=no"'.$Style_no.'>No</a></td>'.
					'<td class="td9"><a href="?menu='.$this->QUERY.'&sort=time"'.$Style_time.'>�Ĥ�</td>'.
					'<td class="td9"><a href="?menu='.$this->QUERY.'&sort=price"'.$Style_price.'>����</a>'.
					'<br /><a href="?menu='.$this->QUERY.'&sort=rprice"'.$Style_rprice.'>(��)</a></td>'.
					'<td class="td9">Item</td>'.
					'<td class="td9"><a href="?menu='.$this->QUERY.'&sort=bid"'.$Style_bid.'>Bids</a></td>'.
					'<td class="td9">������</td><td class="td9">���ʼ�</td></tr>'."\n";

			print("����ʿ�:".$this->ItemAmount()."\n");
			print('<table style="width:725px;text-align:center" cellpadding="0" cellspacing="0" border="0">'."\n");
			print($exp);
			foreach($this->Article as $Article) {

				// �����ֹ�
				print("<tr><td rowspan=\"2\" class=\"td7\">");
				print($Article["No"]);
				// ��λ����
				print("</td><td class=\"td7\">");
				print(AuctionLeftTime($Now,$Article["end"]));
				// ������������
				print("</td><td class=\"td7\">");
				print(MoneyFormat($Article["price"]));
				// �����ƥ�
				print('</td><td class="td7" style="text-align:left">');
				$item	= LoadItemData($Article["item"]);
				print(ShowItemDetail($item,$Article["amount"],1));
				// ���������
				print("</td><td class=\"td7\">");
				print($Article["TotalBid"]);
				// ������
				print("</td><td class=\"td7\">");
				if(!$Article["bidder"])
					$bidder	= "-";
				else
					$bidder	= $this->LoadUserName($Article["bidder"]);
				print($bidder);
				// ���ʼ�
				print("</td><td class=\"td8\">");
				$exhibitor	= $this->LoadUserName($Article["exhibitor"]);
				print($exhibitor);
				// ������
				print("</td></tr><tr>");
				print("<td colspan=\"6\" class=\"td8\" style=\"text-align:left\">");
				print('<form action="?menu=auction" method="post">');
				// �����ե�����
				if($bidding) {
					print('<a style="margin:0 10px" href="#" onClick="Element.toggle(\'Bid'.$Article["No"].'\');return false;">����</a>');
					print('<span style="display:none" id="Bid'.$Article["No"].'">');
					print('&nbsp;<input type="text" name="BidPrice" style="width:80px" class="text" value="'.BottomPrice($Article["price"]).'">');
					print('<input type="submit" value="Bid" class="btn">');
					print('<input type="hidden" name="ArticleNo" value="'.$Article["No"].'">');
					print('</span>');
				}
				print($Article["comment"]?$Article["comment"]:"&nbsp;");
				print("</form>");
				print("</td></tr>\n");
				
				print("</td></tr>\n");
			}
			print($exp);
			print("</table>\n");
			return true;
		}
	}
//////////////////////////////////////////////////
//	�����ֹ�ζ����ʤ����ʤ���Ƥ��뤫��������롣
	function ItemArticleExists($no) {
		if($this->Article["$no"]) {
			return true;
		} else {
			return false;
		}
	}
//////////////////////////////////////////////
//	�����ƥ����ʤ���
	function ItemAddArticle($item,$amount,$id,$time,$StartPrice,$comment) {
		// ��λ����η׻�
		$Now	= time();
		$end	= $Now + round($now + (60 * 60 * $time));
		// ���ϲ��ʤΤ���
		if(ereg("^[0-9]",$StartPrice)) {
			$price	= (int)$StartPrice;
		} else {
			$price	= 0;
		}
		// �����Ƚ���
		$comment	= str_replace("\t","",$comment);
		$comment	= htmlspecialchars(trim($comment),ENT_QUOTES);
		$comment	= stripslashes($comment);
		// �������ֹ�
		$this->ArticleNo++;
		if(9999 < $this->ArticleNo)
			$this->ArticleNo	= 0;
		$New	= array(
			// �������ֹ�
			"No"		=> $this->ArticleNo,
			// ��λ����
			"end"		=> $end,
			// ������������
			"price"		=> (int)$price,
			// ���ʼ�id
			"exhibitor"	=> $id,
			// �����ƥ�
			"item"		=> $item,
			// �Ŀ�
			"amount"	=> (int)$amount,
			// ���������
			"TotalBid"	=> 0,
			// �ǽ�������id
			"bidder"	=> NULL,
			// �ǽ���������(�ȤäƤʤ������Ȥ�������лȤäƤ�������)
			"latest"	=> NULL,
			// ������
			"comment"	=> $comment,
			// IP
			"IP"	=> $_SERVER[REMOTE_ADDR],
			);
		array_unshift($this->Article,$New);
		$itemData	= LoadItemData($item);
		$this->AddLog("No.".$this->ArticleNo." �� <img src=\"".IMG_ICON.$itemData["img"]."\"><span class=\"bold\">{$itemData[name]} x{$amount}</span>�Ĥ�<span class=\"charge\">���ʤ���ޤ�����</span>");
		$this->DataChange	= true;
	}
//////////////////////////////////////////////
//	�����������Υǡ�������¸����
	function ItemSaveData() {
		if(!$this->DataChange)
		{
			fclose($this->fp);
			unset($this->fp);
			return false;
		}
		// �����ƥ� ��������������¸���롣
		$string	= $this->ArticleNo."\n";
		foreach($this->Article as $val) {
			//if(strlen($val["end"]) != 10) continue;
			$string	.=	$val["No"].
				"<>".$val["end"].
				"<>".$val["price"].
				"<>".$val["exhibitor"].
				"<>".$val["item"].
				"<>".$val["amount"].
				"<>".$val["TotalBid"].
				"<>".$val["bidder"].
				"<>".$val["latest"].
				"<>".$val["comment"].
				"<>".$val["IP"]."\n";
		}
		//print($string);
		if(file_exists(AUCTION_ITEM) && $this->fp) {
			WriteFileFP($this->fp,$string,true);
			fclose($this->fp);
			unset($this->fp);
		} else {
			WriteFile(AUCTION_ITEM,$string,true);
		}
		$this->SaveLog();
	}
//////////////////////////////////////////////
//	
	function ItemSortBy($type) {
		switch($type) {
			case "no":
				usort($this->Article,"ItemArticleSortByNo");
				$this->sort	= "no";
				break;
			case "time":
				usort($this->Article,"ItemArticleSortByTime");
				$this->sort	= "time";
				break;
			case "price":
				usort($this->Article,"ItemArticleSortByPrice");
				$this->sort	= "price";
				break;
			case "rprice":
				usort($this->Article,"ItemArticleSortByRPrice");
				$this->sort	= "rprice";
				break;
			case "bid":
				usort($this->Article,"ItemArticleSortByTotalBid");
				$this->sort	= "bid";
				break;
			default:
				usort($this->Article,"ItemArticleSortByTime");
				$this->sort	= "time";
				break;
		}
	}
//////////////////////////////////////////////
// �����ƥ४����������ѤΥե�����򳫤���
// �ǡ�������Ф�,��Ǽ
	function ItemArticleRead() {
		// �ե����뤬������
		if(file_exists(AUCTION_ITEM)) {
			//$fp	= fopen(AUCTION_ITEM,"r+");
			$this->fp	= FileLock(AUCTION_ITEM);
			//if(!$fp) return false;
			//flock($fp,LOCK_EX);
			// �����ֹ�����ɤߤ���
			$this->ArticleNo	= trim(fgets($this->fp));
			while( !feof($this->fp) ) {
				$str	= fgets($this->fp);
				if(!$str) continue;
				$article = explode("<>",$str);
				if(strlen($article["1"]) != 10) continue;
				$this->Article[$article["0"]]	= array(
				"No"		=> $article["0"],// �����ֹ�
				"end"		=> $article["1"],// ��λ����
				"price"		=> $article["2"],// ������������
				"exhibitor"	=> $article["3"],// ���ʼ�id
				"item"		=> $article["4"],// �����ƥ�
				"amount"	=> $article["5"],// �Ŀ�
				"TotalBid"	=> $article["6"],// ���������
				"bidder"	=> $article["7"],// �ǽ�������id
				"latest"	=> $article["8"],// �ǽ���������
				"comment"	=> trim($article["9"]),// ������
				"IP"	=> trim($article["10"]),// IP
				);
			}
		// �ե����뤬̵�����
		} else {
			// ���⤷�ʤ���
		}
	}
//////////////////////////////////////////////////
//	����ʪ�ο�
	function ItemAmount() {
		return count($this->Article);
	}
//////////////////////////////////////////////////
//	�����������в�����ɤ�
	function LoadLog() {
		if($this->AuctionType == "item") {
			if(!file_exists(AUCTION_ITEM_LOG)) {
				$this->AuctionLog	= array();
				return false;
			}
			$fp	= fopen(AUCTION_ITEM_LOG,"r+");
			if(!$fp) return false;
			flock($fp,LOCK_EX);
			while( !feof($fp) ) {
				$str	= trim(fgets($fp));
				if(!$str) continue;
				$this->AuctionLog[]	= $str;
			}
		}
	}
//////////////////////////////////////////////////
//	�����������в������¸
	function SaveLog() {
		if($this->AuctionType == "item") {
			if(!$this->AuctionLog)
				return false;
			// 30�԰ʲ��˼����
			while(100 < count($this->AuctionLog)) {
				array_pop($this->AuctionLog);
			}
			foreach($this->AuctionLog as $log) {
				$string	.= $log."\n";
			}
			WriteFile(AUCTION_ITEM_LOG,$string);
		}
	}
//////////////////////////////////////////////////
//	����ɽ��
	function ShowLog() {
		if(!$this->AuctionLog)
			$this->LoadLog();
		if(!$this->AuctionLog)
			return false;
		foreach($this->AuctionLog as $log) {
			print("{$log}<br />\n");
		}
	}
//////////////////////////////////////////////////
//	�����ɲ�
	function AddLog($string) {
		if(!$this->AuctionLog)
			$this->LoadLog();
		if(!$this->AuctionLog)
			$this->AuctionLog	= array();
		array_unshift($this->AuctionLog,$string);
	}


}
//////////////////////////////////////////////////
//	�����ƥ���ֹ����¤��ؤ���
	function ItemArticleSortByNo($a,$b) {
		if($a["No"] == $b["No"])
			return 0;
		return ($a["No"] > $b["No"]) ? 1:-1;
	}
//////////////////////////////////////////////////
//	�����ƥ��Ĥ���ֽ���¤��Ѥ���
	function ItemArticleSortByTime($a,$b) {
		if($a["end"] == $b["end"])
			return 0;
		return ($a["end"] > $b["end"]) ? 1:-1;
	}
//////////////////////////////////////////////////
//	�����ƥ����ʽ���¤��ؤ���
	function ItemArticleSortByPrice($a,$b) {
		if($a["price"] == $b["price"])
			return 0;
		return ($a["price"] > $b["price"]) ? -1:1;
	}
//////////////////////////////////////////////////
//	�����ƥ����ʽ���¤��ؤ���(���㤯)
	function ItemArticleSortByRPrice($a,$b) {
		if($a["price"] == $b["price"])
			return 0;
		return ($a["price"] > $b["price"]) ? 1:-1;
	}
//////////////////////////////////////////////////
//	�����ƥ������������¤��ؤ���
	function ItemArticleSortByTotalBid($a,$b) {
		if($a["TotalBid"] == $b["TotalBid"])
			return 0;
		return ($a["TotalBid"] > $b["TotalBid"]) ? -1:1;
	}
//////////////////////////////////////////////////
//	�Ĥ���֤��֤�
	function AuctionLeftTime($now,$end,$int=false) {
		$left	= $end - $now;
		// $int=true �ʤ麹ʬ�����֤�
		if($int)
			return $left;
		if($left < 1) {// ��λ���Ƥ������false
			return false;
		}
		if($left < 601) {
			return "{$left}��";
		} else if($left < 3601) {
			$minutes	= floor($left/60);
			return "{$minutes}ʬ";
		} else {
			$hour	= floor($left/3600);
			$minutes	= floor(($left%3600)/60);
			return "{$hour}����{$minutes}ʬ";
		}
	}
//////////////////////////////////////////////////
//	������ʤ��֤�
//	���ʤ�5%�������͡�
//	100�ʲ��ʤ�100������ˤʤ롣
	function BottomPrice($price) {
		$bottom	= floor($price * 0.10);
		if($bottom < 101)
			return sprintf("%0.0f",$price + 100);
		else
			return sprintf("%0.0f",$price + $bottom);
	}
?>