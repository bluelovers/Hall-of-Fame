<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Initialize extends HOF_Class_Controller
{

	function _main_init()
	{
		$this->options['autoView'] = false;
		$this->options['autoViewOutput'] = false;
	}

	function _main_after()
	{
		echo '<pre>';

		if ($this->error)
		{
			echo implode("\n", $this->error);
		}
		else
		{
			touch(DAT_DIR.'initialize'.EXT_LOCK);

			$this->msg[] = 'Initialize End.';
		}

		echo implode("\n", $this->msg);

		echo '</pre>';

		exit();
	}

	function _main_action_default()
	{

		$dirs = array(
			BASE_PATH,
			BASE_PATH_STATIC,
			BASE_TRUST_PATH,

			BASE_PATH_TPL,

			BASE_PATH_CACHE,
			BASE_PATH_CACHE_USER,
			BASE_PATH_TRASH,
			BASE_PATH_SESSION,
			BASE_PATH_LOG,
			LOG_BATTLE_NORMAL,
			LOG_BATTLE_RANK,
			LOG_BATTLE_UNION,

			DAT_DIR,
			BASE_PATH_USER,
			BASE_PATH_UNION,

			BASE_PATH.HOF_Class_Icon::IMG_CHAR,
			BASE_PATH.HOF_Class_Icon::IMG_IMAGE,
			BASE_PATH.HOF_Class_Icon::IMG_ICON,
			BASE_PATH.HOF_Class_Icon::IMG_ITEM,
			BASE_PATH.HOF_Class_Icon::IMG_SKILL,
			BASE_PATH.HOF_Class_Icon::IMG_CHAR,
			BASE_PATH.HOF_Class_Icon::IMG_CHAR_REV,
			BASE_PATH.HOF_Class_Icon::IMG_OTHER,
			BASE_PATH.HOF_Class_Icon::IMG_LAND,
		);

		sort($dirs);

		foreach ($dirs as $dir)
		{
			//HOF_Class_File::mkdir($dir);

			if ($dir != BASE_TRUST_PATH && strpos($dir, BASE_TRUST_PATH) === 0 && is_dir(BASE_TRUST_PATH))
			{
				HOF_Class_File::mkdir($dir);
			}

			if ($dir != BASE_PATH && !file_exists($dir.'index.htm'))
			{
				touch($dir.'index.htm');
			}

			$ret = is_dir($dir);

			$k = str_replace(BASE_PATH, '{BASE_PATH}', $dir);

			$list['dirs'][$k] = $ret;
		}

		debug($list);

		$this->_main_action_htaccess();
	}

	function _main_action_htaccess()
	{
		$this->action = 'htaccess';
		$this->_main_view();

		file_put_contents(BASE_PATH.'.htaccess', (string)$this->content, LOCK_EX);

		$this->msg[] = '.htaccess OK!';

		unset($this->content);
	}

}

