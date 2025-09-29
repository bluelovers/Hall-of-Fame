<?php
/**
 * 戰鬥系統模組
 * Battle system module
 *
 * 處理戰鬥流程、模擬戰鬥、怪物戰鬥等功能
 * Handles battle process, simulation battles, monster battles, etc.
 */

/**
 * 測試角色複製戰鬥
 * Test character doppelganger battle
 *
 * 處理測試戰鬥的複製角色功能
 * Handle test battle with duplicated characters
 */
function CharTestDoppel(&$main) {
    if(!$_POST["TestBattle"]) return 0;

    $char    = $main->char[$_GET["char"]];
    DoppelBattle($main, array($char));
}

/**
 * 複製戰鬥
 * Doppelganger battle
 *
 * 創建複製角色並進行戰鬥
 * Create duplicate characters and engage in battle
 */
function DoppelBattle(&$main, $party,$turns=10) {
    //$enemy    = $party;
    //これが無いとPHP4or5 で違う結果になるんです
    //$enemy    = unserialize(serialize($enemy));
    // ↓
    foreach($party as $key => $char) {
        $enemy[$key]    = new char();
        $enemy[$key]->SetCharData(get_object_vars($char));

    }
    foreach($enemy as $key => $doppel) {
        //$doppel->judge    = array();//コメントを取るとドッペルが行動しない。
        $enemy[$key]->ChangeName("ニセ".$doppel->name);
    }
    //dump($enemy[0]->judge);
    //dump($party[0]->judge);

    include(CLASS_BATTLE);
    $battle    = new battle($party,$enemy);
    $battle->SetTeamName($main->name,"ドッペル");
    $battle->LimitTurns($turns);//最大タ一ン數は10
    $battle->NoResult();
    $battle->Process();//戰鬥開始
    return true;
}

/**
 * 模擬戰鬥處理
 * Simulation battle process
 *
 * 處理模擬戰鬥的準備和驗證
 * Handle simulation battle preparation and validation
 */
function SimuBattleProcess(&$main) {
    if($_POST["simu_battle"]) {
        MemorizeParty($main);//パ一ティ一記憶
        // 自分パ一ティ一
        foreach($main->char as $key => $val) {//チェックされたやつリスト
            if($_POST["char_".$key])
                $MyParty[]    = $main->char[$key];
        }
        if( count($MyParty) === 0) {
            ShowError('戰鬥至少要一個人參加',"margin15");
            return false;
        } else if(5 < count($MyParty)) {
            ShowError('戰鬥最多只能上五個人',"margin15");
            return false;
        }
        DoppelBattle($main, $MyParty,50);
        return true;
    }
}

/**
 * 模擬戰鬥顯示
 * Simulation battle display
 *
 * 顯示模擬戰鬥的表單和隊伍選擇
 * Display simulation battle form and team selection
 */
function SimuBattleShow(&$main, $message=false) {
    print('<div style="margin:15px">');
    ShowError($message);
    print('<span class="bold">模擬戰</span>');
    print('<h4>Teams</h4></div>');
    print('<form action="'.INDEX.'?simulate" method="post">');
    ShowCharacters($main->char,CHECKBOX,explode("<>",$main->party_memo));
        ?>
<div style="margin:15px;text-align:center">
<input type="submit" class="btn" name="simu_battle" value="戰鬥!">
<input type="reset" class="btn" value="重置"><br>
保存此隊伍:<input type="checkbox" name="memory_party" value="1">
</div></form>
<?php
}

/**
 * 怪物顯示
 * Monster display
 *
 * 顯示指定地圖的怪物信息和隊伍選擇
 * Display monster information and team selection for specified map
 */
