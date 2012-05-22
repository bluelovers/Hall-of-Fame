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

	/**
	 * アイテム一覧
	 */
	function _main_action_default()
	{
		$this->user->LoadUserItem();
	}

	function _main_after()
	{
		parent::_main_view();

		$this->user->fpclose_all();
	}

	function _ItemShow()
	{
		if ($this->user->item)
		{
			$goods = new HOF_Class_Item_Style_List();

			$goods->SetID("my");
			$goods->SetName("type");

			foreach ($this->user->item as $no => $val)
			{
				$item = HOF_Class_Item::newInstance($no);
				$string = $item->html($val);
				$goods->AddItem($item, $string);
			}

			echo $goods->output();
		}
		else
		{
			print ("No items.");
		}
	}

}


?>