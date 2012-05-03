<?php

if (!defined('DEBUG')) {
	exit('Access Denied');
}

//////////////////////////////////////////////////
//	店に売ってるものデータ
	function ShopList() {
		return array(//店販売リスト
		1002,1003,1004,1100,1101,1200,
		1700,1701,1702,1703,1800,1801,2000,2001,
		3000,3001,3002,3100,3101,5000,5001,5002,5003,
		5100,5101,5102,5103,5200,5201,5202,5203,
		5500,5501,
		7000,7001,7500,
		//7510,7511,7512,7513,7520,// リセット系アイテム
		8000,8009,
		);
	}
//////////////////////////////////////////////////
//	オークションに出品可能なアイテムの種類
	function CanExhibitType() {
		return array(
		"Sword"	=> "1",
		"TwoHandSword"	=> "1",
		"Dagger"	=> "1",
		"Wand"	=> "1",
		"Staff"	=> "1",
		"Bow"	=> "1",
		"Whip"	=> "1",
		"Shield"	=> "1",
		"Book"	=> "1",
		"Armor"	=> "1",
		"Cloth"	=> "1",
		"Robe"	=> "1",
		"Item"	=> "1",
		"Material"	=> "1",
		);
	}
//////////////////////////////////////////////////
//	精錬可能なアイテムの種類
	function CanRefineType() {
		return array(
		"Sword","TwoHandSword","Dagger",
		"Wand","Staff","Bow",
		"Whip",
		"Shield","Book",
		"Armor","Cloth","Robe",
		);
	}
//////////////////////////////////////////////////
//	期限切れアカウントの一斉削除
	function DeleteAbandonAccount() {
		$list	= game_core::glob(USER);
		$now	= time();

		// ユーザー一覧を取得する
		foreach($list as $file) {
			if(!is_dir($file)) continue;
			$UserID	= substr($file,strrpos($file,"/")+1);
			$user	= new user($UserID,true);

			// 消されるユーザー
			if($user->IsAbandoned())
			{
				// ランキングを読む
				if(!isset($Ranking))
				{
					include_once(CLASS_RANKING);
					$Ranking	= new Ranking();
					$RankChange	= false;// ランキングデータが変更されたか
				}

				// ランキングから消す
				if( $Ranking->DeleteRank($UserID) ) {
					$RankChange	= true;// 変更された
				}

				RecordManage(gc_date("Y M d G:i:s",$now).": user ".$user->id." deleted.");
				$user->DeleteUser(false);//ランキングからは消さないようにfalse
			}
			// 消されないユーザー
				else
			{
				$user->fpCloseAll();
				unset($user);
			}
		}

		// 一通りユーザチェックが終わったのでランキングをどうするか
		if($RankChange === true)
			$Ranking->SaveRanking();
		else if($RankChange === false)
			$Ranking->fpclose();

		//print("<pre>".print_r($list,1)."</pre>");
	}
//////////////////////////////////////////////////
//	定期的に管理する何か
	function RegularControl($value=null) {
		/*
			サーバが重(混み)そうな時間帯は後回しにする。
			PM 7:00 - AM 2:00 は処理しない。
			※時刻は or なのに注意！
		*/
		if(19 <= gc_date("H") || gc_date("H") <= 1)
			 return false;

		$now	= time();

		$fp		= FileLock(CTRL_TIME_FILE,true);
		if(!$fp)
			return false;
		//$ctrltime	= file_get_contents(CTRL_TIME_FILE);
		$ctrltime	= trim(fgets($fp, 1024));
		// 周期がまだなら終了
		if($now < $ctrltime)
		{
			fclose($fp);
			unset($fp);
			return false;
		}

		// 管理の処理
		RecordManage(gc_date("Y M d G:i:s",$now).": auto regular control by {$value}.");

		DeleteAbandonAccount();//その1 放棄ユーザの掃除

		// 定期管理が終わったら次の管理時刻を書き込んで終了する。
		WriteFileFP($fp,$now + CONTROL_PERIOD);
		fclose($fp);
		unset($fp);
	}
