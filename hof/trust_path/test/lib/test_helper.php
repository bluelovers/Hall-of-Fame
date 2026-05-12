<?php

/**
 * HOF 測試共用工具函數
 * HOF Test Shared Helper Functions
 *
 * 提供 setUp() 中常見重複操作的封裝，包括：
 * - CLI 環境下 HOF::$_testIp 的設定與清除
 * - 測試用臨時 YAML 檔案路徑產生
 *
 * Provides encapsulation of common setUp() operations:
 * - HOF::$_testIp setting and clearing for CLI environment
 * - Test temporary YAML file path generation
 *
 * ⚠️ 單一事實來源 (Single Source of Truth) 提醒：
 *   - `_set_production_error_reporting()` 定義於 `bootstrap-core.php`（生產啟動與測試共用）
 *   - `PROJECT_TRUST_PATH` 等 PATH 常數定義於 `bootstrap-core.php`
 *   請勿在此檔案或其他測試檔案中重新定義，應從 `bootstrap-core.php` 引用。
 *
 * @see hof/trust_path/bootstrap-core.php
 *
 * @author Shadow Monarch
 * @copyright 2026
 */

/**
 * [舊方案] 設定 $_SERVER 中的 IP 相關鍵，避免 CLI 環境下 HOF::ip() 觸發 Undefined index
 * [Legacy] Set $_SERVER IP-related keys to avoid HOF::ip() Undefined index in CLI environment
 *
 * ⚠️ 已不建議使用。請改用 _set_test_ip() / _clear_test_ip()，使用 HOF::$_testIp
 *    機制繞過 $_SERVER 存取，避免污染全域 $_SERVER 環境。
 *
 * 保留此函數供參考，但新測試應優先使用 HOF::$_testIp 方案。
 *
 * @see _set_test_ip()
 * @see _clear_test_ip()
 */
function _set_server_ip_for_cli()
{
    $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.100';
    $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.100';
    $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
}

/**
 * [新方案] 設定 HOF::$_testIp，讓 HOF::ip() 繞過 $_SERVER 存取
 * [New] Set HOF::$_testIp to bypass $_SERVER access in HOF::ip()
 *
 * 使用 HOF 類別的靜態屬性 $_testIp，當其值不為 null 時，
 * HOF::ip() 直接回傳該值，完全繞過 $_SERVER 陣列存取。
 * 避免 CLI 環境下 Undefined index E_NOTICE。
 *
 * Uses HOF class static property $_testIp. When non-null,
 * HOF::ip() returns it directly, completely bypassing
 * $_SERVER array access. Avoids Undefined index E_NOTICE in CLI.
 *
 * 選擇 192.168.1.100 的原因：
 * - 非 loopback（127.0.0.1 會被 FILTER_FLAG_NO_RES_RANGE 過濾）
 * - 非保留範圍（0.0.0.0 會被過濾）
 * - 通過 filter_var() + FILTER_VALIDATE_IP 檢驗
 *
 * @see HOF::ip()
 * @see _clear_test_ip()
 */
function _set_test_ip()
{
    HOF::$_testIp = '192.168.1.100';
}

/**
 * 清除 HOF::$_testIp，恢復 HOF::ip() 的正常 $_SERVER 檢查
 * Clear HOF::$_testIp, restore normal $_SERVER checking in HOF::ip()
 *
 * 應在 tearDown() 中呼叫，避免測試間互相影響。
 * Call in tearDown() to prevent cross-test contamination.
 *
 * @see _set_test_ip()
 */
function _clear_test_ip()
{
    HOF::$_testIp = null;
}

/**
 * 產生測試用臨時 YAML 檔案路徑（具唯一性）
 * Generate test temporary YAML file path (with unique ID)
 *
 * @param string $prefix - 檔案字首 / File prefix (default: 'yaml_test')
 * @return string 完整的暫存 YAML 檔案路徑 / Full temp YAML file path
 */
function _temp_yaml_path($prefix = 'yaml_test')
{
    return sys_get_temp_dir() . '/' . $prefix . '_' . uniqid() . '.yml';
}

/**
 * 設定整合測試的共用環境（error_reporting + HOF::$_testIp）
 * Set up common integration test environment (error_reporting + HOF::$_testIp)
 *
 * 此為 _set_production_error_reporting() + _set_test_ip() 的快捷組合，
 * 適用於需要完整 legacy 初始化流程的測試。
 *
 * ⚠️ tearDown() 時必須手動呼叫 _clear_test_ip() 避免跨測試污染。
 *
 * This is a shortcut combining _set_production_error_reporting() and
 * _set_test_ip(), suitable for tests needing full legacy initialization.
 *
 * ⚠️ Remember to call _clear_test_ip() in tearDown() to avoid
 * cross-test contamination.
 */
function _init_integration_test_env()
{
    _set_production_error_reporting();
    _set_test_ip();
}
