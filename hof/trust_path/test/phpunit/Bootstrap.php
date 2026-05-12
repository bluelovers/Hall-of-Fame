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

// Additional test setup - 顯示所有錯誤以獲得最完整的除錯資訊
// Show all errors for complete debugging information
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// 確保日誌目錄存在
// Ensure log directory exists
$logDir = PHPUNIT_TRUST_PATH . '/test/coverage-report/log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// 強制 PHP 錯誤日誌記錄
// Force PHP error logging
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/php_errors.log');

// 設定錯誤處理器以捕獲所有錯誤
// Set error handler to catch all errors
set_error_handler(function($severity, $message, $file, $line) {
    // 只在測試環境中顯示詳細錯誤
    // Only show detailed errors in test environment
    echo "\n=== PHP ERROR ===\n";
    echo "Severity: " . $severity . "\n";
    echo "Message: " . $message . "\n";
    echo "File: " . $file . "\n";
    echo "Line: " . $line . "\n";
    echo "==================\n";

    // 直接寫入日誌檔案
    // Write directly to log file
    $logFile = PHPUNIT_TRUST_PATH . '/test/coverage-report/log/php_errors.log';
    $logMessage = date('[Y-m-d H:i:s]') . " [$severity] $message in $file:$line\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

    // 返回 false 讓 PHP 繼續處理錯誤
    // Return false to let PHP continue error processing
    return false;
}, E_ALL);

// 設定例外處理器
// Set exception handler
set_exception_handler(function($exception) {
    echo "\n=== UNCAUGHT EXCEPTION ===\n";
    echo "Type: " . get_class($exception) . "\n";
    echo "Message: " . $exception->getMessage() . "\n";
    echo "File: " . $exception->getFile() . "\n";
    echo "Line: " . $exception->getLine() . "\n";
    echo "Trace:\n" . $exception->getTraceAsString() . "\n";
    echo "===========================\n";

    // 寫入日誌
    // Write to log
    error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
});

/**
 * Helper function for test output
 */
function _debug($var, $label = null)
{
    if ($label) echo "\n=== $label ===\n";
    var_dump($var);
    echo "\n";
}
