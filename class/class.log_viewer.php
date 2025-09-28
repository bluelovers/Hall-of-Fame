<?php

//	全ランキングの表示
function RankAllShow() {
    print('<div style="margin:15px">'."\n");
    print('<h4>Ranking - '.date("Y年n月j日 G:i:s").'</h4>'."\n");
    include(CLASS_RANKING);
    $Rank	= new Ranking();
    $Rank->ShowRanking();
    print('</div>'."\n");
}

//	戦闘ログの表示
function showLogList() {
    print("<div style=\"margin:15px\">");
    print("<a href=\" ?log\" class=\"a0\">全部</a> ");
    print("<a href=\" ?clog\">普通</a> ");
    print("<a href=\" ?ulog\">BOSS戰</a> ");
    print("<a href=\" ?rlog\">排行戰</a>");

    // common
    print("<h4>最近的戰鬥 - <a href=\" ?clog\" class=\"a0\">全表示</a>(Recent Battles)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_NORMAL);
    $limit = 0;
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file);
        $limit++;
        if(30 <= $limit) {
            break;
        }
    }
    // union
    $limit    = 0;
    print("<h4>BOSS戰 - <a href=\" ?ulog\" class=\"a0\">全表示</a>(Union Battle Log)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_UNION);
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file,"UNION");
        $limit++;
        if(30 <= $limit) {
            break;
        }
    }
    // rank
    $limit    = 0;
    print("<h4>排名戰 - <a href=\" ?rlog\" class=\"a0\">全表示</a>(Rank Battle Log)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_RANK);
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file,"RANK");
        $limit++;
        if(30 <= $limit) {
            break;
        }
    }

    print("</div>\n");
}

//	戦闘ログの表示
function showCommonLog() {
    print("<div style=\"margin:15px\">");
    
    print("<a href=\" ?log\">全部</a> ");
    print("<a href=\" ?clog\" class=\"a0\">普通</a> ");
    print("<a href=\" ?ulog\">BOSS戰</a> ");
    print("<a href=\" ?rlog\">排行戰</a>");
    // common
    print("<h4>最近的戰鬥 - 全記錄(Recent Battles)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_NORMAL);
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file);
    }
    print("</div>\n");
}

//	戦闘ログの表示(union)
function showUnionLog() {
    print("<div style=\"margin:15px\">");

    print("<a href=\" ?log\">全部</a> ");
    print("<a href=\" ?clog\">普通</a> ");
    print("<a href=\" ?ulog\" class=\"a0\">BOSS戰</a> ");
    print("<a href=\" ?rlog\">排行戰</a>");
    // union
    print("<h4>BOSS戰 - 全記錄(Union Battle Log)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_UNION);
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file,"UNION");
    }
    print("</div>\n");
}

//	戦闘ログの表示(ranking)
function showRankingLog() {
    print("<div style=\"margin:15px\">");

    print("<a href=\" ?log\">全部</a> ");
    print("<a href=\" ?clog\">普通</a> ");
    print("<a href=\" ?ulog\">BOSS戰</a> ");
    print("<a href=\" ?rlog\" class=\"a0\">排行戰</a>");
    // rank
    print("<h4>排名賽-全記錄(Rank Battle Log)</h4>\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_RANK);
    foreach(array_reverse($log) as $file) {
        battleLogDetail($file,"RANK");
    }
    print("</div>\n");
}

//	戦闘ログの詳細を表示(リンク)
function battleLogDetail($log,$type=false) {
    $fp    = fopen($log,"r");

    // 数行だけ読み込む。
    $time    = fgets($fp);//開始時間 1行目
    $team    = explode("<>",fgets($fp));//チーム名 2行目
    $number    = explode("<>",trim(fgets($fp)));//人数 3行目
    $avelv    = explode("<>",trim(fgets($fp)));//平均レベル 4行目
    $win    = trim(fgets($fp));// 勝利チーム 5行目
    $act    = trim(fgets($fp));// 総行動数 6行目
    fclose($fp);

    $date    = date("c",substr($time,0,10));
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

//	戦闘ログを回覧する
function showBattleLog($no,$type=false) {
    if($type == "RANK")
        $file    = LOG_BATTLE_RANK.$no.".dat";
    else if($type == "UNION")
        $file    = LOG_BATTLE_UNION.$no.".dat";
    else
        $file    = LOG_BATTLE_NORMAL.$no.".dat";
    if(!file_exists($file)) {//ログが無い
        print("log doesnt exists");
        return false;
    }

    $log    = file($file);
    $row    = 6;//ログの何行目から書き出すか?
    $time    = substr($log[0],0,10);

    //print('<table style="width:100%;text-align:center" class="break"><tr><td>\n');
    print('<div style="padding:15px 0;width:100%;text-align:center" class="break">');
    print("<h2>battle log*</h2>");
    print("\nthis battle starts at<br />");
    print(date("c",substr($time,0,10)));
    print("</div>\n");
    //print("</td></tr></table>\n");

    while(isset($log["$row"])) {
        print($log["$row"]);
        $row++;
    }
}
?>