<?php

$funcs = array(
	'date_default_timezone_set',
	'date_default_timezone_get',
);

$ret['safe_mode'] = ini_get('safe_mode');
$ret['disable_functions'] = explode(',', ini_get('disable_functions'));

$ret['date_default_timezone_get'] = date_default_timezone_get();
$ret['date_default_timezone_set'] = date_default_timezone_set('Asia/Taipei');
$ret['date_default_timezone_get2'] = date_default_timezone_get();

echo '<pre>';
print_r($ret);
echo '</pre>';
