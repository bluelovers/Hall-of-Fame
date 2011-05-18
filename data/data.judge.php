<?
//function DecideJudge($number,$My,$MyTeam,$EnemyTeam,$classBattle) {
function DecideJudge($number,$My,$classBattle) {

	//判定に使用する　数字　変数　配列　とかを
	//計算したりする。
	if($My->team == TEAM_0) {
		$MyTeam	= $classBattle->team0;
		$EnemyTeam	= $classBattle->team1;
		$MyTeamMC	= $classBattle->team0_mc;
		$EnemyTeamMC	= $classBattle->team1_mc;
	} else {
		$MyTeam	= $classBattle->team1;
		$EnemyTeam	= $classBattle->team0;
		$MyTeamMC	= $classBattle->team1_mc;
		$EnemyTeamMC	= $classBattle->team0_mc;
	}

	$Judge		= $My->judge["$number"];
	$Quantity	= $My->quantity["$number"];

	switch($Judge) {

		case 1000:// 必ず
			return true;
		case 1001:// パス
			return false;

//------------------------ HP
		case 1100:// 自分のHP が**(%)以上
			$hpp	= $My->HpPercent();
			if($Quantity <= $hpp) return true;
			break;
		case 1101:// 自分のHP が**(%)以下
			$hpp	= $My->HpPercent();
			if($hpp <= $Quantity) return true;
			break;

		case 1105:// 自分のHP が**以上
			$hp		= $My->HP;
			if($Quantity <= $hp) return true;
			break;
		case 1106:// 自分のHP が**以下
			$hp		= $My->HP;
			if($hp <= $Quantity) return true;
			break;

		case 1110:// 最大HP が**以上
			$mhp		= $My->MAXHP;
			if($Quantity <= $mhp) return true;
			break;
		case 1111:// 最大HP が**以下
			$mhp		= $My->MAXHP;
			if($mhp <= $Quantity) return true;
			break;

		case 1121:// 味方に HPが**(%)以下のキャラ がいる時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->HpPercent() <= $Quantity)
					return true;
			}
			break;

		case 1125:// 味方の 平均HPが **(%)以上の時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
					$sum	+= $char->HpPercent();
					$cnt++;// 生存人数
			}
			$ave	= $sum/$cnt;
			if($Quantity <= $ave) return true;
			break;
		case 1126:// 味方の 平均HPが **(%)以下の時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
					$sum	+= $char->HpPercent();
					$cnt++;// 生存人数
			}
			$ave	= $sum/$cnt;
			if($ave <= $Quantity) return true;
			break;
//------------------------ SP
		case 1200:// 自分のSP が**(%)以上
			$spp	= $My->SpPercent();
			if($Quantity <= $spp) return true;
			break;

		case 1201:// 自分のSP が**(%)以下
			$spp	= $My->SpPercent();
			if($spp <= $Quantity) return true;
			break;

		case 1205:// 自分のSP が**以上
			$sp		= $My->SP;
			if($Quantity <= $sp) return true;
			break;
		case 1206:// 自分のSP が**以下
			$sp		= $My->SP;
			if($sp <= $Quantity) return true;
			break;

		case 1210:// 最大SP が**以上
			$msp		= $My->MAXSP;
			if($Quantity <= $msp) return true;
			break;
		case 1211:// 最大SP が**以下
			$msp		= $My->MAXSP;
			if($msp <= $Quantity) return true;
			break;

		case 1221:// 味方に SPが**(%)以下のキャラ がいる時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
				if($char->SpPercent() <= $Quantity)
					return true;
			}
			break;

		case 1225:// 味方の 平均SPが **(%)以上の時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
					
					$sum	+= $char->SpPercent();
					$cnt++;// 生存人数
			}
			// Divide by Zero
			if(!$cnt)
				break;
			$ave	= $sum/$cnt;
			if($Quantity <= $ave) return true;
			break;

		case 1226:// 味方の 平均SPが **(%)以下の時
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->MAXSP === 0) continue;
					$sum	+= $char->SpPercent();
					$cnt++;// 生存人数
			}
			// Divide by Zero
			if(!$cnt)
				break;
			$ave	= $sum/$cnt;
			if($ave <= $Quantity) return true;
			break;
//------------------------ STR
		case 1300:// 自分のSTRが** 以上
			break;
		case 1301:// 自分のSTRが** 以下
			break;
//------------------------ INT
		case 1310:// 自分のINTが** 以上
			break;
		case 1311:// 自分のINTが** 以下
			break;
//------------------------ DEX
		case 1320:// 自分のDEXが** 以上
			break;
		case 1321:// 自分のDEXが** 以下
			break;
