<?php 
/**
"name"	=> "技能名称",
	"img"	=> "skill_042.png",//图标
	"exp"	=> "技能介绍",
	"sp"	=> "消耗魔力",
	"type"	=> "0",//0=物理 1=魔法
	"target"=> array(friend/enemy/all/self,individual/multi/all,攻撃回数),
		----(例)----------------------------------------
			frien/enemy	= 己方/敌方
			all			= 己方+敌方 全体
			self		= 自己
		enemy individual 1	= 敌方单体攻击1次
		enemy individual 3	= 敌方单体攻击3次
		enemy multi 3		= 敌方随机攻击3人1次(可能对同一目标重复攻击)
		enemy all 1			= 敌方全体攻击1次
		all individual 5	= 对敌我全体攻击5次
		all multi 5			= 对敌我全体随机攻击5人1次(可能对同一目标重复攻击)
		all all 3			= 对敌我全体全部攻击3次
		------------------------------------------------
	"pow"	=> "100",// 攻击倍数 130=1.3倍 100为基本攻击。
	// "hit"	=> "100",// 命中率
	"invalid"	=> "1",//攻击后排
	"support"	=> "1",//己方增益魔法(需要与"invalid"区别)
	"priority"	=> "LowHpRate",//优先目标(LowHpRate,Dead,Summon,Charge)
	//"charge"	=> "",//吟唱或蓄力时间(0=无CD瞬发)
	//"stiff"	=> "",//释放后硬直时间(0=无硬直 100=待机时间x2(待机时间=硬直时间) )
	"charge" => array(charge,stiff),//更改为数组。
	"learn"	=> "学习技能需要的技能点",
	"Up**"
	"Down**"
	"pierce"
	"delay"
	"knockback"
	"poison"
	"summon"
	"move"
	"strict" => array("Bow"=>true),//武器限制
	"umove" // 使用者が移動。
	"DownSTR"	=> "40",// IND DEX SPD LUK ATK MATK DEF MDEF HP SP
	"UpSTR"
	"PlusSTR"	=> 50,

*/
function LoadSkillData($no) {
	switch($no) {
		case "1000":
$skill	= array(
"name"	=> "攻击",
"img"	=> "skill_042.png",
"exp"	=> "通常攻击",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
); break;
		case "1001":
$skill	= array(
"name"	=> "痛击",
"img"	=> "skill_032.png",
"exp"	=> "",
"sp"	=> "8",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("enemy","individual",1),
"pow"	=> "160",
"charge"=> array(20,20),
); break;
		case "1002":
$skill	= array(
"name"	=> "火球术",
"img"	=> "skill_018.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "0",
"target"=> array("enemy","multi",4),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(60,0),
); break;
		case "1003":
$skill	= array(
"name"	=> "双重打击",
"img"	=> "skill_073.png",
"exp"	=> "",
"sp"	=> "15",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",2),
"pow"	=> "90",
); break;
//---------------------------------------------------//
//  1010 到目前为止都是测试技能！                      //
//---------------------------------------------------//
		case "1011":
$skill	= array(
"name"	=> "破坏武器",
"img"	=> "skill_072.png",
"exp"	=> "攻击力低下",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"charge"=> array(0,0),
"DownATK" => "50",
"DownMATK" => "50",
); break;
		case "1012":
$skill	= array(
"name"	=> "破坏装备",
"img"	=> "skill_072.png",
"exp"	=> "防御力低下",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"charge"=> array(0,0),
"DownDEF" => "30",
"DownMDEF" => "30",
); break;
		case "1013":
$skill	= array(
"name"	=> "突刺",
"img"	=> "skill_074.png",
"exp"	=> "",
"sp"	=> "15",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "190",
"charge"=> array(0,40),
); break;
		case "1014":
$skill	= array(
"name"	=> "致命突刺",
"img"	=> "skill_074z.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("enemy","individual",1),
"pow"	=> "360",
"charge"=> array(0,50),
); break;
		case "1015":
$skill	= array(
"name"	=> "推后",
"img"	=> "skill_075.png",
"exp"	=> "后卫化",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("enemy","individual",1),
"pow"	=> "150",
"charge"=> array(40,20),
"knockback"	=> "100",
); break;
		case "1016":
$skill	= array(
"name"	=> "刺穿装备",
"img"	=> "skill_077.png",
"exp"	=> "无视防御力",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"pow"	=> "170",
"charge"=> array(40,40),
"pierce"=> true,
); break;
		case "1017":
$skill	= array(
"name"	=> "愤怒一击",
"img"	=> "skill_031.png",
"exp"	=> "",
"sp"	=> "40",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","multi",5),
"pow"	=> "100",
"charge"=> array(40,60),
); break;
		case "1018":
$skill	= array(
"name"	=> "敌我乱打",
"img"	=> "skill_031z.png",
"exp"	=> "不分敌我的全员攻击",
"sp"	=> "65",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("all","multi",8),
"pow"	=> "100",
"invalid"	=> true,
"charge"=> array(50,100),
); break;
		case "1019":
$skill	= array(
"name"	=> "穿刺",
"img"	=> "skill_077z.png",
"exp"	=> "无视防御力",
"sp"	=> "80",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("enemy","multi",6),
"pow"	=> "60",
"charge"=> array(60,60),
"pierce"=> true,
); break;
		case "1020":
$skill	= array(
"name"	=> "破坏精神",
"img"	=> "skill_073z.png",
"exp"	=> "SP下降",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","individual",1),
"pow"	=> "120",
); break;
		case "1021":
$skill	= array(
"name"	=> "破坏灵魂",
"img"	=> "skill_072z.png",
"exp"	=> "SP+HP下降",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"pow"	=> "160",
); break;
		case "1022":
$skill	= array(
"name"	=> "冲锋",
"img"	=> "skill_033.png",
"exp"	=> "后排时威力四倍+前进",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"charge"=> array(0,30),
); break;
		case "1023":
$skill	= array(
"name"	=> "一击脱离",
"img"	=> "skill_033z.png",
"exp"	=> "前排时威力三倍+后退",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"charge"=> array(0,10),
); break;
		case "1024":
$skill	= array(
"name"	=> "分裂生命",
"img"	=> "skill_073y.png",
"exp"	=> "对象的HP分裂",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("enemy","individual",1),
"charge"=> array(0,50),
); break;
		case "1025":
$skill	= array(
"name"	=> "分裂精神",
"img"	=> "skill_073x.png",
"exp"	=> "对象的SP分裂",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "3",
"target"=> array("enemy","individual",1),
); break;
									// 1100 - 狂战士技能
		case "1100":
$skill	= array(
"name"	=> "力量上升",
"img"	=> "skill_057.png",
"exp"	=> "力量上升",
"sp"	=> "5",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"=> true,
"sacrifice"	=> "15",
"UpSTR"	=> "100",
); break;
		case "1101":
$skill	= array(
"name"	=> "速度上升",
"img"	=> "skill_057.png",
"exp"	=> "速度上升",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"=> true,
"sacrifice"	=> "25",
"PlusSPD"	=> "100",
); break;
		case "1102":
$skill	= array(
"name"	=> "智力上升",
"img"	=> "skill_057.png",
"exp"	=> "智力上升",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"=> true,
"sacrifice"	=> "15",
"UpINT"	=> "100",
); break;
		case "1113":
$skill	= array(
"name"	=> "痛苦",
"img"	=> "skill_057.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","individual",1),
"pow"	=> "200",
"sacrifice"	=> "5",
); break;
		case "1114":
$skill	= array(
"name"	=> "速攻",
"img"	=> "skill_057.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",4),
"pow"	=> "100",
"sacrifice"	=> "15",
); break;
		case "1115":
$skill	= array(
"name"	=> "毁灭",
"img"	=> "skill_057.png",
"exp"	=> "",
"sp"	=> "1",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("enemy","multi",4),
"pow"	=> "200",
"sacrifice"	=> "30",
); break;
		case "1116":
$skill	= array(
"name"	=> "惩罚",
"img"	=> "skill_057.png",
"exp"	=> "根据自己减少的HP来制造对敌人的伤害",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "12",
"target"=> array("enemy","individual",1),
"charge"=> array(100,100),
); break;
		case "1117":
