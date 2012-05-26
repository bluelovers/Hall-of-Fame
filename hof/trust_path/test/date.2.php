<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$day1 = new HOF_Class_Date('2012-02-28');
$day2 = new HOF_Class_Date('2012-03-01');

$day1->setLocale('zh_TW');
$day2->setLocale('zh_TW');

$day1->setTimezone('Asia/Taipei');
$day2->setTimezone('Asia/Taipei');

/*
$day1->setOptions(array('extend_month' => true));
$day2->setOptions(array('extend_month' => true));
*/

_e($day1);
_e('getHour', (int)$day1->getHour());
_e($day2);

_e($day2->sub($day1)->toValue());
_e($day2->toString(Zend_Date::DAY, 'iso'));

$mydate = new Zend_Date(array(
	'second' => 50,
	));

_e($day1->add($mydate));

$i = new HOF_Class_Date_DateInterval(7210);

$map = array('y', 'm', 'd', 'h', 'i', 's', 'invert');

foreach($map as $k)
{
	_e($k, $i->$k);
}

_e($i);