<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Char extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	protected $_cache;

	function _init()
	{
		$this->user = &HOF_Model_Main::getInstance();
	}

	function _main_action_default()
	{
		$this->user->CharDataLoadAll();
		$this->user->fpCloseAll();
		$this->LoginMain();
	}

	/**
	 * ログインした画面
	 */
	function LoginMain()
	{
		$this->ShowTutorial();
		$this->ShowMyCharacters();

		RegularControl($this->user->id);
	}

	/**
	 * 自分のキャラを表示する
	 */
	function ShowMyCharacters($array = NULL)
	{
		// $array ← 色々受け取る
		if (!$this->user->char) return false;

		$divide = (count($this->user->char) < CHAR_ROW ? count($this->user->char) : CHAR_ROW);
		$width = floor(100 / $divide); //各セル横幅

		$this->output->width = $this->user->width;
		$this->output->chars = $this->user->char;
	}

	/**
	 * チュウトリアル
	 */
	function ShowTutorial()
	{
		$last = $this->user->last;
		$start = substr($this->user->start, 0, 10);
		$term = 60 * 60 * 1;

		if (($last - $start) < $term)
		{
			$this->output->show_tutorial = true;
		}
	}

}


?>