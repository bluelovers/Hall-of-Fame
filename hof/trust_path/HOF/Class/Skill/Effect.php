<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * スキル使用対象者(被側)は基本的に
 * ■全員
 * ■ランダム(に複数回)
 * ■(味方の)HPが低い順から回数=人数分
 * ■しぼー者(でランダム)
 * の4種類?
 * ※敵のHPが低い順から狙えると面白くないし。

 * まず、先にターゲットを決める。
 * 候補(味方,敵,自分,味方+敵)
 * 候補より 単体,複数,全体 を 優先順位に従って(単体,複数のみ)決める

 * 攻撃なら、ガードが入るかどうか判定。
 * 攻撃。

 * 支援→そのまま支援
 */
//class HOF_Class_Skill_Effect extends ClassSkillEffect
class HOF_Class_Skill_Effect
{
	protected $battle = null;

	static $map_status = array(

		'attr' => array(
			'STR',
			'INT',
			'DEX',
			'SPD',
			'LUK',

			'MAXHP',
			'MAXSP',

			'ATK',
			'MATK',
			'DEF',
			'MDEF',
			),

		'action' => array(
			'Plus',
			'Up',
			'Down',
			),

		);

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		$this->battle = &$battle;
	}

	/**
	 * 使用者が対象者にスキルを使う
	 */
	function SkillEffect($skill, $skill_no, &$char, &$target)
	{
		if ($target === false)
		{
			print ("No target.Failed!<br />\n");
			return false;
		}

		//スキルを実際に使用する
		switch ($skill_no):

			case 1020: // ManaBreak
				$dmg = self::CalcBasicDamage($skill, $char, $target);
				self::DamageSP($target, $dmg);
				break;

			case 1021: // SoulBreak
				$dmg = self::CalcBasicDamage($skill, $char, $target);
				self::DamageHP($target, $dmg);
				self::DamageSP($target, $dmg);
				break;

			case 1022: //ChargeAttack
				if ($char->POSITION != POSITION_FRONT) $option["multiply"] = 4;
				$dmg = self::CalcBasicDamage($skill, $char, $target, $option);
				self::DamageHP($target, $dmg);
				$char->Move("front");
				break;

			case 1023: //Hit&Away
				if ($char->POSITION == POSITION_FRONT) $option["multiply"] = 3;
				$dmg = self::CalcBasicDamage($skill, $char, $target, $option);
				self::DamageHP($target, $dmg);
				$char->Move("back");
				break;

			case 1024: //LifeDivision
				$value = round(abs($target->HP - $char->HP) * 0.5); // 差分
				if ($char->HP <= $target->HP)
				{
					if (1000 <= $value)
					{
						print ("※値が大きすぎて補正されました。<br />\n");
						$value = 500;
					}
					self::DamageHP($target, $value);
					self::RecoverHP($char, $value);
				}
				else
				{
					self::DamageHP($char, $value);
					self::RecoverHP($target, $value);
				}
				break;

			case 1025: //ManaDivision
				$value = round(abs($target->SP - $char->SP) * 0.5); // 差分
				if ($char->SP <= $target->SP)
				{
					if (1000 <= $value)
					{
						print ("※値が大きすぎて補正されました。<br />\n");
						$value = 500;
					}
					self::DamageSP($target, $value);
					self::RecoverSP($char, $value);
				}
				else
				{
					self::DamageSP($char, $value);
					self::RecoverSP($target, $value);
				}
				break;

			case 1116: //Punish
				$dmg = $char->MAXHP - $char->HP;
				self::DamageHP($target, $dmg);
				break;

			case 1119: //Possession
				if ($char === $target) break;
				$this->StatusChanges($skill, $target);
				break;

			case 1200: //PoisonBlow
				if ($target->STATE === STATE_POISON)
				{
					$option["multiply"] = 6;
					print ("Damage x6!<br />\n");
				}
				$dmg = self::CalcBasicDamage($skill, $char, $target, $option);
				self::DamageHP($target, $dmg);
				break;

			case 1208: //PoisonInvasion
				$Rate = (log(($char->INT + 22) / 10) - 0.8) / 0.85;
				//$Rate	= 0.81 + (pow(pow($char->INT*0.1,2.05),21/40))/10;
				$target->PoisonDamage($Rate);
				break;

			case 1209: //TransPoison
				if ($target->STATE !== STATE_POISON) return false;
				$this->StatusChanges($skill, $target);
				$target->GetNormal(true);
				break;

			case 1220: //AntiPoisoning
				$target->GetPoisonResist(50);
				break;

			case 2030: // LifeDrain
			case 2031: // LifeSqueeze
				if ($char == $target) return false; //自分から自分は吸収しない。
				$dmg = self::CalcBasicDamage($skill, $char, $target);
				self::AbsorbHP($target, $dmg, $char, $dmg);
				break;

			case 2032: // DeathKnell
				$p = mt_rand(1, 100);
				if (50 < $p) $target->HP = 0;
				else  print ("Failed!<br />");
				return true;

			case 2055: // SoulRevenge
				$option["multiply"] = $this->battle->CountDead($char->team()) + 1;
				print ("Damage x" . $option["multiply"] . "!<br />\n");
				$dmg = self::CalcBasicDamage($skill, $char, $target, $option);
				self::DamageHP($target, $dmg);
				break;

			case 2056: // ZombieRevival
				//print("who? : ".$target->Name().":".$target->STATE."<br />\n");
				if ($target->STATE !== STATE_DEAD) break;
				$target->GetNormal(true);
				$this->StatusChanges($skill, $target);
				self::RecoverHP($target, $target->MAXHP);
				break;

			case 2057: // SelfMetamorphorse
				if (60 < $target->HpPercent() || $target->SPECIAL["Metamo"])
				{
					print ("Failed!<br />\n");
					break;
				}
				print ($target->GetSpecial("Metamo", true));

				if ($target->gender == GENDER_GIRL)
				{
					$target->img = "mon_149";
				}
				else
				{
					$target->img = "mon_110";
				}

				$this->StatusChanges($skill, $target);
				self::RecoverHP($target, round($target->MAXHP / 2));
				break;

				// SP吸収系
			case 2090: //EneryRob
			case 2091: //EneryCollect
				if ($char == $target) return false; //自分から自分は吸収しない。
				$dmg = self::CalcBasicDamage($skill, $char, $target, array("pierce" => 1));
				self::AbsorbSP($target, $dmg, $char, $dmg);
				break;

				// チャージ(詠唱)中のキャラのみを対象にする
			case 2110:
			case 2111:
				if ($target->expect === false) break;
				$this->DelayChar($target, $skill);
				break;

				/*
				// HP回復系
				case 3000:// Healing
				case 3001:// PowerHeal
				case 3002:// PartyHeal
				case 3003:// QuickHeal
				case 3004:// SmartHeal
				case 3103://
				case 5007:// Heal(Mons)
				case 5055:// RadiateHeating
				$heal	= self::CalcRecoveryValue($skill,$char,$target);
				self::RecoverHP($target,$heal);
				$this->StatusChanges($skill,$target);
				break;
				*/

			case 3005: // ProgressiveHeal
				$heal = self::CalcRecoveryValue($skill, $char, $target);
				$Rate = ($target->HP / $target->MAXHP) * 100;
				if ($Rate <= 30)
				{
					$heal *= 2;
					print ("heal x2!<br />");
				}
				self::RecoverHP($target, $heal);
				break;

			case 3010: // ManaRecharge(自己SP回復)
				$SpRec = ceil($target->MAXSP * 3 / 10);
				self::RecoverSP($target, $SpRec);
				break;

			case 3011: // HiManaRecharge
				$SpRec = ceil($target->MAXSP * 5 / 10);
				self::RecoverSP($target, $SpRec);
				break;

			case 3012: // LifeConvert
				$HpDmg = ceil($target->MAXHP * 3 / 10);
				self::DamageHP2($target, $HpDmg);
				$SpRec = ceil($target->MAXSP * 7 / 10);
				self::RecoverSP($target, $SpRec);
				break;

			case 3013: // EnergyExchange
				$HpRate = floor($target->HP / $target->MAXHP * 100);
				$SpRate = floor($target->SP / $target->MAXSP * 100);
				print ($target->Name(true)." exchanged rate of HP and SP.<br />");
				print ("HP: {$target->HP}(" . $HpRate . "%) to ");
				$target->HP = round($SpRate / 100 * $target->MAXHP);
				print ("{$target->HP}(" . $SpRate . "%)<br />");
				print ("SP: {$target->SP}(" . $SpRate . "%) to ");
				$target->SP = round($HpRate / 100 * $target->MAXSP);
				print ("{$target->SP}(" . $HpRate . "%)<br />");
				break;

			case 3020: // ManaExtend
				$target->MAXSP = round($target->MAXSP * 1.2);
				print ($target->Name('bold') . "'s MAXSP extended to {$target->MAXSP}.<br />\n");
				break;
				/*
				case 3030: // Reflesh
				if($target->STATE == STATE_DEAD) break;
				if($target->STATE == STATE_POISON)
				$target->GetNormal(true);
				$heal	= self::CalcRecoveryValue($skill,$char,$target);
				self::RecoverHP($target,$heal);
				break;
				*/
				//	蘇生系の技。
			case 3040: // Resurrection
			case 5030: // SoulRestor
			case 5063: // WakeUp
				if ($target->STATE !== 1) break;
				$heal = self::CalcRecoveryValue($skill, $char, $target);
				$target->GetNormal(true);
				self::RecoverHP($target, $heal);
				break;

			case 3050: //Quick
				if ($target == $char) return false;
				if ($target->expect) return false;
				//$target->Quick($this->battle->delay + 1);
				print ("<span class=\"support\">" . $target->Name("bold") . " got quicked!</span>");
				$target->DelayCut(101, $this->battle->delay, 1);
				print ("<br />\n");
				break;

			case 3055: //CastAsist
				if ($target->expect && $target->expect_type === 1)
				{
					print ("<span class=\"support\">" . $target->Name('bold') . " casting shorted!</span>");
					$target->DelayCut(60, $this->battle->delay, 1);
					print ("<br />\n");
				}
				break;

			case 3060: //HolyShield
			case 5067: //BananaProtection
				if (!$target->SPECIAL["Barrier"])
				{
					$target->GetSpecial("Barrier", true);
					print ("<span class=\"support\">" . $target->Name('bold') . " got barriered!</span><br />\n");
				}
				break;
				/*
				case 3101: // Blessing(味方SP回復)
				$RATE	= 3;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				self::RecoverSP($target,$SpRec);
				break;

				case 3102: // Benediction
				$RATE	= 5;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				self::RecoverSP($target,$SpRec);
				break;

				case 3103: // Sanctuary
				$RATE	= 7;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				self::RecoverSP($target,$SpRec);
				break;
				*/
			case 3113: // Berserk
				break;

			case 3120: // FirstAid
				$heal = 50 + $target->MAXHP * 1 / 10;
				$heal = ceil($heal);
				self::RecoverHP($target, $heal);
				break;

			case 3121: // SelfRecovery
				$heal = 50 + $target->MAXHP * 2 / 10;
				$heal = ceil($heal);
				self::RecoverHP($target, $heal);
				break;

			case 3122: // HyperRecovery
				$dif = $char->MAXHP - $char->HP;
				$heal = ceil($dif * 0.6);
				self::RecoverHP($target, $heal);
				break;

				// 召喚キャラのみに適応する技
			case 3300: // PowerTrain
			case 3301: // MindTrain
			case 3302: // SpeedTrain
			case 3303: // DefenceTrain
			case 3304:
			case 3305:
			case 3306:
			case 3307:
			case 3308:
			case 3310: //SuppressBeast
				if (!$target->isSummon()) break;
				$this->StatusChanges($skill, $target);
				break;

			case 3900: // GetPoison
				print ("Got poisoned<br />\n");
				$char->GetPoison(100);
				break;
			case 3901: // GetDead
				self::DamageHP($char, 9999);
				break;

			case 4000: // StanceRestore(臨戦態勢)
				if ($target->behavior['position'] != $target->POSITION)
				{
					$target->Move($target->behavior['position']);
				}
				break;

				// 敵スキル
			case 5002: // BloodSuck
				$dmg = self::CalcBasicDamage($skill, $char, $target, array("pierce" => 1));
				self::AbsorbHP($target, $dmg, $char, $dmg);
				return $dmg;

			case 5006: // Charge!!!
				if ($char == $target)
				{
					$char->POSITION = POSITION_BACK;
					return false; //自分は対象外
				}
				if ($target->POSITION == POSITION_BACK)
				{
					$target->POSITION = POSITION_FRONT;
					print ($target->Name('bold') . " goes forward.<br />");
				}
				$this->StatusChanges($skill, $target);
				break;

			case 5060: //ArmorSnatch
				$target->DownDEF(30);
				$target->DownMDEF(30);
				$char->UpDEF(30);
				$char->UpMDEF(30);
				break;

				// ステータス変化形の技
			case 5022: //Fortune
				if ($char == $target) break; //自分には使わない。
				$heal = self::CalcRecoveryValue($skill, $char, $target);
				self::RecoverHP($target, $heal);
				$this->StatusChanges($skill, $target);
				break;

			case 5803: // Spawn
				$spawn = array(
					1018,
					1019,
					1020,
					1021,
					5002);
				$mob = $spawn[array_rand($spawn)];
				$add = HOF_Model_Char::newMonSummon($mob);
				$this->battle->JoinCharacter($char, $add);
				$add->ShowImage(vcent);
				print ($add->Name('bold') . " joined to the team.<br />\n");
				break;

				//---------------------------------------------//
				// それ以外の技(ここが最も多く分類される)
				// めんどくさいので処理が増えた
				//---------------------------------------------//
			default:
				// 魔方陣描く
				if ($skill["MagicCircleAdd"])
				{
					$this->battle->changeMagicCircle($char->team(), $skill["MagicCircleAdd"]);
					print ($char->Name('bold') . '<span class="support"> draw MagicCircle x' . $skill["MagicCircleAdd"] . '</span><br />' . "\n");
				}
				// 魔方陣消す(敵)
				if ($skill["MagicCircleDeleteEnemy"])
				{
					// 相手チームを指定
					//$EnemyTeam = ($char->team()->team_idx() == TEAM_0) ? TEAM_1 : TEAM_0;

					list(, $EnemyTeam) = $this->battle->teamToggle($char->team());

					$this->battle->changeMagicCircle($EnemyTeam, $skill["MagicCircleDeleteEnemy"], -1);
					print ($char->Name('bold') . '<span class="dmg"> erased enemy MagicCircle x' . $skill["MagicCircleDeleteEnemy"] . '</span><br />' . "\n");
				}
				// HP持続回復
				if ($skill["HpRegen"])
				{
					$target->GetSpecial("HpRegen", $skill["HpRegen"]);
					print ($target->Name('bold') . '<span class="recover"> gained HP regeneration +' . $skill["HpRegen"] . "%</span><br />\n");
				}
				// SP持続回復
				if ($skill["SpRegen"])
				{
					$target->GetSpecial("SpRegen", $skill["SpRegen"]);
					print ($target->Name('bold') . '<span class="support"> gained SP regeneration +' . $skill["SpRegen"] . "%</span><br />\n");
				}
				// チャージ(詠唱)中のキャラのみに適応する技。
				if ($skill["priority"] == "Charge" && !$target->expect) break;

				// 召喚系の処理
				if (!empty($skill["summon"]))
				{
					/*
					// 配列じゃなかったら要素1個の配列にしちゃう。
					if (!is_array($skill["summon"])) $skill["summon"] = array($skill["summon"]);
					*/

					foreach ($skill["summon"] as $SummonNo)
					{
						// 召喚力?
						$Strength = $char->SummonPower();
						$add = HOF_Model_Char::newMonSummon($SummonNo, $Strength);

						// 速攻
						if ($skill["quick"])
						{
 							$add->Quick($this->battle->delay * 2);
						}

						//break;//ここ取るとエラー無くなる(?)。
						$this->battle->JoinCharacter($char, $add);
						$add->ShowImage(vcent);

						print ($add->Name('bold') . " joined to the team.<br />\n");

						$add->enterBattlefield();

						echo '<br/>';
					}
					return true;
				}

				// 毒の治療
				if ($skill["CurePoison"])
				{
					if ($target->STATE == STATE_POISON) $target->GetNormal(true);
				}
				// 基本的なダメージの計算
				if ($skill["pow"])
				{
					if ($skill["support"])
					{
						$heal = self::CalcRecoveryValue($skill, $char, $target);
						self::RecoverHP($target, $heal);
						$this->StatusChanges($skill, $target);
					}
					else
					{
						if ($skill["pierce"]) //?? ここで設定する必要ある？
 								$option["pierce"] = true;
						$dmg = self::CalcBasicDamage($skill, $char, $target, $option);
						self::DamageHP($target, $dmg);
					}
				}
				// SP回復(レート)
				if ($skill["SpRecoveryRate"])
				{
					$SpRec = ceil(sqrt($target->MAXSP) * $skill["SpRecoveryRate"]);
					self::RecoverSP($target, $SpRec);
				}
				// 毒化
				if ($skill["poison"])
				{
					$result = $target->GetPoison($skill["poison"]);
					if ($result === true) print ($target->Name('bold') . " get <span class=\"spdmg\">poisoned</span>&nbsp;!<br />\n");
					else
						if ($result === "BLOCK") print ($target->Name('bold') . " blocked poison.<br />\n");
				}
				// ノックバック(後衛化)
				if ($skill["knockback"]) $target->KnockBack($skill["knockback"]);
				// ステータス変化
				$this->StatusChanges($skill, $target);
				// 隊列の移動
				if ($skill["move"]) $target->Move($skill["move"]);
				// 行動を遅らせる(DELAY)
				$this->DelayChar($target, $skill);
				return $dmg;
		endswitch;
	}

	/**
	 * 行動を遅らせる
	 */
	function DelayChar(&$target, $skill)
	{
		if (!$skill["delay"]) return false;

		print ($target->Name('bold') . " delayed ");
		$target->DelayByRate($skill["delay"], $this->battle->delay, 1);
		print (".<br />\n");
	}


	/**
	 * ステータスを変化させる
	 * Class内にないと駄目。
	 */
	function StatusChanges($skill, &$target)
	{
		foreach (self::$map_status['action'] as $action)
		{
			foreach (self::$map_status['attr'] as $attr)
			{
				$k = $action . $attr;

				if ($skill[$k] && method_exists($target, $k))
				{
					$target->$k($skill[$k]);

					if ($attr == 'SPD')
					{
						$this->battle->ChangeDelay();
					}
				}
			}
		}

		/*
		if ($skill["PlusSTR"]) $target->PlusSTR($skill["PlusSTR"]);
		if ($skill["PlusINT"]) $target->PlusINT($skill["PlusINT"]);
		if ($skill["PlusDEX"]) $target->PlusDEX($skill["PlusDEX"]);
		if ($skill["PlusSPD"])
		{
		$target->PlusSPD($skill["PlusSPD"]);
		$this->battle->ChangeDelay();
		}
		if ($skill["PlusLUK"]) $target->PlusLUK($skill["PlusLUK"]);

		if ($skill["UpMAXHP"]) $target->UpMAXHP($skill["UpMAXHP"]);
		if ($skill["UpMAXSP"]) $target->UpMAXSP($skill["UpMAXSP"]);
		if ($skill["UpSTR"]) $target->UpSTR($skill["UpSTR"]);
		if ($skill["UpINT"]) $target->UpINT($skill["UpINT"]);
		if ($skill["UpDEX"]) $target->UpDEX($skill["UpDEX"]);
		if ($skill["UpSPD"])
		{
		$target->UpSPD($skill["UpSPD"]);
		$this->battle->ChangeDelay();
		}
		if ($skill["UpATK"]) $target->UpATK($skill["UpATK"]);
		if ($skill["UpMATK"]) $target->UpMATK($skill["UpMATK"]);
		if ($skill["UpDEF"]) $target->UpDEF($skill["UpDEF"]);
		if ($skill["UpMDEF"]) $target->UpMDEF($skill["UpMDEF"]);

		if ($skill["DownMAXHP"]) $target->DownMAXHP($skill["DownMAXHP"]);
		if ($skill["DownMAXSP"]) $target->DownMAXSP($skill["DownMAXSP"]);
		if ($skill["DownSTR"]) $target->DownSTR($skill["DownSTR"]);
		if ($skill["DownINT"]) $target->DownINT($skill["DownINT"]);
		if ($skill["DownDEX"]) $target->DownDEX($skill["DownDEX"]);
		if ($skill["DownSPD"])
		{
		$target->DownSPD($skill["DownSPD"]);
		$this->battle->ChangeDelay();
		}
		if ($skill["DownATK"]) $target->DownATK($skill["DownATK"]);
		if ($skill["DownMATK"]) $target->DownMATK($skill["DownMATK"]);
		if ($skill["DownDEF"]) $target->DownDEF($skill["DownDEF"]);
		if ($skill["DownMDEF"]) $target->DownMDEF($skill["DownMDEF"]);
		*/
	}

	/**
	 * 基本的なダメージ計算式でダメージだけ返す。
	 */
	static function CalcBasicDamage($skill, $char, &$target, $option = null)
	{
		//基本的なダメージ計算(物理or魔法)
		if ($skill["type"] == 0)
		{ //物理
			if ($skill["inf"] == "dex") //威力をDEX依存にする
 					$str = $char->DEX;
			else  $str = $char->STR;
			$dmg = sqrt($str) * 10;
			$dmg += $char->atk[0]; //装備の物攻
			$dmg *= $skill["pow"] / 100;
			// 追加防御無視ダメージ
			if ($char->SPECIAL["Pierce"]["0"])
			{
				$Pierce = $char->SPECIAL["Pierce"]["0"] * $skill["pow"] / 100;
			}
		}
		else
		{ //魔法
			$int = $char->INT;
			$dmg = sqrt($int) * 10;
			$dmg += $char->atk[1]; //装備の魔攻
			$dmg *= $skill["pow"] / 100;
			// 追加防御無視ダメージ
			if ($char->SPECIAL["Pierce"]["1"])
			{
				$Pierce = $char->SPECIAL["Pierce"]["1"] * $skill["pow"] / 100;
			}
		}

		if ($option["multiply"]) $dmg *= $option["multiply"];

		// 1回攻撃を防いで0にする。
		if ($target->SPECIAL["Barrier"])
		{
			$target->GetSpecial("Barrier", false);
			print ("Attack has disappeared.<br />\n");
			$dmg = 0;
		}

		$min = $dmg * (1 / 10); //最低保証ダメジ

		//相手の防御力による軽減
		if (!$option["pierce"])
		{
			if ($skill["type"] == 0)
			{ //物理
				$dmg *= 1 - $target->def["0"] / 100;
				$dmg -= $target->def["1"];
			}
			else
			{ //魔法
				$dmg *= 1 - $target->def["2"] / 100;
				$dmg -= $target->def["3"];
			}
		}
		$dmg += $Pierce;
		//ダメージのばらつき
		//$dmg	*= mt_rand(90,110)/100;
		//$dmg	*= mt_rand(90,110)/100;
		//最低ダメージかどうか
		if ($dmg < $min) $dmg = $min;

		return ceil($dmg); //最終ダメージ
	}

	/**
	 * HPへダメージ
	 */
	static function DamageHP(&$target, $value)
	{
		print ('<span class="dmg"><span class="bold">' . $value . '</span> Damage</span> to ' . $target->Name("bold"));
		$target->HpDamage($value);
		print ("<br />\n");
	}

	/**
	 * HPへダメージ(1以下にならない)
	 */
	static function DamageHP2(&$target, $value)
	{
		print ('<span class="dmg"><span class="bold">' . $value . '</span> Damage</span> to ' . $target->Name("bold"));
		$target->HpDamage2($value);
		print ("<br />\n");
	}

	/**
	 * SPへダメージ
	 */
	static function DamageSP(&$target, $value)
	{
		print ('<span class="spdmg"><span class="bold">' . $value . '</span>SP Damage</span> to ' . $target->Name("bold"));
		$target->SpDamage($value);
		print ("<br />\n");
	}

	/**
	 * HPの回復
	 */
	static function RecoverHP(&$target, $value)
	{
		print ($target->Name("bold") . ' <span class="recover">Recovered <span class="bold">' . $value . ' HP</span></span>');
		$target->HpRecover($value);
		print ("<br />\n");
	}

	/**
	 * SPの回復
	 */
	static function RecoverSP(&$target, $value)
	{
		print ($target->Name("bold") . ' <span class="support">Recovered <span class="bold">' . $value . ' SP</span></span>');
		$target->SpRecover($value);
		print ("<br />\n");
	}

	/**
	 * HPの吸収
	 */
	static function AbsorbHP(&$target, $value, &$char, $value2)
	{
		print ('Drained <span class="recover"><span class="bold">' . $value . '</span> HP</span>');
		$char->HpRecover($value);
		print (' from ' . $target->Name('bold'));
		$target->HpDamage($value);
		print ("<br />\n");
	}

	/**
	 * SPの回復
	 */
	static function AbsorbSP(&$target, $value, &$char, $value2)
	{
		print ('Drained <span class="support"><span class="bold">' . $value . '</span> SP</span>');
		$char->SpRecover($value);
		print (' from ' . $target->Name('bold'));
		$target->SpDamage($value);
		print ("<br />\n");
	}

	/**
	 * 回復量の計算
	 */
	static function CalcRecoveryValue($skill, $char, $target)
	{
		$int = $char->INT;
		$heal = sqrt($int) * 10;
		$heal += $char->atk["1"]; //装備の魔攻
		$heal *= $skill["pow"] / 100;
		$heal = ceil($heal);

		// 回復量増加系パッシブ

		// 受ける側が回復量増加系のパッシブスキルを持っていたら増す
		//if($char->special["?"])
		//

		return $heal;
	}

}