//////////////////////////////////////////////////
//	$id が過去登録されたかどうか
	function is_registered($id) {
		if($registered = @file(REGISTER)):
			if(array_search($id."\n",$registered)!==false && !ereg("[\.\/]+",$id) )//改行記号必須
				return true;
			else
				return false;
		endif;
	}
//////////////////////////////////////////////////
//	ファイルロックしたファイルポインタを返す。
	function FileLock($file,$noExit=false) {

		if(!file_exists($file))
			return false;

		$fp	= @fopen($file,"r+") or die("Error!");
		if(!$fp)
			return false;

		$i=0;
		do{
			if(flock($fp, LOCK_EX | LOCK_NB)) {
				stream_set_write_buffer($fp, 0);
				return $fp;
			} else {
				usleep(10000);//0.01秒
				$i++;
			}
		}while($i<5);

		if($noExit) {
			return false;
		} else {
			ob_clean();
			exit("file lock error.");
		}
		//flock($fp, LOCK_EX);//排他
		//flock($fp, LOCK_SH);//共有ロック
		//flock($fp,LOCK_EX);

		return $fp;
	}
//////////////////////////////////////////////////
//	ファイルに書き込む(引数:ファイルポインタ)
	function WriteFileFP($fp,$text,$check=false) {
		if(!$check && !trim($text))//$textが空欄なら終わる
			return false;
		/*if(file_exists($file)):
			ftruncate()
		else:
			$fp	= fopen($file,"w+");*/
		ftruncate($fp,0);
		rewind($fp);
		//$fp	= fopen($file,"w+");
		//flock($fp,LOCK_EX);
		fputs($fp,$text);
		//print("<br>"."<br>".$text);
	}

//////////////////////////////////////////////////
//	ファイルに書き込む
	function WriteFile($file,$text,$check=false) {
		if(!$check && !$text)//$textが空欄なら終わる
			return false;
		/*if(file_exists($file)):
			ftruncate()
		else:
			$fp	= fopen($file,"w+");*/
		$fp	= fopen($file,"w+");
		flock($fp,LOCK_EX);
		fputs($fp,$text);
	}

