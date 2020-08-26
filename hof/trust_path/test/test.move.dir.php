<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

//$dir = BASE_PATH_USER . '../user_del/';
//
//HOF_Class_File::mkdir($dir);

HOF_Class_File::rmdir(BASE_PATH_USER.'test5', true);
