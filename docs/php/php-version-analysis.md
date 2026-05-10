# PHP 版本分析報告
# PHP Version Analysis Report

## 分支資訊 / Branch Info

| 項目 | 內容 |
|------|------|
| 分支名稱 | develop5 |
| 專案 | Hall of Fame (HOF) |
| 原始著作權 | 2007-2008 Juice |
| Bootstrap 著作權 | 2012 bluelovers |
| README 聲明 | PHP 4/5 均可運作，建議 PHP 5 |

---

## 結論 / Conclusion

此分支（develop5）的 PHP 目標版本為 **PHP 5.2**（2006 年 11 月發佈），並維持與 **PHP 4** 的向下相容。

This branch (develop5) targets **PHP 5.2** (released November 2006) with backward compatibility for **PHP 4**.

---

## PHP 功能支援最低版本分析 / PHP Feature Minimum Version Analysis

### 已使用的功能（Present Features）

| 功能 | 最低 PHP 版本 | 說明 |
|------|-------------|------|
| 類別與物件 (`class`, `new`) | **PHP 4** | OOP 基礎 |
| `var` 屬性宣告 | **PHP 4** | PHP 4 風格的類別屬性宣告 |
| `function __construct()`（無可見性修飾詞） | **PHP 4** | PHP 4 風格的建構子（與類別同名也相容） |
| `function __destruct()` | **PHP 5.0** | 解構子（魔術方法） |
| `function __clone()` | **PHP 5.0** | 物件複製鉤子 |
| `function __toString()` | **PHP 5.0** | 字串轉換魔術方法 |
| `function __call($func, $args)` | **PHP 5.0** | 方法多載魔術方法 |
| `function __isset($k)` | **PHP 5.0** | 屬性 isset 魔術方法 |
| `function __get($k)` | **PHP 5.0** | 屬性讀取魔術方法 |
| `public` / `private` / `protected` 可見性 | **PHP 5.0** | 屬性和方法存取控制 |
| `abstract class` / `interface` | **PHP 5.0** | 抽象類別與介面 |
| `self::`, `parent::` | **PHP 5.0** | 類別內部參照 |
| `static` 方法 / 屬性 | **PHP 5.0** | 靜態成員 |
| `array` 型別提示 (`function foo(array $bar)`) | **PHP 5.1** | 參數型別約束 |
| `$_SERVER['REQUEST_TIME']` | **PHP 5.1** | 伺服器超全局變數 |
| `file_put_contents()` | **PHP 5.0** | 檔案寫入函式 |
| `ob_gzhandler` | **PHP 4.0.1** | 輸出壓縮 |
| `ob_start()` | **PHP 4** | 輸出緩衝 |
| `array_unique()` | **PHP 4.0.1** | 陣列去重 |
| `array_merge()` | **PHP 4.0.1** | 陣列合併 |
| Zend Framework 1.x Autoloader | **PHP 5.2** | Zend\_Loader\_Autoloader |

### 未使用的功能（Absent Features）

| 功能 | 最低 PHP 版本 | 狀態 |
|------|-------------|------|
| 命名空間 (`namespace`) | PHP 5.3 (2009-06) | ❌ 未使用 |
| 閉包 / 匿名函式 (`function() use`) | PHP 5.3 | ❌ 未使用 |
| `__DIR__` 常數 | PHP 5.3 | ❌ 使用 `dirname(__FILE__)` |
| `goto` | PHP 5.3 | ❌ 未使用 |
| Heredoc 中的靜態方法呼叫 | PHP 5.3 | ❌ 未使用 |
| 短陣列語法 (`[]`) | PHP 5.4 (2012-03) | ❌ 使用 `array()` |
| Trait | PHP 5.4 | ❌ 未使用 |
| 內建開發伺服器 (`php -S`) | PHP 5.4 | ❌ 使用 `start-server.bat`（系統腳本） |
| `callable` 型別提示 | PHP 5.4 | ❌ 未使用 |
| `$this` 在閉包中可用 | PHP 5.4 | ❌ 無 PHP 閉包 |
| 函式回傳陣列直接解引用 `foo()[0]` | PHP 5.4 | ❌ 未使用 |
| `finally` 例外處理 | PHP 5.5 (2013-06) | ❌ 未使用 |
| `empty()` 支援函式回傳值 | PHP 5.5 | ❌ 未使用 |

