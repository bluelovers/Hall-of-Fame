<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Log extends HOF_Class_Controller
{

	static $map_logtype = array(
		'log' => LOG_BATTLE_NORMAL,
		'clog' => LOG_BATTLE_NORMAL,
		'ulog' => LOG_BATTLE_UNION,
		'rlog' => LOG_BATTLE_RANK,
		);

	function _main_input()
	{
		foreach (self::$map_logtype as $_k => $_v)
		{
			if (isset(HOF::$input->request[$_k]))
			{

				$this->input->action = $_k;

				if (!empty(HOF::$input->request[$_k]))
				{
					$this->input->log = HOF::$input->request[$_k];
				}

				break;
			}
		}

		if ($this->input->log)
		{
			$this->_main_setup('log');
		}
		elseif ($this->input->action)
		{
			$this->_main_setup();
		}
	}

	function _main_action_update()
	{
		if ($_POST["updatetext"])
		{
			$update = htmlspecialchars($_POST["updatetext"], ENT_QUOTES);
			$update = stripslashes($update);
		}
		else  $update = @file_get_contents(UPDATE);

		if ($_POST["updatepass"] == UP_PASS && $_POST["updatetext"])
		{
			$fp = fopen(UPDATE, "w");
			$text = htmlspecialchars($_POST["updatetext"], ENT_QUOTES);
			$text = stripslashes($text);
			flock($fp, 2);
			fputs($fp, $text);
			fclose($fp);
		}

		$this->output['update'] = $update;
		$this->output['updatepass'] = ($_POST["updatepass"] == UP_PASS) ? true : false;
	}

	/**
	 * 戦闘ログの表示
	 */
	function _main_action_default()
	{
		$logs = array();

		$idx = $this->input->action;

		if ($idx != 'log')
		{
			$this->output->full_log = true;

			$map = array();

			$map[] = self::$map_logtype[$idx];
		}
		else
		{
			$map = array_unique(array_values(self::$map_logtype));
		}

		// common

		foreach ($map as $_k)
		{
			$log = HOF_Class_File::glob($_k);
			foreach (array_reverse($log) as $file)
			{
				$logs[$_k][] = HOF_Model_Data::getLogBattleFile($file);
				$limit++;
				if (!$this->output->full_log && 30 <= $limit)
				{
					break;
				}
			}
		}

		$this->output->idx = $idx;

		$this->output['logs'] = $logs;
	}

	function _main_action_log()
	{
		$idx = $this->input->action;

		if ($idx == 'log' || $idx == 'clog')
		{
			$idx = 'clog';
		}

		$this->output->idx = $idx;

		$this->output->log = HOF_Model_Data::getLogBattle($this->input->log, self::$map_logtype[$idx], 1);

		if (!$this->output->log)
		{
			$this->output->error[] = array('log doesnt exists');
		}

		$this->options['escapeHtml'] = false;
	}


}
