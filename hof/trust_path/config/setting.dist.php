<?php

define('BASE_URL_ROOT', '/'.trim(dirname($_SERVER['PHP_SELF']), '/').'/');

define('BASE_URL', 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . (((!$_SERVER['HTTPS'] && $_SERVER['SERVER_PORT'] == 80) || ($_SERVER['HTTPS'] && $_SERVER['SERVER_PORT'] == 8080)) ? '' : ':' . $_SERVER['SERVER_PORT']) . BASE_URL_ROOT);

define('BASE_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/../../');

define('BASE_TRUST_PATH', BASE_PATH . 'trust_path/');

define('BASE_PATH_STATIC', BASE_PATH . 'static/');

define('BASE_PATH_TPL', BASE_TRUST_PATH . 'tpl/');

define('BASE_EXT', '.yml');

// game setting
define('TITLE', 'Hall of Rumor 噂のホール'); //タイトル
define('MAX_TIME', 1000); //最大Time
define('TIME_GAIN_DAY', 6000); //1日に回復する総Time
define('MAX_CHAR', 5); //最大所持キャラ数
define('MAX_USERS', 500); //最大登録者人数
define('ABANDONED', 60 * 60 * 24 * 14); //ゲームを放棄されたとみなす期間
define('CONTROL_PERIOD', 60 * 60 * 12); //定期管理の周期
define('RECORD_IP', 1); //IPを記録するか？(0=NO 1=YES)

// other
define('DEBUG', 0); // 0=OFF
define('SESSION_SWITCH', 0); // 0=OFF
define('CHAR_ROW', 5); // 1画面のキャラの列数
define('CRYPT_KEY', '$1$12345678$'); //パス符号化キー(ゲーム設置後は変えるな)
define('COOKIE_EXPIRE', 60 * 60 * 24 * 3); //60*60*24*3
define('UP_PASS', 'password'); // 更新情報のみしか使用されない

define('START_TIME', 900); //ゲーム開始時に持ってるTime
define('START_MONEY', 50000); //初期所持金
define('MAX_STATUS', 250); //ステータス最大値
define('GET_STATUS_POINT', 5); //LVUPした時に得るステポの数値
define('GET_SKILL_POINT', 2); //LVUPした時に得る技ポの数値
define('MAX_LEVEL', 50); //最大レベル
define('SELLING_PRICE', 1 / 5); //売値が設定されていないアイテムの売値→(買値*SELLING_PRICE)
define('REFINE_LIMIT', 10); //精錬限界値

define('EXP_RATE', 1); //経験値もらえる倍率
define('MONEY_RATE', 1); //お金もらえる倍率

// open all land
define('DEBUG_LANDAPPEAR_ALL', 0);

define('NEW_NAME_COST', 300000); //新しい名前に変更するのに必要なお金
define('BBS_OUT', ''); //外部BBSがあればそのアドレス、無ければ空欄→''
define('BBS_BOTTOM_TOGGLE', 0); // 下にあった一行掲示板(0=OFF)
define('AUCTION_TOGGLE', 0); // オークションを機能させるか(0=OFF 1=ON)
define('AUCTION_EXHIBIT_TOGGLE', 0); // オークションの出品を可能にするか(0=OFF 1=ON)
define('JUDGE_LIST_AUTO_LOAD', 0); //パターン判断のリストを 1=自動 0=手動追加(若干軽い)
define('AUCTION_MAX', 100); //オークション同時に出品できる品数。

// ranking
define('RANK_TEAM_SET_TIME', 60 * 60 * 48); //ランキングのチーム設定できる周期
define('RANK_BATTLE_NEXT_LOSE', 60 * 60 * 24); //ランキング戦 負けたとき次戦えるまで
define('RANK_BATTLE_NEXT_WIN', 60 * 1); //ランキング戦 勝ったとき次戦えるまで

// battle setting
define('NORMAL_BATTLE_TIME', 100); //通常のモンスターとの戦闘で消費する時間
define('ENEMY_INCREASE', 0); //敵の増員(ランダム)
define('BATTLE_MAX_TURNS', 100); //戦闘の最大行動回数(戦闘が数値以上長引くと終了させる)
define('TURN_EXTENDS', 20); // 決着がつきそうな場合延長するターン数。
define('BATTLE_MAX_EXTENDS', 100); //延長した場合の最大行動回数(延長の限界)
define('BTL_IMG_TYPE', 2); // (0=GD 1=CSS 2=反転済画像使用CSS)
define('BTL_IMG', './image.php'); // GD表示
define('BATTLE_STAT_TURNS', 10); // 戦闘の状況を表示する間隔
define('DEAD_IMG', 'mon_145.gif'); // HP=0 のキャラの画像
define('MAX_BATTLE_LOG', 100); // 戦闘ログを保存する件数
define('MAX_BATTLE_LOG_UNION', 100); // 戦闘ログを保存する件数
define('MAX_BATTLE_LOG_RANK', 100); // 戦闘ログを保存する件数
define('MAX_STATUS_MAXIMUM', 2500); // 初期値x値(%) 戦闘中の能力上昇で上がる数値の限界値(1000%=10倍が限界)

define('DELAY_TYPE', 1); // 0=旧 1=新
// DELAY_TYPE=0
define('DELAY', 2.5); //ディレイ(2以上が目安。数値が低いとSPDが高い人は有利)
// DELAY_TYPE=1
define('DELAY_BASE', 5); // 数値が高いと差がつかなくなる。

// union
define('UNION_BATTLE_TIME', 10); //ユニオン戦で消費する時間
define('UNION_BATTLE_NEXT', 60 * 20); //Union次の戦闘までの間隔

// files
define('INDEX', 'index.php');

define('COLOR_FILE', BASE_TRUST_PATH . 'HOF/Resource/Color.dat');

// DAT

define('BASE_PATH_CACHE', BASE_TRUST_PATH . 'cache/');

define('BASE_PATH_TRASH', BASE_PATH_CACHE . 'trash/');

define('BASE_PATH_SESSION', BASE_PATH_CACHE . 'session/');

define('DAT_DIR', BASE_TRUST_PATH . 'dat/');

define('AUCTION_ITEM', DAT_DIR . 'auction'.BASE_EXT); //アイテムオークション用のファイル
define('AUCTION_ITEM_LOG', DAT_DIR . 'auction_log.dat'); //アイテムオークション用のログファイル

define('UPDATE', DAT_DIR . 'update.dat');
define('CTRL_TIME_FILE', DAT_DIR . 'ctrltime.dat'); //定期管理のための時間記憶ファイル
define('RANKING', DAT_DIR . 'ranking'.BASE_EXT);
define('BBS_BOTTOM', DAT_DIR . 'bbs.dat');
define('BBS_TOWN', DAT_DIR . 'bbs_town.dat');
define('MANAGE_LOG_FILE', DAT_DIR . 'managed.dat'); //定期管理記録ファイル

define('BASE_PATH_USER', DAT_DIR . 'user/');
define('BASE_PATH_UNION', DAT_DIR . 'union/');

define('BASE_PATH_LOG', BASE_PATH_CACHE . 'log/');

define('LOG_BATTLE_NORMAL', BASE_PATH_LOG . 'normal/');
define('LOG_BATTLE_RANK', BASE_PATH_LOG . 'rank/');
define('LOG_BATTLE_UNION', BASE_PATH_LOG . 'union/');

// 状態定義
/*
define('FRONT', 'front');
define('BACK', 'back');
*/



?>