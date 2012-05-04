<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle extends battle
{

	function ShowCssImage()
	{
		$img = new HOF_Class_Battle_Style();

		$img
			->SetBackGround($this->BackGround)
			->SetTeams($this->team1, $this->team0)
			->SetMagicCircle($this->team1_mc, $this->team0_mc)
		;

		if (BTL_IMG_TYPE == 2)
		{
			// CSS画像反転無し
			$img->NoFlip();
		}

		echo $img->exec();
	}

}
