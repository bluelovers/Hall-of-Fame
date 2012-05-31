<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$team = new HOF_Class_Battle_Team();
$ta = $team->getIterator();
$team->team_name('A');

$team->team_idx = 0;

$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1000);
$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1001);
$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1002);

//var_dump($team);
//var_dump($ta);

$t2 = $team->getClone(1);

//var_dump($t2);
//var_dump($t2->getIterator());

debug($t2->team_idx(), (string)$t2, (int)$t2);

$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1003);
$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1003);
$team[] = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1004);

$team->pushNameList();
$t2->pushNameList();

$t2->array_splice(count($t2) / 2, 0, array(HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1005)));
$t2->insert(count($t2) / 2, HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, 1005));

$team->fixCharName();
$t2->fixCharName();
//$t2->fixCharName();

foreach ($team as $char)
{
	_e($char->Name());
}

_e('----------------');

debug(count($t2), count($t2) / 2);

foreach ($t2 as $char)
{
	_e($char->Name());
}

$char->STATE = STATE_DEAD;

debug(count($t2->filterState(STATE_DEAD)), $t2->CountAlive());

debug(HOF_Class_Battle_Team::$cache);