---

## 時間線對照 / Timeline

```
2004-07  PHP 5.0.0  ─── OOP 基礎、魔術方法、可見性
2005-11  PHP 5.1.0  ─── array 型別提示、$_SERVER['REQUEST_TIME']
2006-11  PHP 5.2.0  ─── ★★★ 目標版本 ★★★
2007-08  原始 HOF 著作權
2008-08  PHP 4 正式終止支援 (EOL)
2009-06  PHP 5.3.0  ─── 命名空間、閉包、__DIR__
2012-03  PHP 5.4.0  ─── 短陣列、trait、內建伺服器
2012     此 develop5 分支 bootstrap.php（@copyright 2012）
2013-06  PHP 5.5.0  ─── finally、generator
```

---

## Git 提交記錄 / Git Log

```
156bd236 Sco_File_Format
06468683 .
6e6df038 self::$_cache_ = new HOF_Class_File_Cache(array('timeout' => 86400 * 7));
d11907fa . (Step: 4)
adeb7460 . (Step: 3)
```

---

## 附錄：開發與除錯紀錄
## Appendix: Development & Debugging Notes

---

### A. PHP 版本判定方法
### A. PHP Version Determination Method

**判定流程：**

1. **Git log 掃描** — 確認 commit 歷史中最早的著作權年份（2007-2008 Juice）與最後一次有意義提交的時間
2. **語法特徵分析** — 掃描原始碼中使用的 PHP 功能：
   - 有 `public/private/protected`、`abstract`、`interface` → PHP 5.0+
   - 有 `$_SERVER['REQUEST_TIME']` → PHP 5.1+
   - 無 `namespace`、無閉包、無 `__DIR__`、無短陣列 `[]` → 排除 PHP 5.3+
   - 有 Zend Framework 1.x Autoloader 引用 → PHP 5.2+
3. **時間線比對** — 已知 HOF 最初發表於 2007-2008，bootstrap.php 著作權為 2012（bluelovers），而 PHP 5.2 是最後一個不包含命名空間/閉包的重大版本

**結論：** PHP 5.2（2006-11），向下相容 PHP 4。

---

### B. Zend Framework 1 Stub 實作
### B. Zend Framework 1 Stub Implementation