$skill	= array(
"name"	=> "疾病",
"img"	=> "skill_057.png",
"exp"	=> "毒化",
"sp"	=> "32",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"sacrifice"	=> "20",
"charge"=> array(0,50),
"poison"=> "100",
); break;
		case "1118":
$skill	= array(
"name"	=> "击退",
"img"	=> "skill_057.png",
"exp"	=> "敌后退",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"sacrifice"	=> "50",
"charge"=> array(100,100),
"knockback"=> "100",
); break;
		case "1119":
$skill	= array(
"name"	=> "友方强化",
"img"	=> "skill_057.png",
"exp"	=> "?",
"sp"	=> "70",
"type"	=> "0",
"learn"	=> "16",
"target"=> array("friend","all",1),
"sacrifice"	=> "200",
"charge"=> array(100,0),
"UpSTR"	=> "55",
"UpINT"	=> "55",
"UpSPD"	=> "55",
"UpATK"	=> "55",
"UpMATK"=> "55",
); break;
//------------------------------------------------ 1200 暗杀者
		case "1200":
$skill	= array(
"name"	=> "毒之一击",
"img"	=> "skill_074y.png",
"exp"	=> "对方毒状态的话威力6倍",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"limit"=> array("匕首"=>true,),
); break;
		case "1203":
$skill	= array(
"name"	=> "掷匕首",
"img"	=> "we_sword001.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "1",
"target"=> array("enemy","individual",1),
"pow"	=> "140",
"invalid"	=> "1",
"limit"=> array("匕首"=>true,),
); break;
		case "1204":
$skill	= array(
"name"	=> "匕首乱打",
"img"	=> "we_sword001z.png",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",4),
"pow"	=> "130",
"invalid"	=> "1",
"limit"=> array("匕首"=>true,),
); break;
		case "1205":
$skill	= array(
"name"	=> "酸化表面",
"img"	=> "item_027.png",
"exp"	=> "防御低下",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"DownDEF"	=> "30",
"DownMDEF"	=> "30",
); break;
		case "1206":
$skill	= array(
"name"	=> "酸雾",
"img"	=> "skill_079z.png",
"exp"	=> "防御低下",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","all",1),
"DownDEF"	=> "15",
); break;
		case "1207":
$skill	= array(
"name"	=> "毒之气息",
"img"	=> "skill_005cz.png",
"exp"	=> "前卫化",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","all",1),
"umove"	=> "front",
"charge"=> array(30,30),
"poison"=> "80",
); break;
		case "1208":
$skill	= array(
"name"	=> "使毒",
"img"	=> "skill_024z.png",
"exp"	=> "毒状态的对方失血(int相关？？)",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","all",1),
); break;
		case "1209":
$skill	= array(
"name"	=> "传毒",
"img"	=> "item_031.png",
"exp"	=> "毒状态能力上升+解毒",
"sp"	=> "80",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("friend","all",1),
"PlusSTR"	=> 50,
"PlusSPD"	=> 50,
"charge"=> array(0,100),
"CurePoison"	=> true,
); break;
		case "1210":
$skill	= array(
"name"	=> "前排致盲",
"img"	=> "skill_073x.png",
"exp"	=> "行动延迟",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","all",1),
); break;
		case "1211":
$skill	= array(
"name"	=> "后排致盲",
"img"	=> "skill_073x.png",
"exp"	=> "行动延迟",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","all",1),
); break;
		case "1220":
$skill	= array(
"name"	=> "抗毒",
"img"	=> "item_026b.png",
"exp"	=> "毒耐性+50%",
"sp"	=> "80",
"type"	=> "0",
"learn"	=> "5",
"target"=> array("friend","all",1),
); break;
//---------------------------------------------- 1240 驯兽师
		case "1240":
$skill	= array(
"name"	=> "抽打",
"img"	=> "we_other007y.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("enemy","multi",2),
"pow"	=> "90",
"limit"=> array("鞭"=>true,),
); break;
		case "1241":
$skill	= array(
"name"	=> "鞭打",
"img"	=> "we_other007y.png",
"exp"	=> "",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","multi",4),
"pow"	=> "90",
"limit"=> array("鞭"=>true,),
); break;
		case "1242":
$skill	= array(
"name"	=> "鞭子风暴",
"img"	=> "we_other007y.png",
"exp"	=> "",
"sp"	=> "40",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",6),
"pow"	=> "90",
"limit"=> array("鞭"=>true,),
); break;
		case "1243":
$skill	= array(
"name"	=> "鞭咬",
"img"	=> "we_other007y.png",
"exp"	=> "",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",2),
"pow"	=> "80",
"delay"	=> 50,
"limit"=> array("鞭"=>true,),
); break;
		case "1244":
$skill	= array(
"name"	=> "身体固定",
"img"	=> "we_other007y.png",
"exp"	=> "",
"sp"	=> "40",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",2),
"pow"	=> "60",
"delay"	=> 30,
"DownSPD"	=> 30,
"limit"=> array("鞭"=>true,),
); break;
//------------------------------------------------ 
									// 2000 - 魔法系らしい
		case "2000":
$skill	= array(
"name"	=> "火焰风暴",
"img"	=> "skill_004a.png",
"exp"	=> "",
"sp"	=> "70",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","multi",6),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(70,0),
); break;
		case "2001":
$skill	= array(
"name"	=> "地狱火",
"img"	=> "skill_006a.png",
"exp"	=> "",
"sp"	=> "320",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","multi",12),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(120,0),
); break;
		case "2002":
$skill	= array(
"name"	=> "火柱",
"img"	=> "skill_007a.png",
"exp"	=> "力DOWN",
"sp"	=> "40",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","multi",2),
"pow"	=> "140",
"invalid"	=> "1",
"charge"=> array(50,0),
"DownSTR"	=> "40",
); break;
		case "2003":
$skill	= array(
"name"	=> "爆炸",
"img"	=> "skill_005a.png",
"exp"	=> "力DOWN",
"sp"	=> "180",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("all","all",1),
"pow"	=> "200",
"charge"=> array(100,0),
"DownSTR"	=> "40",
); break;
		case "2004":
$skill	= array(
"name"	=> "陨石风暴",
"img"	=> "skill_021z.png",
"exp"	=> "",
"sp"	=> "800",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","multi",16),
"pow"	=> "140",
"charge"=> array(200,0),
); break;
		case "2010":
$skill	= array(
"name"	=> "冰之枪",
"img"	=> "skill_001b.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "1",
"target"=> array("enemy","individual",3),
"pow"	=> "100",
"charge"=> array(30,0),
); break;
		case "2011":
$skill	= array(
"name"	=> "冰标枪",
"img"	=> "skill_002b.png",
"exp"	=> "",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","individual",3),
"pow"	=> "150",
"charge"=> array(40,0),
); break;
		case "2012":
$skill	= array(
"name"	=> "暴风雪",
"img"	=> "skill_006b.png",
"exp"	=> "",
"sp"	=> "240",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","multi",10),
"pow"	=> "90",
"charge"=> array(90,0),
); break;
		case "2013":
$skill	= array(
"name"	=> "冰柱",
"img"	=> "skill_034.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "0",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"charge"=> array(30,0),
); break;
		case "2014":
$skill	= array(
"name"	=> "冰狱",
"img"	=> "skill_055.png",
"exp"	=> "防御DOWN",
"sp"	=> "40",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "180",
"invalid"	=> "1",
"charge"=> array(40,0),
"DownDEF"	=> "30",
"DownMDEF"	=> "30",
); break;
		case "2015":
$skill	= array(
"name"	=> "海浪",
"img"	=> "skill_056z.png",
"exp"	=> "后卫化",
"sp"	=> "520",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","multi",24),
"pow"	=> "80",
"charge"=> array(170,100),
"knockback"	=> "100",
); break;
		case "2020":
$skill	= array(
"name"	=> "雷击",
"img"	=> "skill_030z.png",
"exp"	=> "",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "1",
"target"=> array("enemy","individual",1),
"pow"	=> "400",
"invalid"	=> "1",
"charge"=> array(50,0),
); break;
		case "2021":
$skill	= array(
"name"	=> "闪电球",
"img"	=> "skill_054z.png",
"exp"	=> "",
"sp"	=> "80",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","multi",3),
"pow"	=> "220",
"invalid"	=> "1",
"charge"=> array(70,0),
); break;
		case "2022":
