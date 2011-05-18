<?
if(file_exists(DATA_ENCHANT))
	include(DATA_ENCHANT);

function LoadItemData($no) {
	$base	= substr($no,0,4);//アイテムの種類
	$refine	= (int)substr($no,4,2);//精錬値
	// 付加能力
	$option0	= substr($no,6,3);
	$option1	= substr($no,9,3);
	$option2	= substr($no,12,3);

/*
 * 設定項目
 * ---------------------------------------------
 * "name"=>"名称",
 * "type"=>"種類",
 * "buy"=>"買値",
 * "img"=>"画像",
 * "atk"=>array(物理攻撃,魔法攻撃),
 * "def"=>array(物理割?,物理減,魔法割?,魔法減),
 * "dh"=> true,//両手武器か否か( "D"ouble"H"and )
 * "handle"=>"数値",
 * "need" => array("素材番号"=>数, ...),// 製作に必要なアイテム
 * ---------------------------------------------
 * type
 * "Sword"	片手剣
 * "TwoHandSword"	両手剣
 * "Dagger"	短剣
 * "Spear"	両手槍
 * "Pike"	片手槍
 * "Axe"	両手斧
 * "Hatchet"片手斧
 * "Wand"	片手杖
 * "Staff"	両手杖
 * "Mace"	鈍器(片手)
 * "Bow"	弓
 * "CrossBow"	石弓
 * 
 * "Shield"	盾
 * "MainGauche"	防御用短剣
 * "Book"	本
 * 
 * "Armor"	鎧
 * "Cloth"	服
 * "Robe"	衣
 * 
 * "?"
 *--------------------------------------------
	追加オプション
	P_MAXHP
	M_MAXHP
	P_MAXSP
	M_MAXSP
	P_STR
	P_INT
	P_DEX
	P_SPD
	P_LUK
	P_SUMMON = 召還力強化
	P_PIERCE = array(物理,魔法),
 *--------------------------------------------
 */
	switch($base) {
		case "1000":	//	1000-1100	片手剣
$item	= array(
"name"	=> "ShortSword",
"type"	=> "Sword",
"buy"	=> "500",
"img"	=> "we_sword026.png",
"atk"	=> array(10,0),
"handle"=> "1",
"need"	=> array("6001"=>"4",),
); break;
		case "1001":
$item	= array(
"name"	=> "Falshion",
"type"	=> "Sword",
"buy"	=> "1000",
"img"	=> "we_sword026.png",
"atk"	=> array(15,0),
"handle"=> "2",
"need"	=> array("6001"=>"6","6002"=>"2",),
); break;
		case "1002":
$item	= array(
"name"	=> "GreatSword",
"type"	=> "Sword",
"buy"	=> "3000",
"img"	=> "we_sword026.png",
"atk"	=> array(20,0),
"handle"=> "2",
"need"	=> array("6001"=>"4","6002"=>"4",),
); break;
		case "1003":
$item	= array(
"name"	=> "Rapier",
"type"	=> "Sword",
"buy"	=> "5000",
"img"	=> "we_sword026.png",
"atk"	=> array(25,0),
"handle"=> "3",
"need"	=> array("6001"=>"2","6002"=>"8",),
); break;
		case "1004":
$item	= array(
"name"	=> "Cutlass",
"type"	=> "Sword",
"buy"	=> "8000",
"img"	=> "we_sword026.png",
"atk"	=> array(30,0),
"handle"=> "4",
"need"	=> array("6002"=>"8","6003"=>"2",),
); break;
		case "1005":
$item	= array(
"name"	=> "LongSword",
"type"	=> "Sword",
"buy"	=> "14000",
"img"	=> "we_sword026.png",
"atk"	=> array(40,0),
"handle"=> "5",
"need"	=> array("6003"=>"12",),
); break;
		case "1006":
$item	= array(
"name"	=> "BroadSword",
"type"	=> "Sword",
"buy"	=> "20000",
"img"	=> "we_sword026.png",
"atk"	=> array(50,0),
"handle"=> "6",
"need"	=> array("6002"=>"4","6003"=>"16",),
); break;
		case "1007":
$item	= array(
"name"	=> "Shotel",
"type"	=> "Sword",
"buy"	=> "35000",
"img"	=> "we_sword026.png",
"atk"	=> array(60,0),
"handle"=> "7",
"need"	=> array("6003"=>"24",),
); break;
		case "1008":
$item	= array(
"name"	=> "Flamberge",
"type"	=> "Sword",
"buy"	=> "60000",
"img"	=> "we_sword026.png",
"atk"	=> array(80,0),
"handle"=> "10",
"need"	=> array("6003"=>"32",),
); break;
		case "1020":
$item	= array(
"name"	=> "DragonBuster",
"type"	=> "Sword",
"buy"	=> "70000",
"img"	=> "we_sword026.png",
"atk"	=> array(20,0),
"handle"=> "8",
"P_PIERCE"=> array(30,0),
"need"	=> array("6002"=>"15","6800"=>"1",),
"option"	=> "物理防御無視+30 ,",
); break;
		case "1021":
$item	= array(
"name"	=> "RightBringer",
"type"	=> "Sword",
"buy"	=> "100000",
"img"	=> "we_sword026.png",
"atk"	=> array(3,0),
"handle"=> "1",
); break;
		case "1022":
$item	= array(
"name"	=> "LightBringer",
"type"	=> "Sword",
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
"name"	=> "BananaSword",
"type"	=> "Sword",
"buy"	=> "1000",
"img"	=> "banana.png",
"atk"	=> array(3,0),
"P_SPD"	=> 1,
"handle"=> "0",
"option"	=> "SPD+1 ,",
"need"	=> array("6600"=>"3","6602"=>"1",),
); break;

		case "1100":	//	1100-1200	両手剣
$item	= array(
"name"	=> "Slayer",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_sword006.png",
"atk"	=> array(30,0),
"handle"=> "2",
"need"	=> array("6001"=>"8",),
); break;
		case "1101":
$item	= array(
"name"	=> "Claymore",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "5000",
"img"	=> "we_sword006.png",
"atk"	=> array(45,0),
"handle"=> "3",
"need"	=> array("6001"=>"6","6002"=>"4",),
); break;
		case "1102":
$item	= array(
"name"	=> "Zweihander",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "16000",
"img"	=> "we_sword006.png",
"atk"	=> array(65,0),
"handle"=> "5",
"need"	=> array("6001"=>"6","6002"=>"8",),
); break;
		case "1103":
$item	= array(
"name"	=> "BastardSword",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_sword006.png",
"atk"	=> array(80,0),
"handle"=> "6",
"need"	=> array("6001"=>"2","6002"=>"6","6003"=>"8",),
); break;
		case "1104":
$item	= array(
"name"	=> "Behead",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "70000",
"img"	=> "we_sword006.png",
"atk"	=> array(100,0),
"handle"=> "8",
"need"	=> array("6002"=>"10","6003"=>"20",),
); break;
		case "1120":
$item	= array(
"name"	=> "DragonSlayer",
"type"	=> "TwoHandSword",
"dh"	=> true,
"buy"	=> "80000",
"img"	=> "we_sword006.png",
"atk"	=> array(10,0),
"handle"=> "10",
"need"	=> array(),
"P_PIERCE"=> array(50,0),
"need"	=> array("6002"=>"5","6003"=>"10","6800"=>"1",),
"option"	=> "物理防御無視+50 ,",
); break;
		case "1200":	// 1200-1300	短剣
$item	= array(
"name"	=> "Stiletto",
"type"	=> "Dagger",
"buy"	=> "1000",
"img"	=> "we_sword010.png",
"atk"	=> array(7,0),
"handle"=> "1",
); break;
		case "1201":
$item	= array(
"name"	=> "Kukuri",
"type"	=> "Dagger",
"buy"	=> "10000",
"img"	=> "we_sword010.png",
"atk"	=> array(21,0),
"handle"=> "3",
"need"	=> array("6001"=>12,"6020"=>4),
); break;
		case "1202":
$item	= array(
"name"	=> "Spata",
"type"	=> "Dagger",
"buy"	=> "20000",
"img"	=> "we_sword010.png",
"atk"	=> array(28,0),
"handle"=> "4",
"need"	=> array("6001"=>16,"6020"=>4),
); break;
		case "1203":
$item	= array(
"name"	=> "Gladius",
"type"	=> "Dagger",
"buy"	=> "40000",
"img"	=> "we_sword010.png",
"atk"	=> array(34,0),
"handle"=> "5",
"need"	=> array("6002"=>12,"6020"=>4),
); break;
		case "1204":
$item	= array(
"name"	=> "AssassinDagger",
"type"	=> "Dagger",
"buy"	=> "50000",
"img"	=> "we_sword010.png",
"atk"	=> array(40,0),
"handle"=> "6",
"need"	=> array("6003"=>10,"6020"=>4),
); break;
		case "1205":
$item	= array(
"name"	=> "MailBreaker",
"type"	=> "Dagger",
"buy"	=> "50000",
"img"	=> "we_sword010.png",
"atk"	=> array(20,0),
"handle"=> "6",
"P_PIERCE"	=>array(20,0),
"option"	=> "物理防御無視+20 ,",
"need"	=> array("6003"=>20,"6022"=>4),
); break;

		case "1220":
$item	= array(
"name"	=> "BananaDagger",
"type"	=> "Dagger",
"buy"	=> "1000",
"img"	=> "banana.png",
"atk"	=> array(1,0),
"P_SPD"	=> 1,
"handle"=> "0",
"option"	=> "SPD+1 ,",
"need"	=> array("6600"=>"3","6602"=>"1",),
); break;

		case "1300":	//	1300-1400	両手槍
$item	= array(
"name"	=> "Partizan",
"type"	=> "Spear",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_spear016.png",
"atk"	=> array(28,0),
"handle"=> "2",
); break;
		case "1400":	//	1400-1500	片手槍
$item	= array(
"name"	=> "Javelin",
"type"	=> "Pike",
"buy"	=> "1000",
"img"	=> "we_spear012.png",
"atk"	=> array(14,0),
"handle"=> "2",
); break;
		case "1500":	//	1500-1600	両手斧
$item	= array(
"name"	=> "GreatAxe",
"type"	=> "Axe",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_axe013b.png",
"atk"	=> array(35,0),
"handle"=> "2",
); break;
		case "1600":	//	1600-1700	片手斧
$item	= array(
"name"	=> "TomaHawk",
"type"	=> "Hatchet",
"buy"	=> "1000",
"img"	=> "we_axe003.png",
"atk"	=> array(17,0),
"handle"=> "2",
); break;
		case "1700":	//	1700-1800	片手杖
$item	= array(
"name"	=> "Rod",
"type"	=> "Wand",
"buy"	=> "1000",
"img"	=> "we_staff002.png",
"atk"	=> array(1,5),
"handle"=> "1",
"need"	=> array("6020"=>"2","6001"=>"1",),
); break;
		case "1701":
$item	= array(
"name"	=> "ShortWand",
"type"	=> "Wand",
"buy"	=> "2000",
"img"	=> "we_staff002.png",
"atk"	=> array(5,10),
"handle"=> "2",
"need"	=> array("6020"=>"4","6001"=>"1",),
); break;
		case "1702":
$item	= array(
"name"	=> "WoodenWand",
"type"	=> "Wand",
"buy"	=> "4000",
"img"	=> "we_staff002.png",
"atk"	=> array(8,15),
"handle"=> "3",
"need"	=> array("6020"=>"8","6002"=>"1",),
); break;
		case "1703":
$item	= array(
"name"	=> "SilverWand",
"type"	=> "Wand",
"buy"	=> "6000",
"img"	=> "we_staff002.png",
"atk"	=> array(6,20),
"handle"=> "4",
"need"	=> array("6002"=>"8","6020"=>"2"),
); break;
		case "1704":
$item	= array(
"name"	=> "ForceWand",
"type"	=> "Wand",
"buy"	=> "10000",
"img"	=> "we_staff002.png",
"atk"	=> array(10,26),
"handle"=> "5",
"need"	=> array("6020"=>"10","6002"=>"4",),
); break;
		case "1705":
$item	= array(
"name"	=> "FairlyWand",
"type"	=> "Wand",
"buy"	=> "18000",
"img"	=> "we_staff002.png",
"atk"	=> array(5,32),
"handle"=> "6",
"need"	=> array("6021"=>"6","6002"=>"4",),
); break;
		case "1706":
$item	= array(
"name"	=> "SorecererWand",
"type"	=> "Wand",
"buy"	=> "25000",
"img"	=> "we_staff002.png",
"atk"	=> array(2,40),
"handle"=> "7",
"need"	=> array("6021"=>"10","6002"=>"4",),
); break;
		case "1800":	//	1800-1900	両手杖
$item	= array(
"name"	=> "Staff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "2000",
"img"	=> "we_staff008b.png",
"atk"	=> array(8,25),
"handle"=> "2",
"need"	=> array("6002"=>"2","6020"=>"4"),
); break;
		case "1801":
$item	= array(
"name"	=> "LongStaff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "5000",
"img"	=> "we_staff008b.png",
"atk"	=> array(4,37),
"handle"=> "3",
"need"	=> array("6021"=>"8"),
); break;
		case "1802":
$item	= array(
"name"	=> "MageStaff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "14000",
"img"	=> "we_staff008b.png",
"atk"	=> array(15,49),
"handle"=> "5",
"need"	=> array("6002"=>"2","6021"=>"8",),
); break;
		case "1803":
$item	= array(
"name"	=> "SilverStaff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "20000",
"img"	=> "we_staff008b.png",
"atk"	=> array(10,60),
"handle"=> "6",
"need"	=> array("6002"=>"12","6022"=>"1",),
); break;
		case "1804":
$item	= array(
"name"	=> "GoldStaff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_staff008b.png",
"atk"	=> array(10,72),
"handle"=> "7",
); break;
		case "1805":
$item	= array(
"name"	=> "CrystalStaff",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "35000",
"img"	=> "we_staff008b.png",
"atk"	=> array(12,84),
"handle"=> "8",
); break;
//--------------
		case "1810":
$item	= array(
"name"	=> "ManaStaff",
"type"	=> "Staff",
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
"name"	=> "HeavenStaff",
"type"	=> "Staff",
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
"name"	=> "FourSeason",
"type"	=> "Staff",
"dh"	=> true,
"buy"	=> "60000",
"img"	=> "we_staff008b.png",
"atk"	=> array(12,40),
"handle"=> "8",
"P_MAXSP"	=> "130",
"option"	=> "SP+130 ,",
"need"	=> array("6020"=> 16,"6021"=> 16,),
); break;
		case "1900":	//	1900-2000	鈍器(片手)
$item	= array(
"name"	=> "BronzeMace",
"type"	=> "Mace",
"buy"	=> "1000",
"img"	=> "we_axe015b.png",
"atk"	=> array(5,5),
"handle"=> "2",
); break;
		case "2000":	//	2000-2100	弓
$item	= array(
"name"	=> "ShortBow",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_bow001.png",
"atk"	=> array(20,0),
"handle"=> "2",
"need"	=> array("6020"=>"6","6181"=>"1",),
); break;
		case "2001":
$item	= array(
"name"	=> "CompositeBow",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "4000",
"img"	=> "we_bow001.png",
"atk"	=> array(30,0),
"handle"=> "6",
"need"	=> array("6020"=>"9","6181"=>"2",),
); break;
		case "2002":
$item	= array(
"name"	=> "GreatBow",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "8000",
"img"	=> "we_bow001.png",
"atk"	=> array(40,0),
"handle"=> "12",
"need"	=> array("6021"=>"6","6181"=>"2",),
); break;
		case "2003":
$item	= array(
"name"	=> "HunterBow",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "14000",
"img"	=> "we_bow001.png",
"atk"	=> array(50,0),
"handle"=> "16",
"need"	=> array("6020"=>"4","6021"=>"4","6181"=>"4",),
); break;
		case "2004":
$item	= array(
"name"	=> "SilverBow",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "20000",
"img"	=> "we_bow001.png",
"atk"	=> array(60,0),
"handle"=> "20",
"need"	=> array("6002"=>"4","6021"=>"6","6182"=>"2",),
); break;
		case "2005":
$item	= array(
"name"	=> "SharpShooter",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "30000",
"img"	=> "we_bow001.png",
"atk"	=> array(70,0),
"handle"=> "24",
"need"	=> array("6021"=>"12","6182"=>"4",),
); break;
		case "2006":
$item	= array(
"name"	=> "RobinHood",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "45000",
"img"	=> "we_bow001.png",
"atk"	=> array(80,0),
"handle"=> "28",
"need"	=> array("6021"=>"4","6022"=>"14","6182"=>"6",),
); break;
		case "2007":
$item	= array(
"name"	=> "Varistor",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "60000",
"img"	=> "we_bow001.png",
"atk"	=> array(90,0),
"handle"=> "30",
); break;
		case "2008":
$item	= array(
"name"	=> "Altemis",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "100000",
"img"	=> "we_bow001.png",
"atk"	=> array(140,0),
"handle"=> "40",
); break;
		case "2020":
$item	= array(
"name"	=> "DragonWing",
"type"	=> "Bow",
"dh"	=> true,
"buy"	=> "120000",
"img"	=> "we_bow001.png",
"atk"	=> array(40,0),
"handle"=> "30",
"P_PIERCE"=> array(40,0),
"need"	=> array("6022"=>"10","6182"=>"5","6801"=>"1",),
"option"	=> "物理防御無視+40 ,",
); break;

						//	2100-2199	石弓
		case "2100":
$item	= array(
"name"	=> "Gastraphetes",
"type"	=> "CrossBow",
"dh"	=> true,
"buy"	=> "1000",
"img"	=> "we_bow013.png",
"atk"	=> array(25,0),
"handle"=> "2",
); break;
						//	2200-2299	鞭
		case "2200":
$item	= array(
"name"	=> "TamerWhip",
"type"	=> "Whip",
"buy"	=> "1000",
"img"	=> "we_other007.png",
"atk"	=> array(20,0),
"handle"=> "4",
"P_SUMMON"	=> "10",
"need"	=> array("6181"=>"8",),
); break;
		case "2201":
$item	= array(
"name"	=> "LongWhip",
"type"	=> "Whip",
"buy"	=> "20000",
"img"	=> "we_other007.png",
"atk"	=> array(30,0),
"handle"=> "8",
"P_SUMMON"	=> "15",
"need"	=> array("6040"=>"4","6181"=>"12",),
); break;
		case "2202":
$item	= array(
"name"	=> "Anaconda",
"type"	=> "Whip",
"buy"	=> "30000",
"img"	=> "we_other007.png",
"atk"	=> array(40,0),
"handle"=> "12",
"P_SUMMON"	=> "20",
"need"	=> array("6040"=>"6","6181"=>"16",),
); break;
		case "2203":
$item	= array(
"name"	=> "Scorpio",
"type"	=> "Whip",
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
"name"	=> "Wire",
"type"	=> "Whip",
"buy"	=> "50000",
"img"	=> "we_other007.png",
"atk"	=> array(70,0),
"handle"=> "8",
"P_SUMMON"	=> "4",
"need"	=> array("6040"=>"4","6001"=>"24",),
); break;
		case "2211":
$item	= array(
"name"	=> "SilverTail",
"type"	=> "Whip",
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
"name"	=> "WoodShield",
"type"	=> "Shield",
"buy"	=> "1000",
"img"	=> "shield_001m.png",
"def"	=> array(5,5,0,0),
"handle"=> "1",
"need"	=> array("6001"=>"1","6020"=>"4",),
); break;
		case "3001":
$item	= array(
"name"	=> "Baccrar",
"type"	=> "Shield",
"buy"	=> "2000",
"img"	=> "shield_001m.png",
"def"	=> array(8,8,3,3),
"handle"=> "2",
"need"	=> array("6001"=>"4","6020"=>"2",),
); break;
		case "3002":
$item	= array(
"name"	=> "IronShield",
"type"	=> "Shield",
"buy"	=> "4000",
"img"	=> "shield_001m.png",
"def"	=> array(12,5,5,5),
"handle"=> "3",
"need"	=> array("6003"=>"6",),
); break;
		case "3003":
$item	= array(
"name"	=> "KiteShield",
"type"	=> "Shield",
"buy"	=> "5000",
"img"	=> "shield_001m.png",
"def"	=> array(5,20,10,5),
"handle"=> "3",
"need"	=> array("6001"=>"2","6002"=>"6",),
); break;
		case "3004":
$item	= array(
"name"	=> "ForceShield",
"type"	=> "Shield",
"buy"	=> "8000",
"img"	=> "shield_001m.png",
"def"	=> array(0,0,20,15),
"handle"=> "4",
"need"	=> array("6002"=>"8","6021"=>"4",),
); break;
		case "3005":
$item	= array(
"name"	=> "HeavyShield",
"type"	=> "Shield",
"buy"	=> "8000",
"img"	=> "shield_001m.png",
"def"	=> array(15,10,8,8),
"handle"=> "4",
"need"	=> array("6002"=>"8","6003"=>"8"),
); break;
		case "3006":
$item	= array(
"name"	=> "RoundShield",
"type"	=> "Shield",
"buy"	=> "10000",
"img"	=> "shield_001m.png",
"def"	=> array(15,20,10,10),
"handle"=> "5",
"need"	=> array("6002"=>"4","6003"=>"16"),
); break;
		case "3007":
$item	= array(
"name"	=> "TowerShield",
"type"	=> "Shield",
"buy"	=> "15000",
"img"	=> "shield_001m.png",
"def"	=> array(18,15,15,10),
"handle"=> "6",
"need"	=> array("6002"=>"8","6003"=>"20"),
); break;
		case "3008":
$item	= array(
"name"	=> "FairyShield",
"type"	=> "Shield",
"buy"	=> "18000",
"img"	=> "shield_001m.png",
"def"	=> array(0,0,30,20),
"handle"=> "6",
"need"	=> array("6002"=>"32",),
); break;
		case "3100":	//	3100-		本
$item	= array(
"name"	=> "TextBook",
"type"	=> "Book",
"buy"	=> "200",
"img"	=> "book_002.png",
"atk"	=> array(0,2),
"def"	=> array(0,5,0,0),
"handle"=> "1",
); break;
		case "3101":
$item	= array(
"name"	=> "SpellDictionary",
"type"	=> "Book",
"buy"	=> "5000",
"img"	=> "book_002.png",
"atk"	=> array(0,5),
"def"	=> array(2,2,2,2),
"handle"=> "2",
"need"	=> array("6182"=>"28",),
); break;
		case "3102":
$item	= array(
"name"	=> "SpellNote",
"type"	=> "Book",
"buy"	=> "8000",
"img"	=> "book_002.png",
"atk"	=> array(0,7),
"def"	=> array(2,0,2,0),
"handle"=> "3",
"need"	=> array("6182"=>"28",),
); break;
		case "3103":
$item	= array(
"name"	=> "HolyBible",
"type"	=> "Book",
"buy"	=> "10000",
"img"	=> "book_002.png",
"atk"	=> array(0,4),
"def"	=> array(0,0,8,3),
"handle"=> "3",
"need"	=> array("6182"=>"36",),
); break;
		case "3104":
$item	= array(
"name"	=> "Summoner&#39;sBible",
"type"	=> "Book",
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
"name"	=> "WorldEncyclopedia",
"type"	=> "Book",
"buy"	=> "20000",
"img"	=> "book_002.png",
"atk"	=> array(5,0),
"def"	=> array(10,5,7,0),
"handle"=> "5",
"need"	=> array("6182"=>"58",),
); break;
		case "5000":	//	5000-5100	鎧
$item	= array(
"name"	=> "LeatherArmor ",
"type"	=> "Armor",
"buy"	=> "1000",
"img"	=> "armor_016b.png",
"def"	=> array(18,15,7,0),
"handle"=> "1",
"need"	=> array("6040"=>"8"),
); break;
		case "5001":
$item	= array(
"name"	=> "ScaleArmor",
"type"	=> "Armor",
"buy"	=> "2000",
"img"	=> "armor_016b.png",
"def"	=> array(20,15,10,5),
"handle"=> "2",
"need"	=> array("6040"=>"10","6001"=>"2",),
); break;
		case "5002":
$item	= array(
"name"	=> "RingMail",
"type"	=> "Armor",
"buy"	=> "5000",
"img"	=> "armor_016b.png",
"def"	=> array(25,15,13,10),
"handle"=> "3",
"need"	=> array("6001"=>"14",),
); break;
		case "5003":
$item	= array(
"name"	=> "ChainMail",
"type"	=> "Armor",
"buy"	=> "6000",
"img"	=> "armor_016b.png",
"def"	=> array(30,20,15,5),
"handle"=> "4",
"need"	=> array("6001"=>"16","6002"=>"2",),
); break;
		case "5004":
$item	= array(
"name"	=> "SilverArmor",
"type"	=> "Armor",
"buy"	=> "8000",
"img"	=> "armor_016b.png",
"def"	=> array(35,25,18,10),
"handle"=> "5",
"need"	=> array("6002"=>"18",),
); break;
		case "5005":
$item	= array(
"name"	=> "HalfPlate",
"type"	=> "Armor",
"buy"	=> "10000",
"img"	=> "armor_016b.png",
"def"	=> array(15,70,24,15),
"handle"=> "5",
"need"	=> array("6002"=>"12","6003"=>"6",),
); break;
		case "5006":
$item	= array(
"name"	=> "DragonArmor",
"type"	=> "Armor",
"buy"	=> "14000",
"img"	=> "armor_016b.png",
"def"	=> array(40,30,25,15),
"handle"=> "6",
); break;
		case "5007":
$item	= array(
"name"	=> "PlateMail",
"type"	=> "Armor",
"buy"	=> "10000",
"img"	=> "armor_016b.png",
"def"	=> array(20,100,25,20),
"handle"=> "6",
"need"	=> array("6002"=>"16","6003"=>"8",),
); break;
		case "5008":
$item	= array(
"name"	=> "SprintArmor",
"type"	=> "Armor",
"buy"	=> "18000",
"img"	=> "armor_016b.png",
"def"	=> array(42,35,27,20),
"handle"=> "7",
"need"	=> array("6002"=>"24","6003"=>"10",),
); break;
		case "5009":
$item	= array(
"name"	=> "BattleSuit",
"type"	=> "Armor",
"buy"	=> "18000",
"img"	=> "armor_016b.png",
"def"	=> array(60,40,0,0),
"handle"=> "7",
"need"	=> array("6001"=>"12","6002"=>"12","6003"=>"12",),
); break;
		case "5010":
$item	= array(
"name"	=> "FluterArmor",
"type"	=> "Armor",
"buy"	=> "25000",
"img"	=> "armor_016b.png",
"def"	=> array(45,35,28,20),
"handle"=> "8",
); break;
		case "5011":
$item	= array(
"name"	=> "DevilArmor",
"type"	=> "Armor",
"buy"	=> "20000",
"img"	=> "armor_016b.png",
"def"	=> array(20,140,15,70),
"handle"=> "8",
); break;
		case "5012":
$item	= array(
"name"	=> "CoatOfPlate",
"type"	=> "Armor",
"buy"	=> "25000",
"img"	=> "armor_016b.png",
"def"	=> array(47,35,28,30),
"handle"=> "9",
); break;
		case "5013":
$item	= array(
"name"	=> "GoldArmor",
"type"	=> "Armor",
"buy"	=> "40000",
"img"	=> "armor_016b.png",
"def"	=> array(50,35,30,35),
"handle"=> "10",
); break;
		case "5014":
$item	= array(
"name"	=> "PlatinumArmor",
"type"	=> "Armor",
"buy"	=> "50000",
"img"	=> "armor_016b.png",
"def"	=> array(52,35,30,40),
"handle"=> "12",
); break;
		case "5015":
$item	= array(
"name"	=> "CrystalArmor",
"type"	=> "Armor",
"buy"	=> "80000",
"img"	=> "armor_016b.png",
"def"	=> array(55,35,32,45),
"handle"=> "13",
); break;
		case "5016":
$item	= array(
"name"	=> "FullPlate",
"type"	=> "Armor",
"buy"	=> "120000",
"img"	=> "armor_016b.png",
"def"	=> array(60,40,35,45),
"handle"=> "16",
); break;
		case "5100":	//	5100-5200	服
$item	= array(
"name"	=> "CottonShirt",
"type"	=> "Cloth",
"buy"	=> "500",
"img"	=> "armor_014e.png",
"def"	=> array(5,5,5,5),
"handle"=> "1",
"need"	=> array("6180"=>"4",),
); break;
		case "5101":
$item	= array(
"name"	=> "LeatherJacket",
"type"	=> "Cloth",
"buy"	=> "1000",
"img"	=> "armor_014e.png",
"def"	=> array(10,0,10,0),
"handle"=> "2",
"need"	=> array("6040"=>"4","6180"=>"4",),
); break;
		case "5102":
$item	= array(
"name"	=> "LightJacket",
"type"	=> "Cloth",
"buy"	=> "2000",
"img"	=> "armor_014e.png",
"def"	=> array(15,5,15,5),
"handle"=> "3",
"need"	=> array("6040"=>"2","6180"=>"8",),
); break;
		case "5103":
$item	= array(
"name"	=> "LongCoat",
"type"	=> "Cloth",
"buy"	=> "5000",
"img"	=> "armor_014e.png",
"def"	=> array(18,5,18,5),
"handle"=> "4",
"need"	=> array("6040"=>"6","6180"=>"10",),
); break;
		case "5104":
$item	= array(
"name"	=> "HardJacket",
"type"	=> "Cloth",
"buy"	=> "9000",
"img"	=> "armor_014e.png",
"def"	=> array(23,7,23,7),
"handle"=> "5",
"need"	=> array("6040"=>"10","6180"=>"10",),
); break;
		case "5105":
$item	= array(
"name"	=> "KiltedCoat",
"type"	=> "Cloth",
"buy"	=> "14000",
"img"	=> "armor_014e.png",
"def"	=> array(25,10,25,10),
"handle"=> "6",
"need"	=> array("6040"=>"4","6183"=>"12",),
); break;
		case "5106":
$item	= array(
"name"	=> "NobleCoat",
"type"	=> "Cloth",
"buy"	=> "18000",
"img"	=> "armor_014e.png",
"def"	=> array(28,12,28,12),
"handle"=> "7",
"need"	=> array("6040"=>"6","6183"=>"20",),
); break;
		case "5107":
$item	= array(
"name"	=> "CoatOfLord",
"type"	=> "Cloth",
"buy"	=> "22000",
"img"	=> "armor_014e.png",
"def"	=> array(30,15,30,15),
"handle"=> "8",
"need"	=> array("6040"=>"4","6183"=>"15","6184"=>"15",),
); break;
		case "5200":	//	5200-5300	衣
$item	= array(
"name"	=> "CottonRobe",
"type"	=> "Robe",
"buy"	=> "1000",
"img"	=> "armor_012.png",
"def"	=> array(0,5,30,10),
"handle"=> "1",
"need"	=> array("6180"=>"4",),
); break;
		case "5201":
$item	= array(
"name"	=> "SilverRobe",
"type"	=> "Robe",
"buy"	=> "1500",
"img"	=> "armor_012.png",
"def"	=> array(2,5,35,15),
"handle"=> "2",
"need"	=> array("6002"=>"1","6180"=>"6",),
); break;
		case "5202":
$item	= array(
"name"	=> "ElfRobe",
"type"	=> "Robe",
"buy"	=> "3000",
"img"	=> "armor_012.png",
"def"	=> array(3,10,40,20),
"handle"=> "3",
"need"	=> array("6180"=>"8","6184"=>"2",),
); break;
		case "5203":
$item	= array(
"name"	=> "FairyRobe",
"type"	=> "Robe",
"buy"	=> "5000",
"img"	=> "armor_012.png",
"def"	=> array(4,10,45,25),
"handle"=> "4",
"need"	=> array("6180"=>"12","6184"=>"4",),
); break;
		case "5204":
$item	= array(
"name"	=> "CrossRobe",
"type"	=> "Robe",
"buy"	=> "8000",
"img"	=> "armor_012.png",
"def"	=> array(5,10,48,25),
"handle"=> "5",
"need"	=> array("6180"=>"14","6184"=>"4",),
); break;
		case "5205":
$item	= array(
"name"	=> "WhiteRobe",
"type"	=> "Robe",
"buy"	=> "10000",
"img"	=> "armor_012.png",
"def"	=> array(6,10,50,25),
"handle"=> "6",
"need"	=> array("6183"=>"8","6184"=>"8",),
); break;
		case "5206":
$item	= array(
"name"	=> "HolyRobe",
"type"	=> "Robe",
"buy"	=> "14000",
"img"	=> "armor_012.png",
"def"	=> array(7,10,52,30),
"handle"=> "7",
"need"	=> array("6183"=>"12","6184"=>"12",),
); break;
						// 5500 - 装飾品
		case "5500":
$item	= array(
"name"	=> "LifeRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_MAXHP"	=> "50",
"option"	=> "MAXHP+50, ",
); break;
		case "5501":
$item	= array(
"name"	=> "ManaRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_MAXSP"	=> "20",
"option"	=> "MAXSP+20, ",
); break;

		case "5510":
$item	= array(
"name"	=> "PowerRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_STR"	=> "30",
"option"	=> "STR+30, ",
); break;
		case "5515":
$item	= array(
"name"	=> "IntelligenceRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_INT"	=> "30",
"option"	=> "INT+30, ",
); break;
		case "5520":
$item	= array(
"name"	=> "CleverRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_DEX"	=> "30",
"option"	=> "DEX+30, ",
); break;
		case "5525":
$item	= array(
"name"	=> "SpeedRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_SPD"	=> "10",
"option"	=> "SPD+10, ",
); break;
		case "5530":
$item	= array(
"name"	=> "LuckyRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "3",
"P_LUK"	=> "30",
"option"	=> "LUK+30, ",
); break;



		case "5600":
$item	= array(
"name"	=> "BerserkRing",
"type"	=> "Item",
"buy"	=> "10000",
"img"	=> "acce_024.png",
"handle"=> "2",
"P_STR"	=> "100",
"M_MAXHP"	=> "-50",
"option"	=> "STR+100, HP-50% ,",
); break;
						// 6000	-	素材系
		case "6000"://石系
$item	= array(
"name"	=> "Stone",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "5",
"img"	=> "item_009z.png",
); break;
		case "6001":
$item	= array(
"name"	=> "Steel",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_001.png",
); break;
		case "6002":
$item	= array(
"name"	=> "Silver",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_001.png",
); break;
		case "6003":
$item	= array(
"name"	=> "Iron",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "30",
"img"	=> "mat_001.png",
); break;
						// 6020-木材
		case "6020":
$item	= array(
"name"	=> "Lumber",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_025.png",
); break;
		case "6021":
$item	= array(
"name"	=> "Oak",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "30",
"img"	=> "mat_025.png",
); break;
		case "6022":
$item	= array(
"name"	=> "Cypress",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "40",
"img"	=> "mat_025.png",
); break;
		case "6040"://6040-皮
$item	= array(
"name"	=> "Leather",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_024.png",
); break;
		case "6060"://6060-骨
$item	= array(
"name"	=> "",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_016.png",
); break;
		case "6080"://6080-牙
$item	= array(
"name"	=> "",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_013.png",
); break;
		case "6100"://6100-羽
$item	= array(
"name"	=> "",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "20",
"img"	=> "mat_008.png",
); break;
		case "6120"://6120-宝石
$item	= array(
"name"	=> "Diamond",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "100",
"img"	=> "gem_02.png",
); break;
		case "6140"://6140-音
$item	= array(
"name"	=> "NoisySound",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_007.png",
); break;
		case "6160"://6160-コイン
$item	= array(
"name"	=> "GoldCoin",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "500",
"img"	=> "acce_005.png",
); break;
		case "6161":
$item	= array(
"name"	=> "SilverCoin",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "250",
"img"	=> "acce_005b.png",
); break;
		case "6162":
$item	= array(
"name"	=> "BronzeCoin",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "100",
"img"	=> "acce_005c.png",
); break;
						//6180 - 糸,繊維
		case "6180":
$item	= array(
"name"	=> "Cotton",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6181":
$item	= array(
"name"	=> "Vine",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6182":
$item	= array(
"name"	=> "Hemp",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6183":
$item	= array(
"name"	=> "Wool",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6184":
$item	= array(
"name"	=> "Silk",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_008.png",
); break;
		case "6200"://6200 - 音
$item	= array(
"name"	=> "NoisySound",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "other_007.png",
); break;
		case "6600"://6600 - ゴミ
$item	= array(
"name"	=> "Banana",
"type"	=> "Material",
"buy"	=> "100",
"sell"	=> "50",
"img"	=> "banana.png",
); break;
		case "6601":
$item	= array(
"name"	=> "GoldenBanana",
"type"	=> "Material",
"buy"	=> "100",
"sell"	=> "5000",
"img"	=> "banana.png",
); break;
		case "6602":
$item	= array(
"name"	=> "BananaMetal",
"type"	=> "Material",
"buy"	=> "100",
"sell"	=> "50",
"img"	=> "banana.png",
); break;
		case "6800"://6800 - レア
$item	= array(
"name"	=> "DragonTooth",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_013.png",
); break;
		case "6801":
$item	= array(
"name"	=> "DragonWing",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "mat_011.png",
); break;
		case "6802":
$item	= array(
"name"	=> "BrokenSword",
"type"	=> "Material",
"buy"	=> "1000",
"sell"	=> "10",
"img"	=> "we_sword026.png",
); break;
						// 製作強化系
		case "7000":
$item	= array(
"name"	=> "PowerSphere",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_019.png",
"Add"	=> "X00",
); break;
		case "7001":
$item	= array(
"name"	=> "MagicSphere",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_019.png",
"Add"	=> "X01",
); break;
						// 製作強化系(モンスターレアドロップ)
		case "7100":
$item	= array(
"name"	=> "Goblin&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "M01",
); break;
		case "7101":
$item	= array(
"name"	=> "Bat&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7102":
$item	= array(
"name"	=> "SkeltonWarrior&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7103":
$item	= array(
"name"	=> "SkeltonSoldier&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7104":
$item	= array(
"name"	=> "SkeltonArcher&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7105":
$item	= array(
"name"	=> "SkullShaman&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7106":
$item	= array(
"name"	=> "Cyclops&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7107":
$item	= array(
"name"	=> "GoblinSmith&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7108":
$item	= array(
"name"	=> "MimicMonster&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7109":
$item	= array(
"name"	=> "SkeleCaptain&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7110":
$item	= array(
"name"	=> "EvilSorcerer&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7111":
$item	= array(
"name"	=> "Gaze&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7112":
$item	= array(
"name"	=> "EvilServant&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7113":
$item	= array(
"name"	=> "CentaurHunter&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7114":
$item	= array(
"name"	=> "CentaurKnight&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7115":
$item	= array(
"name"	=> "Bafomet&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
		case "7116":
$item	= array(
"name"	=> "LordBafomet&#39;s Tears",
"type"	=> "Material",
"buy"	=> "3000",
"sell"	=> "100",
"img"	=> "item_018.png",
"Add"	=> "",
); break;
//------------------------------ 7500 他消耗品
		case "7500":
$item	= array(
"name"	=> "RenameCard",
"type"	=> "Other",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_035z.png",
); break;
		case "7510":
$item	= array(
"name"	=> "ResetCrystal(status1)",
"type"	=> "Other",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7511":
$item	= array(
"name"	=> "ResetCrystal(status30)",
"type"	=> "Other",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7512":
$item	= array(
"name"	=> "ResetCrystal(status50)",
"type"	=> "Other",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7513":
$item	= array(
"name"	=> "ResetCrystal(status100)",
"type"	=> "Other",
"buy"	=> "100000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
		case "7520":
$item	= array(
"name"	=> "ResetCrystal(skill)",
"type"	=> "Other",
"buy"	=> "500000",
"sell"	=> "100",
"img"	=> "gem_03.png",
); break;
//------------------------------ 8000 地図,カギ
		case "8000":
$item	= array(
"name"	=> "AncientCave",
"type"	=> "Map",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8001":
$item	= array(
"name"	=> "AncientCave B2",
"type"	=> "Key",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8002":
$item	= array(
"name"	=> "AncientCave B3",
"type"	=> "Key",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8003":
$item	= array(
"name"	=> "AncientCave B4",
"type"	=> "Key",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8004":
$item	= array(
"name"	=> "AncientCave B5",
"type"	=> "Key",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "item_032.png",
); break;
		case "8009":
$item	= array(
"name"	=> "TekitoMountain",
"type"	=> "Map",
"buy"	=> "500",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8010":
$item	= array(
"name"	=> "Map to the depth(滴凍山)",
"type"	=> "Map",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
		case "8011":
$item	= array(
"name"	=> "Map to the peak(滴凍山)",
"type"	=> "Map",
"buy"	=> "5000",
"sell"	=> "100",
"img"	=> "book_003.png",
); break;
						// 9000 - その他
		case "9000":
$item	= array(
"name"	=> "AuctionMembershipCard",
"type"	=> "Special",
"buy"	=> "9999",
"sell"	=> "100",
"img"	=> "item_035.png",
); break;


		default:
			return false;
	}

	// 追加変数
	$item["no"]	= $no;
	$item["base_name"]	= $item["name"];
	switch($item["type"]) {
		case "Sword":
		case "TwoHandSword":
		case "Dagger":
		case "Wand":
		case "Staff":
		case "Bow":
		case "Whip":
			$item["type2"]	= "WEAPON";
			break;
		case "Shield":
		case "Book":
		case "Armor":
		case "Cloth":
		case "Robe":
			$item["type2"]	= "GUARD";
			break;
		default:
			$item["type2"]	= "OTHER";
			break;
	}

	// 精錬値
	if($refine) {
		$item["refine"]	= $refine;
		$item["name"]	= "+".$refine." ".$item["name"];
		//$item["name"]	.= "+".$refine;
		//$RefineRate	= 1 + 0.5 * ($refine/10);
		if(isset($item["atk"]["0"])) {
			//$item["atk"]["0"]	= ceil($item["atk"]["0"] * $RefineRate);// 単純式
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
		// 防具の値強化
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
	// 付加能力
	if($option0)
		AddEnchantData($item,$option0);
	if($option1)
		AddEnchantData($item,$option1);
	if($option2)
		AddEnchantData($item,$option2);

	return $item;
}
?>