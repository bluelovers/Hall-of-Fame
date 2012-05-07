<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Shop extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _init()
	{
		$this->user = &HOF_Model_Main::getInstance();
	}

	function _main_before()
	{
		$this->_input();

		$this->user->LoadUserItem();

		$this->output->npc_no = HOF_Class_Icon::getRandNo(HOF_Class_Icon::IMG_CHAR, 'ori_002');

		//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	}

	function _main_after()
	{
		parent::_main_after();

		$this->user->fpCloseAll();
	}

	function _input()
	{
		$this->input->ItemBuy = $_REQUEST['ItemBuy'];
		$this->input->amount = intval($_POST["amount"]);
	}

	/**
	 * ショップ(買う)
	 */
	function _buy()
	{
		if ($this->ShopBuyProcess()) $this->user->SaveData();

		$this->ShopBuyShow();
	}

	/**
	 * ショップ(売る)
	 */
	function _sell()
	{
		if ($this->ShopSellProcess()) $this->user->SaveData();

		$this->ShopSellShow();
	}

	/**
	 * ショップ(働く)
	 */
	function _work()
	{
		$this->output->work_each_pay = 500;

		if ($this->WorkProcess()) $this->user->SaveData();
	}

	function ShopBuyProcess()
	{
		//dump($_POST);
		if (!$this->input->ItemBuy) return false;

		print ("<div style=\"margin:15px\">");
		print ("<table cellspacing=\"0\">\n");
		print ('<tr><td class="td6" style="text-align:center">値段</td>' . '<td class="td6" style="text-align:center">数</td>' . '<td class="td6" style="text-align:center">計</td>' . '<td class="td6" style="text-align:center">アイテム</td></tr>' . "\n");
		$moneyNeed = 0;
		$ShopList = HOF_Model_Data::getShopList();
		foreach ($ShopList as $itemNo)
		{
			if (!$_POST["check_" . $itemNo]) continue;
			$item = HOF_Model_Data::getItemData($itemNo);
			if (!$item) continue;
			$amount = (int)$_POST["amount_" . $itemNo];
			if ($amount < 0) $amount = 0;

			//print("$itemNo x $Deleted<br>");
			$buyPrice = $item["buy"];
			$Total = $amount * $buyPrice;
			$moneyNeed += $Total;
			print ("<tr><td class=\"td7\">");
			print (HOF_Helper_Global::MoneyFormat($buyPrice) . "\n");
			print ("</td><td class=\"td7\">");
			print ("x {$amount}\n");
			print ("</td><td class=\"td7\">");
			print ("= " . HOF_Helper_Global::MoneyFormat($Total) . "\n");
			print ("</td><td class=\"td8\">");
			print (HOF_Class_Item::ShowItemDetail($item) . "\n");
			print ("</td></tr>\n");
			$this->user->AddItem($itemNo, $amount);
		}
		print ("<tr><td colspan=\"4\" class=\"td8\">合計 : " . HOF_Helper_Global::MoneyFormat($moneyNeed) . "</td></tr>");
		print ("</table>\n");
		print ("</div>");
		if ($this->user->TakeMoney($moneyNeed))
		{
			$this->user->SaveUserItem();
			return true;
		}
		else
		{
			HOF_Helper_Global::ShowError("お金が足りません", "margin15");
			return false;
		}
	}

	function ShopBuyShow()
	{
		print ('<div style="margin:15px">' . "\n");
		print ("<h4>買う</h4>\n");

		print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
	\$('#i'+id+'a').parent('tr').find('td').toggleClass('tdToggleBg').find('#text_'+id).focus();
}
function toggleCheckBox(id) {
	\$(':checkbox[name=check_'+id+']').prop('checked', function (index, oldPropertyValue){
		if (!oldPropertyValue) \$('#text_'+id).focus();

		return !oldPropertyValue;
	});
	toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print ('<form action="?menu=buy" method="post">' . "\n");
		print ("<table cellspacing=\"0\">\n");
		print ('<tr><td class="td6"></td>' . '<td style="text-align:center" class="td6">値段</td>' . '<td style="text-align:center" class="td6">数</td>' . '<td style="text-align:center" class="td6">アイテム</td></tr>' . "\n");
		$ShopList = HOF_Model_Data::getShopList();
		foreach ($ShopList as $itemNo)
		{
			$item = HOF_Model_Data::getItemData($itemNo);
			if (!$item) continue;
			print ("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print ('<input type="checkbox" name="check_' . $itemNo . '" value="1" onclick="toggleCSS(\'' . $itemNo . '\')">' . "\n");
			print ("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// 買値
			$price = $item["buy"];
			print (HOF_Helper_Global::MoneyFormat($price));
			print ("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print ('<input type="text" id="text_' . $itemNo . '" name="amount_' . $itemNo . '" value="1" style="width:60px" class="text">' . "\n");
			print ("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print (HOF_Class_Item::ShowItemDetail($item));
			print ("</td></tr>\n");
		}
		print ("</table>\n");
		print ('<input type="submit" name="ItemBuy" value="Buy" class="btn">' . "\n");
		print ("</form>\n");

		print ("</div>\n");
	}

	function ShopSellProcess()
	{
		//dump($_POST);
		if (!$_POST["ItemSell"]) return false;

		$GetMoney = 0;
		print ("<div style=\"margin:15px\">");
		print ("<table cellspacing=\"0\">\n");
		print ('<tr><td class="td6" style="text-align:center">売値</td>' . '<td class="td6" style="text-align:center">数</td>' . '<td class="td6" style="text-align:center">計</td>' . '<td class="td6" style="text-align:center">アイテム</td></tr>' . "\n");
		foreach ($this->user->item as $itemNo => $amountHave)
		{
			if (!$_POST["check_" . $itemNo]) continue;
			$item = HOF_Model_Data::getItemData($itemNo);
			if (!$item) continue;
			$amount = (int)$_POST["amount_" . $itemNo];
			if ($amount < 0) $amount = 0;
			$Deleted = $this->user->DeleteItem($itemNo, $amount);
			//print("$itemNo x $Deleted<br>");
			$sellPrice = ItemSellPrice($item);
			$Total = $Deleted * $sellPrice;
			$getMoney += $Total;
			print ("<tr><td class=\"td7\">");
			print (HOF_Helper_Global::MoneyFormat($sellPrice) . "\n");
			print ("</td><td class=\"td7\">");
			print ("x {$Deleted}\n");
			print ("</td><td class=\"td7\">");
			print ("= " . HOF_Helper_Global::MoneyFormat($Total) . "\n");
			print ("</td><td class=\"td8\">");
			print (HOF_Class_Item::ShowItemDetail($item) . "\n");
			print ("</td></tr>\n");
		}
		print ("<tr><td colspan=\"4\" class=\"td8\">合計 : " . HOF_Helper_Global::MoneyFormat($getMoney) . "</td></tr>");
		print ("</table>\n");
		print ("</div>");
		$this->user->SaveUserItem();
		$this->user->GetMoney($getMoney);
		return true;
	}

	function ShopSellShow()
	{
		print ('<div style="margin:15px">' . "\n");
		print ("<h4>売る</h4>\n");

		print <<< JS_HTML
<script type="text/javascript">
<!--
function toggleCSS(id) {
	\$('#i'+id+'a').parent('tr').find('td').toggleClass('tdToggleBg').find('#text_'+id).focus();
}
function toggleCheckBox(id) {
	\$(':checkbox[name=check_'+id+']').prop('checked', function (index, oldPropertyValue){
		if (!oldPropertyValue) \$('#text_'+id).focus();

		return !oldPropertyValue;
	});
	toggleCSS(id);
}
// -->
</script>
JS_HTML;

		print ('<form action="?menu=sell" method="post">' . "\n");
		print ("<table cellspacing=\"0\">\n");
		print ('<tr><td class="td6"></td>' . '<td style="text-align:center" class="td6">売値</td>' . '<td style="text-align:center" class="td6">数</td>' . '<td style="text-align:center" class="td6">アイテム</td></tr>' . "\n");
		foreach ($this->user->item as $itemNo => $amount)
		{
			$item = HOF_Model_Data::getItemData($itemNo);
			if (!$item) continue;
			print ("<tr><td class=\"td7\" id=\"i{$itemNo}a\">\n");
			print ('<input type="checkbox" name="check_' . $itemNo . '" value="1" onclick="toggleCSS(\'' . $itemNo . '\')">' . "\n");
			print ("</td><td class=\"td7\" id=\"i{$itemNo}b\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			// 売値
			$price = ItemSellPrice($item);
			print (HOF_Helper_Global::MoneyFormat($price));
			print ("</td><td class=\"td7\" id=\"i{$itemNo}c\">\n");
			print ('<input type="text" id="text_' . $itemNo . '" name="amount_' . $itemNo . '" value="' . $amount . '" style="width:60px" class="text">' . "\n");
			print ("</td><td class=\"td8\" id=\"i{$itemNo}d\" onclick=\"toggleCheckBox('{$itemNo}')\">\n");
			print (HOF_Class_Item::ShowItemDetail($item, $amount));
			print ("</td></tr>\n");
		}
		print ("</table>\n");
		print ('<input type="submit" name="ItemSell" value="Sell" class="btn" />' . "\n");
		print ('<input type="hidden" name="ItemSell" value="1" />' . "\n");
		print ("</form>\n");

		print ("</div>\n");
	}

	/**
	 * アルバイト処理
	 */
	function WorkProcess()
	{
		// 1以上10以下
		$this->input->amount = max(0, min(11, $this->input->amount));

		if ($this->input->amount)
		{
			$time = $this->input->amount * 100;
			$money = $this->input->amount * 500;

			if ($this->user->WasteTime($time))
			{
				ShowResult(HOF_Helper_Global::MoneyFormat($money) . " げっとした！", "margin15");
				$this->user->GetMoney($money);
				return true;
			}
			else
			{
				HOF_Helper_Global::ShowError("時間が足りません。", "margin15");
				return false;
			}
		}
	}

}


?>