$skill	= array(
"name"	=> "闪光",
"img"	=> "skill_022z.png",
"exp"	=> "行动延迟",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","all",1),
"charge"=> array(30,0),
"delay"	=> "25",
); break;
		case "2023":
$skill	= array(
"name"	=> "麻痹",
"img"	=> "skill_025.png",
"exp"	=> "行动延迟",
"sp"	=> "15",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "50",
"charge"=> array(30,0),
"delay"	=> "120",
); break;
		case "2024":
$skill	= array(
"name"	=> "雷暴",
"img"	=> "skill_006cz.png",
"exp"	=> "",
"sp"	=> "400",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","multi",5),
"pow"	=> "300",
"charge"=> array(140,0),
"invalid"	=> "1",
); break;
		case "2030":
$skill	= array(
"name"	=> "生命吸收",
"img"	=> "skill_062z.png",
"exp"	=> "HP吸收",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"pow"	=> "230",
"invalid"	=> "1",
"charge"=> array(10,40),
); break;
		case "2031":
$skill	= array(
"name"	=> "生命挤压",
"img"	=> "skill_078.png",
"exp"	=> "HP吸收",
"sp"	=> "70",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"pow"	=> "120",
"charge"=> array(30,80),
); break;
		case "2032":
$skill	= array(
"name"	=> "死亡之钟",
"img"	=> "skill_041z.png",
"exp"	=> "即死",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("enemy","individual",1),
"invalid"	=> "1",
"charge"=> array(100,0),
); break;
		case "2040":
$skill	= array(
"name"	=> "沙漠风暴",
"img"	=> "skill_006d.png",
"exp"	=> "行动延迟",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("enemy","all",1),
"pow"	=> "60",
"charge"=> array(200,0),
"delay"	=> "80",
); break;
		case "2041":
$skill	= array(
"name"	=> "地震",
"img"	=> "skill_056y.png",
"exp"	=> "",
"sp"	=> "80",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("all","all",1),
"pow"	=> "200",
"charge"=> array(100,30),
); break;
		case "2042":
$skill	= array(
"name"	=> "沉陷",
"img"	=> "skill_056.png",
"exp"	=> "",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("all","all",1),
"pow"	=> "350",
"charge"=> array(130,50),
); break;
//-------------------------------- 2050
		case "2050":
$skill	= array(
"name"	=> "猛毒轰击",
"img"	=> "skill_024.png",
"exp"	=> "毒化",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","multi",2),
"pow"	=> "200",
"charge"=> array(40,0),
"poison"=> "100",
); break;
		case "2051":
$skill	= array(
"name"	=> "毒烟",
"img"	=> "skill_079.png",
"exp"	=> "毒化",
"sp"	=> "80",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"pow"	=> "150",
"charge"=> array(70,0),
"poison"=> "100",
); break;
		case "2055":
$skill	= array(
"name"	=> "灵魂复仇",
"img"	=> "skill_065.png",
"exp"	=> "根据死者数伤害增加",
"sp"	=> "340",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"pow"	=> "50",
"charge"=> array(0,60),
); break;
		case "2056":
$skill	= array(
"name"	=> "僵尸复活",
"img"	=> "skill_061.png",
"exp"	=> "我方复活",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("friend","all",1),
"charge"=> array(50,100),
"DownMAXHP"=>"30",
"DownDEF"=>"100",
"DownMDEF"=>"100",
"DownSPD"=>"50",
"priority"	=> "Dead",
); break;
		case "2057":
$skill	= array(
"name"	=> "自我蜕变",
"img"	=> "skill_066.png",
"exp"	=> "HP60%以下可使用(1回限制)",
"sp"	=> "250",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("self","individual",1),
"charge"=> array(0,40),
"UpMAXHP"=> 200,
"UpMATK"=> 100,
"UpINT"=> 100,
"UpSPD"=> 50,
); break;
//-------------------------------- 2060
		case "2060":
$skill	= array(
"name"	=> "魔法爆炸",
"img"	=> "skill_020.png",
"exp"	=> "",
"sp"	=> "140",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"pow"	=> "500",
"charge"=> array(0,0),
); break;
//---------------------------------- 2090
		case "2090":
$skill	= array(
"name"	=> "能量抢夺",
"img"	=> "skill_037.png",
"exp"	=> "SP吸收",
"sp"	=> "10",
"type"	=> "1",
"learn"	=> "3",
"target"=> array("enemy","individual",1),
"pow"	=> "150",
"invalid"	=> "1",
"charge"=> array(30,0),
); break;
		case "2091":
$skill	= array(
"name"	=> "能量收集",
"img"	=> "skill_037.png",
"exp"	=> "SP吸收",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","all",1),
"pow"	=> "70",
"invalid"	=> "1",
"charge"=> array(100,0),
); break;
							// 2100
		case "2100":
$skill	= array(
"name"	=> "圣光",
"img"	=> "skill_022.png",
"exp"	=> "",
"sp"	=> "10",
"type"	=> "1",
"learn"	=> "1",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(10,0),
); break;
		case "2101":
$skill	= array(
"name"	=> "圣光爆发",
"img"	=> "skill_010z.png",
"exp"	=> "",
"sp"	=> "40",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","multi",3),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(40,0),
); break;
		case "2102":
$skill	= array(
"name"	=> "大十字",
"img"	=> "item_036b.png",
"exp"	=> "",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("enemy","all",1),
"pow"	=> "200",
"charge"=> array(70,30),
"MagicCircleDeleteTeam"	=> 1,
); break;
							// 2110
							// 詠唱中のキャラのみに適応する。
		case "2110":
$skill	= array(
"name"	=> "打扰咏唱",
"img"	=> "skill_016.png",
"exp"	=> "Charge（咏唱）妨害",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"invalid"	=> "1",
"priority"	=> "Charge",
"delay"	=> "200",
"charge"	=> array(0,40),
); break;
		case "2111":
$skill	= array(
"name"	=> "打扰咏唱(全员)",
"img"	=> "skill_016.png",
"exp"	=> "Charge（咏唱）妨害(全)",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","all",1),
"invalid"	=> "1",
"priority"	=> "Charge",
"delay"	=> "100",
"charge"	=> array(0,40),
); break;
/////////////////////// 2300-弓系列 "inf"	=> "dex",// 威力をdex依存にする
		case "2300":
$skill	= array(
"name"	=> "射击",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "100",
"invalid"	=> "1",
"priority"	=> "Back",
"charge"=> array(0,0),
"limit"=> array("弓"=>true,),
); break;
		case "2301":
$skill	= array(
"name"	=> "强力射击",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "200",
"invalid"	=> "1",
"charge"=> array(0,30),
"limit"=> array("弓"=>true),
); break;
		case "2302":
$skill	= array(
"name"	=> "箭雨",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","multi",6),
"inf"	=> "dex",
"pow"	=> "60",
"invalid"	=> "1",
"charge"=> array(0,0),
"limit"=> array("弓"=>true),
); break;
		case "2303":
$skill	= array(
"name"	=> "麻痹射击",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "100",
"invalid"	=> "1",
"priority"	=> "Back",
"charge"=> array(0,0),
"delay"	=> "80",
"limit"=> array("弓"=>true),
); break;
		case "2304":
$skill	= array(
"name"	=> "中毒攻击",
"img"	=> "item_042.png",
"exp"	=> "毒",
"sp"	=> "15",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","multi",2),
"inf"	=> "dex",
"pow"	=> "50",
"invalid"	=> "1",
"charge"=> array(0,0),
"poison"=> "100",
"limit"=> array("弓"=>true),
); break;
		case "2305":
$skill	= array(
"name"	=> "换位射击",
"img"	=> "item_042.png",
"exp"	=> "后卫化",
"sp"	=> "30",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "100",
"charge"=> array(30,0),
"knockback"	=> "100",
"limit"=> array("弓"=>true),
); break;
		case "2306":
$skill	= array(
"name"	=> "穿透射击",
"img"	=> "item_042.png",
"exp"	=> "无视防御",
"sp"	=> "90",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "180",
"invalid"	=> "1",
"charge"=> array(60,0),
"pierce"=> true,
"limit"=> array("弓"=>true),
); break;
		case "2307":
