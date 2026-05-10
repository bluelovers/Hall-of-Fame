# 配點頁 — Stup (Status Up)

- **網址 (URL):** `http://localhost:8085/char/stup?char={角色ID}`
- **進入路徑:** Char Status → Stup 連結
- **Controller:** `HOF_Controller_Char`

## 功能說明

分配升級獲得的潛在能力點數（Potential Ability），提升角色基礎素質。

### Potential Ability 系統

| 項目 | 說明 |
|------|------|
| **獲取方式** | 升級時獲得 1 點 |
| **消耗** | 投入至 STR/INT/DEX/SPD/LUK |
| **未分配點數** | 顯示於頁面（如「Potential Ability: 1」） |
| **重置** | 無法重置 — 投入即永久 |

### 配點流程

1. 頁面顯示當前五圍（STR/INT/DEX/SPD/LUK）
2. 每個素質旁的輸入框可輸入欲分配點數（預設 1）
3. 預覽區域顯示分配後的數值變化
4. 點擊「StatusUp」按鈕確認分配

### 各素質效果

| 素質 | 主要效果 | 次要效果 |
|:----:|---------|---------|
| **STR** | 物理傷害基礎 `sqrt(STR)×10` | 少許 MAXHP |
| **INT** | 魔法傷害/治療基礎 `sqrt(INT)×10` | 少許 MAXSP |
| **DEX** | 部分技能（Shoot 等）改用 DEX 計算 | 命中/迴避 |
| **SPD** | 戰鬥行動順序 | — |
| **LUK** | 爆擊率、掉落率 | — |

### 重要 — 邊際效益遞減

```
STR 10 → 20: Base +10.0（100%↑）
STR 20 → 30: Base +3.5（17.5%↑）
STR 30 → 40: Base +2.0（8.5%↑）
STR 100 → 110: Base +0.5（0.5%↑）
```

由於傷害公式使用 `sqrt(Stat)`，**大量投資單一素質的邊際效益極低**。

### 建議配點策略

| 職業 | 優先素質 | 說明 |
|------|---------|------|
| Warrior | STR 到 20+ → DEX | STR 邊際效益下降後改點 DEX |
| Sorcerer | INT 到 20+ → SPD | 追求先手魔法爆發 |
| Priest | INT 到 15+ 均分 | 治療受 INT 影響，但無需極限 |
| Hunter | DEX 到 25+ | 技能全依存 DEX |

### 注意事項

- 總配點上限受 `MAX_STATUS=250` 限制（單一素質上限亦為 250）
- Stup 分配的點數與 Str/Int 等潛能值直接相關，**對戰鬥有顯著影響**
- 建議先滿足武器裝備的 STR/INT 需求後再分配其餘點數

## 測試日期

2026-05-10
