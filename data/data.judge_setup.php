<?php
function LoadJudgeData($no) {
/*
	判定基礎
	HP
	SP
	人數(自己的生存者數)
	狀態（毒）？？？
	自己的行動回數
	回合限定
	對手的狀態
	單純的概率
*/
	$Quantity	= '←←';
	switch($no) {
		case 1000:// 必定
			$judge["exp"]	= "必定";
			break;
		case 1001:// pass
			$judge["exp"]	= "跳過此判斷";
			break;
//------------------------ HP
		case 1099:
			$judge["exp"]	= "HP";
			$judge["css"]	= true;
			break;
		case 1100:// 自己的HP {$Quantity}(%)以上
			$judge["exp"]	= "自己的HP {$Quantity}(%)以上";
			break;
		case 1101:// 自己的HP {$Quantity}(%)以下
			$judge["exp"]	= "自己的HP {$Quantity}(%)以下";
			break;
		case 1105:// 自己的HP {$Quantity}以上
			$judge["exp"]	= "自己的HP {$Quantity}以上";
			break;
		case 1106:// 自己的HP {$Quantity}以下
			$judge["exp"]	= "自己的HP {$Quantity}以下";
			break;
		case 1110:// 最大HP {$Quantity}以上
			$judge["exp"]	= "最大HP {$Quantity}以上";
			break;
		case 1111:// 最大HP {$Quantity}以下
			$judge["exp"]	= "最大HP {$Quantity}以下";
			break;
		case 1121:// 我方 HP{$Quantity}(%)以下HP
			$judge["exp"]	= "我方 HP{$Quantity}(%)以下";
			break;
		case 1125:// 我方平均HP {$Quantity}(%)以上の箕
			$judge["exp"]	= "我方平均HP {$Quantity}(%)以上";
			break;
		case 1126:// 我方平均HP {$Quantity}(%)以下の箕
			$judge["exp"]	= "我方平均HP {$Quantity}(%)以下";
			break;
//------------------------ SP
		case 1199:
			$judge["exp"]	= "SP";
			$judge["css"]	= true;
			break;
		case 1200:// 自己的SP{$Quantity}(%)以上
			$judge["exp"]	= "自己的SP{$Quantity}(%)以上";
			break;
		case 1201:// 自己的SP{$Quantity}(%)以下
			$judge["exp"]	= "自己的SP{$Quantity}(%)以下";
			break;
		case 1205:// 自己的SP{$Quantity}以上
			$judge["exp"]	= "自己的SP{$Quantity}以上";
			break;
		case 1206:// 自己的SP{$Quantity}以下
			$judge["exp"]	= "自己的SP{$Quantity}以下";
			break;
		case 1210:// 最大SP{$Quantity}以上
			$judge["exp"]	= "最大SP{$Quantity}以上";
			break;
		case 1211:// 最大SP{$Quantity}以下
			$judge["exp"]	= "最大SP{$Quantity}以下";
			break;
		case 1221:// 我方 SP{$Quantity}(%)以下HP
			$judge["exp"]	= "我方 SP{$Quantity}(%)以下";
			break;
		case 1225:// 我方平均SP {$Quantity}(%)以上の箕
			$judge["exp"]	= "我方平均SP {$Quantity}(%)以上";
			break;
		case 1226:// 我方平均SP {$Quantity}(%)以下の箕
			$judge["exp"]	= "我方平均SP {$Quantity}(%)以下";
			break;
/*
//------------------------ STR
		case 1299:
			$judge["exp"]	= "STR";
			break;
		case 1300:// 自己的STR{$Quantity} 以上
			$judge["exp"]	= "自己的STR{$Quantity} 以上";
			break;
		case 1301:// 自己的STR{$Quantity} 以下
			$judge["exp"]	= "自己的STR{$Quantity} 以下";
			break;
//------------------------ INT
		case 1309:
			$judge["exp"]	= "INT";
			break;
		case 1310:// 自己的INT{$Quantity} 以上
			$judge["exp"]	= "自己的INT{$Quantity} 以上";
			break;
		case 1311:// 自己的INT{$Quantity} 以下
			$judge["exp"]	= "自己的INT{$Quantity} 以下";
			break;
//------------------------ DEX
		case 1319:
			$judge["exp"]	= "DEX";
			break;
		case 1320:// 自己的DEX{$Quantity} 以上
			$judge["exp"]	= "自己的DEX{$Quantity} 以上";
			break;
		case 1321:// 自己的DEX{$Quantity} 以下
			$judge["exp"]	= "自己的DEX{$Quantity} 以下";
			break;
//------------------------ SPD
		case 1329:
			$judge["exp"]	= "SPD";
			break;
		case 1330:// 自己的SPD{$Quantity} 以上
			$judge["exp"]	= "自己的SPD{$Quantity} 以上";
			break;
		case 1331:// 自己的SPD{$Quantity} 以下
			$judge["exp"]	= "自己的SPD{$Quantity} 以下";
			break;
//------------------------ LUK
		case 1339:
			$judge["exp"]	= "LUK";
			break;
		case 1340:// 自己的LUK{$Quantity} 以上
			$judge["exp"]	= "自己的LUK{$Quantity} 以上";
			break;
		case 1341:// 自己的LUK{$Quantity} 以下
			$judge["exp"]	= "自己的LUK{$Quantity} 以下";
			break;
//------------------------ ATK
		case 1349:
			$judge["exp"]	= "ATK";
			break;
		case 1350:// 自己的ATK{$Quantity} 以上
			$judge["exp"]	= "自己的ATK{$Quantity} 以上";
			break;
		case 1351:// 自己的ATK{$Quantity} 以下
			$judge["exp"]	= "自己的ATK{$Quantity} 以下";
			break;
//------------------------ MATK
		case 1359:
			$judge["exp"]	= "MATK";
			break;
		case 1360:// 自己的MATK{$Quantity} 以上
			$judge["exp"]	= "自己的MATK{$Quantity} 以上";
			break;
		case 1361:// 自己的MATK{$Quantity} 以下
			$judge["exp"]	= "自己的MATK{$Quantity} 以下";
			break;
//------------------------ DEF
		case 1369:
			$judge["exp"]	= "DEF";
			break;
		case 1370:// 自己的DEF{$Quantity} 以上
			$judge["exp"]	= "自己的DEF{$Quantity} 以上";
			break;
		case 1371:// 自己的DEF{$Quantity} 以下
			$judge["exp"]	= "自己的DEF{$Quantity} 以下";
			break;
//------------------------ MDEF
		case 1379:
			$judge["exp"]	= "MDEF";
			break;
		case 1380:// 自己的MDEF{$Quantity} 以上
			$judge["exp"]	= "自己的MDEF{$Quantity} 以上";
			break;
		case 1381:// 自己的MDEF{$Quantity} 以下
			$judge["exp"]	= "自己的MDEF{$Quantity} 以下";
			break;
*/
//------------------------ 生死(己方)
		case 1399:
			$judge["exp"]	= "生死";
			$judge["css"]	= true;
			break;
		case 1400:// 我方的生存者 {$Quantity}人以上
			$judge["exp"]	= "我方的生存者 {$Quantity}人以上";
			break;
		case 1401:// 我方的生存者 {$Quantity}人以下
			$judge["exp"]	= "我方的生存者 {$Quantity}人以下";
			break;
		case 1405:// 我方的死者 {$Quantity}人以上
			$judge["exp"]	= "我方的死者 {$Quantity}人以上";
			break;
		case 1406:// 我方的死者 {$Quantity}人以下
			$judge["exp"]	= "我方的死者 {$Quantity}人以下";
			break;
		case 1410:// 己方前排的生存 {$Quantity}人以上
			$judge["exp"]	= "我方前排的生存者 {$Quantity}人以上";
			break;
//------------------------ 生死（敵）
		case 1449:
			$judge["exp"]	= "生死(敵)";
			$judge["css"]	= true;
			break;
		case 1450:// 敵方的生存者 {$Quantity}人以上
			$judge["exp"]	= "敵方的生存者 {$Quantity}人以上";
			break;
		case 1451:// 敵方的生存者 {$Quantity}人以下
			$judge["exp"]	= "敵方的生存者 {$Quantity}人以下";
			break;
		case 1455:// 敵方的死者 {$Quantity}人以上
			$judge["exp"]	= "敵方的死者 {$Quantity}人以上";
			break;
		case 1456:// 敵方的死者 {$Quantity}人以下
			$judge["exp"]	= "敵方的死者 {$Quantity}人以下";
			break;
//------------------------ 詠唱
		case 1499:
			$judge["exp"]	= "蓄力+詠唱";
			$judge["css"]	= true;
			break;
		case 1500:// 處於詠唱狀態的 {$Quantity}人以上
			$judge["exp"]	= "蓄力狀態的 {$Quantity}人以上";
			break;
		case 1501:// 處於詠唱狀態的 {$Quantity}人以下
			$judge["exp"]	= "蓄力狀態的 {$Quantity}人以下";
			break;
		case 1505:// 蓄力詠唱狀態的{$Quantity}人以上
			$judge["exp"]	= "詠唱狀態的{$Quantity}人以上";
			break;
		case 1506:// 蓄力詠唱狀態的{$Quantity}人以下
			$judge["exp"]	= "詠唱狀態的{$Quantity}人以下";
			break;
		case 1510:// 蓄力詠唱狀態的{$Quantity}人以上
			$judge["exp"]	= "蓄力詠唱狀態的{$Quantity}人以上";
			break;
		case 1511:// 蓄力詠唱狀態的{$Quantity}人以下
			$judge["exp"]	= "蓄力詠唱狀態的{$Quantity}人以下";
			break;
//------------------------ 詠唱(敵)
		case 1549:
			$judge["exp"]	= "詠唱(敵)";
			$judge["css"]	= true;
			break;
		case 1550:// 敵方蓄力狀態的 {$Quantity}人以上
			$judge["exp"]	= "敵方蓄力狀態的 {$Quantity}人以上";
			break;
		case 1551:// 敵方蓄力狀態的 {$Quantity}人以下
			$judge["exp"]	= "敵方蓄力狀態的 {$Quantity}人以下";
			break;
		case 1555:// 敵方詠唱狀態的 {$Quantity}人以上
			$judge["exp"]	= "敵方詠唱狀態的 {$Quantity}人以上";
			break;
		case 1556:// 敵方詠唱狀態的 {$Quantity}人以下
			$judge["exp"]	= "敵方詠唱狀態的 {$Quantity}人以下";
			break;
		case 1560:// 蓄力敵方詠唱狀態的 {$Quantity}人以上
			$judge["exp"]	= "敵方詠唱蓄力狀態的 {$Quantity}人以上";
			break;
		case 1561:// 蓄力敵方詠唱狀態的 {$Quantity}人以下
			$judge["exp"]	= "敵方蓄力詠唱狀態的 {$Quantity}人以下";
			break;
//------------------------ 毒
		case 1599:
			$judge["exp"]	= "毒";
			$judge["css"]	= true;
			break;
		case 1600:// 極屍毒覺輪
			$judge["exp"]	= "自己處於毒狀態";
			break;
		case 1610:// 我方毒狀態 {$Quantity}人以上
			$judge["exp"]	= "我方毒狀態 {$Quantity}人以上";
			break;
		case 1611:// 我方毒狀態 {$Quantity}人以下
			$judge["exp"]	= "我方毒狀態 {$Quantity}人以下";
			break;
		case 1612:// 我方毒狀態 {$Quantity}% 以下
			$judge["exp"]	= "我方毒狀態 {$Quantity}% 以上";
			break;
		case 1613:// 我方毒狀態 {$Quantity}% 以下
			$judge["exp"]	= "我方毒狀態 {$Quantity}% 以下";
			break;
//------------------------ 毒(敵)
		case 1614:
			$judge["exp"]	= "毒(敵)";
			$judge["css"]	= true;
			break;
		case 1615:// 敵方毒狀態 {$Quantity}人以上
			$judge["exp"]	= "敵方毒狀態 {$Quantity}人以上";
			break;
		case 1616:// 敵方毒狀態 {$Quantity}人以下
			$judge["exp"]	= "敵方毒狀態 {$Quantity}人以下";
			break;
		case 1617:// 敵方毒狀態 {$Quantity}% 以下
			$judge["exp"]	= "敵方毒狀態 {$Quantity}% 以上";
			break;
		case 1618:// 敵方毒狀態 {$Quantity}% 以下
			$judge["exp"]	= "敵方毒狀態 {$Quantity}% 以下";
			break;
//------------------------ 隊列
		case 1699:
			$judge["exp"]	= "隊列";
			$judge["css"]	= true;
			break;
		case 1700:// 自己在前排
			$judge["exp"]	= "自己在前排";
			break;
		case 1701:// 自己在後排
			$judge["exp"]	= "自己在後排";
			break;
		case 1710:// 我方前排{$Quantity}人以上
			$judge["exp"]	= "我方前排{$Quantity}人以上";
			break;
		case 1711:// 我方前排{$Quantity}人以下
			$judge["exp"]	= "我方前排{$Quantity}人以下";
			break;
		case 1712:// 我方前排{$Quantity}人以下
			$judge["exp"]	= "我方前排{$Quantity}人";
			break;
		case 1715:// 我方後排{$Quantity}人以上
			$judge["exp"]	= "我方後排{$Quantity}人以上";
			break;
		case 1716:// 我方後排{$Quantity}人以下
			$judge["exp"]	= "我方後排{$Quantity}人以下";
			break;
		case 1717:// 我方後排{$Quantity}人以下
			$judge["exp"]	= "我方後排{$Quantity}人";
			break;
//------------------------ 隊列(敵)
		case 1749:
			$judge["exp"]	= "隊列(敵)";
			$judge["css"]	= true;
			break;
		case 1750:// 敵方前排{$Quantity}人以上
			$judge["exp"]	= "敵方前排{$Quantity}人以上";
			break;
		case 1751:// 敵方前排{$Quantity}人以下
			$judge["exp"]	= "敵方前排{$Quantity}人以下";
			break;
		case 1752:// 敵方前排{$Quantity}人
			$judge["exp"]	= "敵方前排{$Quantity}人";
			break;
		case 1755:// 敵方後排{$Quantity}人以上
			$judge["exp"]	= "敵方後排{$Quantity}人以上";
			break;
		case 1756:// 敵方後排{$Quantity}人以下
			$judge["exp"]	= "敵方後排{$Quantity}人以下";
			break;
		case 1757:// 敵方後排{$Quantity}人
			$judge["exp"]	= "敵方後排{$Quantity}人";
			break;
//------------------------ 召喚
		case 1799:
			$judge["exp"]	= "召喚";
			$judge["css"]	= true;
			break;
		case 1800:// 我方的召喚物 {$Quantity}匹以上
			$judge["exp"]	= "我方的召喚物 {$Quantity}匹以上";
			break;
		case 1801:// 我方的召喚物 {$Quantity}匹以下
			$judge["exp"]	= "我方的召喚物 {$Quantity}匹以下";
			break;
		case 1805:// 我方的召喚物 {$Quantity}匹
			$judge["exp"]	= "我方的召喚物 {$Quantity}匹";
			break;
//------------------------ 召喚(敵)
		case 1819:
			$judge["exp"]	= "召喚(敵)";
			$judge["css"]	= true;
			break;
		case 1820:// 敵方的召喚物 {$Quantity}匹以上
			$judge["exp"]	= "敵方的召喚物 {$Quantity}匹以上";
			break;
		case 1821:// 敵方的召喚物 {$Quantity}匹以下
			$judge["exp"]	= "敵方的召喚物 {$Quantity}匹以下";
			break;
		case 1825:// 敵方的召喚物 {$Quantity}匹
			$judge["exp"]	= "敵方的召喚物 {$Quantity}匹";
			break;
//------------------------ 魔法陣
		case 1839:
			$judge["exp"]	= "魔法陣";
			$judge["css"]	= true;
			break;
		case 1840:// 我方的魔法陣數 {$Quantity}個以上
			$judge["exp"]	= "我方的魔法陣數 {$Quantity}個以上";
			break;
		case 1841:// 我方的魔法陣數 {$Quantity}個以下
			$judge["exp"]	= "我方的魔法陣數 {$Quantity}個以下";
			break;
		case 1845:// 我方的魔法陣數 {$Quantity}個
			$judge["exp"]	= "我方的魔法陣數 {$Quantity}個";
			break;
//------------------------ 魔法陣(敵)
		case 1849:
			$judge["exp"]	= "魔法陣(敵)";
			$judge["css"]	= true;
			break;
		case 1850:// 敵方的魔法陣數 {$Quantity}個以上
			$judge["exp"]	= "敵方的魔法陣數 {$Quantity}個以上";
			break;
		case 1851:// 敵方的魔法陣數 {$Quantity}個以下
			$judge["exp"]	= "敵方的魔法陣數 {$Quantity}個以下";
			break;
		case 1855:// 敵方的魔法陣數 {$Quantity}個
			$judge["exp"]	= "敵方的魔法陣數 {$Quantity}個";
			break;

//------------------------ 指定行動回數
		case 1899:
			$judge["exp"]	= "指定行動回數";
			$judge["css"]	= true;
			break;
		case 1900:// 自己的行動回數 {$Quantity}回以上
			$judge["exp"]	= "自己的行動回數 {$Quantity}回以上";
			break;
		case 1901:// 自己的行動回數 {$Quantity}回以下
			$judge["exp"]	= "自己的行動回數 {$Quantity}回以下";
			break;
		case 1902:// 自己的第 {$Quantity}回合
			$judge["exp"]	= "自己的第 {$Quantity}回合";
			break;
//------------------------ 回合限制
		case 1919:
			$judge["exp"]	= "回合限制";
			$judge["css"]	= true;
			break;
		case 1920:// {$Quantity}回 必定"
			$judge["exp"]	= "第{$Quantity}回 必定";
			break;
//------------------------ 概率
		case 1939:
			$judge["exp"]	= "概率";
			$judge["css"]	= true;
			break;
		case 1940:// {$Quantity}%的概率
			$judge["exp"]	= "{$Quantity}%的概率";
			break;
//----------------------- 特殊
		case 9000:// 敵方Lv超過以上。
			$judge["exp"]	= "敵方Lv超過{$Quantity}以上";
			break;
		default:
$judge	= false;
	}
	return $judge;
}
?>