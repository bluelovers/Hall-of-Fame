<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Hunt extends HOF_Class_Controller
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
		$this->user->fpCloseAll();
	}

	function _input()
	{

	}

	function _main_action_default()
	{
		$mapList = HOF_Model_Data::getLandAppear($this->user);

		$Union = array();

		if ($files = game_core::glob(UNION))
		{
			foreach ($files as $file)
			{
				$UnionMons = HOF_Model_Char::newUnionFromFile($file);
				if ($UnionMons->is_Alive()) $Union[] = $UnionMons;
			}
		}

		if ($Union)
		{

			$result = $this->user->CanUnionBattle();

			if ($result !== true)
			{
				$left_minute = floor($result / 60);
				$left_second = $result % 60;
			}

			ob_start();
			$this->user->ShowCharacters($Union);
			$union_showchar = ob_get_clean();

		}

		$logs = array();

		$log = game_core::glob(LOG_BATTLE_UNION);
		foreach (array_reverse($log) as $file)
		{
			$limit++;

			$logs[] = $file;

			if (15 <= $limit) break;
		}

		$this->output->maps = $mapList;

		$this->output->union = $Union;
		$this->output->union_showchar = $union_showchar;

		$this->output->result = $result;
		$this->output->left_minute = $left_minute;
		$this->output->left_second = $left_second;

		$this->output->logs = $logs;

		$this->options['escapeHtml'] = false;
	}

}