//////////////////////////////////////////////////
//	ファイルを読んで配列に格納(引数:ファイルポインタ)
	function ParseFileFP($fp) {

		if(!$fp) return false;
		while( !feof($fp) ) {
			$str	= fgets($fp);
			$str	= trim($str);
			if(!$str) continue;
			$pos	= strpos($str,"=");
			if($pos === false)
				continue;
			$key	= substr($str,0,$pos);
			$val	= substr($str,++$pos);
			$data[$key]	= trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if($data)
			return $data;
		else
			return false;
	}
//////////////////////////////////////////////////
//	ファイルを読んで配列に格納
	function ParseFile($file) {

		$fp		= @fopen($file,"r+");
		if(!$fp) return false;
		flock($fp, LOCK_EX | LOCK_NB);
		while( !feof($fp) ) {
			$str	= fgets($fp);
			$str	= trim($str);
			if(!$str) continue;
			$pos	= strpos($str,"=");
			if($pos === false)
				continue;
			$key	= substr($str,0,$pos);
			$val	= substr($str,++$pos);
			$data[$key]	= trim($val);
		}
		//print("<pre>");
		//print_r($data);
		//print("</pre>");
		if($data)
			return $data;
		else
			return false;
	}
//////////////////////////////////////////////////
//
	function UserAmount() {
		static $amount;

		if($amount) {
			return $amount;
		} else {
			$amount	= count(game_core::glob(USER));
			return $amount;
		}
	}
//////////////////////////////////////////////////
//
	function JudgeList(){

		// 自動読み込み(forでループさせてるから無駄な処理)
		if(JUDGE_LIST_AUTO_LOAD) {
			for($i=1000; $i<2500; $i++) {
				if( LoadJudgeData($i) !== false)
					$list[]=$i;
			}
			return $list;
		// 手動(追加した判断は自分で書き足せ)
		} else {
		return array(
1000, 1001, 1099, 1100, 1101,
1105, 1106, 1110, 1111, 1121,
1125, 1126, 1199, 1200, 1201,
1205, 1206, 1210, 1211, 1221,
1225, 1226, 1399, 1400, 1401,
1405, 1406, 1410, 1449, 1450,
1451, 1455, 1456, 1499, 1500,
1501, 1505, 1506, 1510, 1511,
1549, 1550, 1551, 1555, 1556,
1560, 1561, 1599, 1600, 1610,
1611, 1612, 1613,
1614, 1615, 1616, 1617, 1618,
1699,
1700, 1701, 1710, 1711, 1712,
1715, 1716, 1717, 1749, 1750,
1751, 1752, 1755, 1756, 1757,
1799, 1800, 1801, 1805, 1819,
1820, 1821, 1825, 1839, 1840,
1841, 1845, 1849, 1850, 1851,
1855, 1899, 1900, 1901, 1902,
1919, 1920, 1939, 1940,
);
		}

	}

//////////////////////////////////////////////////
//	お金の表示方式
	function MoneyFormat($number) {
		return '$&nbsp;'.number_format($number);
	}
//////////////////////////////////////////////////
//
	function ItemSellPrice($item) {
		$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
		return $price;
	}

//////////////////////////////////////////////////
//	戦闘ログの表示
function ShowLogList() {
	print("<div style=\"margin:15px\">\n");
	/*// ログ少ないなら全部表示すればいい。↓
	// common
	print("<h4>最近の戦闘(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	// union
	print("<h4>ユニオン戦(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	// rank
	print("<h4>ランキング戦(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	*/

	print("<a href=\"?log\" class=\"a0\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");

	// common
	print("<h4>最近の戦闘 - <a href=\"?clog\">全表示</a>(Recent Battles)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_NORMAL);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// union
	$limit	= 0;
	print("<h4>ユニオン戦 - <a href=\"?ulog\">全表示</a>(Union Battle Log)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_UNION);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// rank
	$limit	= 0;
	print("<h4>ランキング戦 - <a href=\"?rlog\">全表示</a>(Rank Battle Log)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_RANK);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}

	print("</div>\n");
}
//////////////////////////////////////////////////
//	戦闘ログの表示
function LogShowCommon() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\" class=\"a0\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");
	// common
	print("<h4>最近の戦闘 - 全ログ(Recent Battles)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_NORMAL);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	戦闘ログの表示(union)
function LogShowUnion() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\" class=\"a0\">Union</a> ");
	print("<a href=\"?rlog\">Ranking</a>");
	// union
	print("<h4>ユニオン戦 - 全ログ(Union Battle Log)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_UNION);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	戦闘ログの表示(ranking)
function LogShowRanking() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">All</a> ");
	print("<a href=\"?clog\">Common</a> ");
	print("<a href=\"?ulog\">Union</a> ");
	print("<a href=\"?rlog\" class=\"a0\">Ranking</a>");
	// rank
	print("<h4>ランキング戦 - 全ログ(Rank Battle Log)</h4>\n");
	$log	= game_core::glob(LOG_BATTLE_RANK);
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	戦闘ログの詳細を表示(リンク)
function BattleLogDetail($log,$type=false) {
	$fp	= fopen($log,"r");

	// 数行だけ読み込む。
	$time	= fgets($fp);//開始時間 1行目
	$team	= explode("<>",fgets($fp));//チーム名 2行目
	$number	= explode("<>",trim(fgets($fp)));//人数 3行目
	$avelv	= explode("<>",trim(fgets($fp)));//平均レベル 4行目
	$win	= trim(fgets($fp));// 勝利チーム 5行目
	$act	= trim(fgets($fp));// 総行動数 6行目
	fclose($fp);

	$date	= gc_date("m/d H:i:s",substr($time,0,10));
	// 勝利チームによって色を分けて表示
	if($type == "RANK")
		print("[ <a href=\"?rlog={$time}\">{$date}</a> ]&nbsp;\n");
	else if($type == "UNION")
		print("[ <a href=\"?ulog={$time}\">{$date}</a> ]&nbsp;\n");
	else
		print("[ <a href=\"?log={$time}\">{$date}</a> ]&nbsp;\n");
	print("<span class=\"bold\">$act</span>turns&nbsp;\n");//総ターン数
	if($win === "0")
		print("<span class=\"recover\">{$team[0]}</span>");
	else if($win === "1")
		print("<span class=\"dmg\">{$team[0]}</span>");
	else
		print("{$team[0]}");

	print("({$number[0]}:{$avelv[0]})");

	print(" vs ");

	if($win === "0")
		print("<span class=\"dmg\">{$team[1]}</span>");
	else if($win === "1")
		print("<span class=\"recover\">{$team[1]}</span>");
	else
		print("{$team[1]}");

	print("({$number[1]}:{$avelv[1]})<br />");
}
//////////////////////////////////////////////////
//	戦闘ログを回覧する
function ShowBattleLog($no,$type=false) {
	if($type == "RANK")
		$file	= LOG_BATTLE_RANK.$no.".dat";
	else if($type == "UNION")
		$file	= LOG_BATTLE_UNION.$no.".dat";
	else
		$file	= LOG_BATTLE_NORMAL.$no.".dat";
	if(!file_exists($file)) {//ログが無い
		print("log doesnt exists");
		return false;
	}

	$log	= file($file);
	$row	= 6;//ログの何行目から書き出すか?
	$time	= substr($log[0],0,10);

	//print('<table style="width:100%;text-align:center" class="break"><tr><td>'."\n");
	print('<div style="padding:15px 0;width:100%;text-align:center" class="break">');
	print("<h2>battle log*</h2>");
	print("\nthis battle starts at<br />");
	print(gc_date("m/d H:i:s",substr($time,0,10)));
	print("</div>\n");
	//print("</td></tr></table>\n");

	while($log["$row"]) {
		print($log["$row"]);
		$row++;
	}
}
//////////////////////////////////////////////////
//	技の詳細を表示
	function ShowSkillDetail($skill,$radio=false) {
		if(!$skill) return false;

		if($radio)
			print('<input type="radio" name="newskill" value="'.$skill["no"].'" class="vcent" />');

		print('<img src="'.IMG_ICON.'skill/'.$skill["img"].'" class="vcent">');
		print("{$skill[name]}");

		if($radio)
			print(" / <span class=\"bold\">{$skill[learn]}</span>pt");

		if($skill[target][0] == "all")//対象
			print(" / <span class=\"charge\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "enemy")
			print(" / <span class=\"dmg\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "friend")
			print(" / <span class=\"recover\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "self")
			print(" / <span class=\"support\">{$skill[target][0]}</span>");
		else if(isset($skill[target][0]))
			print(" / {$skill[target][0]}");

		if($skill[target][1] == "all")//単体or複数or全体
			print(" - <span class=\"charge\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "individual")
			print(" - <span class=\"recover\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "multi")
			print(" - <span class=\"spdmg\">{$skill[target][1]}</span>");
		else if(isset($skill[target][1]))
			print(" - {$skill[target][1]}");

		if(isset($skill["sacrifice"]))
			print(" / <span class=\"dmg\">Sacrifice:{$skill[sacrifice]}%</span>");
		// 消費SP
		if(isset($skill["sp"]))
			print(" / <span class=\"support\">{$skill[sp]}sp</span>");
		// 消費魔方陣
		if($skill["MagicCircleDeleteTeam"])
			print(" / <span class=\"support\">MagicCircle x".$skill["MagicCircleDeleteTeam"]."</span>");
		if($skill["pow"]) {
			print(" / <span class=\"".($skill["support"]?"recover":"dmg")."\">{$skill[pow]}%</span>x");
			print(( $skill["target"][2] ? $skill["target"][2] : "1" ) );
		}
		if($skill["type"] == 1)
			print(" / <span class=\"spdmg\">Magic</span>");
		if($skill["quick"])
			print(" / <span class=\"charge\">Quick</span>");
		if($skill["invalid"])
			print(" / <span class=\"charge\">invalid</span>");
		if($skill["priority"] == "Back")
			print(" / <span class=\"support\">BackAttack</span>");
		if($skill["CurePoison"])
			print(" / <span class=\"support\">CurePoison</span>");

		if($skill["delay"])
			print(" / <span class=\"support\">Delay-".$skill[delay]."%</span>");
//		if($skill["support"])
//			print(" / <span class=\"charge\">support</span>");

		if($skill["UpMAXHP"])
			print(" / <span class=\"charge\">MaxHP+".$skill[UpMAXHP]."%</span>");
		if($skill["UpMAXSP"])
			print(" / <span class=\"charge\">MaxSP+".$skill[UpMAXSP]."%</span>");
		if($skill["UpSTR"])
			print(" / <span class=\"charge\">Str+".$skill[UpSTR]."%</span>");
		if($skill["UpINT"])
			print(" / <span class=\"charge\">Int+".$skill[UpINT]."%</span>");
		if($skill["UpDEX"])
			print(" / <span class=\"charge\">Dex+".$skill[UpDEX]."%</span>");
		if($skill["UpSPD"])
			print(" / <span class=\"charge\">Spd+".$skill[UpSPD]."%</span>");
		if($skill["UpLUK"])
			print(" / <span class=\"charge\">Luk+".$skill[UpLUK]."%</span>");
		if($skill["UpATK"])
			print(" / <span class=\"charge\">Atk+".$skill[UpATK]."%</span>");
		if($skill["UpMATK"])
			print(" / <span class=\"charge\">Matk+".$skill[UpMATK]."%</span>");
		if($skill["UpDEF"])
			print(" / <span class=\"charge\">Def+".$skill[UpDEF]."%</span>");
		if($skill["UpMDEF"])
			print(" / <span class=\"charge\">Mdef+".$skill[UpMDEF]."%</span>");

		if($skill["DownMAXHP"])
			print(" / <span class=\"dmg\">MaxHP-".$skill[DownMAXHP]."%</span>");
		if($skill["DownMAXSP"])
			print(" / <span class=\"dmg\">MaxSP-".$skill[DownMAXSP]."%</span>");
		if($skill["DownSTR"])
			print(" / <span class=\"dmg\">Str-".$skill[DownSTR]."%</span>");
		if($skill["DownINT"])
			print(" / <span class=\"dmg\">Int-".$skill[DownINT]."%</span>");
		if($skill["DownDEX"])
			print(" / <span class=\"dmg\">Dex-".$skill[DownDEX]."%</span>");
		if($skill["DownSPD"])
			print(" / <span class=\"dmg\">Spd-".$skill[DownSPD]."%</span>");
		if($skill["DownLUK"])
			print(" / <span class=\"dmg\">Luk-".$skill[DownLUK]."%</span>");
		if($skill["DownATK"])
			print(" / <span class=\"dmg\">Atk-".$skill[DownATK]."%</span>");
		if($skill["DownMATK"])
			print(" / <span class=\"dmg\">Matk-".$skill[DownMATK]."%</span>");
		if($skill["DownDEF"])
			print(" / <span class=\"dmg\">Def-".$skill[DownDEF]."%</span>");
		if($skill["DownMDEF"])
			print(" / <span class=\"dmg\">Mdef-".$skill[DownMDEF]."%</span>");

		if($skill["PlusSTR"])
			print(" / <span class=\"charge\">Str+".$skill[PlusSTR]."</span>");
		if($skill["PlusINT"])
			print(" / <span class=\"charge\">Int+".$skill[PlusINT]."</span>");
		if($skill["PlusDEX"])
			print(" / <span class=\"charge\">Dex+".$skill[PlusDEX]."</span>");
		if($skill["PlusSPD"])
			print(" / <span class=\"charge\">Spd+".$skill[PlusSPD]."</span>");
		if($skill["PlusLUK"])
			print(" / <span class=\"charge\">Luk+".$skill[PlusLUK]."</span>");

		if($skill["charge"]["0"] || $skill["charge"]["1"]) {
			print(" / (".($skill["charge"]["0"]?$skill["charge"]["0"]:"0").":");
			print(($skill["charge"]["1"]?$skill["charge"]["1"]:"0").")");
		}

		// 武器制限表示
		if($skill["limit"]) {
			$Limit	= " / Limit:";
			foreach($skill["limit"] as $type => $bool) {
				$Limit .= $type.", ";
			}
			print(substr($Limit,0,-2));
		}
		if($skill["exp"])
			print(" / {$skill[exp]}");
		print("\n");
	}
//////////////////////////////////////////////////
//	アイテムの詳細を返す...ちょっと修正したいな。
	function ShowItemDetail($item,$amount=false,$text=false,$need=false) {
		if(!$item) return false;

		$html	= "<img src=\"".IMG_ICON.'item/'.$item["img"]."\" class=\"vcent\">";
		// 精錬値
		if($item["refine"])
			$html	.= "+{$item[refine]} ";
		if($item["AddName"])
			$html	.= "{$item[AddName]} ";
		$html	.= "{$item[base_name]}";// 名前

		if($item["type"])
			$html	.= "<span class=\"light\"> ({$item[type]})</span>";
		if($amount) {//数量
			$html	.= " x<span class=\"bold\" style=\"font-size:80%\">{$amount}</span>";
		}
		if($item["atk"]["0"])//物理攻撃
			$html	.= ' / <span class="dmg">Atk:'.$item[atk][0].'</span>';
		if($item["atk"]["1"])//魔法攻撃
			$html	.= ' / <span class="spdmg">Matk:'.$item[atk][1].'</span>';
		if($item["def"]) {
			$html	.= " / <span class=\"recover\">Def:{$item[def][0]}+{$item[def][1]}</span>";
			$html	.= " / <span class=\"support\">Mdef:{$item[def][2]}+{$item[def][3]}</span>";
		}
		if($item["P_SUMMON"])
			$html	.= ' / <span class="support">Summon+'.$item["P_SUMMON"].'%</span>';
		if(isset($item["handle"]))
			$html	.= ' / <span class="charge">h:'.$item[handle].'</span>';
		if($item["option"])
			$html	.= ' / <span style="font-size:80%">'.substr($item["option"],0,-2)."</span>";

		if($need && $item["need"]) {
			$html	.= " /";
			foreach($item["need"] as $M_itemNo => $M_amount) {
				$M_item	= LoadItemData($M_itemNo);
				$html	.= "<img src=\"".IMG_ICON.$M_item["img"]."\" class=\"vcent\">";
				$html	.= "{$M_item[base_name]}";// 名前
				$html	.= " x<span class=\"bold\" style=\"font-size:80%\">{$M_amount}</span>";
				if($need["$M_itemNo"])
				$html	.= "<span class=\"light\">(".$need["$M_itemNo"].")</span>";
			}
		}

		if($text)
			return $html;

		print($html);
	}

//////////////////////////////////////////////////
//	赤い警告文でエラー表示
	function ShowResult($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="result'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	赤い警告文でエラー表示
	function ShowError($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="error'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	マニュアルを表示する
	function ShowManual() {
		include(MANUAL);
		return true;
	}
//////////////////////////////////////////////////
//	マニュアルを表示する
	function ShowManual2() {
		include(MANUAL_HIGH);
		return true;
	}
//////////////////////////////////////////////////
//	チュートリアルを表示する
	function ShowTutorial() {
		include(TUTORIAL);
		return true;
	}
//////////////////////////////////////////////////
//	更新内容の表示
	function ShowUpDate() {
		print('<div style="margin:15px">');
		print("<p><a href=\"?\">Back</a><br><a href=\"#btm\">to bottom</a></p>");

		if($_POST["updatetext"]) {
			$update	= htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
			$update	= stripslashes($update);
		} else
			$update	= @file_get_contents(UPDATE);

		print('<form action="?update" method="post">');
		if($_POST["updatepass"] == UP_PASS) {
			print('<textarea class="text" rows="12" cols="60" name="updatetext">');
			print("$update");
			print('</textarea><br>');
			print('<input type="submit" class="btn" value="update">');
			print('<a href="?update">リロード</a><br>');
		}

		print(nl2br($update)."\n");
		print('<br><a name="btm"></a>');
		if($_POST["updatepass"] == UP_PASS && $_POST["updatetext"]) {
			$fp	= fopen(UPDATE,"w");
			$text	= htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
			$text	= stripslashes($text);
			flock($fp,2);
			fputs($fp,$text);
			fclose($fp);
		}
print <<< EOD
	<input type="password" class="text" name="updatepass" style="width:100px" value="$_POST[updatepass]">
	<input type="submit" class="btn" value="update">
	</form>
EOD;
		print("<p><a href=\"?\">Back</a></p></div>");
	}
//////////////////////////////////////////////////
//	げーむでーた
	function ShowGameData() {
		?>
<div style="margin:15px">
<h4>GameData</h4>
<div style="margin:0 20px">
| <a href="?gamedata=job">職(Job)</a> |
<a href="?gamedata=item">アイテム(item)</a> |
<a href="?gamedata=judge">判定</a> |
<a href="?gamedata=monster">モンスター</a> |
</div>
</div><?php
	switch($_GET["gamedata"]) {
		case "job": include(GAME_DATA_JOB); break;
		case "item": include(GAME_DATA_ITEM); break;
		case "judge": include(GAME_DATA_JUDGE); break;
		case "monster": include(GAME_DATA_MONSTER); break;
		default: include(GAME_DATA_JOB); break;
	}

	}
//////////////////////////////////////////////////
//
	function userNameLoad() {
		$name	= @file(USER_NAME);
		if($name) {
			foreach($name as $key => $var) {
				$name[$key]	= trim($name[$key]);
				if($name[$key] === "")
					unset($name[$key]);
			}
			return $name;
		} else {
			return array();
		}
	}
//////////////////////////////////////////////////
//
	function userNameAdd($add) {
		foreach(userNameLoad() as $name) {
			$string	.= $name."\n";
		}
		$string .= $add."\n";
		$fp	= fopen(USER_NAME,"w+");
		flock($fp, LOCK_EX);
		fwrite($fp,$string);
		fclose($fp);
	}
//////////////////////////////////////////////////
//	全ランキングの表示
	function RankAllShow() {
		print('<div style="margin:15px">'."\n");
		print('<h4>Ranking - '.gc_date("Y年n月j日 G時i分s秒").'</h4>'."\n");
		include(CLASS_RANKING);
		$Rank	= new Ranking();
		$Rank->ShowRanking();
		print('</div>'."\n");
	}
//////////////////////////////////////////////////
//
	function RecordManage($string) {
		$file	= MANAGE_LOG_FILE;

		$fp	= @fopen($file,"r+") or die();
		$text	= fread($fp,2048);
		ftruncate($fp,0);
		rewind($fp);
		fwrite($fp,$string."\n".$text);
	}

	/*
	*	入力された文字列を確認する
	*	返り値
	*	成功 = array(true,変換($string));
	*	失敗 = array(false,失敗理由);
	*/
	function CheckString($string,$maxLength=16) {
		$string	= trim($string);
		$string	= stripslashes($string);
		if(is_numeric(strpos($string,"\t"))) {
			return array(false,"不正な文字");
		}
		if(is_numeric(strpos($string,"\n"))) {
			return array(false,"不正な文字");
		}
		if (!$string) {
			return array(false,"未入力");
		}
		$length	= strlen($string);
		if ( 0 == $length || $maxLength < $length) {
			return array(false,"長すぎか短すぎる");
		}
		$string	= htmlspecialchars($string,ENT_QUOTES);
		return array(true,$string);
	}
///////////////////////////////////////////////////
//	端末を判断。
	function isMobile() {
		if(strstr($_SERVER['HTTP_USER_AGENT'],"DoCoMo")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"Vodafone")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"SoftBank")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"MOT-")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"J-PHONE")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"KDDI")){
			//$env = 'ez';
			$env = 'ez';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"UP.Browser")){
			$env = 'i';
		}elseif(strstr($_SERVER['HTTP_USER_AGENT'],"WILLCOM")){
			$env = 'ez';
		}else{
			$env = 'pc';
		}
		return $env;
	}
//////////////////////////////////////////////////
//	DUMP
	if(!function_exists("dump"))  {
		function dump($array) {
			print("<pre>".print_r($array,1)."</pre>");
		}
	}

	function gc_date() {
		$_args = func_get_args();
		$_args[1] = ($_args[1] ? $_args[1] : time()) + 8 * 3600;

		return call_user_func_array('date', $_args);
	}

	if (!defined('BASE_PATH')) {
		include dirname(__FILE__).'/../trust_path/bootstrap.php';
	}

	require_once CLASS_DIR.'class.core.php';
?>