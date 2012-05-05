<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_Team
{

	/**
	 * 生存者数を数えて返す
	 */
	function CountAlive($team)
	{
		$no = 0; //初期化
		foreach ($team as $char)
		{
			if ($char->STATE !== 1) $no++;
		}
		return $no;
	}

	/**
	 * 初期キャラ生存数を数えて返す
	 */
		function CountAliveChars($team)
		{
			$no = 0; //初期化
			foreach ($team as $char)
			{
				if ($char->STATE === 1) continue;
				if ($char->monster) continue;
				$no++;
			}
			return $no;
		}

}
