<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

//////////////////////////////////////////////////
//	期限切れアカウントの一斉削除
function DeleteAbandonAccount()
{
	$list = game_core::glob(USER);
	$now = time();

	// ユーザー一覧を取得する
	foreach ($list as $file)
	{
		if (!is_dir($file)) continue;
		$UserID = substr($file, strrpos($file, "/") + 1);
		$user = new HOF_Class_User($UserID, true);

		// 消されるユーザー
		if ($user->IsAbandoned())
		{
			// ランキングを読む
			if (!isset($Ranking))
			{
				include_once (CLASS_RANKING);
				$Ranking = new Ranking();
				$RankChange = false; // ランキングデータが変更されたか
			}

			// ランキングから消す
			if ($Ranking->DeleteRank($UserID))
			{
				$RankChange = true; // 変更された
			}

			RecordManage(gc_date("Y M d G:i:s", $now) . ": user " . $user->id . " deleted.");
			$user->DeleteUser(false); //ランキングからは消さないようにfalse
		}
		// 消されないユーザー
		else
		{
			$user->fpCloseAll();
			unset($user);
		}
	}

	// 一通りユーザチェックが終わったのでランキングをどうするか
	if ($RankChange === true) $Ranking->SaveRanking();
	else
		if ($RankChange === false) $Ranking->fpclose();

	//print("<pre>".print_r($list,1)."</pre>");
}
//////////////////////////////////////////////////
//	定期的に管理する何か
function RegularControl($value = null)
{
	/*
	サーバが重(混み)そうな時間帯は後回しにする。
	PM 7:00 - AM 2:00 は処理しない。
	※時刻は or なのに注意！
	*/
	if (19 <= gc_date("H") || gc_date("H") <= 1) return false;

	$now = time();

	$fp = HOF_Class_File::FileLock(CTRL_TIME_FILE, true);
	if (!$fp) return false;
	//$ctrltime	= file_get_contents(CTRL_TIME_FILE);
	$ctrltime = trim(fgets($fp, 1024));
	// 周期がまだなら終了
	if ($now < $ctrltime)
	{
		fclose($fp);
		unset($fp);
		return false;
	}

	// 管理の処理
	RecordManage(gc_date("Y M d G:i:s", $now) . ": auto regular control by {$value}.");

	DeleteAbandonAccount(); //その1 放棄ユーザの掃除

	// 定期管理が終わったら次の管理時刻を書き込んで終了する。
	HOF_Class_File::WriteFileFP($fp, $now + CONTROL_PERIOD);
	fclose($fp);
	unset($fp);
}

//////////////////////////////////////////////////
//
function ItemSellPrice($item)
{
	$price = (isset($item["sell"]) ? $item["sell"] : round($item["buy"] * SELLING_PRICE));
	return $price;
}

