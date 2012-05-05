<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
class HOF_Class_Battle extends battle
{

	function ShowCssImage()
	{
		echo HOF_Class_Battle_Style::newInstance()
			->SetBackGround($this->BackGround)
			->SetTeams($this->team1, $this->team0)
			->SetMagicCircle($this->team1_mc, $this->team0_mc)
			->NoFlip((BTL_IMG_TYPE == 2))
			->exec()
		;
	}

}
