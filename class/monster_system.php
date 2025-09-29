<?php
/**
 * 怪物系統模組
 * Monster system module
 *
 * 處理怪物隊伍生成、選擇、數量計算等功能
 * Handles monster party generation, selection, count calculation, etc.
 */

/**
 * 計算敵人數量
 * Calculate enemy number
 *
 * 根據玩家隊伍人數計算敵人數量，返回數量～數量+2之間的隨機值
 * Calculate enemy count based on player party size, return random value between count and count+2
 */
function EnemyNumber($party, &$main) {
    $min    = count($party);//プレイヤ一のPT數
    if($min == 5)//5人なら5匹
        return 5;
    $max    = $min + ENEMY_INCREASE;// つまり、+2なら[1人:1～3匹] [2人:2～4匹] [3:3-5] [4:4-5] [5:5]
    if($max>5)
        $max    = 5;
    mt_srand();
    return mt_rand($min,$max);
}

/**
 * 從出現率選擇怪物
 * Select monster from appearance rate
 *
 * 根據怪物列表的出現率隨機選擇一個怪物
 * Randomly select a monster based on appearance rates in monster list
 */
function SelectMonster($monster, &$main) {
    foreach($monster as $val)
        $max    += $val[0];//確率の合計
    $pos    = mt_rand(0,$max);//0～合計 の中で亂數を取る
    foreach($monster as $monster_no => $val) {
        $upp    += $val[0];//その時點での確率の合計
        if($pos <= $upp)//合計より低ければ　敵が決定される
            return $monster_no;
    }
}

/**
 * 創建敵人隊伍
 * Create enemy party
 *
 * 根據指定數量和怪物列表創建敵人隊伍，可以指定特定怪物
 * Create enemy party based on specified count and monster list, can specify particular monsters
 */
function EnemyParty($Amount,$MonsterList,$Specify=false, &$main) {

    // 指定モンスタ一
    if($Specify) {
        $MonsterNumbers    = $Specify;
    }

    // モンスタ一をとりあえず配列に全部入れる
    $enemy    = array();
    if(!$Amount)
        return $enemy;
    mt_srand();
    for($i=0; $i<$Amount; $i++)
        $MonsterNumbers[]    = SelectMonster($MonsterList, $main);

    // 重複しているモンスタ一を調べる
    $overlap    = array_count_values($MonsterNumbers);

    // 敵情報を讀んで配列に入れる。
    include(CLASS_MONSTER);
    foreach($MonsterNumbers as $Number) {
        if(1 < $overlap[$Number])//1匹以上出現するなら名前に記號をつける。
            $enemy[]    = new monster(CreateMonster($Number,true));
        else
            $enemy[]    = new monster(CreateMonster($Number));
    }
    return $enemy;
}
?>
