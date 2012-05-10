<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Item extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _main_init()
	{
		$this->user = &HOF::user();
	}

	function _main_action_default()
	{
		$this->user->LoadUserItem();
	}

	function _main_after()
	{
		parent::_main_view();

		$this->user->fpCloseAll();
	}

	function _ItemShow()
	{
		if ($this->user->item)
		{
			$goods = new HOF_Class_Item_Style_List();
			$goods->SetID("my");
			$goods->SetName("type");
			// JSを使用しない。
			if ($this->no_JS_itemlist) $goods->NoJS();
			//$goods->ListTable("<table>");
			//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
			foreach ($this->user->item as $no => $val)
			{
				$item = HOF_Model_Data::getItemData($no);
				$string = HOF_Class_Item::ShowItemDetail($item, $val, 1) . "<br />";
				//$string	= "<tr><td>".$no."</td><td>".HOF_Class_Item::ShowItemDetail($item,$val,1)."</td></tr>";
				$goods->AddItem($item, $string);
			}
			print ($goods->GetJavaScript("list"));
			print ($goods->ShowSelect());
			print ('<div id="list">' . $goods->ShowDefault() . '</div>');
		}
		else
		{
			print ("No items.");
		}
	}

}


?>