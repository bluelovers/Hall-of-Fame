<?php
// 游戏设置
define("TITLE","荣誉圣殿中文版 （Hall of Fame）");//网页标题
define("MAX_TIME",100);//最大体力
define("TIME_GAIN_DAY",500);//每日所获得的体力
define("MAX_CHAR",5);//最大角色数量
define("MAX_USERS",500);//最大用户数量
define("ABANDONED",60*60*24*14);//删除用户周期
define("CONTROL_PERIOD",60*60*12);//自动管理周期
define("RECORD_IP",1);//IP记录(0=NO 1=YES)

// 其他设置
define("DEBUG",0);// 0=OFF
define("CHAR_NO_IMAGE","NoImage.gif");// 无角色图片
define("SESSION_SWITCH",1);// 0=OFF
define("CHAR_ROW",5);//角色队列数
define("CRYPT_KEY",'$1$12345678$');//パス射规步キ〖(ゲ〖ム肋弥稿は恃えるな)
define("COOKIE_EXPIRE",60*60*24*3);//cookie时间 60*60*24*3
define("UP_PASS","password");// 公告管理密码

define("START_TIME",100);//游戏初始体力
define("START_MONEY",10000);//游戏初始资金
define("MAX_STATUS",255);//最大属性点
define("GET_STATUS_POINT",3);//升级获得属性点
define("GET_SKILL_POINT",1);//升级获得技能点
define("MAX_LEVEL",50);//最大等级
define("SELLING_PRICE",1/5);//卖出物品比率（物品原价x比率）
define("REFINE_LIMIT",10);//篮希嘎肠猛

define("EXP_RATE",1);//经验倍数
define("MONEY_RATE",1);//金钱倍数

define("NEW_NAME_COST",100000);//改变队伍名称所需资金
define("BBS_OUT","http://localhost/bbs/");//论坛链接地址
define("BBS_BOTTOM_TOGGLE",0);//底部论坛链接按钮(0=OFF 1=ON)
define("AUCTION_TOGGLE",1);//是否开启拍卖会所(0=OFF 1=ON)
define("AUCTION_EXHIBIT_TOGGLE",0);////拍卖(0=暂停 1=开启)
define("JUDGE_LIST_AUTO_LOAD",1);//条件判定列表自动取得 1=自动 0=手动操作
define("AUCTION_MAX",100);//最大拍卖数

// 排名设置
define("RANK_TEAM_SET_TIME",60*60*48);//排名队伍設定周期
define("RANK_BATTLE_NEXT_LOSE",60*60*24);//失败后再挑战时间
define("RANK_BATTLE_NEXT_WIN",60*1);//赢得排名站再战的时间

// 对战设置
define("NORMAL_BATTLE_TIME",1);//默认战斗消耗体力
define("ENEMY_INCREASE",0);//对手选择(随机)
define("BATTLE_MAX_TURNS",100);//战斗最大回合数
define("TURN_EXTENDS",20);// 疯缅がつきそうな眷圭变墓するタ〖ン眶。
define("BATTLE_MAX_EXTENDS",100);//变墓した眷圭の呵络乖瓢搀眶(变墓の嘎肠)
define("BTL_IMG_TYPE",2);// (0=GD 1=CSS 2=瓤啪貉茶咙蝗脱CSS)
define("BTL_IMG","./image.php");// GD文件
define("BATTLE_STAT_TURNS",10);// 战斗统计回合
define("DEAD_IMG","mon_145.gif");// HP=0时的角色图片
define("MAX_BATTLE_LOG",100);// 战斗记录保存数(通常怪)
define("MAX_BATTLE_LOG_UNION",50);// 战斗记录保存数(BOSS)
define("MAX_BATTLE_LOG_RANK",50);// 战斗记录保存数(BOSS)
define("MAX_STATUS_MAXIMUM",2500);// 最大战斗回合数(1000%=10)

define("DELAY_TYPE",1);// 0=奠 1=糠
// DELAY_TYPE=0
define("DELAY",2.5);//ディレイ(2笆惧が誊奥。眶猛が你いとSPDが光い客は铜网)
// DELAY_TYPE=1
define("DELAY_BASE",5);// 眶猛が光いと汗がつかなくなる。

// union
define("UNION_BATTLE_TIME",10);//BOSS战消耗体力
define("UNION_BATTLE_NEXT",60*20);//BOSS战再挑战时间

// files
define("INDEX","index.php");

// CLASS FILE
define("CLASS_DIR", "./class/");
define("BTL_IMG_CSS", CLASS_DIR."class.css_btl_image.php");// CSS山绩
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
define("AUCTION_ITEM","./auction.dat");//アイテムオ〖クション脱のファイル
define("AUCTION_ITEM_LOG","./auction_log.dat");//アイテムオ〖クション脱のログファイル

define("REGISTER","./register.dat");
define("UPDATE","./update.dat");
define("CTRL_TIME_FILE","./ctrltime.dat");//年袋瓷妄のための箕粗淡脖ファイル
define("RANKING","./ranking.dat");
define("BBS_BOTTOM","./bbs.dat");
define("BBS_TOWN","./bbs_town.dat");
define("MANAGE_LOG_FILE","./managed.dat");//年袋瓷妄淡峡ファイル
define("USER_NAME","./username.dat");//叹涟瘦赂ファイル

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

// 觉轮年盗
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