//////////////////////////////////////////////////
//	技の詳細を表示
function ShowSkillDetail($skill, $radio = false)
{
	if (!$skill) return false;

	if ($radio) print ('<input type="radio" name="newskill" value="' . $skill["no"] . '" class="vcent" />');

	print ('<img src="' . HOF_Class_Icon::getImageUrl($skill["img"], IMG_ICON . 'skill/') . '" class="vcent">');
	print ("{$skill[name]}");

	if ($radio) print (" / <span class=\"bold\">{$skill[learn]}</span>pt");

	if ($skill[target][0] == "all") //対象
 			print (" / <span class=\"charge\">{$skill[target][0]}</span>");
	else
		if ($skill[target][0] == "enemy") print (" / <span class=\"dmg\">{$skill[target][0]}</span>");
		else
			if ($skill[target][0] == "friend") print (" / <span class=\"recover\">{$skill[target][0]}</span>");
			else
				if ($skill[target][0] == "self") print (" / <span class=\"support\">{$skill[target][0]}</span>");
				else
					if (isset($skill[target][0])) print (" / {$skill[target][0]}");

	if ($skill[target][1] == "all") //単体or複数or全体
 			print (" - <span class=\"charge\">{$skill[target][1]}</span>");
	else
		if ($skill[target][1] == "individual") print (" - <span class=\"recover\">{$skill[target][1]}</span>");
		else
			if ($skill[target][1] == "multi") print (" - <span class=\"spdmg\">{$skill[target][1]}</span>");
			else
				if (isset($skill[target][1])) print (" - {$skill[target][1]}");

	if (isset($skill["sacrifice"])) print (" / <span class=\"dmg\">Sacrifice:{$skill[sacrifice]}%</span>");
	// 消費SP
	if (isset($skill["sp"])) print (" / <span class=\"support\">{$skill[sp]}sp</span>");
	// 消費魔方陣
	if ($skill["MagicCircleDeleteTeam"]) print (" / <span class=\"support\">MagicCircle x" . $skill["MagicCircleDeleteTeam"] . "</span>");
	if ($skill["pow"])
	{
		print (" / <span class=\"" . ($skill["support"] ? "recover" : "dmg") . "\">{$skill[pow]}%</span>x");
		print (($skill["target"][2] ? $skill["target"][2] : "1"));
	}
	if ($skill["type"] == 1) print (" / <span class=\"spdmg\">Magic</span>");
	if ($skill["quick"]) print (" / <span class=\"charge\">Quick</span>");
	if ($skill["invalid"]) print (" / <span class=\"charge\">invalid</span>");
	if ($skill["priority"] == "Back") print (" / <span class=\"support\">BackAttack</span>");
	if ($skill["CurePoison"]) print (" / <span class=\"support\">CurePoison</span>");

	if ($skill["delay"]) print (" / <span class=\"support\">Delay-" . $skill[delay] . "%</span>");
	//		if($skill["support"])
	//			print(" / <span class=\"charge\">support</span>");

	if ($skill["UpMAXHP"]) print (" / <span class=\"charge\">MaxHP+" . $skill[UpMAXHP] . "%</span>");
	if ($skill["UpMAXSP"]) print (" / <span class=\"charge\">MaxSP+" . $skill[UpMAXSP] . "%</span>");
	if ($skill["UpSTR"]) print (" / <span class=\"charge\">Str+" . $skill[UpSTR] . "%</span>");
	if ($skill["UpINT"]) print (" / <span class=\"charge\">Int+" . $skill[UpINT] . "%</span>");
	if ($skill["UpDEX"]) print (" / <span class=\"charge\">Dex+" . $skill[UpDEX] . "%</span>");
	if ($skill["UpSPD"]) print (" / <span class=\"charge\">Spd+" . $skill[UpSPD] . "%</span>");
	if ($skill["UpLUK"]) print (" / <span class=\"charge\">Luk+" . $skill[UpLUK] . "%</span>");
	if ($skill["UpATK"]) print (" / <span class=\"charge\">Atk+" . $skill[UpATK] . "%</span>");
	if ($skill["UpMATK"]) print (" / <span class=\"charge\">Matk+" . $skill[UpMATK] . "%</span>");
	if ($skill["UpDEF"]) print (" / <span class=\"charge\">Def+" . $skill[UpDEF] . "%</span>");
	if ($skill["UpMDEF"]) print (" / <span class=\"charge\">Mdef+" . $skill[UpMDEF] . "%</span>");

	if ($skill["DownMAXHP"]) print (" / <span class=\"dmg\">MaxHP-" . $skill[DownMAXHP] . "%</span>");
	if ($skill["DownMAXSP"]) print (" / <span class=\"dmg\">MaxSP-" . $skill[DownMAXSP] . "%</span>");
	if ($skill["DownSTR"]) print (" / <span class=\"dmg\">Str-" . $skill[DownSTR] . "%</span>");
	if ($skill["DownINT"]) print (" / <span class=\"dmg\">Int-" . $skill[DownINT] . "%</span>");
	if ($skill["DownDEX"]) print (" / <span class=\"dmg\">Dex-" . $skill[DownDEX] . "%</span>");
	if ($skill["DownSPD"]) print (" / <span class=\"dmg\">Spd-" . $skill[DownSPD] . "%</span>");
	if ($skill["DownLUK"]) print (" / <span class=\"dmg\">Luk-" . $skill[DownLUK] . "%</span>");
	if ($skill["DownATK"]) print (" / <span class=\"dmg\">Atk-" . $skill[DownATK] . "%</span>");
	if ($skill["DownMATK"]) print (" / <span class=\"dmg\">Matk-" . $skill[DownMATK] . "%</span>");
	if ($skill["DownDEF"]) print (" / <span class=\"dmg\">Def-" . $skill[DownDEF] . "%</span>");
	if ($skill["DownMDEF"]) print (" / <span class=\"dmg\">Mdef-" . $skill[DownMDEF] . "%</span>");

	if ($skill["PlusSTR"]) print (" / <span class=\"charge\">Str+" . $skill[PlusSTR] . "</span>");
	if ($skill["PlusINT"]) print (" / <span class=\"charge\">Int+" . $skill[PlusINT] . "</span>");
	if ($skill["PlusDEX"]) print (" / <span class=\"charge\">Dex+" . $skill[PlusDEX] . "</span>");
	if ($skill["PlusSPD"]) print (" / <span class=\"charge\">Spd+" . $skill[PlusSPD] . "</span>");
	if ($skill["PlusLUK"]) print (" / <span class=\"charge\">Luk+" . $skill[PlusLUK] . "</span>");

	if ($skill["charge"]["0"] || $skill["charge"]["1"])
	{
		print (" / (" . ($skill["charge"]["0"] ? $skill["charge"]["0"] : "0") . ":");
		print (($skill["charge"]["1"] ? $skill["charge"]["1"] : "0") . ")");
	}

	// 武器制限表示
	if ($skill["limit"])
	{
		$Limit = " / Limit:";
		foreach ($skill["limit"] as $type => $bool)
		{
			$Limit .= $type . ", ";
		}
		print (substr($Limit, 0, -2));
	}
	if ($skill["exp"]) print (" / {$skill[exp]}");
	print ("\n");
}
//////////////////////////////////////////////////
//	アイテムの詳細を返す...ちょっと修正したいな。
function ShowItemDetail($item, $amount = false, $text = false, $need = false)
{
	if (!$item) return false;

	$html = "<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], IMG_ICON . 'item/') . "\" class=\"vcent\">";
	// 精錬値
	if ($item["refine"]) $html .= "+{$item[refine]} ";
	if ($item["AddName"]) $html .= "{$item[AddName]} ";
	$html .= "{$item[base_name]}"; // 名前

	if ($item["type"]) $html .= "<span class=\"light\"> ({$item[type]})</span>";
	if ($amount)
	{ //数量
		$html .= " x<span class=\"bold\" style=\"font-size:80%\">{$amount}</span>";
	}
	if ($item["atk"]["0"]) //物理攻撃
 			$html .= ' / <span class="dmg">Atk:' . $item[atk][0] . '</span>';
	if ($item["atk"]["1"]) //魔法攻撃
 			$html .= ' / <span class="spdmg">Matk:' . $item[atk][1] . '</span>';
	if ($item["def"])
	{
		$html .= " / <span class=\"recover\">Def:{$item[def][0]}+{$item[def][1]}</span>";
		$html .= " / <span class=\"support\">Mdef:{$item[def][2]}+{$item[def][3]}</span>";
	}
	if ($item["P_SUMMON"]) $html .= ' / <span class="support">Summon+' . $item["P_SUMMON"] . '%</span>';
	if (isset($item["handle"])) $html .= ' / <span class="charge">h:' . $item[handle] . '</span>';
	if ($item["option"]) $html .= ' / <span style="font-size:80%">' . substr($item["option"], 0, -2) . "</span>";

	if ($need && $item["need"])
	{
		$html .= " /";
		foreach ($item["need"] as $M_itemNo => $M_amount)
		{
			$M_item = HOF_Model_Data::getItemData($M_itemNo);
			$html .= "<img src=\"" . HOF_Class_Icon::getImageUrl($M_item["img"], IMG_ICON) . "\" class=\"vcent\">";
			$html .= "{$M_item[base_name]}"; // 名前
			$html .= " x<span class=\"bold\" style=\"font-size:80%\">{$M_amount}</span>";
			if ($need["$M_itemNo"]) $html .= "<span class=\"light\">(" . $need["$M_itemNo"] . ")</span>";
		}
	}

	if ($text) return $html;

	print ($html);
}

