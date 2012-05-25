<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$locale = new Zend_Locale('ja');

Zend_Registry::set('Zend_Locale', $locale);

$date1 = new HOF_Class_Date();

$date2 = new HOF_Class_Date(time() + 3600);

$date2->setOptions(array('format_type' => 'iso'));

_e($date2->sub($date1)->toString(Zend_Date::TIME_FULL));


debug(Zend_Locale_Data::getContent('ja', 'day', 'sun'));
debug(f(Zend_Locale::getTranslation(array('hour', 'other', 1), 'unit'), 1));
debug(Zend_Locale_Data::getList('ja', 'DateInterval'));

//debug(Zend_Locale::getTranslationList('unit', $locale, 'day'));

_e((string )$date1, (string )$date2);

/*
var_dump($date ->getLocale());

_e($date->toString());

_e($date->toValue());

$date->setTimezone('Asia/Tokyo');

$date->setLocale('ja');

_e($date->toString('Y-m-d D H:i:s'));

_e($date->toValue());
*/
