<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$cache = new HOF_Class_File_Cache();

$cache->load('test');

debug($cache, $cache->fp, $cache->data);