function userNameLoad()
{
	$name = @file(USER_NAME);
	if ($name)
	{
		foreach ($name as $key => $var)
		{
			$name[$key] = trim($name[$key]);
			if ($name[$key] === "") unset($name[$key]);
		}
		return $name;
	}
	else
	{
		return array();
	}
}
//////////////////////////////////////////////////
//
function userNameAdd($add)
{
	foreach (userNameLoad() as $name)
	{
		$string .= $name . "\n";
	}
	$string .= $add . "\n";
	$fp = fopen(USER_NAME, "w+");
	flock($fp, LOCK_EX);
	fwrite($fp, $string);
	fclose($fp);
}
//////////////////////////////////////////////////
//	全ランキングの表示
function RankAllShow()
{
	print ('<div style="margin:15px">' . "\n");
	print ('<h4>Ranking - ' . gc_date("Y年n月j日 G時i分s秒") . '</h4>' . "\n");
	include (CLASS_RANKING);
	$Rank = new Ranking();
	$Rank->ShowRanking();
	print ('</div>' . "\n");
}
//////////////////////////////////////////////////
//
function RecordManage($string)
{
	$file = MANAGE_LOG_FILE;

	$fp = @fopen($file, "r+") or die();
	$text = fread($fp, 2048);
	ftruncate($fp, 0);
	rewind($fp);
	fwrite($fp, $string . "\n" . $text);
}

