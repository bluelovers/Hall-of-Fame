<?php
// game setting
define("TITLE","Hall of Fame");//タイトル
define("MAX_TIME",1000);//最大Time
define("TIME_GAIN_DAY",6000);//1日に回復する総Time
define("MAX_CHAR",10);//最大所持キャラ数
define("MAX_USERS",500);//最大登録者人数
define("ABANDONED",60*60*24*14);//ゲームを放棄されたとみなす期間
define("CONTROL_PERIOD",60*60*12);//定期管理の周期
define("RECORD_IP",1);//IPを記録するか？(0=NO 1=YES)

// other
define("DEBUG",0);// 0=OFF
define("CHAR_NO_IMAGE","NoImage.gif");// キャラ画像がない場合表示される画像
define("SESSION_SWITCH",1);// 0=OFF
define("CHAR_ROW",5);// 1画面のキャラの列数
define("CRYPT_KEY",'$1$12345678$');//パス符号化キー(ゲーム設置後は変えるな)
define("COOKIE_EXPIRE",60*60*24*3);//60*60*24*3
define("UP_PASS","password");// 更新情報のみしか使用されない

define("START_TIME",900);//ゲーム開始時に持ってるTime
define("START_MONEY",50000);//初期所持金
define("MAX_STATUS",250);//ステータス最大値
define("GET_STATUS_POINT",5);//LVUPした時に得るステポの数値
define("GET_SKILL_POINT",2);//LVUPした時に得る技ポの数値
define("MAX_LEVEL",50);//最大レベル
define("SELLING_PRICE",1/5);//売値が設定されていないアイテムの売値→(買値*SELLING_PRICE)
define("REFINE_LIMIT",10);//精錬限界値

define("EXP_RATE",1);//経験値もらえる倍率
define("MONEY_RATE",1);//お金もらえる倍率

define("NEW_NAME_COST",300000);//新しい名前に変更するのに必要なお金
define("BBS_OUT","");//外部BBSがあればそのアドレス、無ければ空欄→""
define("BBS_BOTTOM_TOGGLE",0);// 下にあった一行掲示板(0=OFF)
define("AUCTION_TOGGLE",0);// オークションを機能させるか(0=OFF 1=ON)
define("AUCTION_EXHIBIT_TOGGLE",0);// オークションの出品を可能にするか(0=OFF 1=ON)
define("JUDGE_LIST_AUTO_LOAD",0);//パターン判断のリストを 1=自動 0=手動追加(若干軽い)
define("AUCTION_MAX",100);//オークション同時に出品できる品数。

// ranking
define("RANK_TEAM_SET_TIME",60*60*48);//ランキングのチーム設定できる周期
define("RANK_BATTLE_NEXT_LOSE",60*60*24);//ランキング戦 負けたとき次戦えるまで
define("RANK_BATTLE_NEXT_WIN",60*1);//ランキング戦 勝ったとき次戦えるまで

// battle setting
define("NORMAL_BATTLE_TIME",100);//通常のモンスターとの戦闘で消費する時間
define("ENEMY_INCREASE",0);//敵の増員(ランダム)
define("BATTLE_MAX_TURNS",100);//戦闘の最大行動回数(戦闘が数値以上長引くと終了させる)
define("TURN_EXTENDS",20);// 決着がつきそうな場合延長するターン数。
define("BATTLE_MAX_EXTENDS",100);//延長した場合の最大行動回数(延長の限界)
define("BTL_IMG_TYPE",2);// (0=GD 1=CSS 2=反転済画像使用CSS)
define("BTL_IMG","./image.php");// GD表示
define("BATTLE_STAT_TURNS",10);// 戦闘の状況を表示する間隔
define("DEAD_IMG","mon_145.gif");// HP=0 のキャラの画像
define("MAX_BATTLE_LOG",100);// 戦闘ログを保存する件数
define("MAX_BATTLE_LOG_UNION",100);// 戦闘ログを保存する件数
define("MAX_BATTLE_LOG_RANK",100);// 戦闘ログを保存する件数
define("MAX_STATUS_MAXIMUM",2500);// 初期値x値(%) 戦闘中の能力上昇で上がる数値の限界値(1000%=10倍が限界)

define("DELAY_TYPE",1);// 0=旧 1=新
// DELAY_TYPE=0
define("DELAY",2.5);//ディレイ(2以上が目安。数値が低いとSPDが高い人は有利)
// DELAY_TYPE=1
define("DELAY_BASE",5);// 数値が高いと差がつかなくなる。

