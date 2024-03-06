<?php 
function CanClassChange($char,$class) {
	switch($class) {
		case "101":// 皇家卫士
			if(19 < $char->level && $char->job == 100)
				return true;
			return false;
		case "102":// 狂战士
			if(24 < $char->level && $char->job == 100)
				return true;
			return false;
		case "103":// 魔女狩
			if(22 < $char->level && $char->job == 100)
				return true;
			return false;
		case "201":// 术士
			if(19 < $char->level && $char->job == 200)
				return true;
			return false;
		case "202":// 召唤师
			if(24 < $char->level && $char->job == 200)
				return true;
			return false;
		case "203":// 死灵法师
			if(21 < $char->level && $char->job == 200)
				return true;
			return false;
		case "301":// 主教
			if(24 < $char->level && $char->job == 300)
				return true;
			return false;
		case "302":// 德鲁伊
			if(19 < $char->level && $char->job == 300)
				return true;
			return false;
		case "401":// 狙击手
			if(19 < $char->level && $char->job == 400)
				return true;
			return false;
		case "402":// 驯兽师
			if(24 < $char->level && $char->job == 400)
				return true;
			return false;
		case "403":// 刺客
			if(21 < $char->level && $char->job == 400)
				return true;
			return false;
		default:
			return false;
	}
}
?>