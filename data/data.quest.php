<?php
// 製作表
function CanQuest($user) {

// ※表示に時間かかる 
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
	$Quest	= array(6810,6811,6812,);
	$Quest	= array_merge($Quest,
	array(6821,6823,6831,9020,9006,6163,)
	);
	//強化用材料
	$Quest	= array_merge($Quest,
	array(7137,7141,7142,7143,7145,7146,7150,7160,6902,6832,6833,6834,6835,)
	);
	//榮譽之心
	$Quest	= array_merge($Quest,
	array(9036,9037,9038,9039,9040,9041,9042,9043,9044,9045,)
	);
	
	return $Quest;

	
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
		case "太刀":
		case "匕首":
		case "魔杖":
		case "杖":
		case "弓":
		case "鞭":
		case "招魂幡":
		case "手槍":
		case "長槍":
		case "副手槍":
		case "權杖":
		case "斧":
		case "槍":
		case "矛":
		case "契約石":
		case "邪劍":
		case "手裡劍":
		case "鐮刀":
		case "戰斧":
		case "聖十字":
		case "雙劍(副)":
		case "雙劍(主)":
		case "爪":
		case "魔法掃帚":
		
		
			$low	= array(
			// Atk+
			100,101,102,103,104,
			105,106,107,108,109,
			// Matk+
			150,151,152,153,154,
			155,156,157,158,159,
			// Atk*
			204,200,205,201,
			// Matk*
			254,250,255,251,
			// HP+
			H00,H01,H02,
			// HP*
			HM0,HM1,HM2,
			// SP+
			S00,S01,
			// SP*
			SM0,SM1,SM2,
			// SPD+
			A00,A01,A02,A03,A04,
			);
			$high	= array(
			// Atk+
			110,111,112,113,114,
			115,116,117,118,119,
			// Matk+
			160,161,162,163,164,
			165,166,167,168,169,
			// Atk*
			202,206,203,
			// Matk*
			252,256,253,
			// HP+
			H03,H04,H05,
			// HP*
			HM3,HM4,HM5,
			// SP+
			S02,S03,
			// SP*
			SM3,SM4,SM5,
			// SPD+
			A05,A06,A07,A08,A09,
			);
			break;
		case "盾":
		case "水晶球":
		case "書":
		case "甲":
		case "衣服":
		case "長袍":
		case "背部":
		case "頭飾":
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
			// SPD+
			A00,A01,A02,A03,A04,
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
			// All+
			A05,A06,A07,A08,A09,
			);
			break;
		//////////////
		
		case "勳章":
			$low	= array(
			// Atk*
			202,206,203,
			// Matk*
			252,256,253,
			);
			$high	= array(
			// HP*
			HM5,
			// SP*
			SM5,
			// All+
			A05,A09,
			304,354,
			);
			break;
	}
	return array($low,$high);
}
?>