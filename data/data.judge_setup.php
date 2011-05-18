<?php
function LoadJudgeData($no) {
/*
	判断材料
	HP
	SP
	人数(自分だけ生存等)
	状態(毒等)？？？
	自分の行動回数
	回数限定
	相手の状態
	単純な確率
*/

	$Quantity	= '○○';

	switch($no) {

		case 1000:// 必ず
			$judge["exp"]	= "必ず";
			break;
		case 1001:// パス
			$judge["exp"]	= "次の判断へ";
			break;
//------------------------ HP
		case 1099:
			$judge["exp"]	= "HP";
			$judge["css"]	= true;
			break;
		case 1100:// 自分のHP が{$Quantity}(%)以上
			$judge["exp"]	= "自分のHP が{$Quantity}(%)以上";
			break;
		case 1101:// 自分のHP が{$Quantity}(%)以下
			$judge["exp"]	= "自分のHP が{$Quantity}(%)以下";
			break;

		case 1105:// 自分のHP が{$Quantity}以上
			$judge["exp"]	= "自分のHP が{$Quantity}以上";
			break;
		case 1106:// 自分のHP が{$Quantity}以下
			$judge["exp"]	= "自分のHP が{$Quantity}以下";
			break;

		case 1110:// 最大HP が{$Quantity}以上
			$judge["exp"]	= "最大HP が{$Quantity}以上";
			break;
		case 1111:// 最大HP が{$Quantity}以下
			$judge["exp"]	= "最大HP が{$Quantity}以下";
			break;

		case 1121:// 味方に HPが{$Quantity}(%)以下のキャラ がいる時
			$judge["exp"]	= "味方に HPが{$Quantity}(%)以下のキャラ がいる";
			break;

		case 1125:// 味方の 平均HPが {$Quantity}(%)以上の時
			$judge["exp"]	= "味方の 平均HPが {$Quantity}(%)以上";
			break;
		case 1126:// 味方の 平均HPが {$Quantity}(%)以下の時
			$judge["exp"]	= "味方の 平均HPが {$Quantity}(%)以下";
			break;
//------------------------ SP
		case 1199:
			$judge["exp"]	= "SP";
			$judge["css"]	= true;
			break;
		case 1200:// 自分のSP が{$Quantity}(%)以上
			$judge["exp"]	= "自分のSP が{$Quantity}(%)以上";
			break;
		case 1201:// 自分のSP が{$Quantity}(%)以下
			$judge["exp"]	= "自分のSP が{$Quantity}(%)以下";
			break;

		case 1205:// 自分のSP が{$Quantity}以上
			$judge["exp"]	= "自分のSP が{$Quantity}以上";
			break;
		case 1206:// 自分のSP が{$Quantity}以下
			$judge["exp"]	= "自分のSP が{$Quantity}以下";
			break;

		case 1210:// 最大SP が{$Quantity}以上
			$judge["exp"]	= "最大SP が{$Quantity}以上";
			break;
		case 1211:// 最大SP が{$Quantity}以下
			$judge["exp"]	= "最大SP が{$Quantity}以下";
			break;

		case 1221:// 味方に SPが{$Quantity}(%)以下のキャラ がいる時
			$judge["exp"]	= "味方に SPが{$Quantity}(%)以下のキャラ がいる";
			break;

		case 1225:// 味方の 平均SPが {$Quantity}(%)以上の時
			$judge["exp"]	= "味方の 平均SPが {$Quantity}(%)以上";
			break;
		case 1226:// 味方の 平均SPが {$Quantity}(%)以下の時
			$judge["exp"]	= "味方の 平均SPが {$Quantity}(%)以下";
			break;
/*
//------------------------ STR
		case 1299:
			$judge["exp"]	= "STR";
			break;
		case 1300:// 自分のSTRが{$Quantity} 以上
			$judge["exp"]	= "自分のSTRが{$Quantity} 以上";
			break;
		case 1301:// 自分のSTRが{$Quantity} 以下
			$judge["exp"]	= "自分のSTRが{$Quantity} 以下";
			break;
//------------------------ INT
		case 1309:
			$judge["exp"]	= "INT";
			break;
		case 1310:// 自分のINTが{$Quantity} 以上
			$judge["exp"]	= "自分のINTが{$Quantity} 以上";
			break;
		case 1311:// 自分のINTが{$Quantity} 以下
			$judge["exp"]	= "自分のINTが{$Quantity} 以下";
			break;
//------------------------ DEX
		case 1319:
			$judge["exp"]	= "DEX";
			break;
		case 1320:// 自分のDEXが{$Quantity} 以上
			$judge["exp"]	= "自分のDEXが{$Quantity} 以上";
			break;
		case 1321:// 自分のDEXが{$Quantity} 以下
			$judge["exp"]	= "自分のDEXが{$Quantity} 以下";
			break;
//------------------------ SPD
		case 1329:
			$judge["exp"]	= "SPD";
			break;
		case 1330:// 自分のSPDが{$Quantity} 以上
			$judge["exp"]	= "自分のSPDが{$Quantity} 以上";
			break;
		case 1331:// 自分のSPDが{$Quantity} 以下
			$judge["exp"]	= "自分のSPDが{$Quantity} 以下";
			break;
//------------------------ LUK
		case 1339:
			$judge["exp"]	= "LUK";
			break;
		case 1340:// 自分のLUKが{$Quantity} 以上
			$judge["exp"]	= "自分のLUKが{$Quantity} 以上";
			break;
		case 1341:// 自分のLUKが{$Quantity} 以下
			$judge["exp"]	= "自分のLUKが{$Quantity} 以下";
			break;
//------------------------ ATK
		case 1349:
			$judge["exp"]	= "ATK";
			break;
		case 1350:// 自分のATKが{$Quantity} 以上
			$judge["exp"]	= "自分のATKが{$Quantity} 以上";
			break;
		case 1351:// 自分のATKが{$Quantity} 以下
			$judge["exp"]	= "自分のATKが{$Quantity} 以下";
			break;
//------------------------ MATK
		case 1359:
			$judge["exp"]	= "MATK";
			break;
		case 1360:// 自分のMATKが{$Quantity} 以上
			$judge["exp"]	= "自分のMATKが{$Quantity} 以上";
			break;
		case 1361:// 自分のMATKが{$Quantity} 以下
			$judge["exp"]	= "自分のMATKが{$Quantity} 以下";
			break;
//------------------------ DEF
		case 1369:
			$judge["exp"]	= "DEF";
			break;
		case 1370:// 自分のDEFが{$Quantity} 以上
			$judge["exp"]	= "自分のDEFが{$Quantity} 以上";
			break;
		case 1371:// 自分のDEFが{$Quantity} 以下
			$judge["exp"]	= "自分のDEFが{$Quantity} 以下";
			break;
//------------------------ MDEF
		case 1379:
			$judge["exp"]	= "MDEF";
			break;
		case 1380:// 自分のMDEFが{$Quantity} 以上
			$judge["exp"]	= "自分のMDEFが{$Quantity} 以上";
			break;
		case 1381:// 自分のMDEFが{$Quantity} 以下
			$judge["exp"]	= "自分のMDEFが{$Quantity} 以下";
			break;
*/
//------------------------ 生死(味方)
		case 1399:
			$judge["exp"]	= "生死";
			$judge["css"]	= true;
			break;
		case 1400:// 味方の生存者が {$Quantity}人以上
			$judge["exp"]	= "味方の生存者が {$Quantity}人以上";
			break;
		case 1401:// 味方の生存者が {$Quantity}人以下
			$judge["exp"]	= "味方の生存者が {$Quantity}人以下";
			break;
		case 1405:// 味方の死者が {$Quantity}人以上
			$judge["exp"]	= "味方の死者が {$Quantity}人以上";
			break;
		case 1406:// 味方の死者が {$Quantity}人以下
			$judge["exp"]	= "味方の死者が {$Quantity}人以下";
			break;

		case 1410:// 味方で前衛の生存者が {$Quantity}人以上
			$judge["exp"]	= "(初期設定が)前衛の生存者 {$Quantity}人以上";
			break;
//------------------------ 生死(敵)
		case 1449:
			$judge["exp"]	= "生死(敵)";
			$judge["css"]	= true;
			break;
		case 1450:// 相手の生存者が {$Quantity}人以上
			$judge["exp"]	= "相手の生存者が {$Quantity}人以上";
			break;
		case 1451:// 相手の生存者が {$Quantity}人以下
			$judge["exp"]	= "相手の生存者が {$Quantity}人以下";
			break;
		case 1455:// 相手の死者が {$Quantity}人以上
			$judge["exp"]	= "相手の死者が {$Quantity}人以上";
			break;
		case 1456:// 相手の死者が {$Quantity}人以下
			$judge["exp"]	= "相手の死者が {$Quantity}人以下";
			break;
//------------------------ チャージ+詠唱
		case 1499:
			$judge["exp"]	= "チャージ+詠唱";
			$judge["css"]	= true;
			break;
		case 1500:// チャージ中のキャラが {$Quantity}人以上
			$judge["exp"]	= "チャージ中のキャラが {$Quantity}人以上";
			break;
		case 1501:// チャージ中のキャラが {$Quantity}人以下
			$judge["exp"]	= "チャージ中のキャラが {$Quantity}人以下";
			break;
		case 1505:// 詠唱中のキャラが {$Quantity}人以上
			$judge["exp"]	= "詠唱中のキャラが {$Quantity}人以上";
			break;
		case 1506:// 詠唱中のキャラが {$Quantity}人以下
			$judge["exp"]	= "詠唱中のキャラが {$Quantity}人以下";
			break;
		case 1510:// チャージか詠唱中のキャラが {$Quantity}人以上
			$judge["exp"]	= "チャージか詠唱中のキャラが {$Quantity}人以上";
			break;
		case 1511:// チャージか詠唱中のキャラが {$Quantity}人以下
			$judge["exp"]	= "チャージか詠唱中のキャラが {$Quantity}人以下";
			break;
//------------------------ チャージ+詠唱(敵)
		case 1549:
			$judge["exp"]	= "チャージ+詠唱(敵)";
			$judge["css"]	= true;
			break;
		case 1550:// チャージ中の相手が {$Quantity}人以上
			$judge["exp"]	= "チャージ中の相手が {$Quantity}人以上";
			break;
		case 1551:// チャージ中の相手が {$Quantity}人以下
			$judge["exp"]	= "チャージ中の相手が {$Quantity}人以下";
			break;
		case 1555:// 詠唱中の相手が {$Quantity}人以上
			$judge["exp"]	= "詠唱中の相手が {$Quantity}人以上";
			break;
		case 1556:// 詠唱中の相手が {$Quantity}人以下
			$judge["exp"]	= "詠唱中の相手が {$Quantity}人以下";
			break;
		case 1560:// チャージか詠唱中の相手が {$Quantity}人以上
			$judge["exp"]	= "チャージか詠唱中の相手が {$Quantity}人以上";
			break;
		case 1561:// チャージか詠唱中の相手が {$Quantity}人以下
			$judge["exp"]	= "チャージか詠唱中の相手が {$Quantity}人以下";
			break;
//------------------------ 毒
		case 1599:
			$judge["exp"]	= "毒";
			$judge["css"]	= true;
			break;
		case 1600:// 自分が毒状態
			$judge["exp"]	= "自分が 毒状態";
			break;
		case 1610:// 毒状態の味方が {$Quantity}人以上
			$judge["exp"]	= "毒状態の味方が {$Quantity}人以上";
			break;
		case 1611:// 毒状態の味方が {$Quantity}人以下
			$judge["exp"]	= "毒状態の味方が {$Quantity}人以下";
			break;
		case 1612:// 毒状態の味方が {$Quantity}% 以下
			$judge["exp"]	= "毒状態の味方が {$Quantity}% 以上";
			break;
		case 1613:// 毒状態の味方が {$Quantity}% 以下
			$judge["exp"]	= "毒状態の味方が {$Quantity}% 以下";
			break;
//------------------------ 毒(敵)
		case 1614:
			$judge["exp"]	= "毒(敵)";
			$judge["css"]	= true;
			break;
		case 1615:// 毒状態の相手が {$Quantity}人以上
			$judge["exp"]	= "毒状態の相手が {$Quantity}人以上";
			break;
		case 1616:// 毒状態の相手が {$Quantity}人以下
			$judge["exp"]	= "毒状態の相手が {$Quantity}人以下";
			break;
		case 1617:// 毒状態の相手が {$Quantity}% 以下
			$judge["exp"]	= "毒状態の相手が {$Quantity}% 以上";
			break;
		case 1618:// 毒状態の相手が {$Quantity}% 以下
			$judge["exp"]	= "毒状態の相手が {$Quantity}% 以下";
			break;
//------------------------ 隊列
		case 1699:
			$judge["exp"]	= "隊列";
			$judge["css"]	= true;
			break;
		case 1700:// 自分が前列
			$judge["exp"]	= "自分が 前列";
			break;
		case 1701:// 自分が後列
			$judge["exp"]	= "自分が 後列";
			break;

		case 1710:// 味方の 前列が{$Quantity}人以上
			$judge["exp"]	= "味方の 前列が{$Quantity}人以上";
			break;
		case 1711:// 味方の 前列が{$Quantity}人以下
			$judge["exp"]	= "味方の 前列が{$Quantity}人以下";
			break;
		case 1712:// 味方の 前列が{$Quantity}人以下
			$judge["exp"]	= "味方の 前列が{$Quantity}人";
			break;

		case 1715:// 味方の 後列が{$Quantity}人以上
			$judge["exp"]	= "味方の 後列が{$Quantity}人以上";
			break;
		case 1716:// 味方の 後列が{$Quantity}人以下
			$judge["exp"]	= "味方の 後列が{$Quantity}人以下";
			break;
		case 1717:// 味方の 後列が{$Quantity}人以下
			$judge["exp"]	= "味方の 後列が{$Quantity}人";
			break;
//------------------------ 隊列(敵)
		case 1749:
			$judge["exp"]	= "隊列(敵)";
			$judge["css"]	= true;
			break;
		case 1750:// 相手の 前列が{$Quantity}人以上
			$judge["exp"]	= "相手の 前列が{$Quantity}人以上";
			break;
		case 1751:// 相手の 前列が{$Quantity}人以下
			$judge["exp"]	= "相手の 前列が{$Quantity}人以下";
			break;
		case 1752:// 相手の 前列が{$Quantity}人
			$judge["exp"]	= "相手の 前列が{$Quantity}人";
			break;

		case 1755:// 相手の 後列が{$Quantity}人以上
			$judge["exp"]	= "相手の 後列が{$Quantity}人以上";
			break;
		case 1756:// 相手の 後列が{$Quantity}人以下
			$judge["exp"]	= "相手の 後列が{$Quantity}人以下";
			break;
		case 1757:// 相手の 後列が{$Quantity}人
			$judge["exp"]	= "相手の 後列が{$Quantity}人";
			break;
//------------------------ 召喚
		case 1799:
			$judge["exp"]	= "召喚";
			$judge["css"]	= true;
			break;
		case 1800:// 味方の 召喚キャラが {$Quantity}匹以上
			$judge["exp"]	= "味方の 召喚キャラが {$Quantity}匹以上";
			break;
		case 1801:// 味方の 召喚キャラが {$Quantity}匹以下
			$judge["exp"]	= "味方の 召喚キャラが {$Quantity}匹以下";
			break;
		case 1805:// 味方の 召喚キャラが {$Quantity}匹
			$judge["exp"]	= "味方の 召喚キャラが {$Quantity}匹";
			break;
//------------------------ 召喚(敵)
		case 1819:
			$judge["exp"]	= "召喚(敵)";
			$judge["css"]	= true;
			break;
		case 1820:// 相手の 召喚キャラが {$Quantity}匹以上
			$judge["exp"]	= "相手の 召喚キャラが {$Quantity}匹以上";
			break;
		case 1821:// 相手の 召喚キャラが {$Quantity}匹以下
			$judge["exp"]	= "相手の 召喚キャラが {$Quantity}匹以下";
			break;
		case 1825:// 相手の 召喚キャラが {$Quantity}匹
			$judge["exp"]	= "相手の 召喚キャラが {$Quantity}匹";
			break;

//------------------------ 魔法陣
		case 1839:
			$judge["exp"]	= "魔法陣";
			$judge["css"]	= true;
			break;
		case 1840:// 味方の魔法陣の数が {$Quantity}個以上
			$judge["exp"]	= "味方の魔法陣の数が {$Quantity}個以上";
			break;
		case 1841:// 味方の魔法陣の数が {$Quantity}個以下
			$judge["exp"]	= "味方の魔法陣の数が {$Quantity}個以下";
			break;
		case 1845:// 味方の魔法陣の数が {$Quantity}個
			$judge["exp"]	= "味方の魔法陣の数が {$Quantity}個";
			break;
//------------------------ 魔法陣(敵)
		case 1849:
			$judge["exp"]	= "魔法陣(敵)";
			$judge["css"]	= true;
			break;
		case 1850:// 相手の魔法陣の数が {$Quantity}個以上
			$judge["exp"]	= "相手の魔法陣の数が {$Quantity}個以上";
			break;
		case 1851:// 相手の魔法陣の数が {$Quantity}個以下
			$judge["exp"]	= "相手の魔法陣の数が {$Quantity}個以下";
			break;
		case 1855:// 相手の魔法陣の数が {$Quantity}個
			$judge["exp"]	= "相手の魔法陣の数が {$Quantity}個";
			break;

//------------------------ 指定行動回数
		case 1899:
			$judge["exp"]	= "指定行動回数";
			$judge["css"]	= true;
			break;
		case 1900:// 自分の行動回数が {$Quantity}回以上
			$judge["exp"]	= "自分の行動が {$Quantity}回以上";
			break;
		case 1901:// 自分の行動回数が {$Quantity}回以下
			$judge["exp"]	= "自分の行動が {$Quantity}回以下";
			break;
		case 1902:// 自分の行動回数が {$Quantity}回目
			$judge["exp"]	= "自分の {$Quantity}回目の行動";
			break;
//------------------------ 回数制限
		case 1919:
			$judge["exp"]	= "回数制限";
			$judge["css"]	= true;
			break;
		case 1920:// {$Quantity}回だけ必ず
			$judge["exp"]	= "{$Quantity}回だけ 必ず";
			break;
//------------------------ 確率
		case 1939:
			$judge["exp"]	= "確率";
			$judge["css"]	= true;
			break;
		case 1940:// {$Quantity}%の確率で
			$judge["exp"]	= "{$Quantity}%の 確率で";
			break;


//----------------------- 特殊
		case 9000:// 相手チームにLv**以上が居る。
			$judge["exp"]	= "相手チームに Lv{$Quantity}以上が居る";
			break;




		default:
$judge	= false;
	}

	return $judge;
}
?>