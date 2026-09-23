# 戰鬥過程與結果系統深入分析

> 分析日期：2026-09-23
> 分析範圍：`HOF/Class/Battle.php` + `HOF/Class/Battle/View.php` + `HOF/Class/Battle/Skill.php` + `HOF/Class/Battle/Team.php` + `HOF/Controller/Battle.php` + `HOF/Class/Char/Battle.php` + `HOF/Class/Char/Battle/Effect.php`

---

## 目錄

1. [戰鬥主迴圈 Process()](#1-戰鬥主迴圈-process)
2. [戰鬥回合結構](#2-戰鬥回合結構)
3. [Action — 行動執行](#3-action--行動執行)
4. [BattleResult — 勝敗判定](#4-battleresult--勝敗判定)
5. [延長回合機制](#5-延長回合機制)
6. [View — 戰鬥顯示系統](#6-view--戰鬥顯示系統)
7. [ShowResult — 結果畫面](#7-showresult--結果畫面)
8. [獎勵系統 — EXP/Money/Item](#8-獎勵系統--expmoneyitem)
9. [傷害累計 AddTotalDamage](#9-傷害累計-addtotaldamage)
10. [RecordLog — 戰鬥日誌](#10-recordlog--戰鬥日誌)
11. [Delay 系統在戰鬥中的運作](#11-delay-系統在戰鬥中的運作)
12. [完整戰鬥生命週期](#12-完整戰鬥生命週期)
13. [相關原始碼檔案](#13-相關原始碼檔案)

---

## 1. 戰鬥主迴圈 Process()

> 來源：`HOF_Class_Battle::Process()` (Battle.php 第 534-578 行)

```php
function Process()
{
    // 1. 隨機種子初始化
    HOF_Helper_Math::rand_seed();

    // 2. 戰鬥標題（隊伍資訊表頭）
    $this->BattleHeader();

    // 3. 入場（依 SPD 排序顯示每個角色入場）
    $this->initEnterBattlefield();

    // 4. 主迴圈：直到有勝敗結果
    do
    {
        // 4a. 每 N 回合顯示戰況快照
        if ($this->actions % BATTLE_STAT_TURNS == 0)
        {
            $this->BattleState();       // 顯示角色 HP/SP + 場景圖
            HOF_Helper_Math::rand_seed(); // 重新播種隨機數
        }

        // 4b. 選擇下一個行動者（根據 DELAY_TYPE）
        if (DELAY_TYPE === 0)
            $char = &$this->NextActer();      // 舊版
        elseif (DELAY_TYPE === 1)
            $char = &$this->NextActerNew();   // 新版（目前使用）

        // 4c. 執行行動
        $this->Action($char);

        // 4d. 判定戰鬥是否結束
        $result = $this->BattleResult();

        // 4e. SPD 變化 → 重算 Delay
        if ($this->ChangeDelay)
            $this->SetDelay();

    } while ($result === null || $result === false);
    //    ↑ 迴圈條件：null=未結束, false=延長回合, 非false=已有結果

    // 5. 顯示最終結果
    $this->ShowResult($result);

    // 6. 關閉戰鬥表格
    $this->BattleFoot();
}
```

### 迴圈流程圖

```
Process()
    │
    ├─ rand_seed()
    ├─ BattleHeader()          ← 開啟 <table>，顯示隊伍總覽
    ├─ initEnterBattlefield()  ← 每個角色入場訊息
    │
    └─ do ─────────────────────────────────────────────┐
        │                                               │
        ├─ actions % 10 == 0 ?                          │
        │   └─ BattleState()  ← 截圖快照 + HP/SP 面板    │
        │                                               │
        ├─ NextActerNew()     ← 找 delay 最小的存活者     │
        │                                               │
        ├─ Action($char)      ← AI 判定 + UseSkill       │
        │                                               │
        ├─ BattleResult()     ← 判定勝敗                  │
        │   ├─ 全滅 → result = 隊伍編號 / BATTLE_DRAW    │
        │   ├─ 回合上限 → 延長或判定 → result            │
        │   └─ 未結束 → return false                     │
        │                                               │
        └─ while (result === null || result === false) ─┘
            │
            ├─ ShowResult($result)   ← 顯示勝負 + 獎勵
            └─ BattleFoot()          ← 關閉 </table>
```

### 迴圈終止條件

| `$result` 值 | 意義 | 迴圈行為 |
|-------------|------|---------|
| `null` | 初始值，尚未判定 | 繼續 |
| `false` | 未結束 / 延長回合 | 繼續 |
| `BATTLE_DRAW` (0) | 平手 | **結束** |
| `TEAM_0` (0) | 玩家隊勝 | **結束** |
| `TEAM_1` (1) | 敵方隊勝 | **結束** |

> ⚠️ `BATTLE_DRAW` 和 `TEAM_0` 都是 `0`，在 `ShowResult()` 中透過 `=== BATTLE_DRAW` 嚴格比較區分。

---

## 2. 戰鬥回合結構

### 2.1 行動計數器

```php
var $actions = 0;  // 總行動次數（非傳統「回合」概念）
```

- `actions` 是**所有角色行動的總次數**，不是輪次
- 5v5 戰鬥中，一個「輪次」= 約 10 次 action
- `BATTLE_STAT_TURNS = 10` → 每 10 次行動顯示一次快照

### 2.2 行動次數的增減

| 時機 | 變化 | 來源 |
|------|------|------|
| `Action()` 開始 | `actions++` | Battle.php 第 356 行 |
| 詠唱/蓄力開始 | `actions--` | Skill.php 第 103 行 |
| 一般技能使用 | 不變（已 ++） | — |
| 無 pattern 可用 | 不變（已 ++，但 throw） | Battle.php 第 285 行 |

> 詠唱/蓄力**不計入行動次數**，因為 `actions++` 後又 `actions--`。

### 2.3 ActCount（角色個別行動次數）

```php
// Skill.php 第 112 行
$My->ActCount++;  // 僅在實際使用技能時 ++
```

- `ActCount` 是**角色個別**的行動計數
- 用於 AI 判定代碼 1900/1901/1902（行動次數條件）
- 詠唱/蓄力開始時**不增加**

---

## 3. Action — 行動執行

> 來源：`HOF_Class_Battle::Action()` (Battle.php 第 278-384 行)

### 3.1 完整流程

```php
function Action(&$char)
{
    // 1. 無 pattern → 異常
    if (empty($char->behavior['pattern']))
    {
        $char->delay = $char->SPD;
        throw new RuntimeException(...);
    }

    // 2. 輸出表格列（左/右側欄位）
    echo "<tr><td class=\"ttd2\">";
    if (team == TEAM_0) echo "</td><td class=\"ttd1\">";

    // 3. 判定行動
    if ($char->expect)
    {
        // 詠唱/蓄力完成 → 直接使用儲存的技能
        $skill = $char->expect;
        $return = &$char->target_expect;
    }
    else
    {
        // 步驟 A: 持續回復
        $char->AutoRegeneration();
        // 步驟 B: 毒傷害
        $char->PoisonDamage();

        // 步驟 C: AI 判定迴圈
        $JudgeKey = -1;
        do {
            $Keys = array();
            do {
                $JudgeKey++;
                $Keys[] = $JudgeKey;
                // action=9000（複合判定）→ 繼續加入下一個
            } while (action == 9000 && judge);

            $return = $this->MultiFactJudge($Keys, $char);

            if ($return) {
                $skill = pattern[$JudgeKey]['action'];
                JdgCount[$no]++;  // 記錄判定命中
                break;
            }
        } while (pattern[$JudgeKey]['judge']);  // 還有下一個判定可試
    }

    // 4. 總行動數 +1
    $this->actions++;

    // 5. 執行技能
    if ($skill)
        $this->UseSkill($skill, $return, $char);
    else
    {
        echo "sunk in thought and couldn't act. (No more patterns)";
        $char->DelayReset();
    }

    // 6. 關閉表格列
    echo "</td></tr>";
}
```

### 3.2 詠唱 vs 一般行動

```
char->expect 存在？
    │
    ├─ YES → 直接使用 $char->expect 技能
    │         （跳過 AI 判定、跳過 Regen/Poison）
    │
    └─ NO  → AutoRegeneration() + PoisonDamage()
              → AI 判定 → 決定技能
```

> **關鍵差異：** 詠唱完成的行動**不會觸發 Regen 和毒傷**，因為這些已在詠唱開始前的回合觸發過。

### 3.3 無技能可用的處理

```php
echo $char->Name('bold') . " sunk in thought and couldn't act.<br />(No more patterns)<br />\n";
$char->DelayReset();
```

- 顯示「沉思無法行動」訊息
- **Delay 重置** → 該角色很快會再次行動

---

## 4. BattleResult — 勝敗判定

> 來源：`HOF_Class_Battle::BattleResult()` (Battle.php 第 390-486 行)

### 4.1 判定流程

```php
function BattleResult()
{
    // 檢查雙方全滅
    $team0Lose = (TEAM_0 存活 == 0);
    $team1Lose = (TEAM_1 存活 == 0);

    // ── 情況 1: 雙方全滅 → 平手 ──
    if ($team0Lose && $team1Lose)
        return $this->result = BATTLE_DRAW;    // 0

    // ── 情況 2: 玩家隊全滅 → 敵方勝 ──
    elseif ($team0Lose)
        return $this->result = TEAM_1;         // 1

    // ── 情況 3: 敵方全滅 → 玩家勝 ──
    elseif ($team1Lose)
        return $this->result = TEAM_0;         // 0

    // ── 情況 4: 雙方存活 + 達到回合上限 ──
    elseif ($this->BattleMaxTurn <= $this->actions)
    {
        // 4a. 嘗試延長回合
        $AliveNumDiff = abs(T0存活 - T1存活);
        $Not5 = (T0存活 != 5 && T1存活 != 5);

        if (($Not5 || $AliveNumDiff > 0)
            && $this->BattleMaxTurn < BATTLE_MAX_EXTENDS)
        {
            if ($this->ExtendTurns(TURN_EXTENDS, 1))
                return false;  // ← 延長成功，繼續戰鬥
        }

        // 4b. 無法延長 → 根據 BattleResultType 判定
        if ($this->BattleResultType == 0)
        {
            // 型態 0：平手
            return $this->result = BATTLE_DRAW;
        }
        elseif ($this->BattleResultType == 1)
        {
            // 型態 1：比較存活「角色數」（排除怪物和召喚物）
            $team0Alive = CountAliveChars();  // 非怪物 + 非死亡
            $team1Alive = CountAliveChars();

            if ($team0Alive > $team1Alive) return TEAM_0;
            elseif ($team1Alive > $team0Alive) return TEAM_1;
            else return BATTLE_DRAW;
        }
        else
        {
            // 錯誤代碼
            echo "error321708...";
            return BATTLE_DRAW;
        }
    }

    // ── 情況 5: 雙方存活 + 未到回合上限 → 戰鬥繼續 ──
    return false;
}
```

### 4.2 判定決策樹

```
BattleResult()
    │
    ├─ TEAM_0 全滅 & TEAM_1 全滅 → BATTLE_DRAW (平手)
    │
    ├─ TEAM_0 全滅 → TEAM_1 勝 (敵方勝利)
    │
    ├─ TEAM_1 全滅 → TEAM_0 勝 (玩家勝利)
    │
    ├─ 雙方存活 & actions ≥ BattleMaxTurn
    │   │
    │   ├─ 可延長？(Not5 || 存活差>0) && < MAX_EXTENDS
    │   │   └─ YES → ExtendTurns() → return false (繼續)
    │   │
    │   ├─ 不可延長 & BattleResultType=0
    │   │   └─ → BATTLE_DRAW
    │   │
    │   └─ 不可延長 & BattleResultType=1
    │       ├─ T0 存活角色 > T1 → TEAM_0 勝
    │       ├─ T1 存活角色 > T0 → TEAM_1 勝
    │       └─ 相同 → BATTLE_DRAW
    │
    └─ 雙方存活 & actions < BattleMaxTurn → return false (繼續)
```

### 4.3 BattleResultType 差異

| 型態 | 超時判定 | 使用場景 |
|------|---------|---------|
| `0` (預設) | 平手 | 一般戰鬥 |
| `1` | 比較存活角色數 | 排名戰、工會戰 |

### 4.4 存活計數的三種模式

| 函式 | 排除條件 | 用途 |
|------|---------|------|
| `CountAlive()` | `STATE === STATE_DEAD` | 一般勝敗判定 |
| `CountAliveChars()` | 死亡 **或** `isMon()` | BattleResultType=1 |
| `CountTrueChars()` | `isSummon()` | EXP 分配 |

> **差異：** `CountAliveChars()` 不計算怪物，所以敵方隊（全怪物）的 `CountAliveChars()` 永遠是 0。這意味著 `BattleResultType=1` 時，只要玩家有 1 個存活角色就贏。

---

## 5. 延長回合機制

> 來源：`ExtendTurns()` (Battle.php 第 681-697 行)

### 5.1 常數

| 常數 | 值 | 說明 |
|------|-----|------|
| `BATTLE_MAX_TURNS` | 100 | 初始最大行動數 |
| `TURN_EXTENDS` | 20 | 每次延長量 |
| `BATTLE_MAX_EXTENDS` | 100 | 延長後上限 |

### 5.2 延長條件

```php
// 必須同時滿足：
($Not5 || $AliveNumDiff > 0)     // 條件 A
&& $this->BattleMaxTurn < BATTLE_MAX_EXTENDS  // 條件 B
&& !$this->NoExtends             // 條件 C（LimitTurns 設定）
```

| 條件 | 說明 |
|------|------|
| A: `$Not5` | 雙方存活數**都不等於 5**（表示有人死亡） |
| A: `$AliveNumDiff > 0` | 雙方存活數**有差異** |
| B: `< MAX_EXTENDS` | 尚未達到 100 上限 |
| C: `!NoExtends` | 未呼叫過 `LimitTurns()` |

### 5.3 延長效果

```php
$this->BattleMaxTurn += TURN_EXTENDS;  // +20
if ($this->BattleMaxTurn > 100)
    $this->BattleMaxTurn = 100;        // 上限 100
echo "battle turns extended.";         // 顯示訊息
```

> **注意：** 初始 `BATTLE_MAX_TURNS=100`，`BATTLE_MAX_EXTENDS=100`，所以實際上**永遠無法延長**（100 不小於 100）。這是一個潛在的 bug 或設計限制。

### 5.4 LimitTurns — 手動限制

```php
function LimitTurns($no)
{
    $this->BattleMaxTurn = $no;
    $this->NoExtends = true;  // 永不延長
}
```

用於特殊戰鬥（如排名戰）設定固定回合上限。

---

## 6. View — 戰鬥顯示系統

> 來源：`HOF_Class_Battle_View` (View.php)

### 6.1 類別結構

```php
class HOF_Class_Battle_View
{
    protected $battle;  // 參照 HOF_Class_Battle

    function BattleHeader()   // 開啟表格 + 隊伍總覽
    function BattleState()    // 定期快照（場景圖 + HP/SP）
    function ShowResult($r)   // 最終結果畫面
    function BattleFoot()     // 關閉表格
}
```

### 6.2 顯示時序

```
BattleHeader()          ← <table> 開始
  │
  ├─ initEnterBattlefield()   角色入場（非 View 方法，但輸出到同一表格）
  │
  ├─ [迴圈開始]
  │   ├─ BattleState()        每 10 次行動顯示快照
  │   ├─ Action()             每次行動的結果
  │   └─ ...
  │   [迴圈結束]
  │
  ├─ ShowResult()        最終結果
  │
BattleFoot()             ← </table> 結束
```

### 6.3 BattleHeader — 表頭

計算並顯示雙方隊伍的：
- 總等級 (Total Lv)
- 平均等級 (Average Lv) → 保留 1 位小數
- 總 HP / 總最大 HP

```php
// 平均等級計算
$avelv = round($total_lv / count($team) * 10) / 10;
// 先 ×10 四捨五入到整數，再 ÷10 → 保留 1 位小數

// Union 戰時隱藏敵方 HP
if ($this->battle->UnionBattle) {
    $team1_total_hp = '????';
    $team1_total_maxhp = '????';
}
```

**輸出格式：**
```html
<table class="battle_frame">
  <tr>
    <td class="teams">        ← 左側（TEAM_1 敵方）
      隊伍名 | Total Lv | Average Lv | Total HP
    </td>
    <td class="teams ttd1">   ← 右側（TEAM_0 玩家）
      隊伍名 | Total Lv | Average Lv | Total HP
    </td>
  </tr>
```

### 6.4 BattleState — 定期快照

```php
function BattleState()
{
    // 靜態變數防止重複顯示同一 action
    static $last;
    if ($last !== $this->battle->actions) $last = $this->battle->actions;
    else return false;  // ← 已顯示過，跳過

    // 1. 場景圖片（含捲動導覽）
    echo "<a name=\"s" . $Scroll . "\"></a>";
    echo "<< >> 捲動連結";
    $this->battle->outputImage();  // 呼叫 Battle::outputImage()

    // 2. 左側隊伍（TEAM_1）HP/SP 面板
    echo "<table><tr><td width=50%>";  // 後衛
    foreach (TEAM_1 as $char)
        if ($char->POSITION != FRONT) echo $char->ShowHpSp();
    echo "</td><td width=50%>";         // 前衛
    foreach (TEAM_1 as $char)
        if ($char->POSITION == FRONT) echo $char->ShowHpSp();

    // 3. 右側隊伍（TEAM_0）HP/SP 面板
    // 前衛在左、後衛在右（對稱佈局）
}
```

**顯示規則：**
- 死亡的召喚物**不顯示**
- 左側（TEAM_1）：後衛在左、前衛在右
- 右側（TEAM_0）：前衛在左、後衛在右
- 靜態 `$last` 確保同一 `actions` 只顯示一次

### 6.5 outputImage — 場景圖

```php
function outputImage()
{
    $output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)
        ->setBg($this->BackGround)
        ->setTeams(TEAM_1 隊伍, TEAM_0 隊伍)
        ->setMagicCircle(TEAM_1 魔方陣, TEAM_0 魔方陣)
        ->exec();
    echo $output;
}
```

### 6.6 戰鬥日誌的 HTML 結構

```
<table class="battle_frame">
  <tbody>
    [BattleHeader] 隊伍總覽列
    [BattleState]  場景圖列 (colspan=2)
    [BattleState]  HP/SP 面板列
      ├─ <td class="ttd2"> ← 左側（TEAM_1 行動）
      └─ <td class="ttd1"> ← 右側（TEAM_0 行動）

    [Action] 行動列（交替左右）
      ├─ <td class="ttd2"> ← TEAM_1 行動
      │   └─ </td><td class="ttd1">
      └─ <td class="ttd2">
          └─ </td><td class="ttd1"> ← TEAM_0 行動

    [BattleState] 再次快照...
    ...
    [ShowResult]  結果列
  </tbody>
</table>
```

---

## 7. ShowResult — 結果畫面

> 來源：`HOF_Class_Battle_View::ShowResult()` (View.php 第 183-312 行)

### 7.1 資料收集

```php
// 左側（TEAM_1 敵方）
foreach (TEAM_1 as $char) {
    if ($char->STATE !== STATE_DEAD) $TotalAlive2++;
    $TotalHp2    += $char->HP;
    $TotalMaxHp2 += $char->MAXHP;
}

// 右側（TEAM_0 玩家）
foreach (TEAM_0 as $char) {
    if ($char->STATE !== STATE_DEAD) $TotalAlive1++;
    $TotalHp1    += $char->HP;
    $TotalMaxHp1 += $char->MAXHP;
}
```

### 7.2 三種顯示模式

#### 模式 A：模擬戰（NoResult = true）

```php
if ($this->battle->NoResult) {
    echo "模擬戦終了";
    // 顯示 HP remain / Alive / TotalDamage
    // 不顯示勝負
    return false;
}
```

#### 模式 B：平手

```php
if ($result === BATTLE_DRAW) {
    echo "<span style=\"font-size:150%\">Draw Game</span>";
}
```

#### 模式 C：有勝負

```php
$TeamName = teams[$result]['team']->team_name();
echo "<span style=\"font-size:200%\">{$TeamName} Wins!</span>";
```

### 7.3 顯示內容

```
┌─────────────────────────────────────────────────────┐
│              {隊伍名} Wins!  (或 Draw Game)         │
├──────────────────────┬──────────────────────────────┤
│  左側 (TEAM_1)       │  右側 (TEAM_0)               │
│  HP remain: xxx/xxx  │  HP remain: xxx/xxx          │
│  Alive: x/x          │  Alive: x/x                  │
│  TotalDamage: xxx    │  TotalDamage: xxx            │
│  TotalExp: xxx       │  TotalExp: xxx               │
│  Funds: xxx          │  Funds: xxx                  │
│  Items:              │  Items:                      │
│    [img] 道具名 x N   │    [img] 道具名 x N           │
└──────────────────────┴──────────────────────────────┘
```

**欄位條件：**

| 欄位 | 顯示條件 |
|------|---------|
| HP remain | Union 戰顯示 `????/????`，否則顯示數值 |
| Alive | 無條件顯示 |
| TotalDamage | 無條件顯示（來自 `data->log['dmg']`） |
| TotalExp | `data->reward['exp']` 非 0 時 |
| Funds | `data->reward['money']` 非 0 時 |
| Items | `data->reward['item']` 非空時 |

### 7.4 三種 HP 顯示格式

| 場景 | 左側 (TEAM_1) | 右側 (TEAM_0) |
|------|--------------|--------------|
| 一般 | `$TotalHp2/$TotalMaxHp2` | `$TotalHp1/$TotalMaxHp1` |
| Union | `????/????` | `$TotalHp1/$TotalMaxHp1` |
| 模擬戰 | `$TotalHp2/$TotalMaxHp2` | `$TotalHp1/$TotalMaxHp1` |

---

## 8. 獎勵系統 — EXP/Money/Item

### 8.1 獎勵資料結構

```php
// HOF_Class_Battle_Team::$data (HOF_Class_Array_Prop)
$team->data->reward['exp']    // 總經驗值
$team->data->reward['money']  // 總金錢
$team->data->reward['item']   // 總物品 array(itemno => amount)
$team->data->log['dmg']       // 總傷害
```

### 8.2 獎勵觸發時機

> 來源：`HOF_Class_Battle_Skill::UseSkill()` (Skill.php 第 215-219 行)

```php
// 每次技能使用後，檢查目標是否死亡
list($exp, $money, $itemdrop) = $this->battle->JudgeTargetsDead($candidate);

$this->battle->getExp($exp, $My->team);      // 分配 EXP
$this->battle->getItem($itemdrop, $My->team); // 累積物品
$this->battle->getMoney($money, $My->team);   // 累積金錢
```

### 8.3 JudgeTargetsDead — 死亡獎勵判定

> 來源：`HOF_Class_Battle::JudgeTargetsDead()` (Battle.php 第 865-928 行)

```php
function JudgeTargetsDead(&$target)
{
    foreach ($target as $key => $char)
    {
        // 1. Union 怪：按傷害比例給 EXP
        if (method_exists($char, 'HpDifferenceEXP'))
            $exp += $char->HpDifferenceEXP();

        // 2. 死亡判定
        if ($char->CharJudgeDead())
        {
            // 顯示死亡訊息
            echo "{$char->Name()} down.";

            // 累積獎勵
            $exp     += $char->DropExp();
            $money   += $char->DropMoney();

            // 物品掉落
            if ($item = $char->DropItem())
            {
                $itemdrop[$item]++;
                echo "{$char->Name()} dropped [img] {$item名}";
            }

            // 3. 召喚物死亡 → 從候選列表移除
            if ($char->isSummon())
                unset($target[$key]);

            // 4. 死亡 → 需要重算 Delay
            $this->ChangeDelay();
        }
    }

    return array($exp, $money, $itemdrop);
}
```

### 8.4 DropExp / DropMoney / DropItem

> 來源：`HOF_Class_Char_Battle` (Char/Battle.php 第 63-106 行)

#### DropExp — 經驗值掉落

```php
function DropExp()
{
    if (isset($this->char->reward['exphold']))
    {
        $exp = $this->char->reward['exphold'];
        $this->char->reward['exphold'] = round($exp / 2);  // ← 降為一半
        return (int)$exp;
    }
    return false;
}
```

**特性：**
- 每次死亡掉落 `exphold` 值
- 掉落後 `exphold` **降為一半**（防止重複刷 EXP）
- 死亡復活後再死，只掉一半的一半

#### DropMoney — 金錢掉落

```php
function DropMoney()
{
    if ($this->char->reward['moneyhold'])
    {
        $money = $this->char->reward['moneyhold'];
        $this->char->reward['moneyhold'] = 0;  // ← 歸零
        return (int)$money;
    }
    return false;
}
```

**特性：**
- 一次性掉落全部金錢
- 掉落後歸零（不會重複掉落）

#### DropItem — 物品掉落

```php
function DropItem()
{
    if (!empty($this->char->reward['itemdrop']))
    {
        $item = $this->char->reward['itemdrop'];
        $this->char->reward['itemdrop'] = false;  // ← 清除
        return $item;
    }
    return false;
}
```

**特性：**
- 一次性掉落
- 掉落後清除（不會重複掉落）

### 8.5 HpDifferenceEXP — Union 怪傷害 EXP

> 來源：`HOF_Class_Char_Type_UnionMon` (UnionMon.php 第 309-314 行)

```php
function HpDifferenceEXP()
{
    $dif = $this->maxhp - $this->HP;  // 已造成的傷害
    $exp = ceil($this->reward['exphold'] * ($dif / $this->maxhp));
    return $exp;
}
```

```
EXP = ceil(exphold × (已造成傷害 / 最大HP))
```

**特性：**
- 按**傷害比例**給 EXP（打 50% 血給 50% EXP）
- 每次技能使用後**重複計算**（累積）
- 死亡時 `DropExp()` 再給一次（可能重複計算？需確認）

### 8.6 getExp — EXP 分配

> 來源：`HOF_Class_Battle::getExp()` (Battle.php 第 745-772 行)

```php
function getExp($exp, $team)
{
    if (empty($exp) || !$Alive = $team->CountTrueChars()) return false;

    $team->data->reward['exp'] += $exp;

    // 只分給非召喚物的存活者
    $ExpGet = ceil($exp / $Alive);
    echo "Alives get {$ExpGet}exps.";

    foreach ($team as $char)
    {
        if ($char->STATE === STATE_DEAD) continue;  // 死亡者無 EXP

        if ($char->getExp($ExpGet))  // 升級回傳 true
            echo "{$char->Name()} LevelUp!";
    }
}
```

**分配規則：**
```
每人獲得 EXP = ceil(總EXP / 非召喚物數量)
死亡者：不獲得
召喚物：不參與分配（CountTrueChars 排除）
```

### 8.7 getItem / getMoney — 累積

```php
function getItem($itemdrop, $team)
{
    if (empty($itemdrop)) return false;
    foreach ($itemdrop as $itemno => $amount)
        $team->data->reward['item'][$itemno] += $amount;
}

function getMoney($money, $team)
{
    if (empty($money)) return false;
    echo "{$team->team_name()} Get {$money}.";
    $team->data->reward['money'] += (int)$money;
}
```

### 8.8 獎勵流程圖

```
UseSkill() 執行技能
    │
    ├─ SkillEffect() → 造成傷害
    │
    ├─ AddTotalDamage($My->team, $dmg)  ← 累積傷害到 log['dmg']
    │
    ├─ JudgeTargetsDead($candidate)
    │   │
    │   ├─ 目標死亡？
    │   │   ├─ DropExp()     → $exp += exphold (exphold ÷ 2)
    │   │   ├─ DropMoney()   → $money += moneyhold (moneyhold = 0)
    │   │   ├─ DropItem()    → $itemdrop[item]++ (itemdrop = false)
    │   │   └─ ChangeDelay() → 重算 Delay
    │   │
    │   └─ return [$exp, $money, $itemdrop]
    │
    ├─ getExp($exp, $My->team)
    │   └─ reward['exp'] += exp
    │      每個存活者 ceil(exp / CountTrueChars)
    │      升級檢查
    │
    ├─ getItem($itemdrop, $My->team)
    │   └─ reward['item'][no] += amount
    │
    └─ getMoney($money, $My->team)
        └─ reward['money'] += money
```

### 8.9 Controller 層的獎勵處理

> 來源：`HOF_Controller_Battle` (Controller/Battle.php)

```php
// 戰鬥結束後
$battle->Process();
$battle->SaveCharacters();                    // 保存角色狀態

list($UserMoney) = $battle->ReturnMoney();    // 取得金錢 [team0, team1]
$this->user->getMoney($UserMoney);            // 加入使用者金錢

if ($this->user->options['record_btl_log'])
    $battle->RecordLog();                     // 保存戰鬥日誌

if ($itemdrop = $battle->ReturnItemGet(TEAM_0))  // 取得物品
{
    foreach ($itemdrop as $itemno => $amount)
        $this->user->item_add($itemno, $amount);
    $this->user->item_save();
}
```

---

## 9. 傷害累計 AddTotalDamage

> 來源：`HOF_Class_Battle::AddTotalDamage()` (Battle.php 第 737-740 行)

```php
function AddTotalDamage($team, $dmg)
{
    $team->data->log['dmg'] += (int)$dmg;
}
```

### 9.1 呼叫位置

> 來源：`HOF_Class_Battle_Skill::UseSkill()` (Skill.php)

```php
// 單體攻擊（第 168 行）
for ($i = 0; $i < $skill["target"]["2"]; $i++) {
    $dmg = $this->battle->SkillEffect($skill, $skill_no, $My, $target);
    $this->battle->AddTotalDamage($My->team, $dmg);  // ← 每次傷害都累計
}

// 多體攻擊（第 181 行）
for ($i = 0; $i < $skill["target"]["2"]; $i++) {
    $target = SelectTarget(...);
    $dmg = SkillEffect(...);
    AddTotalDamage($My->team, $dmg);
}

// 全體攻擊（第 200 行）
foreach ($candidate as $target) {
    for ($i = 0; $i < $skill["target"]["2"]; $i++) {
        $dmg = SkillEffect(...);
        AddTotalDamage($My->team, $dmg);
    }
}
```

### 9.2 統計特性

| 項目 | 說明 |
|------|------|
| 累計對象 | `$My->team`（攻擊者所屬隊伍） |
| 累計時機 | 每次 `SkillEffect()` 回傳後 |
| 數值類型 | `(int)$dmg` 強制轉整數 |
| 涵蓋範圍 | 所有傷害（物理/魔法/毒/回復？） |

> ⚠️ **可能的問題：** `SkillEffect()` 的回傳值如果包含**回復量**（`support` 技能），也會被計入 `log['dmg']`。需要確認 `SkillEffect()` 對 `support` 技能的回傳值。

### 9.3 在結果畫面顯示

```php
// View.php 第 259、288 行
echo "TotalDamage : " . $this->battle->teams[TEAM_0]['team']->data->log['dmg'];
echo "TotalDamage : " . $this->battle->teams[TEAM_1]['team']->data->log['dmg'];
```

---

## 10. RecordLog — 戰鬥日誌

> 來源：`HOF_Class_Battle::RecordLog()` (Battle.php 第 223-273 行)

### 10.1 日誌檔案結構

```php
function RecordLog($type = false)
{
    // 1. 決定檔案路徑和上限
    if ($type == "RANK") {
        $file = LOG_BATTLE_RANK;
        $logAmount = MAX_BATTLE_LOG_RANK;      // 100
    }
    elseif ($type == "BASE_PATH_UNION") {
        $file = LOG_BATTLE_UNION;
        $logAmount = MAX_BATTLE_LOG_UNION;     // 100
    }
    else {
        $file = LOG_BATTLE_NORMAL;
        $logAmount = MAX_BATTLE_LOG;           // 100
    }

    // 2. 刪除超出上限的舊日誌
    while ($logAmount <= count($log)) {
        unlink($log[$i]);
        $i++;
    }

    // 3. 建立新日誌檔案
    $time = HOF_Helper_Char::uniqid_birth();   // 時間戳 ID
    $file .= $time . ".dat";

    // 4. 檔案標頭（7 行）
    $head  = $time . "\n";                                    // 開始時間
    $head .= "隊伍0名 <> 隊伍1名\n";                           // 參加團隊
    $head .= "人數0 <> 人數1\n";                               // 參加人數
    $head .= "平均Lv0 <> 平均Lv1\n";                           // 平均等級
    $head .= $this->result . "\n";                            // 勝利團隊
    $head .= $this->actions . "\n";                           // 總行動數
    $head .= "\n";                                            // 空行

    // 5. 寫入檔案 = 標頭 + ob 內容（戰鬥過程 HTML）
    WriteFile($file, $head . ob_get_contents());
}
```

### 10.2 日誌格式範例

```
692a3b1c001234                    ← 時間戳
PlayerName<>Grass Land            ← 隊伍名
3<>4                              ← 人數
15.2<>12.8                        ← 平均等級
0                                  ← 勝利團隊 (0=TEAM_0)
47                                 ← 總行動數
                                   ← 空行
<table class="battle_frame">      ← HTML 戰鬥過程
...（完整戰鬥過程 HTML）
</table>
```

### 10.3 日誌類型

| 類型 | `$type` 參數 | 檔案常數 | 上限常數 |
|------|------------|---------|---------|
| 一般戰鬥 | `false` / 無 | `LOG_BATTLE_NORMAL` | `MAX_BATTLE_LOG` (100) |
| 排名戰 | `"RANK"` | `LOG_BATTLE_RANK` | `MAX_BATTLE_LOG_RANK` (100) |
| 工會戰 | `"BASE_PATH_UNION"` | `LOG_BATTLE_UNION` | `MAX_BATTLE_LOG_UNION` (100) |

### 10.4 觸發條件

```php
// 一般戰鬥：使用者選項控制
if ($this->user->options['record_btl_log'])
    $battle->RecordLog();

// 工會戰：無條件記錄
$battle->RecordLog("BASE_PATH_UNION");

// 排名戰：無條件記錄（需確認）
```

---

## 11. Delay 系統在戰鬥中的運作

### 11.1 SetDelay — 基礎 Delay 計算

> 來源：`HOF_Class_Battle::SetDelay()` (Battle.php 第 1097-1137 行)

```php
function SetDelay()
{
    if (DELAY_TYPE === 0)   // 舊版
    {
        // 計算 MaxSPD 和 AverageSPD
        $this->delay = $MaxSPD + ($AverageSPD * DELAY);
        $this->ChangeDelay = false;
    }
    elseif (DELAY_TYPE === 1)   // 新版（目前使用）
    {
        // 空的！新版不需要預計算基礎 Delay
    }
}
```

> **DELAY_TYPE=1 時 `SetDelay()` 是空操作**，因為新版的 `DelayValue()` 是角色個別計算的。

### 11.2 NextActerNew — 新版行動順序

> 來源：`HOF_Class_Battle::NextActerNew()` (Battle.php 第 584-643 行)

```php
function &NextActerNew()
{
    // 1. 找出 delay 最小的存活者
    $nextDis = 1000;
    foreach ($this->teams as $idx => $data)
    {
        foreach ($data['team'] as $char)
        {
            if ($char->STATE === STATE_DEAD) continue;

            $charDis = $char->nextDis();  // 計算距離

            if ($charDis == $nextDis)
                $NextChar[] = $char;      // 並列
            elseif ($charDis < $nextDis)
            {
                $nextDis = $charDis;
                $NextChar = array($char);
            }
        }
    }

    // 2. 如果最小距離 ≥ 0，全體 delay 減少
    if ($nextDis >= 0)
    {
        foreach ($this->teams as &$data)
            foreach ($data['team'] as &$char)
                $char->Delay($nextDis);   // 全體前進
    }

    // 3. 並列時隨機選一個
    $next = (count($NextChar) > 1)
        ? $NextChar[array_rand($NextChar)]
        : reset($NextChar);

    // 4. 行動者 delay 重置
    $next->DelayByRate(1, $this->delay);

    return $next;
}
```

### 11.3 nextDis — 行動距離計算

> 來源：`HOF_Class_Char_Battle_Effect::nextDis()` (Effect.php 第 112-119 行)

```php
function nextDis()
{
    if ($this->char->STATE === STATE_DEAD) return 100;  // 死亡者距離 100

    $distance = bcdiv(bcsub(100, $this->char->delay), $this->char->DelayValue());
    return $distance;
}
```

```
nextDis = (100 - delay) / DelayValue
DelayValue = sqrt(SPD) + DELAY_BASE(5)
```

### 11.4 Delay 操作函式

| 函式 | DELAY_TYPE=0 | DELAY_TYPE=1 |
|------|-------------|-------------|
| `DelayReset()` | `delay = SPD` | `delay = 0` |
| `Delay($no)` | `delay += $no` | `delay += $no × DelayValue` |
| `DelayValue()` | 不使用 | `sqrt(SPD) + 5` |
| `DelayByRate($No,...)` | `delay -= (BaseDelay - SPD) × $No/100` | `delay -= $No` |

### 11.5 行動後的 Delay 重置

```php
// UseSkill() 末尾（Skill.php 第 234-235 行）
$My->DelayReset();  // delay = 0 (TYPE=1)
```

**例外情況：**

| 情況 | Delay 處理 |
|------|----------|
| 一般技能使用 | `DelayReset()` → `delay = 0` |
| 有硬直 (`charge[1]`) | `DelayReset()` + `DelayByRate(charge[1])` |
| 詠唱/蓄力開始 | `DelayByRate(charge[0])` → 不 Reset |
| SP 不足 | `DelayReset()` |
| 武器不匹配 | `DelayReset()` |
| 魔方陣不足 | `DelayReset()` |
| 無 pattern | `DelayReset()` |

### 11.6 SPD 變化 → Delay 重算

```php
// 戰鬥主迴圈中
if ($this->ChangeDelay)
{
    $this->SetDelay();  // DELAY_TYPE=1 時為空操作
}
```

> **DELAY_TYPE=1 時，SPD 變化不需要重算全局 Delay**，因為 `DelayValue()` 是即時計算的。

---

## 12. 完整戰鬥生命週期

```
 Controller 層
     │
     ├─ 檢查 Time 足夠 (NORMAL_BATTLE_TIME)
     ├─ MyParty() → 玩家隊伍
     ├─ EnemyParty() → 敵方隊伍（含 level_fix）
     ├─ WasteTime()
     │
     ├─ new HOF_Class_Battle($MyParty, $EnemyParty)
     │   ├─ _extend_init() → 載入 View/SkillEffect/Skill/Judge
     │   ├─ 建立 TEAM_0, TEAM_1
     │   ├─ 每個角色 setBattleVariable()
     │   ├─ SetDelay()
     │   └─ DelayResetAll()
     │
     ├─ SetBackGround(), SetTeamName()
     │
     ├─ Process() ──────────────────────────────────┐
     │   ├─ rand_seed()                             │
     │   ├─ BattleHeader()                          │
     │   ├─ initEnterBattlefield()                  │
     │   ├─ do {                                    │
     │   │   ├─ BattleState() (每10行動)             │
     │   │   ├─ NextActerNew()                      │
     │   │   ├─ Action($char)                       │
     │   │   │   ├─ AutoRegeneration()              │
     │   │   │   ├─ PoisonDamage()                  │
     │   │   │   ├─ AI 判定 → skill                 │
     │   │   │   ├─ actions++                       │
     │   │   │   ├─ UseSkill()                      │
     │   │   │   │   ├─ SP 消耗                     │
     │   │   │   │   ├─ 詠唱檢查                    │
     │   │   │   │   ├─ SelectTarget + Defending    │
     │   │   │   │   ├─ SkillEffect() × N           │
     │   │   │   │   ├─ AddTotalDamage()            │
     │   │   │   │   ├─ JudgeTargetsDead()          │
     │   │   │   │   │   ├─ DropExp/Money/Item      │
     │   │   │   │   │   └─ ChangeDelay()           │
     │   │   │   │   ├─ getExp/getItem/getMoney     │
     │   │   │   │   └─ DelayReset()                │
     │   │   │   └─ ...                             │
     │   │   ├─ BattleResult()                      │
     │   │   └─ if ChangeDelay → SetDelay()         │
     │   } while (result === null || false)         │
     │   ├─ ShowResult($result)                     │
     │   └─ BattleFoot()                            │
     │──────────────────────────────────────────────┘
     │
     ├─ SaveCharacters() → 每個角色 saveCharData()
     ├─ ReturnMoney() → [team0金錢, team1金錢]
     ├─ user->getMoney($UserMoney)
     ├─ RecordLog() → 保存 HTML 日誌
     └─ ReturnItemGet(TEAM_0) → user->item_add()
```

---

## 13. 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Class/Battle.php` | `Process()`, `Action()`, `BattleResult()`, `NextActerNew()`, `ExtendTurns()`, `JudgeTargetsDead()`, `SelectTarget()`, `Defending()`, `AddTotalDamage()`, `getExp()`, `getItem()`, `getMoney()`, `ReturnMoney()`, `ReturnItemGet()`, `RecordLog()`, `SaveCharacters()` | 戰鬥主引擎 |
| `HOF/Class/Battle/View.php` | `BattleHeader()`, `BattleState()`, `ShowResult()`, `BattleFoot()` | 戰鬥顯示系統 |
| `HOF/Class/Battle/Skill.php` | `UseSkill()` | 技能使用流程（含獎勵觸發） |
| `HOF/Class/Battle/Team.php` | `CountAlive()`, `CountDead()`, `CountAliveChars()`, `CountTrueChars()`, `pick()`, `pickList()`, `data()` | 隊伍管理與計數 |
| `HOF/Class/Char/Battle.php` | `DropExp()`, `DropMoney()`, `DropItem()`, `GetSpecial()`, `SummonPower()` | 角色戰鬥資料 |
| `HOF/Class/Char/Battle/Effect.php` | `HpDamage()`, `HpDamage2()`, `SpDamage()`, `HpRecover()`, `SpRecover()`, `CharJudgeDead()`, `nextDis()`, `DelayReset()`, `Delay()`, `DelayValue()`, `DelayByRate()`, `AutoRegeneration()`, `PoisonDamage()` | HP/SP 操作、Delay 系統 |
| `HOF/Class/Char/Type/UnionMon.php` | `HpDifferenceEXP()` | Union 怪傷害比例 EXP |
| `HOF/Controller/Battle.php` | `MonsterBattle()`, `UnionProcess()`, `MyParty()`, `EnemyParty()`, `EnemyNumber()`, `RecordLog()` 呼叫 | Controller 層戰鬥流程 |

### 相關分析文件

| 文件 | 內容 |
|------|------|
| [戰鬥機制與算法分析](02-battle-mechanism.md) | 傷害公式、回復公式、Buff/Debuff、詠唱系統 |
| [角色與裝備系統分析](03-char-equipment-system.md) | 角色屬性、HP/SP 公式、裝備系統、升級 |
| [隊伍系統分析](04-party-team-system.md) | 隊伍編成、敵方生成、時間系統 |
| [戰鬥細節系統分析](05-battle-details.md) | 常數數值、AI 判定、行為模式、傷害波動 |