//------------------------ SPD
		case 1330:// 自分のSPDが** 以上
			break;
		case 1331:// 自分のSPDが** 以下
			break;
//------------------------ LUK
		case 1340:// 自分のLUKが** 以上
			break;
		case 1341:// 自分のLUKが** 以下
			break;
//------------------------ ATK
		case 1350:// 自分のATKが** 以上
			break;
		case 1351:// 自分のATKが** 以下
			break;
//------------------------ MATK
		case 1360:// 自分のMATKが** 以上
			break;
		case 1361:// 自分のMATKが** 以下
			break;
//------------------------ DEF
		case 1370:// 自分のDEFが** 以上
			break;
		case 1371:// 自分のDEFが** 以下
			break;
//------------------------ MDEF
		case 1380:// 自分のMDEFが** 以上
			break;
		case 1381:// 自分のMDEFが** 以下
			break;
//------------------------ 人数(味方)
		case 1400:// 味方の生存者が *人以上
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($Quantity <= $alive) return true;
			break;

		case 1401:// 味方の生存者が *人以下
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($alive <= $Quantity) return true;
			break;

		case 1405:// 味方の死者が *人以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($Quantity <= $dead) return true;
			break;

		case 1406:// 味方の死者が *人以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($dead <= $Quantity) return true;
			break;

		case 1410:// 味方で前衛の生存者が *人以上
			$front_alive	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE !== DEAD && $char->position == FRONT)
					$front_alive++;
			}
			if($Quantity <= $front_alive) return true;
			break;

//------------------------ 人数(敵)
		case 1450:// 相手の生存者が *人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($Quantity <= $alive) return true;
			break;

		case 1451:// 相手の生存者が *人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE !== DEAD)
					$alive++;
			}
			if($alive <= $Quantity) return true;
			break;

		case 1455:// 相手の死者が *人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($Quantity <= $dead) return true;
			break;

		case 1456:// 相手の死者が *人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD)
					$dead++;
			}
			if($dead <= $Quantity) return true;
			break;

//------------------------ チャージ+詠唱
		case 1500:// チャージ中のキャラが *人以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($Quantity <= $charge) return true;
			break;

		case 1501:// チャージ中のキャラが *人以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($charge <= $Quantity) return true;
			break;

		case 1505:// 詠唱中のキャラが *人以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($Quantity <= $cast) return true;
			break;

		case 1506:// 詠唱中のキャラが *人以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$cast++;
			}
			if($cast <= $Quantity) return true;
			break;

		case 1510:// チャージか詠唱中のキャラが *人以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($Quantity <= $expect) return true;
			break;

		case 1511:// チャージか詠唱中のキャラが *人以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($expect <= $Quantity) return true;
			break;

//------------------------ チャージ+詠唱(敵)
		case 1550:// チャージ中の相手が *人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($Quantity <= $charge) return true;
			break;

		case 1551:// チャージ中の相手が *人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CHARGE)
					$charge++;
			}
			if($charge <= $Quantity) return true;
			break;

		case 1555:// 詠唱中の相手が *人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($Quantity <= $cast) return true;
			break;

		case 1556:// 詠唱中の相手が *人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST)
					$cast++;
			}
			if($cast <= $Quantity) return true;
			break;

		case 1560:// チャージか詠唱中の相手が *人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($Quantity <= $expect) return true;
			break;

		case 1561:// チャージか詠唱中の相手が *人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->expect_type === CAST || $char->expect_type === CHARGE)
					$expect++;
			}
			if($expect <= $Quantity) return true;
			break;

//------------------------ 毒
		case 1600:// 自分が毒状態
			if($My->STATE === POISON) return true;
			break;

		case 1610:// 毒状態の味方が **人以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($Quantity <= $poison) return true;
			break;

		case 1611:// 毒状態の味方が **人以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($poison <= $Quantity) return true;
			break;

		case 1612:// 毒状態の味方が **% 以上
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Quantity <= $Rate) return true;
			break;

		case 1613:// 毒状態の味方が **% 以下
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Rate <= $Quantity) return true;
			break;
//------------------------ 毒(敵)
		case 1615:// 毒状態の相手が **人以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($Quantity <= $poison) return true;
			break;

		case 1616:// 毒状態の相手が **人以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->STATE === POISON)
					$poison++;
			}
			if($poison <= $Quantity) return true;
			break;

		case 1612:// 毒状態の相手が **% 以上
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Quantity <= $Rate) return true;
			break;

		case 1613:// 毒状態の相手が **% 以下
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				$alive++;
				if($char->STATE === POISON)
					$poison++;
			}
			if(!$alive) return false;
			$Rate	= ($poison/$alive) * 100;
			if($Rate <= $Quantity) return true;
			break;
