# 戰鬥系統 / 技能系統 / 職業系統 — 操作指南

> 記錄日期：2026-05-10
> 測試版本：develop5 分支

---

## 目錄

1. [戰鬥系統總覽](#1-戰鬥系統總覽)
2. [狩獵流程](#2-狩獵流程)
3. [怪物等級動態調整機制](#3-怪物等級動態調整機制)
4. [技能系統](#4-技能系統)
5. [職業系統](#5-職業系統)
6. [Union 公會怪物戰](#6-union-公會怪物戰)
7. [Ranking 排名戰](#7-ranking-排名戰)
8. [Colosseum 競技場](#8-colosseum-競技場)
9. [相關原始碼架構](#9-相關原始碼架構)

---

## 1. 戰鬥系統總覽

HOF 的戰鬥系統為**回合制隊伍戰鬥**，採用自動進行模式（玩家設定 AI 策略後自動執行）。

### 基本概念

| 項目 | 說明 |
|------|------|
| **隊伍** | 玩家可選擇 1~5 名角色組成隊伍 |
| **敵方** | 根據狩獵區域隨機生成 1~5 名怪物 |
| **回合** | 自動執行，每回合所有單位依速度順序行動 |
| **Time 消耗** | 每場戰鬥消耗 `NORMAL_BATTLE_TIME`（預設 100 Time） |
| **勝利條件** | 敵方全滅 |
| **敗北條件** | 我方全滅（不會 Game Over，可使用道具恢復） |

### 進入戰鬥

```
Top Page → [Hunt] → 選擇狩獵區域 → 選擇隊伍成員 → [この場所で狩る]
```

**狩獵區域列表** — 由 YAML 資源檔定義，各區域包含不同怪物組合與權重。部分區域需持有特定鑰匙道具才能進入（如 land.snow2.yml 需要 item 8011）。

> ### ⚠️ 關於 `proper` / 區域等級標示
>
> Land YAML 的 `proper` 欄位決定 Hunt 列表右側顯示的等級範圍文字，但有部分區域（如火山、海中、砂漠、沼、雪原等特殊區域）的 `proper` 設定為 `Lv??`。
>
> **這是故意設計，不是 Bug。** 這些區域刻意不顯示等級，可能是為了：
> - 隱藏該區域的難度讓玩家自行探索
> - 該區域的怪物等級動態調整幅度較大，顯示固定範圍無意義
> - 劇情/神秘區域的設計考量
>
> **不需修改 `proper: 'Lv??'` 的設定。**

---

## 2. 狩獵流程

### 步驟

1. **選擇區域**：`/battle/hunt` — 顯示可進入的狩獵區域列表
2. **選擇隊伍**：從已僱用的角色中選擇 1~5 人
3. **執行戰鬥**：系統隨機選擇怪物 → 建立敵方隊伍 → 自動進行回合制戰鬥
4. **結果畫面**：顯示戰鬥日誌、獲得 EXP 與金錢

### 戰場構成

```
[前列]     [後列]
我方角色   我方角色
敵方怪物   敵方怪物
```

- 前列角色受到更多攻擊
- 後列角色攻擊力降低但較安全
- 部分技能有前列/後列限制

### 戰鬥行動

角色根據設定的 **AI 判定條件（Judge）** 自動選擇行動：

| 行動類型 | 說明 |
|---------|------|
| **通常攻擊** | 對敵方單體造成物理傷害 |
| **技能使用** | 消耗 SP 發動技能（如 Bash、DoubleAttack、Stab 等） |
| **防禦** | 減少受到的傷害 |
| **道具使用** | 使用携带的道具 |
| **逃跑** | 脫離戰鬥 |

### 戰後獎勵

- **EXP**：依怪物強度與數量計算（受 `EXP_RATE` 倍率影響）
- **金錢**：依怪物掉落計算（受 `MONEY_RATE` 倍率影響）
- **掉落物**：低機率獲得怪物持有的物品

---

## 3. 怪物等級動態調整機制

### 結論：**此版本具有完整的自動等級調整機制**

怪物等級並非固定，而是根據玩家隊伍中**最高等級成員**動態調整。

### 核心調整函式：`EnemyParty()`

**檔案：** `hof/trust_path/HOF/Controller/Battle.php` (L530-582)

```php
function EnemyParty($Amount, $MonsterList, $Specify = false)
```

#### 兩種調整模式

| 模式 | 條件 | 公式 | 效果 |
|------|------|------|------|
| **積極調整改** | 玩家最高等級 > 怪物基礎等級 + 10 | `mt_rand(floor((top-b)/3), round(top-b+5))` | 大幅度補正等級差距 |
| **微調模式** | 玩家最高等級 ≤ 怪物基礎等級 + 10 | 從 `[-3,-2,-1,0,0,0,1,1,1,2,3,4,5]` 隨機選取 | 小幅度隨機調整 |

#### 判斷依據：`top_level`

**檔案：** `hof/trust_path/HOF/Controller/Battle.php` (L397-415)

`top_level` = 玩家選擇的隊伍成員中**最高等級**（非平均、非總和）

```php
function MyParty()
{
    $this->_cache['top_level'] = 0;
    foreach ((array)$this->input->input_char_id as $k)
    {
        $i = max($i, $this->user->char[$k]->level);
    }
    $this->_cache['top_level'] = $i;
}
```

#### 素質調整函式：`level_fix()`

**檔案：** `hof/trust_path/HOF/Class/Char/Abstract.php` (L695-748)

當怪物等級調整後，透過 `level_fix($lv_add)` 重新計算所有素質：

```
new_stat = ceil(old_stat * (new_level / old_level))
```

- 當等級增加超過 10 倍時：加入 **50%~175% 的隨機變異**
- 調整的素質：STR、INT、DEX、SPD、LUK
- HP/SP 也自動重新計算

#### 敵方數量調整：`EnemyNumber()`

**檔案：** `hof/trust_path/HOF/Controller/Battle.php` (L513-524)

敵方數量根據我方隊伍人數動態決定：

```
我方 1 人 → 敵方 1 人
我方 3 人 → 敵方 3~4 人（若 top_level > 5）
我方 5 人 → 敵方 5 人
```

> 設定常數 `ENEMY_INCREASE = 1` 控制當玩家等級 > 5 時是否增加 1 名敵方。

### 怪物資料來源

怪物基礎資料儲存於 YAML 資源檔：

```
hof/trust_path/HOF/Resource/Mon/mon.{id}.yml
```

**範例：**

| 檔案 | 怪物名稱 | 基礎等級 | 基礎 HP |
|------|---------|---------|---------|
| mon.1000.yml | GoblinAxe | Lv.1 | 140 |
| mon.1050.yml | DarkElfHunter | Lv.39 | 580 |

> 基礎等級是調整的基準點，實際戰鬥時的等級會依上述公式動態計算。

### 注意：區域解鎖條件

狩獵區域的**解鎖**是依據持有特定道具（鑰匙），而非玩家等級。因此低等玩家理論上可進入高等區域，但怪物會因 `top_level` 較低而保持接近基礎等級。

---

## 4. 技能系統

### 技能一覽

可從以下頁面查看完整技能列表：
- **遊戲內：** `/gamedata/job`（按職業分類）
- **管理頁面：** `/admin.php?menu=other` → スキル一覧

### 技能分類

| 分類 | 說明 | 範例 |
|------|------|------|
| **物理攻擊** | 消耗 SP 造成物理傷害 | Attack、Bash、DoubleAttack、Stab |
| **強化輔助** | 提升自身能力 | Reinforce（提升 ATK） |
| **恢復** | 恢復 HP | FirstAid |
| **特殊** | 特殊效果 | FatalStab（高爆擊）、Charge（蓄力） |

### 技能學習

**網址：** `/char/skilllearn/{角色ID}`

- 每個技能需要消耗 **Skill Point**
- Skill Point 透過**升級**獲得（每升 1 級獲得 1 點）
- 部分技能有**職業限制**（如 Warrior 可學 Bash，Magician 可學魔法）
- 已學習的技能顯示為「■」標記

### 技能在戰鬥中的使用

角色的行動由 **AI 判定條件（Judge）** 控制：

```
Judge 系統：
條件編號 1200 → 自己的 SP 在 XX% 以上 → 使用技能
條件編號 900  → 必定執行 → 通常攻擊
```

AI 會依序檢查 Judge 列表，符合條件的第一個行動被執行。

### 技能相關原始碼

| 檔案 | 說明 |
|------|------|
| `HOF/Class/Skill.php` | 技能基底類別 |
| `HOF/Class/Skill/Effect.php` | 技能效果實作 |
| `HOF/Class/Skill/Type/` | 各類型技能 |
| `HOF/Resource/Skill/` | 技能 YAML 資源檔 |
| `HOF/Resource/Skilltree/` | 技能樹 YAML 資源檔 |

---

## 5. 職業系統

### 職業一覽

可從以下頁面查看完整職業列表：
- **遊戲內：** `/gamedata/job`
- **管理頁面：** `/admin.php?menu=other` → 職業一覧

### 職業特性

每個職業具有以下屬性（定義於 YAML 資源檔）：

| 屬性 | 說明 |
|------|------|
| **名稱** | 職業顯示名稱 |
| **裝備欄位** | 可裝備的武器/防具類型（Weapon/Armor/Helm/Accessory） |
| **可學習技能** | 該職業可學習的技能列表 |
| **基礎素質** | STR/INT/DEX/SPD/LUK 的初始值與成長率 |
| **Rank** | 轉職階級（初級/上級） |

### 轉職系統 (JobChange)

**網址：** `/char/jobchange/{角色ID}`

- **無職業者（Lv.1）**：顯示「None.」— 無法轉職
- **基礎職業**：達到指定等級後可轉職為上級職業
- 轉職後可學習該職業的專屬技能
- 轉職不重置等級與素質

### 職業資源檔案

```
hof/trust_path/HOF/Resource/Char/{職業ID}.yml
```

---

## 6. Union 公會怪物戰

Union（公會怪物）是遊戲中的 Boss 戰系統，與普通狩獵不同：

### 進入條件

- 可從 `/battle/hunt` 頁面選擇 Union 標籤
- 需滿足特定條件（如持有對應道具）
- **等級上限**：Union 怪物有 `lv_limit`，**限制玩家隊伍總等級**不可超過

### 等級限制機制（與普通怪相反）

**檔案：** `hof/trust_path/HOF/Controller/Battle.php` (L275-279)

```php
if ($Union->lv_limit < $TotalLevel) {
    // ERROR: 合計レベルオーバー(XX/YY)
    // 隊伍總等級超過限制，無法挑戰
}
```

### 戰鬥流程

1. 系統讀取 Union 怪物資料（包含本體 + 隨從）
2. 隨從透過 `EnemyParty()` 同樣經過等級調整
3. 戰鬥消耗 `UNION_BATTLE_TIME`（預設 10 Time，低於普通戰鬥）
4. 結束後需等待 `UNION_BATTLE_NEXT`（預設 1200 秒）才能再次挑戰

---

## 7. Ranking 排名戰

### 基本規則

| 項目 | 值 |
|------|-----|
| **隊伍設定周期** | 48 小時（`RANK_TEAM_SET_TIME`） |
| **敗北等待時間** | 24 小時（`RANK_BATTLE_NEXT_LOSE`） |
| **勝利等待時間** | 60 秒（`RANK_BATTLE_NEXT_WIN`） |
| **戰鬥日誌保存數** | 100 筆（`MAX_BATTLE_LOG_RANK`） |

### 進行方式

**網址：** `/rank`

1. 需要先設定排名戰隊伍
2. 系統會配對其他玩家的隊伍進行自動對戰
3. 排名根據勝敗記錄計算
4. 戰勝與戰敗後的等待時間不同

---

## 8. Colosseum 競技場

**網址：** `/rank`（與 Ranking 共用控制器）

- 顯示當前排名與勝敗記錄
- 可查看其他玩家的隊伍配置
- 可查閱歷史戰鬥記錄

---

## 9. 相關原始碼架構

### 主要檔案

| 檔案 | 用途 | 關鍵函式 |
|------|------|---------|
| `HOF/Controller/Battle.php` | 戰鬥控制器 | `HuntProcess()`, `EnemyParty()`, `MyParty()`, `UnionProcess()`, `EnemyNumber()` |
| `HOF/Class/Battle.php` | 戰鬥引擎 | 回合處理、傷害計算、狀態管理 |
| `HOF/Class/Battle/Skill.php` | 技能在戰鬥中的處理 | `skill_active()`, `skill_use()` |
| `HOF/Class/Skill.php` | 技能基底類別 | 技能效果定義 |
| `HOF/Class/Skill/Effect.php` | 技能效果實作 | `Damage()`, `Heal()`, `Buff()` |
| `HOF/Class/Char/Abstract.php` | 角色基底 | `level_fix()`, `hpsp()` |
| `HOF/Class/Char/Pattern.php` | AI 模式處理 | Judge 條件判斷 |
| `HOF/Model/Char.php` | 角色模型 | `newMon()`, `newUnion()` |
| `HOF/Helper/Battle.php` | 戰鬥輔助函式 | 各類計算公式 |

### 資料流

```
玩家選擇 Hunt
    ↓
Battle::HuntProcess()
    ├─ Data::getLandAppear() → 取得可選區域
    ├─ 玩家選擇隊伍成員
    ├─ MyParty() → 設定 _cache['top_level']
    ├─ 隨機選擇怪物 (from Land YAML)
    ├─ EnemyParty() → 建立敵方隊伍（含等級調整）
    │   ├─ level_fix() → 調整怪物素質
    │   └─ EnemyNumber() → 決定敵方數量
    ├─ 執行回合制戰鬥
    └─ 顯示結果（EXP、金錢、掉落物）
```

---

---

## 10. 傷害計算公式（完整版）

> 記錄日期：2026-05-10
> 原始碼：`HOF/Class/Skill/Effect.php:670-748` — `CalcBasicDamage()`

### 10.1 物理傷害

```
Base  = sqrt(STR) × 10 + WeaponATK[0]
        (若技能 inf = "dex"，改用 sqrt(DEX) × 10)
Raw   = Base × (SkillPow / 100) × 額外倍率
減傷  = Raw × (1 - TargetDEF[0]/100) - TargetDEF[1]
最終  = ceil( max(減傷 + Pierce, Raw × 10%) )
```

**變數說明：**

| 變數 | 來源 | 說明 |
|------|------|------|
| `STR` | 角色 STR + 裝備 P_STR | 力量，物理傷害基礎 |
| `WeaponATK[0]` | 裝備累加 `atk[0]` | 武器物理攻擊力 |
| `SkillPow` | YAML skill 的 `pow` 欄位 | 技能倍率（如 160=160%） |
| `TargetDEF[0]` | 目標 `def[0]` | 物理防禦百分比減傷 |
| `TargetDEF[1]` | 目標 `def[1]` | 物理固定減傷 |
| `Pierce` | 特殊能力 `Pierce[0]` | 無視防禦追加傷害 |

### 10.2 魔法傷害

```
Base  = sqrt(INT) × 10 + WeaponATK[1]
Raw   = Base × (SkillPow / 100) × 額外倍率
減傷  = Raw × (1 - TargetDEF[2]/100) - TargetDEF[3]
最終  = ceil( max(減傷 + Pierce, Raw × 10%) )
```

### 10.3 治療公式

```
Heal = ceil( (sqrt(INT) × 10 + WeaponATK[1]) × (SkillPow / 100) )
```

### 10.4 多段技能傷害

部分技能（如 RagingBlow, FireBall, FireStorm）為多段攻擊，每段獨立計算傷害：

```
總傷害 = 每段傷害 × 段數
```

| 技能 | 段數 | 單發 pow | 總倍率 | 備註 |
|------|:---:|:--------:|:------:|------|
| FireBall | 4 | 100% | **400%** | 魔法，charge 60 |
| FireStorm | 6 | 100% | **600%** | 魔法，charge 70，需 Lv.4 |
| RagingBlow | 5 | 100% | **500%** | 物理，charge 40/60，需 Lv.6 |
| HellFire | 12 | 100% | **1200%** | 魔法，charge 120，需 Lv.12 |

### 10.5 DEF 陣列結構

```php
$def = array(
    [0] => 物理防禦(%)，   // Raw × (1 - def[0]/100)
    [1] => 物理防禦(-)，   // Raw - def[1]
    [2] => 魔法防禦(%)，   // Raw × (1 - def[2]/100)
    [3] => 魔法防禦(-)，   // Raw - def[3]
);
```

### 10.6 Buff/Debuff 公式

| 效果 | 公式 |
|------|------|
| UpATK(n%) | `atk[0] ×= (1 + n/100)` |
| DownDEF(n%) | `def[0] ×= (1 - n/100)` |
| UpDEF(n%) | `def[0] += floor((100 - def[0]) × n/100)` — 遞增式減傷 |
| UpSTR(n%) | `STR ×= (1 + n/100)` |
| DownSPD(n%) | `SPD ×= (1 - n/100)` |

### 10.7 重要特性

1. **STR/INT 邊際效益遞減：** 公式使用 `sqrt(Stat)`，STR 10→20 提升 10.0（100%↑），STR 100→110 僅提升 0.5（0.5%↑）
2. **武器 ATK 無衰減：** 直接加進 Base，**武器品質遠比點數重要**
3. **無隨機變異：** `$dmg *= mt_rand(90,110)/100` 已被註解 — 傷害完全固定
4. **最小傷害保證：** 至少造成 pre-defense Raw 的 10%
5. **玩家保護機制：** 傷害≥剩餘HP 且 HP>10 時強制留 1 HP（但多段技能後續段數無保護）
6. **低等保護：** Lv<10 且 MAXHP<200 的角色，傷害額外減少 `max(10, 25-level)`

### 10.8 技能一覽（含 pow 與屬性）

**物理技能：**

| 技能ID | 名稱 | SP | pow | 段數 | 屬性 | 備註 |
|:------:|------|:--:|:---:|:----:|:----:|------|
| 1000 | Attack | 0 | 100 | 1 | STR | 預設普通攻擊 |
| 1001 | Bash | 8 | 160 | 1 | STR | charge 20/20，Warrior起始 |
| 1017 | RagingBlow | 40 | 100 | 5 | STR | charge 40/60，需 Lv.6 |
| 2300 | Shoot | 0 | 100 | 1 | DEX | 需弓，後列優先 |
| 2310 | DoubleShot | 28 | 80 | 2 | DEX | 需弓，後列優先 |

**魔法技能：**

| 技能ID | 名稱 | SP | pow | 段數 | 屬性 | 備註 |
|:------:|------|:--:|:---:|:----:|:----:|------|
| 1002 | FireBall | 20 | 100 | 4 | INT | charge 60/0，Sorcerer起始 |
| 2000 | FireStorm | 70 | 100 | 6 | INT | charge 70/0，需 Lv.4 |
| 2002 | FirePillar | 40 | 140 | 2 | INT | charge 50/0，+DownSTR 40% |
| 2001 | HellFire | 320 | 100 | 12 | INT | charge 120/0，需 Lv.12 |

**輔助/恢復技能：**

| 技能ID | 名稱 | SP | pow | 對象 | 備註 |
|:------:|------|:--:|:---:|:----:|------|
| 3000 | Healing | 5 | 200 | 友方單體 | charge 30/0，Priest起始 |
| 3001 | PowerHeal | 20 | 300 | 友方×2 | charge 50/0，需 Lv.4 |
| 3002 | PartyHeal | 30 | 150 | 友方全體 | charge 50/0，需 Lv.12 |
| 3003 | QuickHeal | 20 | 180 | 友方×2 | 無 charge，需 Lv.4 |
| 3004 | SmartHeal | 30 | 200 | 友方×3 | charge 40/0，需 Lv.10 |
| 3005 | ProgressiveHeal | 30 | 125 | 友方×3 | HP≤30%時效果×2，需 Lv.4 |
| 3010 | ManaRecharge | 0 | — | 自身 | SP 回復，Sorcerer起始 |
| 3101 | Blessing | 0 | — | 友方全體 | SP 回復 rate:3，Priest起始 |

---

## 附錄：設定常數一覽

| 常數 | 預設值 | 說明 |
|------|--------|------|
| `NORMAL_BATTLE_TIME` | 100 | 普通戰鬥消耗 Time |
| `UNION_BATTLE_TIME` | 10 | Union 戰消耗 Time |
| `UNION_BATTLE_NEXT` | 1200 | Union 戰等待時間 (秒) |
| `ENEMY_INCREASE` | 1 | 敵方增員 (0/1) |
| `EXP_RATE` | 1 | 經驗值倍率 |
| `MONEY_RATE` | 1 | 金錢倍率 |
| `MAX_BATTLE_LOG` | 100 | 戰鬥日誌保存數 |
