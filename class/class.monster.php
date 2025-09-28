<?php

include_once("class.char.php");

/**
 * モンスタークラス
 * Monster class extending character class with monster-specific properties and methods.
 */
class monster extends char {

	// モンスター専用の変数
	var $monster = true;
	var $exphold; //経験値
	var $moneyhold; //お金
	var $itemdrop; //落とすアイテム
	var $summon;

	/**
	 * モンスターコンストラクタ
	 * Monster constructor to initialize monster data.
	 *
	 * @param object $data Character data array
	 */
	function monster($data) {
		$this->SetCharData($data);
	}

	/**
	 * キャラデータの保存
	 * Save character data method for monsters, which returns false as monsters are not saved.
	 *
	 * @return bool False indicating that monsters are not saved.
	 */
	function SaveCharData() {
		// モンスターは保存しない。
		return false;
	}

	/**
	 * 生存状態にする
	 * Get the character to normal state method. If already alive, return true. 
	 * If dead and summonable, revive it. If poisoned, cure it.
	 *
	 * @param bool $mes Print message if true
	 * @return bool True if state is set to normal, false otherwise.
	 */
	function GetNormal($mes = false) {
		if ($this->STATE === ALIVE)
			return true;
		if ($this->STATE === DEAD) { //死亡状態
			if ($this->summon) return true;
			if ($mes)
				print($this->Name(bold) . ' <span class="recover">revived</span>!<br />' . "\n");
			$this->STATE = 0;
			return true;
		}
		if ($this->STATE === POISON) { //毒状態
			if ($mes)
				print($this->Name(bold) . "'s <span class=\"spdmg\">poison</span> has cured.<br />\n");
			$this->STATE = 0;
			return true;
		}
	}

	/**
	 * しぼーしてるかどうか確認する
	 * Check if the character is dead. If HP is less than 1 and not already dead, set state to dead and reset HP.
	 *
	 * @return bool True if character is marked as dead, false otherwise.
	 */
	function CharJudgeDead() {
		if ($this->HP < 1 && $this->STATE !== DEAD) { //しぼー
			$this->STATE = DEAD;
			$this->HP = 0;
			$this->ResetExpect();
			return true;
		}
	}

	/**
	 * キャラの変数をセットする
	 * Set character data method for monsters, initializing various attributes.
	 *
	 * @param object $monster Character data array
	 */
	function SetCharData($monster) {
		$this->name = $monster["name"];
		$this->level = $monster["level"];

		if ($monster["img"])
			$this->img = $monster["img"];

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

		if (is_array($monster["judge"]))
			$this->judge = $monster["judge"];
		if (is_array($monster["quantity"]))
			$this->quantity = $monster["quantity"];
		if (is_array($monster["action"]))
			$this->action = $monster["action"];

		//モンスター専用
		$this->monster = true;
		$this->summon = $monster["summon"];
		$this->exphold = $monster["exphold"];
		$this->moneyhold = $monster["moneyhold"];
		$this->itemdrop = $monster["itemdrop"];
		$this->atk = $monster["atk"];
		$this->def = $monster["def"];
		$this->SPECIAL = $monster["SPECIAL"];
	}

	/**
	 * 戦闘用の変数
	 * Set battle variables method to prepare character for battle.
	 *
	 * @param bool $team Team affiliation of the character
	 */
	function SetBattleVariable($team = false) {
		if (isset($this->IMG))
			return false;

		$this->team = $team;
		$this->IMG = $this->img;
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
		$this->STATE = ALIVE;

		$this->expect = false;
		$this->ActCount = 0;
		$this->JdgCount = array();
	}
}

?>