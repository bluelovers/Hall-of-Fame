<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

debug(bcdiv('9', '3'));

$auth = "24\tLewis Carroll";
$n = sscanf($auth, "%d\t%s %s", $id, $first, $last);
$m = sscanf($auth, "%d\t%s %s");

debug($n, $id, $first, $last);
debug($m);
