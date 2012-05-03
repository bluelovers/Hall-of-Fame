<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>skl_list2</title>
<style type="text/css">
<!--
*{
	padding	: 0;
	margin	: 0;
	line-height	: 140%;
	font-family	: Osaka,Verdana,"ＭＳ Ｐゴシック";
	overflow:inherit;
}
body{
  margin:30px;
  background	: #98a0a5/*#bfbfbf*/;
  color	: #bdc8d7;
}
td{
  white-space: nowrap;
  background-color : #10151b;
  text-align:center;
  padding:4px;
}
.a{
  background-color : #333333;
}
-->
</style></head>
<body>
<?php
include("./data.skill.php");

$det	= '<tr><td class="a">No</td>
<td class="a">Name</td>
<td class="a">IMG</td>
<td class="a">SP</td>
<td class="a">type</td>
<td class="a">lrn</td>
<td class="a">Target</td>
<td class="a">pow</td>
<td class="a">hit</td>
<td class="a">invalid</td>
<td class="a">support</td>
<td class="a">priority</td>
<td class="a">charge</td>
<td class="a">exp</td></tr>'."\n";
$img_f	= "../image/icon/";

print('<table border="0" cellspacing="1"><tbody>');
print($det);
$detcount=0;
for($no=1000; $no<9999; $no++) {
	$skill = LoadSkillData($no);
	if(!$skill) continue;

	$detcount++;
	if($detcount%10==0) print($det);

	print("<tr>");
	print("<td>{$no}</td>");//no
	print("<td style=\"font-weight:bold\">{$skill[name]}</td>");//name
	$img	= '<img src="'.$img_f.$skill["img"].'">';
	print("<td>$img</td>");//img
	print("<td>{$skill[sp]}".($skill["sacrifice"]?"<span style=\"color:red\">s:{$skill[sacrifice]}</span>":"")."</td>");//sp
	print("<td>{$skill[type]}</td>");//type
	print("<td>{$skill[learn]}</td>");//learn
	print("<td>{$skill[target][0]},{$skill[target][1]},{$skill[target][2]}</td>");//target
	if($skill[summon]) {
		print("<td>召喚:{$skill[summon]}".($skill[quick]?"(Q)":"")."</td>");
	} else {
		print("<td>{$skill[pow]}% x {$skill[target][2]} = ".($skill[pow]*$skill[target][2])."%</td>");//pow
	}
	print("<td>{$skill[hit]}</td>");//hit
	print("<td>{$skill[invalid]}</td>");//invalid
	print("<td>{$skill[support]}</td>");//
	print("<td>{$skill[priority]}</td>");//
	print("<td>{$skill[charge][0]}:{$skill[charge][1]}</td>");//charge
	print("<td>{$skill[exp]}</td>");//
	
	print("</tr>\n");
}
print($det);
print("</tbody></table>");
?>
</body>
</html>
