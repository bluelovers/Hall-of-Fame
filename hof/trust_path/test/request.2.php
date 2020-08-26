<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$_POST['c'] = array(
1,2,3,

);

$request = new HOF_Class_Request_Query('_POST', $_POST);

debug((string)$request, $request);
