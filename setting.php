<?php
// game setting
define("TITLE","Hall of Fame");//�����ȥ�
define("MAX_TIME",1000);//����Time
define("TIME_GAIN_DAY",6000);//1���˲���������Time
define("MAX_CHAR",10);//������������
define("MAX_USERS",500);//������Ͽ�ԿͿ�
define("ABANDONED",60*60*24*14);//��������������줿�Ȥߤʤ�����
define("CONTROL_PERIOD",60*60*12);//��������μ���
define("RECORD_IP",1);//IP��Ͽ���뤫��(0=NO 1=YES)

// other
define("DEBUG",0);// 0=OFF
define("CHAR_NO_IMAGE","NoImage.gif");// �����������ʤ����ɽ����������
define("SESSION_SWITCH",1);// 0=OFF
define("CHAR_ROW",5);// 1���̤Υ��������
define("CRYPT_KEY",'$1$12345678$');//�ѥ���沽����(���������ָ���Ѥ����)
define("COOKIE_EXPIRE",60*60*24*3);//60*60*24*3
define("UP_PASS","password");// ��������Τߤ������Ѥ���ʤ�

define("START_TIME",900);//�����೫�ϻ��˻��äƤ�Time
define("START_MONEY",50000);//��������
define("MAX_STATUS",250);//���ơ�����������
define("GET_STATUS_POINT",5);//LVUP�����������륹�ƥݤο���
define("GET_SKILL_POINT",2);//LVUP�����������뵻�ݤο���
define("MAX_LEVEL",50);//�����٥�
define("SELLING_PRICE",1/5);//���ͤ����ꤵ��Ƥ��ʤ������ƥ�����͢�(����*SELLING_PRICE)
define("REFINE_LIMIT",10);//��ϣ�³���

define("EXP_RATE",1);//�и��ͤ�館����Ψ
define("MONEY_RATE",1);//�����館����Ψ

define("NEW_NAME_COST",300000);//������̾�����ѹ�����Τ�ɬ�פʤ���
define("BBS_OUT","");//����BBS������Ф��Υ��ɥ쥹��̵����ж���""
define("BBS_BOTTOM_TOGGLE",0);// ���ˤ��ä���ԷǼ���(0=OFF)
define("AUCTION_TOGGLE",0);// ������������ǽ�����뤫(0=OFF 1=ON)
define("AUCTION_EXHIBIT_TOGGLE",0);// �����������ν��ʤ��ǽ�ˤ��뤫(0=OFF 1=ON)
define("JUDGE_LIST_AUTO_LOAD",0);//�ѥ�����Ƚ�ǤΥꥹ�Ȥ� 1=��ư 0=��ư�ɲ�(�㴳�ڤ�)
define("AUCTION_MAX",100);//�����������Ʊ���˽��ʤǤ����ʿ���

// ranking
define("RANK_TEAM_SET_TIME",60*60*48);//��󥭥󥰤Υ���������Ǥ������
define("RANK_BATTLE_NEXT_LOSE",60*60*24);//��󥭥��� �餱���Ȥ����廊��ޤ�
define("RANK_BATTLE_NEXT_WIN",60*1);//��󥭥��� ���ä��Ȥ����廊��ޤ�

// battle setting
define("NORMAL_BATTLE_TIME",100);//�̾�Υ�󥹥����Ȥ���Ʈ�Ǿ��񤹤����
define("ENEMY_INCREASE",0);//Ũ������(������)
define("BATTLE_MAX_TURNS",100);//��Ʈ�κ����ư���(��Ʈ�����Ͱʾ�Ĺ�����Ƚ�λ������)
define("TURN_EXTENDS",20);// ���夬�Ĥ������ʾ���Ĺ���륿�������
define("BATTLE_MAX_EXTENDS",100);//��Ĺ�������κ����ư���(��Ĺ�θ³�)
define("BTL_IMG_TYPE",2);// (0=GD 1=CSS 2=ȿž�Ѳ�������CSS)
define("BTL_IMG","./image.php");// GDɽ��
define("BATTLE_STAT_TURNS",10);// ��Ʈ�ξ�����ɽ������ֳ�
define("DEAD_IMG","mon_145.gif");// HP=0 �Υ����β���
define("MAX_BATTLE_LOG",100);// ��Ʈ������¸������
define("MAX_BATTLE_LOG_UNION",100);// ��Ʈ������¸������
define("MAX_BATTLE_LOG_RANK",100);// ��Ʈ������¸������
define("MAX_STATUS_MAXIMUM",2500);// �����x��(%) ��Ʈ���ǽ�Ͼ徺�Ǿ夬����ͤθ³���(1000%=10�ܤ��³�)

define("DELAY_TYPE",1);// 0=�� 1=��
// DELAY_TYPE=0
define("DELAY",2.5);//�ǥ��쥤(2�ʾ夬�ܰ¡����ͤ��㤤��SPD���⤤�ͤ�ͭ��)
// DELAY_TYPE=1
define("DELAY_BASE",5);// ���ͤ��⤤�Ⱥ����Ĥ��ʤ��ʤ롣

// union
define("UNION_BATTLE_TIME",10);//��˥�����Ǿ��񤹤����
define("UNION_BATTLE_NEXT",60*20);//Union������Ʈ�ޤǤδֳ�

// files
define("INDEX","index.php");

// CLASS FILE
define("CLASS_DIR", "./class/");
define("BTL_IMG_CSS", CLASS_DIR."class.css_btl_image.php");// CSSɽ��
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
define("AUCTION_ITEM","./auction.dat");//�����ƥ४����������ѤΥե�����
define("AUCTION_ITEM_LOG","./auction_log.dat");//�����ƥ४����������ѤΥ��ե�����

define("REGISTER","./register.dat");
define("UPDATE","./update.dat");
define("CTRL_TIME_FILE","./ctrltime.dat");//��������Τ���λ��ֵ����ե�����
define("RANKING","./ranking.dat");
define("BBS_BOTTOM","./bbs.dat");
define("BBS_TOWN","./bbs_town.dat");
define("MANAGE_LOG_FILE","./managed.dat");//���������Ͽ�ե�����
define("USER_NAME","./username.dat");//̾����¸�ե�����

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

// �������
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