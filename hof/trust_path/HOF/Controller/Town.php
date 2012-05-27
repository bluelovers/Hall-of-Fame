<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * 街
 */
class HOF_Controller_Town extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _init()
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

		$this->_input();

		$this->user->item();
		$this->user->fpclose_all();
	}

	function _input()
	{
		$this->input->message = trim($_POST["message"]);
	}

	function _main_action_default()
	{
		$_list = HOF_Model_Data::getTownAppear($this->user);

		if ($_list["Auction"] && !AUCTION_TOGGLE)
		{
			$_list["Auction"] = false;
		}

		$list = array();

		foreach ($_list as $k => $v)
		{
			$list[$k] = $v;

			$k = HOF::putintoPathParts($k);

			$list[$k] = $v;
		}

		/**
		 * 町の表示
		 */
		$this->output->list = $list;

		/**
		 * 普通の1行掲示板
		 */
		$this->output->log = $this->_town_bbs();

		$this->options['escapeHtml'] = false;
	}

	/**
	 * 普通の1行掲示板
	 */
	function _town_bbs()
	{
		$log = HOF::log()->data('bbs_town');

		if ($this->input->message && strlen($this->input->message) < 121)
		{
			$this->input->message = htmlspecialchars($this->input->message, ENT_QUOTES);
			$this->input->message = stripslashes($this->input->message);

			$name = "<span class=\"bold\">{$this->user->name}</span>";

			$message = $name . " > " . $this->input->message;

			if ($this->UserColor) $message = "<span style=\"color:{$this->UserColor}\">" . $message . "</span>";

			$message .= " <span class=\"light\">(" . HOF_Helper_Global::gc_date("Mj G:i") . ")</span>";

			$log[] = $message;

			HOF::log()->data('bbs_town', $log);
		}

		$log = array_reverse($log);

		return $log;
	}

}
