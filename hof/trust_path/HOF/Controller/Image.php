<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Image extends HOF_Class_Controller
{

	function _main_init()
	{
		$type = 'png';

		$img = new HOF_Class_Battle_Style_Image($type);

		$img->SetBackGround();
		$img->SetCharFile();
		$img->ShowInfo();
		$img->CopyChar();
		$img->Filter();
		$img->OutPutImage();

		exit();
	}

}
