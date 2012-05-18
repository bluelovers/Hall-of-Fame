<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

$no = '0000';

//debug(HOF_Model_Char::getUnionDataBase($no));

//debug(HOF_Model_Char::getUnionDataMon($no));

//debug(HOF_Model_Char::getUnionList(), HOF_Model_Char::getUnionData($no));

$u = HOF_Model_Char::newUnion($no);

$u->HP = 500;

$u->SaveCharData();

debug($u);
