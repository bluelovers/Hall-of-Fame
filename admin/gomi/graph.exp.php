<table><tbody>
<?php 
for($i=1; $i<50; $i++) {
	print("<tr><td>");

		switch($i) {
			case 40:	$no	= 30000; break;
			case 41:	$no	= 40000; break;
			case 42:	$no	= 50000; break;
			case 43:	$no	= 60000; break;
			case 44:	$no	= 70000; break;
			case 45:	$no	= 80000; break;
			case 46:	$no	= 100000; break;
			case 47:	$no	= 250000; break;
			case 48:	$no	= 500000; break;
			case 49:	$no	= 999990; break;
			case 50:
			case (50 <= $i):
				$no	= "MAX"; break;
			case(21 < $i):
				$no	= 2*pow($i,3)+100*$i+100;
				$no	-= substr($no,-2);
				$no /= 5;
				break;
			default:
				$no	= pow($i-1,2)/2*100+100;
				$no /= 5;
				break;
		}

	echo $i.":".$no."<br>";
	print("</td><td>");
	$wid=round($no/10);
	print("<img src=\"./bar.gif\" width=\"$wid\" height=\"7\"");
	print("</td></tr>");
}
?>
</tbody></table>