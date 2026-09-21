# 戰鬥機制與算法完整分析

> 分析日期：2026-09-21
> 分析範圍：`HOF/Class/Battle.php` + `Battle/` 目錄 + `Skill/Effect.php` + `Char/Battle/Effect.php`

---

## 目錄

1. [戰鬥主流程](#1-戰鬥主流程)
2. [行動順序 — Delay 系統](#2-行動順序--delay-系統)
3. [傷害計算公式](#3-傷害計算公式)
4. [防禦/守護機制](#4-防禦守護機制)
5. [回復公式](#5-回復公式)
6. [狀態異常](#6-狀態異常)
7. [Buff/Debuff 系統](#7-buffdebuff-系統)
8. [SP 消耗與 HP 犧牲](#8-sp-消耗與-犧牲)
9. [詠唱/蓄力系統](#9-詠唱蓄力系統)
10. [目標選擇與優先順位](#10-目標選擇與優先順位)
11. [死亡判定與獎勵](#11-死亡判定與獎勵)
12. [魔方陣系統](#12-魔方陣系統)
13. [戰鬥結果判定](#13-戰鬥結果判定)
14. [相關原始碼檔案](#14-相關原始碼檔案)

---

## 1. 戰鬥主流程

### 1.1 啟動流程

```
Battle Controller
    │
    ▼
new HOF_Class_Battle($MyTeam, $EnemyTeam)
    │  ├─ _extend_init()         載入 View, Skill_Effect, Battle_Skill, Battle_Judge
    │  ├─ 建立兩支 Battle_Team
    │  ├─ setBattleVariable()    計算裝備、被動技能、ATK/DEF
    │  ├─ SetDelay()             計算戰鬥基準 Delay
    │  └─ DelayResetAll()        所有人 delay = SPD
    │
    ▼
Process()
    │  ├─ rand_seed()            亂數種子
    │  ├─ BattleHeader()         標題顯示
    │  ├─ initEnterBattlefield()  依 SPD 排序顯示入場
    │  │
    │  └─ do {                   ★ 主迴圈 ★
    │      ├─ NextActer()        找下一個行動者
    │      ├─ Action($char)      執行行動
    │      ├─ BattleResult()     判定戰鬥是否結束
    │      └─ SetDelay()         必要時重算 Delay
    │  } while ($result === null || $result === false);
    │
    └─ ShowResult() / BattleFoot()
```

### 1.2 單一行動流程 (Action)

```php
function Action(&$char)
{
    // 1. 檢查是否有行為模式
    if (empty($char->behavior['pattern'])) throw RuntimeException;

    // 2. 持續回復 (HpRegen/SpRegen)
    $char->AutoRegeneration();

    // 3. 毒傷害
    $char->PoisonDamage();

    // 4. AI 判定 → 決定使用哪個技能
    do {
        $Keys[] = $JudgeKey;
        $return = $this->MultiFactJudge($Keys, $char);
        if ($return) { $skill = $char->behavior['pattern'][$JudgeKey]['action']; break; }
    } while ($char->behavior['pattern'][$JudgeKey]['judge']);

    // 5. 執行技能
    $this->UseSkill($skill, $return, $char);
}
```

### 1.3 技能使用流程 (UseSkill)

```
UseSkill($skill_no, $JudgedTarget, $My)
    │
    ├─ 1. 讀取技能資料 (getSkill)
    ├─ 2. 怪物 SP 折扣 (×0.7，蘇生系 ×0.07)
    ├─ 3. 武器類型檢查 (limit) → 不符則失敗
    ├─ 4. SP 充足檢查 → 不足則失敗
    ├─ 5. 詠唱/蓄力檢查
    │      └─ 若需詠唱且尚未開始 → 開始詠唱，return
    │
    ├─ 6. 消耗魔方陣 (MagicCircleDeleteTeam)
    ├─ 7. 消耗 SP
    ├─ 8. HP 犧牲 (sacrifice)
    │
    ├─ 9. 選擇目標候選 (friend/enemy/self/all)
    ├─ 10. 依 target[1] 決定施放方式
    │      ├─ individual → 選 1 目標 + 守護判定 + 重複 N 次
    │      ├─ multi      → 重複 N 次，每次選 1 目標 + 守護判定
    │      └─ all        → 全體施放（無守護判定）
    │
    ├─ 11. 使用後移動 (umove)
    ├─ 12. 死亡判定 (JudgeTargetsDead)
    ├─ 13. 獎勵計算 (EXP, 金錢, 掉落)
    └─ 14. 硬直處理 (charge[1]) → DelayReset
```

---

## 2. 行動順序 — Delay 系統

### 2.1 核心概念

HOF 使用 **延遲行動制**（非傳統回合制）。每個角色有一個 `delay` 值，**越小越先行動**。

### 2.2 兩種 Delay 模式

由常數 `DELAY_TYPE` 決定：

| 模式 | 初始值 | 行動後重置 | 下次距離計算 |
|------|--------|-----------|-------------|
| `DELAY_TYPE = 0` | `delay = SPD` | `delay = SPD` | `distance = (100 - delay) / DelayValue` |
| `DELAY_TYPE = 1` | `delay = 0` | `delay = 0` | `distance = (100 - delay) / DelayValue` |

### 2.3 DelayValue 計算

```php
function DelayValue()
{
    return bcadd(sqrt($this->char->SPD), DELAY_BASE);
}
```

> `DelayValue = sqrt(SPD) + DELAY_BASE`

### 2.4 戰鬥基準 Delay (SetDelay)

```php
// DELAY_TYPE === 0 時
$TotalSPD = 所有角色 SPD 總和;
$MaxSPD   = 所有角色 SPD 最大值;
$AverageSPD = $TotalSPD / 角色總數;
$AveDELAY = $AverageSPD * DELAY;          // DELAY 為常數
$this->delay = $MaxSPD + $AveDELAY;       // 戰鬥基準 Delay
```

### 2.5 行動者選擇 (NextActer — DELAY_TYPE=0)

```php
// 找 delay 最大者（最接近行動）
foreach ($all_chars as $char) {
    if ($delay <= $char->delay) {
        if ($delay == $char->delay && mt_rand(0, 1)) continue; // 同值 50% 機率跳過
        $NextChar = $char;
    }
}

// 全員 delay 減少（差分法）
$dif = $this->delay - $NextChar->delay;
foreach ($all_chars as $char) {
    $char->delay -= $dif;  // 所有人靠近行動
}

// 行動者 delay 重置
$NextChar->DelayReset();  // delay = SPD
```

### 2.6 行動者選擇 (NextActerNew — DELAY_TYPE=1)

```php
// 找 nextDis 最小者（最快到達行動點）
$nextDis = 1000;
foreach ($all_chars as $char) {
    $charDis = $char->nextDis();  // (100 - delay) / DelayValue
    if ($charDis < $nextDis) {
        $NextChar = [$char];
    } elseif ($charDis == $nextDis) {
        $NextChar[] = $char;  // 同值隨機
    }
}

// 全員 delay 減少
foreach ($all_chars as $char) {
    $char->Delay($nextDis);
}
```

### 2.7 Delay 操作函式

| 函式 | 公式 (TYPE=0) | 說明 |
|------|--------------|------|
| `DelayReset()` | `delay = SPD` | 行動後重置 |
| `Delay($no)` | `delay += $no` | 減少 delay（靠近行動） |
| `DelayByRate($No, $BaseDelay)` | `delay -= (BaseDelay - SPD) × ($No / 100)` | 按比例延遲 |
| `DelayCut($No, $BaseDelay)` | `delay += (BaseDelay - delay) × ($No / 100)` | 按比例加速 |
| `Quick($delay)` | `delay = $delay` | 立即行動 |

---

## 3. 傷害計算公式

### 3.1 物理傷害 (type=0)

```
Base   = sqrt(STR) × 10 + atk[0]
         (若 inf="dex"，改用 sqrt(DEX) × 10)
Raw    = Base × (pow / 100)
         × option["multiply"] (若有)

min_dmg = Raw × 10%     ← 最低保證傷害

── 防禦減傷 ── (若非 pierce)
Raw   = Raw × (1 - def[0] / 100)   ← 百分比減傷
Raw   = Raw - def[1]               ← 固定值減傷

── 最終 ──
Final = ceil(max(Raw + Pierce, min_dmg))
```

### 3.2 魔法傷害 (type=1)

```
Base   = sqrt(INT) × 10 + atk[1]
Raw    = Base × (pow / 100)
         × option["multiply"] (若有)

min_dmg = Raw × 10%

── 防禦減傷 ── (若非 pierce)
Raw   = Raw × (1 - def[2] / 100)
Raw   = Raw - def[3]

── 最終 ──
Final = ceil(max(Raw + Pierce, min_dmg))
```

### 3.3 DEF 陣列結構

```php
$this->def = array(
    [0] => 物理防禦(%),   // Raw × (1 - def[0]/100)
    [1] => 物理防禦(-),   // Raw -= def[1]
    [2] => 魔法防禦(%),   // Raw × (1 - def[2]/100)
    [3] => 魔法防禦(-),   // Raw -= def[3]
);
```

裝備 DEF 累加方式：
```php
$this->def[0] += $item[def][0]; // 物理防禦(%)
$this->def[1] += $item[def][1]; // 物理防禦(-)
$this->def[2] += $item[def][2]; // 魔法防禦(%)
$this->def[3] += $item[def][3]; // 魔法防禦(-)
```

### 3.4 低等角色傷害減免

```php
if ($target->isChar && $dmg > 20) {
    if ($target->HP > 10 && $dmg >= $target->HP) {
        $dmg = $target->HP - 1;       // 留 1 HP（不致死）
    }
    elseif ($target->level < 10 && $target->MAXHP < 200) {
        $dmg -= max(10, 25 - $target->level);  // 低等減傷
    }
}
```

| 條件 | 效果 |
|------|------|
| 目標是玩家角色 & 傷害≥20 | 啟用保護 |
| HP > 10 & 傷害≥HP | 強制留 1 HP |
| Lv < 10 & MAXHP < 200 | 傷害 -max(10, 25-level) |

### 3.5 Barrier 絕對防禦

```php
if ($target->SPECIAL["Barrier"]) {
    $target->GetSpecial("Barrier", false);  // 消耗 Barrier
    $dmg = 0;                               // 傷害歸零
}
```

一次消耗，完全抵擋一次攻擊。

### 3.6 Pierce 防禦貫穿傷害

```php
if ($char->SPECIAL["Pierce"]["0"]) {  // 物理 Pierce
    $Pierce = $char->SPECIAL["Pierce"]["0"] * $skill["pow"] / 100;
}
// 最終傷害 += $Pierce（無視 DEF）
```

Pierce 傷害獨立計算，不受 DEF 減免。

---

## 4. 防禦/守護機制

### 4.1 前衛守護 (Guard/Protect)

```php
function &Defending(&$target, &$candidate, $skill)
{
    // 跳過條件
    if ($skill["invalid"]) return false;  // 防禦貫穿技
    if ($skill["support"]) return false;  // 支援技
    if ($target->POSITION == POSITION_FRONT) return false;  // 前衛不需守護

    // 候選前衛：前衛 + 生存 + HP > 1
    foreach ($candidate as &$char) {
        if ($char->POSITION == POSITION_FRONT
            && $char->STATE !== STATE_DEAD
            && 1 < $char->HP) {
            $fore[] = &$char;
        }
    }

    // 依 guard 行為判定是否守護
    shuffle($fore);
    foreach ($fore as &$char) {
        switch ($char->behavior['guard']) {
            case 'always':                $defender = $char; break;
            case 'life25': case 'life50': case 'life75':
                sscanf($char->behavior['guard'], 'life%2d', $n);
                if ($n < $HpRate) $defender = $char;
                break;
            case 'prob25': case 'prob50': case 'prob75':
                sscanf($char->behavior['guard'], 'prob%2d', $n);
                if ($prob < $n) $defender = $char;
                break;
            case 'never': default: continue;
        }
        if ($defender) return $defender;  // 守護者取代目標
    }
}
```

### 4.2 守護行為一覽

| guard 值 | 條件 | 說明 |
|----------|------|------|
| `always` | 必定守護 | 總是保護後衛 |
| `life25` | HP > 25% 時守護 | HP 太低不守 |
| `life50` | HP > 50% 時守護 | |
| `life75` | HP > 75% 時守護 | |
| `prob25` | 25% 機率守護 | 隨機 |
| `prob50` | 50% 機率守護 | |
| `prob75` | 75% 機率守護 | |
| `never` | 不守護 | 預設行為 |

### 4.3 守護的限制

- **全體攻擊不觸發守護**：`target[1] == "all"` 時跳過 `Defending()`
- **僅單體/多體觸發**：`individual` 和 `multi` 才會呼叫 `Defending()`
- **多段攻擊中，守護者 HP ≤ 1 時不再守護**

---

## 5. 回復公式

### 5.1 HP 回復

```php
static function CalcRecoveryValue($skill, $char, $target) {
    $int = $char->INT;
    $heal = sqrt($int) * 10;
    $heal += $char->atk["1"];           // 裝備魔攻
    $heal *= $skill["pow"] / 100;       // 技能倍率
    $heal = ceil($heal);
    return $heal;
}
```

```
Heal = ceil( (sqrt(INT) × 10 + atk[1]) × (pow / 100) )
```

### 5.2 SP 回復

```
SpRec = ceil(sqrt(MAXSP) × SpRecoveryRate)
```

### 5.3 持續回復 (Regen)

```
HpRegen 回復量 = round(MAXHP × HpRegen / 100)   每回合
SpRegen 回復量 = round(MAXSP × SpRegen / 100)   每回合
```

### 5.4 吸收 (Absorb)

```
AbsorbHP = 對目標造成傷害 × 同量 HP 回復給使用者
AbsorbSP = 對目標造成 SP 傷害 × 同量 SP 回復給使用者
```

---

## 6. 狀態異常

### 6.1 中毒 (Poison)

#### 中毒判定

```php
function GetPoison($BePoison) {
    if ($this->char->STATE === STATE_POISON) return false;  // 已中毒

    if ($this->char->SPECIAL["PoisonResist"]) {
        // 有抗性：機率降低
        $BePoison *= (1 - $this->char->SPECIAL["PoisonResist"] / 100);
        if (mt_rand(0, 99) < $BePoison) {
            $this->char->STATE = STATE_POISON;
            return true;
        } else {
            return "BLOCK";  // 抵抗
        }
    }

    $this->char->STATE = STATE_POISON2;  // 無抗性直接中毒
    return true;
}
```

#### 毒傷害公式

```php
function PoisonDamageFormula($multiply = 1) {
    $damage = round($this->char->MAXHP * 0.10) + ceil($this->char->level / 2);
    $damage *= $multiply;
    return round($damage);
}
```

```
毒傷害 = (MAXHP × 10% + ceil(level / 2)) × 倍率
```

- 使用 `HpDamage2()` — 不會致死，最低 HP 為 1
- 每回合自動觸發（在 `Action()` 中呼叫）

### 6.2 毒抗性獲得

```php
function GetPoisonResist($no) {
    $Add = (100 - $this->char->SPECIAL["PoisonResist"]) * ($no / 100);
    $this->char->SPECIAL["PoisonResist"] += round($Add);
}
```

抗性為遞減式：已有 50% 抗性再獲得 50% 效果 → 實際 +25%（共 75%）。

---

## 7. Buff/Debuff 系統

### 7.1 Up (百分比增益)

```php
function UpSTR($no) {
    $this->char->STR = round($this->char->STR * (1 + $no / 100));
    // 上限檢查：不超過原始值 × MAX_STATUS_MAXIMUM%
    if ($this->char->str * MAX_STATUS_MAXIMUM / 100 < $this->char->STR) {
        $this->char->STR = round($this->char->str * MAX_STATUS_MAXIMUM / 100);
    }
}
```

### 7.2 Down (百分比減益)

```php
function DownSTR($no) {
    $this->char->STR = round($this->char->STR * (1 - $no / 100));
}
```

### 7.3 DEF Up (遞增式)

```php
function UpDEF($no) {
    $up = floor((100 - $this->char->def["0"]) * ($no / 100));
    $this->char->def["0"] += $up;
}
```

DEF% 的 Up 是遞增式：DEF 50% 時 Up 50% → 實際 +25%（共 75%），不會超過 100%。

### 7.4 Plus (永久加算)

```php
function PlusSTR($no) {
    $this->char->STR += $no;  // 直接加值，無上限
}
```

### 7.5 效果總覽

| 類型 | 公式 | 上限 | 範例 |
|------|------|------|------|
| `UpSTR(10)` | `STR ×= 1.10` | 原始值 × MAX_STATUS_MAXIMUM% | STR 100 → 110 |
| `DownSTR(10)` | `STR ×= 0.90` | 無 | STR 100 → 90 |
| `PlusSTR(50)` | `STR += 50` | 無 | STR 100 → 150 |
| `UpDEF(10)` | `def[0] += floor((100-def[0]) × 0.10)` | 100% | DEF 50% → 55% |
| `DownDEF(10)` | `def[0] ×= 0.90` | 無 | DEF 50% → 45% |
| `UpATK(10)` | `atk[0] ×= 1.10` | 無 | ATK 100 → 110 |

---

## 8. SP 消耗與 HP 犧牲

### 8.1 SP 消耗

```php
// 怪物使用技能 SP 折扣
if ($My->isMon()) {
    $skill["sp"] *= 0.7;  // 7 折

    // 蘇生系 further 折扣
    if (in_array($skill_no, array(3040, 5030, 5063))) {
        $skill["sp"] *= 0.1;  // 再 1 折（共 0.07）
    }
    $skill["sp"] = (int)$skill["sp"];
}
```

| 角色類型 | SP 消耗 | 蘇生系 SP |
|---------|---------|----------|
| 玩家角色 | 100% | 100% |
| 怪物 | 70% | 7% |

### 8.2 HP 犧牲 (Sacrifice)

```php
function SacrificeHp($rate) {
    $SelfDamage = ceil($this->char->MAXHP * ($rate / 100));
    if ($this->char->POSITION != POSITION_FRONT) $SelfDamage *= 2;
    $this->char->HpDamage($SelfDamage);
}
```

```
犧牲 HP = ceil(MAXHP × rate / 100)
若在後衛 → 再 ×2
```

---

## 9. 詠唱/蓄力系統

### 9.1 詠唱開始

```php
// 物理系 → 蓄力 (charging)
$My->expect_type = EXPECT_CHARGE;
// 魔法系 → 詠唱 (casting)
$My->expect_type = EXPECT_CAST;

$My->expect = $skill_no;  // 記住要使用的技能
$My->DelayByRate($skill["charge"][0], $this->battle->delay, 1);  // 設定詠唱時間

$this->battle->actions--;  // 詠唱不算一次行動
```

### 9.2 詠唱完成

```php
// 下次行動時若 $char->expect 有值 → 直接使用該技能
if ($char->expect) {
    $skill = $char->expect;
    $return = &$char->target_expect;
    // → 跳過 AI 判定，直接 UseSkill
}
```

### 9.3 硬直 (Stiff)

```php
// 技能使用後
if ($skill["charge"][1]) {
    $My->DelayReset();
    $My->DelayByRate($skill["charge"][1], $this->battle->delay, 1);
}
```

---

## 10. 目標選擇與優先順位

### 10.1 priority 行為

| priority | 行為 | 無候選時 |
|----------|------|---------|
| `LowHpRate` | 選 HP% 最低者 | 回退隨機 |
| `Back` | 選後衛角色 | 回退隨機 |
| `Dead` | 選死亡角色 | **技能失敗** |
| `Summon` | 選召喚角色 | **技能失敗** |
| `Charge` | 選詠唱中角色 | **技能失敗** |
| _(省略)_ | 隨機選存活者 | — |

### 10.2 候選篩選

```php
// friend → 我方隊伍
// enemy → 敵方隊伍
// self  → 使用者自身
// all   → 我方 + 敵方合併
```

### 10.3 攻擊範圍

| target[1] | 行為 | 守護 |
|-----------|------|------|
| `individual` | 選 1 目標，重複 N 次 | ✓ 觸發 |
| `multi` | 重複 N 次，每次隨機選 1 | ✓ 觸發 |
| `all` | 全體施放 | ✗ 不觸發 |

---

## 11. 死亡判定與獎勵

### 11.1 死亡判定

```php
function CharJudgeDead() {
    if ($this->char->HP < 1 && $this->char->STATE !== STATE_DEAD) {
        $this->char->STATE = STATE_DEAD;
        $this->char->HP = 0;
        $this->char->ResetExpect();  // 取消詠唱
        return true;
    }
}
```

### 11.2 經驗值分配

```php
function getExp($exp, $team) {
    $Alive = $team->CountTrueChars();  // 非召喚角色數
    $ExpGet = ceil($exp / $Alive);     // 平分給生存者

    foreach ($team as $char) {
        if ($char->STATE === STATE_DEAD) continue;  // 死亡者不給
        $char->getExp($ExpGet);                     // 可能觸發 LvUp
    }
}
```

### 11.3 掉落物

```php
function JudgeTargetsDead(&$target) {
    foreach ($target as $char) {
        if ($char->CharJudgeDead()) {
            $exp   += $char->DropExp();
            $money += $char->DropMoney();
            if ($item = $char->DropItem()) {
                $itemdrop[$item]++;
            }
            if ($char->isSummon()) unset($target[$key]);  // 召喚物消失
        }
    }
    return [$exp, $money, $itemdrop];
}
```

---

## 12. 魔方陣系統

### 12.1 魔方陣操作

```php
// 增加
$this->teams[$team]['mc'] = minmax($mc + $amount, 0, 5);

// 消除
if ($this->teams[$team]['mc'] < $amount) return false;  // 不足
$this->teams[$team]['mc'] -= $amount;
```

- 上限 **5 個**
- 消除不足時技能失敗（`MagicCircleDeleteTeam`）
- 可透過技能增加/消除己方或敵方魔方陣

### 12.2 魔方陣在 Judge 中的應用

```php
case 1840: // 味方の魔法陣の数が **個以上
    if ($Quantity <= $MyTeamMC) return true;
case 1850: // 相手の魔法陣の数が **個以上
    if ($Quantity <= $EnemyTeamMC) return true;
```

---

## 13. 戰鬥結果判定

### 13.1 判定流程

```
BattleResult()
    │
    ├─ 雙方全滅 → BATTLE_DRAW
    ├─ 我方全滅 → TEAM_1 勝利
    ├─ 敵方全滅 → TEAM_0 勝利
    │
    └─ 達到最大回合數 (BattleMaxTurn)
        │
        ├─ 延長條件：
        │   (任一方非全員5人 OR 生存者差>0) AND 未超過 BATTLE_MAX_EXTENDS
        │   → ExtendTurns(TURN_EXTENDS)
        │
        ├─ BattleResultType = 0 → BATTLE_DRAW
        │
        └─ BattleResultType = 1 → 依生存者數判勝敗
            ├─ team0Alive > team1Alive → TEAM_0 勝
            ├─ team1Alive > team0Alive → TEAM_1 勝
            └─ 相同 → BATTLE_DRAW
```

### 13.2 延長條件

```php
$AliveNumDiff = abs(team0存活 - team1存活);
$Not5 = (team0存活 != 5 && team1存活 != 5);

if (($Not5 || $AliveNumDiff > 0) && $BattleMaxTurn < BATTLE_MAX_EXTENDS) {
    ExtendTurns(TURN_EXTENDS);  // 延長回合
}
```

---

## 14. 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Class/Battle.php` | `Process()`, `Action()`, `BattleResult()`, `Defending()`, `SelectTarget()`, `SetDelay()`, `NextActer()` | 戰鬥主引擎 |
| `HOF/Class/Battle/Skill.php` | `UseSkill()` | 技能使用完整流程 |
| `HOF/Class/Battle/Judge.php` | `DecideJudge()`, `MultiFactJudge()` | AI 判定條件系統 |
| `HOF/Class/Battle/Team.php` | `CountAlive()`, `CountDead()`, `CountAliveChars()`, `pick()` | 隊伍管理 |
| `HOF/Class/Skill/Effect.php` | `CalcBasicDamage()`, `CalcRecoveryValue()`, `SkillEffect()`, `StatusChanges()` | 傷害/回復計算、技能效果 |
| `HOF/Class/Char/Battle/Effect.php` | `HpDamage()`, `HpRecover()`, `DelayReset()`, `DelayByRate()`, `PoisonDamage()`, `GetPoison()`, `Move()`, `KnockBack()`, `SacrificeHp()` | 角色戰鬥效果 |
| `HOF/Class/Char/Type/Char.php` | `setBattleVariable()`, `CalcEquips()` | 戰鬥前裝備/被動計算 |

### 相關分析文件

| 文件 | 內容 |
|------|------|
| [戰鬥細節系統分析](05-battle-details.md) | 常數實際數值、AI 判定完整列表、行為模式、傷害波動、狀態持續、回合結束處理 |
| [角色與裝備系統分析](03-char-equipment-system.md) | 角色屬性、HP/SP 公式、裝備系統、升級、技能樹 |
| [隊伍系統分析](04-party-team-system.md) | 隊伍編成、敵方生成、時間系統、戰鬥後處理 |
