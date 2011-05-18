<?php
function LoadSkillTree($char) {
/*
	習得可能な技を返す。
*/
	// 習得済みスキル。
	// array_search() でなく ハッシュを使って毎回判定する。
	// どっちの処理が速いかは、知らん。
	$lnd	= array_flip($char->skill);
	$lnd[key($lnd)]++;//配列の先頭の値が"0"なので1にする(isset使わずにtrueにするため)

	$list	= array();//空配列


	//////////////////////////////////// 剣技
	if(	$char->job == 100
	 ||	$char->job == 101
	 ||	$char->job == 102
	 ||	$char->job == 103) {
		if($lnd["1001"])//Bash
			$list[]	= "1003";//DowbleAttack
		if($lnd["1001"])
			$list[]	= "1013";//Stab
		if($lnd["1001"])
			$list[]	= "3110";//Reinforce
		if($lnd["1001"])
			$list[]	= "3120";//FirstAid
		if($lnd["1003"])//DowbleAttack
			$list[]	= "1017";//RagingBlow
		if($lnd["1003"])
			$list[]	= "1011";//WeaponBreak
		if($lnd["1013"])//Stab
			$list[]	= "1014";//FatalStab
		if($lnd["1013"])
			$list[]	= "1016";//ArmorPierce
		if($lnd["3120"])//FirstAid
			$list[]	= "3121";//SelfRecovery
	}
	// RoyalGuard
	if($char->job == 101) {
		if($lnd["1003"])//DowbleAttack
			$list[]	= "1012";//ArmorBreak
		if($lnd["1017"]) {//RagingBlow
			$list[]	= "1018";//Indiscriminate
			$list[]	= "1022";//ChargeAttack
		}
		if($lnd["1013"]) {//Stab
			$list[]	= "1015";//KnockBack
			$list[]	= "1023";//Hit&Away
		}
		if($lnd["1016"])//ArmorPierce
			$list[]	= "1019";//PierceRush
		if($lnd["3110"]){ // Reinforce
			$list[]	= "3111";//OverLimit
			$list[]	= "3112";//Deffensive
		}
		if($lnd["3121"]){ // SelfRecovery
			$list[]	= "3122";//HyperRecovery
			$list[]	= "3123";//SelfRegeneration
		}
	}

	// Sacrier
	if($char->job == 102) {
		$list[]	= "1100";// ObtainPower
		if($lnd["1100"])// ObtainPower
			$list[]	= "1101";// ObtainSpeed
		if($lnd["1101"])// ObtainSpeed
			$list[]	= "1102";// ObtainMind

		$list[]	= "1113";// Pain
		if($lnd["1113"]) {// Pain
			$list[]	= "1114";// Rush
			$list[]	= "1117";// illness
		}
		if($lnd["1114"]) {// Rush
			$list[]	= "1115";// Ruin
			$list[]	= "1118";// Pressure
		}
		if($lnd["1115"])// Ruin
			$list[]	= "1116";// Punish
		// Rush + illness + ObtainMaind
		if($lnd["1114"] && $lnd["1117"] && $lnd["1102"])
			$list[]	= "1119";// Possession
	}

	// WitchHunt
	if($char->job == 103) {
		if($lnd["1003"])//DowbleAttack
			$list[]	= "1020";//ManaBreak
		if($lnd["1020"]) {//ManaBreak
			$list[]	= "1021";//SoulBreak
			$list[]	= "1025";//ManaDivision
		}
		if($lnd["1021"])//SoulBreak
			$list[]	= "1024";//LifeDivision
		// 無条件
		$list[]	= "2090";//EnergyRob
		$list[]	= "3231";//ForceShield(self)
		if($lnd["2090"]) {//EnergyRob
			$list[]	= "2091";//EnergyCollect
			$list[]	= "2110";//ChargeDisturb
			$list[]	= "2111";//ChargeDisturb(all)
		}
		if($lnd["2091"])//EnergyCollect
			$list[]	= "3421";//CircleErase
		if($lnd["3231"]) {//ForceShield(self)
			$list[]	= "3215";//MindBreak
			$list[]	= "3230";//ForceShield
			$list[]	= "3235";//MindBreak
		}
	}

	//////////////////////////////////// 魔法系
	if(	$char->job == 200
	||	$char->job == 201
	||	$char->job == 202
	||	$char->job == 203) {
		$list[]	= "3011";//HiManaRecharge

		if($lnd["1002"])//FireBall
			$list[]	= "2000";//FireStorm
		if($lnd["2000"])//FireStorm
			$list[]	= "2002";//FirePillar
	
		if($lnd["1002"])//FireBall
			$list[]	= "2010";//IceSpear
		if($lnd["2010"])//IceSpear
			$list[]	= "2011";//IceJavelin
		if($lnd["2011"])//IceSpear
			$list[]	= "2014";//IcePrison
	
		if($lnd["1002"])//FireBall
			$list[]	= "2020";//ThunderBolt
		if($lnd["2020"])//ThunderBolt
			$list[]	= "2021";//LightningBall
		if($lnd["2021"])//LightningBall
			$list[]	= "2022";//Flash
		if($lnd["2021"])
			$list[]	= "2023";//Paralysis
	}

	// Warlock
	if($char->job == 201) {
		if($lnd["2000"])//FireStorm
			$list[]	= "2001";//HellFire
		if($lnd["2001"])//HellFire
			$list[]	= "2004";//MeteoStorm
		if($lnd["2002"])
			$list[]	= "2003";//Explosion

		if($lnd["2011"])//IceSpear
			$list[]	= "2012";//Blizzard
		if($lnd["2011"] && $lnd["2014"])//IceSpear + IcePrison
			$list[]	= "2015";//TidalWave

		if($lnd["2021"])//LightningBall
			$list[]	= "2024";//ThunderStorm

		if($lnd["3011"])//HiManaRecharge
			$list[]	= "3012";//LifeConvert

		if($lnd["3012"])//LifeConvert
			$list[]	= "3013";//EnergyExchange

		if($lnd["2000"] && $lnd["2021"])//FireStorm + LightningBall
			$list[]	= "2041";//EarthQuake
		if($lnd["2041"])//EarthQuake
			$list[]	= "2042";//Subsidence

		if($lnd["2011"] && $lnd["2021"])//IceSpear + LightningBall
			$list[]	= "2040";//SandStorm
	}

	// Summoner
	if($char->job == 202) {
		
		$list[]	= "3020";//ManaExtend
		$list[]	= "2500";//SummonIfrit
		$list[]	= "2501";//SummonLeviathan
		$list[]	= "2502";//SummonArchAngel
		$list[]	= "2503";//SummonFallenAngel
		$list[]	= "2504";//SummonThor
		if($lnd["3011"])//HiManaRecharge
			$list[]	= "3012";//LifeConvert

		$list[]	= "3410";//MagicCircle
		if($lnd["3410"]) {
			$list[]	= "3411";//DoubleMagicCircle
			$list[]	= "3420";//CircleErace
		}
	}

	// Necromancer
	if($char->job == 203) {
		$list[]	= "2030";//LifeDrain
		if($lnd["2030"]) {//LifeDrain
			$list[]	= "2031";//LifeSqueeze
			$list[]	= "2050";//VenomBlast
			$list[]	= "3205";//Fear
			$list[]	= "3215";//MindBreak
		}
		if($lnd["2050"])//VenomBlast
			$list[]	= "2051";//PoisonSmog
		/* // 設定が簡単すぎる。
		if($lnd["2031"])//LifeSqueeze
			$list[]	= "2032";//DeathKnell
		*/
		$list[]	= "2460";//RaiseDead(Zombie)
		if($lnd["2460"]) {//RaiseDead(Zombie)
			$list[]	= "2461";// Ghoul
			$list[]	= "2462";// RaiseMummy
		}
		if($lnd["2461"] && $lnd["2462"])// Ghoul + RaiseMummy
			$list[]	= "2055";// SoulRevenge
		if($lnd["2461"])// Ghoul
			$list[]	= "2463";// ZombieControl
		if($lnd["2463"])
			$list[]	= "2057";// SelfMetamorphose
		if($lnd["2462"])// RaiseMummy
			$list[]	= "2464";// GraveYard
		if($lnd["2464"])
			$list[]	= "2056";// ZombieRevival
		// ZombieControl + GraveYard
		if($lnd["2463"] && $lnd["2464"])
			$list[]	= "2465";// Biohazard
	}

	//////////////////////////////////// 支援系
	if(	$char->job == 300
	 ||	$char->job == 301
	 ||	$char->job == 302) {
		if($lnd["3000"]) {//Healing
			$list[]	= "2100";//Holy
			$list[]	= "3001";//PowerHeal
			$list[]	= "3003";//QuickHeal
		}
		if($lnd["3001"] || $lnd["3003"]) {// Power || QuickHeal
			$list[]	= "3002";//PartyHeal
			$list[]	= "3004";//SmartHeal
			$list[]	= "3030";//Reflesh
		}

		if($lnd["2100"])//Holy
			$list[]	= "2480";//HealRabbit

		if($lnd["3101"])//Blessing
			$list[]	= "3102";//Benediction
	}

	// Bishop
	if($char->job == 301) {
		if($lnd["2100"]) {//Holy
			$list[]	= "2101";//HolyBurst
			$list[]	= "3200";//Encourage
			$list[]	= "3210";//Charm
			$list[]	= "3220";//ProtectionField
			$list[]	= "3230";//ForceShield
		}
		if($lnd["2101"])//HolyBurst
			$list[]	= "2102";//GrandCross
		if($lnd["3220"])//ProtectionField
			$list[]	= "3400";//Regeneration
		if($lnd["3230"])//ForceShield
			$list[]	= "3401";//ManaRegen
		if($lnd["2480"])//HealRabbit
			$list[]	= "2481";//AdventAngel

		if($lnd["3102"] && $lnd["3220"] && $lnd["3230"])
			$list[]	= "3103";//Sanctuary
		$list[]	= "3415";//MagicCircle
	}

	// Druid
	if($char->job == 302) {
		
		if($lnd["3004"]) {//SmartHeal
			$list[]	= "3005";//ProgressiveHeal
			$list[]	= "3060";//HolyShield
		}

		if($lnd["3060"]) {
			$list[]	= "3050";//Quick
			$list[]	= "3055";//CastAsist
		}

		$list[]	= "3250";//PowerAsist
		$list[]	= "3255";//MagicAsist
		if($lnd["3250"] or $lnd["3255"])
			$list[]	= "3265";//SpeedAsist
		$list[]	= "3415";//MagicCircle
	}
	//////////////////////////////////// 弓系
	if( $char->job == 400
	||	$char->job == 401
	||	$char->job == 402
	||	$char->job == 403) {
		$list[]	= "2310";//DoubleShot
		if(!$lnd["2300"])
			$list[]	= "2300";//Shoot
	
		if($lnd["2300"]) {//Shoot
			$list[]	= "2301";//PowerShoot
			$list[]	= "2302";//ArrowShower
			$list[]	= "2303";//PalsyShot
		}
	}

	// Sniper
	if($char->job == 401) {
		if($lnd["2303"])//PalsyShot
			$list[]	= "2304";//PoisonShot
		if($lnd["2301"]){ //PowerShoot
			$list[]	= "2305";//ChargeShot
			$list[]	= "2306";//PierceShot
		}
		if($lnd["2306"]) {//PierceShot
			$list[]	= "2308";//Aiming
			$list[]	= "2309";//Disarm
		}
		// ArrowShower + ChargeShot + PierceShot
		if($lnd["2302"] && $lnd["2305"] && $lnd["2306"])
			$list[]	= "2307";//HurricaneShot
	}

	// BeastTamer
	if($char->job == 402) {

		$list[]	= "1240";//Whip
		if($lnd["1240"]) {
			$list[]	= "1241";//Lashing
			$list[]	= "1243";//WhipBite
		}
		if($lnd["1241"]) {
			$list[]	= "1242";//WhipStorm
			$list[]	= "1244";//BodyBind
		}
		

		$list[]	= "2401";//CallPookie
		$list[]	= "2404";//CallTrainedLion
		$list[]	= "2408";//CallSprite
		if($lnd["2401"])//CallPookie
			$list[]	= "2402";//CallWildBoar
		if($lnd["2402"])//Call
			$list[]	= "2403";//CallGrandDino
		if($lnd["2404"])//CallTrainedLion
			$list[]	= "2405";//CallBear
		if($lnd["2405"])//CallBear
			$list[]	= "2406";//CallChimera
		if($lnd["2408"])//CallSprite
			$list[]	= "2409";//CallFlyHippo
		if($lnd["2409"])//Call
			$list[]	= "2410";//CallDragon
		if($lnd["2408"] && $lnd["2405"])//CallSprite+Bear
			$list[]	= "2407";//CallSnowMan

		$list[]	= "3300";//PowerTrain
		$list[]	= "3301";//MindTrain
		$list[]	= "3302";//SpeedTrain
		$list[]	= "3303";//DefenceTrain
		if($lnd["3300"])//
			$list[]	= "3304";//BuildUp
		if($lnd["3301"])//
			$list[]	= "3305";//Intention
		if($lnd["3302"])//
			$list[]	= "3306";//Nimble
		if($lnd["3303"])//
			$list[]	= "3307";//Fortify
		// ～Train 4種類
		if($lnd["3300"] && $lnd["3301"] && $lnd["3302"] && $lnd["3303"]) {
			$list[]	= "3308";//FullSupport
			$list[]	= "3310";//SuppressBeast
		}
	}

	// Murderer
	if($char->job == 403) {
		$list[]	= "1200";//PoisonBlow
		if($lnd["1200"]) {
			$list[]	= "1207";//PoisonBreath
			$list[]	= "1208";//PoisonInvasion
			$list[]	= "1220";//AntiPoisoning
		}
		if($lnd["1208"])
			$list[]	= "1209";//TransPoison
		$list[]	= "1203";//KnifeThrow
		if($lnd["1203"])
			$list[]	= "1204";//ScatterKnife
		$list[]	= "1205";//SulfaricAcid
		if($lnd["1205"])
			$list[]	= "1206";//AcidMist
		
	}


	//////////////////////////////////// その他
	if(!$lnd["3010"] && $char->job == "200")//ManaRecharge
		$list[]	= "3010";

	//////////////////////////////////// 共通系
	if(19 < $char->level)
		$list[]	= "4000";//臨戦態勢

	if(4 < $char->level)
		$list[]	= "9000";//複数判定(* think over)

	asort($list);
	return $list;
}
?>