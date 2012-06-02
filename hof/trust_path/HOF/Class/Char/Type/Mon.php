<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_MONSTER);

class HOF_Class_Char_Type_Mon extends HOF_Class_Char_Abstract
{

	public function source($over = false)
	{
		if (!isset($this->source))
		{
			$data = HOF_Model_Char::getBaseMonster($this->no);

			$this->source = new HOF_Class_Array($data);
		}

		return $this->source;
	}

	/**
	 * 生存状態にする。
	 */
	function GetNormal($mes = false)
	{
		if ($this->STATE === STATE_ALIVE) return true;
		if ($this->STATE === STATE_DEAD)
		{
			// 死亡状態
			if ($this->isSummon()) return true;
			if ($mes) print ($this->Name('bold') . ' <span class="recover">revived</span>!<br />' . "\n");
			$this->STATE = STATE_ALIVE;
			return true;
		}
		if ($this->STATE === STATE_POISON)
		{
			// 毒状態
			if ($mes) print ($this->Name('bold') . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = STATE_ALIVE;
			return true;
		}
	}

	/**
	 * しぼーしてるかどうか確認する。
	 */
	function CharJudgeDead()
	{
		if ($this->HP < 1 && $this->STATE !== STATE_DEAD)
		{
			// しぼー
			$this->STATE = STATE_DEAD;
			$this->HP = 0;
			$this->ResetExpect();
			//$this->delay	= 0;

			return true;
		}
	}
	/**
	 * キャラの変数をセットする。
	 */
	function setCharData($data_attr)
	{
		parent::setCharData($data_attr);

		$this->icon = $this->img;

		$this->no = $data_attr["no"];

		/*
		$this->maxhp = $data_attr["maxhp"];
		$this->hp = $data_attr["hp"];
		$this->maxsp = $data_attr["maxsp"];
		$this->sp = $data_attr["sp"];
		*/

		// モンスター専用
		//$this->monster		= $data_attr["monster"];
		//$this->monster = true;

		$this->atk = $data_attr["atk"];
		$this->def = $data_attr["def"];
		$this->SPECIAL = $data_attr["SPECIAL"];

		$this->hpsp(true, true);
	}

}
