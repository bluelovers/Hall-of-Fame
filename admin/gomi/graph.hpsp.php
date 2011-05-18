<table><tbody>
<?php
$coe	= 3;
$str	= 1;
$lv		= 1;
//for($lv=1; $lv<51; $lv++) {
for($str=1; $str<251; $str++) {
	print("<tr><td>\n");
	//print("$lv");
	print("$str");
	print("</td><td>\n");
	$rstr	= 251 - $str;
	$hp	= 100 * $coe * (1 + ($lv - 1)/49) * (1 + ((250*250) - $rstr*$rstr)/(250*250));
	print("$hp");
	print("</td></tr>\n");
}
?>
</tbody></table>