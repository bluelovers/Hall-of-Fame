<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

/*
$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON.HOF_Class_Char::TYPE_SUMMON, '1000');

$c2 = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON.HOF_Class_Char::TYPE_SUMMON, '1000', array('strength' => 2));

//debug($char->isUnion(), $char->isMon(), $char->isSummon(), $char);
debug($char->options(), $char->isSummon());

debug($c2->options(), $c2->isSummon());
*/

$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, '912e83a06af3684f96e8587ca5d128ea', null, 'admin');

debug($char->icon(), $char->data, $char->birth, $char->uniqid());

$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, 'char:100', null, 'admin');

debug($char->icon(), $char->data, $char->birth, $char->uniqid());

$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, 'mon:1000', array('job' => 100), 'admin');

debug($char->icon(), $char->data, $char->birth, $char->uniqid());

$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, 'mon:1012', array('job' => 100), 'admin');

debug($char->icon(), $char->data, $char->birth, $char->uniqid());

$char = $char->getClone();

debug($char->icon(), $char->data, $char->birth, $char->uniqid());