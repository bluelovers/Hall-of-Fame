<table><tbody>
<?php 
for($i=0; $i<5000; $i++) {
	print("<tr><td>");
	//$no	= $i-(($i*$i)/1000);
	//$no	= ceil(pow($i,9/17)*10);
	$no	= sqrt($i)*10;
	//if($i < 100)
	//	$no -= ceil($no * $i/100);
	//if($i < 100)
	//	$no	= $no * ($no/100);
	//if($no < 100)
	//	$no	*= (101 - (100-$no))/100;
	//$no	= $i - $i * ((sqrt($i)*sqrt($i))/($i*$i));
	//$no	= $i - $i*(sqrt($i*2)/100);
	//$no	= pow($i,2) - $i*sqrt($i);
	echo $i.":".$no."<br>";
	print("</td><td>");
	$wid=round($no);
	print("<img src=\"./bar.gif\" width=\"$no\" height=\"7\"");
	print("</td></tr>");
}
?>
</tbody></table>