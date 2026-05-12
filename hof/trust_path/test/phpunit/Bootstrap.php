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

/**
 * 使用 dirname(__FILE__) 定位 trust_path/test/bootstrap.php
 * Uses dirname(__FILE__) to locate trust_path/test/bootstrap.php
 *
 * 此為 PHP 5.2 相容寫法（不可使用 __DIR__）。
 * bootstrap-core.php 會透過 test/bootstrap.php 的載入鏈自動載入，
 * 提供所有 PROJECT_* 路徑常數。
 *
 * This is the PHP 5.2-compatible approach (__DIR__ not available).
 * bootstrap-core.php is loaded through the test/bootstrap.php chain,
 * providing all PROJECT_* path constants.
 *
 * @see hof/trust_path/bootstrap-core.php
 * @see hof/trust_path/test/bootstrap.php
 */
require_once dirname(__FILE__) . '/../bootstrap.php';

/**
 * 至此 bootstrap-core.php 已載入，PROJECT_TEST_PATH / PROJECT_TEST_LOG_PATH 等常數已可用
 * At this point bootstrap-core.php is loaded; PROJECT_TEST_PATH, PROJECT_TEST_LOG_PATH etc. are available
 */

// Additional test setup - 顯示所有錯誤以獲得最完整的除錯資訊
// Show all errors for complete debugging information
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// 確保日誌目錄存在
// Ensure log directory exists
/**
 * 使用 bootstrap-core.php 定義的 PROJECT_TEST_LOG_PATH
 * Uses PROJECT_TEST_LOG_PATH defined in bootstrap-core.php
 *
 * @see hof/trust_path/bootstrap-core.php
 */
$logDir = PROJECT_TEST_LOG_PATH;
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// 強制 PHP 錯誤日誌記錄
// Force PHP error logging
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/php_errors.log');

// 設定錯誤處理器以捕獲所有錯誤
// Set error handler to catch all errors
// set_error_handler(function($severity, $message, $file, $line) {
//     // 只在測試環境中顯示詳細錯誤
//     // Only show detailed errors in test environment
//     echo "\n=== PHP ERROR ===\n";
//     echo "Severity: " . $severity . "\n";
//     echo "Message: " . $message . "\n";
//     echo "File: " . $file . ":" . $line . "\n";
//     echo "==================\n";

//     // 直接寫入日誌檔案
//     // Write directly to log file
//     $logFile = PROJECT_TEST_LOG_PATH . '/php_errors.log';
//     $logMessage = date('[Y-m-d H:i:s]') . " [$severity] $message in $file:$line\n";
//     file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

//     // 返回 false 讓 PHP 繼續處理錯誤
//     // Return false to let PHP continue error processing
//     return false;
// }, E_ALL);

// 設定例外處理器
// Set exception handler
// set_exception_handler(function($exception) {
//     echo "\n=== UNCAUGHT EXCEPTION ===\n";
//     echo "Type: " . get_class($exception) . "\n";
//     echo "Message: " . $exception->getMessage() . "\n";
//     echo "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
//     echo "Trace:\n" . $exception->getTraceAsString() . "\n";
//     echo "===========================\n";

//     // 寫入日誌
//     // Write to log
//     error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine());
// });

/**
 * Helper function for test output
 */
function _debug($var, $label = null)
{
    if ($label) echo "\n=== $label ===\n";
    var_dump($var);
    echo "\n";
}