function MonsterShow(&$main) {
    $land_id    = $_GET["common"];
    include(DATA_LAND);
    include_once(DATA_LAND_APPEAR);
    // まだ行けないマップなのに行こうとした。
    if(!in_array($_GET["common"],LoadMapAppear($main))) {
        print('<div style="margin:15px">not appeared or not exist</div>');
        return false;
    }
    list($land,$monster_list)    = LandInformation($land_id);
    if(!$land || !$monster_list) {
        print('<div style="margin:15px">fail to load</div>');
        return false;
    }

    print('<div style="margin:15px">');
    ShowError($message);
    print('<span class="bold">'.$land["name"].'</span>');
    print('<h4>隊伍</h4></div>');
    print('<form action="'.INDEX.'?common='.$_GET["common"].'" method="post">');
    ShowCharacters($main->char,"CHECKBOX",explode("<>",$main->party_memo));
        ?>
<div style="margin:15px;text-align:center">
<input type="submit" class="btn" name="monster_battle" value="戰鬥!">
<input type="reset" class="btn" value="重置"><br>
保存此隊伍:<input type="checkbox" name="memory_party" value="1">
</div></form>
<?php
    include(DATA_MONSTER);
    include(CLASS_MONSTER);
    foreach($monster_list as $id =>$val) {
        if($val[1])
            $monster[]    = new monster(CreateMonster($id));
    }
    print('<div style="margin:15px"><h4>MonsterAppearance</h4></div>');
    ShowCharacters($monster,"MONSTER",$land["land"]);
}

/**
 * 怪物戰鬥處理
 * Monster battle process
 *
 * 處理與怪物戰鬥的完整流程
 * Handle complete process of battling with monsters
 */
function MonsterBattle(&$main) {
    if($_POST["monster_battle"]) {
        MemorizeParty($main);//パ一ティ一記憶
        // そのマップで戰えるかどうか確認する。
        include_once(DATA_LAND_APPEAR);
        $land    = LoadMapAppear($main);
        if(!in_array($_GET["common"],$land)) {
            ShowError("沒有出現地圖","margin15");
            return false;
        }

        // Timeが足りてるかどうか確認する
        if($main->time < NORMAL_BATTLE_TIME) {
            ShowError("Time 不足 (必要 Time:".NORMAL_BATTLE_TIME.")","margin15");
            return false;
        }
        // 自分パ一ティ一
        foreach($main->char as $key => $val) {//チェックされたやつリスト
            if($_POST["char_".$key])
                $MyParty[]    = $main->char[$key];
        }
        if( count($MyParty) === 0) {
            ShowError('戰鬥至少要一個人參加',"margin15");
            return false;
        } else if(5 < count($MyParty)) {
            ShowError('戰鬥最多只能上五個人',"margin15");
            return false;
        }
        // 敵パ一ティ一(または一匹)
        include(DATA_LAND);
        include(DATA_MONSTER);
        list($Land,$MonsterList)    = LandInformation($_GET["common"]);
        $EneNum    = EnemyNumber($MyParty, $main);
        $EnemyParty    = EnemyParty($EneNum,$MonsterList, false, $main);

        $main->WasteTime(NORMAL_BATTLE_TIME);//時間の消費
        include(CLASS_BATTLE);
        $battle    = new battle($MyParty,$EnemyParty);
        $battle->SetBackGround($Land["land"]);//背景
        $battle->SetTeamName($main->name,$Land["name"]);
        $battle->Process();//戰鬥開始
        $battle->SaveCharacters();//キャラデ一タ保存
        list($UserMoney)    = $battle->ReturnMoney();//戰鬥で得た合計金額
        //お金を增やす
        $main->GetMoney($UserMoney);
        //戰鬥ログの保存
        if($main->record_btl_log)
            $battle->RecordLog();

        // 道具を受け取る
        if($itemdrop    = $battle->ReturnItemGet(0)) {
            $main->LoadUserItem();
            foreach($itemdrop as $itemno => $amount)
                $main->AddItem($itemno,$amount);
            $main->SaveUserItem();
        }

        //dump($itemdrop);
        //dump($this->item);
        return true;
    }
}
?>
