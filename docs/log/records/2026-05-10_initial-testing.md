# 首次測試紀錄 — Initial Testing

- **日期:** 2026-05-10
- **目的:** 修復登入與角色建立流程 Bug，驗證遊戲可正常運行

---

## 操作 1：登入遊戲

| 項目 | 內容 |
|------|------|
| **步驟** | 開啟 `http://localhost:8085/` |
| **帳號** | `demo` / `demo`（已存在） |
| **結果** | ✅ 成功登入，顯示首頁（團隊名稱 TestTeam，角色 Hero1 Lv.1 Warrior） |
| **Console Errors** | 無 PHP errors/warnings |

## 操作 2：瀏覽狩獵頁面

| 項目 | 內容 |
|------|------|
| **步驟** | 點擊導航列「Hunt」連結 |
| **結果** | ✅ 成功載入狩獵入口頁面 |
| **Console Errors** | 無 |

## 操作 3：瀏覽普通怪物列表

| 項目 | 內容 |
|------|------|
| **步驟** | 點擊「CommonMonster」連結 |
| **結果** | ✅ 成功載入 16 個狩獵區域列表 |
| **Console Errors** | 無 |

## 操作 4：進入戰鬥準備頁面

| 項目 | 內容 |
|------|------|
| **步驟** | 點擊「ゴブリンと遊ぶ(最弱)」(Lv1) |
| **結果** | ✅ 成功載入戰鬥準備頁面，顯示我方角色與敵方怪物 |
| **Console Errors** | 無 |

## 操作 5：嘗試開始戰鬥（未勾選角色）

| 項目 | 內容 |
|------|------|
| **步驟** | 直接點擊「Battle !按鈕」 |
| **結果** | ✅ 顯示「戦闘するには最低1人必要」（預期行為，需先勾選角色） |
| **Console Errors** | 無 |

---

## 修復摘要

| Bug | 檔案 | 修復 |
|-----|------|------|
| `setTimestamp()` undefined | `Zend/Date.php` | 新增方法 |
| `Illegal string offset 'judge'` | `HOF/Class/Char/Pattern.php:219,229` | `is_array()` 提前檢查 |
| YAML 回傳非陣列 | `HOF/Class/File/Cache.php:53` | `is_array()` 防衛 |
