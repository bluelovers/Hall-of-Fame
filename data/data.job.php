<?php 
/*
	職業性別名
	職業的圖像
	可以裝備物
	"coe"	=> array(HP係數 ,SP係數),
	"change"	=> array(可以轉的職),
*/
function LoadJobData($no) {
	switch($no) {
		case "100":
$job	= array(
"name_male"		=> "戰士",
"name_female"	=> "戰士",
"img_male"		=> "mon_079.gif",
"img_female"	=> "mon_080r.gif",
"equip"			=> array("劍","雙手劍","盾","甲","衣服","長袍","道具"),
"coe"			=> array(3, 0.5),
"change"		=> array(101,102,103),
); break;
		case "101":
$job	= array(
"name_male"		=> "皇家衛士",
"name_female"	=> "皇家衛士",
"img_male"		=> "mon_199r.gif",
"img_female"	=> "mon_234r.gif",
"equip"			=> array("劍","雙手劍","盾","甲","衣服","長袍","道具"),
"coe"			=> array(4, 0.7),
); break;
		case "102":
$job	= array(
"name_male"		=> "狂戰士",
"name_female"	=> "狂戰士",
"img_male"		=> "mon_100r.gif",
"img_female"	=> "mon_012.gif",
"equip"			=> array("劍","雙手劍","盾","衣服","長袍","道具"),
"coe"			=> array(5.0, 0.2),
); break;
		case "103":
$job	= array(
"name_male"		=> "魔女狩",
"name_female"	=> "魔女狩",
"img_male"		=> "mon_150.gif",
"img_female"	=> "mon_234.gif",
"equip"			=> array("劍","匕首","盾","甲","衣服","長袍","道具"),
"coe"			=> array(3.7, 1),
); break;
		case "200":
$job	= array(
"name_male"		=> "巫師",
"name_female"	=> "巫師",
"img_male"		=> "mon_106.gif",
"img_female"	=> "mon_018.gif",
"equip"			=> array("魔杖","杖","書","衣服","長袍","道具"),
"coe"			=> array(1.5, 1),
"change"		=> array(201,202,203),
); break;
		case "201":
$job	= array(
"name_male"		=> "術士",
"name_female"	=> "術士",
"img_male"		=> "mon_196z.gif",
"img_female"	=> "mon_246r.gif",
"equip"			=> array("魔杖","杖","書","衣服","長袍","道具"),
"coe"			=> array(2.1, 2),
); break;
		case "202":
$job	= array(
"name_male"		=> "召喚師",
"name_female"	=> "召喚師",
"img_male"		=> "mon_196y.gif",
"img_female"	=> "mon_246z.gif",
"equip"			=> array("魔杖","杖","書","長袍","道具"),
"coe"			=> array(1.5, 2.5),
); break;
		case "203":
$job	= array(
"name_male"		=> "死靈法師",
"name_female"	=> "死靈法師",
"img_male"		=> "mon_196x.gif",
"img_female"	=> "mon_246y.gif",
"equip"			=> array("魔杖","杖","書","衣服","長袍","道具"),
"coe"			=> array(2.1, 1.5),
); break;
		case "300":
$job	= array(
"name_male"		=> "牧師",
"name_female"	=> "女祭司",
"img_male"		=> "mon_213.gif",
"img_female"	=> "mon_214.gif",
"equip"			=> array("魔杖","書","衣服","長袍","道具"),
"coe"			=> array(2, 0.8),
"change"		=> array(301,302),
); break;
		case "301":
$job	= array(
"name_male"		=> "主教",
"name_female"	=> "主教",
"img_male"		=> "mon_213r.gif",
"img_female"	=> "mon_214r.gif",
"equip"			=> array("魔杖","書","衣服","長袍","道具"),
"coe"			=> array(2.7, 1.4),
); break;
		case "302":
$job	= array(
"name_male"		=> " 德魯伊",
"name_female"	=> " 德魯伊",
"img_male"		=> "mon_213rz.gif",
"img_female"	=> "mon_214rz.gif",
"equip"			=> array("魔杖","書","衣服","長袍","道具"),
"coe"			=> array(2.5, 1.2),
); break;
		case "400":
$job	= array(
"name_male"		=> " 獵人",
"name_female"	=> " 獵人",
"img_male"		=> "mon_219rr.gif",
"img_female"	=> "mon_219r.gif",
"equip"			=> array("弓","衣服","長袍","道具"),
"coe"			=> array(2.2, 0.7),
"change"		=> array(401,402,403),
); break;
		case "401":
$job	= array(
"name_male"		=> "神射手",
"name_female"	=> "神射手",
"img_male"		=> "mon_076z.gif",
"img_female"	=> "mon_042z.gif",
"equip"			=> array("弓","衣服","長袍","道具"),
"coe"			=> array(3.0, 0.8),
); break;
		case "402":
$job	= array(
"name_male"		=> "馴獸師",
"name_female"	=> "馴獸師",
"img_male"		=> "mon_216z.gif",
"img_female"	=> "mon_217z.gif",
"equip"			=> array("弓","鞭","衣服","長袍","道具"),
"coe"			=> array(3.2, 1.0),
); break;
		case "403":
$job	= array(
"name_male"		=> "刺客",
"name_female"	=> "刺客",
"img_male"		=> "mon_216y.gif",
"img_female"	=> "mon_217rz.gif",
"equip"			=> array("匕首","弓","甲","衣服","道具"),
"coe"			=> array(3.6, 0.7),
); break;
	}
	return $job;
}
?>