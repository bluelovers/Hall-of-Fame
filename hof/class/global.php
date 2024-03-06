<?php 
//////////////////////////////////////////////////
//	商店列表
	function ShopList() {
		return array(
		1002,1003,1004,1100,1101,1200,
		1700,1701,1702,1703,1800,1801,2000,2001,
		3000,3001,3002,3100,3101,5000,5001,5002,5003,
		5100,5101,5102,5103,5200,5201,5202,5203,
		5500,5501,
		7000,7001,7500,
		//7510,7511,7512,7513,7520,// 重置道具
		8000,8009,8012,
		);
	}
//////////////////////////////////////////////////
//	可以拍卖的道具类型
	function CanExhibitType() {
		return array(
		"剑"	=> "1",
		"双手剑"	=> "1",
		"匕首"	=> "1",
		"魔杖"	=> "1",
		"杖"	=> "1",
		"弓"	=> "1",
		"鞭"	=> "1",
		"盾"	=> "1",
		"书"	=> "1",
		"甲"	=> "1",
		"衣服"	=> "1",
		"长袍"	=> "1",
		"道具"	=> "1",
		"材料"	=> "1",
		);
	}
//////////////////////////////////////////////////
//	可以精炼的道具类型
	function CanRefineType() {
		return array(
		"剑","双手剑","匕首",
		"魔杖","杖","弓",
		"鞭",
		"盾","书",
		"甲","衣服","长袍",
		);
	}
//////////////////////////////////////////////////
//	删除过期用户
	function DeleteAbandonAccount() {
		$list	= glob(USER."*");
		$now	= time();
		// 用户列表
		foreach($list as $file) {
			if(!is_dir($file)) continue;
			$UserID	= substr($file,strrpos($file,"/")+1);
			$user	= new user($UserID,true);
			// 用户将被删除
			if($user->IsAbandoned())
			{
				// 排行榜相关
				if(!isset($Ranking))
				{
					include_once(CLASS_RANKING);
					$Ranking	= new Ranking();
					$RankChange	= false;// 排行榜不可被修改
				}
				// 消除排名
				if( $Ranking->DeleteRank($UserID) ) {
					$RankChange	= true;// 排行榜可以修改了
				}
				RecordManage(date("Y M d G:i:s",$now).": user ".$user->id." deleted.");
				$user->DeleteUser(false);//设置false则不可从排行榜删除
			}
			// 不可删除
				else
			{
				$user->fpCloseAll();
				unset($user);
			}
		}
		// 用户验证后对排行榜的处理
		if($RankChange === true)
			$Ranking->SaveRanking();
		else if($RankChange === false)
			$Ranking->fpclose();
		//print("<pre>".print_r($list,1)."</pre>");
	}
//////////////////////////////////////////////////
//	定期自动管理相关
	function RegularControl($value=null) {
		/*
			服务器负载过大则时间段推迟。
			PM 7:00 - AM 2:00不处理。
			※时间设置请慎重！
		*/
		if(19 <= date("H") || date("H") <= 1)
			 return false;
		$now	= time();
		$fp		= FileLock(CTRL_TIME_FILE,true);
		if(!$fp)
			return false;
		//$ctrltime	= file_get_contents(CTRL_TIME_FILE);
		$ctrltime	= trim(fgets($fp, 1024));
		// 如果未到周期，则结束
		if($now < $ctrltime)
		{
			fclose($fp);
			unset($fp);
			return false;
		}
		// 管理の処理
		RecordManage(date("Y M d G:i:s",$now).": auto regular control by {$value}.");
		DeleteAbandonAccount();//设置为1 清除过期用户
		// 定期管理结束后，写入下一个管理时间并结束。
		WriteFileFP($fp,$now + CONTROL_PERIOD);
		fclose($fp);
		unset($fp);
	}
//////////////////////////////////////////////////
//	$id 是否登录过
	function is_registered($id) {
		if($registered = @file(REGISTER)):
			if(array_search($id."\n",$registered)!==false && !mb_ereg("[\.\/]+",$id) )//改行記号必須
				return true;
			else
				return false;
		endif;
	}
//////////////////////////////////////////////////
//	锁文件并返回文件指针
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
				usleep(10000);//0.01秒为单位
				$i++;
			}
		}while($i<5);
		//if($noExit) {
		//	return false;
		//} else {
		//	ob_clean();
		//	exit("file lock error.");
		//}
		//flock($fp, LOCK_EX);//排他
		//flock($fp, LOCK_SH);//共有ロック
		//flock($fp,LOCK_EX);
		return $fp;
	}
