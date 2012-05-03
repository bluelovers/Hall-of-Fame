<?php
print("--------- Blessing...<br />\n");
$RATE	= 3;
for($MAXSP=10; $MAXSP<1000; $MAXSP++) {
$SPrec	= ceil(sqrt($MAXSP) * $RATE);
$PER	= ($SPrec / $MAXSP) * 100;
print($SPrec."/".$MAXSP." - ".sprintf("%d",$PER)."%");
print("<br />\n");
}
print("--------- Benediction...<br />\n");
$RATE	= 5;
for($MAXSP=10; $MAXSP<1000; $MAXSP++) {
$SPrec	= ceil(sqrt($MAXSP) * $RATE);
$PER	= ($SPrec / $MAXSP) * 100;
print($SPrec."/".$MAXSP." - ".sprintf("%d",$PER)."%");
print("<br />\n");
}
?>