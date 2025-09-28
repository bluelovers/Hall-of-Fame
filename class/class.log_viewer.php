<?php

//	全ランキングの表示
function RankAllShow() {
    print('<div style="margin:15px">\n');
    print('<h4>Ranking - '.date("Y年n月j日 G:i:s").'</h4>\n');
    include(CLASS_RANKING);
    $Rank	= new Ranking();
    $Rank->ShowRanking();
    print('</div>\n');
}

//	戦闘ログの表示
function showLogList() {
    print("<div style=\"margin:15px\">");
    print("<a href=\"?log\" class=\"a0\">全部</a> ");
    print("<a href=\"?clog\">普通</a> ");
    print("<a href=\"?ulog\">BOSS戰</a> ");
    print("<a href=\"?rlog\">排行戰</a>");

    // common
    print("<h4>最近的戰鬥 - <a href=\"?clog\">全表示</a>(Recent Battles)</h4>\n");
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
    print("<h4>BOSS戰 - <a href=\"?ulog\">全表示</a>(Union Battle Log)</h4>\n");
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
    print("<h4>排名戰 - <a href=\"?rlog\">全表示</a>(Rank Battle Log)</h4>\n");
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
    
    print("<a href=\"?log\">全部</a> ");
    print("<a href=\"?clog\" class=\"a0\">普通</a> ");
    print("<a href=\"?ulog\">BOSS戰</a> ");
    print("<a href=\"?rlog\">排行戰</a>");
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

    print("<a href=\"?log\">全部</a> ");
    print("<a href=\"?clog\">普通</a> ");
    print("<a href=\"?ulog\" class=\"a0\">BOSS戰</a> ");
    print("<a href=\"?rlog\">排行戰</a>");
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

    print("<a href=\"?log\">全部</a> ");
    print("<a href=\"?clog\">普通</a> ");
    print("<a href=\"?ulog\">BOSS戰</a> ");
    print("<a href=\"?rlog\" class=\"a0\">排行戰</a>");
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

function HuntShow($main) {
    include(DATA_LAND);
    include(DATA_LAND_APPEAR);
    print('<div style="margin:15px">');
    print('<h4>普通怪物</h4>');
    print('<div style="margin:0 20px">');

    $mapList    = LoadMapAppear($main);
    foreach($mapList as $map) {
        list($land)    = LandInformation($map);
        print("<p style='display:inline;margin-right:32px;'><a href=\" ?common={$map}\">{$land[name]}</a>");
        //print(" ({$land[proper]})");
        print("</p>");
    }

    // Union
    print("</div>\n");
    $files    = GlobOnlyFileDat(UNION);
    if($files) {
        include(CLASS_UNION);
        include(DATA_MONSTER);
        $Union = [];
        foreach($files as $file) {
            $UnionMons    = new union($file);
            if($UnionMons->is_Alive())
                $Union[]    = $UnionMons;
        }
    }
    if(isset($Union)) {
        print('<h4>BOSS</h4>');
        $result = $main->CanUnionBattle();
        if($result !== true) {
            $left_minute    = floor($result/60);
            $left_second    = $result%60;
            print('<div style="margin:0 20px">');
            print('離下次戰鬥還需要 : <span class="bold">'.$left_minute. ":".sprintf("%02d",$left_second)."</span>");
            print("</div>");
        }
        print("</div>");
        $main->ShowCharacters($Union);
    } else {
        print("</div>");
    }

    // union
    print("<div style=\"margin:0 15px\">\n");
    print("<h4>BOSS戰記錄 <a href=\"?ulog\">全表示</a></h4>\n");
    print("<div style=\"margin:0 20px\">\n");
    $log    = @GlobOnlyFileDat(LOG_BATTLE_UNION);
    $limit = 0;
    if ($log) {
        foreach(array_reverse($log) as $file) {
            $limit++;
            battleLogDetail($file,"UNION");
            if(15 <= $limit)
                break;
        }
    }
    print("</div></div>\n");
}

function RankShow($main, &$Ranking) {

    // チ一ム再設定の殘り時間計算
    $now    = time();
    $left_mes = '';
    $disable = '';
    if( ($now - $main->rank_set_time) < RANK_TEAM_SET_TIME) {
        $left    = RANK_TEAM_SET_TIME - ($now - $main->rank_set_time);
        $hour    = floor($left / 3600);
        $min    = floor(($left % 3600)/60);
        $left_mes    = "<div class=\"bold\">{$hour}Hour {$min}minutes left to set again.</div>\n";
        $disable    = " disabled";
    }
        ?>

<div style="margin:15px">
<?php print ShowError(isset($message) ? $message : '');?>
<form action="?menu=rank" method="post">
<h4>排行榜(Ranking) - <a href="?rank">查看排名</a> <a href="?manual#ranking" target="_blank" class="a0">?</a></h4>
<?php
    // 挑戰できるかどうか(時間の經過で)
    $CanRankBattle    = $main->CanRankBattle();
    $disableRB = '';
    if($CanRankBattle !== true) {
        print('<p>Time left to Next : <span class="bold">');
        print($CanRankBattle[0].":".sprintf("%02d",$CanRankBattle[1]).":".sprintf("%02d",$CanRankBattle[2]));
        print("</span></p>\n");
        $disableRB    = " disabled";
    }

    print("<div style=\"width:100%;padding-left:30px\">\n");
    print("<div style=\"float:left;width:50%\">\n");
    print("<div class=\"u\">TOP 5</div>\n");
    $Ranking->ShowRanking(0,4);
    print("</div>\n");
    print("<div style=\"float:right;width:50%\">\n");
    print("<div class=\"u\">NEAR 5</div>\n");
    $Ranking->ShowRankingRange($main->id,5);
    print("</div>\n");
    print("<div style=\"clear:both\"></div>\n");
    print("</div>\n");

?>
<input type="submit" class="btn" value="挑戰！" name="ChallengeRank" style="width:160px"<?php print $disableRB?> />
</form>
<form action="?menu=rank" method="post">
<h4>隊伍設置(Team Setting)</h4>
<p>排名戰隊伍設定。<br />
這裡設置排名戰隊伍。</p>
</div>
<?php $main->ShowCharacters($main->char,'CHECKBOX',explode("<>",$main->party_rank));?>

<div style="margin:15px">
<?php print $left_mes?>
<input type="submit" class="btn" style="width:160px" value="設定隊伍"<?php print $disable?> />
<input type="hidden" name="SetRankTeam" value="1" />
<p>設定後<?php print $reset=floor(RANK_TEAM_SET_TIME/(60*60))?>小時後才能再設置。<br />Team setting disabled after <?php print $reset?>hours once set.</p>
</form>
</div>
<?php 
}

?>