/*
*	入力された文字列を確認する
*	返り値
*	成功 = array(true,変換($string));
*	失敗 = array(false,失敗理由);
*/
function CheckString($string, $maxLength = 16)
{
	$string = trim($string);
	$string = stripslashes($string);
	if (is_numeric(strpos($string, "\t")))
	{
		return array(false, "不正な文字");
	}
	if (is_numeric(strpos($string, "\n")))
	{
		return array(false, "不正な文字");
	}
	if (!$string)
	{
		return array(false, "未入力");
	}
	$length = strlen($string);
	if (0 == $length || $maxLength < $length)
	{
		return array(false, "長すぎか短すぎる");
	}
	$string = htmlspecialchars($string, ENT_QUOTES);
	return array(true, $string);
}
///////////////////////////////////////////////////
//	端末を判断。
function isMobile()
{
	if (strstr($_SERVER['HTTP_USER_AGENT'], "DoCoMo"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "Vodafone"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "SoftBank"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "MOT-"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "J-PHONE"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "KDDI"))
	{
		//$env = 'ez';
		$env = 'ez';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "UP.Browser"))
	{
		$env = 'i';
	}
	elseif (strstr($_SERVER['HTTP_USER_AGENT'], "WILLCOM"))
	{
		$env = 'ez';
	}
	else
	{
		$env = 'pc';
	}
	return $env;
}
//////////////////////////////////////////////////
//	DUMP
if (!function_exists("dump"))
{
	function dump($array)
	{
		print ("<pre>" . print_r($array, 1) . "</pre>");
	}
}

function gc_date()
{
	$_args = func_get_args();
	$_args[1] = ($_args[1] ? $_args[1] : time()) + 8 * 3600;

	return call_user_func_array('date', $_args);
}

if (!defined('BASE_PATH'))
{
	include dirname(__FILE__) . '/../trust_path/bootstrap.php';
}

require_once CLASS_DIR . 'class.core.php';


?>