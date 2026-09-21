# 戰鬥細節系統分析

> 分析日期：2026-09-21
> 補充分析：AI 判定、行為模式、暴擊、傷害波動、狀態持續、裝備限制、回合結束、pick 概率、常數值

### 系統狀態速覽

| 系統 | 狀態 | 說明 |
|------|------|------|
| 常數定義 | ✅ 啟用 | `setting.dist.php` 中所有戰鬥相關常數 |
| AI 判定 | ✅ 啟用 | ~50 種判定代碼，1300-1381 屬性條件為空壳 |
| 行為模式 | ✅ 啟用 | Pattern 系統，怪物自動復活技能 |
| 暴擊機制 | ❌ 不存在 | LUK 在傷害公式中未引用 |
| 傷害隨機波動 | ⚠️ 被關閉 | 雙層 `mt_rand(90,110)` 已註解，傷害為固定值 |
| 狀態異常 | ✅ 啟用 | 中毒/Regen 永久持續，無自然解除 |
| 裝備限制 | ✅ 啟用 | 兩層檢查（職業類型 + Handle），`need` 未檢查 |

---

## 目錄

1. [常數實際數值](#1-常數實際數值)
2. [AI 判定條件完整列表](#2-ai-判定條件完整列表)
3. [行為模式 (Pattern) 系統](#3-行為模式-pattern-系統)
4. [暴擊機制](#4-暴擊機制)
5. [傷害隨機波動（被關閉的機制）](#5-傷害隨機波動被關閉的機制)
6. [狀態異常持續與解除](#6-狀態異常持續與解除)
7. [裝備職業限制檢查](#7-裝備職業限制檢查)
8. [回合結束處理](#8-回合結束處理)
9. [Battle_Team pick() 概率機制](#9-battle_team-pick-概率機制)

---

## 1. 常數實際數值

> 來源：`hof/trust_path/config/setting.dist.php`

### 1.1 遊戲基礎設定

| 常數 | 值 | 說明 |
|------|-----|------|
| `MAX_TIME` | 1000 | Time 上限 |
| `TIME_GAIN_DAY` | 6000 | 每日自動回復的 Time |
| `START_TIME` | 900 | 遊戲開始時持有的 Time |
| `MAX_CHAR` | 5 | 最大所持角色數 |
| `MAX_USERS` | 500 | 最大註冊人數 |
| `MAX_LEVEL` | 50 | 最大等級 |
| `MAX_STATUS` | 250 | 單一屬性上限值 |
| `GET_STATUS_POINT` | 5 | 每次升級獲得的能力點 |
| `GET_SKILL_POINT` | 2 | 每次升級獲得的技能點 |
| `START_MONEY` | 50000 | 初始金錢 |
| `EXP_RATE` | 1 | 經驗值倍率 |
| `MONEY_RATE` | 1 | 金錢倍率 |
| `ABANDONED` | 1,209,600 (14天) | 視為放棄帳號的時間 |
| `NEW_NAME_COST` | 300,000 | 改名費用 |
| `REFINE_LIMIT` | 10 | 精鍊上限 |
| `SELLING_PRICE` | 1/5 | 預設賣出倍率（買價×0.2） |

### 1.2 戰鬥設定

| 常數 | 值 | 說明 |
|------|-----|------|
| `NORMAL_BATTLE_TIME` | **1** | 一般戰鬥消耗 Time |
| `ENEMY_INCREASE` | **1** | 敵人增員數量 |
| `BATTLE_MAX_TURNS` | 100 | 戰鬥最大回合數 |
| `TURN_EXTENDS` | 20 | 延長回合數 |
| `BATTLE_MAX_EXTENDS` | 100 | 延長後的最大回合數 |
| `BATTLE_STAT_TURNS` | 10 | 戰鬥統計顯示間隔 |
| `MAX_BATTLE_LOG` | 100 | 戰鬥日誌保存件數 |
| `MAX_BATTLE_LOG_UNION` | 100 | Union 戰日誌保存件數 |
| `MAX_BATTLE_LOG_RANK` | 100 | 排名戰日誌保存件數 |
| `MAX_STATUS_MAXIMUM` | 2500 | 戰鬥中能力上昇上限（2500%=25倍） |

### 1.3 Delay 系統設定

| 常數 | 值 | 說明 |
|------|-----|------|
| `DELAY_TYPE` | **1** | 使用新版 Delay 系統 |
| `DELAY` | 2.5 | 舊版 Delay 系數（TYPE=0 時使用） |
| `DELAY_BASE` | **5** | 新版 Delay 基底值（TYPE=1 時使用） |

### 1.4 Union/排名戰設定

| 常數 | 值 | 說明 |
|------|-----|------|
| `UNION_BATTLE_TIME` | **10** | Union 戰消耗 Time |
| `UNION_BATTLE_NEXT` | **1200 (20分鐘)** | Union 戰冷卻時間（秒） |
| `RANK_TEAM_SET_TIME` | 172,800 (48小時) | 排名戰隊伍設定冷卻 |
| `RANK_BATTLE_NEXT_LOSE` | 86,400 (24小時) | 排名戰敗北後冷卻 |
| `RANK_BATTLE_NEXT_WIN` | **60 (1分鐘)** | 排名戰勝利後冷卻 |

### 1.5 設定值對戰鬥的影響

```
DELAY_TYPE = 1 (新版)
    │
    ├── DelayValue = sqrt(SPD) + 5
    │   SPD=100 → DelayValue=15
    │   SPD=25  → DelayValue=10
    │   SPD=1   → DelayValue=6
    │
    └── 行動順序：delay 最小者先行動

ENEMY_INCREASE = 1
    │
    └── 敵人數量 = 玩家人數 + (最高等級>5 ? 1 : 0)
        1人PT → 1~2敵  |  3人PT → 3~4敵  |  5人PT → 5敵

NORMAL_BATTLE_TIME = 1
    │
    └── 每場戰鬥僅消耗 1 Time（可大量刷怪）
```

---

## 2. AI 判定條件完整列表

> 來源：`HOF_Class_Battle_Judge::DecideJudge()` (Judge.php 第 20-728 行)

### 2.1 判定代碼總覽

| 代碼範圍 | 類別 | 說明 |
|---------|------|------|
| 1000-1001 | 基本 | 必定/跳過 |
| 1100-1126 | HP 條件 | 自身/隊友 HP 條件 |
| 1200-1226 | SP 條件 | 自身/隊友 SP 條件 |
| 1300-1381 | 屬性條件 | STR/INT/DEX/SPD/LUK/ATK/MATK/DEF/MDEF |
| 1400-1410 | 我方人數 | 生存/死亡/前衛人數 |
| 1450-1456 | 敵方人數 | 生存/死亡人數 |
| 1500-1511 | 我方詠唱 | 蓄力/詠唱中人數 |
| 1550-1561 | 敵方詠唱 | 蓄力/詠唱中人數 |
| 1600-1616 | 中毒狀態 | 自身/隊友/敵方中毒 |
| 1700-1717 | 我方隊列 | 前衛/後衛人數 |
| 1750-1757 | 敵方隊列 | 前衛/後衛人數 |
| 1800-1825 | 召喚數量 | 我方/敵方召喚物數量 |
| 1840-1855 | 魔方陣 | 我方/敵方魔方陣數 |
| 1900-1902 | 行動次數 | 自身行動回數 |
| 1920 | 次數限制 | 指定次數內必定 |
| 1940 | 概率 | 指定概率觸發 |
| 9000 | 特殊 | 敵方是否有 Lv 以上角色 |

### 2.2 完整判定列表

```
1000  必ず (必定通過)
1001  パス (必定跳過)

── HP 條件 ──
1100  自分のHPが **(%)以上     自身 HP% ≥ N
1101  自分のHPが **(%)以下     自身 HP% ≤ N
1105  自分のHPが **以上        自身 HP ≥ N
1106  自分のHPが **以下        自身 HP ≤ N
1110  最大HPが **以上          自身 MAXHP ≥ N
1111  最大HPが **以下          自身 MAXHP ≤ N
1121  味方にHPが**(%)以下のキャラがいる  隊友 HP% ≤ N
1125  味方の平均HPが **(%)以上  隊友平均 HP% ≥ N
1126  味方の平均HPが **(%)以下  隊友平均 HP% ≤ N

── SP 條件 ──
1200  自分のSPが **(%)以上     自身 SP% ≥ N
1201  自分のSPが **(%)以下     自身 SP% ≤ N
1205  自分のSPが **以上        自身 SP ≥ N
1206  自分のSPが **以下        自身 SP ≤ N
1210  最大SPが **以上          自身 MAXSP ≥ N
1211  最大SPが **以下          自身 MAXSP ≤ N
1221  味方にSPが**(%)以下のキャラがいる  隊友 SP% ≤ N
1225  味方の平均SPが **(%)以上  隊友平均 SP% ≥ N
1226  味方の平均SPが **(%)以下  隊友平均 SP% ≤ N

── 屬性條件 (⚠️ 未實作，仅有 case 無處理邏輯) ──
1300  自分のSTRが **以上       (空)
1301  自分のSTRが **以下       (空)
1310  自分のINTが **以上       (空)
1311  自分のINTが **以下       (空)
1320  自分のDEXが **以上       (空)
1321  自分のDEXが **以下       (空)
1330  自分のSPDが **以上       (空)
1331  自分のSPDが **以下       (空)
1340  自分のLUKが **以上       (空)
1341  自分のLUKが **以下       (空)
1350  自分のATKが **以上       (空)
1351  自分のATKが **以下       (空)
1360  自分のMATKが **以上      (空)
1361  自分のMATKが **以下      (空)
1370  自分のDEFが **以上       (空)
1371  自分のDEFが **以下       (空)
1380  自分のMDEFが **以上      (空)
1381  自分のMDEFが **以下      (空)

── 我方人數 ──
1400  味方の生存者が *人以上    生存 ≥ N
1401  味方の生存者が *人以下    生存 ≤ N
1405  味方の死者が *人以上      死亡 ≥ N
1406  味方の死者が *人以下      死亡 ≤ N
1410  味方で前衛の生存者が *人以上  前衛生存 ≥ N

── 敵方人數 ──
1450  相手の生存者が *人以上    敵生存 ≥ N
1451  相手の生存者が *人以下    敵生存 ≤ N
1455  相手の死者が *人以上      敵死亡 ≥ N
1456  相手の死者が *人以下      敵死亡 ≤ N

── 我方詠唱/蓄力 ──
1500  チャージ中のキャラが *人以上   我方蓄力 ≥ N
1501  チャージ中のキャラが *人以下   我方蓄力 ≤ N
1505  詠唱中のキャラが *人以上       我方詠唱 ≥ N
1506  詠唱中のキャラが *人以下       我方詠唱 ≤ N
1510  チャージか詠唱中のキャラが *人以上  我方蓄力+詠唱 ≥ N
1511  チャージか詠唱中のキャラが *人以下  我方蓄力+詠唱 ≤ N

── 敵方詠唱/蓄力 ──
1550  チャージ中の相手が *人以上     敵蓄力 ≥ N
1551  チャージ中の相手が *人以下     敵蓄力 ≤ N
1555  詠唱中の相手が *人以上         敵詠唱 ≥ N
1556  詠唱中の相手が *人以下         敵詠唱 ≤ N
1560  チャージか詠唱中の相手が *人以上  敵蓄力+詠唱 ≥ N
1561  チャージか詠唱中の相手が *人以下  敵蓄力+詠唱 ≤ N

── 中毒狀態 ──
1600  自分が毒状態               自身中毒
1610  毒状態の味方が **人以上    我方中毒 ≥ N
1611  毒状態の味方が **人以下    我方中毒 ≤ N
1612  毒状態の味方が **% 以上    我方中毒% ≥ N
1613  毒状態の味方が **% 以下    我方中毒% ≤ N
1615  毒状態の相手が **人以上    敵中毒 ≥ N
1616  毒状態の相手が **人以下    敵中毒 ≤ N

── 隊列 ──
1700  自分が前列                 自身前衛
1701  自分が後列                 自身後衛
1710  味方の前列が**人以上       我方前衛 ≥ N
1711  味方の前列が**人以下       我方前衛 ≤ N
1712  味方の前列が**人           我方前衛 == N
1715  味方の後列が**人以上       我方後衛 ≥ N
1716  味方の後列が**人以下       我方後衛 ≤ N
1717  味方の後列が**人           我方後衛 == N
1750  相手の前列が**人以上       敵前衛 ≥ N
1751  相手の前列が**人以下       敵前衛 ≤ N
1752  相手の前列が**人           敵前衛 == N
1755  相手の後列が**人以上       敵後衛 ≥ N
1756  相手の後列が**人以下       敵後衛 ≤ N
1757  相手の後列が**人           敵後衛 == N

── 召喚 ──
1800  味方の召喚キャラが **匹以上  我方召喚 ≥ N
1801  味方の召喚キャラが **匹以下  我方召喚 ≤ N
1805  味方の召喚キャラが **匹      我方召喚 == N
1820  相手の召喚キャラが **匹以上  敵召喚 ≥ N
1821  相手の召喚キャラが **匹以下  敵召喚 ≤ N
1825  相手の召喚キャラが **匹      敵召喚 == N

── 魔方陣 ──
1840  味方の魔法陣の数が **個以上  我方魔方陣 ≥ N
1841  味方の魔法陣の数が **個以下  我方魔方陣 ≤ N
1845  味方の魔法陣の数が **個      我方魔方陣 == N
1850  相手の魔法陣の数が **個以上  敵魔方陣 ≥ N
1851  相手の魔法陣の数が **個以下  敵魔方陣 ≤ N
1855  相手の魔法陣の数が **個      敵魔方陣 == N

── 行動次數 ──
1900  自分の行動回数が **回以上    ActCount ≥ N
1901  自分の行動回数が **回以下    ActCount ≤ N
1902  自分の行動回数が **回目      ActCount == N

── 次數/概率 ──
1920  **回だけ必ず               前 N 次必定通過
1940  **%の確率で                N% 概率通過

── 特殊 ──
9000  相手チームにLv**以上が居る  敵方有 Lv≥N 角色
```

### 2.3 判定流程

```
MultiFactJudge($Keys, $char)
    │
    └─ foreach $Keys as $no
           │
           ├─ DecideJudge($no, $char)
           │   └─ switch ($pattern['judge'])
           │       ├─ case 1100: return $Quantity <= $My->HpPercent()
           │       ├─ case 1940: return mt_rand(1,100) <= $Quantity
           │       └─ ...
           │
           └─ if (!$return) return false  // 任一判定失敗則終止

    return true  // 全部通過
```

**多條件判定範例：**
```yaml
# 判定代碼陣列 [judge, quantity, action]
pattern:
  - [1101, 50, 3000]    # 自身 HP ≤ 50% → 使用技能 3000
  - [1940, 30, 2000]    # 30% 概率 → 使用技能 2000
  - [1000, 0, 1000]     # 必定 → 通常攻擊
```

> ⚠️ **注意：** 1300-1381 屬性條件（STR/INT/DEX 等）僅有 case 定義但**無處理邏輯**（空 break），相當於未實作。

---

## 3. 行為模式 (Pattern) 系統

> 來源：`HOF_Class_Char_Pattern` (Pattern.php)

### 3.1 Pattern 結構

```yaml
behavior:
  position: "front"        # 固定隊列 (可選)
  guard: "always"          # 守護行為 (可選)
  pattern:                  # AI 行動列表
    - [judge_code, quantity, action_code]
    - [judge_code, quantity, action_code]
    ...
```

### 3.2 Pattern Item 格式

每個 pattern item 是一個三元素陣列：

```php
array(
    'judge'   => 1101,    // 判斷代碼
    'quantity' => 50,      // 判斷參數
    'action'  => 3000,     // 行動代碼（技能編號或 1000=通常攻擊）
)
```

### 3.3 行動代碼

| action 值 | 說明 |
|-----------|------|
| 1000 | 通常攻擊（物理單體） |
| 技能編號 | 使用指定技能（如 2000=火球、3000=回復） |
| 9000 | 複合判定（連續多個 judge） |

### 3.4 怪物自動 Pattern 處理

```php
// Pattern.php → _pattern_plus()
function _pattern_plus(&$pattern_new)
{
    if (!$this->cache['init'] && $this->char->isMon())
    {
        // 1. 移除開頭的 judge=1000（必定）項目
        $judge_1000 = array();
        while ($first_v['judge'] == 1000)
        {
            $judge_1000[] = array_shift($pattern_new);
        }

        // 2. 非召喚物、非工會怪 → 插入復活技能
        if (!$this->char->isSummon() && !$this->char->isUnion())
        {
            // [1405, 1, 9000] + [1940, 10, 3040]
            // = 我方死亡≥1人 且 10%概率 → 使用復活 (3040)
            array_splice($pattern_new, 0, 0, array(
                [1405, 1, 9000],
                [1940, 10, 3040]
            ));
        }

        // 3. 將移除的 judge=1000 項目放回末尾
        foreach ($judge_1000 as $v)
        {
            array_push($pattern_new, $v);
        }

        // 4. 追加預設通常攻擊 [1000, 0, 1000]
        array_push($pattern_new, $this->_fix_pattern_item());
    }
}
```

### 3.5 指令數量限制 (INT 依賴)

```php
function pattern_max()
{
    if ($this->char->isMon()) return -1;  // 怪物無限制

    $val = $this->char->int;
    $map = [10, 15, 30, 50, 80, 120, 160, 200, 251];

    $n = 2;  // 最少 2 條
    foreach ($map as $v)
    {
        if ($val >= $v) $n++;
        else break;
    }

    if (29 < $this->char->level) $n++;  // Lv30+ 額外 +1

    return $n;
}
```

| INT 範圍 | 指令數 | 額外條件 |
|---------|--------|---------|
| <10 | 2 | — |
| 10-14 | 3 | — |
| 15-29 | 4 | — |
| 30-49 | 5 | — |
| 50-79 | 6 | — |
| 80-119 | 7 | — |
| 120-159 | 8 | — |
| 160-199 | 9 | — |
| 200-250 | 10 | — |
| 251+ | 11 | — |
| Lv30+ | +1 | 上述基礎上 +1 |
| 怪物 | 無限制 | — |

### 3.6 Pattern 執行流程

```
Action($char)
    │
    ├─ 1. AutoRegeneration()    持續回復
    ├─ 2. PoisonDamage()        毒傷害
    │
    ├─ 3. AI 判定迴圈
    │   do {
    │       $JudgeKey++
    │       $Keys[] = $JudgeKey
    │       // 若 action=9000 → 繼續加入下一個判定
    │   } while (action == 9000 && judge)
    │
    │   $return = MultiFactJudge($Keys, $char)
    │   if ($return) → 使用該技能
    │
    └─ 4. 執行技能 UseSkill()
```

---

## 4. 暴擊機制

### 4.1 分析結果

在所有戰鬥相關原始碼中**未發現暴擊（Critical Hit）機制**。

```
搜索範圍：
├── Battle.php         — 無 critical/暴擊 相關邏輯
├── Skill/Effect.php   — CalcBasicDamage() 無暴擊計算
├── Char/Battle/Effect.php — 無暴擊觸發
└── Battle/Judge.php   — 無暴擊判定

結論：LUK 屬性在傷害公式中未被使用，暴擊系統不存在。
```

### 4.2 LUK 的實際用途

LUK 屬性在戰鬥傷害公式中**未被引用**。可能的用途：
- 裝備掉落率（未在戰鬥系統中驗證）
- 中毒抵抗間接影響（透過 `PoisonResist`）
- 遊戲設計上為未實作的預留屬性

---

## 5. 傷害隨機波動（被關閉的機制）

> **備註：** 此機制已被註解掉，以下作為設計參考保留。

### 5.1 原始碼

```php
// Skill/Effect.php → CalcBasicDamage() 第 727-729 行
//ダメージのばらつき
//$dmg	*= mt_rand(90,110)/100;
//$dmg	*= mt_rand(90,110)/100;
```

### 5.2 算法分析

兩行完全相同的乘法，形成**兩層獨立波動**：

```
最終傷害 = 基礎傷害 × R1 × R2

其中：
  R1 = mt_rand(90,110) / 100    範圍 0.90 ~ 1.10
  R2 = mt_rand(90,110) / 100    範圍 0.90 ~ 1.10
```

### 5.3 數學分佈

```
理論最小值：0.90 × 0.90 = 0.810  (基礎傷害的 81%)
理論最大值：1.10 × 1.10 = 1.210  (基礎傷害的 121%)

期望值：E[R1] × E[R2] = 1.00 × 1.00 = 1.00（無偏）

標準差估算：
  Var(R) = E[R²] - E[R]²
  E[R²] = (90²+91²+...+110²) / 21 / 10000 = 1.00333
  Var(R) = 0.00333
  Var(R1×R2) ≈ Var(R1) + Var(R2) = 0.00666（近似）
  σ ≈ 0.0816

  即最終傷害的標準差約為基礎傷害的 8.2%
```

### 5.4 分佈示意

```
機率分佈（兩層波動疊加）：

121% │                                              ██
115% │                                        ████████
110% │                                  ██████████████
105% │                            ████████████████████
100% │                      ██████████████████████████
 95% │                ████████████████████████████████
 90% │          ██████████████████████████████████████
 85% │    ████████████████████████████████████████████
 81% │ ██
     └──────────────────────────────────────────────────
       81%        90%        100%       110%       121%
                      傷害倍率

特徵：
  ├── 對稱分佈，中心在 100%
  ├── 大部分傷害集中在 90%~110%（機率 ~73%）
  ├── 極端值（<85% 或 >115%）出現機率很低
  └── 兩層波動使分佈更接近常態分佈（中央極限定理效應）
```

### 5.5 與單層波動的比較

| 比較項目 | 單層波動 (×1) | 雙層波動 (×2) |
|---------|-------------|-------------|
| 範圍 | 90%~110% | 81%~121% |
| 標準差 | ~5.77% | ~8.16% |
| 極端值機率 | 低 | 稍高但仍低 |
| 分佈形狀 | 均勻分佈 | 近似常態分佈 |
| 戰鬥體驗 | 微小波動 | 明顯但不誇張 |

### 5.6 Node.js 復刻建議

```javascript
// 方案 A：完全還原（雙層波動）
function damageVariance(baseDamage) {
  const r1 = (Math.floor(Math.random() * 21) + 90) / 100; // 90~110
  const r2 = (Math.floor(Math.random() * 21) + 90) / 100; // 90~110
  return Math.ceil(baseDamage * r1 * r2);
}

// 方案 B：簡化為單層波動（推薦，效果相近）
function damageVarianceSimple(baseDamage) {
  const r = (Math.floor(Math.random() * 21) + 90) / 100; // 90~110
  return Math.ceil(baseDamage * r);
}

// 方案 C：使用正態分佈（更自然的波動）
function damageVarianceNormal(baseDamage) {
  // Box-Muller 變換
  const u1 = Math.random();
  const u2 = Math.random();
  const z = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
  const sigma = 0.05; // 5% 標準差
  const r = 1 + z * sigma;
  return Math.ceil(baseDamage * Math.max(0.81, Math.min(1.21, r)));
}
```

### 5.7 結論

此機制為**原始設計中預留但未啟用的功能**。若要在復刻版中啟用：
- 直接取消註解即可還原原始行為
- 建議使用方案 B（單層波動），簡化且效果差異極小
- 波動範圍 ±10% 適合回合制 RPG，不會造成過大隨機性

---

## 6. 狀態異常持續與解除

### 6.1 中毒狀態

#### 中毒觸發

```php
// Char/Battle/Effect.php → GetPoison()
function GetPoison($BePoison)
{
    if ($this->char->STATE === STATE_POISON) return false;  // 已中毒

    if ($this->char->SPECIAL["PoisonResist"])
    {
        // 有抗性：機率降低
        $BePoison *= (1 - $this->char->SPECIAL["PoisonResist"] / 100);

        if (mt_rand(0, 99) < $BePoison)
        {
            $this->char->STATE = STATE_POISON;  // 中毒
            return true;
        }
        else
        {
            return "BLOCK";  // 抵抗
        }
    }

    // 無抗性：直接中毒
    $this->char->STATE = STATE_POISON;
    return true;
}
```

#### 中毒傷害觸發時機

```php
// Battle.php → Action() 第 316-318 行
$char->AutoRegeneration();    // 1. 持續回復
$char->PoisonDamage();        // 2. 毒傷害

// 每回合行動前觸發
// 使用 HpDamage2()，不會致死，最低 HP=1
```

#### 中毒解除條件

```php
// Skill/Effect.php → SkillEffect() 中
case 'CurePoison':
    if ($target->STATE == STATE_POISON)
    {
        $target->GetNormal(true);  // 回復正常狀態
    }
    break;
```

**中毒解除方式：**
- 使用解毒技能（`CurePoison: 1` 的技能）
- **中毒不會自然解除**，必須透過技能治療
- 死亡後復活會回到正常狀態

### 6.2 持續回復 (Regen)

#### 觸發時機

```php
// Battle.php → Action() 第 316 行
// 每回合行動前自動觸發
$char->AutoRegeneration();

// Char/Battle/Effect.php → AutoRegeneration()
function AutoRegeneration()
{
    if ($this->char->SPECIAL["HpRegen"])
    {
        $Regen = round($this->char->MAXHP * $this->char->SPECIAL["HpRegen"] / 100);
        $this->char->HpRecover($Regen);
    }

    if ($this->char->SPECIAL["SpRegen"])
    {
        $Regen = round($this->char->MAXSP * $this->char->SPECIAL["SpRegen"] / 100);
        $this->char->SpRecover($Regen);
    }
}
```

#### Regen 回復量

```
HP Regen = round(MAXHP × HpRegen% / 100)
SP Regen = round(MAXSP × SpRegen% / 100)
```

#### Regen 獲得/持續

```php
// Skill/Effect.php → SkillEffect()
if ($skill["HpRegen"])
{
    $target->GetSpecial("HpRegen", $skill["HpRegen"]);
    // 獲得 HpRegen 特殊狀態，永久持續
}
```

**Regen 特性：**
- 透過技能獲得，**永久持續**（無回合數限制）
- 每回合行動前自動觸發
- 透過 `GetSpecial()` 設定，可疊加
- 死亡後復活不會自動移除

### 6.3 狀態持續總結

| 狀態 | 持續方式 | 解除條件 |
|------|---------|---------|
| 中毒 (STATE_POISON) | 永久 | 解毒技能 (CurePoison) |
| HP Regen | 永久 | 無（永久持續） |
| SP Regen | 永久 | 無（永久持續） |
| Barrier | 消耗次數 | 抵擋一次攻擊後消失 |
| PoisonResist | 永久 | 無（永久持續） |

> **注意：** 所有狀態異常（中毒、Regen 等）**無自然回合數限制**，必須透過技能或死亡復活來解除。

---

## 7. 裝備職業限制檢查

> 來源：`HOF_Controller_Char::_equip_item()` (Char.php 第 552-606 行)

### 7.1 檢查流程

```php
function _equip_item()
{
    // 1. 檢查是否持有該物品
    if (!$this->user->item[$this->input->item_no])
    {
        $this->_msg_error("Item not exists.");
        return false;
    }

    // 2. 取得職業資料
    $JobData = HOF_Model_Data::getJobData($this->char->job);

    // 3. 取得物品資料
    $item = HOF_Model_Data::getItemData($this->input->item_no);

    // 4. ★ 職業裝備類型檢查 ★
    if (!in_array($item["type"], $JobData["equip"]))
    {
        $this->_msg_error("{$this->char->job_name} can't equip {$item[name]}.");
        return false;
    }

    // 5. 執行裝備（含 Handle 檢查）
    list($fail, $return) = $this->char->setEquip($item);

    if ($fail)
    {
        $this->_msg_error("Handle Over. Can't Equip {$item[name]}.");
    }
    else
    {
        $this->user->item_remove($this->input->item_no);
    }
}
```

### 7.2 兩層檢查機制

```
裝備流程
    │
    ├─ 第一層：職業類型檢查 (Char.php)
    │   └─ $JobData["equip"] 是否包含 $item["type"]
    │      例：劍士可裝備 Sword/Dagger/Shield/Armor
    │          法師可裝備 Wand/Staff/Book/Robe
    │
    └─ 第二層：Handle 負荷檢查 (Char.php → setEquip())
        └─ 裝備後總 Handle ≤ 玩家最大 Handle
           MaxHandle = 5 + floor(level/10) + floor(DEX/5)
```

### 7.3 職業可裝備類型

每個職業的 `$JobData["equip"]` 定義了可裝備的物品類型列表，如：

```yaml
# job.{no}.yml
equip:
  - Sword
  - Dagger
  - Shield
  - Armor
  - Item
```

> **注意：** 物品的 `need` 欄位（`{job_no: level}`）**未在裝備流程中檢查**。`need` 可能僅供顯示或商店購買時參考。

---

## 8. 回合結束處理

### 8.1 行動前處理 (每回合開始)

```php
// Battle.php → Action() 第 315-318 行
$char->AutoRegeneration();    // 1. 持續回復 (HpRegen/SpRegen)
$char->PoisonDamage();        // 2. 毒傷害
// 3. AI 判定 → 決定技能
// 4. 執行技能
```

### 8.2 毒傷害

```php
// Char/Battle/Effect.php → PoisonDamage()
function PoisonDamage($multiply = 1)
{
    if ($this->char->STATE !== STATE_POISON) return false;

    $poison = $this->char->PoisonDamageFormula($multiply);
    $this->char->HpDamage2($poison);  // 不會致死
}

function PoisonDamageFormula($multiply = 1)
{
    $damage = round($this->char->MAXHP * 0.10) + ceil($this->char->level / 2);
    $damage *= $multiply;
    return round($damage);
}
```

```
毒傷害 = (MAXHP × 10% + ceil(level / 2)) × 倍率
```

### 8.3 持續回復

```php
// AutoRegeneration()
HpRegen 回復 = round(MAXHP × HpRegen% / 100)
SpRegen 回復 = round(MAXSP × SpRegen% / 100)
```

### 8.4 戰鬥結束處理

```php
// Battle.php → Process() 戰鬥結束後
$battle->SaveCharacters();    // 保存所有角色資料
list($UserMoney) = $battle->ReturnMoney();  // 計算金錢獎勵
$this->user->getMoney($UserMoney);          // 增加金錢
$battle->RecordLog();                       // 保存戰鬥日誌

// 掉落物
if ($itemdrop = $battle->ReturnItemGet(TEAM_0))
{
    foreach ($itemdrop as $itemno => $amount)
    {
        $this->user->item_add($itemno, $amount);
    }
    $this->user->item_save();
}

$this->user->SaveData();  // 保存使用者資料
```

### 8.5 回合結束時序

```
每回合時序：
    │
    ├─ 1. AutoRegeneration()   持續回復
    ├─ 2. PoisonDamage()       毒傷害
    ├─ 3. AI 判定              決定行動
    ├─ 4. UseSkill()           執行技能
    │      ├─ SP 消耗
    │      ├─ 詠唱檢查
    │      ├─ 目標選擇 + 守護判定
    │      ├─ 傷害/回復計算
    │      ├─ 狀態異常套用
    │      └─ 硬直處理
    ├─ 5. Delay 重置
    └─ 6. BattleResult()       判定勝敗

戰鬥結束後：
    ├─ SaveCharacters()        保存角色
    ├─ ReturnMoney()           計算金錢
    ├─ ReturnItemGet()         計算掉落
    ├─ RecordLog()             保存日誌
    └─ SaveData()              保存使用者
```

---

## 9. Battle_Team pick() 概率機制

> 來源：`HOF_Class_Battle_Team::pick()` (Team.php 第 339-374 行)

### 9.1 概率選擇算法

```php
public function pick($pick_list = null)
{
    // 1. 計算概率總和
    foreach ($pick_list as $val) $max += $val[0];

    HOF_Helper_Math::rand_seed();

    // 2. 在 0~總和 中取隨機數
    $pos = mt_rand(0, $max);

    // 3. 洗牌後依序檢查
    $list = HOF_Helper_Array::array_shuffle($pick_list);

    foreach ($list as $no => $val)
    {
        $upp += $val[0];

        // 4. 累計概率 ≥ 隨機數 → 選中
        if ($pos <= $upp)
        {
            return $no;
        }
    }

    // 5. 兜底：隨機選一個
    return array_rand($list);
}
```

### 9.2 概率計算範例

```php
// 狩獵區域的怪物列表格式
$MonsterList = array(
    // [出現率, 權重]
    array(1000, 4),   // 敵人 1000，權重 4
    array(1001, 3),   // 敵人 1001，權重 3
    array(1002, 2),   // 敵人 1002，權重 2
    array(1003, 1),   // 敵人 1003，權重 1
);

// 總和 = 4+3+2+1 = 10
// 敵人 1000：4/10 = 40%
// 敵人 1001：3/10 = 30%
// 敵人 1002：2/10 = 20%
// 敵人 1003：1/10 = 10%
```

### 9.3 批量選擇 (pickList)

```php
public function pickList($amount = null, $pick_list = null)
{
    $list = array();

    for ($i = 0; $i < $amount; $i++)
    {
        $list[] = $this->pick($pick_list);  // 每次獨立隨機
    }

    return $list;
}
```

**特性：**
- 每次選擇**獨立隨機**（可重複選中同一敵人）
- 選擇前**洗牌**（`array_shuffle`）確保概率均勻分佈
- 使用 `mt_rand` 偽隨機數生成器
- 兜底機制：若迴圈未選中，回傳 `array_rand` 隨機結果

---

## 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Class/Battle/Judge.php` | `DecideJudge()`, `MultiFactJudge()` | AI 判定條件系統 |
| `HOF/Class/Char/Pattern.php` | `pattern()`, `pattern_max()`, `_pattern_plus()` | 行為模式管理 |
| `HOF/Class/Skill/Effect.php` | `CalcBasicDamage()` | 傷害計算（含波動註解） |
| `HOF/Class/Battle.php` | `Action()`, `BattleResult()`, `Process()` | 戰鬥主流程 |
| `HOF/Class/Char/Battle/Effect.php` | `AutoRegeneration()`, `PoisonDamage()`, `GetPoison()` | 回復/毒/狀態管理 |
| `HOF/Class/Battle/Team.php` | `pick()`, `pickList()` | 敵人概率選擇 |
| `HOF/Controller/Char.php` | `_equip_item()` | 裝備職業限制檢查 |
| `HOF/Const/setting.dist.php` | — | 所有常數定義 |
