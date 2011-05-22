<?php 
// キャラがそのクラスに転職できるか(クラスの転職条件)
function CanClassChange($char,$class) {
	switch($class) {
		case "101":// 皇家衛士
			if(19 < $char->level && $char->job == 100)
				return true;
			return false;
		case "102":// 狂戰士
			if(24 < $char->level && $char->job == 100)
				return true;
			return false;
		case "103":// 魔女狩
			if(22 < $char->level && $char->job == 100)
				return true;
			return false;
		case "201":// 術士
			if(19 < $char->level && $char->job == 200)
				return true;
			return false;
		case "202":// 召喚師
			if(24 < $char->level && $char->job == 200)
				return true;
			return false;
		case "203":// 死靈法師
			if(21 < $char->level && $char->job == 200)
				return true;
			return false;
		case "301":// 主教
			if(24 < $char->level && $char->job == 300)
				return true;
			return false;
		case "302":// 德魯伊
			if(19 < $char->level && $char->job == 300)
				return true;
			return false;
		case "401":// 狙擊手
			if(19 < $char->level && $char->job == 400)
				return true;
			return false;
		case "402":// 馴獸師
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