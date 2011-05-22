<?php 
if(file_exists(DATA_ENCHANT))
	include(DATA_ENCHANT);

function LoadItemData($no) {
	$base	= substr($no,0,4);//道具種類
	$refine	= (int)substr($no,4,2);//精煉值
	// 附加值
	$option0	= substr($no,6,3);
	$option1	= substr($no,9,3);
	$option2	= substr($no,12,3);

/*
 * 道具設定
 * ---------------------------------------------
 * "name"=>"道具名",
 * "type"=>"種類",
 * "buy"=>"買值",
 * "img"=>"對應圖",
 * "atk"=>array(物攻,魔攻),
 * "def"=>array(物理%，物理減，魔法%，魔法減),
 * "dh"=> true,//是否佔用雙手
 * "handle"=>"裝備值",
 * "need" => array("素材id"=>數量, ...),
 * ---------------------------------------------
 * type
 * "劍"	單手劍
 * "雙手劍"	雙手劍
 * "匕首"	短劍
 * "槍"	雙手槍
 * "Pike"	單手槍
 * "斧"	雙手斧
 * "短柄斧"單手斧
 * "魔杖"	單手杖
 * "杖"	雙手杖
 * "錘"	鈍器(單手)
 * "弓"	弓
 * "弩"	石弓
 * 
 * "盾"	盾
 * "MainGauche"	防禦用短劍
 * "書"	書
 * 
 * "甲"	鎧
 * "衣服"	服
 * "長袍"	衣
 * 
 * "?"
 *--------------------------------------------
	追加項
	P_MAXHP
	M_MAXHP
	P_MAXSP
	M_MAXSP
	P_STR
	P_INT
	P_DEX
	P_SPD
	P_LUK
	P_SUMMON = 強化召喚
	P_PIERCE = array(物理,魔法),
 *--------------------------------------------
 */
	switch($base) {
		case "1000":	//	1000-1100	劍
$item	= array(
"name"	=> "短劍",
"type"	=> "劍",
"buy"	=> "500",
"img"	=> "we_sword026.png",
"atk"	=> array(10,0),
"handle"=> "1",
"need"	=> array("6001"=>"4",),
); break;
		case "1001":
$item	= array(
"name"	=> "長刀",
"type"	=> "劍",
"buy"	=> "1000",
"img"	=> "we_sword026.png",
"atk"	=> array(15,0),
"handle"=> "2",
"need"	=> array("6001"=>"6","6002"=>"2",),
); break;
		case "1002":
$item	= array(
"name"	=> "重劍",
"type"	=> "劍",
"buy"	=> "3000",
"img"	=> "we_sword026.png",
"atk"	=> array(20,0),
"handle"=> "2",
"need"	=> array("6001"=>"4","6002"=>"4",),
); break;
		case "1003":
$item	= array(
"name"	=> "刺劍",
"type"	=> "劍",
"buy"	=> "5000",
"img"	=> "we_sword026.png",
"atk"	=> array(25,0),
"handle"=> "3",
"need"	=> array("6001"=>"2","6002"=>"8",),
); break;
		case "1004":
$item	= array(
"name"	=> "砍刀",
"type"	=> "劍",
"buy"	=> "8000",
"img"	=> "we_sword026.png",
"atk"	=> array(30,0),
"handle"=> "4",
"need"	=> array("6002"=>"8","6003"=>"2",),
); break;
		case "1005":
$item	= array(
"name"	=> "長劍",
"type"	=> "劍",
"buy"	=> "14000",
"img"	=> "we_sword026.png",
"atk"	=> array(40,0),
"handle"=> "5",
"need"	=> array("6003"=>"12",),
); break;
		case "1006":
$item	= array(
"name"	=> "寬劍",
"type"	=> "劍",
"buy"	=> "20000",
"img"	=> "we_sword026.png",
"atk"	=> array(50,0),
"handle"=> "6",
"need"	=> array("6002"=>"4","6003"=>"16",),
); break;
		case "1007":
$item	= array(
"name"	=> "彎刀",
"type"	=> "劍",
"buy"	=> "35000",
"img"	=> "we_sword026.png",
"atk"	=> array(60,0),
"handle"=> "7",
"need"	=> array("6003"=>"24",),
); break;
		case "1008":
$item	= array(
"name"	=> "波形刀",
"type"	=> "劍",
"buy"	=> "60000",
"img"	=> "we_sword026.png",
"atk"	=> array(80,0),
"handle"=> "10",
"need"	=> array("6003"=>"32",),
); break;
		case "1020":
$item	= array(
"name"	=> "戮龍劍",
"type"	=> "劍",
"buy"	=> "70000",
"img"	=> "we_sword026.png",
"atk"	=> array(20,0),
"handle"=> "8",
"P_PIERCE"=> array(30,0),
"need"	=> array("6002"=>"15","6800"=>"1",),
"option"	=> "無視物理防禦+30 ,",
); break;
		case "1021":
$item	= array(
"name"	=> "權天使",
"type"	=> "劍",
"buy"	=> "100000",
"img"	=> "we_sword026.png",
"atk"	=> array(3,0),
"handle"=> "1",
); break;
		case "1022":
$item	= array(
"name"	=> "光天使",
"type"	=> "劍",
"buy"	=> "160000",
"img"	=> "we_sword026.png",
"atk"	=> array(80,0),
"def"	=> array(5,0,5,0),
"P_SPD"	=> 5,
"handle"=> "9",
"option"	=> "Def/Mdef+5 ,SPD+5 ,",
"need"	=> array("6002"=>"30","6003"=>"10","6802"=>"1",),
); break;
		case "1023":
$item	= array(
"name"	=> "香蕉劍",
"type"	=> "劍",
"buy"	=> "1000",
"img"	=> "banana.png",
"atk"	=> array(3,0),
"P_SPD"	=> 1,
"handle"=> "0",
"option"	=> "SPD+1 ,",
"need"	=> array("6600"=>"3","6602"=>"1",),
); break;

		case "1100":	//	1100-1200	雙手劍
$item	= array(
"name"	=> "殺手劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_sword006.png",
"atk"	=> array(30,0),
"handle"=> "2",
"need"	=> array("6001"=>"8",),
); break;
		case "1101":
$item	= array(
"name"	=> "大劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "5000",
"img"	=> "we_sword006.png",
"atk"	=> array(45,0),
"handle"=> "3",
"need"	=> array("6001"=>"6","6002"=>"4",),
); break;
		case "1102":
$item	= array(
"name"	=> "小型大劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "16000",
"img"	=> "we_sword006.png",
"atk"	=> array(65,0),
"handle"=> "5",
"need"	=> array("6001"=>"6","6002"=>"8",),
); break;
		case "1103":
$item	= array(
"name"	=> "巨劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_sword006.png",
"atk"	=> array(80,0),
"handle"=> "6",
"need"	=> array("6001"=>"2","6002"=>"6","6003"=>"8",),
); break;
		case "1104":
$item	= array(
"name"	=> "斬首劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "70000",
"img"	=> "we_sword006.png",
"atk"	=> array(100,0),
"handle"=> "8",
"need"	=> array("6002"=>"10","6003"=>"20",),
); break;
		case "1120":
$item	= array(
"name"	=> "斬龍劍",
"type"	=> "雙手劍",
"dh"	=> true,
"buy"	=> "80000",
"img"	=> "we_sword006.png",
"atk"	=> array(10,0),
"handle"=> "10",
"need"	=> array(),
"P_PIERCE"=> array(50,0),
"need"	=> array("6002"=>"5","6003"=>"10","6800"=>"1",),
"option"	=> "無視物理防禦+50 ,",
); break;
		case "1200":	// 1200-1300	短劍
$item	= array(
"name"	=> "短劍",
"type"	=> "匕首",
"buy"	=> "1000",
"img"	=> "we_sword010.png",
"atk"	=> array(7,0),
"handle"=> "1",
); break;
		case "1201":
$item	= array(
"name"	=> "Kukuri",
"type"	=> "匕首",
"buy"	=> "10000",
"img"	=> "we_sword010.png",
"atk"	=> array(21,0),
"handle"=> "3",
"need"	=> array("6001"=>12,"6020"=>4),
); break;
		case "1202":
$item	= array(
"name"	=> "斯巴達短劍",
"type"	=> "匕首",
"buy"	=> "20000",
"img"	=> "we_sword010.png",
"atk"	=> array(28,0),
"handle"=> "4",
"need"	=> array("6001"=>16,"6020"=>4),
); break;
		case "1203":
$item	= array(
"name"	=> "古羅馬短劍",
"type"	=> "匕首",
"buy"	=> "40000",
"img"	=> "we_sword010.png",
"atk"	=> array(34,0),
"handle"=> "5",
"need"	=> array("6002"=>12,"6020"=>4),
); break;
		case "1204":
$item	= array(
"name"	=> "暗殺匕首",
"type"	=> "匕首",
"buy"	=> "50000",
"img"	=> "we_sword010.png",
"atk"	=> array(40,0),
"handle"=> "6",
"need"	=> array("6003"=>10,"6020"=>4),
); break;
		case "1205":
$item	= array(
"name"	=> "破甲劍",
"type"	=> "匕首",
"buy"	=> "50000",
"img"	=> "we_sword010.png",
"atk"	=> array(20,0),
"handle"=> "6",
"P_PIERCE"	=>array(20,0),
"option"	=> "無視物理防禦+20 ,",
"need"	=> array("6003"=>20,"6022"=>4),
); break;

		case "1220":
$item	= array(
"name"	=> "香蕉匕首",
"type"	=> "匕首",
"buy"	=> "1000",
"img"	=> "banana.png",
"atk"	=> array(1,0),
"P_SPD"	=> 1,
"handle"=> "0",
"option"	=> "SPD+1 ,",
"need"	=> array("6600"=>"3","6602"=>"1",),
); break;

		case "1300":	//	1300-1400	雙手槍
$item	= array(
"name"	=> "戰戟",
"type"	=> "槍",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_spear016.png",
"atk"	=> array(28,0),
"handle"=> "2",
); break;
		case "1400":	//	1400-1500	單手槍
$item	= array(
"name"	=> "投槍",
"type"	=> "矛",
"buy"	=> "1000",
"img"	=> "we_spear012.png",
"atk"	=> array(14,0),
"handle"=> "2",
); break;
		case "1500":	//	1500-1600	雙手斧
$item	= array(
"name"	=> "大型斧",
"type"	=> "斧",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_axe013b.png",
"atk"	=> array(35,0),
"handle"=> "2",
); break;
		case "1600":	//	1600-1700	戰斧
$item	= array(
"name"	=> "戰斧",
"type"	=> "短柄斧",
"buy"	=> "1000",
"img"	=> "we_axe003.png",
"atk"	=> array(17,0),
"handle"=> "2",
); break;
		case "1700":	//	1700-1800	單手杖
$item	= array(
"name"	=> "棒",
"type"	=> "魔杖",
"buy"	=> "1000",
"img"	=> "we_staff002.png",
"atk"	=> array(1,5),
"handle"=> "1",
"need"	=> array("6020"=>"2","6001"=>"1",),
); break;
		case "1701":
$item	= array(
"name"	=> "短棒",
"type"	=> "魔杖",
"buy"	=> "2000",
"img"	=> "we_staff002.png",
"atk"	=> array(5,10),
"handle"=> "2",
"need"	=> array("6020"=>"4","6001"=>"1",),
); break;
		case "1702":
$item	= array(
"name"	=> "木棍",
"type"	=> "魔杖",
"buy"	=> "4000",
"img"	=> "we_staff002.png",
"atk"	=> array(8,15),
"handle"=> "3",
"need"	=> array("6020"=>"8","6002"=>"1",),
); break;
		case "1703":
$item	= array(
"name"	=> "銀棍",
"type"	=> "魔杖",
"buy"	=> "6000",
"img"	=> "we_staff002.png",
"atk"	=> array(6,20),
"handle"=> "4",
"need"	=> array("6002"=>"8","6020"=>"2"),
); break;
		case "1704":
$item	= array(
"name"	=> "戰鬥棍",
"type"	=> "魔杖",
"buy"	=> "10000",
"img"	=> "we_staff002.png",
"atk"	=> array(10,26),
"handle"=> "5",
"need"	=> array("6020"=>"10","6002"=>"4",),
); break;
		case "1705":
$item	= array(
"name"	=> "漂亮的棍",
"type"	=> "魔杖",
"buy"	=> "18000",
"img"	=> "we_staff002.png",
"atk"	=> array(5,32),
"handle"=> "6",
"need"	=> array("6021"=>"6","6002"=>"4",),
); break;
		case "1706":
$item	= array(
"name"	=> "巫師棍",
"type"	=> "魔杖",
"buy"	=> "25000",
"img"	=> "we_staff002.png",
"atk"	=> array(2,40),
"handle"=> "7",
"need"	=> array("6021"=>"10","6002"=>"4",),
); break;
		case "1800":	//	1800-1900	雙手杖
$item	= array(
"name"	=> "杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "2000",
"img"	=> "we_staff008b.png",
"atk"	=> array(8,25),
"handle"=> "2",
"need"	=> array("6002"=>"2","6020"=>"4"),
); break;
		case "1801":
$item	= array(
"name"	=> "長杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "5000",
"img"	=> "we_staff008b.png",
"atk"	=> array(4,37),
"handle"=> "3",
"need"	=> array("6021"=>"8"),
); break;
		case "1802":
$item	= array(
"name"	=> "魔術杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "14000",
"img"	=> "we_staff008b.png",
"atk"	=> array(15,49),
"handle"=> "5",
"need"	=> array("6002"=>"2","6021"=>"8",),
); break;
		case "1803":
$item	= array(
"name"	=> "銀杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "20000",
"img"	=> "we_staff008b.png",
"atk"	=> array(10,60),
"handle"=> "6",
"need"	=> array("6002"=>"12","6022"=>"1",),
); break;
		case "1804":
$item	= array(
"name"	=> "金杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_staff008b.png",
"atk"	=> array(10,72),
"handle"=> "7",
); break;
		case "1805":
$item	= array(
"name"	=> "水晶杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "35000",
"img"	=> "we_staff008b.png",
"atk"	=> array(12,84),
"handle"=> "8",
); break;
//--------------
		case "1810":
$item	= array(
"name"	=> "魔法杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "20000",
"img"	=> "we_staff008b.png",
"atk"	=> array(3,24),
"handle"=> "4",
"P_MAXSP"	=> "60",
"option"	=> "SP+60 ,",
"need"	=> array("6020"=> 8,"6021"=> 8,),
); break;
		case "1811":
$item	= array(
"name"	=> "天堂杖",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "35000",
"img"	=> "we_staff008b.png",
"atk"	=> array(12,32),
"handle"=> "6",
"P_MAXSP"	=> "90",
"option"	=> "SP+90 ,",
"need"	=> array("6020"=> 12,"6021"=> 12,),
); break;
		case "1812":
$item	= array(
"name"	=> "四季",
"type"	=> "杖",
"dh"	=> true,
"buy"	=> "60000",
"img"	=> "we_staff008b.png",
"atk"	=> array(12,40),
"handle"=> "8",
"P_MAXSP"	=> "130",
"option"	=> "SP+130 ,",
"need"	=> array("6020"=> 16,"6021"=> 16,),
); break;
		case "1900":	//	1900-2000	鈍器(單手
$item	= array(
"name"	=> "青銅錘",
"type"	=> "錘",
"buy"	=> "1000",
"img"	=> "we_axe015b.png",
"atk"	=> array(5,5),
"handle"=> "2",
); break;
		case "2000":	//	2000-2100	弓
$item	= array(
"name"	=> "短弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_bow001.png",
"atk"	=> array(20,0),
"handle"=> "2",
"need"	=> array("6020"=>"6","6181"=>"1",),
); break;
		case "2001":
$item	= array(
"name"	=> "復合弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "4000",
"img"	=> "we_bow001.png",
"atk"	=> array(30,0),
"handle"=> "6",
"need"	=> array("6020"=>"9","6181"=>"2",),
); break;
		case "2002":
$item	= array(
"name"	=> "大型弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "8000",
"img"	=> "we_bow001.png",
"atk"	=> array(40,0),
"handle"=> "12",
"need"	=> array("6021"=>"6","6181"=>"2",),
); break;
		case "2003":
$item	= array(
"name"	=> "獵手弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "14000",
"img"	=> "we_bow001.png",
"atk"	=> array(50,0),
"handle"=> "16",
"need"	=> array("6020"=>"4","6021"=>"4","6181"=>"4",),
); break;
		case "2004":
$item	= array(
"name"	=> "銀弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "20000",
"img"	=> "we_bow001.png",
"atk"	=> array(60,0),
"handle"=> "20",
"need"	=> array("6002"=>"4","6021"=>"6","6182"=>"2",),
); break;
		case "2005":
$item	= array(
"name"	=> "鋒利射手",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_bow001.png",
"atk"	=> array(70,0),
"handle"=> "24",
"need"	=> array("6021"=>"12","6182"=>"4",),
); break;
		case "2006":
$item	= array(
"name"	=> "羅賓漢弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "45000",
"img"	=> "we_bow001.png",
"atk"	=> array(80,0),
"handle"=> "28",
"need"	=> array("6021"=>"4","6022"=>"14","6182"=>"6",),
); break;
		case "2007":
$item	= array(
"name"	=> "變阻弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "60000",
"img"	=> "we_bow001.png",
"atk"	=> array(90,0),
"handle"=> "30",
); break;
		case "2008":
$item	= array(
"name"	=> "亞特米斯弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "100000",
"img"	=> "we_bow001.png",
"atk"	=> array(140,0),
"handle"=> "40",
); break;
		case "2020":
$item	= array(
"name"	=> "龍翼弓",
"type"	=> "弓",
"dh"	=> true,
"buy"	=> "120000",
"img"	=> "we_bow001.png",
"atk"	=> array(40,0),
"handle"=> "30",
"P_PIERCE"=> array(40,0),
"need"	=> array("6022"=>"10","6182"=>"5","6801"=>"1",),
"option"	=> "無視物理防禦+40 ,",
); break;

						//	2100-2199	石弓
		case "2100":
$item	= array(
"name"	=> "弩手",
"type"	=> "弩",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_bow013.png",
"atk"	=> array(25,0),
"handle"=> "2",
); break;
						//	2200-2299	鞭
		case "2200":
$item	= array(
"name"	=> "馴獸鞭",
"type"	=> "鞭",
"buy"	=> "1000",
"img"	=> "we_other007.png",
"atk"	=> array(20,0),
"handle"=> "4",
"P_SUMMON"	=> "10",
"need"	=> array("6181"=>"8",),
); break;
		case "2201":
$item	= array(
"name"	=> "長鞭",
"type"	=> "鞭",
"buy"	=> "20000",
"img"	=> "we_other007.png",
"atk"	=> array(30,0),
"handle"=> "8",
"P_SUMMON"	=> "15",
"need"	=> array("6040"=>"4","6181"=>"12",),
); break;
		case "2202":
$item	= array(
"name"	=> "蟒鞭",
"type"	=> "鞭",
"buy"	=> "30000",
"img"	=> "we_other007.png",
"atk"	=> array(40,0),
"handle"=> "12",
"P_SUMMON"	=> "20",
"need"	=> array("6040"=>"6","6181"=>"16",),
); break;
		case "2203":
$item	= array(
"name"	=> "蠍鞭",
"type"	=> "鞭",
"buy"	=> "50000",
"img"	=> "we_other007.png",
"atk"	=> array(50,0),
"handle"=> "16",
"P_SUMMON"	=> "25",
"need"	=> array("6040"=>"12","6181"=>"24","6000"=>"24",),
); break;
// 2210 鞭
		case "2210":
$item	= array(
"name"	=> "金屬鞭",
"type"	=> "鞭",
"buy"	=> "50000",
"img"	=> "we_other007.png",
"atk"	=> array(70,0),
"handle"=> "8",
"P_SUMMON"	=> "4",
"need"	=> array("6040"=>"4","6001"=>"24",),
); break;
		case "2211":
$item	= array(
"name"	=> "銀尾鞭",
"type"	=> "鞭",
"buy"	=> "70000",
"img"	=> "we_other007.png",
"atk"	=> array(100,0),
"handle"=> "10",
"P_SUMMON"	=> "8",
"need"	=> array("6040"=>"12","6002"=>"32",),
); break;
//------------------------------------- 3000 盾
		case "3000":
$item	= array(
"name"	=> "木盾",
"type"	=> "盾",
"buy"	=> "1000",
"img"	=> "shield_001m.png",
"def"	=> array(5,5,0,0),
"handle"=> "1",
"need"	=> array("6001"=>"1","6020"=>"4",),
); break;
		case "3001":
$item	= array(
"name"	=> "Baccrar",
"type"	=> "盾",
"buy"	=> "2000",
"img"	=> "shield_001m.png",
"def"	=> array(8,8,3,3),
"handle"=> "2",
"need"	=> array("6001"=>"4","6020"=>"2",),
); break;
		case "3002":
$item	= array(
"name"	=> "鐵盾",
"type"	=> "盾",
"buy"	=> "4000",
"img"	=> "shield_001m.png",
"def"	=> array(12,5,5,5),
"handle"=> "3",
"need"	=> array("6003"=>"6",),
); break;
		case "3003":
$item	= array(
"name"	=> "鳶盾",
"type"	=> "盾",
"buy"	=> "5000",
"img"	=> "shield_001m.png",
"def"	=> array(5,20,10,5),
"handle"=> "3",
"need"	=> array("6001"=>"2","6002"=>"6",),
); break;
		case "3004":
$item	= array(
"name"	=> "強力盾",
"type"	=> "盾",
"buy"	=> "8000",
"img"	=> "shield_001m.png",
"def"	=> array(0,0,20,15),
"handle"=> "4",
"need"	=> array("6002"=>"8","6021"=>"4",),
); break;
		case "3005":
$item	= array(
"name"	=> "重盾",
"type"	=> "盾",
"buy"	=> "8000",
"img"	=> "shield_001m.png",
"def"	=> array(15,10,8,8),
"handle"=> "4",
"need"	=> array("6002"=>"8","6003"=>"8"),
); break;
		case "3006":
$item	= array(
"name"	=> "圓盾",
"type"	=> "盾",
"buy"	=> "10000",
"img"	=> "shield_001m.png",
"def"	=> array(15,20,10,10),
"handle"=> "5",
"need"	=> array("6002"=>"4","6003"=>"16"),
); break;
		case "3007":
$item	= array(
"name"	=> "塔盾",
"type"	=> "盾",
"buy"	=> "15000",
"img"	=> "shield_001m.png",
"def"	=> array(18,15,15,10),
"handle"=> "6",
"need"	=> array("6002"=>"8","6003"=>"20"),
); break;
		case "3008":
$item	= array(
"name"	=> "精靈盾",
"type"	=> "盾",
"buy"	=> "18000",
"img"	=> "shield_001m.png",
"def"	=> array(0,0,30,20),
"handle"=> "6",
"need"	=> array("6002"=>"32",),
); break;
		case "3100":	//	3100-		書
$item	= array(
"name"	=> "課本",
"type"	=> "書",
"buy"	=> "200",
"img"	=> "book_002.png",
"atk"	=> array(0,2),
"def"	=> array(0,5,0,0),
"handle"=> "1",
); break;
		case "3101":
$item	= array(
"name"	=> "咒語字典",
"type"	=> "書",
"buy"	=> "5000",
"img"	=> "book_002.png",
"atk"	=> array(0,5),
"def"	=> array(2,2,2,2),
"handle"=> "2",
"need"	=> array("6182"=>"28",),
); break;
		case "3102":
$item	= array(
"name"	=> "咒語日記",
"type"	=> "書",
"buy"	=> "8000",
"img"	=> "book_002.png",
"atk"	=> array(0,7),
"def"	=> array(2,0,2,0),
"handle"=> "3",
"need"	=> array("6182"=>"28",),
); break;
		case "3103":
$item	= array(
"name"	=> "聖經",
"type"	=> "書",
"buy"	=> "10000",
"img"	=> "book_002.png",
"atk"	=> array(0,4),
"def"	=> array(0,0,8,3),
"handle"=> "3",
"need"	=> array("6182"=>"36",),
); break;
		case "3104":
$item	= array(
"name"	=> "召喚之書",
"type"	=> "書",
"buy"	=> "12000",
"img"	=> "book_002.png",
"atk"	=> array(0,3),
"def"	=> array(0,0,4,5),
"handle"=> "3",
"P_SUMMON"	=> "10",
"need"	=> array("6182"=>"36",),
); break;
		case "3105":
$item	= array(
"name"	=> "世界百科全書",
"type"	=> "書",
"buy"	=> "20000",
"img"	=> "book_002.png",
"atk"	=> array(5,0),
"def"	=> array(10,5,7,0),
"handle"=> "5",
"need"	=> array("6182"=>"58",),
); break;
		case "5000":	//	5000-5100	甲
$item	= array(
"name"	=> "皮甲",
"type"	=> "甲",
"buy"	=> "1000",
"img"	=> "armor_016b.png",
"def"	=> array(18,15,7,0),
"handle"=> "1",
"need"	=> array("6040"=>"8"),
); break;
		case "5001":
$item	= array(
"name"	=> "板甲",
"type"	=> "甲",
"buy"	=> "2000",
"img"	=> "armor_016b.png",
"def"	=> array(20,15,10,5),
"handle"=> "2",
"need"	=> array("6040"=>"10","6001"=>"2",),
); break;
		case "5002":
$item	= array(
"name"	=> "鏈甲",
"type"	=> "甲",
"buy"	=> "5000",
"img"	=> "armor_016b.png",
"def"	=> array(25,15,13,10),
"handle"=> "3",
"need"	=> array("6001"=>"14",),
); break;
		case "5003":
$item	= array(
"name"	=> "鎖子甲",
"type"	=> "甲",
"buy"	=> "6000",
"img"	=> "armor_016b.png",
"def"	=> array(30,20,15,5),
"handle"=> "4",
"need"	=> array("6001"=>"16","6002"=>"2",),
); break;
		case "5004":
$item	= array(
"name"	=> "銀甲",
"type"	=> "甲",
"buy"	=> "8000",
"img"	=> "armor_016b.png",
"def"	=> array(35,25,18,10),
"handle"=> "5",
"need"	=> array("6002"=>"18",),
); break;
		case "5005":
$item	= array(
"name"	=> "半鎧甲",
"type"	=> "甲",
"buy"	=> "10000",
"img"	=> "armor_016b.png",
"def"	=> array(15,70,24,15),
"handle"=> "5",
"need"	=> array("6002"=>"12","6003"=>"6",),
); break;
		case "5006":
$item	= array(
"name"	=> "龍甲",
"type"	=> "甲",
"buy"	=> "14000",
"img"	=> "armor_016b.png",
"def"	=> array(40,30,25,15),
"handle"=> "6",
); break;
		case "5007":
$item	= array(
"name"	=> "鍍鎧甲",
"type"	=> "甲",
"buy"	=> "10000",
"img"	=> "armor_016b.png",
"def"	=> array(20,100,25,20),
"handle"=> "6",
"need"	=> array("6002"=>"16","6003"=>"8",),
); break;
		case "5008":
$item	= array(
"name"	=> "SprintArmor",
"type"	=> "甲",
"buy"	=> "18000",
"img"	=> "armor_016b.png",
"def"	=> array(42,35,27,20),
"handle"=> "7",
"need"	=> array("6002"=>"24","6003"=>"10",),
); break;
		case "5009":
$item	= array(
"name"	=> "戰鬥軍甲",
"type"	=> "甲",
"buy"	=> "18000",
"img"	=> "armor_016b.png",
"def"	=> array(60,40,0,0),
"handle"=> "7",
"need"	=> array("6001"=>"12","6002"=>"12","6003"=>"12",),
); break;
		case "5010":
$item	= array(
"name"	=> "槽甲",
"type"	=> "甲",
"buy"	=> "25000",
"img"	=> "armor_016b.png",
"def"	=> array(45,35,28,20),
"handle"=> "8",
); break;
		case "5011":
$item	= array(
"name"	=> "惡魔甲",
"type"	=> "甲",
"buy"	=> "20000",
"img"	=> "armor_016b.png",
"def"	=> array(20,140,15,70),
"handle"=> "8",
); break;
		case "5012":
$item	= array(
"name"	=> "袍甲",
"type"	=> "甲",
"buy"	=> "25000",
"img"	=> "armor_016b.png",
"def"	=> array(47,35,28,30),
"handle"=> "9",
); break;
		case "5013":
$item	= array(
"name"	=> "金甲",
"type"	=> "甲",
"buy"	=> "40000",
"img"	=> "armor_016b.png",
"def"	=> array(50,35,30,35),
"handle"=> "10",
); break;
		case "5014":
$item	= array(
"name"	=> "白金甲",
"type"	=> "甲",
"buy"	=> "50000",
"img"	=> "armor_016b.png",
"def"	=> array(52,35,30,40),
"handle"=> "12",
); break;
		case "5015":
$item	= array(
"name"	=> "水晶甲",
"type"	=> "甲",
"buy"	=> "80000",
"img"	=> "armor_016b.png",
"def"	=> array(55,35,32,45),
"handle"=> "13",
); break;
		case "5016":
$item	= array(
"name"	=> "複合板甲",
"type"	=> "甲",
"buy"	=> "120000",
"img"	=> "armor_016b.png",
"def"	=> array(60,40,35,45),
"handle"=> "16",
); break;
		case "5100":	//	5100-5200	服
$item	= array(
"name"	=> "棉汗衫",
"type"	=> "衣服",
"buy"	=> "500",
"img"	=> "armor_014e.png",
"def"	=> array(5,5,5,5),
"handle"=> "1",
"need"	=> array("6180"=>"4",),
); break;
		case "5101":
$item	= array(
"name"	=> "皮夾克",
"type"	=> "衣服",
"buy"	=> "1000",
"img"	=> "armor_014e.png",
"def"	=> array(10,0,10,0),
"handle"=> "2",
"need"	=> array("6040"=>"4","6180"=>"4",),
); break;
		case "5102":
$item	= array(
"name"	=> "輕夾克",
"type"	=> "衣服",
"buy"	=> "2000",
"img"	=> "armor_014e.png",
"def"	=> array(15,5,15,5),
"handle"=> "3",
"need"	=> array("6040"=>"2","6180"=>"8",),
); break;
		case "5103":
$item	= array(
"name"	=> "長外套",
"type"	=> "衣服",
"buy"	=> "5000",
"img"	=> "armor_014e.png",
"def"	=> array(18,5,18,5),
"handle"=> "4",
"need"	=> array("6040"=>"6","6180"=>"10",),
); break;
		case "5104":
$item	= array(
"name"	=> "硬夾克",
"type"	=> "衣服",
"buy"	=> "9000",
"img"	=> "armor_014e.png",
"def"	=> array(23,7,23,7),
"handle"=> "5",
"need"	=> array("6040"=>"10","6180"=>"10",),
); break;
		case "5105":
$item	= array(
"name"	=> "褶外套",
"type"	=> "衣服",
"buy"	=> "14000",
"img"	=> "armor_014e.png",
"def"	=> array(25,10,25,10),
"handle"=> "6",
"need"	=> array("6040"=>"4","6183"=>"12",),
); break;
		case "5106":
$item	= array(
"name"	=> "貴族外套",
"type"	=> "衣服",
"buy"	=> "18000",
"img"	=> "armor_014e.png",
"def"	=> array(28,12,28,12),
"handle"=> "7",
"need"	=> array("6040"=>"6","6183"=>"20",),
); break;
		case "5107":
$item	= array(
"name"	=> "王者外套",
"type"	=> "衣服",
"buy"	=> "22000",
"img"	=> "armor_014e.png",
"def"	=> array(30,15,30,15),
"handle"=> "8",
"need"	=> array("6040"=>"4","6183"=>"15","6184"=>"15",),
); break;
		case "5200":	//	5200-5300	衣
$item	= array(
"name"	=> "棉長袍",
"type"	=> "長袍",
"buy"	=> "1000",
"img"	=> "armor_012.png",
"def"	=> array(0,5,30,10),
"handle"=> "1",
"need"	=> array("6180"=>"4",),
); break;
		case "5201":
$item	= array(
"name"	=> "銀長袍",
"type"	=> "長袍",
"buy"	=> "1500",
"img"	=> "armor_012.png",
"def"	=> array(2,5,35,15),
"handle"=> "2",
"need"	=> array("6002"=>"1","6180"=>"6",),
); break;
		case "5202":
$item	= array(
"name"	=> "小精靈長袍",
"type"	=> "長袍",
"buy"	=> "3000",
"img"	=> "armor_012.png",
"def"	=> array(3,10,40,20),
"handle"=> "3",
"need"	=> array("6180"=>"8","6184"=>"2",),
); break;
		case "5203":
$item	= array(
"name"	=> "仙女長袍",
"type"	=> "長袍",
"buy"	=> "5000",
"img"	=> "armor_012.png",
"def"	=> array(4,10,45,25),
"handle"=> "4",
"need"	=> array("6180"=>"12","6184"=>"4",),
); break;
		case "5204":
$item	= array(
"name"	=> "十字長袍",
"type"	=> "長袍",
"buy"	=> "8000",
"img"	=> "armor_012.png",
"def"	=> array(5,10,48,25),
"handle"=> "5",
"need"	=> array("6180"=>"14","6184"=>"4",),
); break;
		case "5205":
$item	= array(
"name"	=> "白色長袍",
"type"	=> "長袍",
"buy"	=> "10000",
"img"	=> "armor_012.png",
"def"	=> array(6,10,50,25),
"handle"=> "6",
"need"	=> array("6183"=>"8","6184"=>"8",),
); break;
		case "5206":
$item	= array(
"name"	=> "神聖長袍",
"type"	=> "長袍",
"buy"	=> "14000",
"img"	=> "armor_012.png",
"def"	=> array(7,10,52,30),
"handle"=> "7",
"need"	=> array("6183"=>"12","6184"=>"12",),
); break;
						// 5500 - 裝飾品
		case "5500":
$item	= array(
"name"	=> "生命指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_MAXHP"	=> "50",
"option"	=> "MAXHP+50, ",
); break;
		case "5501":
$item	= array(
"name"	=> "魔法指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_MAXSP"	=> "20",
"option"	=> "MAXSP+20, ",
); break;

		case "5510":
$item	= array(
"name"	=> "力量指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_STR"	=> "30",
"option"	=> "STR+30, ",
); break;
		case "5515":
$item	= array(
"name"	=> "智慧指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_INT"	=> "30",
"option"	=> "INT+30, ",
); break;
		case "5520":
$item	= array(
"name"	=> "靈巧指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_DEX"	=> "30",
"option"	=> "DEX+30, ",
); break;
		case "5525":
$item	= array(
"name"	=> "速度指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_SPD"	=> "10",
"option"	=> "SPD+10, ",
); break;
		case "5530":
$item	= array(
"name"	=> "幸運指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_LUK"	=> "30",
"option"	=> "LUK+30, ",
); break;
		case "5600":
$item	= array(
"name"	=> "狂暴指環",
"type"	=> "道具",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_STR"	=> "100",
"M_MAXHP"	=> "-50",
"option"	=> "STR+100, HP-50% ,",
); break;
						// 6000	-	素材
		case "6000"://石頭
$item	= array(
"name"	=> "石頭",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "5",
"img"	=> "item_009z.png",
); break;
		case "6001":
$item	= array(
"name"	=> "鋼",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_001.png",
); break;
		case "6002":
$item	= array(
"name"	=> "銀",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_001.png",
); break;
		case "6003":
$item	= array(
"name"	=> "鐵",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "30",
"img"	=> "mat_001.png",
); break;
						// 6020-木頭
		case "6020":
$item	= array(
"name"	=> "木料",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_025.png",
); break;
		case "6021":
$item	= array(
"name"	=> "橡樹",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "30",
"img"	=> "mat_025.png",
); break;
		case "6022":
$item	= array(
"name"	=> "柏樹",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "40",
"img"	=> "mat_025.png",
); break;
		case "6040"://6040-皮
$item	= array(
"name"	=> "皮革",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_024.png",
); break;
		case "6060"://6060-骨
$item	= array(
"name"	=> "骨頭",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_016.png",
); break;
		case "6080"://6080-牙
$item	= array(
"name"	=> "獸牙",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_013.png",
); break;
		case "6100"://6100-毛
$item	= array(
"name"	=> "羽毛",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_008.png",
); break;
		case "6120"://6120-寶石
$item	= array(
"name"	=> "鑽石",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "100",
"img"	=> "gem_02.png",
); break;
		case "6140"://6140-音
$item	= array(
"name"	=> "噪聲",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_007.png",
); break;
		case "6160"://6160-錢幣
$item	= array(
"name"	=> "金幣",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "500",
"img"	=> "acce_005.png",
); break;
		case "6161":
$item	= array(
"name"	=> "銀幣",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "250",
"img"	=> "acce_005b.png",
); break;
		case "6162":
$item	= array(
"name"	=> "銅幣",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "100",
"img"	=> "acce_005c.png",
); break;
						//6180 - 絲，纖維
		case "6180":
$item	= array(
"name"	=> "棉花",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6181":
$item	= array(
"name"	=> "籐",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6182":
$item	= array(
"name"	=> "大麻",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6183":
$item	= array(
"name"	=> "羊毛",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6184":
$item	= array(
"name"	=> "絲",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6200"://6200 - 音
$item	= array(
"name"	=> "噪音",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_007.png",
); break;
		case "6600"://6600 - 垃圾
$item	= array(
"name"	=> "香蕉",
"type"	=> "材料",
"buy"	=> "100",
"sell"	=> "50",
"img"	=> "banana.png",
); break;
		case "6601":
$item	= array(
"name"	=> "黃金香蕉",
"type"	=> "材料",
"buy"	=> "100",
"sell"	=> "5000",
"img"	=> "banana.png",
); break;
		case "6602":
$item	= array(
"name"	=> "香蕉金屬",
"type"	=> "材料",
"buy"	=> "100",
"sell"	=> "50",
"img"	=> "banana.png",
); break;
		case "6800"://6800 - 稀有
$item	= array(
"name"	=> "龍牙",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_013.png",
); break;
		case "6801":
$item	= array(
"name"	=> "龍翼",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_011.png",
); break;
		case "6802":
$item	= array(
"name"	=> "斷劍",
"type"	=> "材料",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "we_sword026.png",
); break;
						// 製作強化系
		case "7000":
$item	= array(
"name"	=> "力量球",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_019.png",
"Add"	=> "X00",
); break;
		case "7001":
$item	= array(
"name"	=> "魔法球",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_019.png",
"Add"	=> "X01",
); break;
						// 製作強化系(怪物掉落稀有)
		case "7100":
$item	= array(
"name"	=> "哥布林之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "M01",
); break;
		case "7101":
$item	= array(
"name"	=> "蝙蝠之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7102":
$item	= array(
"name"	=> "骷髏勇士之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7103":
$item	= array(
"name"	=> "骷髏戰士之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7104":
$item	= array(
"name"	=> "骷髏射手之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7105":
$item	= array(
"name"	=> "骨頭薩滿之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7106":
$item	= array(
"name"	=> "獨眼巨人之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7107":
$item	= array(
"name"	=> "哥布林鐵匠之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7108":
$item	= array(
"name"	=> "模仿獸之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7109":
$item	= array(
"name"	=> "骷髏隊長之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7110":
$item	= array(
"name"	=> "邪惡巫師之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7111":
$item	= array(
"name"	=> "眼球怪之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7112":
$item	= array(
"name"	=> "邪惡傭人之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7113":
$item	= array(
"name"	=> "人馬獵手之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7114":
$item	= array(
"name"	=> "人馬騎士之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7115":
$item	= array(
"name"	=> "巴風特之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7116":
$item	= array(
"name"	=> "巴風特王之淚",
"type"	=> "材料",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
//------------------------------ 7500 其他消耗品
		case "7500":
$item	= array(
"name"	=> "重命名卡",
"type"	=> "其他",
"buy"	=> "0",
"sell"	=> "0",
"img"	=> "item_035z.png",
); break;
		case "7510":
$item	= array(
"name"	=> "重置水晶(狀態1)",
"type"	=> "其他",
"buy"	=> "100000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7511":
$item	= array(
"name"	=> "重置水晶(狀態30)",
"type"	=> "其他",
"buy"	=> "300000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7512":
$item	= array(
"name"	=> "重置水晶(狀態50)",
"type"	=> "其他",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7513":
$item	= array(
"name"	=> "重置水晶(狀態100)",
"type"	=> "其他",
"buy"	=> "1000000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7520":
$item	= array(
"name"	=> "重置水晶(技能)",
"type"	=> "其他",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
//------------------------------ 8000 地圖，鑰匙
		case "8000":
$item	= array(
"name"	=> "古代洞穴",
"type"	=> "地圖",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8001":
$item	= array(
"name"	=> "古代洞穴 B2",
"type"	=> "鑰匙",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8002":
$item	= array(
"name"	=> "古代洞穴 B3",
"type"	=> "鑰匙",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8003":
$item	= array(
"name"	=> "古代洞穴 B4",
"type"	=> "鑰匙",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8004":
$item	= array(
"name"	=> "古代洞穴 B5",
"type"	=> "鑰匙",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8009":
$item	= array(
"name"	=> "滴凍山入口",
"type"	=> "地圖",
"buy"	=> "500",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8010":
$item	= array(
"name"	=> "滴凍山中腹",
"type"	=> "地圖",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8011":
$item	= array(
"name"	=> "滴凍山頂上",
"type"	=> "地圖",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
			// 9000 - 其他
		case "9000":
$item	= array(
"name"	=> "拍賣會員卡",
"type"	=> "特殊",
"buy"	=> "9999",
"sell"	=> "100",
"img"	=> "item_035.png",
); break;
		default:
			return false;
	}

	// 追加變數
	$item["no"]	= $no;
	$item["base_name"]	= $item["name"];
	switch($item["type"]) {
		case "劍":
		case "雙手劍":
		case "匕首":
		case "魔杖":
		case "杖":
		case "弓":
		case "鞭":
			$item["type2"]	= "WEAPON";
			break;
		case "盾":
		case "書":
		case "甲":
		case "衣服":
		case "長袍":
			$item["type2"]	= "GUARD";
			break;
		default:
			$item["type2"]	= "其他";
			break;
	}
	// 精煉值
	if($refine) {
		$item["refine"]	= $refine;
		$item["name"]	= "+".$refine." ".$item["name"];
		//$item["name"]	.= "+".$refine;
		//$RefineRate	= 1 + 0.5 * ($refine/10);
		if(isset($item["atk"]["0"])) {
			//$item["atk"]["0"]	= ceil($item["atk"]["0"] * $RefineRate);// 單純式
			// 1.05*1.05*1.05....
			/*
			for($i=0; $i<$refine; $i++) {
				$item["atk"]["0"]	*= 1.05;
			}
			*/
			$item["atk"]["0"]	*= ( 1 + ($refine*$refine)/100 );
			$item["atk"]["0"]	= ceil($item["atk"]["0"]);
		}
		if(isset($item["atk"]["1"])) {
			//$item["atk"]["1"]	= ceil($item["atk"]["1"] * $RefineRate);
			/*
			for($i=0; $i<$refine; $i++) {
				$item["atk"]["1"]	*= 1.05;
			}
			*/
			$item["atk"]["1"]	*= ( 1 + ($refine*$refine)/100 );
			$item["atk"]["1"]	= ceil($item["atk"]["1"]);
		}
		// 防具值強化
		$RefineRate	= 1 + 0.3 * ($refine/10);
		if(isset($item["def"]["0"]))
			$item["def"]["0"]	= ceil($item["def"]["0"] * $RefineRate);
		if(isset($item["def"]["1"]))
			$item["def"]["1"]	= ceil($item["def"]["1"] * $RefineRate);
		if(isset($item["def"]["2"]))
			$item["def"]["2"]	= ceil($item["def"]["2"] * $RefineRate);
		if(isset($item["def"]["3"]))
			$item["def"]["3"]	= ceil($item["def"]["3"] * $RefineRate);
			
	}
	// 附加能力
	if($option0)
		AddEnchantData($item,$option0);
	if($option1)
		AddEnchantData($item,$option1);
	if($option2)
		AddEnchantData($item,$option2);
	return $item;
}
?>