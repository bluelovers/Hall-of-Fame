<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_Attr
{

	protected $char;

	private $_cache;

	function __construct($char)
	{
		if (is_object($char))
		{
			$this->char = $char;
		}
		elseif (is_array($char))
		{
			$this->char = HOF_Class_Array::_toArrayObjectRecursive($char, 0);
		}
	}

	/**
	 * 必要経験値
	 */
	function CalcExpNeed()
	{
		if ($this->char->isUnion())
		{
			$exp = 50;

			return $exp;
		}
		elseif ($this->char->monster)
		{
			$exp = 3;

			return $exp;
		}

		switch ($this->char->level)
		{
			case 40:
				$exp = 30000;
				break;
			case 41:
				$exp = 40000;
				break;
			case 42:
				$exp = 50000;
				break;
			case 43:
				$exp = 60000;
				break;
			case 44:
				$exp = 70000;
				break;
			case 45:
				$exp = 80000;
				break;
			case 46:
				$exp = 100000;
				break;
			case 47:
				$exp = 250000;
				break;
			case 48:
				$exp = 500000;
				break;
			case 49:
				$exp = 999990;
				break;
			case 50:
			case (50 <= $this->char->level):
				$exp = "MAX";
				break;
			case (21 < $this->char->level):
				$exp = 2 * pow($this->char->level, 3) + 100 * $this->char->level + 100;
				$exp -= substr($exp, -2);
				$exp /= 5;
				break;
			default:
				$exp = pow($this->char->level - 1, 2) / 2 * 100 + 100;
				$exp /= 5;
				break;
		}

		return $exp;
	}

	/**
	 * 経験値を得る
	 */
	function GetExp($exp)
	{
		if ($this->char->isSummon())
		{
			return false;
		}

		$max_level = MAX_LEVEL;

		if ($this->char->monster)
		{
			// モンスターは経験値を得ない
			//return false;

			$exp = 1;
			$max_level *= $this->char->isUnion() ? 5 : 1.2;
		}

		// 最大レベルの場合経験値を得ない
		if (floor($max_level) <= $this->char->level) return false;

		$this->char->exp += $exp;

		// 必要な経験値
		$need = $this->CalcExpNeed($this->char->level);
		if ($need <= $this->char->exp)
		{
			$this->char->LevelUp();
			return true;
		}
	}

	/**
	 * レベルあげる時の処理
	 */
	function LevelUp()
	{
		$this->char->exp = 0;
		$this->char->level++;
		$this->char->statuspoint += GET_STATUS_POINT; //ステポをもらえる。
		$this->char->skillpoint += GET_SKILL_POINT;
	}

	function id($id = null)
	{
		if (!isset($this->char->ID))
		{
			$this->char->ID = HOF_Helper_Char::uniqid(get_class($this->char).$this->char->name);
		}

		if ($id !== null)
		{
			$this->char->ID = $id;
		}

		return $this->char->ID;
	}

}