**問題：** HOF 依賴 Zend Framework 1（`Zend_Loader_Autoloader`、`Zend_Date` 等），但此分支不包含 ZF1 程式碼。完整 ZF1 原始碼位於外部目錄 `D:\Users\WebstormProjects\php\hof-include\Zend\` 僅供參考，無法做為正式引用。

**解決方式：** 在 `hof/includes/Zend/` 下建立最小 stub，僅實作 HOF 實際呼叫到的方法：

| 檔案 | 實作內容 |
|------|---------|
| `Zend/Loader.php` | `loadClass()`, `loadFile()`, `_securityCheck()` |
| `Zend/Loader/Autoloader.php` | Singleton, `unshiftAutoloader()`, `pushAutoloader()`, `autoload()`, `getClassAutoloaders()`, `_internalAutoloader` 回退 |
| `Zend/Loader/Autoloader/Interface.php` | `Zend_Loader_Autoloader_Interface` |
| `Zend/Exception.php` | `Zend_Exception extends Exception` |
| `Zend/Date.php` | 常數 (`DATETIME_FULL`, `TIME_FULL`, `DAY`)、`__construct()`, `toString()`, `__toString()`, `getLocale()`, `getTimezone()`, `setTimezone()`, `getHour()`, `getValue()`, `sub()` |
| `Zend/Locale.php` | 最小實作 |
| `Zend/Registry.php` | `getInstance()` singleton |

**原則：** stub 僅實作 HOF 原始碼中實際呼叫到的公開方法（public methods），不包含 ZF1 的完整內部邏輯。

---

### C. include_path 問題與解決
### C. include_path Issue & Solution

**問題：** `bootstrap.php` 第 33 行 `require_once('Zend/Loader/Autoloader.php')` 找不到檔案，因為 ZF1 stub 位於 `hof/includes/Zend/`，但 PHP CLI 的預設 `include_path` 為 `.;C:\php\pear`，不包含 `hof/includes/`。

專案原始設計依賴 `hof/php.ini`：
```
include_path = "D:\...\hof\includes;.;C:\php\pear"
```

但 **PHP CLI SAPI 不自動載入 CWD 下的 php.ini**（根據 PHP 官方文件，CLI 模式的 php.ini 搜尋順序排除 Current Working Directory）。

**解決方式：** 使用 `-c <目錄>` 參數強制 PHP 載入指定目錄下的 `php.ini`：
```bash
php -c D:\path\to\hof -S 127.0.0.1:8085 -t hof
```
PHP 會在 `D:\path\to\hof\` 找到 `php.ini`，其中的 `include_path` 設定即可生效。

**補充說明：** 與直接使用 `-d include_path=...` 相比，`-c <dir>` 的優點是：
- 使用專案既有的 `hof/php.ini`，無需建立額外設定檔
- 不修改 PHP 安裝目錄下的任何檔案
- 不修改原始碼

---

### D. 伺服器啟動與日誌取得方法
### D. Server Startup & Log Retrieval

**背景：** 此專案在 Windows 環境開發。PHP 有多個版本共存於 `D:\Users\WebstormProjects\php\bin\`。專案已預先建立啟動/終止腳本。

#### 相關腳本

| 腳本 | 用途 |
|------|------|
| `hof/start_and_kill.bat` | 呼叫 PowerShell `start_and_kill.ps1` 腳本，啟動伺服器，監控 10 秒後自動關閉 |
| `hof/start_and_kill.ps1` | 啟動 PHP 內建伺服器於 port 8085，輸出與錯誤分別寫入 `server_output.log` 與 `server_error.log`，10 秒後自動終止行程 |
| `hof/start_and_kill.bat -NoKill` | 啟動 PHP 內建伺服器於 port 8085（長期背景執行，需手動關閉） |
| `hof/taskill-port.bat` | 終止 PHP 行程（批次檔，呼叫 .ps1） |
| `hof/taskill-port.ps1` | 終止 PHP 行程（PowerShell，支援 `-Port` 參數） |

#### 使用方法

**啟動並自動關閉（測試用）：**
```bash
./hof/start_and_kill.bat
```
或直接執行 PowerShell 腳本：
```powershell
powershell -ExecutionPolicy Bypass -File hof/start_and_kill.ps1
```
腳本會啟動伺服器、監控 10 秒輸出，然後自動關閉。日誌位於 `hof/server_output.log` 與 `hof/server_error.log`。

**啟動長期伺服器（除錯用）：**
```bash
./hof/start_and_kill.bat -NoKill
```
需手動關閉（Ctrl+C 或 `hof/taskill-port.bat`）。

#### 執行環境注意事項

- `start_and_kill.ps1` 使用 `-c "$PROJECT_DIR"` 載入 `hof/php.ini`，自動處理 `include_path` 設定。
- PHP CLI 的行為（如 php.ini 載入規則、.htaccess 不支援）與 Apache mod_php/CGI 有差異。

---

### E. Gzip 輸出緩衝遮蔽錯誤
### E. Gzip Output Buffering Masking Errors

**問題：** `bootstrap.php:19` 的 `ob_start('ob_gzhandler')` 在程式最前端啟動 gzip 輸出緩衝。當後續程式碼發生致命錯誤（如 `require_once` 找不到檔案）時：

1. PHP 立即終止腳本執行
2. 輸出緩衝區為空（錯誤發生在任何 `echo`/`print` 之前）
3. `ob_gzhandler` 回呼被呼叫，壓縮空內容
4. 結果為 HTTP 200 + 空主體，**完全遮蔽了錯誤訊息**

**解決方式：** 暫時註解該行，讓錯誤訊息直接輸出：
```php
/**
 * 暫時停用以顯示原始 PHP 錯誤訊息
 * 正式環境可啟用
 */
