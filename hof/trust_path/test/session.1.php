<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

debug(__LINE__, $session, $_SESSION);

//error_reporting(E_ALL);

debug(__LINE__, class_exists('HOF_Class_Session_User'));

$session = new HOF_Class_Session_User();

debug(__LINE__, $session, $_SESSION);

$session->id(123);

debug(__LINE__, $session, $_SESSION);

$session->session_update();

debug(__LINE__, $session, $_SESSION);

$session->id(0);

debug(__LINE__, $session, $_SESSION);

$session->session_decode();

debug(__LINE__, $session, $_SESSION);