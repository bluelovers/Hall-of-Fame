<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once ("./bootstrap.php");

//$v['land'] = HOF_Class_Icon::getImageList(HOF_Class_Icon::IMG_LAND);

$v[] = HOF_Class_Icon::getImage('bg_swamp2', HOF_Class_Icon::IMG_LAND);
$v[] = HOF_Class_Icon::getImage('land_swamp2', HOF_Class_Icon::IMG_LAND);

$v[] = HOF_Class_Icon::getImage('we_sword026', HOF_Class_Icon::IMG_ITEM);

debug($v);