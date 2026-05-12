# 戰鬥日誌 YAML 快取損毀與渲染錯誤分析報告
# Battle Log YAML Cache Corruption and Rendering Error Analysis Report

**日期 (Date):** 2026-05-13  
**狀態 (Status):** 已解決 (Resolved)  
**類別 (Category):** 併發競爭、資料結構、檔案鎖定 (Concurrency, Data Structure, File Locking)

---

## 1. 問題描述 / Problem Description

在使用遊戲戰鬥功能並頻繁刷新頁面（F5）後，`/log` 頁面出現嚴重的 UI 渲染錯誤，並伴隨以下現象：
1.  **PHP 警告 (PHP Warning)**：`Illegal string offset` 發生於 `HOF_Class_Icon::getImage()`。
2.  **YAML 結構損毀**：`cache.icon_cache.yml` 中出現大量巢狀且被轉義的字串 Key（例如 `"data: \"...\""`），而非正確的陣列結構。
3.  **圖示 404 錯誤**：部分怪物圖示（如 `mon_145.png`）被錯誤地偵測為 `.gif`，導致前端無法載入。

## 2. 根因分析 / Root Cause Analysis

### A. 非遞迴陣列轉換 (Non-recursive Array Conversion)
`HOF_Class_Array::toArray()` 原始實作僅處理第一層，當資料結構中包含巢狀的 `HOF_Class_Array` 物件時，這些物件會被傳入 YAML 序列化器。Symfony YAML 序列化器無法正確處理這些物件，進而將其轉義為長字串，導致快取結構從「陣列」退化為「損毀字串」。

### B. 快取 Key 歧義 (Cache Key Ambiguity)
`HOF_Class_Icon` 直接使用帶有 `./` 或尾斜槓的目錄路徑（如 `./static/image/char/`）作為 YAML Key。這種寫法在某些解析環境下會產生歧義，且 `getImageList` 與 `getImage` 之間存取 Key 不一致，導致重複寫入與偵測失效。

### C. 檔案鎖定逾時過短 (Insufficient File Lock Timeout)
在高頻率刷新頁面時，多個進程同時競爭同一個快取檔案。原系統僅提供 5 次（約 0.05 秒）的鎖定重試，逾時後可能導致檔案讀取不完整或寫入衝突，加劇了 YAML 結構的損毀。

---

## 3. 修復方案 / Resolution

### A. 重構陣列轉換邏輯
*   **檔案**: `hof/trust_path/HOF/Class/Array.php`
*   **變更**: 將 `toArray()` 改為遞迴實作，確保所有層級的物件皆轉換為純陣列。

### B. 規範化快取 Key
*   **檔案**: `hof/trust_path/HOF/Class/Icon.php`
*   **變更**: 引入 `_normalizeDirKey()`，移除路徑頭尾的 `./` 與 `/`（例如 `static/image/char`）。同步更新 `getImageList` 與 `getImage` 使用規範化後的 Key。

### C. 強化併發保護
*   **檔案**: `hof/trust_path/HOF/Class/File.php`
*   **變更**: 將 `fplock` 的重試次數提升至 **50 次**（總逾時約 0.5 秒），有效應對 F5 連續刷新帶來的併發壓力。

### D. 防禦性類型保護
*   **檔案**: `hof/trust_path/HOF/Class/File/Cache.php`
*   **變更**: 在載入 YAML 後增加型別檢查，若解析結果非陣列則強制重設，防止損毀資料擴散。

---

## 4. 驗證結果 / Verification

1.  **壓力測試**: 模擬連續 F5 刷新觸發戰鬥，`cache.icon_cache.yml` 保持結構正確，無新增 PHP 警告。
2.  **資源偵測**: `mon_145.png` 已能被正確識別並記錄於快取，不再發生 `gif` 404 錯誤。
3.  **UI 恢復**: `/log` 頁面渲染正常，所有角色與動作圖示顯示正確。

---

## 5. 後續建議 / Recommendations

1.  **定期監控日誌**: 若 `/log` 頁面再次出現偏移錯誤，優先檢查 `trust_path/cache/log/` 目錄下的 `.dat` 檔案是否混入了 PHP 錯誤輸出。
2.  **YAML 規範**: 未來新增快取邏輯時，應確保 Key 值不包含特殊字元，並優先使用規範化路徑。