// union
define("UNION_BATTLE_TIME",10);//ユニオン戦で消費する時間
define("UNION_BATTLE_NEXT",60*20);//Union次の戦闘までの間隔

// files
define("INDEX","index.php");

// CLASS FILE
define("CLASS_DIR", "./class/");
define("BTL_IMG_CSS", CLASS_DIR."class.css_btl_image.php");// CSS表示
define("CLASS_MAIN", CLASS_DIR."class.main.php");
define("CLASS_USER", CLASS_DIR."class.user.php");
define("CLASS_CHAR", CLASS_DIR."class.char.php");
define("CLASS_MONSTER", CLASS_DIR."class.monster.php");
define("CLASS_UNION", CLASS_DIR."class.union.php");
define("CLASS_BATTLE", CLASS_DIR."class.battle.php");
define("CLASS_SKILL_EFFECT", CLASS_DIR."class.skill_effect.php");
define("CLASS_RANKING", CLASS_DIR."class.rank2.php");
define("CLASS_JS_ITEMLIST", CLASS_DIR."class.JS_itemlist.php");
define("CLASS_SMITHY", CLASS_DIR."class.smithy.php");
define("CLASS_AUCTION", CLASS_DIR."class.auction.php");
define("GLOBAL_PHP", CLASS_DIR."global.php");
define("COLOR_FILE", CLASS_DIR."Color.dat");

// DATA FILE
define("DATA_DIR", "./data/");
define("DATA_BASE_CHAR", DATA_DIR."data.base_char.php");
define("DATA_JOB", DATA_DIR."data.job.php");
define("DATA_ITEM", DATA_DIR."data.item.php");
define("DATA_ENCHANT", DATA_DIR."data.enchant.php");
define("DATA_SKILL", DATA_DIR."data.skill.php");
define("DATA_SKILL_TREE", DATA_DIR."data.skilltree.php");
define("DATA_JUDGE_SETUP", DATA_DIR."data.judge_setup.php");
define("DATA_JUDGE", DATA_DIR."data.judge.php");
define("DATA_MONSTER", DATA_DIR."data.monster.php");
define("DATA_LAND", DATA_DIR."data.land_info.php");
define("DATA_LAND_APPEAR", DATA_DIR."data.land_appear.php");
define("DATA_CLASSCHANGE", DATA_DIR."data.classchange.php");
define("DATA_CREATE", DATA_DIR."data.create.php");
define("DATA_TOWN", DATA_DIR."data.town_appear.php");

define("MANUAL", DATA_DIR."data.manual0.php");
define("MANUAL_HIGH", DATA_DIR."data.manual1.php");

define("GAME_DATA_JOB", DATA_DIR."data.gd_job.php");
define("GAME_DATA_ITEM", DATA_DIR."data.gd_item.php");
define("GAME_DATA_JUDGE", DATA_DIR."data.gd_judge.php");
define("GAME_DATA_MONSTER", DATA_DIR."data.gd_monster.php");

define("TUTORIAL", DATA_DIR."data.tutorial.php");
// DAT
define("AUCTION_ITEM","./auction.dat");//アイテムオークション用のファイル
define("AUCTION_ITEM_LOG","./auction_log.dat");//アイテムオークション用のログファイル

define("REGISTER","./register.dat");
define("UPDATE","./update.dat");
define("CTRL_TIME_FILE","./ctrltime.dat");//定期管理のための時間記憶ファイル
define("RANKING","./ranking.dat");
define("BBS_BOTTOM","./bbs.dat");
define("BBS_TOWN","./bbs_town.dat");
define("MANAGE_LOG_FILE","./managed.dat");//定期管理記録ファイル
define("USER_NAME","./username.dat");//名前保存ファイル

// dir
define("IMG_CHAR","./image/char/");
define("IMG_CHAR_REV","./image/char_rev/");
define("IMG_ICON","./image/icon/");
define("IMG_OTHER","./image/other/");
define("USER","./user/");
define("UNION","./union/");
define("DATA","data.dat");
define("ITEM","item.dat");

define("LOG_BATTLE_NORMAL","./log/normal/");
define("LOG_BATTLE_RANK","./log/rank/");
define("LOG_BATTLE_UNION","./log/union/");

// 状態定義
define("FRONT","front");
define("BACK","back");

define("TEAM_0",0);
define("TEAM_1",1);
define("WIN",0);
define("LOSE",1);
define("DRAW","d");

define("ALIVE",0);
define("DEAD",1);
define("POISON",2);

define("CHARGE",0);
define("CAST",1);

?>