//------------------------ 隊列
		case 1700:// 自分が前列
			if($My->POSITION == FRONT) return true;
			break;

		case 1701:// 自分が後列
			if($My->POSITION == BACK) return true;
			break;

		case 1710:// 味方の 前列が**人以上
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity <= $front) return true;
			break;

		case 1711:// 味方の 前列が**人以下
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front <= $Quantity) return true;
			break;

		case 1712:// 味方の 前列が**人
			$front	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front == $Quantity) return true;
			break;

		case 1715:// 味方の 後列が**人以上
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity <= $back) return true;
			break;

		case 1716:// 味方の 後列が**人以下
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back <= $Quantity) return true;
			break;

		case 1717:// 味方の 後列が**人
			$back	= 0;
			foreach($MyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back == $Quantity) return true;
			break;

//------------------------ 隊列(敵)
		case 1750:// 相手の 前列が**人以上
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity <= $front) return true;
			break;

		case 1751:// 相手の 前列が**人以下
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($front <= $Quantity) return true;
			break;

		case 1752:// 相手の 前列が**人
			$front	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == FRONT)
					$front++;
			}
			if($Quantity == $front) return true;
			break;

		case 1755:// 相手の 後列が**人以上
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity <= $back) return true;
			break;

		case 1756:// 相手の 後列が**人以下
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($back <= $Quantity) return true;
			break;

		case 1757:// 相手の 後列が**人
			$back	= 0;
			foreach($EnemyTeam as $char) {
				if($char->STATE === DEAD) continue;
				if($char->POSITION == BACK)
					$back++;
			}
			if($Quantity == $back) return true;
			break;

//------------------------ 召喚
		case 1800:// 味方の 召喚キャラが **匹以上
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($Quantity <= $summon) return true;
			break;

		case 1801:// 味方の 召喚キャラが **匹以下
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon <= $Quantity) return true;
			break;

		case 1805:// 味方の 召喚キャラが **匹
			$summon	= 0;
			foreach($MyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon == $Quantity) return true;
			break;

//------------------------ 召喚(敵)
		case 1820:// 相手の 召喚キャラが **匹以上
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($Quantity <= $summon) return true;
			break;

		case 1821:// 相手の 召喚キャラが **匹以下
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon <= $Quantity) return true;
			break;

		case 1825:// 相手の 召喚キャラが **匹
			$summon	= 0;
			foreach($EnemyTeam as $char) {
				//if($char->STATE === DEAD) continue;
				if($char->summon)
					$summon++;
			}
			if($summon == $Quantity) return true;
			break;

//------------------------ 魔法陣
		case 1840:// 味方の魔法陣の数が **個以上
			if($Quantity <= $MyTeamMC)
				return true;
			break;
		case 1841:// 味方の魔法陣の数が **個以下
			if($MyTeamMC <= $Quantity)
				return true;
			break;
		case 1845:// 味方の魔法陣の数が **個
			if($Quantity == $MyTeamMC)
				return true;
			break;
//------------------------ 魔法陣(敵)
		case 1850:// 相手の魔法陣の数が **個以上
			if($Quantity <= $EnemyTeamMC)
				return true;
			break;
		case 1851:// 相手の魔法陣の数が **個以下
			if($EnemyTeamMC <= $Quantity)
				return true;
			break;
		case 1855:// 相手の魔法陣の数が **個
			if($Quantity == $EnemyTeamMC)
				return true;
			break;

//------------------------ 指定行動回数
		case 1900:// 自分の行動回数が **回以上
			if(($Quantity-1) <= $My->ActCount) return true;
			break;

		case 1901:// 自分の行動回数が **回以下
			if($My->ActCount <= ($Quantity-1)) return true;
			break;

		case 1902:// 自分の行動回数が **回目
			if($My->ActCount == ($Quantity-1)) return true;
			break;

//------------------------ 回数制限
		case 1920:// **回だけ必ず
			if($My->JdgCount[$number] < $Quantity) return true;
			break;

//------------------------ 確率
		case 1940:// **%の確率で
			$prob	= mt_rand(1,100);
			if($prob <= $Quantity) return true;
			break;


//------------------------ 特殊な判定
		case 9000:// 相手チームにLv**以上が居る。
			foreach($EnemyTeam as $char) {
				if($Quantity <= $char->level)
					return true;
			}
			break;
	}
}
//////////////////////////////////////////////////
//	SPにも代用できた。(?)
function &FuncTeamHpSpRate(&$TeamHpRate,$NO) {
	foreach($TeamHpRate as $key => $Rate) {
		if($Rate <= $NO)
			$target[]	= &$MyTeam[$key];
	}
	return $target ? $target : false;
}
?>