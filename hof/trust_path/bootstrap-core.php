<?php

/**
 * ============================================================
 * HOF 核心啟動設定 — 單一事實來源
 * HOF Core Bootstrap Configuration — Single Source of Truth
 * ============================================================
 *
 * 本檔案定義全專案共用的：
 * - 路徑常數（PATH constants）
 * - 共用工具函數（_set_production_error_reporting）
 *
 * 無論是生產啟動（bootstrap.php）或測試啟動（Bootstrap.php），
 * 都應引用此檔案作為唯一的事實來源，避免 PATH 定義散落各處。
 *
 * This file defines the project-wide shared:
 * - PATH constants
 * - Shared utility functions (_set_production_error_reporting)
 *
 * Both production bootstrap (bootstrap.php) and test bootstrap
 * (Bootstrap.php) should reference this file as the single source
 * of truth, avoiding path definitions scattered across files.
 *
 * @see hof/trust_path/bootstrap.php
 * @see hof/trust_path/test/phpunit/Bootstrap.php
 * @see hof/trust_path/test/lib/test_helper.php
 */

/**
 * trust_path/ 的絕對路徑
 * Absolute path to trust_path/
 *
 * 以本檔案所在目錄為基準，所有專案內部路徑皆以此為基礎進行計算。
 * Base path for all project-internal path calculations.
 *
 * @example D:/Users/.../hof/trust_path
 */
define('PROJECT_TRUST_PATH', realpath(dirname(__FILE__)));

/**
 * 專案根目錄的絕對路徑
 * Absolute path to the project root directory
 *
 * trust_path/ 的上層 = hof/（Web 根目錄）
 * Parent of trust_path/ = hof/ (Web document root)
 *
 * @example D:/Users/.../hof
 */
define('PROJECT_ROOT_PATH', realpath(PROJECT_TRUST_PATH . '/..'));

/**
 * 靜態資源目錄的絕對路徑
 * Absolute path to the static resources directory
 *
 * @example D:/Users/.../hof/static
 */
define('PROJECT_STATIC_PATH', realpath(PROJECT_ROOT_PATH . '/static'));

/**
 * 靜態圖片資源目錄的絕對路徑
 * Absolute path to the static image directory
 *
 * Resource YAML 中的 img 欄位值應對應於此目錄下的檔案。
 *
 * @example D:/Users/.../hof/static/image
 */
define('PROJECT_STATIC_IMAGE_PATH', PROJECT_STATIC_PATH . '/image');

/**
 * 測試目錄的絕對路徑
 * Absolute path to the test directory
 *
 * @example D:/Users/.../hof/trust_path/test
 */
define('PROJECT_TEST_PATH', realpath(PROJECT_TRUST_PATH . '/test'));

/**
 * 測試涵蓋率報告日誌目錄的絕對路徑
 * Absolute path to the test coverage report log directory
 *
 * @note 不使用 realpath()，因為此目錄可能尚未建立
 *       realpath() 僅用於已存在的路徑，否則回傳 false
 *       We avoid realpath() here because this directory may not exist yet.
 *       realpath() only works for existing paths; otherwise it returns false.
 *
 * @example D:/Users/.../hof/trust_path/test/coverage-report/log
 */
define('PROJECT_TEST_LOG_PATH', PROJECT_TEST_PATH . '/coverage-report/log');

/**
 * 測試固定資料目錄的絕對路徑
 * Absolute path to the test fixture directory
 *
 * @example D:/Users/.../hof/trust_path/test/fixture
 */
define('PROJECT_TEST_FIXTURE_PATH', realpath(PROJECT_TEST_PATH . '/fixture'));

/**
 * 開發工具目錄的絕對路徑
 * Absolute path to the development tools (bin) directory
 *
 * @example D:/Users/.../hof/trust_path/bin
 */
define('PROJECT_BIN_PATH', realpath(PROJECT_TRUST_PATH . '/bin'));

/**
 * 伺服器遊戲資料目錄的絕對路徑
 * Absolute path to the game data directory
 *
 * 如果刪除 遊戲就會被重置
 *
 * @example D:/Users/.../hof/trust_path/dat
 *
 * equals DAT_DIR
 */
define('PROJECT_GAME_DATA_PATH', realpath(PROJECT_TRUST_PATH . '/dat'));

define('PROJECT_GAME_DATA_RESOURCE_PATH', realpath(PROJECT_TRUST_PATH . '/HOF/Resource'));

/**
 * 設定 error_reporting 比照 production 環境
 * Set error_reporting to match production environment
*
 * 定義於此處（bootstrap-core.php）而非各別測試檔案，
 * 確保生產啟動與測試啟動使用相同的 error_reporting 設定，
 * 遵循單一事實來源原則。
 *
 * PHPUnit Bootstrap 設定 error_reporting(E_ALL | E_STRICT)
 * 且 phpunit.xml 設定 convertNoticesToExceptions="true"，
 * 但此專案為 PHP 5.2~5.6 legacy 程式碼，存在大量 harmless 的
 * E_NOTICE / E_STRICT（如直接存取未定義陣列索引、非靜態方法靜態呼叫）。
 *
 * 整合測試依賴完整的 legacy 初始化流程（類別建構子、Autoloader、YAML 載入等），
 * 在修正所有底層 notice 之前，需比照 production 的錯誤報告層級來執行測試。
 *
 * Listed here (boostrap-core.php) instead of individual test files
 * to ensure both production and test bootstrap use the same
 * error_reporting level, following the single source of truth.
 *
 * PHPUnit Bootstrap sets error_reporting(E_ALL | E_STRICT)
 * and phpunit.xml sets convertNoticesToExceptions="true",
 * but this is a PHP 5.2~5.6 legacy codebase with many harmless
 * E_NOTICE / E_STRICT (undefined array index access, non-static method static calls).
 *
 * Integration tests depend on the full legacy initialization flow
 * (class constructors, Autoloader, YAML loading, etc.).
 * Until all underlying notices are fixed, tests must run at
 * production error_reporting level.
 */
function _set_production_error_reporting()
{
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}
