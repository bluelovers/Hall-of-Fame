<?
// キャラがそのクラスに転職できるか(クラスの転職条件)
function CanClassChange($char,$class) {
	switch($class) {

		case "101":// RoyalGuard
			if(19 < $char->level && $char->job == 100)
				return true;
			return false;

		case "102":// Sacrier
			if(24 < $char->level && $char->job == 100)
				return true;
			return false;

		case "103":// WitchHunt
			if(22 < $char->level && $char->job == 100)
				return true;
			return false;

		case "201":// Warlock
			if(19 < $char->level && $char->job == 200)
				return true;
			return false;

		case "202":// Summoner
			if(24 < $char->level && $char->job == 200)
				return true;
			return false;

		case "203":// Necromancer
			if(21 < $char->level && $char->job == 200)
				return true;
			return false;

		case "301":// Bishop
			if(24 < $char->level && $char->job == 300)
				return true;
			return false;

		case "302":// Druid
			if(19 < $char->level && $char->job == 300)
				return true;
			return false;

		case "401":// Sniper
			if(19 < $char->level && $char->job == 400)
				return true;
			return false;

		case "402":// BeastTamer
			if(24 < $char->level && $char->job == 400)
				return true;
			return false;

		case "403":// Murderer
			if(21 < $char->level && $char->job == 400)
				return true;
			return false;

		default:
			return false;
	}
}
?>