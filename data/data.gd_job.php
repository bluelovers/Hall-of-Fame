<?php
include_once(DATA_JOB);
?>
<div style="margin:15px">
<h4>職業(Job)</h4>
<ul>
<li><a href="#100">戰士</a>
<ul>
<li><a href="#101">皇家衛士</a></li>
<li><a href="#102">狂戰士</a></li>
<li><a href="#103">魔女狩</a></li>
</ul>
</li>
<li><a href="#200">巫師</a>
<ul>
<li><a href="#201">術士</a></li>
<li><a href="#202">召喚師</a></li>
<li><a href="#203">死靈法師</a></li>
</ul>
</li>
<li><a href="#300">牧師</a>
<ul>
<li><a href="#301">主教</a></li>
<li><a href="#302">德魯伊</a></li>
</ul>
</li>
<li><a href="#400">獵人</a>
<ul>
<li><a href="#401">狙擊手</a></li>
<li><a href="#402">馴獸師</a></li>
<li><a href="#403">刺客</a></li>
</ul>
</li>
</ul>
<h4>Variety</h4>
<table cellspacing="0" style="width:740px">
<?php
$job	= array(
// ここでしか澀妥痰いので 喀デ【タには今きません。
100 => "戰士系基本職業<br />攻防力強。",
101 => "戰士系高級職業<br />更高級的攻防。",
102 => "戰士系高級職業<br />專職負責攻擊的職業。<br />以犧牲自己體力的方式釋放強力技能。<br /><a href=\"?manual#sacrier\">Sacrier的攻擊</a>",
103 => "戰士系高級職業<br />奪取對手的魔力，非正統意義上的戰士。",
200 => "法師系基本職業。<br />攻擊力弱但可使用強力的魔法。",
201 => "法師系高級職業。<br />可以使用更加強大的魔法。",
202 => "法師系高級職業。<br />可以花費時間來召喚強力的召喚獸。",
203 => "法師系高級職業。<br />降低對手的能力，製作殭屍。<br />使毒。",
300 => "牧師系基本職業。<br />回復我方的HP、SP。",
301 => "牧師系高級職業。<br />提高我方的能力值。",
302 => "牧師系高級職業。<br />具有一些特殊的支援能力。",
400 => "獵人基本職業。<br />擁有著不會被對方前衛影響的攻擊技能。",
401 => "獵人高級職業。<br />可進行強力的攻擊。",
402 => "獵人高級職業。<br />更快的召喚及擅長強化召喚獸。",
403 => "獵人高級職業。<br />善於使用毒的職業。",
);
$JobSkill	= array(
// ここでしか澀妥痰いので 喀デ【タには今きません。
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
	$equip	= "裝備 : ";
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