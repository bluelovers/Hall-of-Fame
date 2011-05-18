<?
//////////////////////////////////////////////////
//	Ź����äƤ��Υǡ���
	function ShopList() {
		return array(//Ź����ꥹ��
		1002,1003,1004,1100,1101,1200,
		1700,1701,1702,1703,1800,1801,2000,2001,
		3000,3001,3002,3100,3101,5000,5001,5002,5003,
		5100,5101,5102,5103,5200,5201,5202,5203,
		5500,5501,
		7000,7001,7500,
		//7510,7511,7512,7513,7520,// �ꥻ�åȷϥ����ƥ�
		8000,8009,
		);
	}
//////////////////////////////////////////////////
//	�����������˽��ʲ�ǽ�ʥ����ƥ�μ���
	function CanExhibitType() {
		return array(
		"Sword"	=> "1",
		"TwoHandSword"	=> "1",
		"Dagger"	=> "1",
		"Wand"	=> "1",
		"Staff"	=> "1",
		"Bow"	=> "1",
		"Whip"	=> "1",
		"Shield"	=> "1",
		"Book"	=> "1",
		"Armor"	=> "1",
		"Cloth"	=> "1",
		"Robe"	=> "1",
		"Item"	=> "1",
		"Material"	=> "1",
		);
	}
//////////////////////////////////////////////////
//	��ϣ��ǽ�ʥ����ƥ�μ���
	function CanRefineType() {
		return array(
		"Sword","TwoHandSword","Dagger",
		"Wand","Staff","Bow",
		"Whip",
		"Shield","Book",
		"Armor","Cloth","Robe",
		);
	}
//////////////////////////////////////////////////
//	�����ڤ쥢������Ȥΰ��ƺ��
	function DeleteAbandonAccount() {
		$list	= glob(USER."*");
		$now	= time();

		// �桼�����������������
		foreach($list as $file) {
			if(!is_dir($file)) continue;
			$UserID	= substr($file,strrpos($file,"/")+1);
			$user	= new user($UserID,true);

			// �ä����桼����
			if($user->IsAbandoned())
			{
				// ��󥭥󥰤��ɤ�
				if(!isset($Ranking))
				{
					include_once(CLASS_RANKING);
					$Ranking	= new Ranking();
					$RankChange	= false;// ��󥭥󥰥ǡ������ѹ����줿��
				}

				// ��󥭥󥰤���ä�
				if( $Ranking->DeleteRank($UserID) ) {
					$RankChange	= true;// �ѹ����줿
				}

				RecordManage(date("Y M d G:i:s",$now).": user ".$user->id." deleted.");
				$user->DeleteUser(false);//��󥭥󥰤���Ͼä��ʤ��褦��false
			}
			// �ä���ʤ��桼����
				else
			{
				$user->fpCloseAll();
				unset($user);
			}
		}

		// ���̤�桼�������å�������ä��Τǥ�󥭥󥰤�ɤ����뤫
		if($RankChange === true)
			$Ranking->SaveRanking();
		else if($RankChange === false)
			$Ranking->fpclose();

		//print("<pre>".print_r($list,1)."</pre>");
	}
//////////////////////////////////////////////////
//	���Ū�˴������벿��
	function RegularControl($value=null) {
		/*
			�����Ф���(����)�����ʻ����Ӥϸ�󤷤ˤ��롣
			PM 7:00 - AM 2:00 �Ͻ������ʤ���
			������� or �ʤΤ���ա�
		*/
		if(19 <= date("H") || date("H") <= 1)
			 return false;

		$now	= time();

		$fp		= FileLock(CTRL_TIME_FILE,true);
		if(!$fp)
			return false;
		//$ctrltime	= file_get_contents(CTRL_TIME_FILE);
		$ctrltime	= trim(fgets($fp, 1024));
		// �������ޤ��ʤ齪λ
		if($now < $ctrltime)
		{
			fclose($fp);
			unset($fp);
			return false;
		}

		// �����ν���
		RecordManage(date("Y M d G:i:s",$now).": auto regular control by {$value}.");

		DeleteAbandonAccount();//����1 �����桼�����ݽ�

		// �������������ä��鼡�δ��������񤭹���ǽ�λ���롣
		WriteFileFP($fp,$now + CONTROL_PERIOD);
		fclose($fp);
		unset($fp);
	}
