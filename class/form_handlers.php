<?php
/**
 * 表單處理模組
 * Form handlers module
 *
 * 處理各種表單提交和驗證功能
 * Handles various form submissions and validation functions
 */

/**
 * 記憶隊伍選擇
 * Memorize party selection
 *
 * 當用戶選擇記住隊伍時，保存當前選擇的角色
 * When user chooses to remember party, save currently selected characters
 */
function MemorizeParty(&$main) {
    if($_POST["memory_party"]) {
        //$temp    = $main->party_memo;//一時的に記憶
        //$main->party_memo    = array();
        foreach($main->char as $key => $val) {//チェックされたやつリスト
            if($_POST["char_".$key])
                //$main->party_memo[]     = $key;
                $PartyMemo[]    = $key;
        }
        //if(5 < count($main->party_memo) )//5人以上は馱目
        //    $main->party_memo    = $temp;
        if(0 < count($PartyMemo) && count($PartyMemo) < 6)
            $main->party_memo    = implode("<>",$PartyMemo);
    }
}

/**
 * 排名處理
 * Ranking process
 *
 * 處理排名戰鬥和隊伍設定的相關功能
 * Handle ranking battle and team setting related functions
 */
function RankProcess(&$main, &$Ranking) {

    // RankBattle
    if($_POST["ChallengeRank"]) {
        if(!$main->party_rank) {
            ShowError("小隊尚未設定","margin15");
            return false;
        }
        $result    = $main->CanRankBattle();
        if(is_array($result)) {
            ShowError("仍需等待時間（？）","margin15");
            return false;
        }

        /*
            $BattleResult = 0;//勝利
            $BattleResult = 1;//敗北
            $BattleResult = "d";//引分
        */
        //list($message,$BattleResult)    = $Rank->Challenge(&$main);
        $Result    = $Ranking->Challenge($main);

        //if($Result === "Battle")
        //    $main->RankRecord($BattleResult,"CHALLENGE",false);

        /*
        // 勝敗によって次までの戰鬥の時間を設定する
        //勝利
        if($BattleResult === 0) {
            $main->SetRankBattleTime(time() + RANK_BATTLE_NEXT_WIN);

        //敗北
        } else if($BattleResult === 1) {
            $main->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

        //引分け
        } else if($BattleResult === "d") {
            $main->SetRankBattleTime(time() + RANK_BATTLE_NEXT_LOSE);

        }
        */

        return $Result;// 戰鬥していれば $Result = "Battle";
    }

    // ランキング用のチ一ム登錄
    if($_POST["SetRankTeam"]) {
        $now    = time();
        // まだ設定時間が殘っている。
        if(($now - $main->rank_set_time) < RANK_TEAM_SET_TIME) {
            $left    = RANK_TEAM_SET_TIME - ($now - $main->rank_set_time);
            $day    = floor($left / 3600 / 24);
            $hour    = floor($left / 3600)%24;
            $min    = floor(($left % 3600)/60);
            $sec    = floor(($left % 3600)%60);
            ShowError("離再設定隊伍還需 {$day}日 と {$hour}小時 {$min}分 {$sec}秒","margin15");
            return false;
        }
        foreach($main->char as $key => $val) {//チェックされたやつリスト
            if($_POST["char_".$key])
                $checked[]    = $key;
        }
        // 設定キャラ數が多いか少なすぎる
        if(count($checked) == 0 || 5 < count($checked)) {
            ShowError("隊伍人數應大於1人小於5人","margin15");
            return false;
        }

        $main->party_rank    = implode("<>",$checked);
        $main->rank_set_time    = $now;
        ShowResult("隊伍設定完成","margin15");
        return true;
    }
}
?>
