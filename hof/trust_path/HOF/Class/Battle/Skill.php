<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Battle_Skill
{
	protected $battle;

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		$this->battle = &$battle;
	}

	function UseSkill($skill_no, &$JudgedTarget, &$My, &$MyTeam, &$Enemy)
	{
		/**
		 * 技データ読む
		 */
		$skill = HOF_Model_Data::getSkill($skill_no);

		if ($My->monster)
		{
			$skill["sp"] *= 0.7;

			if (in_array($skill_no, array(3040, 5030, 5063)))
			{
				$skill["sp"] *= 0.1;
			}

			$skill["sp"] = (int)$skill["sp"];
		}

		// 武器タイプ不一致
		if ($skill["limit"] && !$My->monster)
		{
			if (!$skill["limit"][$My->WEAPON])
			{
				echo('<span class="u">' . $My->Name('bold'));
				echo('<span class="dmg"> Failed </span>to ');
				echo("<img src=\"" . HOF_Class_Icon::getImageUrl($skill["img"], HOF_Class_Icon::IMG_SKILL) . "\" class=\"vcent\"/>");
				echo($skill[name] . "</span><br />\n");
				//echo($My->Name('bold')." Failed to use ".$skill["name"]."<br />\n");
				echo("(Weapon type doesnt match)<br />\n");
				$My->DelayReset(); // 行動順をリセット
				return true;
			}
		}

		// SP不足
		if ($My->SP < $skill["sp"])
		{
			echo($My->Name('bold') . " failed to " . $skill["name"] . "(SP shortage)");
			if ($My->expect)
			{ //もし詠唱や貯め途中でSPが不足した場合
				$My->ResetExpect();
			}
			$My->DelayReset(); // 行動順をリセット
			return true;
		}

		// もし "詠唱" や "貯め" が必要な技なら(+詠唱開始してない場合)→詠唱,貯め開始
		if ($skill["charge"]["0"] && $My->expect === false)
		{
			// こちらは貯めと詠唱を開始する場合 /////////////////////
			// 物理か魔法によって文を変える
			if ($skill["type"] == 0)
			{ //物理
				echo('<span class="charge">' . $My->Name('bold') . ' start charging.</span>');
				$My->expect_type = EXPECT_CHARGE;
			}
			else
			{ //魔法
				echo('<span class="charge">' . $My->Name('bold') . ' start casting.</span>');
				$My->expect_type = EXPECT_CAST;
			}
			$My->expect = $skill_no; //詠唱・貯め完了と同時に使用する技
			// ↓使ってないのでコメントにした。
			//$My->target_expect	= $JudgedTarget;//一応ターゲットも保存
			//詠唱・貯め時間の設定。
			$My->DelayByRate($skill["charge"]["0"], $this->battle->delay, 1);
			echo("<br />\n");

			// 戦闘の総行動回数を減らす(貯めor詠唱 は行動に入れない)
			$this->battle->actions--;

			return true; //ディレイ変更したからリセットしないように。
		}
		else
		{
			// 技を実際に使用する ///////////////////////////////////

			// 行動回数をプラスする
			$My->ActCount++;

			// 行動内容の表示(行動する)
			echo('<div class="u">' . $My->Name('bold'));
			echo("<img src=\"" . HOF_Class_Icon::getImageUrl($skill["img"], HOF_Class_Icon::IMG_SKILL) . "\" class=\"vcent\"/>");
			echo($skill[name] . "</div>\n");

			// 魔法陣を消費(味方)
			if ($skill["MagicCircleDeleteTeam"])
			{
				if ($this->battle->MagicCircleDelete($My->team, $skill["MagicCircleDeleteTeam"]))
				{
					echo($My->Name('bold') . '<span class="charge"> use MagicCircle x' . $skill["MagicCircleDeleteTeam"] . '</span><br />' . "\n");
					// 魔法陣消費失敗
				}
				else
				{
					echo('<span class="dmg">failed!(MagicCircle isn\'t enough)</span><br />' . "\n");
					$My->DelayReset(); // 行動順をリセット
					return true;
				}
			}

			// SPの消費(この位置だと貯め・詠唱完了と同時に消費する)
			$My->SpDamage($skill["sp"], false);

			// チャージ(詠唱)完了と同時に使用する技の情報を消す。
			if ($My->expect) $My->ResetExpect();

			// HP犠牲技の場合(Sacrifice)
			if ($skill["sacrifice"]) $My->SacrificeHp($skill["sacrifice"]);

		}

		// ターゲットを選ぶ(候補)
		if ($skill["target"]["0"] == "friend"):
			$candidate = &$MyTeam;
		elseif ($skill["target"]["0"] == "enemy"):
			$candidate = &$Enemy;
		elseif ($skill["target"]["0"] == "self"):
			$candidate[] = &$My;
		elseif ($skill["target"]["0"] == "all"):
			//$candidate	= $MyTeam + $Enemy;//???
			$candidate = array_merge_recursive(&$MyTeam, &$Enemy); //結合の後,並びをランダムにした方がいい??
		endif;

		// 候補から使用する対象を選ぶ → (スキル使用)

		// 単体に使用
		if ($skill["target"]["1"] == "individual")
		{
			$target = &$this->battle->SelectTarget($candidate, $skill); //対象を選択
			if ($defender = &$this->battle->Defending($target, $candidate, $skill)) //守りに入るキャラ
 					$target = &$defender;
			for ($i = 0; $i < $skill["target"]["2"]; $i++)
			{ //単体に複数回実行
				$dmg = $this->battle->SkillEffect($skill, $skill_no, &$My, &$target);
				$this->battle->AddTotalDamage($MyTeam, $dmg);
			}

			// 複数に使用
		}
		else
			if ($skill["target"]["1"] == "multi")
			{
				for ($i = 0; $i < $skill["target"]["2"]; $i++)
				{
					$target = &$this->battle->SelectTarget($candidate, $skill); //対象を選択
					if ($defender = &$this->battle->Defending($target, $candidate, $skill)) //守りに入るキャラ
 							$target = &$defender;
					$dmg = $this->battle->SkillEffect($skill, $skill_no, &$My, &$target);
					$this->battle->AddTotalDamage($MyTeam, $dmg);
				}

				// 全体に使用
			}
			else
				if ($skill["target"]["1"] == "all")
				{
					foreach ($candidate as $key => $char)
					{
						$target = &$candidate[$key];
						//if($char->STATE === STATE_DEAD) continue;//死亡者はパス。
						if ($skill["priority"] != "Dead")
						{ //一時的に。
							if ($char->STATE === STATE_DEAD) continue; //死亡者はパス。
						}
						// 全体攻撃は守りに入れない(とする)
						for ($i = 0; $i < $skill["target"]["2"]; $i++)
						{
							$dmg = $this->battle->SkillEffect($skill, $skill_no, &$My, &$target);
							$this->battle->AddTotalDamage($MyTeam, $dmg);
						}
					}
				}

		// 使用後使用者に影響する効果等
		if ($skill["umove"]) $My->Move($skill["umove"]);

		// 攻撃対象になったキャラ達がどうなったか確かめる(とりあえずHP=0になったかどうか)。
		if ($skill["sacrifice"])
		{ // Sacri系の技を使った場合。
			$Sacrier[] = &$My;
			$this->battle->JudgeTargetsDead($Sacrier);
		}
		list($exp, $money, $itemdrop) = $this->battle->JudgeTargetsDead($candidate); //又、取得する経験値を得る

		$this->battle->GetExp($exp, $MyTeam);
		$this->battle->GetItem($itemdrop, $MyTeam);
		$this->battle->GetMoney($money, $MyTeam);

		// 技の使用等でSPDが変化した場合DELAYを再計算する。
		if ($this->battle->ChangeDelay) $this->battle->SetDelay();

		// 行動後の硬直(があれば設定する)
		if ($skill["charge"]["1"])
		{
			$My->DelayReset();
			echo($My->Name('bold') . " Delayed");
			$My->DelayByRate($skill["charge"]["1"], $this->battle->delay, 1);
			echo("<br />\n");
			return false;
		}

		// 最後に行動順をリセットする。
		$My->DelayReset();
	}

}