//////////////////////////////////////////////////
//	$id �������Ͽ���줿���ɤ���
	function is_registered($id) {
		if($registered = @file(REGISTER)):
			if(array_search($id."\n",$registered)!==false && !ereg("[\.\/]+",$id) )//���Ե���ɬ��
				return true;
			else
				return false;
		endif;
	}
//////////////////////////////////////////////////
//	�ե������å������ե�����ݥ��󥿤��֤���
	function FileLock($file,$noExit=false) {

		if(!file_exists($file))
			return false;

		$fp	= @fopen($file,"r+") or die("Error!");
		if(!$fp)
			return false;

		$i=0;
		do{
			if(flock($fp, LOCK_EX | LOCK_NB)) {
				stream_set_write_buffer($fp, 0);
				return $fp;
			} else {
				usleep(10000);//0.01��
				$i++;
			}
		}while($i<5);

		if($noExit) {
			return false;
		} else {
			ob_clean();
			exit("file lock error.");
		}
		//flock($fp, LOCK_EX);//��¾
		//flock($fp, LOCK_SH);//��ͭ��å�
		//flock($fp,LOCK_EX);

		return $fp;
	}
//////////////////////////////////////////////////
//	�ե�����˽񤭹���(����:�ե�����ݥ���)
	function WriteFileFP($fp,$text,$check=false) {
		if(!$check && !trim($text))//$text������ʤ齪���
			return false;
		/*if(file_exists($file)):
			ftruncate()
		else:
			$fp	= fopen($file,"w+");*/
		ftruncate($fp,0);
		rewind($fp);
		//$fp	= fopen($file,"w+");
		//flock($fp,LOCK_EX);
		fputs($fp,$text);
		//print("<br>"."<br>".$text);
	}

//////////////////////////////////////////////////
//	�ե�����˽񤭹���
	function WriteFile($file,$text,$check=false) {
		if(!$check && !$text)//$text������ʤ齪���
			return false;
		/*if(file_exists($file)):
			ftruncate()
		else:
			$fp	= fopen($file,"w+");*/
		$fp	= fopen($file,"w+");
		flock($fp,LOCK_EX);
		fputs($fp,$text);
	}

