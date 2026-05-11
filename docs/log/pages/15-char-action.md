# 角色行動設定頁面 — Char Action (Pattern 設定)

- **網址 (URL):** `http://localhost:8085/char/action?char={char_hash}`
- **Controller:** `HOF_Controller_Char`
- **Action:** `action`
- **模板:** `tpl/char/char.judge.php`

## 功能說明

設定角色的 AI 行動模式（Action Pattern），讓角色在戰鬥中根據條件自動選擇技能。

### 頁面元素

| 元素 | 說明 |
|------|------|
| **Pattern 行 (1..N)** | 每行包含判定條件 + 數值 + 技能 + 選擇 radio |
| **判定條件下拉選單** | 「必ず」「次の判断へ」「自分のHP が○○(%)以下」等 80+ 種條件 |
| **數值輸入框** | 與判定條件搭配的數值（如 HP %、SP 量等） |
| **技能下拉選單** | 該角色已習得的技能列表 |
| **Radio 按鈕** | 選取要操作的 pattern 行（用於 Add/Delete） |
| **Set Pattern** | 儲存目前設定 |
| **Set & Test** | 儲存設定 + 立即進行 DoppelBattle 模擬戰 |
| **Switch Pattern** | 交換目前 pattern 與記憶中的 pattern |
| **Add** | 在選取的 pattern 行前插入新行 |
| **Delete** | 刪除選取的 pattern 行 |
| **Simulate** | 連結到戰鬥模擬器 |
| **位置(Position)** | 前衛(Front) / 後衛(Backs) |
| **護衛(Guarding)** | 必ず守る / 體力條件守る / 概率守る / 守らない |

### Pattern 條件種類（部分列舉）

| 條件值 | 顯示名稱 | 說明 |
|--------|---------|------|
| 1000 | 必ず | 無條件執行 |
| 1001 | 次の判断へ | 跳過，交給下一行判斷 |
| 1100 | 自分のHP が○○(%)以上 | 自身 HP 百分比以上 |
| 1101 | 自分のHP が○○(%)以下 | 自身 HP 百分比以下 |
| 1110 | 自分のHP が○○以上 | 自身 HP 絕對值以上 |
| 1111 | 自分のHP が○○以下 | 自身 HP 絕對值以下 |
| 1200 | 味方に HPが○○(%)以下のキャラ がいる | 隊友 HP 低於百分比 |
| 2101 | 自分のSP が○○(%)以下 | 自身 SP 百分比以下 |
| 5000 | 自分が 前列 | 自身在前列時 |
| ... | ... | 共 80+ 種判斷條件 |

### Pattern 數量限制

Pattern 數量由角色 INT 屬性決定：
- INT < 10: 2 個
- INT ≥ 10: 3 個
- INT ≥ 15: 4 個
- INT ≥ 30: 5 個
...
- Lv > 29: 額外 +1

### 操作流程

1. 從角色選單點擊角色名稱 → Action
2. 在 Pattern 行中選擇判斷條件、輸入數值、選擇技能
3. 點擊「Set Pattern」儲存

## 已知問題

- **BUG-003**: Pattern 設定後重新載入頁面恢復為預設值（YAML 已正確寫入但載入顯示異常）
- DoppelBattle 中怪物傷害為 0，導致 HP 條件模式永遠不會觸發

## 技能資料

| 技能 ID | 名稱 | SP | 目標 | 類型 | 效果 |
|---------|------|----|------|------|------|
| 1000 | Attack | 0 | enemy/individual | physical | 通常攻擊 |
| 1001 | Bash | 8 | enemy/individual | physical | 160% 傷害 |
| 3120 | FirstAid | 0 | self/individual | physical/support:1 | 自己HP回復 |

## 測試日期

2026-05-10

## 注意事項

- 「Set & Test」會先儲存 pattern，然後執行 DoppelBattle（單人模擬戰）
- DoppelBattle 中 HP 不會減少（怪物傷害為 0），因此 HP 條件永遠不會觸發
- Pattern 中的 `judge` 欄位儲存為數值 ID（如 1101），對應到 `HOF/Resource/Judge/` 下的 YAML 定義