$skill	= array(
"name"	=> "飓风射击",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "180",
"type"	=> "0",
"learn"	=> "16",
"target"=> array("enemy","multi",16),
"inf"	=> "dex",
"pow"	=> "70",
"invalid"	=> "1",
"charge"=> array(50,80),
"limit"=> array("弓"=>true),
); break;
		case "2308":
$skill	= array(
"name"	=> "瞄准",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "130",
"invalid"	=> "1",
"priority"	=> "Back",
"charge"=> array(0,0),
"limit"=> array("弓"=>true),
); break;
		case "2309":
$skill	= array(
"name"	=> "解除武装",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("enemy","individual",1),
"inf"	=> "dex",
"pow"	=> "80",
"invalid"	=> "1",
"priority"	=> "Back",
"DownATK" => "70",
"DownMATK" => "70",
"limit"=> array("弓"=>true),
); break;
		case "2310":
$skill	= array(
"name"	=> "双重射击",
"img"	=> "item_042.png",
"exp"	=> "",
"sp"	=> "28",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("enemy","multi",2),
"inf"	=> "dex",
"pow"	=> "80",
"invalid"	=> "1",
"priority"	=> "Back",
"limit"=> array("弓"=>true),
); break;
								// 2400-召喚系
		case "2400":
$skill	= array(
"name"	=> "哥布林召唤",
"img"	=> "skill_066.png",
"exp"	=> "哥布林召唤",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(30,0),
"summon"	=> "1000",
); break;
		case "2401":
$skill	= array(
"name"	=> "召唤小猪",
"img"	=> "skill_028.png",
"exp"	=> "召唤小猪",
"sp"	=> "150",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"=> array(0,100),
"summon"	=> "5008",
); break;
		case "2402":
$skill	= array(
"name"	=> "召唤疯猪",
"img"	=> "skill_028.png",
"exp"	=> "召唤疯猪",
"sp"	=> "250",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("self","individual",1),
"charge"=> array(0,300),
"summon"	=> "5009",
); break;
		case "2403":
$skill	= array(
"name"	=> "大怪物召唤",
"img"	=> "skill_029.png",
"exp"	=> "大怪物召唤",
"sp"	=> "350",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,500),
"summon"	=> "5010",
); break;
		case "2404":
$skill	= array(
"name"	=> "召唤狮子",
"img"	=> "skill_028.png",
"exp"	=> "召唤狮子",
"sp"	=> "150",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"=> array(0,100),
"summon"	=> "5011",
"quick"	=> true,
); break;
		case "2405":
$skill	= array(
"name"	=> "召唤熊",
"img"	=> "skill_028.png",
"exp"	=> "召唤熊",
"sp"	=> "250",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("self","individual",1),
"charge"=> array(0,300),
"summon"	=> "5012",
"quick"	=> true,
); break;
		case "2406":
$skill	= array(
"name"	=> "召唤合成兽",
"img"	=> "skill_029.png",
"exp"	=> "召唤合成兽",
"sp"	=> "350",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,500),
"summon"	=> "5013",
"quick"	=> true,
); break;
		case "2407":
$skill	= array(
"name"	=> "召唤雪男",
"img"	=> "skill_028.png",
"exp"	=> "召唤雪男",
"sp"	=> "250",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("self","individual",1),
"charge"=> array(0,300),
"summon"	=> "5014",
"quick"	=> true,
); break;
		case "2408":
$skill	= array(
"name"	=> "召唤小妖精",
"img"	=> "skill_028.png",
"exp"	=> "召唤小妖精",
"sp"	=> "150",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"=> array(0,100),
"summon"	=> "5015",
"quick"	=> true,
); break;
		case "2409":
$skill	= array(
"name"	=> "召唤飞河马",
"img"	=> "skill_028.png",
"exp"	=> "召唤飞河马",
"sp"	=> "250",
"type"	=> "0",
"learn"	=> "10",
"target"=> array("self","individual",1),
"charge"=> array(0,300),
"summon"	=> "5016",
"quick"	=> true,
); break;
		case "2410":
$skill	= array(
"name"	=> "召唤龙",
"img"	=> "skill_029.png",
"exp"	=> "召唤龙",
"sp"	=> "350",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,500),
"summon"	=> "5017",
"quick"	=> true,
); break;
				// 2460 - 僵尸?
		case "2460":
$skill	= array(
"name"	=> "僵尸",
"img"	=> "skill_028.png",
"exp"	=> "僵尸",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "2",
"target"=> array("self","individual",1),
"charge"=> array(30,0),
"summon"	=> "5004",
); break;
		case "2461":
$skill	= array(
"name"	=> "食尸鬼",
"img"	=> "skill_028.png",
"exp"	=> "食尸鬼",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"=> array(40,0),
"summon"	=> "5005",
); break;
		case "2462":
$skill	= array(
"name"	=> "木乃伊",
"img"	=> "skill_028.png",
"exp"	=> "木乃伊",
"sp"	=> "120",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("self","individual",1),
"charge"=> array(60,0),
"summon"	=> "5006",
); break;
		case "2463":
$skill	= array(
"name"	=> "僵尸控制",
"img"	=> "skill_028.png",
"exp"	=> "3体召唤",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("self","individual",1),
"charge"=> array(50,50),
"summon"	=> array(5004,5005,5004),
); break;
		case "2464":
$skill	= array(
"name"	=> "墓地",
"img"	=> "skill_028.png",
"exp"	=> "3体召唤",
"sp"	=> "360",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("self","individual",1),
"charge"=> array(100,0),
"summon"	=> array(5006,5007,5006),
); break;
		case "2465":
$skill	= array(
"name"	=> "生化危机",
"img"	=> "skill_028.png",
"exp"	=> "5体召唤",
"sp"	=> "560",
"type"	=> "1",
"learn"	=> "16",
"target"=> array("self","individual",1),
"charge"=> array(160,0),
"summon"	=> array(5004,5006,5007,5006,5004),
); break;
								// 2480
		case "2480":
$skill	= array(
"name"	=> "治愈兔",
"img"	=> "skill_038.png",
"exp"	=> "召唤治疗兔子",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> "5000",
"quick"	=> true,
); break;
		case "2481":
$skill	= array(
"name"	=> "天使降临",
"img"	=> "skill_038.png",
"exp"	=> "天使降临",
"sp"	=> "160",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("self","individual",1),
"charge"=> array(60,0),
"summon"	=> "5001",
"quick"	=> true,
); break;
//-----------------------------------------	2500 まだ召喚系
		case "2500":
$skill	= array(
"name"	=> "伊弗里特",
"img"	=> "skill_029.png",
"exp"	=> "",
"sp"	=> "700",
"type"	=> "1",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(100,300),
"summon"	=> "5103",
"quick"	=> true,
"MagicCircleDeleteTeam"	=> 4,
); break;
		case "2501":
$skill	= array(
"name"	=> "利维坦",
"img"	=> "skill_029.png",
"exp"	=> "",
"sp"	=> "700",
"type"	=> "1",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(100,300),
"summon"	=> "5104",
"quick"	=> true,
"MagicCircleDeleteTeam"	=> 4,
); break;
		case "2502":
$skill	= array(
"name"	=> "天使长",
"img"	=> "skill_029.png",
"exp"	=> "",
"sp"	=> "900",
"type"	=> "1",
"learn"	=> "30",
"target"=> array("self","individual",1),
"charge"=> array(100,300),
"summon"	=> "5100",
"quick"	=> true,
"MagicCircleDeleteTeam"	=> 5,
); break;
		case "2503":
$skill	= array(
"name"	=> "堕落天使",
"img"	=> "skill_029.png",
"exp"	=> "",
"sp"	=> "900",
"type"	=> "1",
"learn"	=> "30",
"target"=> array("self","individual",1),
"charge"=> array(100,300),
"summon"	=> "5101",
"quick"	=> true,
"MagicCircleDeleteTeam"	=> 5,
); break;
		case "2504":
$skill	= array(
"name"	=> "托尔",
"img"	=> "skill_029.png",
"exp"	=> "",
"sp"	=> "1200",
"type"	=> "1",
"learn"	=> "35",
"target"=> array("self","individual",1),
"charge"=> array(100,500),
"summon"	=> "5102",
"quick"	=> true,
"MagicCircleDeleteTeam"	=> 5,
); break;
////////////////////////////////////////
		case "3000"://	3000 - 其他
