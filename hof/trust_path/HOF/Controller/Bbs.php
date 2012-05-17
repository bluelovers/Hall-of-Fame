<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Bbs extends HOF_Class_Controller
{

	function _main_before()
	{
		if (!BBS_BOTTOM_TOGGLE) return false;

		$file = BBS_BOTTOM;

		if (!file_exists($file)) return false;

		$log = file($file);

		if ($_POST["message"] && strlen($_POST["message"]) < 121)
		{
			$_POST["message"] = htmlspecialchars($_POST["message"], ENT_QUOTES);
			$_POST["message"] = stripslashes($_POST["message"]);

			$name = ($this->name ? "<span class=\"bold\">{$this->name}</span>" : "名無し");
			$message = $name . " > " . $_POST["message"];
			if ($this->UserColor) $message = "<span style=\"color:{$this->UserColor}\">" . $message . "</span>";
			$message .= " <span class=\"light\">(" . HOF_Helper_Global::gc_date("Mj G:i") . ")</span>\n";

			array_unshift($log, $message);

			// ログ保存行数あ
			while (150 < count($log))
			{
				array_pop($log);
			}

			HOF_Class_File::WriteFile($file, implode(null, $log));
		}
	}

}
