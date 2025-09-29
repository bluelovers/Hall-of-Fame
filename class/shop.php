<?php

/**
 * 顯示商店頁面的頁首
 * @param main $main 主物件
 */
function ShopHeader($main) {
	?>
	<div style="margin:15px">
		<h4>店</h4>
		<div style="width:600px">
			<div style="float:left;width:50px;">
				<img src="<?php print IMG_CHAR?>ori_002.gif" />
			</div>
			<div style="float:right;width:550px;">
				歡迎光臨一<br />
				<a href="?menu=buy">買</a> / <a href="?menu=sell">賣</a><br />
				<a href="?menu=work">打工</a>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
	<?php
}

/**
 * 處理商店相關的請求
 * @param main $main 主物件
 * @return bool
 */
function ShopProcess($main) {
	switch(true) {
		case($_POST["partjob"]):
			if($main->WasteTime(100)) {
				$main->GetMoney(500);
				ShowResult("工作".MoneyFormat(500)." げっとした(!?)","margin15");
				return true;
			} else {
				ShowError("時間が無い。動くなんてもったいない.(?)","margin15");
				return false;
			}
		case($_POST["shop_buy"]):
			$ShopList    = ShopList();//賣ってるものデ一タ
			if($_POST["item_no"] && in_array($_POST["item_no"],$ShopList)) {
				if(preg_match("/^[0-9]/",$_POST["amount"])) {
									$amount    = (int)$_POST["amount"];
					if($amount == 0)
						$amount    = 1;
				} else {
					$amount    = 1;
				}
				$item    = LoadItemData($_POST["item_no"]);
				$need    = $amount * $item["buy"];//購入に必要なお金
				if($main->TakeMoney($need)) {// お金を引けるかで判定。
					$main->AddItem($_POST["item_no"],$amount);
					$main->SaveUserItem();
					if(1 < $amount) {
						$img    = "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
						ShowResult("{$img}{$item[name]}  {$amount}個 買入 (".MoneyFormat($item["buy"])." x{$amount} = ".MoneyFormat($need).")","margin15");
						return true;
					} else {
						$img    = "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
						ShowResult("{$img}{$item[name]}個 買入 (".MoneyFormat($need).")","margin15");
						return true;
					}
				} else {//資金不足
					ShowError("資金不足(需要".MoneyFormat($need).")","margin15");
					return false;
				}
			}
			break;
		case($_POST["shop_sell"]):
			if($_POST["item_no"] && $main->item[$_POST["item_no"]]) {
				if(preg_match("/^[0-9]/",$_POST["amount"])) {
					$amount    = (int)$_POST["amount"];
					if($amount == 0)
						$amount    = 1;
				} else {
					$amount    = 1;
				}
				// 消した個數(超過して賣られるのも防ぐ)
				$DeletedAmount    = $main->DeleteItem($_POST["item_no"],$amount);
				$item    = LoadItemData($_POST["item_no"]);
				$price    = (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
				$main->GetMoney($price*$DeletedAmount);
				$main->SaveUserItem();
				if($DeletedAmount != 1)
					$add    = " x{$DeletedAmount}";
				$img    = "<img src=\"".IMG_ICON.$item[img]."\" class=\"vcent\" />";
				ShowResult("{$img}{$item[name]}{$add}".MoneyFormat($price*$DeletedAmount)." 出售","margin15");
				return true;
			}
			break;
	}
}

/**
 * 顯示商店主頁面
 * @param main $main 主物件
 * @param string|null $message 顯示的訊息
 */
function ShopShow($main, $message=NULL) {
	?>
	<div style="margin:15px">
	<?php print ShowError($message)?>
	<h4>Goods List</h4>
	<div style="margin:0 20px">
<?php
	include(CLASS_JS_ITEMLIST);
	$ShopList    = ShopList();//賣ってるものデ一タ

	$goods    = new JS_ItemList();
	$goods->SetID("JS_buy");
	$goods->SetName("type_buy");
	// JSを使用しない。
	if($main->no_JS_itemlist)
		$goods->NoJS();
	foreach($ShopList as $no) {
		$item    = LoadItemData($no);
		$string    = '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
		$string    .= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($item["buy"])."</span>".ShowItemDetail($item,false,1)."<br />";
		$goods->AddItem($item,$string);
	}
	print($goods->GetJavaScript("list_buy"));
	print($goods->ShowSelect());

	print('<form action="?shop" method="post">');
	print('<div id="list_buy">'.$goods->ShowDefault().'</div>');
	print('<input type="submit" class="btn" name="shop_buy" value="買">
');
	print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)<br />');
	print('<input type="hidden" name="shop_buy" value="1">
');
	print('</form></div>');

	print("<h4>My Items<a name=\"sell\"></a></h4>");//所持物賣る
	print('<div style="margin:0 20px">');
	if($main->item) {
		$goods    = new JS_ItemList();
		$goods->SetID("JS_sell");
		$goods->SetName("type_sell");
		// JSを使用しない。
		if($main->no_JS_itemlist)
			$goods->NoJS();
		foreach($main->item as $no => $val) {
			$item    = LoadItemData($no);
			$price    = (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
			$string    = '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
			$string    .= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";
			$head    = '<input type="radio" name="item_no" value="'.$no.'" class="vcent">'.MoneyFormat($item["buy"]);
			$goods->AddItem($item,$string);
		}
		print($goods->GetJavaScript("list_sell"));
		print($goods->ShowSelect());
	
		print('<form action="?shop" method="post">');
		print('<div id="list_sell">'.$goods->ShowDefault().'</div>');
		print('<input type="submit" class="btn" name="shop_sell" value="Sell">');
		print('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)');
		print('<input type="hidden" name="shop_sell" value="1">');
		print('</form>');
	} else {
		print("No items");
	}
	print("</div>\n");
	?>
	<form action="?shop" method="post">
		<h4>打工</h4>
		<div style="margin:0 20px">
			店で打工してお金を得ます...<br />
			<input type="submit" class="btn" name="partjob" value="打工">
			Get <?php print MoneyFormat("500")?> for 100Time.
		</form>
	</div>
</div>
<?php
}

/**
 * 處理購買請求
 * @param main $main 主物件
 * @return bool
 */
function ShopBuyProcess($main) {
	if(!$_POST["ItemBuy"])
		return false;

	print("<div style=\"margin:15px\">");
	print("<table cellspacing=\"0\">
");
	print('<tr><td class="td6" style="text-align:center">價格</td>'.
	'<td class="td6" style="text-align:center">數</td>'.
	'<td class="td6" style="text-align:center">共計</td>'.
	'<td class="td6" style="text-align:center">道具</td></tr>');
	$moneyNeed    = 0;
	$ShopList    = ShopList();
	foreach($ShopList as $itemNo) {
		if(!$_POST["check_".$itemNo])
			continue;
		$item    = LoadItemData($itemNo);
		if(!$item) continue;
		$amount    = (int)$_POST["amount_".$itemNo];
		if($amount < 0)
			$amount    = 0;
		
		$buyPrice    = $item["buy"];
		$Total    = $amount * $buyPrice;
		$moneyNeed    += $Total;
		print("<tr><td class=\"td7\">");
		print(MoneyFormat($buyPrice)."\n");
		print("</td><td class=\"td7\">");
		print("x {$amount}\n");
		print("</td><td class=\"td7\">");
		print("= ".MoneyFormat($Total)."\n");
		print("</td><td class=\"td8\">");
		print(ShowItemDetail($item)."\n");
		print("</td></tr>\n");
		$main->AddItem($itemNo,$amount);
	}
	print("<tr><td colspan=\"4\" class=\"td8\">共計 : ".MoneyFormat($moneyNeed)."</td></tr>");
	print("</table>\n");
	print("</div>");
	if($main->TakeMoney($moneyNeed)) {
		$main->SaveUserItem();
		return true;
	} else {
		ShowError("您沒有足夠的錢","margin15");
		return false;
	}
}

/**
 * 顯示購買頁面
 * @param main $main 主物件
 */
function ShopBuyShow($main) {
	print('<div style="margin:15px">');
	print("<h4>購買</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

	print('<form action="?menu=buy" method="post">');
	print("<table cellspacing=\"0\">
");
	print('<tr><td class="td6"></td>'.
	'<td style="text-align:center" class="td6">價格</td>'.
	'<td style="text-align:center" class="td6">數</td>'.
	'<td style="text-align:center" class="td6">道具</td></tr>');
	$ShopList    = ShopList();
	foreach($ShopList as $itemNo) {
		$item    = LoadItemData($itemNo);
		if(!$item) continue;
		print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">
");
		print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">');
		print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox(\'{$itemNo}\')\">
");
		// 買值
		$price    = $item["buy"];
		print(MoneyFormat($price));
		print("</td><td class=\"td7\" id=\"i{$itemNo}c\">
");
		print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="1" style="width:60px" class="text">');
		print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox(\'{$itemNo}\')\">
");
		print(ShowItemDetail($item));
		print("</td></tr>\n");
	}
	print("</table>\n");
	print('<input type="submit" name="ItemBuy" value="買" class="btn">');
	print("</form>\n");

	print("</div>\n");
}

/**
 * 處理出售請求
 * @param main $main 主物件
 * @return bool
 */
function ShopSellProcess($main) {
	if(!$_POST["ItemSell"])
		return false;

	$getMoney    = 0;
	print("<div style=\"margin:15px\">");
	print("<table cellspacing=\"0\">
");
	print('<tr><td class="td6" style="text-align:center">價格</td>'.
	'<td class="td6" style="text-align:center">數</td>'.
	'<td class="td6" style="text-align:center">共計</td>'.
	'<td class="td6" style="text-align:center">道具</td></tr>');
	foreach($main->item as $itemNo => $amountHave) {
		if(!$_POST["check_".$itemNo])
			continue;
		$item    = LoadItemData($itemNo);
		if(!$item) continue;
		$amount    = (int)$_POST["amount_".$itemNo];
		if($amount < 0)
			$amount    = 0;
		$Deleted    = $main->DeleteItem($itemNo,$amount);
		$sellPrice    = ItemSellPrice($item);
		$Total    = $Deleted * $sellPrice;
		$getMoney    += $Total;
		print("<tr><td class=\"td7\">");
		print(MoneyFormat($sellPrice)."\n");
		print("</td><td class=\"td7\">");
		print("x {$Deleted}\n");
		print("</td><td class=\"td7\">");
		print("= ".MoneyFormat($Total)."\n");
		print("</td><td class=\"td8\">");
		print(ShowItemDetail($item)."\n");
		print("</td></tr>\n");
	}
	print("<tr><td colspan=\"4\" class=\"td8\">共計 : ".MoneyFormat($getMoney)."</td></tr>");
	print("</table>\n");
	print("</div>");
	$main->SaveUserItem();
	$main->GetMoney($getMoney);
	return true;
}

/**
 * 顯示出售頁面
 * @param main $main 主物件
 */
function ShopSellShow($main) {
	print('<div style="margin:15px">');
	print("<h4>出售</h4>\n");

print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
Element.toggleClassName('i'+id+'a', 'tdToggleBg');
Element.toggleClassName('i'+id+'b', 'tdToggleBg');
Element.toggleClassName('i'+id+'c', 'tdToggleBg');
Element.toggleClassName('i'+id+'d', 'tdToggleBg');
Field.focus('text_'+id);
}
function toggleCheckBox(id) {
if($('check_'+id).checked) {
  $('check_'+id).checked = false;
} else {
  $('check_'+id).checked = true;
  Field.focus('text_'+id);
}
toggleCSS(id);
}
// -->
</script>
JS_HTML;

	print('<form action="?menu=sell" method="post">');
	print("<table cellspacing=\"0\">
");
	print('<tr><td class="td6"></td>'.
	'<td style="text-align:center" class="td6">價格</td>'.
	'<td style="text-align:center" class="td6">數</td>'.
	'<td style="text-align:center" class="td6">道具</td></tr>');
	foreach($main->item as $itemNo => $amount) {
		$item    = LoadItemData($itemNo);
		if(!$item) continue;
		print("<tr><td class=\"td7\" id=\"i{$itemNo}a\">
");
		print('<input type="checkbox" name="check_'.$itemNo.'" value="1" onclick="toggleCSS(\''.$itemNo.'\')">');
		print("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox(\'{$itemNo}\')\">
");
		// 價格
		$price    = ItemSellPrice($item);
		print(MoneyFormat($price));
		print("</td><td class=\"td7\" id=\"i{$itemNo}c\">
");
		print('<input type="text" id="text_'.$itemNo.'" name="amount_'.$itemNo.'" value="'.$amount.'" style="width:60px" class="text">');
		print("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox(\'{$itemNo}\')\">
");
		print(ShowItemDetail($item,$amount));
		print("</td></tr>\n");
	}
	print("</table>\n");
	print('<input type="submit" name="ItemSell" value="Sell" class="btn" />');
	print('<input type="hidden" name="ItemSell" value="1" />');
	print("</form>\n");

	print("</div>\n");
}

/**
 * 處理打工請求
 * @param main $main 主物件
 */
function WorkProcess($main) {
	/*if($_POST["amount"]) {
		$amount    = (int)$_POST["amount"];
		// 1以上10以下
		if(0 < $amount && $amount < 11) {
			$time    = $amount * 100;
			$money    = $amount * 500;
			if($main->WasteTime($time)) {
				ShowResult(MoneyFormat($money)." げっとした！","margin15");
				$main->GetMoney($money);
				return true;
			} else {
				ShowError("您沒有足夠的時間。","margin15");
				return false;
			}
		}
	}*/
}

/**
 * 顯示打工頁面
 * @param main $main 主物件
 */
function WorkShow($main) {
	?>
	<div style="margin:15px">
		<h4>一份兼職工作！</h4>
		<form method="post" action="?menu=work">
			<p>1回 100Time<br />
			給與 : <?php print MoneyFormat(500)?>/回</p>
			<select name="amount">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select><br />
			<input type="submit" value="打工" class="btn"/>
		</form>
	</div>
	<?php
}

