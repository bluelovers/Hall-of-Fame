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

	/**
	 * 戦闘時のチームを設定(あんまり使ってない)
	 */
	function team($team = null)
	{
		if ($team !== null)
		{
			$this->char->team = $team;
		}

		return $this->char->team;
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

	//	経験値を出す(モンスターだけ?)
	function DropExp()
	{
		if (isset($this->char->reward['exphold']))
		{
			$exp = $this->char->reward['exphold'];
			$this->char->reward['exphold'] = round($exp / 2);
			return (int)$exp;
		}
		else
		{
			return false;
		}
	}

	//	お金を出す(モンスターだけ?)
	function DropMoney()
	{
		if ($this->char->reward['moneyhold'])
		{
			$money = $this->char->reward['moneyhold'];
			$this->char->reward['moneyhold'] = 0;
			return (int)$money;
		}
		else
		{
			return false;
		}
	}

	//	アイテムを落とす(モンスターだけ?)
	function DropItem()
	{
		if (!empty($this->char->reward['itemdrop']))
		{
			$item = $this->char->reward['itemdrop'];
			// 一度落としたアイテムは消す
			$this->char->reward['itemdrop'] = false;
			return $item;
		}
		else
		{
			return false;
		}
	}

}
