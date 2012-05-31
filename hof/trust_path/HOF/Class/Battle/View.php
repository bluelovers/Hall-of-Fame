<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_View
{

	protected $battle = null;

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		$this->battle = &$battle;
	}

	/**
	 * 戦闘開始した時の平均レベルや合計HP等を計算・表示
	 * 戦闘の経緯は一つの表で構成されるうっう
	 */
	function BattleHeader()
	{
		// チーム0
		foreach ($this->battle->teams[TEAM_0]['team'] as $char)
		{
			// 合計LV
			$team0_total_lv += $char->level;
			// 合計HP
			$team0_total_hp += $char->HP;
			// 合計最大HP
			$team0_total_maxhp += $char->MAXHP;
		}

		// チーム0平均LV
		$team0_avelv = round($team0_total_lv / count($this->battle->teams[TEAM_0]['team']) * 10) / 10;
		$this->battle->teams[TEAM_0]['ave_lv'] = $team0_avelv;

		// チーム1
		foreach ($this->battle->teams[TEAM_1]['team'] as $char)
		{

			$team1_total_lv += $char->level;
			$team1_total_hp += $char->HP;
			$team1_total_maxhp += $char->MAXHP;
		}
		$team1_avelv = round($team1_total_lv / count($this->battle->teams[TEAM_1]['team']) * 10) / 10;
		$this->battle->teams[TEAM_1]['ave_lv'] = $team1_avelv;

		if ($this->battle->UnionBattle)
		{
			$team1_total_hp = '????';
			$team1_total_maxhp = '????';
		}

		$this->battle->teams[TEAM_0]['team']->data('isUnion') && $css_union[TEAM_0] = 'g_union';
		$this->battle->teams[TEAM_1]['team']->data('isUnion') && $css_union[TEAM_1] = 'g_union';

		echo <<< EOM
<table style="width:100%;" cellspacing="0" class="battle_frame">
	<tbody>
		<tr>
			<td class="teams">
				<div class="bold {$css_union[TEAM_1]}">
					{$this->battle->teams[TEAM_1]['name']}
				</div>
				Total Lv : {$team1_total_lv} <br>
				Average Lv : {$team1_avelv} <br>
				Total HP : {$team1_total_hp} / {$team1_total_maxhp} </td>
			<td class="teams ttd1">
				<div class="bold {$css_union[TEAM_0]}">
					{$this->battle->teams[TEAM_0]['name']}
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
		echo '</tbody></table>';
	}

	/**
	 * 戦闘画像・各キャラの残りHP残りSP等を表示
	 */
	function BattleState()
	{
		static $last;
		if ($last !== $this->battle->actions) $last = $this->battle->actions;
		else  return false;

		echo("<tr><td colspan=\"2\" class=\"btl_img\">\n");
		// 戦闘ステップ順に自動スクロール
		echo("<a name=\"s" . $this->battle->Scroll . "\"></a>\n");
		echo("<div style=\"width:100%;hight:100%;position:relative;\">\n");
		echo('<div style="position:absolute;bottom:0px;right:50px;z-index: 100;">' . "\n");
		if ($this->battle->Scroll) echo("<a href=\"#s" . ($this->battle->Scroll - 1) . "\">&lt;&lt;</a>\n");
		else  echo("&lt;&lt;");
		echo("<a href=\"#s" . (++$this->battle->Scroll) . "\">&gt;&gt;</a>\n");
		echo('</div>');

		/*
		switch (BTL_IMG_TYPE)
		{
		case 0:
		echo('<div style="text-align:center">');
		$this->battle->ShowGdImage(); //画像
		echo('</div>');
		break;
		case 1:
		case 2:
		$this->battle->ShowCssImage(); //画像
		break;
		}
		*/
		// bluelovers
		$this->battle->outputImage();
		// bluelovers

		echo("</div>");
		echo("</td></tr><tr><td class=\"ttd2 break\">\n");

		echo("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n"); // team1-backs

		// 	左側チーム後衛
		foreach ($this->battle->teams[TEAM_1]['team'] as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === STATE_DEAD && $char->isSummon()) continue;

			if ($char->POSITION != POSITION_FRONT) echo $char->ShowHpSp();
		}

		// 	左側チーム前衛
		echo("</td><td style=\"width:50%\">\n");
		foreach ($this->battle->teams[TEAM_1]['team'] as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === STATE_DEAD && $char->isSummon()) continue;

			if ($char->POSITION == POSITION_FRONT) echo $char->ShowHpSp();
		}

		echo("</td></tr></tbody></table>\n");

		echo("</td><td class=\"ttd1 break\">\n");

		// 	右側チーム前衛
		echo("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");
		foreach ($this->battle->teams[TEAM_0]['team'] as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === STATE_DEAD && $char->isSummon()) continue;
			if ($char->POSITION == POSITION_FRONT) echo $char->ShowHpSp();
		}

		// 	右側チーム後衛
		echo("</td><td style=\"width:50%\">\n");
		foreach ($this->battle->teams[TEAM_0]['team'] as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === STATE_DEAD && $char->isSummon()) continue;
			if ($char->POSITION != POSITION_FRONT) echo $char->ShowHpSp();
		}
		echo("</td></tr></tbody></table>\n");

		echo("</td></tr>\n");
	}

	/**
	 * 戦闘の結果表示
	 */
	function ShowResult($result)
	{

		// 左側のチーム(戦闘を受けた側)
		$TotalAlive2 = 0;
		// 残りHP / 合計HP の 表示
		foreach ($this->battle->teams[TEAM_1]['team'] as $char)
		{
			//チーム1
			if ($char->STATE !== STATE_DEAD) $TotalAlive2++;
			$TotalHp2 += $char->HP; //合計HP
			$TotalMaxHp2 += $char->MAXHP; //合計最大HP
		}

		// 右側のチーム(戦闘を仕掛けた側)
		$TotalAlive1 = 0;
		foreach ($this->battle->teams[TEAM_0]['team'] as $char)
		{
			//チーム0
			if ($char->STATE !== STATE_DEAD) $TotalAlive1++;
			$TotalHp1 += $char->HP; //合計HP
			$TotalMaxHp1 += $char->MAXHP; //合計最大HP
		}

		// 結果を表示しない。
		if ($this->battle->NoResult)
		{
			echo('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break break-top">');
			//echo("<a name=\"s{$this->battle->Scroll}\"></a>");// スクロールの最後
			echo("模擬戦終了");
			echo("</td></tr>\n");
			echo('<tr><td class="teams break">' . "\n");
			// 左側チーム
			echo("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
			echo("Alive : {$TotalAlive2}/" . count($this->battle->teams[TEAM_1]['team']) . "<br />\n");
			echo("TotalDamage : {$this->battle->teams[TEAM_1]['dmg']}<br />\n");
			// 右側チーム
			echo('</td><td class="teams break">' . "\n");
			echo("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
			echo("Alive : {$TotalAlive1}/" . count($this->battle->teams[TEAM_0]['team']) . "<br />\n");
			echo("TotalDamage : {$this->battle->teams[TEAM_0]['dmg']}<br />\n");
			echo("</td></tr>\n");
			return false;
		}

		//if($this->battle->actions % BATTLE_STAT_TURNS != 0 || $result == "draw")
		//if(($this->battle->actions + 1) % BATTLE_STAT_TURNS != 0)
		$BreakTop = " break-top";
		echo('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break' . $BreakTop . '">' . "\n");
		//echo($this->battle->actions."%".BATTLE_STAT_TURNS."<br>");
		echo("<a name=\"s{$this->battle->Scroll}\"></a>\n"); // スクロールの最後
		if ($result === BATTLE_DRAW)
		{
			echo("<span style=\"font-size:150%\">Draw Game</span><br />\n");
		}
		else
		{
			$Team = &$this->battle->teams[$result]['team'];
			$TeamName = $this->battle->teams[$result]['name'];
			echo("<span style=\"font-size:200%\">{$TeamName} Wins!</span><br />\n");
		}

		echo('<tr><td class="teams">' . "\n");
		// Unionとそうでないのでわける
		echo("HP remain : ");
		echo($this->battle->UnionBattle ? "????/????" : "{$TotalHp2}/{$TotalMaxHp2}");
		echo("<br />\n");
		/*
		if($this->battle->UnionBattle) {
		echo("HP remain : ????/????<br />\n");
		} else {
		echo("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
		}
		*/
		// 左側チーム
		echo("Alive : {$TotalAlive2}/" . count($this->battle->teams[TEAM_1]['team']) . "<br />\n");
		echo("TotalDamage : {$this->battle->teams[TEAM_1]['dmg']}<br />\n");
		if ($this->battle->teams[TEAM_1]['exp']) //得た経験値
 				echo("TotalExp : " . $this->battle->teams[TEAM_1]['exp'] . "<br />\n");
		if ($this->battle->teams[TEAM_1]['money']) //得たお金
 				echo("Funds : " . HOF_Helper_Global::MoneyFormat($this->battle->teams[TEAM_1]['money']) . "<br />\n");
		if ($this->battle->teams[TEAM_1]['item'])
		{
			//得たアイテム
			echo("<div class=\"bold\">Items</div>\n");
			foreach ($this->battle->teams[TEAM_0]['item'] as $itemno => $amount)
			{
				$item = HOF_Model_Data::getItemData($itemno);
				echo("<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM ) . "\" class=\"vcent\">");
				echo("{$item[name]} x {$amount}<br />\n");
			}
		}

		// 右側チーム
		echo('</td><td class="teams">');
		echo("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
		echo("Alive : {$TotalAlive1}/" . count($this->battle->teams[TEAM_0]['team']) . "<br />\n");
		echo("TotalDamage : {$this->battle->teams[TEAM_0]['dmg']}<br />\n");
		if ($this->battle->teams[TEAM_0]['exp']) //得た経験値
 				echo("TotalExp : " . $this->battle->teams[TEAM_0]['exp'] . "<br />\n");
		if ($this->battle->teams[TEAM_0]['money']) //得たお金
 				echo("Funds : " . HOF_Helper_Global::MoneyFormat($this->battle->teams[TEAM_0]['money']) . "<br />\n");
		if ($this->battle->teams[TEAM_0]['item'])
		{
			//得たアイテム
			echo("<div class=\"bold\">Items</div>\n");
			foreach ($this->battle->teams[TEAM_0]['item'] as $itemno => $amount)
			{
				$item = HOF_Model_Data::getItemData($itemno);
				echo("<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM ) . "\" class=\"vcent\">");
				echo("{$item[name]} x {$amount}<br />\n");
			}
		}
		echo("</td></tr>\n");
		//echo("</td></tr>\n");//?
	}

}
