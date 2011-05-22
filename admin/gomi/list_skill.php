<?php
include("./data.skill.php");

//print("<table><tbody>");

for($no=1000; $no<10000; $no++) {
	$skill = LoadSkillData($no);
	if(!$skill) continue;

//	print("<tr><td>");
	print("case $no: // ".$skill[name]."<br>");
//	print("// target=({$skill[target][0]},{$skill[target][1]},{$skill[target][2]})<br>");
//	print("// pow={$skill[pow]}<br>");
//	print("break;<br>");
//	print("</td><td>");
	$list[]	= $no;
}
foreach($list as $var) {
	print("<>$var");
}
//print("</tbody></table>");
?>
