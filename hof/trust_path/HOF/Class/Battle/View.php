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

	function add($output)
	{
		$this->output[] = $output;
	}

	function __toString()
	{
		$output = array_merge((array )$this->header, (array )$this->output, (array )$this->footer);

		return implode('', (array )$output);
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

	/**
	 * 戦闘画像・各キャラの残りHP残りSP等を表示
	 */
	function BattleState()
	{
		static $last;
		if ($last !== $this->battle->actions) $last = $this->battle->actions;
		else  return false;

		$this->add("<tr><td colspan=\"2\" class=\"btl_img\">\n");
		// 戦闘ステップ順に自動スクロール
		$this->add("<a name=\"s" . $this->battle->Scroll . "\"></a>\n");
		$this->add("<div style=\"width:100%;hight:100%;position:relative;\">\n");
		$this->add('<div style="position:absolute;bottom:0px;right:0px;">' . "\n");
		if ($this->battle->Scroll) $this->add("<a href=\"#s" . ($this->battle->Scroll - 1) . "\">&lt;&lt;</a>\n");
		else  $this->add("&lt;&lt;");
		$this->add("<a href=\"#s" . (++$this->battle->Scroll) . "\">&gt;&gt;</a>\n");
		$this->add('</div>');

		/*
		switch (BTL_IMG_TYPE)
		{
		case 0:
		$this->add('<div style="text-align:center">');
		$this->battle->ShowGdImage(); //画像
		$this->add('</div>');
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

		$this->add("</div>");
		$this->add("</td></tr><tr><td class=\"ttd2 break\">\n");

		$this->add("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n"); // team1-backs

		// 	左側チーム後衛
		foreach ($this->battle->team1 as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === DEAD && $char->summon == true) continue;

			if ($char->POSITION != FRONT) $char->ShowHpSp();
		}

		// 	左側チーム前衛
		$this->add("</td><td style=\"width:50%\">\n");
		foreach ($this->battle->team1 as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === DEAD && $char->summon == true) continue;

			if ($char->POSITION == FRONT) $char->ShowHpSp();
		}

		$this->add("</td></tr></tbody></table>\n");

		$this->add("</td><td class=\"ttd1 break\">\n");

		// 	右側チーム前衛
		$this->add("<table style=\"width:100%\"><tbody><tr><td style=\"width:50%\">\n");
		foreach ($this->battle->team0 as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === DEAD && $char->summon == true) continue;
			if ($char->POSITION == FRONT) $char->ShowHpSp();
		}

		// 	右側チーム後衛
		$this->add("</td><td style=\"width:50%\">\n");
		foreach ($this->battle->team0 as $char)
		{
			// 召喚キャラが死亡している場合は飛ばす
			if ($char->STATE === DEAD && $char->summon == true) continue;
			if ($char->POSITION != FRONT) $char->ShowHpSp();
		}
		$this->add("</td></tr></tbody></table>\n");

		$this->add("</td></tr>\n");
	}

	/**
	 * 戦闘の結果表示
	 */
	function ShowResult($result)
	{

		// 左側のチーム(戦闘を受けた側)
		$TotalAlive2 = 0;
		// 残りHP / 合計HP の 表示
		foreach ($this->battle->team1 as $char)
		{
			//チーム1
			if ($char->STATE !== DEAD) $TotalAlive2++;
			$TotalHp2 += $char->HP; //合計HP
			$TotalMaxHp2 += $char->MAXHP; //合計最大HP
		}

		// 右側のチーム(戦闘を仕掛けた側)
		$TotalAlive1 = 0;
		foreach ($this->battle->team0 as $char)
		{
			//チーム0
			if ($char->STATE !== DEAD) $TotalAlive1++;
			$TotalHp1 += $char->HP; //合計HP
			$TotalMaxHp1 += $char->MAXHP; //合計最大HP
		}

		// 結果を表示しない。
		if ($this->battle->NoResult)
		{
			$this->add('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break break-top">');
			//$this->add("<a name=\"s{$this->battle->Scroll}\"></a>");// スクロールの最後
			$this->add("模擬戦終了");
			$this->add("</td></tr>\n");
			$this->add('<tr><td class="teams break">' . "\n");
			// 左側チーム
			$this->add("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
			$this->add("Alive : {$TotalAlive2}/" . count($this->battle->team1) . "<br />\n");
			$this->add("TotalDamage : {$this->battle->team1_dmg}<br />\n");
			// 右側チーム
			$this->add('</td><td class="teams break">' . "\n");
			$this->add("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
			$this->add("Alive : {$TotalAlive1}/" . count($this->battle->team0) . "<br />\n");
			$this->add("TotalDamage : {$this->battle->team0_dmg}<br />\n");
			$this->add("</td></tr>\n");
			return false;
		}

		//if($this->battle->actions % BATTLE_STAT_TURNS != 0 || $result == "draw")
		//if(($this->battle->actions + 1) % BATTLE_STAT_TURNS != 0)
		$BreakTop = " break-top";
		$this->add('<tr><td colspan="2" style="text-align:center;padding:10px 0px" class="break' . $BreakTop . '">' . "\n");
		//$this->add($this->battle->actions."%".BATTLE_STAT_TURNS."<br>");
		$this->add("<a name=\"s{$this->battle->Scroll}\"></a>\n"); // スクロールの最後
		if ($result == "draw")
		{
			$this->add("<span style=\"font-size:150%\">Draw Game</span><br />\n");
		}
		else
		{
			$Team = &$this->battle->{$result};
			$TeamName = $this->battle->{$result . "_name"};
			$this->add("<span style=\"font-size:200%\">{$TeamName} Wins!</span><br />\n");
		}

		$this->add('<tr><td class="teams">' . "\n");
		// Unionとそうでないのでわける
		$this->add("HP remain : ");
		$this->add($this->battle->UnionBattle ? "????/????" : "{$TotalHp2}/{$TotalMaxHp2}");
		$this->add("<br />\n");
		/*
		if($this->battle->UnionBattle) {
		$this->add("HP remain : ????/????<br />\n");
		} else {
		$this->add("HP remain : {$TotalHp2}/{$TotalMaxHp2}<br />\n");
		}
		*/
		// 左側チーム
		$this->add("Alive : {$TotalAlive2}/" . count($this->battle->team1) . "<br />\n");
		$this->add("TotalDamage : {$this->battle->team1_dmg}<br />\n");
		if ($this->battle->team1_exp) //得た経験値
 				$this->add("TotalExp : " . $this->battle->team1_exp . "<br />\n");
		if ($this->battle->team1_money) //得たお金
 				$this->add("Funds : " . HOF_Helper_Global::MoneyFormat($this->battle->team1_money) . "<br />\n");
		if ($this->battle->team1_item)
		{
			//得たアイテム
			$this->add("<div class=\"bold\">Items</div>\n");
			foreach ($this->battle->team0_item as $itemno => $amount)
			{
				$item = HOF_Model_Data::getItemData($itemno);
				$this->add("<img src=\"" . IMG_ICON . 'item/' . $item["img"] . "\" class=\"vcent\">");
				$this->add("{$item[name]} x {$amount}<br />\n");
			}
		}

		// 右側チーム
		$this->add('</td><td class="teams">');
		$this->add("HP remain : {$TotalHp1}/{$TotalMaxHp1}<br />\n");
		$this->add("Alive : {$TotalAlive1}/" . count($this->battle->team0) . "<br />\n");
		$this->add("TotalDamage : {$this->battle->team0_dmg}<br />\n");
		if ($this->battle->team0_exp) //得た経験値
 				$this->add("TotalExp : " . $this->battle->team0_exp . "<br />\n");
		if ($this->battle->team0_money) //得たお金
 				$this->add("Funds : " . HOF_Helper_Global::MoneyFormat($this->battle->team0_money) . "<br />\n");
		if ($this->battle->team0_item)
		{
			//得たアイテム
			$this->add("<div class=\"bold\">Items</div>\n");
			foreach ($this->battle->team0_item as $itemno => $amount)
			{
				$item = HOF_Model_Data::getItemData($itemno);
				$this->add("<img src=\"" . IMG_ICON . 'item/' . $item["img"] . "\" class=\"vcent\">");
				$this->add("{$item[name]} x {$amount}<br />\n");
			}
		}
		$this->add("</td></tr>\n");
		//$this->add("</td></tr>\n");//?
	}

}
