# Hall of Fame — 任務追蹤清單

> 建立日期：2026-05-10
> 最後更新：2026-05-10

---

## BUG-001: Data.php:418 foreach() Warning

| 欄位 | 內容 |
|------|------|
| 狀態 | ✅ **已修復** |
| 優先級 | **高** |
| 檔案 | `HOF/Model/Data.php` |
| 函式 | `getLandAppear()` |
| 行號 | 390 與 418 |
| 類型 | Warning / 潛在 Crashi |
| 症狀 | `foreach() argument must be of type array|object, string given` in `getLandAppear()` |
| 根因 | `(array)` 轉型 wrap 後的內層值可能是純量（如字串），導致內層 `foreach($_data as $_k => $_v)` 收到非陣列 |
| 修復方案 | 在兩處 inner foreach 前加入 `if (is_array($_data))` 守衛條件 |
| 相關函式 | `getAll()` (同檔案), HOF 的 Model/Data YAML 緩存層 |
| 修復日期 | 2026-05-10 |
| 紀錄檔案 | `docs/log/issues/2026-05-10-BUG-001.md` |

---

## BUG-002: char.judge.php 模板路徑錯誤

| 欄位 | 內容 |
|------|------|
| 狀態 | 🟡 **調查中（正常流程無法重現）** |
| 優先級 | **高** |
| 症狀 | `include(...tpl//char.judge.php): failed to open stream` |
| 錯誤路徑 | `tpl/char.judge.php`（缺少子目錄 `char/`）|
| 實際路徑 | `tpl/char/char.judge.php` |
| 根本原因 | View.php `_getTplFile()` 產生的路徑不含子目錄 |
| 影響範圍 | 使用 `?char=hash` 參數進入 Judge 頁面時會報錯 |
| 分析結果 | `char.action.php` 透過 `$this->slot('char/char.judge')` 正確載入（含子目錄）。導覽列中無 direct judge 連結。`Gamedata` 控制器有獨立 `_main_action_judge()`，使用 `tpl/gamedata.judge.php`（正常）。正常流程下無法重現此錯誤。 |
| 是否需要 View.php 原始碼調查 | 否（流程已釐清）|
| 備註 | 可能為非預期操作路徑（如直接輸入 URL `?controller=char&action=judge&char=hash`）導致 action 設為 `judge` 但無對應處理方法，經由 `_main_view()` 自動產生了 `char.judge` 模板名稱 |

---

## BUG-003: AI Pattern 設定後戰鬥未觸發技能

| 欄位 | 內容 |
|------|------|
| 狀態 | 🔴 **未修復** |
| 優先級 | **高** |
| 設定確認 | 已儲存（收到「パターン設定保存 完了」） |
| 症狀 | 戰鬥中 Hero1 (Lv.2 Warrior) 只普通攻擊，從不使用任何技能 |
| Pattern 設定 | Pattern 1: HP <= 25% → Skill "回復の祈り" |
| | Pattern 2: HP <= 50% → Skill "回復の祈り" |
| 測試戰鬥 | Lv.1 ゴブリン / Lv.3 シルバーウルフ / Lv.5 フレイムスピリット（均未觸發） |
| 預調查事項 | 1️⃣ 先確認角色資料檔案中 pattern 設定是否真正被持久化儲存 |
| | 2️⃣ 再深入 Battle.php 行動選擇引擎的 AI 決策邏輯 |
| | 3️⃣ 檢查 skill ID 是否正確對應（"回復の祈り"） |
| 相關檔案 | `HOF/Class/Battle.php`, `HOF/Controller/Char.php`, 角色資料檔案 |

---

## TASK-001: Pattern 設定確認（角色資料檔案檢查）

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **高** |
| 說明 | 在修復模板路徑 BUG-002 前，需直接讀取角色儲存檔案確認 pattern 是否有被寫入 |
| 預期資料位置 | `trust_path/user/` 或 `trust_path/dat/` 下的角色 hash 對應檔案 |

---

## TASK-002: 修正 View.php 模板路徑

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **高** |
| 步驟 | 1. 讀取 View.php template_file 計算邏輯 |
| | 2. 確認為何 `char.judge` 需要從 `tpl/` 而非 `tpl/char/` 載入 |
| | 3. 比對其他 `char\.*` 模板的載入方式 |
| | 4. 修復路徑計算 |

---

## TASK-003: 修正 Data.php:418 foreach() Warning

| 欄位 | 內容 |
|------|------|
| 狀態 | ✅ **已完成** |
| 優先級 | **高** |
| 說明 | 在 `getLandAppear()` 函式的兩處 inner foreach 加入 `is_array()` 守衛 |
| 完成日期 | 2026-05-10 |

---

## TASK-004: 深入調查 Battle.php AI 決策邏輯

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **高**（僅在確認 Pattern 儲存成功後執行）|
| 範圍 | 行動選擇引擎、Pattern 讀取、skill ID 對應、回合觸發條件 |

---

## TASK-005: 經驗值系統分析 + 全員練等

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **中** |
| 目標 | 1. 理解 EXP 計算公式（等級差、地圖效益、升級曲線） |
| | 2. 全員練等至 Lv.5+ 以解鎖 Job Change 測試 |
| 副作用 | 練等過程可反覆觸發 AI Pattern，利於偵錯 |

---

## TASK-006: Funds 顯示異常

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **低** |
| 症狀 | 招募 Priest1 花費 $250 後 Funds 仍顯示 $42,680（原始值） |
| 調查方向 | 需確認 Funds 是否有獨立於角色之外的全局儲存位置，或招募費用是否有扣款延遲 |

---

## TASK-007: docs/log/ 目錄維護

| 欄位 | 內容 |
|------|------|
| 狀態 | ⏳ **待處理** |
| 優先級 | **低** |
| 現有檔案 | `docs/log/pages/06-char-judge.md`（已建立）|
| 待補 | 戰鬥系統操作記錄、管理後台操作記錄 |

---

## 進展記錄

```
2026-05-10 Phase 1: AI Pattern 設定完成（介面操作+確認）
2026-05-10 Phase 2: 三場戰鬥測試 → Pattern 未觸發
2026-05-10 Phase 3: 發現 Data.php Warning + View.php 模板路徑錯誤
2026-05-10 Phase 4: 建立任務追蹤清單
2026-05-10 Phase 5: BUG-001 修復完成 (Data.php foreach is_array guard)
2026-05-10 Phase 5: BUG-002 分析完成 (正常流程無法重現, slot 路徑正確)
2026-05-10 Phase 5: 開始 TASK-001 Pattern 持久化檢查 + BUG-003 Battle AI 調查
```
