<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Log extends HOF_Class_Controller
{

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

}
