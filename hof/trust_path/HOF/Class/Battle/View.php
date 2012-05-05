<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_View extends HOF_Class_Array
{

	protected $battle = null;

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		parent::__construct($this->_data_default);

		$this->battle = &$battle;
	}

	function output()
	{
		return $this->__toString();
	}

	function __toString()
	{
		return is_array($this->output) ? implode('', (array )$this->output) : (string )$this->output;
	}

	/**
	 * 戦闘開始した時の平均レベルや合計HP等を計算・表示
	 * 戦闘の経緯は一つの表で構成されるうっう
	 */
	function BattleHeader()
	{
		// チーム0
		foreach ($this->battle->team0 as $char)
		{
			// 合計LV
			$team0_total_lv += $char->level;
			// 合計HP
			$team0_total_hp += $char->HP;
			// 合計最大HP
			$team0_total_maxhp += $char->MAXHP;
		}

		// チーム0平均LV
		$team0_avelv = round($team0_total_lv / count($this->battle->team0) * 10) / 10;
		$this->battle->team0_ave_lv = $team0_avelv;

		// チーム1
		foreach ($this->battle->team1 as $char)
		{

			$team1_total_lv += $char->level;
			$team1_total_hp += $char->HP;
			$team1_total_maxhp += $char->MAXHP;
		}
		$team1_avelv = round($team1_total_lv / count($this->battle->team1) * 10) / 10;
		$this->battle->team1_ave_lv = $team1_avelv;

		if ($this->battle->UnionBattle)
		{
			$team1_total_hp = '????';
			$team1_total_maxhp = '????';
		}

		$this->header[] = <<< EOM
<table style="width:100%;" cellspacing="0">
	<tbody>
		<tr>
			<td class="teams">
				<div class="bold">
					{$this->battle->team1_name}
				</div>
				Total Lv : {$team1_total_lv} <br>
				Average Lv : {$team1_avelv} <br>
				Total HP : {$team1_total_hp} / {$team1_total_maxhp} </td>
			<td class="teams ttd1">
				<div class="bold">
					{$this->battle->team0_name}
				</div>
				Total Lv : {$team0_total_lv} <br>
				Average Lv : {$team0_avelv} <br>
				Total HP : {$team0_total_hp} / {$team0_total_maxhp} </td>
		</tr>
EOM
		;
	}

	/**
	 * 戦闘終了時に表示
	 */
	function BattleFoot()
	{
		$this->footer[] = '</tbody></table>';
	}

}
