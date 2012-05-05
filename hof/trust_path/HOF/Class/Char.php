<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once (DATA_BASE_CHAR);
include_once (CLASS_CHAR);

class HOF_Class_Char extends char
{

	/**
	 * 戦闘中のキャラ名,HP,SP を色を分けて表示する
	 * それ以外にも必要な物があれば表示するようにした。
	 */
	function ShowHpSp()
	{
		$output = '';

		if ($this->STATE === 1) $sub = " dmg";
		else
			if ($this->STATE === 2) $sub = " spdmg";
		//名前
		$output .= "<span class=\"bold{$sub}\">{$this->name}</span>\n";
		// チャージor詠唱
		if ($this->expect_type === 0) $output .= '<span class="charge">(charging)</span>' . "\n";
		else
			if ($this->expect_type === 1) $output .= '<span class="charge">(casting)</span>' . "\n";
		// HP,SP
		$output .= "<div class=\"hpsp\">\n";
		$sub = $this->STATE === 1 ? "dmg" : "recover";
		$output .= "<span class=\"{$sub}\">HP : {$this->HP}/{$this->MAXHP}</span><br />\n"; //HP
		$sub = $this->STATE === 1 ? "dmg" : "support";
		$output .= "<span class=\"{$sub}\">SP : {$this->SP}/{$this->MAXSP}</span>\n";
		$output .= "</div>\n"; //SP

		return $output;
	}

	function setTeamObj(&$team)
	{
		$this->team_obj = &$team;
	}

	function &getTeamObj()
	{
		return $this->team_obj;
	}

}
