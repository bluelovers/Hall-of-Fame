<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include (CLASS_SKILL_EFFECT);

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
class HOF_Class_Skill_Effect extends HOF_Class_Array
{

	/**
	 * @param HOF_Class_Battle $battle
	 */
	function __construct(&$battle)
	{
		parent::__construct();

		$this->battle = &$battle;
	}

	/**
	 * 使用者が対象者にスキルを使う
	 */
	function SkillEffect($skill, $skill_no, &$user, &$target)
	{
		if ($target === false)
		{
			print ("No target.Failed!<br />\n");
			return false;
		}

		//スキルを実際に使用する
		switch ($skill_no):

			case 1020: // ManaBreak
				$dmg = CalcBasicDamage($skill, $user, $target);
				DamageSP($target, $dmg);
				break;

			case 1021: // SoulBreak
				$dmg = CalcBasicDamage($skill, $user, $target);
				DamageHP($target, $dmg);
				DamageSP($target, $dmg);
				break;

			case 1022: //ChargeAttack
				if ($user->POSITION != "front") $option["multiply"] = 4;
				$dmg = CalcBasicDamage($skill, $user, $target, $option);
				DamageHP($target, $dmg);
				$user->Move("front");
				break;

			case 1023: //Hit&Away
				if ($user->POSITION == "front") $option["multiply"] = 3;
				$dmg = CalcBasicDamage($skill, $user, $target, $option);
				DamageHP($target, $dmg);
				$user->Move("back");
				break;

			case 1024: //LifeDivision
				$value = round(abs($target->HP - $user->HP) * 0.5); // 差分
				if ($user->HP <= $target->HP)
				{
					if (1000 <= $value)
					{
						print ("※値が大きすぎて補正されました。<br />\n");
						$value = 500;
					}
					DamageHP($target, $value);
					RecoverHP($user, $value);
				}
				else
				{
					DamageHP($user, $value);
					RecoverHP($target, $value);
				}
				break;

			case 1025: //ManaDivision
				$value = round(abs($target->SP - $user->SP) * 0.5); // 差分
				if ($user->SP <= $target->SP)
				{
					if (1000 <= $value)
					{
						print ("※値が大きすぎて補正されました。<br />\n");
						$value = 500;
					}
					DamageSP($target, $value);
					RecoverSP($user, $value);
				}
				else
				{
					DamageSP($user, $value);
					RecoverSP($target, $value);
				}
				break;

			case 1116: //Punish
				$dmg = $user->MAXHP - $user->HP;
				DamageHP($target, $dmg);
				break;

			case 1119: //Possession
				if ($user === $target) break;
				$this->StatusChanges($skill, $target);
				break;

			case 1200: //PoisonBlow
				if ($target->STATE === 2)
				{
					$option["multiply"] = 6;
					print ("Damage x6!<br />\n");
				}
				$dmg = CalcBasicDamage($skill, $user, $target, $option);
				DamageHP($target, $dmg);
				break;

			case 1208: //PoisonInvasion
				$Rate = (log(($user->INT + 22) / 10) - 0.8) / 0.85;
				//$Rate	= 0.81 + (pow(pow($user->INT*0.1,2.05),21/40))/10;
				$target->PoisonDamage($Rate);
				break;

			case 1209: //TransPoison
				if ($target->STATE !== POISON) return false;
				$this->StatusChanges($skill, $target);
				$target->GetNormal(true);
				break;

			case 1220: //AntiPoisoning
				$target->GetPoisonResist(50);
				break;

			case 2030: // LifeDrain
			case 2031: // LifeSqueeze
				if ($user == $target) return false; //自分から自分は吸収しない。
				$dmg = CalcBasicDamage($skill, $user, $target);
				AbsorbHP($target, $dmg, $user, $dmg);
				break;

			case 2032: // DeathKnell
				$p = mt_rand(1, 100);
				if (50 < $p) $target->HP = 0;
				else  print ("Failed!<br />");
				return true;

			case 2055: // SoulRevenge
				$option["multiply"] = $this->battle->CountDead($user) + 1;
				print ("Damage x" . $option["multiply"] . "!<br />\n");
				$dmg = CalcBasicDamage($skill, $user, $target, $option);
				DamageHP($target, $dmg);
				break;

			case 2056: // ZombieRevival
				//print("who? : ".$target->Name().":".$target->STATE."<br />\n");
				if ($target->STATE !== DEAD) break;
				$target->GetNormal(true);
				$this->StatusChanges($skill, $target);
				RecoverHP($target, $target->MAXHP);
				break;

			case 2057: // SelfMetamorphorse
				if (60 < $target->HpPercent() || $target->SPECIAL["Metamo"])
				{
					print ("Failed!<br />\n");
					break;
				}
				print ($target->GetSpecial("Metamo", true));
				if ($target->gender == 0) $target->img = "mon_110r.gif"; //♂
				else  $target->img = "mon_149r.gif"; //♀
				$this->StatusChanges($skill, $target);
				RecoverHP($target, round($target->MAXHP / 2));
				break;

				// SP吸収系
			case 2090: //EneryRob
			case 2091: //EneryCollect
				if ($user == $target) return false; //自分から自分は吸収しない。
				$dmg = CalcBasicDamage($skill, $user, $target, array("pierce" => 1));
				AbsorbSP($target, $dmg, $user, $dmg);
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
				$heal	= CalcRecoveryValue($skill,$user,$target);
				RecoverHP($target,$heal);
				$this->StatusChanges($skill,$target);
				break;
				*/

			case 3005: // ProgressiveHeal
				$heal = CalcRecoveryValue($skill, $user, $target);
				$Rate = ($target->HP / $target->MAXHP) * 100;
				if ($Rate <= 30)
				{
					$heal *= 2;
					print ("heal x2!<br />");
				}
				RecoverHP($target, $heal);
				break;

			case 3010: // ManaRecharge(自己SP回復)
				$SpRec = ceil($target->MAXSP * 3 / 10);
				RecoverSP($target, $SpRec);
				break;

			case 3011: // HiManaRecharge
				$SpRec = ceil($target->MAXSP * 5 / 10);
				RecoverSP($target, $SpRec);
				break;

			case 3012: // LifeConvert
				$HpDmg = ceil($target->MAXHP * 3 / 10);
				DamageHP2($target, $HpDmg);
				$SpRec = ceil($target->MAXSP * 7 / 10);
				RecoverSP($target, $SpRec);
				break;

			case 3013: // EnergyExchange
				$HpRate = floor($target->HP / $target->MAXHP * 100);
				$SpRate = floor($target->SP / $target->MAXSP * 100);
				print ("{$target->name} exchanged rate of HP and SP.<br />");
				print ("HP: {$target->HP}(" . $HpRate . "%) to ");
				$target->HP = round($SpRate / 100 * $target->MAXHP);
				print ("{$target->HP}(" . $SpRate . "%)<br />");
				print ("SP: {$target->SP}(" . $SpRate . "%) to ");
				$target->SP = round($HpRate / 100 * $target->MAXSP);
				print ("{$target->SP}(" . $HpRate . "%)<br />");
				break;

			case 3020: // ManaExtend
				$target->MAXSP = round($target->MAXSP * 1.2);
				print ($target->Name(bold) . "'s MAXSP extended to {$target->MAXSP}.<br />\n");
				break;
				/*
				case 3030: // Reflesh
				if($target->STATE == DEAD) break;
				if($target->STATE == POISON)
				$target->GetNormal(true);
				$heal	= CalcRecoveryValue($skill,$user,$target);
				RecoverHP($target,$heal);
				break;
				*/
				//	蘇生系の技。
			case 3040: // Resurrection
			case 5030: // SoulRestor
			case 5063: // WakeUp
				if ($target->STATE !== 1) break;
				$heal = CalcRecoveryValue($skill, $user, $target);
				$target->GetNormal(true);
				RecoverHP($target, $heal);
				break;

			case 3050: //Quick
				if ($target == $user) return false;
				if ($target->expect) return false;
				//$target->Quick($this->battle->delay + 1);
				print ("<span class=\"support\">" . $target->Name("bold") . " got quicked!</span>");
				$target->DelayCut(101, $this->battle->delay, 1);
				print ("<br />\n");
				break;

			case 3055: //CastAsist
				if ($target->expect && $target->expect_type === 1)
				{
					print ("<span class=\"support\">" . $target->Name(bold) . " casting shorted!</span>");
					$target->DelayCut(60, $this->battle->delay, 1);
					print ("<br />\n");
				}
				break;

			case 3060: //HolyShield
			case 5067: //BananaProtection
				if (!$target->SPECIAL["Barrier"])
				{
					$target->GetSpecial("Barrier", true);
					print ("<span class=\"support\">" . $target->Name(bold) . " got barriered!</span><br />\n");
				}
				break;
				/*
				case 3101: // Blessing(味方SP回復)
				$RATE	= 3;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				RecoverSP($target,$SpRec);
				break;

				case 3102: // Benediction
				$RATE	= 5;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				RecoverSP($target,$SpRec);
				break;

				case 3103: // Sanctuary
				$RATE	= 7;
				$SpRec	= ceil(sqrt($target->MAXSP) * $RATE);
				RecoverSP($target,$SpRec);
				break;
				*/
			case 3113: // Berserk
				break;

			case 3120: // FirstAid
				$heal = 50 + $target->MAXHP * 1 / 10;
				$heal = ceil($heal);
				RecoverHP($target, $heal);
				break;

			case 3121: // SelfRecovery
				$heal = 50 + $target->MAXHP * 2 / 10;
				$heal = ceil($heal);
				RecoverHP($target, $heal);
				break;

			case 3122: // HyperRecovery
				$dif = $user->MAXHP - $user->HP;
				$heal = ceil($dif * 0.6);
				RecoverHP($target, $heal);
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
				if (!$target->summon) break;
				$this->StatusChanges($skill, $target);
				break;

			case 3900: // GetPoison
				print ("Got poisoned<br />\n");
				$user->GetPoison(100);
				break;
			case 3901: // GetDead
				DamageHP($user, 9999);
				break;

			case 4000: // StanceRestore(臨戦態勢)
				if ($target->position != $target->POSITION)
				{
					$target->Move($target->position);
				}
				break;

				// 敵スキル
			case 5002: // BloodSuck
				$dmg = CalcBasicDamage($skill, $user, $target, array("pierce" => 1));
				AbsorbHP($target, $dmg, $user, $dmg);
				return $dmg;

			case 5006: // Charge!!!
				if ($user == $target)
				{
					$user->POSITION = "back";
					return false; //自分は対象外
				}
				if ($target->POSITION == "back")
				{
					$target->POSITION = "front";
					print ($target->Name(bold) . " goes forward.<br />");
				}
				$this->StatusChanges($skill, $target);
				break;

			case 5060: //ArmorSnatch
				$target->DownDEF(30);
				$target->DownMDEF(30);
				$user->UpDEF(30);
				$user->UpMDEF(30);
				break;

				// ステータス変化形の技
			case 5022: //Fortune
				if ($user == $target) break; //自分には使わない。
				$heal = CalcRecoveryValue($skill, $user, $target);
				RecoverHP($target, $heal);
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
				$add = CreateSummon($mob);
				$this->battle->JoinCharacter($user, $add);
				$add->ShowImage(vcent);
				print ($add->Name(bold) . " joined to the team.<br />\n");
				break;

				//---------------------------------------------//
				// それ以外の技(ここが最も多く分類される)
				// めんどくさいので処理が増えた
				//---------------------------------------------//
			default:
				// 魔方陣描く
				if ($skill["MagicCircleAdd"])
				{
					$this->battle->MagicCircleAdd($user->team, $skill["MagicCircleAdd"]);
					print ($user->Name(bold) . '<span class="support"> draw MagicCircle x' . $skill["MagicCircleAdd"] . '</span><br />' . "\n");
				}
				// 魔方陣消す(敵)
				if ($skill["MagicCircleDeleteEnemy"])
				{
					$EnemyTeam = ($user->team == TEAM_0) ? TEAM_1 : TEAM_0; //相手チームを指定
					$this->battle->MagicCircleDelete($EnemyTeam, $skill["MagicCircleDeleteEnemy"]);
					print ($user->Name(bold) . '<span class="dmg"> erased enemy MagicCircle x' . $skill["MagicCircleDeleteEnemy"] . '</span><br />' . "\n");
				}
				// HP持続回復
				if ($skill["HpRegen"])
				{
					$target->GetSpecial("HpRegen", $skill["HpRegen"]);
					print ($target->Name(bold) . '<span class="recover"> gained HP regeneration +' . $skill["HpRegen"] . "%</span><br />\n");
				}
				// SP持続回復
				if ($skill["SpRegen"])
				{
					$target->GetSpecial("SpRegen", $skill["SpRegen"]);
					print ($target->Name(bold) . '<span class="support"> gained SP regeneration +' . $skill["SpRegen"] . "%</span><br />\n");
				}
				// チャージ(詠唱)中のキャラのみに適応する技。
				if ($skill["priority"] == "Charge" && !$target->expect) break;
				// 召喚系の処理
				if ($skill["summon"])
				{
					// 配列じゃなかったら要素1個の配列にしちゃう。
					if (!is_array($skill["summon"])) $skill["summon"] = array($skill["summon"]);
					foreach ($skill["summon"] as $SummonNo)
					{
						$Strength = $user->SUmmonPower(); //召喚力?
						$add = CreateSummon($SummonNo, $Strength);
						if ($skill["quick"]) // 速攻
 								$add->Quick($this->battle->delay * 2);
						//break;//ここ取るとエラー無くなる(?)。
						$this->battle->JoinCharacter($user, $add);
						$add->ShowImage(vcent);
						print ($add->Name(bold) . " joined to the team.<br />\n");
					}
					return true;
				}

				// 毒の治療
				if ($skill["CurePoison"])
				{
					if ($target->STATE == POISON) $target->GetNormal(true);
				}
				// 基本的なダメージの計算
				if ($skill["pow"])
				{
					if ($skill["support"])
					{
						$heal = CalcRecoveryValue($skill, $user, $target);
						RecoverHP($target, $heal);
						$this->StatusChanges($skill, $target);
					}
					else
					{
						if ($skill["pierce"]) //?? ここで設定する必要ある？
 								$option["pierce"] = true;
						$dmg = CalcBasicDamage($skill, $user, $target, $option);
						DamageHP($target, $dmg);
					}
				}
				// SP回復(レート)
				if ($skill["SpRecoveryRate"])
				{
					$SpRec = ceil(sqrt($target->MAXSP) * $skill["SpRecoveryRate"]);
					RecoverSP($target, $SpRec);
				}
				// 毒化
				if ($skill["poison"])
				{
					$result = $target->GetPoison($skill["poison"]);
					if ($result === true) print ($target->Name(bold) . " get <span class=\"spdmg\">poisoned</span>&nbsp;!<br />\n");
					else
						if ($result === "BLOCK") print ($target->Name(bold) . " blocked poison.<br />\n");
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

		print ($target->Name(bold) . " delayed ");
		$target->DelayByRate($skill["delay"], $this->battle->delay, 1);
		print (".<br />\n");
	}


	/**
	 * ステータスを変化させる
	 * Class内にないと駄目。
	 */
	function StatusChanges($skill, &$target)
	{
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
	}

}