//////////////////////////////////////////////////
//	�ե�������ɤ������˳�Ǽ(����:�ե�����ݥ���)
	function ParseFileFP($fp) {

		if(!$fp) return false;
		while( !feof($fp) ) {
			$str	= fgets($fp);
			$str	= trim($str);
			if(!$str) continue;
			$pos	= strpos($str,"=");
			if($pos === false)
				continue;
			$key	= substr($str,0,$pos);
			$val	= substr($str,++$pos);
			$data[$key]	= trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if($data)
			return $data;
		else
			return false;
	}
//////////////////////////////////////////////////
//	�ե�������ɤ������˳�Ǽ
	function ParseFile($file) {

		$fp		= fopen($file,"r+");
		if(!$fp) return false;
		flock($fp, LOCK_EX | LOCK_NB);
		while( !feof($fp) ) {
			$str	= fgets($fp);
			$str	= trim($str);
			if(!$str) continue;
			$pos	= strpos($str,"=");
			if($pos === false)
				continue;
			$key	= substr($str,0,$pos);
			$val	= substr($str,++$pos);
			$data[$key]	= trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if($data)
			return $data;
		else
			return false;
	}
//////////////////////////////////////////////////
//	
	function UserAmount() {
		static $amount;

		if($amount) {
			return $amount;
		} else {
			$amount	= count(glob(USER."*"));
			return $amount;
		}
	}
//////////////////////////////////////////////////
//	
	function JudgeList(){

		// ��ư�ɤ߹���(for�ǥ롼�פ����Ƥ뤫��̵�̤ʽ���)
		if(JUDGE_LIST_AUTO_LOAD) {
			for($i=1000; $i<2500; $i++) {
				if( LoadJudgeData($i) !== false)
					$list[]=$i;
			}
			return $list;
		// ��ư(�ɲä���Ƚ�Ǥϼ�ʬ�ǽ�­��)
		} else {
		return array(
1000, 1001, 1099, 1100, 1101,
1105, 1106, 1110, 1111, 1121,
1125, 1126, 1199, 1200, 1201,
1205, 1206, 1210, 1211, 1221,
1225, 1226, 1399, 1400, 1401,
1405, 1406, 1410, 1449, 1450,
1451, 1455, 1456, 1499, 1500,
1501, 1505, 1506, 1510, 1511,
1549, 1550, 1551, 1555, 1556,
1560, 1561, 1599, 1600, 1610,
1611, 1612, 1613, 
1614, 1615, 1616, 1617, 1618,
1699,
1700, 1701, 1710, 1711, 1712,
1715, 1716, 1717, 1749, 1750,
1751, 1752, 1755, 1756, 1757,
1799, 1800, 1801, 1805, 1819,
1820, 1821, 1825, 1839, 1840,
1841, 1845, 1849, 1850, 1851,
1855, 1899, 1900, 1901, 1902,
1919, 1920, 1939, 1940, 
); 
		}

	}

//////////////////////////////////////////////////
//	�����ɽ������
	function MoneyFormat($number) {
		return '$&nbsp;'.number_format($number);
	}
//////////////////////////////////////////////////
//	
	function ItemSellPrice($item) {
		$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
		return $price;
	}

//////////////////////////////////////////////////
//	��Ʈ����ɽ��
function ShowLogList() {
	print("<div style=\"margin:15px\">\n");
	/*// �����ʤ��ʤ�����ɽ������Ф�������
	// common
	print("<h4>�Ƕ����Ʈ(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	// union
	print("<h4>��˥�����(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	// rank
	print("<h4>��󥭥���(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	*/

	print("<a href=\"?log\" class=\"a0\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");

	// common
	print("<h4>�Ƕ����Ʈ - <a href=\"?clog\">��ɽ��</a>(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// union
	$limit	= 0;
	print("<h4>��˥����� - <a href=\"?ulog\">��ɽ��</a>(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// rank
	$limit	= 0;
	print("<h4>��󥭥��� - <a href=\"?rlog\">��ɽ��</a>(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}

	print("</div>\n");
}
//////////////////////////////////////////////////
//	��Ʈ����ɽ��
function LogShowCommon() {
	print("<div style=\"margin:15px\">\n");
	
	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\" class=\"a0\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");
	// common
	print("<h4>�Ƕ����Ʈ - ����(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	��Ʈ����ɽ��(union)
function LogShowUnion() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\" class=\"a0\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");
	// union
	print("<h4>��˥����� - ����(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	��Ʈ����ɽ��(ranking)
function LogShowRanking() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\" class=\"a0\">Ranking</a>");
	// rank
	print("<h4>��󥭥��� - ����(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	��Ʈ���ξܺ٤�ɽ��(���)
function BattleLogDetail($log,$type=false) {
	$fp	= fopen($log,"r");

	// ���Ԥ����ɤ߹��ࡣ
	$time	= fgets($fp);//���ϻ��� 1����
	$team	= explode("<>",fgets($fp));//������̾ 2����
	$number	= explode("<>",trim(fgets($fp)));//�Ϳ� 3����
	$avelv	= explode("<>",trim(fgets($fp)));//ʿ�ѥ�٥� 4����
	$win	= trim(fgets($fp));// ���������� 5����
	$act	= trim(fgets($fp));// ���ư�� 6����
	fclose($fp);

	$date	= date("m/d H:i:s",substr($time,0,10));
	// ����������ˤ�äƿ���ʬ����ɽ��
	if($type == "RANK")
		print("[ <a href=\"?rlog={$time}\">{$date}</a> ]&nbsp;\n");
	else if($type == "UNION")
		print("[ <a href=\"?ulog={$time}\">{$date}</a> ]&nbsp;\n");
	else
		print("[ <a href=\"?log={$time}\">{$date}</a> ]&nbsp;\n");
	print("<span class=\"bold\">$act</span>turns&nbsp;\n");//�������
	if($win === "0")
		print("<span class=\"recover\">{$team[0]}</span>");
	else if($win === "1")
		print("<span class=\"dmg\">{$team[0]}</span>");
	else
		print("{$team[0]}");

	print("({$number[0]}:{$avelv[0]})");

	print(" vs ");

	if($win === "0")
		print("<span class=\"dmg\">{$team[1]}</span>");
	else if($win === "1")
		print("<span class=\"recover\">{$team[1]}</span>");
	else
		print("{$team[1]}");

	print("({$number[1]}:{$avelv[1]})<br />");
}
//////////////////////////////////////////////////
//	��Ʈ�����������
function ShowBattleLog($no,$type=false) {
	if($type == "RANK")
		$file	= LOG_BATTLE_RANK.$no.".dat";
	else if($type == "UNION")
		$file	= LOG_BATTLE_UNION.$no.".dat";
	else
		$file	= LOG_BATTLE_NORMAL.$no.".dat";
	if(!file_exists($file)) {//����̵��
		print("log doesnt exists");
		return false;
	}

	$log	= file($file);
	$row	= 6;//���β����ܤ���񤭽Ф���?
	$time	= substr($log[0],0,10);

	//print('<table style="width:100%;text-align:center" class="break"><tr><td>'."\n");
	print('<div style="padding:15px 0;width:100%;text-align:center" class="break">');
	print("<h2>battle log*</h2>");
	print("\nthis battle starts at<br />");
	print(date("m/d H:i:s",substr($time,0,10)));
	print("</div>\n");
	//print("</td></tr></table>\n");

	while($log["$row"]) {
		print($log["$row"]);
		$row++;
	}
}
//////////////////////////////////////////////////
//	���ξܺ٤�ɽ��
	function ShowSkillDetail($skill,$radio=false) {
		if(!$skill) return false;
		
		if($radio)
			print('<input type="radio" name="newskill" value="'.$skill["no"].'" class="vcent" />');

		print('<img src="'.IMG_ICON.$skill["img"].'" class="vcent">');
		print("{$skill[name]}");

		if($radio)
			print(" / <span class=\"bold\">{$skill[learn]}</span>pt");

		if($skill[target][0] == "all")//�о�
			print(" / <span class=\"charge\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "enemy")
			print(" / <span class=\"dmg\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "friend")
			print(" / <span class=\"recover\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "self")
			print(" / <span class=\"support\">{$skill[target][0]}</span>");
		else if(isset($skill[target][0]))
			print(" / {$skill[target][0]}");

		if($skill[target][1] == "all")//ñ��orʣ��or����
			print(" - <span class=\"charge\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "individual")
			print(" - <span class=\"recover\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "multi")
			print(" - <span class=\"spdmg\">{$skill[target][1]}</span>");
		else if(isset($skill[target][1]))
			print(" - {$skill[target][1]}");

		if(isset($skill["sacrifice"]))
			print(" / <span class=\"dmg\">Sacrifice:{$skill[sacrifice]}%</span>");
		// ����SP
		if(isset($skill["sp"]))
			print(" / <span class=\"support\">{$skill[sp]}sp</span>");
		// ����������
		if($skill["MagicCircleDeleteTeam"])
			print(" / <span class=\"support\">MagicCircle x".$skill["MagicCircleDeleteTeam"]."</span>");
		if($skill["pow"]) {
			print(" / <span class=\"".($skill["support"]?"recover":"dmg")."\">{$skill[pow]}%</span>x");
			print(( $skill["target"][2] ? $skill["target"][2] : "1" ) );
		}
		if($skill["type"] == 1)
			print(" / <span class=\"spdmg\">Magic</span>");
		if($skill["quick"])
			print(" / <span class=\"charge\">Quick</span>");
		if($skill["invalid"])
			print(" / <span class=\"charge\">invalid</span>");
		if($skill["priority"] == "Back")
			print(" / <span class=\"support\">BackAttack</span>");
		if($skill["CurePoison"])
			print(" / <span class=\"support\">CurePoison</span>");

		if($skill["delay"])
			print(" / <span class=\"support\">Delay-".$skill[delay]."%</span>");
//		if($skill["support"])
//			print(" / <span class=\"charge\">support</span>");

		if($skill["UpMAXHP"])
			print(" / <span class=\"charge\">MaxHP+".$skill[UpMAXHP]."%</span>");
		if($skill["UpMAXSP"])
			print(" / <span class=\"charge\">MaxSP+".$skill[UpMAXSP]."%</span>");
		if($skill["UpSTR"])
			print(" / <span class=\"charge\">Str+".$skill[UpSTR]."%</span>");
		if($skill["UpINT"])
			print(" / <span class=\"charge\">Int+".$skill[UpINT]."%</span>");
		if($skill["UpDEX"])
			print(" / <span class=\"charge\">Dex+".$skill[UpDEX]."%</span>");
		if($skill["UpSPD"])
			print(" / <span class=\"charge\">Spd+".$skill[UpSPD]."%</span>");
		if($skill["UpLUK"])
			print(" / <span class=\"charge\">Luk+".$skill[UpLUK]."%</span>");
		if($skill["UpATK"])
			print(" / <span class=\"charge\">Atk+".$skill[UpATK]."%</span>");
		if($skill["UpMATK"])
			print(" / <span class=\"charge\">Matk+".$skill[UpMATK]."%</span>");
		if($skill["UpDEF"])
			print(" / <span class=\"charge\">Def+".$skill[UpDEF]."%</span>");
		if($skill["UpMDEF"])
			print(" / <span class=\"charge\">Mdef+".$skill[UpMDEF]."%</span>");

		if($skill["DownMAXHP"])
			print(" / <span class=\"dmg\">MaxHP-".$skill[DownMAXHP]."%</span>");
		if($skill["DownMAXSP"])
			print(" / <span class=\"dmg\">MaxSP-".$skill[DownMAXSP]."%</span>");
		if($skill["DownSTR"])
			print(" / <span class=\"dmg\">Str-".$skill[DownSTR]."%</span>");
		if($skill["DownINT"])
			print(" / <span class=\"dmg\">Int-".$skill[DownINT]."%</span>");
		if($skill["DownDEX"])
			print(" / <span class=\"dmg\">Dex-".$skill[DownDEX]."%</span>");
		if($skill["DownSPD"])
			print(" / <span class=\"dmg\">Spd-".$skill[DownSPD]."%</span>");
		if($skill["DownLUK"])
			print(" / <span class=\"dmg\">Luk-".$skill[DownLUK]."%</span>");
		if($skill["DownATK"])
			print(" / <span class=\"dmg\">Atk-".$skill[DownATK]."%</span>");
		if($skill["DownMATK"])
			print(" / <span class=\"dmg\">Matk-".$skill[DownMATK]."%</span>");
		if($skill["DownDEF"])
			print(" / <span class=\"dmg\">Def-".$skill[DownDEF]."%</span>");
		if($skill["DownMDEF"])
			print(" / <span class=\"dmg\">Mdef-".$skill[DownMDEF]."%</span>");

		if($skill["PlusSTR"])
			print(" / <span class=\"charge\">Str+".$skill[PlusSTR]."</span>");
		if($skill["PlusINT"])
			print(" / <span class=\"charge\">Int+".$skill[PlusINT]."</span>");
		if($skill["PlusDEX"])
			print(" / <span class=\"charge\">Dex+".$skill[PlusDEX]."</span>");
		if($skill["PlusSPD"])
			print(" / <span class=\"charge\">Spd+".$skill[PlusSPD]."</span>");
		if($skill["PlusLUK"])
			print(" / <span class=\"charge\">Luk+".$skill[PlusLUK]."</span>");

		if($skill["charge"]["0"] || $skill["charge"]["1"]) {
			print(" / (".($skill["charge"]["0"]?$skill["charge"]["0"]:"0").":");
			print(($skill["charge"]["1"]?$skill["charge"]["1"]:"0").")");
		}

		// �������ɽ��
		if($skill["limit"]) {
			$Limit	= " / Limit:";
			foreach($skill["limit"] as $type => $bool) {
				$Limit .= $type.", ";
			}
			print(substr($Limit,0,-2));
		}
		if($skill["exp"])
			print(" / {$skill[exp]}");
		print("\n");
	}
//////////////////////////////////////////////////
//	�����ƥ�ξܺ٤��֤�...����äȽ����������ʡ�
	function ShowItemDetail($item,$amount=false,$text=false,$need=false) {
		if(!$item) return false;

		$html	= "<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\">";
		// ��ϣ��
		if($item["refine"])
			$html	.= "+{$item[refine]} ";
		if($item["AddName"])
			$html	.= "{$item[AddName]} ";
		$html	.= "{$item[base_name]}";// ̾��

		if($item["type"])
			$html	.= "<span class=\"light\"> ({$item[type]})</span>";
		if($amount) {//����
			$html	.= " x<span class=\"bold\" style=\"font-size:80%\">{$amount}</span>";
		}
		if($item["atk"]["0"])//ʪ������
			$html	.= ' / <span class="dmg">Atk:'.$item[atk][0].'</span>';
		if($item["atk"]["1"])//��ˡ����
			$html	.= ' / <span class="spdmg">Matk:'.$item[atk][1].'</span>';
		if($item["def"]) {
			$html	.= " / <span class=\"recover\">Def:{$item[def][0]}+{$item[def][1]}</span>";
			$html	.= " / <span class=\"support\">Mdef:{$item[def][2]}+{$item[def][3]}</span>";
		}
		if($item["P_SUMMON"])
			$html	.= ' / <span class="support">Summon+'.$item["P_SUMMON"].'%</span>';
		if(isset($item["handle"]))
			$html	.= ' / <span class="charge">h:'.$item[handle].'</span>';
		if($item["option"])
			$html	.= ' / <span style="font-size:80%">'.substr($item["option"],0,-2)."</span>";

		if($need && $item["need"]) {
			$html	.= " /";
			foreach($item["need"] as $M_itemNo => $M_amount) {
				$M_item	= LoadItemData($M_itemNo);
				$html	.= "<img src=\"".IMG_ICON.$M_item["img"]."\" class=\"vcent\">";
				$html	.= "{$M_item[base_name]}";// ̾��
				$html	.= " x<span class=\"bold\" style=\"font-size:80%\">{$M_amount}</span>";
				if($need["$M_itemNo"])
				$html	.= "<span class=\"light\">(".$need["$M_itemNo"].")</span>";
			}
		}

		if($text)
			return $html;

		print($html);
	}

//////////////////////////////////////////////////
//	�֤��ٹ�ʸ�ǥ��顼ɽ��
	function ShowResult($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="result'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	�֤��ٹ�ʸ�ǥ��顼ɽ��
	function ShowError($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="error'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	�ޥ˥奢���ɽ������
	function ShowManual() {
		include(MANUAL);
		return true;
	}
//////////////////////////////////////////////////
//	�ޥ˥奢���ɽ������
	function ShowManual2() {
		include(MANUAL_HIGH);
		return true;
	}
//////////////////////////////////////////////////
//	���塼�ȥꥢ���ɽ������
	function ShowTutorial() {
		include(TUTORIAL);
		return true;
	}
//////////////////////////////////////////////////
//	�������Ƥ�ɽ��
	function ShowUpDate() {
		print('<div style="margin:15px">');
		print("<p><a href=\"?\">Back</a><br><a href=\"#btm\">to bottom</a></p>");

		if($_POST["updatetext"]) {
			$update	= htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
			$update	= stripslashes($update);
		} else
			$update	= @file_get_contents(UPDATE);

		print('<form action="?update" method="post">');
		if($_POST["updatepass"] == UP_PASS) {
			print('<textarea class="text" rows="12" cols="60" name="updatetext">');
			print("$update");
			print('</textarea><br>');
			print('<input type="submit" class="btn" value="update">');
			print('<a href="?update">�����</a><br>');
		}

		print(nl2br($update)."\n");
		print('<br><a name="btm"></a>');
		if($_POST["updatepass"] == UP_PASS && $_POST["updatetext"]) {
			$fp	= fopen(UPDATE,"w");
			$text	= htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
			$text	= stripslashes($text);
			flock($fp,2);
			fputs($fp,$text);
			fclose($fp);
		}
print <<< EOD
	<input type="password" class="text" name="updatepass" style="width:100px" value="$_POST[updatepass]">
	<input type="submit" class="btn" value="update">
	</form>
EOD;
		print("<p><a href=\"?\">Back</a></p></div>");
	}
//////////////////////////////////////////////////
//	������ǡ���
	function ShowGameData() {
		?>
<div style="margin:15px">
<h4>GameData</h4>
<div style="margin:0 20px">
| <a href="?gamedata=job">��(Job)</a> | 
<a href="?gamedata=item">�����ƥ�(item)</a> | 
<a href="?gamedata=judge">Ƚ��</a> | 
<a href="?gamedata=monster">��󥹥���</a> | 
</div>
</div><?
	switch($_GET["gamedata"]) {
		case "job": include(GAME_DATA_JOB); break;
		case "item": include(GAME_DATA_ITEM); break;
		case "judge": include(GAME_DATA_JUDGE); break;
		case "monster": include(GAME_DATA_MONSTER); break;
		default: include(GAME_DATA_JOB); break;
	}

	}
//////////////////////////////////////////////////
//	
	function userNameLoad() {
		$name	= @file(USER_NAME);
		if($name) {
			foreach($name as $key => $var) {
				$name[$key]	= trim($name[$key]);
				if($name[$key] === "")
					unset($name[$key]);
			}
			return $name;
		} else {
			return array();
		}
	}
//////////////////////////////////////////////////
//	
	function userNameAdd($add) {
		foreach(userNameLoad() as $name) {
			$string	.= $name."\n";
		}
		$string .= $add."\n";
		$fp	= fopen(USER_NAME,"w+");
		flock($fp, LOCK_EX);
		fwrite($fp,$string);
		fclose($fp);
	}
//////////////////////////////////////////////////
//	����󥭥󥰤�ɽ��
	function RankAllShow() {
		print('<div style="margin:15px">'."\n");
		print('<h4>Ranking - '.date("Yǯn��j�� G��iʬs��").'</h4>'."\n");
		include(CLASS_RANKING);
		$Rank	= new Ranking();
		$Rank->ShowRanking();
		print('</div>'."\n");
	}
//////////////////////////////////////////////////
//	
	function RecordManage($string) {
		$file	= MANAGE_LOG_FILE;

		$fp	= @fopen($file,"r+") or die();
		$text	= fread($fp,2048);
		ftruncate($fp,0);
		rewind($fp);
		fwrite($fp,$string."\n".$text);
	}

	/*
	*	���Ϥ��줿ʸ������ǧ����
	*	�֤���
	*	���� = array(true,�Ѵ�($string));
	*	���� = array(false,������ͳ);
	*/
	function CheckString($string,$maxLength=16) {
		$string	= trim($string);
		$string	= stripslashes($string);
		if(is_numeric(strpos($string,"\t"))) {
			return array(false,"������ʸ��");
		}
		if(is_numeric(strpos($string,"\n"))) {
			return array(false,"������ʸ��");
		}
		if (!$string) {
			return array(false,"̤����");
		}
		$length	= strlen($string);
		if ( 0 == $length || $maxLength < $length) {
			return array(false,"Ĺ������û������");
		}
		$string	= htmlspecialchars($string,ENT_QUOTES);
		return array(true,$string);
	}
///////////////////////////////////////////////////
//	ü����Ƚ�ǡ�
	function isMobile() {
		if(strstr($_SERVER['HTTP_USER_AGENT'],"DoCoMo")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Vodafone")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"SoftBank")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"MOT-")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"J-PHONE")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"KDDI")){
			//$env = 'ez';
			$env = 'ez';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"UP.Browser")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"WILLCOM")){
			$env = 'ez';
		}else{
			$env = 'pc';
		}
		return $env;
	}
//////////////////////////////////////////////////
//	DUMP
	if(!function_exists("dump"))  {
		function dump($array) {
			print("<pre>".print_r($array,1)."</pre>");
		}
	}
?>