<?php

error_reporting( -1 );
ini_set( 'display_startup_errors', 1 );
ini_set( 'display_errors', 1 );


set_include_path(get_include_path() . PATH_SEPARATOR . realpath( './trust_path/includes/' ));

require ("trust_path/bootstrap.php");

HOF::router();

