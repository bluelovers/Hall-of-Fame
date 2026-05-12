# IP 取得機制與測試繞過方案
# IP Retrieval Mechanism & Test Bypass Strategy

## 概述

本文件說明 HOF 專案中 IP 位址的取得機制、觸發鏈、在 CLI 測試環境下的問題，以及官方的解決方案。

---

## 1. 機制說明

### 1.1 核心函式：`HOF::ip()`

定義於 `hof/trust_path/HOF.php:323`：

```php
public static function ip($ipv6 = false, $allow_private = true)
{
    $keys = array(
        'HTTP_X_FORWARDED_FOR',
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR');

    $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

    if ($allow_private === true)
    {
        $flags = FILTER_FLAG_NO_RES_RANGE;
    }

    if (!$ipv6)
    {
        $flags = $flags | FILTER_FLAG_IPV4;
    }

    foreach ($keys as $k)
    {
        if (filter_var($_SERVER[$k], FILTER_VALIDATE_IP, $flags))
        {
            $ip = $_SERVER[$k];
            break;
        }
    }

    return $ip;
}
```

**行為特徵：**

| 特徵 | 說明 |
|------|------|
| 檢查順序 | `HTTP_X_FORWARDED_FOR` → `HTTP_CLIENT_IP` → `REMOTE_ADDR` |
| 驗證方式 | `filter_var()` + `FILTER_VALIDATE_IP` |
| 過濾規則 | 預設排除 `FILTER_FLAG_NO_RES_RANGE`（保留範圍） |
| IP 版本 | 預設僅 IPv4 |
| 安全性 | 直接存取 `$_SERVER[$k]`，**不使用 `isset()` 保護** |

### 1.2 觸發鏈

```
HOF::ip()
  ↑ 被多處呼叫
  │
  ├── HOF_Class_Char_Abstract::uniqid()     ← 任何角色物件建立時
  ├── HOF_Helper_Char::uniqid()             ← 工具函數
  ├── HOF_Controller_Game                   ← 使用者登入
  ├── HOF_Model_Main                        ← 使用者資料 CRUD
  └── HOF_Class_Item_Auction                ← 拍賣 IP 檢查
```

### 1.3 所有呼叫點

| 檔案 | 行號 | 用途 |
|------|------|------|
| `HOF/Class/Char/Abstract.php` | 182 | `uniqid()` 產生角色唯一識別碼 |
| `HOF/Helper/Char.php` | 32 | `uniqid()` 雜湊種子 |
| `HOF/Controller/Game.php` | 694 | 使用者 IP 記錄 |
| `HOF/Model/Main.php` | 51, 213 | 使用者資料的 IP 欄位 |
| `HOF/Class/Item/Auction.php` | 341, 716 | 拍賣 IP 檢查 |

---

## 2. 問題：CLI 環境下的 E_NOTICE

### 2.1 原因

`HOF::ip()` 直接存取 `$_SERVER[$k]` 而不使用 `isset()`。在 CLI 環境下（如 PHPUnit 測試）：

- `$_SERVER['HTTP_X_FORWARDED_FOR']` 不存在
- `$_SERVER['HTTP_CLIENT_IP']` 不存在
- `$_SERVER['REMOTE_ADDR']` 不存在

每一行缺少的鍵都會觸發 `E_NOTICE: Undefined index`。

### 2.2 觸發條件

任何建立 `HOF_Class_Char_Abstract` 子類別物件的測試都會觸發此問題，因為建構子（`Abstract.php:156-166`）總是呼叫：

```php
public function __construct($no, $options = array(), $owner = null, $player = null)
{
    $this->initCharType();
    $owner !== null && $this->owner($owner);
    $player !== null && $this->player($player);

    $this->uniqid(true);        // ← 觸發 HOF::ip()
    $this->_extend_init();
    $this->init($no, $options);
}
```

### 2.3 嚴重性

在 PHPUnit 的嚴格環境下（`error_reporting(E_ALL | E_STRICT)` + `convertNoticesToExceptions="true"`），這些 harmless 的 E_NOTICE 會被轉為 `PHPUnit_Framework_Error_Notice` exception，導致測試中斷。