$skill	= array(
"name"	=> "治疗",
"img"	=> "skill_013a.png",
"exp"	=> "HP回复",
"sp"	=> "5",
"type"	=> "1",
"learn"	=> "0",
"target"=> array("friend","individual",1),
"pow"	=> "200",
"support"	=> "1",
"priority"	=> "LowHpRate",
"exp"	=> "",
"charge"=> array(30,0),
); break;
		case "3001":
$skill	= array(
"name"	=> "高级治疗",
"img"	=> "skill_013b.png",
"exp"	=> "HP回复",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("friend","multi",2),
"pow"	=> "300",
"support"	=> "1",
"priority"	=> "LowHpRate",
"charge"=> array(50,0),
); break;
		case "3002":
$skill	= array(
"name"	=> "群体回复",
"img"	=> "skill_013c.png",
"exp"	=> "HP回复",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "12",
"target"=> array("friend","all",1),
"pow"	=> "150",
"support"	=> "1",
"priority"	=> "LowHpRate",
"charge"=> array(50,0),
); break;
		case "3003":
$skill	= array(
"name"	=> "快速回复",
"img"	=> "skill_013b.png",
"exp"	=> "HP回复",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("friend","multi",2),
"pow"	=> "180",
"support"	=> "1",
"priority"	=> "LowHpRate",
); break;
		case "3004":
$skill	= array(
"name"	=> "整体回复",
"img"	=> "skill_013b.png",
"exp"	=> "HP回复",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","multi",3),
"pow"	=> "200",
"support"	=> "1",
"priority"	=> "LowHpRate",
"charge"=> array(40,0),
); break;
		case "3005":
$skill	= array(
"name"	=> "渐渐回复",
"img"	=> "skill_013b.png",
"exp"	=> "对象HP30%以下时回复量2倍",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("friend","multi",3),
"pow"	=> "125",
"support"	=> "1",
"priority"	=> "LowHpRate",
"charge"=> array(20,0),
); break;
		case "3010"://	3010
