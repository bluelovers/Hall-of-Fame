<?php
include_once(DATA_JOB);
?>
<div style="margin:15px">
<h4>����(Job)</h4>
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
// �����Ǥ���ɬ��̵���Τ� ���ǡ����ˤϽ񤭤ޤ���
100 => "��ηϴ��ܿ���<br />���������Ѥ��ơ�����⤽��������",
101 => "��ηϾ�鿦��<br />�ɸ�⹶�����궯���ʤ롣",
102 => "��ηϾ�鿦��<br />������ò�������Ρ�<br />��ʬ�����Ϥ����˶��Ϥʵ����Ȥ��롣<br /><a href=\"?manual#sacrier\">Sacrier�ι���ˤĤ���</a>",
103 => "��ηϾ�鿦��<br />�������Ϥ�å�ä��ꤹ�롢�����§Ū����Ρ�",
200 => "��ˡ�ϴ��ܿ���<br />�⤿��夤��������ˡ���Ȥ��롣",
201 => "��ˡ�Ͼ�鿦��<br />����˶��Ϥ���ˡ���Ȥ���褦�ˤʤ롣",
202 => "��ˡ�Ͼ�鿦��<br />���֤Ϥ����뤬���Ϥʾ����ä�Ƥ٤롣",
203 => "��ˡ�Ͼ�鿦��<br />����ǽ�Ϥ򲼤����ꡢ����Ӥ��ä������롣<br />�Ǥⰷ���롣",
300 => "�������ܿ���<br />̣����HP,SP�β������Ǥ��롣",
301 => "������鿦��<br />̣����ǽ���ͤ�夲���褦�ˤʤ롣",
302 => "������鿦��<br />�ü�ʻٱ�ǽ�Ϥ���äƤ��롣",
400 => "�ݷϴ��ܿ���<br />�������Ҥ˱ƶ����줺�˹���Ǥ��롣",
401 => "�ݷϾ�鿦��<br />����˶��Ϥʹ��⤬��ǽ��",
402 => "�ݷϾ�鿦��<br />���ᤤ�����Ⱦ����äζ��������ա�",
403 => "�ݷϾ�鿦��<br />�Ǥΰ�����Ĺ�������ȡ�",
);
$JobSkill	= array(
// �����Ǥ���ɬ��̵���Τ� ���ǡ����ˤϽ񤭤ޤ���
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
	$equip	= "���� : ";
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