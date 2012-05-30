<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_Battle
{

	protected $chat;

	function __construct($char)
	{
		$this->char = $char;
	}

	function setTeamObj(&$team)
	{
		$this->char->team_obj = &$team;
	}

	function &getTeamObj()
	{
		return $this->char->team_obj;
	}

	/**
	 * 召喚力?召喚した時の召喚モンスターの強さ
	 */
	function SummonPower()
	{
		$DEX_PART = sqrt($this->char->DEX) * 5; // DEX分の強化分
		$Strength = 1 + ($DEX_PART + $this->char->LUK) / 250;
		if ($this->char->SPECIAL["Summon"]) $Strength *= (100 + $this->char->SPECIAL["Summon"]) / 100;
		return $Strength;
	}

	//	特殊技能?の追加
	function GetSpecial($name, $value)
	{
		if (is_bool($value))
		{
			$this->char->SPECIAL["$name"] = $value;
		}
		elseif (is_array($value))
		{
			foreach ($value as $key => $val)
			{
				$this->char->SPECIAL["$name"]["$key"] += $val;
			}
		}
		else
		{
			$this->char->SPECIAL["$name"] += $value;
		}
	}

	// 戦闘時のチームを設定(あんまり使ってない)
	function SetTeam($no)
	{
		$this->char->team = $no;
	}

	//	経験値を出す(モンスターだけ?)
	function DropExp()
	{
		if ($this->char->isSummon())
		{
			return false;
		}

		if (isset($this->char->exphold))
		{
			$exp = $this->char->exphold;
			$this->char->exphold = round($exp / 2);
			return $exp;
		}
		elseif ($this->char->isChar())
		{
			return 1;
		}
		else
		{
			return false;
		}
	}

	//	お金を出す(モンスターだけ?)
	function DropMoney()
	{
		if ($this->char->isSummon())
		{
			return false;
		}

		if (isset($this->char->moneyhold))
		{
			$money = $this->char->moneyhold;
			$this->char->moneyhold = 0;
			return $money;
		}
		else
		{
			return false;
		}
	}

	//	アイテムを落とす(モンスターだけ?)
	function DropItem()
	{
		if ($this->char->isSummon())
		{
			return false;
		}

		if ($this->char->itemdrop)
		{
			$item = $this->char->itemdrop;
			// 一度落としたアイテムは消す
			$this->char->itemdrop = false;
			return $item;
		}
		else
		{
			return false;
		}
	}

}
