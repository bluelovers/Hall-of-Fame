<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Helper_Battle
{

	/**
	 * ドッペルゲンガーと戦う。
	 */
	function DoppelBattle($MyTeam, $turns = 10)
	{

		$MyTeam = new HOF_Class_Battle_Team2($MyTeam);
		$EnemyTeam = $MyTeam->getClone();

		HOF_Class_Battle_Team2::clsNameList();

		$MyTeam->pushNameList();
		$EnemyTeam->pushNameList();

		$MyTeam->fixCharName();
		$EnemyTeam->fixCharName(null, '幻影の');

		$battle = new HOF_Class_Battle($MyTeam, $EnemyTeam);

		$battle->SetTeamName(HOF::user()->name, "ドッペル");
		$battle->LimitTurns($turns);
		$battle->NoResult();
		$battle->Process();

		return true;

		//$enemy	= $party;
		//これが無いとPHP4or5 で違う結果になるんです
		//$enemy	= unserialize(serialize($enemy));
		// ↓

		$enemy = array();

		foreach ($party as $key => $char)
		{
			/*
			$enemy[$key] = new HOF_Class_Char_Type_Char();
			$enemy[$key]->setCharData(get_object_vars($char));
			*/

			//$enemy[$key] = HOF_Model_Char::newChar(get_object_vars($char));
			$enemy[$key] = $char->getClone();
			$enemy[$key]->ChangeName("偽の" . $char->name);
		}

		/*
		foreach ($enemy as $key => $doppel)
		{
			//$doppel->judge	= array();//コメントを取るとドッペルが行動しない。
			$enemy[$key]->ChangeName("ニセ" . $doppel->name);
		}
		*/

		//dump($enemy[0]->judge);
		//dump($party[0]->judge);

		$enemy = HOF_Class_Battle_Team::newInstance($enemy);
		$party = HOF_Class_Battle_Team::newInstance($party);

		$battle = new HOF_Class_Battle($party, $enemy);
		$battle->SetTeamName($this->name, "ドッペル");
		$battle->LimitTurns($turns); //最大ターン数は10
		$battle->NoResult();
		$battle->Process(); //戦闘開始
		return true;
	}

}
