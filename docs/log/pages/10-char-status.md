# 角色狀態頁 — Character Status

- **網址 (URL):** `http://localhost:8085/char/char?char={角色ID}`
- **進入路徑:** Top 頁點擊角色圖示、或導航列「Setting」旁的 Char 連結
- **Controller:** `HOF_Controller_Char`

## 功能說明

顯示單一角色的完整狀態數值。

### 角色資訊欄

| 欄位 | 說明 |
|------|------|
| **角色名稱** | 建立時設定的名稱 |
| **Lv** | 當前等級 / Exp 顯示如 `20/30` |
| **職業** | 當前職業名稱（如 Warrior） |
| **HP** | 當前 HP / 最大值 |
| **SP** | 當前 SP / 最大值 |
| **STR** | 力量 — 影響物理傷害 |
| **INT** | 智力 — 影響魔法傷害與治療量 |
| **DEX** | 靈巧 — 部分技能改用 DEX 計算傷害 |
| **SPD** | 速度 — 影響行動順序 |
| **LUK** | 幸運 — 影響爆擊/掉落 |

### 戰鬥中素質計算

```
戰鬥 STR = 基礎 STR + 裝備 P_STR（無百分比加成）
戰鬥 INT = 基礎 INT + 裝備 P_INT
戰鬥 MAXHP = round(基礎 maxhp × (1 + M_MAXHP/100) + P_MAXHP)
```

### 子頁面連結

| 連結 | URL | 說明 |
|------|-----|------|
| **Stup** | `/char/stup?char={ID}` | 配點 — 分配升級獲得的潛在能力點 |
| **Action** | `/char/action?char={ID}` | 行動設定 — 戰鬥方針與技能配置 |
| **Equip** | `/char/equip?char={ID}` | 裝備管理 |
| **SkillLearn** | `/char/skill_learn?char={ID}` | 技能學習 |
| **JobChange** | `/char/job_change?char={ID}` | 轉職 |

### 注意事項

- 顯示的 HP/SP 為當前值，戰鬥後自動回滿（敗北時也回滿）
- 角色被 Kick 後無法復原
- LUK 的具體影響公式尚待分析

## 測試日期

2026-05-10
