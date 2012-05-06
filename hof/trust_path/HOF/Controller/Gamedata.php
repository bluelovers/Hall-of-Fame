<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Gamedata extends HOF_Class_Controller
{

	/**
	 * げーむでーた
	 */
	function ShowGameData()
	{

		switch ($_GET["gamedata"])
		{
			case "job":
				include (GAME_DATA_JOB);
				break;
			case "item":
				include (GAME_DATA_ITEM);
				break;
			case "judge":
				include (GAME_DATA_JUDGE);
				break;
			case "monster":
				include (GAME_DATA_MONSTER);
				break;
			default:
				include (GAME_DATA_JOB);
				break;
		}

	}

}
