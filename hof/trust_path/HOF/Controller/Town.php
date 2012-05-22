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
		$this->_input();

		$this->user->LoadUserItem();
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
		$file = BBS_TOWN;

		if (!file_exists($file)) return false;

		$log = file($file);

		if ($this->input->message && strlen($this->input->message) < 121)
		{
			$this->input->message = htmlspecialchars($this->input->message, ENT_QUOTES);
			$this->input->message = stripslashes($this->input->message);

			$name = "<span class=\"bold\">{$this->name}</span>";

			$message = $name . " > " . $this->input->message;

			if ($this->UserColor) $message = "<span style=\"color:{$this->UserColor}\">" . $message . "</span>";

			$message .= " <span class=\"light\">(" . HOF_Helper_Global::gc_date("Mj G:i") . ")</span>";

			array_unshift($log, $message);
			while (50 < count($log)) array_pop($log);

			HOF_Class_File::WriteFile($file, implode(null, $log));
		}

		return $log;
	}

}
