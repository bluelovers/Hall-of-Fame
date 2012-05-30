<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_MONSTER);

class HOF_Class_Char_Type_Mon extends HOF_Class_Char_Abstract
{

	/**
	 * モンスター専用の変数
	 */
	//var $monster = true;

	/**
	 * 経験値
	 */
	var $exphold;

	/**
	 * お金
	 */
	var $moneyhold;

	/**
	 * 落とすアイテム
	 */
	var $itemdrop;
	//var $summon;

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

		$this->no = $data_attr["no"];

		$this->level = $data_attr["level"];

		//$this->img = $data_attr["img"];

		$this->str = $data_attr["str"];
		$this->int = $data_attr["int"];
		$this->dex = $data_attr["dex"];
		$this->spd = $data_attr["spd"];
		$this->luk = $data_attr["luk"];

		$this->maxhp = $data_attr["maxhp"];
		$this->hp = $data_attr["hp"];
		$this->maxsp = $data_attr["maxsp"];
		$this->sp = $data_attr["sp"];

		$this->position = $data_attr["position"];
		$this->guard = $data_attr["guard"];

		// モンスター専用
		//$this->monster		= $data_attr["monster"];
		//$this->monster = true;



		$this->exphold = $data_attr["exphold"];
		$this->moneyhold = $data_attr["moneyhold"];
		$this->itemdrop = $data_attr["itemdrop"];
		$this->atk = $data_attr["atk"];
		$this->def = $data_attr["def"];
		$this->SPECIAL = $data_attr["SPECIAL"];

		$this->pattern = $data_attr["pattern"];
	}

	/**
	 * 戦闘用の変数
	 */
	function setBattleVariable($team = false)
	{
		if ($this->_cache_char_['init'][__FUNCTION__ ]) return false;

		$this->_cache_char_['init'][__FUNCTION__ ] = true;

		$this->team = $team; //これ必要か?

		$this->MAXHP = $this->maxhp;
		$this->HP = $this->hp;
		$this->MAXSP = $this->maxsp;
		$this->SP = $this->sp;
		$this->STR = $this->str + $this->P_STR;
		$this->INT = $this->int + $this->P_INT;
		$this->DEX = $this->dex + $this->P_DEX;
		$this->SPD = $this->spd + $this->P_SPD;
		$this->LUK = $this->luk + $this->P_LUK;
		$this->POSITION = $this->position;

		$this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
	}

}