//////////////////////////////////////////////////
//文件写入（参数：文件指针）
	function WriteFileFP($fp,$text,$check=false) {
		if(!$check && !trim($text))//空白的话结束
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
//	写入文件
	function WriteFile($file,$text,$check=false) {
		if(!$check && !$text)//如果$text为空，则结束
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
//	读取文件并将其存储在数组中(参数:文件指针)
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
//	ファイルを粕んで芹误に呈羌
	function ParseFile($file) {

		$fp		= fopen($file,"r+");
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
			$amount	= count(glob(USER."*"));
			return $amount;
		}
	}
//////////////////////////////////////////////////
//	
	function JudgeList(){

		// 极瓢粕み哈み(forでル〖プさせてるから痰绿な借妄)
		if(JUDGE_LIST_AUTO_LOAD) {
			for($i=1000; $i<2500; $i++) {
				if( LoadJudgeData($i) !== false)
					$list[]=$i;
			}
			return $list;
		// 缄瓢(纳裁した冉们は极尸で今き颅せ)
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
//	お垛の山绩数及
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
//	里飘ログの山绩
function ShowLogList() {
	print("<div style=\"margin:15px\">\n");
	/*// ログ警ないなら链婶山绩すればいい。
	// common
	print("<h4>最近的战斗(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	// union
	print("<h4>BOSS战(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	// rank
	print("<h4>ランキング里(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	*/

	print("<a href=\"?log\" class=\"a0\">全部</a> ");
	print("<a href=\"?clog\">普通</a> ");
	print("<a href=\"?ulog\">BOSS战</a> ");
	print("<a href=\"?rlog\">排行战</a>");

	// common
	print("<h4>最近的战斗 - <a href=\"?clog\">全部显示</a>(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// union
	$limit	= 0;
	print("<h4>BOSS战 - <a href=\"?ulog\">全部显示</a>(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
		$limit++;
		if(30 <= $limit) {
			break;
		}
	}
	// rank
	$limit	= 0;
	print("<h4>排名战 - <a href=\"?rlog\">全部显示</a>(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
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
//	里飘ログの山绩
function LogShowCommon() {
	print("<div style=\"margin:15px\">\n");
	
	print("<a href=\"?log\">全部</a> ");
	print("<a href=\"?clog\" class=\"a0\">普通</a> ");
	print("<a href=\"?ulog\">BOSS战</a> ");
	print("<a href=\"?rlog\">排行战</a>");
	// common
	print("<h4>最近的战斗 - 全记录(Recent Battles)</h4>\n");
	$log	= @glob(LOG_BATTLE_NORMAL."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file);
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	里飘ログの山绩(union)
function LogShowUnion() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">全部</a> ");
	print("<a href=\"?clog\">普通</a> ");
	print("<a href=\"?ulog\" class=\"a0\">BOSS战</a> ");
	print("<a href=\"?rlog\">排行战</a>");
	// union
	print("<h4>BOSS战 - 全记录(Union Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_UNION."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"UNION");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	里飘ログの山绩(ranking)
function LogShowRanking() {
	print("<div style=\"margin:15px\">\n");

	print("<a href=\"?log\">全部</a> ");
	print("<a href=\"?clog\">普通</a> ");
	print("<a href=\"?ulog\">BOSS战</a> ");
	print("<a href=\"?rlog\" class=\"a0\">排行战</a>");
	// rank
	print("<h4>排名赛-全记录(Rank Battle Log)</h4>\n");
	$log	= @glob(LOG_BATTLE_RANK."*");
	foreach(array_reverse($log) as $file) {
		BattleLogDetail($file,"RANK");
	}
	print("</div>\n");
}
//////////////////////////////////////////////////
//	里飘ログの拒嘿を山绩(リンク)
function BattleLogDetail($log,$type=false) {
	$fp	= fopen($log,"r");

	// 眶乖だけ粕み哈む。
	$time	= fgets($fp);//倡幌箕粗 1乖誊
	$team	= explode("<>",fgets($fp));//チ〖ム叹 2乖誊
	$number	= explode("<>",trim(fgets($fp)));//客眶 3乖誊
	$avelv	= explode("<>",trim(fgets($fp)));//士堆レベル 4乖誊
	$win	= trim(fgets($fp));// 尽网チ〖ム 5乖誊
	$act	= trim(fgets($fp));// 另乖瓢眶 6乖誊
	fclose($fp);

	$date	= date("m/d H:i:s",substr($time,0,10));
	// 尽网チ〖ムによって咖を尸けて山绩
	if($type == "RANK")
		print("[ <a href=\"?rlog={$time}\">{$date}</a> ]&nbsp;\n");
	else if($type == "UNION")
		print("[ <a href=\"?ulog={$time}\">{$date}</a> ]&nbsp;\n");
	else
		print("[ <a href=\"?log={$time}\">{$date}</a> ]&nbsp;\n");
	print("<span class=\"bold\">战斗$act</span>回合&nbsp;\n");//另タ〖ン眶
	if($win === "0")
		print("[胜]&nbsp;<span class=\"recover\">{$team[0]}</span>");
	else if($win === "1")
		print("[败]&nbsp;<span class=\"dmg\">{$team[0]}</span>");
	else
		print("[平]&nbsp;{$team[0]}");

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
//	里飘ログを搀枉する
function ShowBattleLog($no,$type=false) {
	if($type == "RANK")
		$file	= LOG_BATTLE_RANK.$no.".dat";
	else if($type == "UNION")
		$file	= LOG_BATTLE_UNION.$no.".dat";
	else
		$file	= LOG_BATTLE_NORMAL.$no.".dat";
	if(!file_exists($file)) {//ログが痰い
		print("没有找到记录");
		return false;
	}

	$log	= file($file);
	$row	= 6;//ログの部乖誊から今き叫すか?
	$time	= substr($log[0],0,10);

	//print('<table style="width:100%;text-align:center" class="break"><tr><td>'."\n");
	print('<div style="padding:15px 0;width:100%;text-align:center" class="break">');
	print("<h2>战斗记录*</h2>");
	print("\n战斗开始于<br />");
	print(date("m/d H:i:s",substr($time,0,10)));
	print("</div>\n");
	//print("</td></tr></table>\n");

	while($log["$row"]) {
		print($log["$row"]);
		$row++;
	}
}
//////////////////////////////////////////////////
//	显示技能描述内容
	function ShowSkillDetail($skill,$radio=false) {
		if(!$skill) return false;
		
		if($radio)
			print('<input type="radio" name="newskill" value="'.$skill["no"].'" class="vcent" />');

		print('<img src="'.IMG_ICON.$skill["img"].'" class="vcent">');
		print("{$skill[name]}");

		if($radio)
			print(" / <span class=\"bold\">需要 {$skill[learn]} 点技能</span>");

/*原版英文内容
		if($skill[target][0] == "all")//滦据
			print(" / <span class=\"charge\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "enemy")
			print(" / <span class=\"dmg\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "friend")
			print(" / <span class=\"recover\">{$skill[target][0]}</span>");
		else if($skill[target][0] == "self")
			print(" / <span class=\"support\">{$skill[target][0]}</span>");
		else if(isset($skill[target][0]))
			print(" / {$skill[target][0]}");
*/

		if($skill[target][0] == "all")//转换技能对象为中文表述
			print(" / <span class=\"charge\">战场</span>");
		else if($skill[target][0] == "enemy")
			print(" / <span class=\"dmg\">敌方</span>");
		else if($skill[target][0] == "friend")
			print(" / <span class=\"recover\">友方</span>");
		else if($skill[target][0] == "self")
			print(" / <span class=\"support\">自己</span>");
		else if(isset($skill[target][0]))
			print(" / {$skill[target][0]}");
/*未翻译原版英文表述
		if($skill[target][1] == "all")//帽挛or剩眶or链挛
			print(" - <span class=\"charge\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "individual")
			print(" - <span class=\"recover\">{$skill[target][1]}</span>");
		else if($skill[target][1] == "multi")
			print(" - <span class=\"spdmg\">{$skill[target][1]}</span>");
		else if(isset($skill[target][1]))
			print(" - {$skill[target][1]}");
*/
		if($skill[target][1] == "all")//转换技能效果范围为中文表述
			print(" - <span class=\"charge\">全体</span>");
		else if($skill[target][1] == "individual")
			print(" - <span class=\"recover\">单体</span>");
		else if($skill[target][1] == "multi")
			print(" - <span class=\"spdmg\">群体</span>");
		else if(isset($skill[target][1]))
			print(" - {$skill[target][1]}");

		if(isset($skill["sacrifice"]))
			print(" / <span class=\"dmg\">Sacrifice:{$skill[sacrifice]}%</span>");
		// 消耗SP
		if(isset($skill["sp"]))
			print(" / <span class=\"support\">消耗 {$skill[sp]} 魔力</span>");
		// 久锐蒜数控
		if($skill["MagicCircleDeleteTeam"])
			print(" / <span class=\"support\">MagicCircle x".$skill["MagicCircleDeleteTeam"]."</span>");
		if($skill["pow"]) {
			print(" / <span class=\"".($skill["support"]?"recover":"dmg")."\">{$skill[pow]}%</span>x");
			print(( $skill["target"][2] ? $skill["target"][2] : "1" ) );
		}
		if($skill["type"] == 1)
			print(" / <span class=\"spdmg\">魔法</span>");
		if($skill["quick"])
			print(" / <span class=\"charge\">Quick</span>");
		if($skill["invalid"])
			print(" / <span class=\"charge\">攻击后排</span>");
		if($skill["priority"] == "Back")
			print(" / <span class=\"support\">以牙还牙</span>");
		if($skill["CurePoison"])
			print(" / <span class=\"support\">CurePoison</span>");

		if($skill["delay"])
			print(" / <span class=\"support\">延迟 -".$skill[delay]."%</span>");
//		if($skill["support"])
//			print(" / <span class=\"charge\">support</span>");

		if($skill["UpMAXHP"])
			print(" / <span class=\"charge\">生命上限 +".$skill[UpMAXHP]."%</span>");
		if($skill["UpMAXSP"])
			print(" / <span class=\"charge\">魔力上限 +".$skill[UpMAXSP]."%</span>");
		if($skill["UpSTR"])
			print(" / <span class=\"charge\">力量 +".$skill[UpSTR]."%</span>");
		if($skill["UpINT"])
			print(" / <span class=\"charge\">智慧 +".$skill[UpINT]."%</span>");
		if($skill["UpDEX"])
			print(" / <span class=\"charge\">敏捷 +".$skill[UpDEX]."%</span>");
		if($skill["UpSPD"])
			print(" / <span class=\"charge\">速度 +".$skill[UpSPD]."%</span>");
		if($skill["UpLUK"])
			print(" / <span class=\"charge\">幸运 +".$skill[UpLUK]."%</span>");
		if($skill["UpATK"])
			print(" / <span class=\"charge\">物理攻击 +".$skill[UpATK]."%</span>");
		if($skill["UpMATK"])
			print(" / <span class=\"charge\">魔法攻击 +".$skill[UpMATK]."%</span>");
		if($skill["UpDEF"])
			print(" / <span class=\"charge\">物理防御 +".$skill[UpDEF]."%</span>");
		if($skill["UpMDEF"])
			print(" / <span class=\"charge\">魔法防御 +".$skill[UpMDEF]."%</span>");

		if($skill["DownMAXHP"])
			print(" / <span class=\"dmg\">生命上限 -".$skill[DownMAXHP]."%</span>");
		if($skill["DownMAXSP"])
			print(" / <span class=\"dmg\">魔力上限 -".$skill[DownMAXSP]."%</span>");
		if($skill["DownSTR"])
			print(" / <span class=\"dmg\">力量 -".$skill[DownSTR]."%</span>");
		if($skill["DownINT"])
			print(" / <span class=\"dmg\">智慧 -".$skill[DownINT]."%</span>");
		if($skill["DownDEX"])
			print(" / <span class=\"dmg\">敏捷 -".$skill[DownDEX]."%</span>");
		if($skill["DownSPD"])
			print(" / <span class=\"dmg\">速度 -".$skill[DownSPD]."%</span>");
		if($skill["DownLUK"])
			print(" / <span class=\"dmg\">幸运 -".$skill[DownLUK]."%</span>");
		if($skill["DownATK"])
			print(" / <span class=\"dmg\">物理攻击 -".$skill[DownATK]."%</span>");
		if($skill["DownMATK"])
			print(" / <span class=\"dmg\">魔法攻击 -".$skill[DownMATK]."%</span>");
		if($skill["DownDEF"])
			print(" / <span class=\"dmg\">物理防御 -".$skill[DownDEF]."%</span>");
		if($skill["DownMDEF"])
			print(" / <span class=\"dmg\">魔法防御 -".$skill[DownMDEF]."%</span>");

		if($skill["PlusSTR"])
			print(" / <span class=\"charge\">力量 +".$skill[PlusSTR]."</span>");
		if($skill["PlusINT"])
			print(" / <span class=\"charge\">智慧 +".$skill[PlusINT]."</span>");
		if($skill["PlusDEX"])
			print(" / <span class=\"charge\">敏捷 +".$skill[PlusDEX]."</span>");
		if($skill["PlusSPD"])
			print(" / <span class=\"charge\">速度 +".$skill[PlusSPD]."</span>");
		if($skill["PlusLUK"])
			print(" / <span class=\"charge\">幸运 +".$skill[PlusLUK]."</span>");

		if($skill["charge"]["0"] || $skill["charge"]["1"]) {
			print(" / (".($skill["charge"]["0"]?$skill["charge"]["0"]:"0").":");
			print(($skill["charge"]["1"]?$skill["charge"]["1"]:"0").")");
		}

		// 装备需求
		if($skill["limit"]) {
			$Limit	= " / 装备需求：";
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
//	显示道具描述内容
	function ShowItemDetail($item,$amount=false,$text=false,$need=false) {
		if(!$item) return false;

		$html	= "<img src=\"".IMG_ICON.$item["img"]."\" class=\"vcent\">";
		// 篮希猛
		if($item["refine"])
			$html	.= "+{$item[refine]} ";
		if($item["AddName"])
			$html	.= "{$item[AddName]} ";
		$html	.= "{$item[base_name]}";// 叹涟

		if($item["type"])
			$html	.= "<span class=\"light\"> ({$item[type]})</span>";
		if($amount) {//眶翁
			$html	.= " x<span class=\"bold\" style=\"font-size:80%\">{$amount}</span>";
		}
		if($item["atk"]["0"])//湿妄苟封
			$html	.= ' / <span class="dmg">物理攻击：'.$item[atk][0].'</span>';
		if($item["atk"]["1"])//蒜恕苟封
			$html	.= ' / <span class="spdmg">魔法攻击：'.$item[atk][1].'</span>';
		if($item["def"]) {
			$html	.= " / <span class=\"recover\">物理防御：{$item[def][0]}+{$item[def][1]}</span>";
			$html	.= " / <span class=\"support\">魔法防御：{$item[def][2]}+{$item[def][3]}</span>";
		}
		if($item["P_SUMMON"])
			$html	.= ' / <span class="support">召唤 +'.$item["P_SUMMON"].'%</span>';
		if(isset($item["handle"]))
			$html	.= ' / <span class="charge">重量：'.$item[handle].'</span>';
		if($item["option"])
			$html	.= ' / <span style="font-size:80%">'.substr($item["option"],0,-2)."</span>";

		if($need && $item["need"]) {
			$html	.= " /";
			foreach($item["need"] as $M_itemNo => $M_amount) {
				$M_item	= LoadItemData($M_itemNo);
				$html	.= "<img src=\"".IMG_ICON.$M_item["img"]."\" class=\"vcent\">";
				$html	.= "{$M_item[base_name]}";// 叹涟
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
//	乐い焚桂矢でエラ〖山绩
	function ShowResult($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="result'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	乐い焚桂矢でエラ〖山绩
	function ShowError($message,$add=false) {
		if($add)
			$add	= " ".$add;
		if(is_string($message))
			print('<div class="error'.$add.'">'.$message.'</div>'."\n");
	}
//////////////////////////////////////////////////
//	マニュアルを山绩する
	function ShowManual() {
		include(MANUAL);
		return true;
	}
//////////////////////////////////////////////////
//	マニュアルを山绩する
	function ShowManual2() {
		include(MANUAL_HIGH);
		return true;
	}
//////////////////////////////////////////////////
//	チュ〖トリアルを山绩する
	function ShowTutorial() {
		include(TUTORIAL);
		return true;
	}
//////////////////////////////////////////////////
//	构糠柒推の山绩
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
			print('<a href="?update">刷新<br>');
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
//	げ〖むで〖た
	function ShowGameData() {
		?>
<div style="margin:15px">
<h4>GameData</h4>
<div style="margin:0 20px">
| <a href="?gamedata=job">职业(Job)</a> | 
<a href="?gamedata=item">道具(item)</a> | 
<a href="?gamedata=judge">判定</a> | 
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
//	链ランキングの山绩
	function RankAllShow() {
		print('<div style="margin:15px">'."\n");
		print('<h4>Ranking - '.date("Y年n月j日 G:i:s").'</h4>'."\n");
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
	*	掐蜗された矢机误を澄千する
	*	手り猛
	*	喇根 = array(true,恃垂($string));
	*	己窃 = array(false,己窃妄统);
	*/
	function CheckString($string,$maxLength=16) {
		$string	= trim($string);
		$string	= stripslashes($string);
		if(is_numeric(strpos($string,"\t"))) {
			return array(false,"非法字符");
		}
		if(is_numeric(strpos($string,"\n"))) {
			return array(false,"非法字符");
		}
		if (!$string) {
			return array(false,"不能为空");
		}
		$length	= strlen($string);
		if ( 0 == $length || $maxLength < $length) {
			return array(false,"过短或过长");
		}
		$string	= htmlspecialchars($string,ENT_QUOTES);
		return array(true,$string);
	}
///////////////////////////////////////////////////
//	眉琐を冉们。
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
?>