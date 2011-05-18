<?php
include_once(DATA_JOB);
?>
<div style="margin:15px">
<h4>職業(Job)</h4>
<ul>
<li><a href="#100">Warrior</a>
<ul>
<li><a href="#101">RoyalGuard</a></li>
<li><a href="#102">Sacrier</a></li>
<li><a href="#103">WitchHunt</a></li>
</ul>
</li>
<li><a href="#200">Sorcerer</a>
<ul>
<li><a href="#201">Warlock</a></li>
<li><a href="#202">Summoner</a></li>
<li><a href="#203">Necromancer</a></li>
</ul>
</li>
<li><a href="#300">Priest</a>
<ul>
<li><a href="#301">Bishop</a></li>
<li><a href="#302">Druid</a></li>
</ul>
</li>
<li><a href="#400">Hunter</a>
<ul>
<li><a href="#401">Sniper</a></li>
<li><a href="#402">BeastTamer</a></li>
<li><a href="#403">Murderer</a></li>
</ul>
</li>
</ul>
<h4>Variety</h4>
<table cellspacing="0" style="width:740px">
<?php
$job	= array(
// ここでしか必要無いので 職データには書きません。
100 => "戦士系基本職。<br />そこそこ耐えて、攻撃もそこそこ。",
101 => "戦士系上級職。<br />防御も攻撃も一回り強くなる。",
102 => "戦士系上級職。<br />攻撃に特化した戦士。<br />自分の体力を犠牲に強力な技が使える。<br /><a href=\"?manual#sacrier\">Sacrierの攻撃について</a>",
103 => "戦士系上級職。<br />相手の魔力を奪ったりする、やや変則的な戦士。",
200 => "魔法系基本職。<br />撃たれ弱いが強い魔法が使える。",
201 => "魔法系上級職。<br />さらに強力な魔法が使えるようになる。",
202 => "魔法系上級職。<br />時間はかかるが強力な召喚獣を呼べる。",
203 => "魔法系上級職。<br />相手の能力を下げたり、ゾンビを作ったり出来る。<br />毒も扱える。",
300 => "聖職基本職。<br />味方のHP,SPの回復ができる。",
301 => "聖職上級職。<br />味方の能力値も上げれるようになる。",
302 => "聖職上級職。<br />特殊な支援能力を持っている。",
400 => "弓系基本職。<br />相手の前衛に影響されずに攻撃できる。",
401 => "弓系上級職。<br />さらに強力な攻撃が可能。",
402 => "弓系上級職。<br />素早い召喚と召喚獣の強化が得意。",
403 => "弓系上級職。<br />毒の扱いに長けた職業。",
);
$JobSkill	= array(
// ここでしか必要無いので 職データには書きません。
100 => array(1001,3110,3120),
101 => array(1012,1023,1019),
102 => array(1100,1114,1118),
103 => array(1020,2090,3215),
200 => array(1002,2011,3011),
201 => array(2001,2024,2015),
202 => array(3020,2500,2501),
203 => array(2030,2050,2460),
300 => array(3000,3101,2100),
301 => array(2101,3220,2481),
302 => array(3050,3055,3060),
400 => array(2300,2301,2302),
401 => array(2305,2306,2307),
402 => array(2405,2406,3300),
403 => array(1200,1207,1204),
);
include(DATA_SKILL);
foreach($job as $No => $exp) {
	$flag	= $flag ^ 1;
	$css	= $flag?' class="td6"':' style="padding:3px;"';
	$JobData	= LoadJobData($No);
	print("<tr>\n");
	print('<td'.$css.' valign="top"><a name="#'.$No.'"></a><span class="bold">');
	print($JobData["name_male"]);
	if($JobData["name_male"] !== $JobData["name_female"])
		print("<br />(".$JobData["name_female"].")");
	print('</span></td>'."\n");
	print("<td$css>");
	print('<img src="'.IMG_CHAR.$JobData["img_male"].'" />');
	print('<img src="'.IMG_CHAR.$JobData["img_female"].'" />');
	print("</td>");
	print("<td$css>$exp");
	print("</td>");
	print("<tr><td$css colspan=\"3\"><div style=\"margin-left:30px\">");
	$equip	= "装備 : ";
	foreach($JobData["equip"] as $item){
		$equip	.= $item.", "; 
	}
	print(substr($equip,0,-2));
	print("</div></td></tr>\n");
	print("<tr><td$css colspan=\"3\"><div style=\"padding-left:30px\">\n");
	foreach($JobSkill["$No"] as $SkillNo) {
		$skill	= LoadSkillData($SkillNo);
		ShowSkillDetail($skill);
		print("<br />\n");
	}
	print("</div></td></tr>");
	print("</tr>\n");
}/*
<tr>
<td><span class="bold">Warrior</span></td>
<td><img src="<?=IMG_CHAR?>mon_079.gif" /><img src="<?=IMG_CHAR?>mon_080r.gif" /></td>
</tr>
<tr><td colspan="2"></td></tr>*/
?>
</table>
</div>