//ob_start('ob_gzhandler');
```

**後續影響：**
- 開發階段可看到原始 PHP 錯誤訊息（Fatal Error、Warning 等）
- PHP 內建伺服器（測試用）無 gzip 壓縮
- 正式環境（Apache + mod_php）可取消註解恢復 gzip，或由 Apache 的 `mod_deflate` 處理壓縮

---

## 附錄 F：操作注意事項
## Appendix F: Operational Notes

### 長期執行模式限制
### Long-running Mode Restriction

**除非有特殊需求或真正需要長期執行的伺服器，否則請勿使用 `start_and_kill.bat -NoKill` 長期執行模式。**

Do **NOT** use `start_and_kill.bat -NoKill` (long-running mode) unless there is a specific need for a persistent server. The short-lived mode (10-second auto-stop) is sufficient for checking error messages and debugging.

### PHP 二進位路徑保護
### PHP Binary Path Protection

**絕對禁止修改 `D:\Users\WebstormProjects\php\` 路徑下任何檔案。**

Modifying any files under `D:\Users\WebstormProjects\php\` is **strictly forbidden**.

所有 PHP 設定應透過以下方式達成：
- `hof/php.ini`（透過 `-c <dir>` 載入）
- `hof/start_and_kill.ps1` 的 `-d` 參數（命令行 ini 設定）
- 專案內的 stub/設定檔

---

## 附錄 G：Zend Framework 1 Stub 實作
## Appendix G: ZF1 Stub Implementation

### 為何需要 ZF1 Stub

HOF 依賴 Zend Framework 1 (ZF1) 的部分類別，但完整 ZF1 安裝包含大量檔案且非此專案所需。為最小化依賴，在 `hof/includes/Zend/` 建立僅包含實際使用到的方法的 stub。

### ZF1 Stub 清單

| Stub 檔案 | 對應 Zend 類別 | 實作方法 |
|-----------|---------------|---------|
| `Zend/Loader.php` | `Zend_Loader` | `loadClass()`, `loadFile()`, `autoload()`, `registerAutoload()` |
| `Zend/Loader/Autoloader.php` | `Zend_Loader_Autoloader` | Singleton + autoloader 註冊 |
| `Zend/Loader/Autoloader/Interface.php` | `Zend_Loader_Autoloader_Interface` | 介面定義 |
| `Zend/Exception.php` | `Zend_Exception` | 基礎例外類別 |
| `Zend/Date.php` | `Zend_Date` | 日期操作（需搭配 `HOF_Class_Date`） |
| `Zend/Locale.php` | `Zend_Locale` | 區域設定 |
| `Zend/Registry.php` | `Zend_Registry` | 靜態註冊表 |
| `Zend/Session.php` | `Zend_Session` | Session 管理（包裝原生 PHP session） |
| `Zend/Session/Namespace.php` | `Zend_Session_Namespace` | Session 命名空間容器 |

---

## 附錄 H：Symfony YAML Stub 實作
## Appendix H: Symfony YAML Stub Implementation

### 為何需要 Symfony YAML

HOF 使用 YAML 格式儲存使用者資料（使用者設定、角色資料等）。`HOF_Class_Yaml` 繼承 `Symfony_Component_Yaml_Yaml`，需要以下類別：

| Stub 檔案 | 對應類別 | 用途 |
|-----------|---------|------|
| `Symfony/Component/Yaml/Yaml.php` | `Symfony_Component_Yaml_Yaml` | 主要接口（parse/dump） |
| `Symfony/Component/Yaml/Parser.php` | `Symfony_Component_Yaml_Parser` | YAML 解析器 |
| `Symfony/Component/Yaml/Dumper.php` | `Symfony_Component_Yaml_Dumper` | YAML 產生器 |
| `Symfony/Component/Yaml/Inline.php` | `Symfony_Component_Yaml_Inline` | 行內 YAML 解析 |
| `Symfony/Component/Yaml/Escaper.php` | `Symfony_Component_Yaml_Escaper` | 字串逸出處理 |
| `Symfony/Component/Yaml/Unescaper.php` | `Symfony_Component_Yaml_Unescaper` | 字串反逸出處理 |
| `Symfony/Component/Yaml/Exception/ParseException.php` | - | 解析例外 |
| `Symfony/Component/Yaml/Exception/DumpException.php` | - | 輸出例外 |

### 實作原則

1. **不修改 `D:\Users\WebstormProjects\php\` 下任何檔案**
2. **使用 PHP 5.6.32 相容語法**（支援除 call-time pass-by-reference 外的所有 PHP 5.6 特性）
3. **盡量簡化物件導向層級**，減少複雜繼承鏈
4. **無命名空間**（使用 PEAR-style 類別名稱以相容 Zend_Loader 自動載入）
5. **修改紀錄檔案**：僅更新此文件，不另創檔案
