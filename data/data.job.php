<?
/*
	職の男性,女性名
	職の画像
	装備可能な物
	"coe"	=> array(HP係数 ,SP係数),
	"change"	=> array(転職可能な職),
*/
function LoadJobData($no) {
	switch($no) {
		case "100":
$job	= array(
"name_male"		=> "Warrior",
"name_female"	=> "Warrior",
"img_male"		=> "mon_079.gif",
"img_female"	=> "mon_080r.gif",
"equip"			=> array("Sword","TwoHandSword","Shield","Armor","Cloth","Robe","Item"),
"coe"			=> array(3, 0.5),//3 0.5
"change"		=> array(101,102,103),
); break;
		case "101":
$job	= array(
"name_male"		=> "RoyalGuard",
"name_female"	=> "RoyalGuard",
"img_male"		=> "mon_199r.gif",
"img_female"	=> "mon_234r.gif",
"equip"			=> array("Sword","TwoHandSword","Shield","Armor","Cloth","Robe","Item"),
"coe"			=> array(4, 0.7),
); break;
		case "102":
$job	= array(
"name_male"		=> "Sacrier",
"name_female"	=> "Sacrier",
"img_male"		=> "mon_100r.gif",
"img_female"	=> "mon_012.gif",
"equip"			=> array("Sword","TwoHandSword","Shield","Cloth","Robe","Item"),
"coe"			=> array(5.0, 0.2),
); break;
		case "103":
$job	= array(
"name_male"		=> "WitchHunt",
"name_female"	=> "WitchHunt",
"img_male"		=> "mon_150.gif",
"img_female"	=> "mon_234.gif",
"equip"			=> array("Sword","Dagger","Shield","Armor","Cloth","Robe","Item"),
"coe"			=> array(3.7, 1),
); break;
		case "200":
$job	= array(
"name_male"		=> "Sorcerer",
"name_female"	=> "Sorceress",
"img_male"		=> "mon_106.gif",
"img_female"	=> "mon_018.gif",
"equip"			=> array("Wand","Staff","Book","Cloth","Robe","Item"),
"coe"			=> array(1.5, 1),//1.5 1
"change"		=> array(201,202,203),
); break;
		case "201":
$job	= array(
"name_male"		=> "Warlock",
"name_female"	=> "Warlock",
"img_male"		=> "mon_196z.gif",
"img_female"	=> "mon_246r.gif",
"equip"			=> array("Wand","Staff","Book","Cloth","Robe","Item"),
"coe"			=> array(2.1, 2),//1.5 1
); break;
		case "202":
$job	= array(
"name_male"		=> "Summoner",
"name_female"	=> "Summoner",
"img_male"		=> "mon_196y.gif",
"img_female"	=> "mon_246z.gif",
"equip"			=> array("Wand","Staff","Book","Robe","Item"),
"coe"			=> array(1.5, 2.5),//1.5 1
); break;
		case "203":
$job	= array(
"name_male"		=> "Necromancer",
"name_female"	=> "Necromancer",
"img_male"		=> "mon_196x.gif",
"img_female"	=> "mon_246y.gif",
"equip"			=> array("Wand","Staff","Book","Cloth","Robe","Item"),
"coe"			=> array(2.1, 1.5),//1.5 1
); break;
		case "300":
$job	= array(
"name_male"		=> "Priest",
"name_female"	=> "Priestess",
"img_male"		=> "mon_213.gif",
"img_female"	=> "mon_214.gif",
"equip"			=> array("Wand","Book","Cloth","Robe","Item"),
"coe"			=> array(2, 0.8),
"change"		=> array(301,302),
); break;
		case "301":
$job	= array(
"name_male"		=> "Bishop",
"name_female"	=> "Bishop",
"img_male"		=> "mon_213r.gif",
"img_female"	=> "mon_214r.gif",
"equip"			=> array("Wand","Book","Cloth","Robe","Item"),
"coe"			=> array(2.7, 1.4),
); break;
		case "302":
$job	= array(
"name_male"		=> "Druid",
"name_female"	=> "Druid",
"img_male"		=> "mon_213rz.gif",
"img_female"	=> "mon_214rz.gif",
"equip"			=> array("Wand","Book","Cloth","Robe","Item"),
"coe"			=> array(2.5, 1.2),
); break;
		case "400":
$job	= array(
"name_male"		=> "Hunter",
"name_female"	=> "Hunter",
"img_male"		=> "mon_219rr.gif",
"img_female"	=> "mon_219r.gif",
"equip"			=> array("Bow","Cloth","Robe","Item"),
"coe"			=> array(2.2, 0.7),
"change"		=> array(401,402,403),
); break;
		case "401":
$job	= array(
"name_male"		=> "Sniper",
"name_female"	=> "Sniper",
"img_male"		=> "mon_076z.gif",
"img_female"	=> "mon_042z.gif",
"equip"			=> array("Bow","Cloth","Robe","Item"),
"coe"			=> array(3.0, 0.8),
); break;
		case "402":
$job	= array(
"name_male"		=> "BeastTamer",
"name_female"	=> "BeastTamer",
"img_male"		=> "mon_216z.gif",
"img_female"	=> "mon_217z.gif",
"equip"			=> array("Bow","Whip","Cloth","Robe","Item"),
"coe"			=> array(3.2, 1.0),
); break;
		case "403":
$job	= array(
"name_male"		=> "Murderer",
"name_female"	=> "Murderer",
"img_male"		=> "mon_216y.gif",
"img_female"	=> "mon_217rz.gif",
"equip"			=> array("Dagger","Bow","Armor","Cloth","Item"),
"coe"			=> array(3.6, 0.7),
); break;
	}

	return $job;
}
?>