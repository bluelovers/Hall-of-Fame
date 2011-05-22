<?php
// 製作表
function CanCreate($user) {

/*	// ※表示に時間かかる 
	// アイテムデ一タにneedが設定されてるものを全て自動取得する
	for($i=1000; $i<10000; $i++) {
		$item	= LoadItemData($i);
		if(!$item) continue;
		if($item["need"])
			$create[]	= $i;
	}
	return $create;
*/
	// 劍
	$create	= array(1000,1001,1002,1003,1004,1005,1006,1007,1008,1020,1022,1023);
	// 雙手劍
	$create	= array_merge($create,
	array(1100,1101,1102,1103,1104,1120)
	);
	// 短劍
	$create	= array_merge($create,
	array(1201,1202,1203,1204,1205,1220,)
	);
	// 杖
	$create	= array_merge($create,
	array(1700,1701,1702,1703,1704,1705,1706,)
	);
	// 雙手杖
	$create	= array_merge($create,
	array(1800,1801,1802,1803,1810,1811,1812,)
	);
	// 弓
	$create	= array_merge($create,
	array(2000,2001,2002,2003,2004,2005,2006,2020,)
	);
	// 鞭
	$create	= array_merge($create,
	array(2200,2201,2202,2203,2210,2211,)
	);
	// 盾
	$create	= array_merge($create,
	array(3000,3001,3002,3003,3004,3005,3006,3007,3008,)
	);
	// 本
	$create	= array_merge($create,
	array(3101,3102,3103,3104,3105,)
	);
	// 鎧
	$create	= array_merge($create,
	array(5000,5001,5002,5003,5004,5005,5007,5008,5009,)
	);
	// 服
	$create	= array_merge($create,
	array(5100,5101,5102,5103,5104,5105,5106,5107,)
	);
	// 衣
	$create	= array_merge($create,
	array(5200,5201,5202,5203,5204,5205,5206,)
	);
	return $create;
}
// 判斷道具需求
function HaveNeeds($item,$UserItem) {
	// 沒有道具的情況
	if(!$UserItem) return false;
	// 對像到不不能做成情況下
	if(!$item["need"]) return false;
	foreach($item["need"] as $NeedNo => $Amount) {
		if($UserItem[$NeedNo] < $Amount)
			return false;
	}
	return true;
}

// 道具所返回的能力
function ItemAbilityPossibility($type) {
	switch($type) {
		case "劍":
		case "雙手劍":
		case "匕首":
		case "魔杖":
		case "杖":
		case "弓":
		case "鞭":
			$low	= array(
			// Atk+
			100,101,102,103,104,
			105,106,107,108,109,
			// Matk+
			150,151,152,153,154,
			155,156,157,158,159,
			// Atk*
			200,201,
			// Matk*
			250,251,
			// HP+
			H00,H01,H02,
			// HP*
			HM0,HM1,HM2,
			// SP+
			S00,S01,
			// SP*
			SM0,SM1,SM2,
			// STR+
			P00,P01,P02,P03,P04,
			// INT+
			I00,I01,I02,I03,I04,
			// DEX+
			D00,D01,D02,D03,D04,
			// SPD+
			A00,A01,A02,A03,A04,
			// LUK+
			L00,L01,L02,L03,L04,
			);
			$high	= array(
			// Atk+
			110,111,112,113,114,
			115,116,117,118,119,
			// Matk+
			160,161,162,163,164,
			165,166,167,168,169,
			// Atk*
			202,203,
			// Matk*
			252,253,
			// HP+
			H03,H04,H05,
			// HP*
			HM3,HM4,HM5,
			// SP+
			S02,S03,
			// SP*
			SM3,SM4,SM5,
			// STR+
			P05,P06,P07,P08,P09,
			// INT+
			I05,I06,I07,I08,I09,
			// DEX+
			D05,D06,D07,D08,D09,
			// SPD+
			A05,A06,A07,A08,A09,
			// LUK+
			L05,L06,L07,L08,L09,
			);
			break;
		case "盾":
		case "書":
		case "甲":
		case "衣服":
		case "長袍":
			$low	= array(
			// Def +
			300,301,
			// Mdef +
			350,351,
			// HP+
			H00,H01,H02,
			// HP*
			HM0,HM1,HM2,
			// SP+
			S00,S01,
			// SP*
			SM0,SM1,SM2,
			// STR+
			P00,P01,P02,P03,P04,
			// INT+
			I00,I01,I02,I03,I04,
			// DEX+
			D00,D01,D02,D03,D04,
			// SPD+
			A00,A01,A02,A03,A04,
			// LUK+
			L00,L01,L02,L03,L04,
			);
			$high	= array(
			// Def +
			302,303,304,
			// Mdef +
			352,353,354,
			// HP+
			H03,H04,H05,
			// HP*
			HM3,HM4,HM5,
			// SP+
			S02,S03,
			// SP*
			SM3,SM4,SM5,
			// STR+
			P05,P06,P07,P08,P09,
			// INT+
			I05,I06,I07,I08,I09,
			// DEX+
			D05,D06,D07,D08,D09,
			// SPD+
			A05,A06,A07,A08,A09,
			// LUK+
			L05,L06,L07,L08,L09,
			);
			break;
	}
	return array($low,$high);
}
?>