$skill	= array(
"name"	=> "恢复精神",
"img"	=> "skill_019.png",
"exp"	=> "SP回复",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "0",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
		case "3011":
$skill	= array(
"name"	=> "集中精神",
"img"	=> "skill_019z.png",
"exp"	=> "SP回复",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"	=> array(30,0),
); break;
		case "3012":
$skill	= array(
"name"	=> "血转魔",
"img"	=> "skill_019y.png",
"exp"	=> "SP回复",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("self","individual",1),
"pow"	=> "100",
"support"	=> "1",
"charge"	=> array(20,0),
); break;
		case "3013":
$skill	= array(
"name"	=> "魔转血",
"img"	=> "exchange.png",
"exp"	=> "HP,SP交换(%)",
"sp"	=> "10",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
		case "3020":
$skill	= array(
"name"	=> "魔力上升",
"img"	=> "skill_019.png",
"exp"	=> "最大SP上升",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
					// 3030
		case "3030":
$skill	= array(
"name"	=> "异常恢复",
"img"	=> "skill_008.png",
"exp"	=> "状态异常恢复",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "2",
"target"=> array("friend","all",1),
"support"	=> "1",
"pow"	=> "70",
"charge"	=> array(50,0),
"CurePoison"	=> true,
); break;
					// 3040
		case "3040":
$skill	= array(
"name"	=> "复苏",
"img"	=> "mat_026.png",
"exp"	=> "复苏",
"sp"	=> "120",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(40,30),
"pow"		=> "600",
"priority"	=> "Dead",
); break;
//---------------------------------- 3050
		case "3050":
$skill	= array(
"name"	=> "立即行动",
"img"	=> "skill_015.png",
"exp"	=> "立即行动",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(200,100),
); break;
//---------------------------------- 3055
		case "3055":
$skill	= array(
"name"	=> "加快咏唱",
"img"	=> "skill_016z.png",
"exp"	=> "加快咏唱",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,100),
); break;
//---------------------------------- 3060
		case "3060":
$skill	= array(
"name"	=> "圣光防护",
"img"	=> "skill_045z.png",
"exp"	=> "一回合受伤无效化",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,100),
); break;
//-------------------------- 3101
		case "3101"://	3101
$skill	= array(
"name"	=> "祝福",
"img"	=> "skill_008.png",
"exp"	=> "SP回复",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "0",
"target"=> array("friend","all",1),
"SpRecoveryRate"	=> 3,
"support"	=> "1",
); break;
		case "3102":
$skill	= array(
"name"	=> "大祝福",
"img"	=> "skill_009.png",
"exp"	=> "SP回复",
"sp"	=> "20",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("friend","all",1),
"SpRecoveryRate"	=> 5,
"support"	=> "1",
"charge"	=> array(40,0),
); break;
		case "3103":
$skill	= array(
"name"	=> "圣域",
"img"	=> "skill_010.png",
"exp"	=> "HP,SP回复",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("friend","all",1),
"pow"	=> "500",
"SpRecoveryRate"	=> 7,
"support"	=> "1",
"charge"	=> array(50,0),
"MagicCircleDeleteTeam"	=> 2,
"CurePoison"	=> true,
); break;
//----------------------------- 3110
		case "3110":
$skill	= array(
"name"	=> "强化",
"img"	=> "skill_059.png",
"exp"	=> "自己强化",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "2",
"target"=> array("self","individual",1),
"support"	=> "1",
"UpSTR"	=> "30",
); break;
		case "3111":
$skill	= array(
"name"	=> "超限",
"img"	=> "skill_059z.png",
"exp"	=> "自己强化·弱",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("self","individual",1),
"support"	=> "1",
"UpSTR"	=> "80",
"DownMAXHP"	=> "20",
); break;
		case "3112":
$skill	= array(
"name"	=> "防御",
"img"	=> "skill_059y.png",
"exp"	=> "自己强化·弱·前卫化",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("self","individual",1),
"support"	=> "1",
"UpDEF"=> "20",
"DownSTR"=> "20",
"move"	=> "front",
); break;
		case "3113":
$skill	= array(
"name"	=> "狂暴化",
"img"	=> "skill_058z.png",
"exp"	=> "狂暴化",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "12",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
		case "3120":
$skill	= array(
"name"	=> "急救",
"img"	=> "skill_014.png",
"exp"	=> "自己HP回复",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "1",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
		case "3121":
$skill	= array(
"name"	=> "自我回复",
"img"	=> "skill_062.png",
"exp"	=> "",
"sp"	=> "15",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("self","individual",1),
"support"	=> "1",
); break;
		case "3122":
$skill	= array(
"name"	=> "超回复",
"img"	=> "skill_062y.png",
"exp"	=> "恢复自己损失掉的HP中的60%",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"=> array(0,30),
); break;
		case "3123":
$skill	= array(
"name"	=> "自我持续回复",
"img"	=> "skill_062x.png",
"exp"	=> "HP持续回复力+10%",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "3",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"=> array(0,30),
"HpRegen"	=> 10,
); break;
		case "3130":
$skill	= array(
"name"	=> "咏唱辅助",
"img"	=> "skill_062x.png",
"exp"	=> "咏唱辅助",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "3",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"=> array(0,30),
"HpRegen"	=> 10,
); break;
		case "3135":
$skill	= array(
"name"	=> "圣光盾",
"img"	=> "skill_062x.png",
"exp"	=> "一回合伤害无效化",
"sp"	=> "10",
"type"	=> "0",
"learn"	=> "3",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"=> array(0,30),
); break;
//-----------------------------------------------// 3200
		case "3200":
$skill	= array(
"name"	=> "勇气",
"img"	=> "skill_044.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"UpSTR"	=> "30",
); break;
						// 3205
		case "3205":
$skill	= array(
"name"	=> "害怕",
"img"	=> "skill_048.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"DownSTR"	=> "40",
); break;
						// 3210
		case "3210":
$skill	= array(
"name"	=> "魅力",
"img"	=> "skill_046.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"UpINT"	=> "30",
); break;
						// 3215
		case "3215":
$skill	= array(
"name"	=> "破坏智力",
"img"	=> "skill_050.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"DownINT"	=> "40",
); break;
						// 3220
		case "3220":
$skill	= array(
"name"	=> "防御地带",
"img"	=> "skill_045.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"UpDEF"	=> "10",
); break;
		case "3221":
$skill	= array(
"name"	=> "防护+",
"img"	=> "skill_045.png",
"sp"	=> "90",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(90,0),
"UpDEF"	=> "15",
); break;
		case "3222":
$skill	= array(
"name"	=> "防护Q",
"img"	=> "skill_045.png",
"sp"	=> "70",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,0),
"UpDEF"	=> "5",
); break;
						// 3230
		case "3230":
$skill	= array(
"name"	=> "力量地带",
"img"	=> "skill_070.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"UpMDEF"	=> "10",
); break;
		case "3231":
$skill	= array(
"name"	=> "力量地带[自我]",
"img"	=> "skill_070.png",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"	=> array(30,0),
"UpMDEF"	=> "30",
); break;
						// 3235
		case "3235":
$skill	= array(
"name"	=> "抗性降低",
"img"	=> "skill_071.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("enemy","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"DownMDEF"	=> "10",
); break;
//---------------------------- 3250
		case "3250":
$skill	= array(
"name"	=> "力量辅助",
"img"	=> "skill_044.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"PlusSTR"	=> 30,
); break;
		case "3255":
$skill	= array(
"name"	=> "魔法辅助",
"img"	=> "skill_046.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"PlusINT"	=> 30,
); break;
		case "3265":
$skill	= array(
"name"	=> "速度辅助",
"img"	=> "skill_015.png",
"sp"	=> "60",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(30,0),
"PlusSPD"	=> 20,
); break;
//------------------------------------------------// 3300 - 召唤物强化系
		case "3300":
$skill	= array(
"name"	=> "召唤物强化",
"img"	=> "we_other007.png",
"exp"	=> "召唤物强化",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpSTR"	=> "80",
"limit"=> array("鞭"=>true,),
); break;
		case "3301":
$skill	= array(
"name"	=> "召唤物智力强化",
"img"	=> "we_other007.png",
"exp"	=> "召唤物强化",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpINT"	=> "80",
"limit"=> array("鞭"=>true,),
); break;
		case "3302":
$skill	= array(
"name"	=> "召唤物速度强化",
"img"	=> "we_other007.png",
"exp"	=> "召唤物强化",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpSPD"	=> "50",
"limit"=> array("鞭"=>true,),
); break;
		case "3303":
$skill	= array(
"name"	=> "召唤物防御强化",
"img"	=> "we_other007.png",
"exp"	=> "召唤物强化",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "4",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpDEF"	=> "15",
"UpMDEF"	=> "15",
"limit"=> array("鞭"=>true,),
); break;
		case "3304":
$skill	= array(
"name"	=> "召唤物整体强化",
"img"	=> "we_other007z.png",
"exp"	=> "召唤物强化",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpSTR"	=> "150",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
		case "3305":
$skill	= array(
"name"	=> "愈合",
"img"	=> "we_other007z.png",
"exp"	=> "召唤物强化",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpINT"	=> "150",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
		case "3306":
$skill	= array(
"name"	=> "敏捷",
"img"	=> "we_other007z.png",
"exp"	=> "召唤物强化",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpSPD"	=> "100",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
		case "3307":
$skill	= array(
"name"	=> "增强",
"img"	=> "we_other007z.png",
"exp"	=> "召唤物强化",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,50),
"UpDEF"	=> "30",
"UpMDEF"	=> "30",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
		case "3308":
$skill	= array(
"name"	=> "全力支持",
"img"	=> "we_other007z.png",
"exp"	=> "召唤物强化",
"sp"	=> "200",
"type"	=> "0",
"learn"	=> "8",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,150),
"UpSTR"	=> "100",
"UpINT"	=> "100",
"UpSPD"	=> "100",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
		case "3310":
$skill	= array(
"name"	=> "野兽禁止",
"img"	=> "we_other007x.png",
"exp"	=> "召唤物弱化",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "6",
"target"=> array("enemy","individual",1),
"support"	=> "1",
"charge"	=> array(0,50),
"DownSTR"	=> "50",
"DownINT"	=> "50",
"DownSPD"	=> "50",
"DownDEF"	=> "20",
"DownMDEF"	=> "20",
"priority"	=> "Summon",
"limit"=> array("鞭"=>true,),
); break;
//----------------------------------------- 3400 持续回复系
		case "3400":
$skill	= array(
"name"	=> "持续回复",
"img"	=> "skill_062x.png",
"exp"	=> "HP持续回复+5%",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "8",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(10,0),
"HpRegen"	=> 5,
); break;
		case "3401":
$skill	= array(
"name"	=> "魔力持续回复",
"img"	=> "skill_062x.png",
"exp"	=> "SP持续回复+5%",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"	=> array(10,0),
"SpRegen"	=> 5,
); break;
//----------------------------------------- 3410 魔法阵を描く系
		case "3410":
$skill	= array(
"name"	=> "魔法阵",
"img"	=> "ms_01.png",
"exp"	=> "魔法阵+1",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"	=> array(0,0),
"MagicCircleAdd"	=> 1,
); break;
		case "3411":
$skill	= array(
"name"	=> "双重魔法阵",
"img"	=> "ms_01.png",
"exp"	=> "魔法阵+2",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "6",
"target"=> array("self","individual",1),
"charge"	=> array(60,0),
"MagicCircleAdd"	=> 2,
); break;
		case "3415":
$skill	= array(
"name"	=> "魔法阵",
"img"	=> "ms_01.png",
"exp"	=> "魔法阵+1",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"	=> array(30,0),
"MagicCircleAdd"	=> 1,
); break;
//----------------------------------------- 3420 魔法阵を消す系
		case "3420":
$skill	= array(
"name"	=> "魔法阵消除",
"img"	=> "ms_02.png",
"exp"	=> "对手魔法阵-1",
"sp"	=> "150",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"	=> array(30,0),
"MagicCircleDeleteEnemy"	=> 1,
); break;
		case "3421"://消費大
$skill	= array(
"name"	=> "魔法阵消除",
"img"	=> "ms_02.png",
"exp"	=> "对手魔法阵-1",
"sp"	=> "240",
"type"	=> "1",
"learn"	=> "4",
"target"=> array("self","individual",1),
"charge"	=> array(40,0),
"MagicCircleDeleteEnemy"	=> 1,
); break;
//----------------------------------------- 3900 テストに便利な技
		case "3900":
$skill	= array(
"name"	=> "中毒",
"img"	=> "acce_003c.png",
"exp"	=> "自己毒化",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("self","individual",1),
); break;
		case "3901":
$skill	= array(
"name"	=> "即死",
"img"	=> "acce_003c.png",
"exp"	=> "死亡",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "0",
"target"=> array("self","individual",1),
); break;
//////////////////////////////////////////////////
		case "4000":
$skill	= array(
"name"	=> "复原",
"img"	=> "inst_002.png",
"exp"	=> "队列修正",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "5",
"target"=> array("friend","all",1),
"support"	=> "1",
); break;
/*----------------------------------------------*
*   5000 - 5999 EnemySkills                     *
*-----------------------------------------------*/
		case "4999":
$skill	= array(
"name"	=> "---- 5000 ----------",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
); break;
		case "5000":
$skill	= array(
"name"	=> "地颤",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "70",
"charge"=> array(0,20),
); break;
		case "5001":
$skill	= array(
"name"	=> "超音波",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"invalid"	=> "1",
"pow"	=> "50",
"charge"=> array(0,0),
"delay"	=> "20",
); break;
		case "5002":
$skill	= array(
"name"	=> "吸血",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "100",
"invalid"	=> "1",
"charge"=> array(0,0),
); break;
		case "5003":
$skill	= array(
"name"	=> "毒牙",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "200",
"charge"=> array(0,0),
"poison"=> "100",
); break;
		case "5004":
$skill	= array(
"name"	=> "猛毒",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "200",
"invalid"	=> "1",
"charge"=> array(0,0),
"poison"=> "100",
); break;
		case "5005":
$skill	= array(
"name"	=> "防御",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "20",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"=> array(0,0),
"UpDEF"	=> "10",
"UpMDEF"=> "10",
); break;
		case "5006":
$skill	= array(
"name"	=> "突击!!!",
"img"	=> "skill_066.png",
"exp"	=> "突击命令",
"exp"	=> "突击命令",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("friend","all",1),
"support"	=> "1",
"charge"=> array(0,30),
"UpSTR"	=> "50",
); break;
		case "5007":
$skill	= array(
"name"	=> "治疗",// うさぎ 他 専用
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "5",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","individual",1),
"pow"	=> "200",
"support"	=> "1",
"priority"	=> "LowHpRate",
"charge"=> array(0,0),
); break;
		case "5008":
$skill	= array(
"name"	=> "擒咬",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "130",
); break;
		case "5009":
$skill	= array(
"name"	=> "爪击",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "200",
); break;
		case "5010":
$skill	= array(
"name"	=> "咬",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "90",
"pierce"=> true,
); break;
		case "5011":
$skill	= array(
"name"	=> "熊摔",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "200",
"knockback"	=> "100",
); break;
		case "5012":
$skill	= array(
"name"	=> "掷石",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "120",
"invalid"	=> "1",
); break;
		case "5013":
$skill	= array(
"name"	=> "空袭",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "180",
"invalid"	=> "1",
); break;
		case "5014":
$skill	= array(
"name"	=> "多重爪击",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","multi",3),
"pow"	=> "70",
"pierce"=> true,
); break;
		case "5015":
$skill	= array(
"name"	=> "雪暴",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "30",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "100",
"invalid"	=> "1",
"DownSPD"	=> "10",
); break;
		case "5016":
$skill	= array(
"name"	=> "飞行",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "10",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","all",1),
"charge"	=> array(10,0),
"support"	=> "1",
"UpSPD"	=> "20",
); break;
		case "5017":
$skill	= array(
"name"	=> "幸运",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","all",1),
"charge"	=> array(0,0),
"support"	=> "1",
"UpSTR"	=> "30",
"UpINT"	=> "30",
"UpDEX"	=> "30",
"UpSPD"	=> "30",
); break;
		case "5018":
$skill	= array(
"name"	=> "火之气息",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "120",
"invalid"	=> "1",
); break;
		case "5019":
$skill	= array(
"name"	=> "砍翻",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "300",
); break;
		case "5020":
$skill	= array(
"name"	=> "愤怒火焰",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "300",
); break;
		case "5021":
$skill	= array(
"name"	=> "水波",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "200",
"knockback"	=> "100",
); break;
		case "5022":
$skill	= array(
"name"	=> "命运女神",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","all",1),
"support"	=> "1",
"pow"	=> "200",
"UpSTR"	=> "100",
"UpINT"	=> "100",
"UpDEX"	=> "100",
"UpSPD"	=> "100",
"UpDEF"	=> "30",
"UpMDEF"	=> "30",
); break;
		case "5023":
$skill	= array(
"name"	=> "厄运",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "500",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"DownMAXHP"	=> "70",
"invalid"	=> "1",
); break;
		case "5024":
$skill	= array(
"name"	=> "惩罚者",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "500",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "500",
); break;
		case "5025":
$skill	= array(
"name"	=> "圣光风暴",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "150",
"invalid"	=> "1",
); break;
		case "5026":
$skill	= array(
"name"	=> "销毁",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "250",
); break;
		case "5027":
$skill	= array(
"name"	=> "涡流",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "150",
"DownSPD"	=> "20",
); break;
		case "5028":
$skill	= array(
"name"	=> "暗之光",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","multi",3),
"pow"	=> "150",
"invalid"	=> "1",
); break;
		case "5029":
$skill	= array(
"name"	=> "雷神之锤",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"pow"	=> "600",
"invalid"	=> "1",
); break;
		case "5030":
$skill	= array(
"name"	=> "灵魂复苏",
"img"	=> "skill_008.png",
"exp"	=> "复苏",
"sp"	=> "400",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","multi",2),
"support"	=> "1",
"charge"	=> array(0,0),
"pow"		=> "400",
"priority"	=> "Dead",
); break;
		case "5031":
$skill	= array(
"name"	=> "锤击",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"charge"	=> array(0,30),
"pow"		=> "220",
); break;
		case "5032":
$skill	= array(
"name"	=> "地面攻击",
"img"	=> "skill_066.png",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"charge"	=> array(0,50),
"pow"	=> "50",
"delay"	=> "30",
); break;
		case "5033":
$skill	= array(
"name"	=> "武器锻造",
"img"	=> "skill_066.png",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("friend","all",1),
"support"=> true,
"charge"	=> array(0,100),
"UpATK"	=> "50",
); break;
		case "5034":
$skill	= array(
"name"	=> "石像鬼召唤",
"img"	=> "skill_066.png",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"	=> array(0,100),
"summon"	=> array(1026),
); break;
		case "5035":
$skill	= array(
"name"	=> "火蛇",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"charge"	=> array(50,0),
"pow"	=> "80",
); break;
		case "5036":
$skill	= array(
"name"	=> "凝视",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"delay"	=> "30",
); break;
		case "5037":
$skill	= array(
"name"	=> "眼射线",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"invalid"	=> true,
"pow"	=> "200",
"DownSTR"	=> "30",
"DownDEF"	=> "30",
); break;
		case "5038":
$skill	= array(
"name"	=> "暗之气息",
"img"	=> "skill_066.png",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"pow"	=> "150",
"DownINT"	=> "20",
"charge"	=> array(0,50),
); break;
		case "5039":
$skill	= array(
"name"	=> "毒气",
"img"	=> "skill_066.png",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"poison"	=> "100",
); break;
		case "5040":
$skill	= array(
"name"	=> "黑暗圣光",
"img"	=> "skill_066.png",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","multi",6),
"invalid"	=> true,
"pow"	=> "150",
"charge"	=> array(70,0),
); break;
		case "5041":
$skill	= array(
"name"	=> "黑暗迷雾",
"img"	=> "skill_066.png",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"invalid"	=> true,
"charge"	=> array(90,0),
"DownMDEF"	=> "70",
); break;
		case "5042":
$skill	= array(
"name"	=> "雪球",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","multi",2),
"invalid"	=> true,
"pow"	=> "70",
); break;
		case "5043":
$skill	= array(
"name"	=> "大雪球",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","multi",4),
"invalid"	=> true,
"pow"	=> "50",
); break;
		case "5044":
$skill	= array(
"name"	=> "滑雪",
"img"	=> "skill_066.png",
"sp"	=> "70",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "100",
"DownSPD"=> "15",
); break;
		case "5045":
$skill	= array(
"name"	=> "冰之气息",
"img"	=> "skill_066.png",
"sp"	=> "40",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "80",
"DownDEF"=> "10",
); break;
		case "5046":
$skill	= array(
"name"	=> "冰装甲",
"img"	=> "skill_066.png",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","all",1),
"support"	=> true,
"UpDEF"	=> "10",
"UpMDEF"=> "15",
"charge"=> array(50,0),
); break;
		case "5047":
$skill	= array(
"name"	=> "冰柱",
"img"	=> "skill_066.png",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","multi",3),
"pow"	=> "100",
"DownDEF"	=> "10",
"charge"=> array(30,0),
); break;
		case "5048":
$skill	= array(
"name"	=> "诅咒咆哮",
"img"	=> "skill_066.png",
"sp"	=> "120",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"DownSTR"	=> "30",
"charge"=> array(0,50),
); break;
		case "5049":
$skill	= array(
"name"	=> "欢呼",
"img"	=> "skill_066.png",
"sp"	=> "60",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("friend","all",1),
"UpSTR"	=> "40",
"UpINT"	=> "40",
); break;
		case "5050":
$skill	= array(
"name"	=> "冰重击",
"img"	=> "skill_066.png",
"sp"	=> "100",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","multi",3),
"pow"	=> "200",
"DownDEF"	=> "20",
"charge"	=> array(40,0),
); break;
		case "5051":
$skill	= array(
"name"	=> "雪暴",
"img"	=> "skill_066.png",
"sp"	=> "80",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"charge"	=> array(50,0),
"delay"	=> "70",
); break;
		case "5052":
$skill	= array(
"name"	=> "即爆炸弹",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"DownMAXHP"	=> "50",
"invalid"	=> true,
); break;
		case "5053":
$skill	= array(
"name"	=> "冰墙",
"img"	=> "skill_066.png",
"sp"	=> "300",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("friend","all",1),
"charge"	=> array(10,0),
"UpDEF"	=> "20",
"UpMDEF"	=> "70",
); break;
		case "5054":
$skill	= array(
"name"	=> "绝对零度",
"img"	=> "skill_066.png",
"sp"	=> "200",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("all","all",1),
"charge"=> array(30,0),
"pow"	=> "250",
); break;
		case "5055":
$skill	= array(
"name"	=> "辐射加热",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"pow"	=> "400",
"DownDEF"	=> "20",
); break;
		case "5056":
$skill	= array(
"name"	=> "咬",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"charge"=> array(0,0),
"pow"	=> "340",
); break;
		case "5057":
$skill	= array(
"name"	=> "爪击",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",2),
"charge"=> array(0,0),
"pow"	=> "100",
"pierce"	=> true,
"charge"	=> array(0,70),
); break;
		case "5058":
$skill	= array(
"name"	=> "嚎叫",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"charge"=> array(0,0),
"DownSTR"	=> "30",
"charge"	=> array(0,40),
); break;
		case "5059":
$skill	= array(
"name"	=> "掠夺",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"charge"=> array(0,0),
"DownDEF"=> 40,
"DownATK"=> 40,
); break;
		case "5060":
$skill	= array(
"name"	=> "夺取装甲",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"charge"=> array(0,30),
"DownDEF"=> 40,
//"DownATK"=> 40,
); break;
		case "5061":
$skill	= array(
"name"	=> "强化掠夺",
"img"	=> "skill_066.png",
"sp"	=> "100",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"charge"=> array(0,70),
"DownATK"=> 70,
"DownMATK"=> 70,
); break;
		case "5062":
$skill	= array(
"name"	=> "匕首暴徒",
"img"	=> "we_sword001z.png",
"sp"	=> "130",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","multi",8),
"pow"	=> "100",
"charge"=> array(0,70),
"invalid"	=> "1",
); break;
		case "5063":
$skill	= array(
"name"	=> "清醒",
"img"	=> "skill_008.png",
"exp"	=> "复苏",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","individual",1),
"support"	=> "1",
"charge"	=> array(0,0),
"pow"		=> "10",
"priority"	=> "Dead",
); break;
		case "5064":
$skill	= array(
"name"	=> "香蕉火箭",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","individual",1),
"charge"	=> array(0,30),
"pow"		=> "300",
); break;
		case "5065":
$skill	= array(
"name"	=> "香蕉射击",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("enemy","multi",3),
"invalid"	=> true,
"charge"	=> array(0,0),
"pow"		=> "70",
); break;
		case "5066":
$skill	= array(
"name"	=> "香蕉回复",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("self","individual",1),
"support"	=> "1",
"charge"	=> array(0,0),
"pow"		=> "250",
"CurePoison"	=> true,
"SpRecoveryRate"	=> 4,
"support"	=> "1",
); break;
		case "5067":
$skill	= array(
"name"	=> "香蕉防护",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "50",
"type"	=> "1",
"learn"	=> "10",
"target"=> array("friend","all",1),
"charge"	=> array(0,0),
"support"	=> "1",
); break;
		case "5068":
$skill	= array(
"name"	=> "召唤奴隶",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,50),
"summon"	=> array(1100),
); break;
		case "5069":
$skill	= array(
"name"	=> "召唤奴隶",
"img"	=> "banana.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,50),
"summon"	=> array(1101),
); break;
		case "5070":
$skill	= array(
"name"	=> "召唤奇美拉",
"img"	=> "skill_029.png",
"exp"	=> "合成兽召唤",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> "5013",
); break;
		case "5071":
$skill	= array(
"name"	=> "召唤雪男",
"img"	=> "skill_029.png",
"exp"	=> "合成兽召唤",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> "5014",
); break;
		case "5072":
$skill	= array(
"name"	=> "召唤野猪",
"img"	=> "skill_029.png",
"exp"	=> "合成兽召唤",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> "5014",
); break;
		case "5073":
$skill	= array(
"name"	=> "召唤狮子",
"img"	=> "skill_029.png",
"exp"	=> "合成兽召唤",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "20",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> "5011",
); break;
		case "5799":
$skill	= array(
"name"	=> "----5799--------",
"img"	=> "skill_066.png",
"sp"	=> "0",
"type"	=> "1",
"learn"	=> "99",
"target"=> array("enemy","all",1),
"pow"	=> "0",
); break;
						// 敵専用召還狼技
		case "5800":
$skill	= array(
"name"	=> "群召唤",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"=> "1012",
); break;
		case "5801":
$skill	= array(
"name"	=> "召唤奴隶",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,0),
"summon"	=> array(1012,1012,1012,1012,1012),
); break;
		case "5802":
$skill	= array(
"name"	=> "死者复生",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "80",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(30,0),
"summon"	=> array(5003),
); break;
		case "5803":
$skill	= array(
"name"	=> "生育",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"charge"=> array(0,150),
); break;
		case "5804":
$skill	= array(
"name"	=> "召唤奴隶",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "200",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"summon"	=> array(1034,1034),
); break;
		case "5805":
$skill	= array(
"name"	=> "嚎叫",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "40",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"summon"	=> array(1038),
"charge"	=> array(0,30),
); break;
		case "5806":
$skill	= array(
"name"	=> "雪橇鹿",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"summon"	=> array(1047),
); break;
		case "5807":
$skill	= array(
"name"	=> "召唤猎手之狼",
"img"	=> "skill_066.png",
"exp"	=> "",
"sp"	=> "0",
"type"	=> "0",
"learn"	=> "99",
"target"=> array("self","individual",1),
"summon"	=> array(1091),
); break;
/*----------------------------------------------*
*   7000 - 7999 PassiveSkills（被动技能）        *
*-----------------------------------------------*
	Passive 設定項目
"passive"	=> 1,//被动技能
"p_maxhp"	=> "30",//最大HP+30
"p_maxsp"	=> "10",//最大SP+10
"p_str"	=> "1",//最大str+
"p_int"	=> "2",//最大int+
"p_dex"	=> "3",//最大dex+
"p_spd"	=> "4",//最大spd+
"p_luk"	=> "5",//最大luk+
*-----------------------------------------------*/
		case "7000":
$skill	= array(
"name"	=> "生命生化",
"img"	=> "acce_003c.png",
"exp"	=> "HP+30",
"learn"	=> "2",
"passive"	=> 1,
"p_maxhp"	=> "30",
); break;
		case "7001":
$skill	= array(
"name"	=> "生命洪流",
"img"	=> "acce_003c.png",
"exp"	=> "HP+80",
"learn"	=> "9",
"passive"	=> 1,
"p_maxhp"	=> "80",
); break;
		case "7002":
$skill	= array(
"name"	=> "生命超越",
"img"	=> "acce_003c.png",
"exp"	=> "HP+200",
"learn"	=> "21",
"passive"	=> 1,
"P_MAXHP"	=> "200",
); break;
		case "7003":
$skill	= array(
"name"	=> "生命辅助1",
"img"	=> "acce_003c.png",
"exp"	=> "HP+30",
"learn"	=> "4",
"passive"	=> 1,
"P_MAXHP"	=> "30",
); break;
		case "7004":
$skill	= array(
"name"	=> "生命辅助2",
"img"	=> "acce_003c.png",
"exp"	=> "HP+70",
"learn"	=> "9",
"passive"	=> 1,
"P_MAXHP"	=> "70",
); break;
		case "7005":
$skill	= array(
"name"	=> "生命辅助3",
"img"	=> "acce_003c.png",
"exp"	=> "HP+150",
"learn"	=> "21",
"passive"	=> 1,
"P_MAXHP"	=> "150",
); break;
							// HealBonus
		case "7005":
$skill	= array(
"name"	=> "生命辅助3",
"img"	=> "acce_003c.png",
"exp"	=> "HP+150",
"learn"	=> "21",
"passive"	=> 1,
"P_MAXHP"	=> "150",
); break;
//----------------------------------------------//
// 9999                                         //
//----------------------------------------------//
		case "9000":
$skill	= array(
"name"	=> "* 继续思考",
"name2"	=> "* 考虑一下（？）",
"exp"	=> "多重判定，引用 swto 的话就是and",
"img"	=> "skill_040.png",
"learn"	=> "4",
"type"	=> false,
"pow"	=> false,
); break;
//----------------------------------------------//
	}
	if(!$skill)
		return false;
	$skill	+= array("no"=>"$no");
	return $skill;
}
?>