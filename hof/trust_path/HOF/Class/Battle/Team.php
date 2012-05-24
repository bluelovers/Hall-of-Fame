<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_Team extends HOF_Class_Array
{

	function __construct($team = array())
	{
		if ($team instanceof self)
		{
			$team = $team->toArray();
		}

		parent::__construct($team);

		if (!empty($team))
		{
			$this->update();
		}
	}

	/**
	 * @return self
	 */
	public static function &newInstance($team = array())
	{
		if ($team instanceof self)
		{
			return $team;
		}

		return new self($team);
	}

	function update($no = null)
	{
		foreach($this as $char)
		{
			$char->setTeamObj(&$this);

			if (isset($no) && !is_null($no))
			{
				$char->SetTeam($no);
			}
		}
	}

	function addChar($char, $no = null)
	{
		$char->setTeamObj(&$this);
		if (isset($no) && !is_null($no))
		{
			$char->SetTeam($no);
		}

		$this[] = $char;
	}

	function addChars($list, $no = null)
	{
		foreach($list as $char)
		{
			$this->addChar($char, $no);
		}
	}

	protected function _getTeamArray($who)
	{
		if ($who instanceof self)
		{
			$team = $who->toArray();
		}
		elseif ($who instanceof HOF_Class_Char)
		{
			$team = $who->getTeamObj()->toArray();
		}
		elseif (@isset($this))
		{
			$team = $this->toArray();
		}
		else
		{
			$team = (array)$who;
		}

		return $team;
	}

	/**
	 * 指定キャラのチームの死者数を数える(指定のチーム)ネクロマンサしか使ってない?
	 */
	function CountDead($who = null)
	{
		$team = self::_getTeamArray($who);

		$count = 0;

		foreach ((array)$team as $char)
		{
			if ($char->STATE === STATE_DEAD)
			{
				$count++;
			}
			else
			{
				if ($char->SPECIAL["Undead"] == true)
				{
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * 生存者数を数えて返す
	 */
	function CountAlive($who = null)
	{
		$team = self::_getTeamArray($who);

		$no = 0; //初期化
		foreach ((array)$team as $char)
		{
			if ($char->STATE !== 1) $no++;
		}
		return $no;
	}

	/**
	 * 初期キャラ生存数を数えて返す
	 */
	function CountAliveChars($who = null)
	{
		$team = self::_getTeamArray($who);

		$no = 0; //初期化
		foreach ((array)$team as $char)
		{
			if ($char->STATE === 1) continue;
			if ($char->monster) continue;
			$no++;
		}
		return $no;
	}

	function insert($offset, $insert)
	{
		parent::insert($offset, $insert);

		$this->update();
	}

}
