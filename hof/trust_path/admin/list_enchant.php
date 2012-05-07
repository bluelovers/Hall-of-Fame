<?php

list($low, $high) = HOF_Class_Item_Create::ItemAbilityPossibility("Sword");
print ("---------------LOW<br />\n");
foreach ($low as $enchant)
{
	$item = array();
	HOF_Model_Item::addEnchantData($item, $enchant);
	print ('<span style="width:10em;text-align:right">' . $enchant . '</span>:' . $item["option"] . "<br />\n");
}
print ("---------------HIGH<br />\n");
foreach ($high as $enchant)
{
	$item = array();
	HOF_Model_Item::addEnchantData($item, $enchant);
	print ('<span style="width:10em;text-align:right">' . $enchant . '</span>:' . $item["option"] . "<br />\n");
}

//dump($low);
//dump($high);

function dump($var)
{
	print ("<pre>\n");
	var_dump($var);
	print ("\n</pre>\n");
}


?>