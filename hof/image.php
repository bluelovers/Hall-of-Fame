<?php

set_include_path(get_include_path() . PATH_SEPARATOR . realpath( './trust_path/includes/' ));

require ("trust_path/bootstrap.php");

HOF::router('image');

