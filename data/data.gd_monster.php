<?php
/*
	�ɤä����������Ʋ���ɽ������Ƥʤ��Τ�ɬ�פʤ��ľ����
*/
include_once(DATA_MONSTER);
?>
<div style="margin:0 15px">
<h4>��󥹥���</h4>
<table class="align-center" style="width:740px" cellspacing="0">
<?php
$List	= array(
1000	=> array("grass","SP������Ȥ��ϡ���������򤿤ޤˤ��Ƥ������١�"),
1001	=> array("grass","SP������Ȥ��ϡ���������򤿤ޤˤ��Ƥ������١�"),
1002	=> array("grass","����˲����Ф�����򤹤롣"),
1003	=> array("grass","���������ʶ�����"),
1005	=> array("grass","��٥뤬�㤤�ȶ��������롣"),
1009	=> array("grass","HP���⤤��"),
1012	=> array("cave","��֤�Ƥ�ǵ۷칶��򤷤Ƥ��롣"),
1014	=> array("cave","��ˡ�ǹ��⤷�ʤ����ݤ��ˤ�����"),
1017	=> array("cave","ƶ���Υܥ����ݤ��ȱ��˹Ԥ���褦�ˤʤ롣"),
);
$Detail	= "<tr>
<td class=\"td6\">Image</td>
<td class=\"td6\">EXP</td>
<td class=\"td6\">MONEY</td>
<td class=\"td6\">HP</td>
<td class=\"td6\">SP</td>
<td class=\"td6\">STR</td>
<td class=\"td6\">INT</td>
<td class=\"td6\">DEX</td>
<td class=\"td6\">SPD</td>
<td class=\"td6\">LUK</td>
</tr>";
foreach($List as $No => $exp) {
	$monster	= CreateMonster($No);
	$char	= new char($monster);
	print($Detail);
	print("</td><td class=\"td7\">\n");
	//print('<img src="'.IMG_CHAR.$monster["img"].'" />'."\n");
	$char->ShowCharWithLand($exp[0]);
	print("</td><td class=\"td7\">\n");
	print("{$monster[exphold]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[moneyhold]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[maxhp]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[maxsp]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[str]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[int]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[dex]}\n");
	print("</td><td class=\"td7\">\n");
	print("{$monster[spd]}\n");
	print("</td><td class=\"td8\">\n");
	print("{$monster[luk]}\n");
	print("</td></tr>\n");
	print("<tr><td class=\"td7\" colspan=\"11\">\n");
	print("$exp[1]");
	print("</td></tr>\n");
}
?>
</table>
</div>