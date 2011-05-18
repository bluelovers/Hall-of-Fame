<?php
function AddEnchantData(&$item, $opt) {

/*
	製作した防具に付与される追加効果。
*/

	switch($opt) {
											// ATK
		case 100:
			$item["atk"]["0"]	+= 1;
			$item["option"]	.= "Atk+1, ";
			break;
		case 101:
			$item["atk"]["0"]	+= 2;
			$item["option"]	.= "Atk+2, ";
			break;
		case 102:
			$item["atk"]["0"]	+= 3;
			$item["option"]	.= "Atk+3, ";
			break;
		case 103:
			$item["atk"]["0"]	+= 4;
			$item["option"]	.= "Atk+4, ";
			break;
		case 104:
			$item["atk"]["0"]	+= 5;
			$item["option"]	.= "Atk+5, ";
			break;
		case 105:
			$item["atk"]["0"]	+= 6;
			$item["option"]	.= "Atk+6, ";
			break;
		case 106:
			$item["atk"]["0"]	+= 7;
			$item["option"]	.= "Atk+7, ";
			break;
		case 107:
			$item["atk"]["0"]	+= 8;
			$item["option"]	.= "Atk+8, ";
			break;
		case 108:
			$item["atk"]["0"]	+= 9;
			$item["option"]	.= "Atk+9, ";
			break;
		case 109:
			$item["atk"]["0"]	+= 10;
			$item["option"]	.= "Atk+10, ";
			break;
		case 110:
			$item["atk"]["0"]	+= 11;
			$item["option"]	.= "Atk+11, ";
			break;
		case 111:
			$item["atk"]["0"]	+= 12;
			$item["option"]	.= "Atk+12, ";
			break;
		case 112:
			$item["atk"]["0"]	+= 13;
			$item["option"]	.= "Atk+13, ";
			break;
		case 113:
			$item["atk"]["0"]	+= 14;
			$item["option"]	.= "Atk+14, ";
			break;
		case 114:
			$item["atk"]["0"]	+= 15;
			$item["option"]	.= "Atk+15, ";
			break;
		case 115:
			$item["atk"]["0"]	+= 16;
			$item["option"]	.= "Atk+16, ";
			break;
		case 116:
			$item["atk"]["0"]	+= 17;
			$item["option"]	.= "Atk+17, ";
			break;
		case 117:
			$item["atk"]["0"]	+= 18;
			$item["option"]	.= "Atk+18, ";
			break;
		case 118:
			$item["atk"]["0"]	+= 19;
			$item["option"]	.= "Atk+19, ";
			break;
		case 119:
			$item["atk"]["0"]	+= 20;
			$item["option"]	.= "Atk+20, ";
			break;
											// MATK
		case 150:
			$item["atk"]["1"]	+= 1;
			$item["option"]	.= "Matk+1, ";
			break;
		case 151:
			$item["atk"]["1"]	+= 2;
			$item["option"]	.= "Matk+2, ";
			break;
		case 152:
			$item["atk"]["1"]	+= 3;
			$item["option"]	.= "Matk+3, ";
			break;
		case 153:
			$item["atk"]["1"]	+= 4;
			$item["option"]	.= "Matk+4, ";
			break;
		case 154:
			$item["atk"]["1"]	+= 5;
			$item["option"]	.= "Matk+5, ";
			break;
		case 155:
			$item["atk"]["1"]	+= 6;
			$item["option"]	.= "Matk+6, ";
			break;
		case 156:
			$item["atk"]["1"]	+= 7;
			$item["option"]	.= "Matk+7, ";
			break;
		case 157:
			$item["atk"]["1"]	+= 8;
			$item["option"]	.= "Matk+8, ";
			break;
		case 158:
			$item["atk"]["1"]	+= 9;
			$item["option"]	.= "Matk+9, ";
			break;
		case 159:
			$item["atk"]["1"]	+= 10;
			$item["option"]	.= "Matk+10, ";
			break;
		case 160:
			$item["atk"]["1"]	+= 11;
			$item["option"]	.= "Matk+11, ";
			break;
		case 161:
			$item["atk"]["1"]	+= 12;
			$item["option"]	.= "Matk+12, ";
			break;
		case 162:
			$item["atk"]["1"]	+= 13;
			$item["option"]	.= "Matk+13, ";
			break;
		case 163:
			$item["atk"]["1"]	+= 14;
			$item["option"]	.= "Matk+14, ";
			break;
		case 164:
			$item["atk"]["1"]	+= 15;
			$item["option"]	.= "Matk+15, ";
			break;
		case 165:
			$item["atk"]["1"]	+= 16;
			$item["option"]	.= "Matk+16, ";
			break;
		case 166:
			$item["atk"]["1"]	+= 17;
			$item["option"]	.= "Matk+17, ";
			break;
		case 167:
			$item["atk"]["1"]	+= 18;
			$item["option"]	.= "Matk+18, ";
			break;
		case 168:
			$item["atk"]["1"]	+= 19;
			$item["option"]	.= "Matk+19, ";
			break;
		case 169:
			$item["atk"]["1"]	+= 20;
			$item["option"]	.= "Matk+20, ";
			break;
											// Atk *
		case 200:
			$item["atk"]["0"]	= round($item["atk"]["0"] * 1.05);
			$item["option"]	.= "Atk+5%, ";
			break;
		case 201:
			$item["atk"]["0"]	= round($item["atk"]["0"] * 1.10);
			$item["option"]	.= "Atk+10%, ";
			break;
		case 202:
			$item["atk"]["0"]	= round($item["atk"]["0"] * 1.15);
			$item["option"]	.= "Atk+15%, ";
			break;
		case 203:
			$item["atk"]["0"]	= round($item["atk"]["0"] * 1.20);
			$item["option"]	.= "Atk+20%, ";
			break;
											// Matk *
		case 250:
			$item["atk"]["1"]	= round($item["atk"]["1"] * 1.05);
			$item["option"]	.= "Matk+5%, ";
			break;
		case 251:
			$item["atk"]["1"]	= round($item["atk"]["1"] * 1.10);
			$item["option"]	.= "Matk+10%, ";
			break;
		case 252:
			$item["atk"]["1"]	= round($item["atk"]["1"] * 1.15);
			$item["option"]	.= "Matk+15%, ";
			break;
		case 253:
			$item["atk"]["1"]	= round($item["atk"]["1"] * 1.20);
			$item["option"]	.= "Matk+20%, ";
			break;

											// Def +
		case 300:
			$item["def"]["0"]	+= 1;
			$item["option"]	.= "Def+1, ";
			break;
		case 301:
			$item["def"]["0"]	+= 2;
			$item["option"]	.= "Def+2, ";
			break;
		case 302:
			$item["def"]["0"]	+= 3;
			$item["option"]	.= "Def+3, ";
			break;
		case 303:
			$item["def"]["0"]	+= 4;
			$item["option"]	.= "Def+4, ";
			break;
		case 304:
			$item["def"]["0"]	+= 5;
			$item["option"]	.= "Def+5, ";
			break;
											// Mdef +
		case 350:
			$item["def"]["2"]	+= 1;
			$item["option"]	.= "Mdef+1, ";
			break;
		case 351:
			$item["def"]["2"]	+= 2;
			$item["option"]	.= "Mdef+2, ";
			break;
		case 352:
			$item["def"]["2"]	+= 3;
			$item["option"]	.= "Mdef+3, ";
			break;
		case 353:
			$item["def"]["2"]	+= 4;
			$item["option"]	.= "Mdef+4, ";
			break;
		case 354:
			$item["def"]["2"]	+= 5;
			$item["option"]	.= "Mdef+5, ";
			break;
											// Def *
											// Mdef *
		case 400:
			break;

											// HP +
		case H00:
			$item["P_MAXHP"]	+= 10;
			$item["option"]	.= "MAXHP+10, ";
			break;
		case H01:
			$item["P_MAXHP"]	+= 20;
			$item["option"]	.= "MAXHP+20, ";
			break;
		case H02:
			$item["P_MAXHP"]	+= 30;
			$item["option"]	.= "MAXHP+30, ";
			break;
		case H03:
			$item["P_MAXHP"]	+= 40;
			$item["option"]	.= "MAXHP+40, ";
			break;
		case H04:
			$item["P_MAXHP"]	+= 50;
			$item["option"]	.= "MAXHP+50, ";
			break;
		case H05:
			$item["P_MAXHP"]	+= 60;
			$item["option"]	.= "MAXHP+60, ";
			break;
											// HP *
		case HM0:
			$item["M_MAXHP"]	+= 1;
			$item["option"]	.= "MAXHP+1%, ";
			break;
		case HM1:
			$item["M_MAXHP"]	+= 2;
			$item["option"]	.= "MAXHP+2%, ";
			break;
		case HM2:
			$item["M_MAXHP"]	+= 3;
			$item["option"]	.= "MAXHP+3%, ";
			break;
		case HM3:
			$item["M_MAXHP"]	+= 4;
			$item["option"]	.= "MAXHP+4%, ";
			break;
		case HM4:
			$item["M_MAXHP"]	+= 5;
			$item["option"]	.= "MAXHP+5%, ";
			break;
		case HM5:
			$item["M_MAXHP"]	+= 6;
			$item["option"]	.= "MAXHP+6%, ";
			break;
											// SP +
		case S00:
			$item["P_MAXSP"]	+= 10;
			$item["option"]	.= "MAXSP+10, ";
			break;
		case S01:
			$item["P_MAXSP"]	+= 20;
			$item["option"]	.= "MAXSP+20, ";
			break;
		case S02:
			$item["P_MAXSP"]	+= 30;
			$item["option"]	.= "MAXSP+30, ";
			break;
		case S03:
			$item["P_MAXSP"]	+= 40;
			$item["option"]	.= "MAXSP+40, ";
			break;
											// SP *
		case SM0:
			$item["M_MAXSP"]	+= 1;
			$item["option"]	.= "MAXSP+1%, ";
			break;
		case SM1:
			$item["M_MAXSP"]	+= 2;
			$item["option"]	.= "MAXSP+2%, ";
			break;
		case SM2:
			$item["M_MAXSP"]	+= 3;
			$item["option"]	.= "MAXSP+3%, ";
			break;
		case SM3:
			$item["M_MAXSP"]	+= 4;
			$item["option"]	.= "MAXSP+4%, ";
			break;
		case SM4:
			$item["M_MAXSP"]	+= 5;
			$item["option"]	.= "MAXSP+5%, ";
			break;
		case SM5:
			$item["M_MAXSP"]	+= 6;
			$item["option"]	.= "MAXSP+6%, ";
			break;
											// STR +
		case P00:
			$item["P_STR"]	+= 1;
			$item["option"]	.= "STR+1, ";
			break;
		case P01:
			$item["P_STR"]	+= 2;
			$item["option"]	.= "STR+2, ";
			break;
		case P02:
			$item["P_STR"]	+= 3;
			$item["option"]	.= "STR+3, ";
			break;
		case P03:
			$item["P_STR"]	+= 4;
			$item["option"]	.= "STR+4, ";
			break;
		case P04:
			$item["P_STR"]	+= 5;
			$item["option"]	.= "STR+5, ";
			break;
		case P05:
			$item["P_STR"]	+= 6;
			$item["option"]	.= "STR+6, ";
			break;
		case P06:
			$item["P_STR"]	+= 7;
			$item["option"]	.= "STR+7, ";
			break;
		case P07:
			$item["P_STR"]	+= 8;
			$item["option"]	.= "STR+8, ";
			break;
		case P08:
			$item["P_STR"]	+= 9;
			$item["option"]	.= "STR+9, ";
			break;
		case P09:
			$item["P_STR"]	+= 10;
			$item["option"]	.= "STR+10, ";
			break;
											// INT +
		case I00:
			$item["P_INT"]	+= 1;
			$item["option"]	.= "INT+1, ";
			break;
		case I01:
			$item["P_INT"]	+= 2;
			$item["option"]	.= "INT+2, ";
			break;
		case I02:
			$item["P_INT"]	+= 3;
			$item["option"]	.= "INT+3, ";
			break;
		case I03:
			$item["P_INT"]	+= 4;
			$item["option"]	.= "INT+4, ";
			break;
		case I04:
			$item["P_INT"]	+= 5;
			$item["option"]	.= "INT+5, ";
			break;
		case I05:
			$item["P_INT"]	+= 6;
			$item["option"]	.= "INT+6, ";
			break;
		case I06:
			$item["P_INT"]	+= 7;
			$item["option"]	.= "INT+7, ";
			break;
		case I07:
			$item["P_INT"]	+= 8;
			$item["option"]	.= "INT+8, ";
			break;
		case I08:
			$item["P_INT"]	+= 9;
			$item["option"]	.= "INT+9, ";
			break;
		case I09:
			$item["P_INT"]	+= 10;
			$item["option"]	.= "INT+10, ";
			break;
											// DEX +
		case D00:
			$item["P_DEX"]	+= 1;
			$item["option"]	.= "DEX+1, ";
			break;
		case D01:
			$item["P_DEX"]	+= 2;
			$item["option"]	.= "DEX+2, ";
			break;
		case D02:
			$item["P_DEX"]	+= 3;
			$item["option"]	.= "DEX+3, ";
			break;
		case D03:
			$item["P_DEX"]	+= 4;
			$item["option"]	.= "DEX+4, ";
			break;
		case D04:
			$item["P_DEX"]	+= 5;
			$item["option"]	.= "DEX+5, ";
			break;
		case D05:
			$item["P_DEX"]	+= 6;
			$item["option"]	.= "DEX+6, ";
			break;
		case D06:
			$item["P_DEX"]	+= 7;
			$item["option"]	.= "DEX+7, ";
			break;
		case D07:
			$item["P_DEX"]	+= 8;
			$item["option"]	.= "DEX+8, ";
			break;
		case D08:
			$item["P_DEX"]	+= 9;
			$item["option"]	.= "DEX+9, ";
			break;
		case D09:
			$item["P_DEX"]	+= 10;
			$item["option"]	.= "DEX+10, ";
			break;
											// SPD +
		case A00:
			$item["P_SPD"]	+= 1;
			$item["option"]	.= "SPD+1, ";
			break;
		case A01:
			$item["P_SPD"]	+= 2;
			$item["option"]	.= "SPD+2, ";
			break;
		case A02:
			$item["P_SPD"]	+= 3;
			$item["option"]	.= "SPD+3, ";
			break;
		case A03:
			$item["P_SPD"]	+= 4;
			$item["option"]	.= "SPD+4, ";
			break;
		case A04:
			$item["P_SPD"]	+= 5;
			$item["option"]	.= "SPD+5, ";
			break;
		case A05:
			$item["P_SPD"]	+= 6;
			$item["option"]	.= "SPD+6, ";
			break;
		case A06:
			$item["P_SPD"]	+= 7;
			$item["option"]	.= "SPD+7, ";
			break;
		case A07:
			$item["P_SPD"]	+= 8;
			$item["option"]	.= "SPD+8, ";
			break;
		case A08:
			$item["P_SPD"]	+= 9;
			$item["option"]	.= "SPD+9, ";
			break;
		case A09:
			$item["P_SPD"]	+= 10;
			$item["option"]	.= "SPD+10, ";
			break;
											// LUK +
		case L00:
			$item["P_LUK"]	+= 1;
			$item["option"]	.= "LUK+1, ";
			break;
		case L01:
			$item["P_LUK"]	+= 2;
			$item["option"]	.= "LUK+2, ";
			break;
		case L02:
			$item["P_LUK"]	+= 3;
			$item["option"]	.= "LUK+3, ";
			break;
		case L03:
			$item["P_LUK"]	+= 4;
			$item["option"]	.= "LUK+4, ";
			break;
		case L04:
			$item["P_LUK"]	+= 5;
			$item["option"]	.= "LUK+5, ";
			break;
		case L05:
			$item["P_LUK"]	+= 6;
			$item["option"]	.= "LUK+6, ";
			break;
		case L06:
			$item["P_LUK"]	+= 7;
			$item["option"]	.= "LUK+7, ";
			break;
		case L07:
			$item["P_LUK"]	+= 8;
			$item["option"]	.= "LUK+8, ";
			break;
		case L08:
			$item["P_LUK"]	+= 9;
			$item["option"]	.= "LUK+9, ";
			break;
		case L09:
			$item["P_LUK"]	+= 10;
			$item["option"]	.= "LUK+10, ";
			break;



											// 特殊製作
											// 日付や時間帯によっても効果かえれるよ date();
		case X00:
			if($item["type2"] == "WEAPON") {
				$item["atk"]["0"]	+= 5;
				$item["option"]	.= "Atk+5, ";
				$item["AddName"]	= "Power";
			} else {
				$item["def"]["0"]	+= 2;
				$item["option"]	.= "Def+2, ";
				$item["AddName"]	= "Hard";
			}
			break;
		case X01:
			if($item["type2"] == "WEAPON") {
				$item["atk"]["1"]	+= 5;
				$item["option"]	.= "Matk+5, ";
				$item["AddName"]	= "Wise";
			} else {
				$item["def"]["2"]	+= 2;
				$item["option"]	.= "Mdef+2, ";
				$item["AddName"]	= "Wise";
			}
			break;
		case M01:
			$item["P_MAXHP"]	+= 10;
			$item["option"]	.= "MAXHP+10, ";
			$item["AddName"]	= "Goblin";
			break;
	}
}
?>