---

## 3. 解決方案：靜態測試 IP 屬性

### 3.1 設計

在 `HOF` 類別新增一個靜態屬性 `$_testIp`，當其值不為 `null` 時，`HOF::ip()` 直接回傳該值，完全繞過 `$_SERVER` 存取。

```php
public static $_testIp = null;

public static function ip($ipv6 = false, $allow_private = true)
{
    // 測試模式：直接回傳測試 IP，繞過 $_SERVER
    // Test mode: return test IP directly, bypass $_SERVER
    if (self::$_testIp !== null)
    {
        return self::$_testIp;
    }

    // ... 原有邏輯
}
```

### 3.2 使用方式

在測試的 `setUp()` 中設定，`tearDown()` 中清除：

```php
protected function setUp()
{
    parent::setUp();

    /** 設定測試 IP，繞過 HOF::ip() 的 $_SERVER 存取 */
    HOF::$_testIp = '192.168.1.100';
}

protected function tearDown()
{
    /** 清除測試 IP，恢復正常行為 */
    HOF::$_testIp = null;

    parent::tearDown();
}
```

### 3.3 選擇 192.168.1.100 的原因

`HOF::ip()` 使用 `filter_var()` 搭配 `FILTER_FLAG_NO_RES_RANGE` 進行驗證：

| IP | `FILTER_FLAG_NO_RES_RANGE` | 結果 |
|----|---------------------------|------|
| `127.0.0.1` | 被過濾（loopback = reserved） | ❌ 不通過 |
| `192.168.1.100` | 不屬於保留範圍 | ✅ 通過 |
| `0.0.0.0` | 被過濾 | ❌ 不通過 |

### 3.4 優點

| 優點 | 說明 |
|------|------|
| 零變更呼叫端 | 所有呼叫 `HOF::ip()` 的程式碼自動受益 |
| 向後相容 | `$_testIp = null`（預設值）時行為完全不變 |
| 輕量 | 單一屬性檢查，無效能影響 |
| 集中管理 | 透過共用 `test_helper.php` 統一設定與清除 |

---

## 4. 共用測試工具

定義於 `test/lib/test_helper.php`：

| 函數 | 用途 |
|------|------|
| `_set_production_error_reporting()` | 設定 error_reporting 比照 production |
| `_set_server_ip_for_cli()` | 設定 `$_SERVER` IP 鍵（舊方案） |
| `_set_test_ip()` | 使用新方案設定 `HOF::$_testIp` |
| `_clear_test_ip()` | 清除 `HOF::$_testIp`（在 tearDown 中呼叫） |
| `_init_integration_test_env()` | 組合：error_reporting + test IP |
| `_temp_yaml_path($prefix)` | 產生唯一性的暫存 YAML 路徑 |

### 4.1 建議的 setUp/tearDown 模式

```php
protected function setUp()
{
    parent::setUp();
    _set_test_ip();          // 設定 HOF::$_testIp，繞過 $_SERVER
    _set_production_error_reporting();  // 比照 production 錯誤報告
}

protected function tearDown()
{
    _clear_test_ip();        // 清除 HOF::$_testIp
    parent::tearDown();
}
```

---

## 5. 相關檔案

| 檔案 | 角色 |
|------|------|
| `hof/trust_path/HOF.php` (`HOF::ip()`) | 核心 IP 取得函式 + `$_testIp` 屬性 |
| `hof/trust_path/HOF/Class/Char/Abstract.php` (`uniqid()`) | 角色物件建立觸發點 |
| `test/lib/test_helper.php` | 共用測試工具（`_set_test_ip` / `_clear_test_ip`） |
| `test/phpunit/*Test.php` (`setUp`/`tearDown`) | 各測試類別的環境設定 |

---

## 6. 版本歷史

| 日期 | 變更 |
|------|------|
| 2026-05-12 | 建立本文件。實作 `HOF::$_testIp` 機制，取代 `$_SERVER` 手動設定方案 |
