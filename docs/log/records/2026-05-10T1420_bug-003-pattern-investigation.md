# BUG-003 Pattern 持久化問題 — 瀏覽器測試記錄

> 日期：2026-05-10
> 操作者：Shadow Monarch
> 目標：驗證 AI Pattern 設定是否能持久化儲存

---

## 操作記錄

### 步驟 1: 開啟角色行動頁面

- **網址**: `http://localhost:8085/char/action?char=b3e304903f09e14b8386a49a1e1e01e3`
- **角色**: Hero1 (Lv.2 Warrior, INT=2, HP=342, SP=52)
- **觀察**: Pattern 顯示為預設值（必ず→Attack）× 2 行

### 步驟 2: 設定 Pattern 並儲存

設定 Pattern 1:
- 判斷條件: 自分のHP が○○(%)以下
- 數值: 25
- 技能: FirstAid

設定 Pattern 2:
- 判斷條件: 自分のHP が○○(%)以下
- 數值: 50
- 技能: FirstAid

**操作**: 點擊「Set Pattern」按鈕

**結果**: ✅ 頁面顯示「パターン設定保存 完了」
- Row 1: judge="自分のHP が○○(%)以下", quantity=25, skill="FirstAid - (SP: 0)"
- Row 2: judge="自分のHP が○○(%)以下", quantity=50, skill="FirstAid - (SP: 0)"

### 步驟 3: 讀取角色 YAML 檔案（確認持久化）

**檔案**: `hof/trust_path/dat/user/demo/char.b3e304903f09e14b8386a49a1e1e01e3.yml`

**結果**: ✅ YAML 已正確寫入
```yaml
behavior:
  pattern: [{judge: "1101", quantity: "25", action: "3120"}, {judge: "1101", quantity: "50", action: "3120"}]
```

### 步驟 4: 重新載入頁面（測試持久化）

**操作**: 導覽至 `http://localhost:8085/` 再重新點擊 Hero1 → Action

**結果**: ❌ **Pattern 恢復為預設值**（必ず→Attack）× 2 行

### 步驟 5: 再次讀取 YAML 檔案（確認未被覆寫）

**結果**: ✅ YAML 檔案**仍然保留正確的 Pattern 資料**
```yaml
behavior:
  pattern: [{judge: "1101", quantity: "25", action: "3120"}, {judge: "1101", quantity: "50", action: "3120"}]
```

---

## 結論

| 檢查項目 | 結果 |
|---------|------|
| POST 儲存是否成功 | ✅ 成功 |
| YAML 是否正確寫入 | ✅ 正確寫入 |
| YAML 是否被覆寫 | ✅ 未被覆寫，資料保留 |
| GET 重新載入後顯示 | ❌ 恢復為預設值 |

**BUG 位置確認**: 問題不在 SAVE 流程（YAML 正確寫入），而是在 LOAD/顯示流程（GET 請求時讀取到錯誤的 pattern 資料）。

---

## 相關截圖

無（本輪測試未擷取螢幕截圖）

---

## 參考

- BUG-003 詳細分析: `docs/log/issues/2026-05-10-BUG-003.md`
- Action Pattern 頁面說明: `docs/log/pages/15-char-action.md`
- 任務追蹤: `docs/task/MAIN-TASKS.md`
