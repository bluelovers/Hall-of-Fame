<?php

/**
 * PHPUnit Bootstrap for HOF
 *
 * Loads the main HOF bootstrap with correct paths
 * and sets up the test environment for PHPUnit.
 *
 * @author Shadow Monarch
 * @copyright 2026
 */

// Define HOF trust_path explicitly (absolute path to trust_path/)
define('PHPUNIT_TRUST_PATH', realpath(dirname(__file__) . '/..'));

// Set working directory to trust_path/ so relative paths in bootstrap work
chdir(PHPUNIT_TRUST_PATH);

// Include main bootstrap (at trust_path/bootstrap.php)
require_once PHPUNIT_TRUST_PATH . '/bootstrap.php';

// Additional test setup
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * Helper function for test output
 */
function _debug($var, $label = null)
{
    if ($label) echo "\n=== $label ===\n";
    var_dump($var);
    echo "\n";
}
