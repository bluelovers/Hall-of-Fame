<?php

define('EXT_YAML', '.yml');
define('EXT_DAT', '.dat');

define('BASE_EXT', EXT_YAML);

define('GENDER_UNKNOW', 0);
define('GENDER_BOY', 1);
define('GENDER_GIRL', 2);

define('POSITION_FRONT', 'front');
define('POSITION_BACK', 'back');

define('STATE_ALIVE', 0);
define('STATE_DEAD', 1);
define('STATE_POISON', 2);

define('JOB_UNDEFINED', 0);

/**
 * キャラ画像がない場合表示される画像
 */
define('NO_IMAGE', 'noimage');

define('USER_DATA', 'data'.BASE_EXT);
define('USER_ITEM', 'item'.BASE_EXT);
define('USER_UUID', 'uuid'.EXT_DAT);

define('EXPECT_CHARGE', 0);
define('EXPECT_CAST', 1);

define('INPUT_CHECKBOX', 'checkbox');
define('INPUT_RADIO', 'radio');

define('TEAM_0', 0);
define('TEAM_1', 1);

define('BATTLE_WIN', 0);
define('BATTLE_LOSE', 1);
define('BATTLE_DRAW', 'd');
