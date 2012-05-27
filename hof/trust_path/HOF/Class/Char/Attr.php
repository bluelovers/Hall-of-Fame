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
	 * 経験値を得る
	 */
	function GetExp($exp)
	{
		// モンスターは経験値を得ない
		if ($this->char->monster) return false;

		// 最大レベルの場合経験値を得ない
		if (MAX_LEVEL <= $this->char->level) return false;

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

}
