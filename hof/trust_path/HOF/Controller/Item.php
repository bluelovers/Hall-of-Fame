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

	function _main_before()
	{
		if (!$this->user->allowPlay())
		{
			$this->_main_stop(true);

			HOF_Class_Controller::getInstance('game', 'login')->_main_exec('login');

			return;
		}
	}

	/**
	 * アイテム一覧
	 */
	function _main_action_default()
	{
		//$this->user->item();
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
				$goods->item_add($item, $string);
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