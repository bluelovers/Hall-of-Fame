<?php
include_once(DATA_ITEM);
?>
<div style="margin:0 15px">
<?php
$ItemList	= array(
"Éð´ï(Weapon)"	=> array(1000,1100,1700,1800,2000),
"½â(Shield)"	=> array(3000,3001,3100,3101),
"³»(Armor)"	=> array(5000,5001,5100,5101,5200,5202),
"¥¢¥¤¥Æ¥à(Item)"	=> array(5500,5501),
"ÁÇºà(Material)"	=> array(6000,6001,6040,6180,6800,7000),
);
foreach($ItemList as $Type => $ItemNoArray) {
	print("<h4>$Type</h4>\n");
	foreach($ItemNoArray as $ItemNo) {
		$item	= LoadItemData($ItemNo);
		ShowItemDetail($item);
		print("<br />\n");
	}
}
?>
</div>