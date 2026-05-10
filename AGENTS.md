# AGENTS.md — Hall of Fame (HOF) 專案指引

> 本文件為 AI Agent 與開發者整理的專案規範、架構與開發指引。
> 內容來源自專案原始碼、設定檔、腳本、文件與開發紀錄。

> ⚠️ **伺服器啟動唯一準則：僅限使用 `taskill-port.bat && start_and_kill.bat` 的組合命令**
> （或需要 `瀏覽器互動測試流程` 時，參考 [7. 開發腳本 - 瀏覽器互動測試流程](#7-開發腳本)）
>
> **絕對禁止以任何其他方式啟動 PHP 伺服器**（包括但不限於手動下 `php -S` 指令、直接執行 PHP 內建伺服器、透過其他腳本或工具啟動）。
>
> 違反此規則會導致：
> - 行程殘留，Port 被佔用且無錯誤訊息
> - 無限等待/阻塞
> - 難以偵錯的各種隱形問題
>
> 詳見 [7. 開發腳本 — ⚠️ 伺服器啟動重要限制](#7-開發腳本)

---

## 目錄

1. [專案概述](#1-專案概述)
2. [執行環境](#2-執行環境)
3. [目錄結構](#3-目錄結構)
4. [架構總覽](#4-架構總覽)
5. [類別載入系統](#5-類別載入系統)
6. [設定系統](#6-設定系統)
7. [開發腳本](#7-開發腳本)
8. [PHP 版本相容性](#8-php-版本相容性)
9. [編碼規範](#9-編碼規範)
10. [Stub / 外部依賴](#10-stub--外部依賴)
11. [常見問題排除](#11-常見問題排除)
12. [注意事項與禁止事項](#12-注意事項與禁止事項)

---

## 1. 專案概述

**Hall of Fame (HOF)** 是一個以 PHP 撰寫的網頁遊戲，最初由 Juice 於 2007–2008 年開發，後續由 bluelovers 於 2012 年進行分支改寫。

| 項目 | 內容 |
|------|------|
| 專案名稱 | Hall of Fame (HOF) |
| 目前分支 | `develop5` |
| 原始著作權 | 2007–2008 Juice |
| Bootstrap 著作權 | 2012 bluelovers |
| 基底提交 | `156bd236beeef65ce0e3f3dec898352ebe2cba8a` (2012-06-05) |
| 資料庫 | 不使用資料庫（檔案系統 + YAML） |
| 主要語言 | PHP |

### 分支特性

此 `develop5` 分支與其他分支相比**結構性差異最大**，但優點是大多數資料已經從原始碼中抽離出來儲存為 YAML 格式（使用 `hof/trust_path/HOF/Resource/` 目錄）。

---

## 2. 執行環境

### 目前可執行環境

| 項目 | 值 |
|------|-----|
| PHP 版本 | **5.6.32** (nts, Win32, VC11, x64) |
| PHP 路徑 | `D:\Users\WebstormProjects\php\bin\php-5.6.32-nts-Win32-VC11-x64\php.exe` |
| 擴充目錄 | `D:\Users\WebstormProjects\php\bin\php-5.6.32-nts-Win32-VC11-x64\ext` |
| 啟用擴充 | `php_mbstring.dll` |
| 時區 | `Asia/Taipei` |
| 平台 | Windows (Win32) |

### PHP 設定 (php.ini)

專案使用 `hof/php.ini` 自訂設定，透過 `-c <dir>` 參數載入：

```ini
extension_dir = "D:\Users\WebstormProjects\php\bin\php-5.6.32-nts-Win32-VC11-x64\ext"
extension = php_mbstring.dll
date.timezone = "Asia/Taipei"
include_path = "D:\Users\WebstormProjects\game\Hall-of-Fame\.codenomad\worktrees\develop5\hof\includes;."
```

> **注意：** PHP CLI 不自動載入 CWD 下的 `php.ini`，必須使用 `-c <目錄>` 參數強制載入。

> **原則：** 外部 `php.ini` 僅作為伺服器端的統一化設定。若能直接在 PHP 程式碼中設定的項目（如 `date.timezone`），應優先於程式碼內設定，以降低對外部設定檔的依賴。

### 模組依賴原則

- **盡量不依賴 DLL 擴充模組**（如 PHP extensions），功能應優先以 PHP 腳本本身實作
- **盡量不依賴 include_path 引用路徑**，模組應置於專案內，使 `include_path` 保持為 `"."`（僅當前目錄）
- 亦即避免依賴類似 `include_path = "D:\...\hof\includes;."` 的路徑設定

---

## 3. 目錄結構

```
develop5/
├── .gitignore
├── AGENTS.md                        # 本文件
├── README.md                        # 專案 ReadMe
├── debug-cls.bat                    # 清除 ~* 暫存檔
├── docs/
│   └── php-version-analysis.md      # PHP 版本分析報告
├── hof/                             # ★ Web 根目錄
│   ├── .htaccess
│   ├── index.php                    # 前端入口
│   ├── admin.php                    # 管理後台入口
│   ├── php.ini                      # PHP 設定（給 -c 參數用）
│   ├── server_router.php            # PHP 內建伺服器路由
│   ├── start_and_kill.bat           # 啟動腳本（批次檔）
│   ├── start_and_kill.ps1           # 啟動腳本（PowerShell）
│   ├── start-server.bat             # 長期伺服器啟動腳本
│   ├── taskill-port.bat             # 終止 PHP 行程（批次檔，呼叫 .ps1）
│   ├── taskill-port.ps1             # 終止 PHP 行程（PowerShell，支援 -Port 參數）
│   ├── _diag.php                    # 診斷腳本
│   ├── test_bootstrap.php           # Bootstrap 測試
│   ├── test_info.php                # PHP 資訊頁
│   ├── favicon.ico
│   ├── includes/                    # ★ 外部函式庫 / Stub
│   │   ├── Zend/                    # Zend Framework 1 Stub
│   │   └── Symfony/                 # Symfony YAML Stub
│   ├── static/                      # 靜態資源
│   └── trust_path/                  # ★ 專案核心目錄
│       ├── bootstrap.php            # 啟動載入腳本
│       ├── HOF.php                  # HOF 主類別（Singleton）
│       ├── config/
│       │   ├── setting.dist.php     # 預設設定（版本控管）
│       │   └── setting.php          # 自訂設定（.gitignore）
│       ├── admin/                   # 管理後台功能
│       ├── tpl/                     # 模板檔案
│       ├── dat/                     # 動態資料（使用者、排名等）
│       ├── cache/                   # 快取目錄
│       ├── test/                    # 測試腳本
│       ├── HOF/                     # ★ 核心類別與資源
│       │   ├── Autoloader.php       # 自動載入器
│       │   ├── Loader.php           # 類別載入器
│       │   ├── Class/               # 類別定義
│       │   ├── Controller/          # 控制器
│       │   ├── Model/               # 資料模型
│       │   ├── Helper/              # 輔助函數
│       │   ├── Syntax/              # 語法工具
│       │   ├── Const/               # 常數定義
│       │   └── Resource/            # ★ YAML 資源檔案
│       │       ├── Char/            # 角色設定
│       │       ├── Item/            # 物品設定
│       │       ├── Skill/           # 技能設定
│       │       ├── Skilltree/       # 技能樹設定
│       │       ├── Union/           # 工會設定
│       │       ├── Guard/           # 守衛設定
│       │       └── Color.dat        # 顏色資料
│       └── user/                    # 使用者資料（.gitignore）
```

### 關鍵入口檔案

| 檔案 | 用途 |
|------|------|
| `hof/index.php` | 前端入口，呼叫 `require("trust_path/bootstrap.php")` → `HOF::router()` |
| `hof/admin.php` | 管理後台，需密碼驗證 (`ADMIN_PASSWORD = "password"`) |
| `hof/trust_path/bootstrap.php` | 啟動流程：錯誤設定 → 載入設定 → 初始化 Zend Autoloader → 註冊 HOF Autoloader → 初始化 HOF Singleton |
| `hof/server_router.php` | PHP 內建伺服器路由器，處理靜態檔案與 URL 重寫 |

---

## 4. 架構總覽

### 啟動流程

```
index.php / admin.php
    │
    ▼
trust_path/bootstrap.php
    │
    ├─ 1. error_reporting 設定
    ├─ 2. 定義 REQUEST_TIME
    ├─ 3. 載入 bootstrap.options.php（可選）
    ├─ 4. 載入 config/setting.php（可選，自訂覆蓋）
    ├─ 5. 載入 config/setting.dist.php（強制，預設設定）
    ├─ 6. 初始化 Zend_Loader_Autoloader
    ├─ 7. 載入 HOF_Autoloader, HOF_Loader
    ├─ 8. 註冊 HOF Autoloader（支援 HOF_ 前綴類別自動載入）
    ├─ 9. 載入語法工具與常數
    └─ 10. HOF::getInstance() → HOF::router()
```

### 核心類別繼承關係

```
HOF (final)                                  — 主 Singleton，路由、快取、日誌、Session
└── HOF_Class_Main extends HOF_Class_User    — 使用者認證與登入狀態管理

HOF_Autoloader extends Zend_Loader_Autoloader — 自動載入器
HOF_Loader extends Zend_Loader                — 類別檔案載入器
```

### Controller 分派

`HOF::router()` 根據路由參數分派到對應的 Controller：

| Controller 類別 | 路由範圍 |
|-----------------|---------|
| `HOF_Controller_Game` | 遊戲主流程（登入、新遊戲、設定、排名、刪除資料） |
| `HOF_Controller_Char` | 角色管理（狀態、裝備、技能學習、轉職） |
| `HOF_Controller_Battle` | 戰鬥系統（普通戰鬥、工會戰、排名戰） |
| `HOF_Controller_Town` | 城鎮功能 |
| `HOF_Controller_Shop` | 商店（買/賣） |
| `HOF_Controller_Smithy` | 精鍊 |
| `HOF_Controller_Auction` | 拍賣場 |
| `HOF_Controller_Recruit` | 招募 |
| `HOF_Controller_Rank` | 排名 |
| `HOF_Controller_Log` | 日誌 |
| `HOF_Controller_Manual` | 說明手冊 |
| `HOF_Controller_Initialize` | 初始化 |
| `HOF_Controller_Image` | 圖片處理 |
| `HOF_Controller_Gamedata` | 遊戲資料 |
| `HOF_Controller_Item` | 物品管理 |

### 模型層

| 模型類別 | 用途 |
|---------|------|
| `HOF_Model_Main` | 主要資料存取（使用者、角色資料 CRUD） |
| `HOF_Model_Data` | 資料處理 |
| `HOF_Model_Char` | 角色資料模型 |

### Helper 工具函數

Helper 類別以靜態方法提供工具功能：

| Helper 類別 | 用途 |
|-------------|------|
| `HOF_Helper_Global` | 全域輔助函數 |
| `HOF_Helper_Array` | 陣列操作 |
| `HOF_Helper_Battle` | 戰鬥計算 |
| `HOF_Helper_Char` | 角色相關計算 |
| `HOF_Helper_Date` | 日期處理 |
| `HOF_Helper_Item` | 物品操作 |
| `HOF_Helper_Math` | 數學計算 |
| `HOF_Helper_Object` | 物件操作 |

---

## 5. 類別載入系統

### 載入架構

專案使用兩層自動載入機制：

1. **Zend_Loader_Autoloader** — 基礎 autoloader 框架（Zend Framework 1 風格）
2. **HOF_Autoloader** — 擴充的 autoloader，支援自訂命名空間前綴（如 `HOF_`）

### 類別命名與路徑對應

採用 **PEAR-style** 類別命名規則（底線對應目錄分隔）：

| 類別名稱 | 對應檔案路徑 |
|----------|-------------|
| `HOF_Class_Main` | `HOF/Class/Main.php` |
| `HOF_Controller_Game` | `HOF/Controller/Game.php` |
| `HOF_Model_Main` | `HOF/Model/Main.php` |
| `HOF_Helper_Battle` | `HOF/Helper/Battle.php` |
| `HOF_Class_File_Cache` | `HOF/Class/File/Cache.php` |

### Autoloader 註冊流程

```php
// bootstrap.php
Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(true);
Zend_Loader::loadClass('HOF_Autoloader', BASE_TRUST_PATH);
Zend_Loader::loadClass('HOF_Loader', BASE_TRUST_PATH);

HOF_Autoloader::getInstance()
    ->pushAutoloader(BASE_TRUST_PATH, 'HOF_')
    ->setDefaultAutoloader(array('HOF_Loader', 'loadClass'));
```

### 手動載入工具

```php
// 載入檔案（相對於 BASE_TRUST_PATH）
HOF_Loader::loadFile('syntax.func.php', BASE_TRUST_PATH.'HOF/Syntax', true);

// 載入類別
Zend_Loader::loadClass('ClassName', $searchPath);
```

---

## 6. 設定系統

### 設定檔案層次

```
bootstrap.options.php  (可選，不存在則跳過)
    │
    ▼
config/setting.php     (可選，.gitignore，用於自訂覆蓋)
    │
    ▼
config/setting.dist.php (強制，版本控管，所有預設設定)
```

### 關鍵路徑常數

| 常數 | 值 | 用途 |
|------|-----|------|
| `BASE_PATH` | `hof/trust_path/../../` | 專案根目錄（相對於 `config/`） |
| `BASE_TRUST_PATH` | `BASE_PATH . 'trust_path/'` | 核心目錄 |
| `BASE_URL_ROOT` | 從 `$_SERVER['PHP_SELF']` 計算 | URL 根路徑 |
| `BASE_URL` | 完整 URL（含 scheme/host/port） | 網站基礎 URL |
| `BASE_PATH_TPL` | `BASE_TRUST_PATH . 'tpl/'` | 模板目錄 |
| `BASE_PATH_STATIC` | `BASE_PATH . 'static/'` | 靜態資源目錄 |
| `BASE_PATH_CACHE` | `BASE_TRUST_PATH . 'cache/'` | 快取目錄 |
| `DAT_DIR` | `BASE_TRUST_PATH . 'dat/'` | 資料目錄 |
| `BASE_EXT` | `.yml` | 資源檔案副檔名 |

### 遊戲設定常數（選列）

| 常數 | 預設值 | 說明 |
|------|--------|------|
| `TITLE` | `Hall of Rumor 噂のホール` | 遊戲標題 |
| `MAX_TIME` | `1000` | 最大 Time |
| `MAX_CHAR` | `5` | 最大角色數 |
| `MAX_USERS` | `500` | 最大註冊人數 |
| `MAX_LEVEL` | `50` | 最大等級 |
| `MAX_STATUS` | `250` | 狀態最大值 |
| `START_MONEY` | `50000` | 初始金錢 |
| `DEBUG` | `0` | 除錯模式（0=關） |
| `AUCTION_TOGGLE` | `0` | 拍賣功能開關 |

---

## 7. 開發腳本

### 伺服器啟動腳本

> ⚠️ **各分支 port 不同：** 本分支 `develop5` 使用 **port 8085**，其他分支可能使用不同埠號。

| 腳本 | 用途 | 執行方式 |
|------|------|---------|
| `hof/start_and_kill.bat` | 啟動 PHP 伺服器，監控 10 秒後自動關閉（測試用） | `./hof/start_and_kill.bat` |
| `hof/start_and_kill.ps1` | PowerShell 版本，支援參數 | 透過 `start_and_kill.bat` 或 `taskill-port.bat` 間接呼叫 |
| `hof/start_and_kill.bat -NoKill` | 啟動長期背景伺服器（需手動關閉） | `./hof/start_and_kill.bat -NoKill` |
| `hof/start-server.bat` | 簡易啟動腳本（舊版） | `./hof/start-server.bat` |

### 工具腳本

| 腳本 | 用途 |
|------|------|
| `hof/taskill-port.bat` | 終止 PHP 行程（批次檔，呼叫 .ps1） |
| `hof/taskill-port.ps1` | 終止 PHP 行程（PowerShell，支援 `-Port` 參數，預設 8085） |
| `debug-cls.bat` | 清除專案內所有 `~*` 暫存檔 |

### PowerShell 腳本參數

```powershell
# start_and_kill.ps1 支援參數
-NoKill              # 長期執行模式（不倒數自動關閉）
-Port (或 -p)        # 埠號，預設 8085（此為 develop5 分支預設值）
-Timeout (或 -t)     # 自動關閉秒數，預設 10
```

### ⚠️ 伺服器啟動重要限制

> **絕對禁止以任何其他方式啟動 PHP 伺服器**（包括但不限於手動下 `php -S` 指令、直接執行 PHP 內建伺服器、透過其他腳本或工具啟動）。

**禁止自行構造 `php -S` 啟動指令。** 直接使用 `php -S` 會導致：
- 無限等待/阻塞（無 timeout 機制）
- 行程殘留，Port 被佔用且無錯誤訊息
- 難以偵錯的各種隱形問題

**一律強制使用專案提供的 `start_and_kill.bat` / `start_and_kill.ps1` 腳本**，預設 10 秒已足夠取得必要資訊。
若時間不足，可透過參數調整：
```bash
./hof/start_and_kill.bat -Timeout 30    # 延長至 30 秒
./hof/start_and_kill.bat -NoKill         # 長期執行（需手動關閉）
```

### 瀏覽器互動測試流程

當需要透過實際操作遊戲流程才能檢測錯誤或確認錯誤發生點時，可使用瀏覽器 DevTools 進行互動。

> ⚠️ **伺服器啟動時機：僅在修正完成、準備驗證時才啟動。**
>
> PHP 為無狀態直譯語言，修改程式碼後必須重啟伺服器才能套用。
> 若在修改程式碼前就先啟動伺服器，後續修正時需先關閉再重啟，浪費時間與步驟。
>
> **正確流程：** 先完成所有程式碼修改 → 再啟動伺服器 → 進行驗證測試

#### 啟動與連線

**啟動伺服器時，一律同時執行關閉舊行程指令**，確保每次啟動都是全新的伺服器狀態：

```bash
./hof/taskill-port.bat && ./hof/start_and_kill.bat -NoKill
```

- `taskill-port.bat` — 先關閉可能殘留的舊伺服器行程
- `start_and_kill.bat -NoKill` — 再啟動新伺服器（長期模式）
- 兩者用 `&&` 串聯，一次命令完成

使用背景方式啟動長期伺服器避免阻塞：
- 使用 `run_background_process` 工具執行上述命令
- 或透過 `nohup` / 背景行程方式啟動

啟動後即可透過 Chrome DevTools MCP 工具連線至 `http://localhost:8085`。

> **原因：** `start_and_kill.bat` 預設為同步阻塞模式。若未使用背景方式啟動，伺服器會佔用終端導致無法操作瀏覽器，或伺服器提早關閉。
>
> **不需要先確認舊行程是否存活：** 無論有無殘留行程，`taskill-port.bat` 都會執行清理。每次都先關再開，可確保讀取到最新程式碼，省去手動判斷的步驟。

#### 登入與帳號

- **預設測試帳號：** `demo` / `demo`
- 若使用者不存在，或必須建立新帳號才能複現問題時，可自行建立帳號密碼
- **建立後務必記錄帳號密碼**保存於 `docs/log/` 以供後續參考

#### 操作原則

- 可自行判斷遊戲流程中的輸入欄位並進行操作
- 可自由瀏覽各頁面、點擊按鈕、填寫表單以觸發錯誤
- **必須記錄操作原因與步驟**，保存於 `docs/log/records/` 目錄下，以日期時間為 prefix
- **必須記錄頁面的功能與詳細操作指南(包含網址)**，保存於 `docs/log/pages/` 目錄下，包含但不限於：注意事項、容易忽略沒注意到的部分，出現的NPC/怪物等

### 伺服器路由器功能

`hof/server_router.php` 負責：

1. 修正 `SERVER_NAME`（從 `HTTP_HOST` 取得）
2. 提供靜態檔案服務（CSS、JS、圖片、字型等 MIME 類型）
3. 處理 `/static/` 路徑（含遞迴修正損壞的 URL）
4. 處理 `/index.php/xxx` 路徑
5. 重設 `PHP_SELF` / `SCRIPT_NAME` 為 `/index.php`

---

## 8. PHP 版本相容性

### 目標版本

此分支的目標 PHP 版本為 **PHP 5.2**（2006 年 11 月發佈），向下相容 PHP 4。

目前實際使用 **PHP 5.6.32** 執行，相容於 PHP 5.2–5.6 之間的所有功能。

### 已使用的功能（最低需求）

| 功能 | 最低 PHP 版本 |
|------|-------------|
| 類別與物件 (`class`, `new`) | PHP 4 |
| `var` 屬性宣告 | PHP 4 |
| `function __construct()`（無可見性修飾詞） | PHP 4 |
| `ob_start()`, `ob_gzhandler` | PHP 4 |
| `function __destruct()` | PHP 5.0 |
| `function __clone()` | PHP 5.0 |
| `function __toString()` | PHP 5.0 |
| `function __call($func, $args)` | PHP 5.0 |
| `function __isset($k)`, `__get($k)` | PHP 5.0 |
| `public` / `private` / `protected` | PHP 5.0 |
| `abstract class` / `interface` | PHP 5.0 |
| `self::`, `parent::` | PHP 5.0 |
| `static` 方法 / 屬性 | PHP 5.0 |
| `file_put_contents()` | PHP 5.0 |
| `array` 型別提示 (`function foo(array $bar)`) | PHP 5.1 |
| `$_SERVER['REQUEST_TIME']` | PHP 5.1 |
| Zend Framework 1.x Autoloader | PHP 5.2 |

### 未使用的功能（不應使用）

| 功能 | 最低 PHP 版本 | 說明 |
|------|-------------|------|
| 命名空間 (`namespace`) | PHP 5.3 | ❌ 使用 PEAR-style 類別命名 |
| 閉包 / 匿名函式 (`function() use`) | PHP 5.3 | ❌ 不使用 |
| `__DIR__` 常數 | PHP 5.3 | ❌ 使用 `dirname(__FILE__)` |
| 短陣列語法 (`[]`) | PHP 5.4 | ❌ 使用 `array()` |
| Trait | PHP 5.4 | ❌ 不使用 |
| `callable` 型別提示 | PHP 5.4 | ❌ 不使用 |
| `finally` 例外處理 | PHP 5.5 | ❌ 不使用 |
| `empty()` 支援函式回傳值 | PHP 5.5 | ❌ 不使用 |

### 相容性時間線

```
2004-07  PHP 5.0.0  ─── OOP 基礎、魔術方法、可見性
2005-11  PHP 5.1.0  ─── array 型別提示、$_SERVER['REQUEST_TIME']
2006-11  PHP 5.2.0  ─── ★★★ 目標版本 ★★★
2007-08  原始 HOF 著作權
2009-06  PHP 5.3.0  ─── 命名空間、閉包、__DIR__（不使用）
2012-03  PHP 5.4.0  ─── 短陣列、trait、內建伺服器（不使用）
2012     此 develop5 分支（@copyright 2012）
```

---

## 9. 編碼規範

### 9.1 PHP 語法要求

- **使用 PHP 5.6.32 相容語法**
- **不使用 call-time pass-by-reference**（PHP 5.4 已移除）
- **不使用命名空間**（namespace）
- **優先使用相容性高的語法（PHP 4/5 通用）**，避免 PHP 5.3+ 專屬功能
- **避免使用後續 PHP 版本中已被棄用或破壞性變更的語法/API**
- 陣列使用 `array()` 而非 `[]`
- 路徑常數使用 `dirname(__FILE__)` 而非 `__DIR__`

### 9.2 模組依賴原則

- **盡量不依賴 DLL 擴充模組**（PHP extensions），功能應優先以 PHP 腳本本身實作或間接達成
- **盡量不依賴 include_path 引用路徑** — 模組應置於專案內可直達的位置
- 目標是讓 `include_path` 保持為 `"."`（僅當前目錄），不依賴類似 `include_path = "D:\...\hof\includes;."` 的外部路徑設定
- `date.timezone` 等執行期設定若能直接在 PHP 程式碼中設定，優先於程式碼內設定（`date_default_timezone_set()`），外部 `php.ini` 僅作為伺服器端的統一化

### 9.3 物件導向原則

- **以函數為優先，物件導向為輔** — 能直接用函數實作的邏輯就不要包裝成類別
- **避免不必要的物件導向** — 不為了「物件導向」而刻意建立類別或繼承層級
- **僅在必要時才使用物件導向**，例如：
  - 需要管理狀態（State）或生命週期
  - 需要多型（Polymorphism）或介面約束
  - 需要 Singleton 等特定模式
- **靜態 Helper 類別也不鼓勵** — 若只是工具函數集合，直接用全域函數（如 `syntax.func.php` 風格）即可，不需要包裝成靜態類別
- **在不造成大量結構改變的前提下**，既有程式碼維持原狀，新實作時才遵循此原則

### 9.4 命名規範

- **PEAR-style** 類別命名：`HOF_Controller_Game`（底線對應目錄分隔）
- 常數：大寫底線式（`MAX_CHAR`, `BASE_PATH`, `DEBUG`）
- 類別屬性：PHP 4 `var` 或 `public/protected/private`
- 建構子：`function __construct()` 或 PHP 4 風格（與類別同名）

### 9.5 重構原則

- **已存在的語法盡量不更動**
- **新實作時避免使用上述未使用的功能**
- **漸進式小量重構** — 在不造成大量改動與相容性問題的情況下逐步改善
- 避免大規模結構改寫

### 9.6 單一事實來源 (Single Source of Truth)

- **各種資料、函數、邏輯定義應只有一個維護點**，避免分散多處各自維護相同定義
- 例如常數、設定值、驗證邏輯等，應定義於共用位置，透過引用（include / 載入）方式組合使用
- **可組合使用** — 拆分的模組應能彼此組合、重複利用，而非各自複製貼上
- 既有程式碼中若有重複定義，在**不造成大量改動的前提下**漸進式收攏至共用位置

### 9.7 錯誤處理

```php
// bootstrap.php 預設設定
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '1');
```

### 9.8 輸出緩衝

```
bootstrap.php 中 ob_start('ob_gzhandler') 預設為註解狀態

開發階段：註解（顯示原始錯誤訊息）
正式環境：取消註解（啟用 gzip 壓縮），或由 Apache mod_deflate 處理
```

---

## 10. Stub / 外部依賴

> **原則：** `hof/includes/` 下的 stub 僅實作 HOF 實際使用到的公開方法，不包含完整的第三方函式庫邏輯。

### 10.1 Zend Framework 1 Stub

位於 `hof/includes/Zend/`，因完整 ZF1 程式碼位於外部目錄 `D:\Users\WebstormProjects\php\hof-include\Zend\` 僅供參考。

| Stub 檔案 | 對應類別 | 實作重點 |
|-----------|---------|---------|
| `Zend/Loader.php` | `Zend_Loader` | `loadClass()`, `loadFile()`, `securityCheck()` |
| `Zend/Loader/Autoloader.php` | `Zend_Loader_Autoloader` | Singleton, autoloader 註冊 |
| `Zend/Loader/Autoloader/Interface.php` | `Zend_Loader_Autoloader_Interface` | 介面定義 |
| `Zend/Exception.php` | `Zend_Exception extends Exception` | 基礎例外類別 |
| `Zend/Date.php` | `Zend_Date` | 日期操作（常數、建構、格式化、時區） |
| `Zend/Locale.php` | `Zend_Locale` | 區域設定 |
| `Zend/Registry.php` | `Zend_Registry` | 靜態註冊表 (Singleton) |
| `Zend/Session.php` | `Zend_Session` | Session 管理（包裝原生 PHP session） |
| `Zend/Session/Namespace.php` | `Zend_Session_Namespace` | Session 命名空間容器 |

### 10.2 Symfony YAML Stub

位於 `hof/includes/Symfony/Component/Yaml/`，提供 YAML 解析與產生功能。

| Stub 檔案 | 用途 |
|-----------|------|
| `Yaml.php` | 主要接口 (`parse()` / `dump()`) |
| `Parser.php` | YAML 解析器 |
| `Dumper.php` | YAML 產生器 |
| `Inline.php` | 行內 YAML 解析 |
| `Escaper.php` | 字串逸出處理 |
| `Unescaper.php` | 字串反逸出處理 |
| `Exception/ParseException.php` | 解析例外 |
| `Exception/ExceptionInterface.php` | 例外介面 |

### 10.3 外部參考目錄

當需要新增功能或模組時，可參考以下目錄以簡易實作方式建立（**不要複製複雜模組**）：

- `D:\Users\WebstormProjects\php\hof-include` — HOF 相關包含檔案
- `D:\Users\WebstormProjects\php\Scophp` — ScoPHP 函式庫

---

## 11. 常見問題排除

### 11.1 include_path 載入失敗

**症狀：** `require_once('Zend/Loader/Autoloader.php')` 找不到檔案
**原因：** PHP CLI 不自動載入 CWD 下的 `php.ini`
**解決：** 使用 `-c <dir>` 參數強制載入

```bash
php -c hof/ -S 0.0.0.0:8085 -t hof/
```

### 11.2 Gzip 輸出緩衝遮蔽錯誤

**症狀：** HTTP 200 + 空主體，無任何錯誤訊息
**原因：** `ob_start('ob_gzhandler')` 在錯誤發生前壓縮了空內容
**解決：** 開發階段註解該行

```php
//ob_start('ob_gzhandler');
```

### 11.3 Port 被佔用

**症狀：** 伺服器啟動後立即中斷
**解決：** 執行 `hof/taskill-port.bat` 或 `hof/taskill-port.ps1` 終止舊行程，或透過 `-Port` 參數變更埠號再啟動

---

## 12. 注意事項與禁止事項

### 禁止事項

- ❌ **絕對禁止修改 `D:\Users\WebstormProjects\php\` 路徑下任何檔案**
  - 所有 PHP 設定應透過 `hof/php.ini` 或 PowerShell 參數達成
- ❌ 除非必要，避免使用長期執行模式 (`start_and_kill.bat -NoKill`)
  - 10 秒自動關閉模式已足夠檢查錯誤與除錯
- ❌ 不要複製外部參考目錄的複雜模組 — 應以簡易實作方式建立所需功能

### 注意事項

- ⚠️ 所有分支之間皆有相容性與結構性差異，切換分支時需注意
- ⚠️ PHP CLI 的行為（php.ini 載入規則、.htaccess 不支援）與 Apache mod_php/CGI 有差異
- ⚠️ 專案內模組即為全部模組，沒有外部引用模組（透過 Composer 等）

### 開發建議

- 新功能實作時，先確認是否可透過全域函數實作，再考慮物件導向
- 優先使用相容性高的語法（PHP 4/5 通用），避免 PHP 5.3+ 專屬功能
- 若缺少功能或模組，參考外部目錄後以最簡方式實作
