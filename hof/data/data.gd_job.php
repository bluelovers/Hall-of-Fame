<?php
include_once(DATA_JOB);
?>
<div style="margin:15px">
<h4>职业(Job)</h4>
<ul>
<li><a href="#100">战士</a>
<ul>
<li><a href="#101">皇家卫士</a></li>
<li><a href="#102">狂战士</a></li>
<li><a href="#103">魔女狩</a></li>
</ul>
</li>
<li><a href="#200">巫师</a>
<ul>
<li><a href="#201">术士</a></li>
<li><a href="#202">召唤师</a></li>
<li><a href="#203">死灵法师</a></li>
</ul>
</li>
<li><a href="#300">牧师</a>
<ul>
<li><a href="#301">主教</a></li>
<li><a href="#302">德鲁伊</a></li>
</ul>
</li>
<li><a href="#400">猎人</a>
<ul>
<li><a href="#401">狙击手</a></li>
<li><a href="#402">驯兽师</a></li>
<li><a href="#403">刺客</a></li>
</ul>
</li>
</ul>
<h4>Variety</h4>
<table cellspacing="0" style="width:740px">
<?php
$job	= array(
// ここでしか涩妥痰いので 喀デ〖タには今きません。
100 => "战士系基本职业<br />攻防力强。",
101 => "战士系高级职业<br />更高级的攻防。",
102 => "战士系高级职业<br />专职负责攻击的职业。<br />以牺牲自己体力的方式释放强力技能。<br /><a href=\"?manual#sacrier\">Sacrier的攻击</a>",
103 => "战士系高级职业<br />夺取对手的魔力，非正统意义上的战士。",
200 => "法师系基本职业。<br />攻击力弱但可使用强力的魔法。",
201 => "法师系高级职业。<br />可以使用更加强大的魔法。",
202 => "法师系高级职业。<br />可以花费时间来召唤强力的召唤兽。",
203 => "法师系高级职业。<br />降低对手的能力，制作僵尸。<br />使毒。",
300 => "牧师系基本职业。<br />回复我方的HP、SP。",
301 => "牧师系高级职业。<br />提高我方的能力值。",
302 => "牧师系高级职业。<br />具有一些特殊的支援能力。",
400 => "猎人基本职业。<br />拥有着不会被对方前卫影响的攻击技能。",
401 => "猎人高级职业。<br />可进行强力的攻击。",
402 => "猎人高级职业。<br />更快的召唤及擅长强化召唤兽。",
403 => "猎人高级职业。<br />善于使用毒的职业。",
);
$JobSkill	= array(
// ここでしか涩妥痰いので 喀デ〖タには今きません。
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
	$equip	= "装备 : ";
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