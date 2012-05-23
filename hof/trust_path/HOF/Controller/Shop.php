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

	function _main_init()
	{
		$this->user = &HOF::user();

		$this->options['defaultAction'] = 'buy';
		$this->options['allowActions'] = true;
	}

	function _main_before()
	{
		$this->_input();

		if (!$this->user->allowPlay())
		{
			$this->_main_stop(true);

			HOF_Class_Controller::getInstance('game', 'login')->_main_exec('login');

			return;
		}

		$this->user->LoadUserItem();

		$this->output->npc_no = HOF_Class_Icon::getRandNo(HOF_Class_Icon::IMG_CHAR, 'ori_002');

		//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	}

	function _main_after()
	{
		parent::_main_after();

		$this->user->fpclose_all();
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
	}

	function _main_action_buy()
	{
		$ShopList = HOF_Model_Data::getShopList();

		$list = array();

		foreach ($ShopList as $itemNo)
		{
			$item = HOF_Model_Data::newItem($itemNo);
			if (!$item) continue;

			$list[$itemNo] = $item;
		}

		$this->output->shoplist = $list;
	}

	function _main_action_sell()
	{
		$ShopList = $this->user->item;

		$list = array();

		foreach ($ShopList as $itemNo => $amount)
		{
			$item = HOF_Model_Data::newItem($itemNo);

			if (!$item) continue;

			$item->amount = $amount;

			$list[$itemNo] = $item;
		}

		$this->output->shoplist = $list;
	}

	function _main_action_work()
	{

	}

	/**
	 * ショップ(売る)
	 */
	function _sell()
	{
		if ($this->ShopSellProcess()) $this->user->SaveData();
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
			$item = HOF_Class_Item::newInstance($itemNo);
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
			print ($item->html() . "\n");
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
			$item = HOF_Class_Item::newInstance($itemNo);
			if (!$item) continue;
			$amount = (int)$_POST["amount_" . $itemNo];
			if ($amount < 0) $amount = 0;
			$Deleted = $this->user->DeleteItem($itemNo, $amount);
			//print("$itemNo x $Deleted<br>");
			$sellPrice = HOF_Helper_Item::ItemSellPrice($item);
			$Total = $Deleted * $sellPrice;
			$getMoney += $Total;
			print ("<tr><td class=\"td7\">");
			print (HOF_Helper_Global::MoneyFormat($sellPrice) . "\n");
			print ("</td><td class=\"td7\">");
			print ("x {$Deleted}\n");
			print ("</td><td class=\"td7\">");
			print ("= " . HOF_Helper_Global::MoneyFormat($Total) . "\n");
			print ("</td><td class=\"td8\">");
			print ($item->html() . "\n");
			print ("</td></tr>\n");
		}
		print ("<tr><td colspan=\"4\" class=\"td8\">合計 : " . HOF_Helper_Global::MoneyFormat($getMoney) . "</td></tr>");
		print ("</table>\n");
		print ("</div>");
		$this->user->SaveUserItem();
		$this->user->GetMoney($getMoney);
		return true;
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
				HOF_Helper_Global::ShowResult(HOF_Helper_Global::MoneyFormat($money) . " げっとした！", "margin15");
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