<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_MONSTER);

class HOF_Class_Char_Mon extends HOF_Class_Char
{

	/**
	 * モンスター専用の変数
	 */
	var $monster = true;

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
	var $summon;

	function __construct($data)
	{
		$this->_extend_init();

		$this->SetCharData($data);
	}

	function _extend_init()
	{
		$this->extend('HOF_Class_Char_Pattern');
		$this->extend('HOF_Class_Char_View');
		$this->extend('HOF_Class_Char_Battle_Effect');
	}

	/**
	 * キャラデータの保存
	 */
	function SaveCharData()
	{
		// モンスターは保存しない。
		return false;
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
			if ($this->summon) return true;
			if ($mes) print ($this->Name(bold) . ' <span class="recover">revived</span>!<br />' . "\n");
			$this->STATE = 0;
			return true;
		}
		if ($this->STATE === STATE_POISON)
		{
			// 毒状態
			if ($mes) print ($this->Name(bold) . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = 0;
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
	function SetCharData($monster)
	{

		$this->no = $monster["no"];

		$this->name = $monster["name"];
		$this->level = $monster["level"];

		if ($monster["img"]) $this->img = $monster["img"];

		$this->str = $monster["str"];
		$this->int = $monster["int"];
		$this->dex = $monster["dex"];
		$this->spd = $monster["spd"];
		$this->luk = $monster["luk"];

		$this->maxhp = $monster["maxhp"];
		$this->hp = $monster["hp"];
		$this->maxsp = $monster["maxsp"];
		$this->sp = $monster["sp"];

		$this->position = $monster["position"];
		$this->guard = $monster["guard"];

		// モンスター専用
		//$this->monster		= $monster["monster"];
		$this->monster = true;
		$this->summon = $monster["summon"];
		$this->exphold = $monster["exphold"];
		$this->moneyhold = $monster["moneyhold"];
		$this->itemdrop = $monster["itemdrop"];
		$this->atk = $monster["atk"];
		$this->def = $monster["def"];
		$this->SPECIAL = $monster["SPECIAL"];

		$this->pattern = $monster["pattern"];
	}

	/**
	 * 戦闘用の変数
	 */
	function SetBattleVariable($team = false)
	{
		if ($this->_cache_char_['init'][__FUNCTION__]) return false;

		$this->_cache_char_['init'][__FUNCTION__] = true;

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
