# Monster Level Scaling Analysis — Hall of Fame (HOF)
# 怪物等級動態調整機制分析報告

> **Investigation Date:** 2026-05-10
> **Scope:** hof/trust_path/HOF/ directory
> **Working Tree:** develop5

---

## Summary / 摘要

**Does automatic level scaling exist? / 是否存在自動等級調整？**

**YES / 是** — This game version has a fully implemented dynamic monster level scaling mechanism.

這個遊戲版本具有完整的怪物等級動態調整機制。當玩家隊伍的最高等級成員超過怪物基礎等級 10 級以上時，怪物會被大幅強化以貼近玩家等級。

---

## Mechanism Overview / 機制總覽

### Data Flow / 資料流

Player selects party
        |
        v
MyParty() calculates top_level (highest party member level)
        |
        v
MonsterBattle() loads land data -> picks random monsters from YAML
        |
        v
EnemyParty() creates enemies, comparing top_level vs monster base level
        |
        v
If top_level > monster_base + 10  -->  Aggressive scaling mode
If top_level <= monster_base + 10 -->  Tuning mode
        |
        v
level_fix() proportionally scales stats using new/old level ratio
        |
        v
Battle starts with scaled monsters

---

## 1. Core Scaling Function: `EnemyParty()`
## 核心調整函式

**File:** `hof/trust_path/HOF/Controller/Battle.php`, Lines 530-582

### Full Code / 完整程式碼

```php
function EnemyParty($Amount, $MonsterList, $Specify = false)
{
    $MonsterNumbers = array();

    // Specified (forced) monsters for Union battles
    if ($Specify)
    {
        $MonsterNumbers = (array)$Specify;
    }

    $team = array();
    if (!$Amount) return $team;

    $team = new HOF_Class_Battle_Team();
    $team->data('pick_list', $MonsterList);
    $team->data('plus_list', (array)$MonsterNumbers);
    $team->data('amount', $Amount);

    $MonsterNumbers = array_merge($MonsterNumbers, $team->pickList($Amount, $MonsterList));

    $this->_cache['top_level'];  // Highest player party member level

    HOF_Helper_Math::rand_seed();

    // Tuning mode pool: biased toward 0 and 1
    $lv_arr = range(-3, 5);
    $lv_arr = array_pad($lv_arr, count($lv_arr) + 3, 0);
    $lv_arr = array_pad($lv_arr, count($lv_arr) + 3, 1);
    shuffle($lv_arr);

    foreach ($MonsterNumbers as $Number)
    {
        $char = HOF_Model_Char::newMon($Number);

        if ($this->_cache['top_level'] > ($char->level + 10))
        {
            // === AGGRESSIVE SCALING MODE ===
            $lv = mt_rand(
                floor(($this->_cache['top_level'] - $char->level) / 3),
                round($this->_cache['top_level'] - $char->level + 5)
            );
        }
        else
        {
            // === TUNING MODE ===
            $lv = $lv_arr[array_rand($lv_arr)];  // -3 ~ +5, biased toward 0/1
        }

        $char->level_fix($lv);
        $team[] = $char;
    }

    return $team;
}
```

### Two Scaling Modes / 兩種調整模式

| Mode | Condition | Formula | Effect |
|------|-----------|---------|--------|
| **Aggressive** (積極調整) | `top_level > monster_base + 10` | `mt_rand(floor((T-B)/3), round(T-B+5))` | Large positive level boost toward player level |
| **Tuning** (微調) | `top_level <= monster_base + 10` | Random from pool [-3,-2,-1,0,0,0,1,1,1,2,3,4,5] | Small random tweak (biased to 0 or +1) |

#### Aggressive Mode Example / 積極調整範例

- Player top_level = 50
- Monster base level = 20 (from YAML)
- Condition: 50 > (20 + 10) = 30 → TRUE
- Level addition: `mt_rand(floor(30/3)=10, round(30+5)=35)` → Monster gets +10 to +35 levels
- Result: Monster becomes level 30~55 (toward player's 50)

#### Tuning Mode Example / 微調範例

- Player top_level = 25
- Monster base level = 20 (from YAML)
- Condition: 25 > (20 + 10) = 30 → FALSE
- Level addition: random from [-3, -2, -1, 0, 0, 0, 1, 1, 1, 2, 3, 4, 5]
- Result: Monster becomes level 17~25 (minor adjustment)

---

## 2. How `top_level` Is Determined
## 玩家最高等級的計算方式

**File:** `hof/trust_path/HOF/Controller/Battle.php`, Lines 397-431

```php
function MyParty()
{
    $this->MemorizeParty();
    $MyParty = array();
    $this->_cache['top_level'] = 0;          // Initialize to 0

    foreach ((array)$this->input->input_char_id as $k)
    {
        if ($this->user->char[$k])
        {
            $MyParty[] = $this->user->char[$k];
            $i = max($i, $this->user->char[$k]->level);  // Track highest
        }
    }

    $this->_cache['top_level'] = $i;          // Store highest level
    // ...
}
```

`top_level` = **the highest level among the player's selected party members** (not total, not average).

最高等級隊員的等級，非總和也非平均值。

---

## 3. Stat Scaling: `level_fix()`
## 屬性數值等比調整

**File:** `hof/trust_path/HOF/Class/Char/Abstract.php`, Lines 695-748

```php
function level_fix($lv_add = 0)
{
    if ($this->isChar(true)) return false;  // SKIP for player characters!

    $old['lv'] = $this->level;
    $this->level = max(1, $this->level + $lv_add);

    $div = bcdiv($this->level, $old['lv'], 3);  // Ratio: new_level / old_level

    if (0 !== $cmp = bccomp($div, 1))
    {
        // Dampen extremely large ratios (>10x)
        if ($cmp > 0 && $div > 10)
        {
            $div = bcsub($div, rand(0, 5), 3);
        }

        foreach (array('str', 'int', 'dex', 'spd', 'luk') as $k)
        {
            if (!isset($this->{$k})) continue;

            // For >10x increases, add 50%-175% random variance
            $div2 = ($cmp > 0 && $div > 10)
                ? bcmul($div, bcdiv(mt_rand(50, 175), 100, 3), 3)
                : $div;

            if (is_array($this->{$k}))
            {
                foreach ($this->{$k} as &$v)
                    $v = ceil(bcmul($v, $div2));
            }
            else
            {
                $this->{$k} = ceil(bcmul($this->{$k}, $div2));
            }
        }
    }
    $this->hpsp(-1);  // Refresh HP/SP from new stats
}
```

### Stat Scaling Formula / 屬性公式

```
new_stat = ceil(old_stat * (new_level / old_level))
```

**Special case for extreme scaling (>10x level increase):**
- Ratio is dampened by subtracting 0-5
- Each stat gets an additional random variance multiplier: 50%-175% of the ratio
  - `div2 = ratio * rand(50, 175) / 100`

### Which Stats Are Scaled / 被縮放的屬性

- `str` (Strength)
- `int` (Intelligence)
- `dex` (Dexterity)
- `spd` (Speed)
- `luk` (Luck)

**NOT scaled:** `atk` (Attack), `def` (Defense) — these use the base YAML values.

HP/SP are refreshed via `hpsp(-1)` after scaling, which recalculates from the new stats.

---

## 4. Enemy Count Scaling: `EnemyNumber()`
## 敵人人數調整

**File:** `hof/trust_path/HOF/Controller/Battle.php`, Lines 513-524

```php
function EnemyNumber($party)
{
    $min = count($party);  // Base = number of player party members
    if ($min == 5) return 5;

    $max = $min + ($this->_cache['top_level'] > 5 ? ENEMY_INCREASE : 0);

    if ($max > 5) $max = 5;
    return mt_rand($min, $max);
}
```

### Config Constant / 設定常數

**File:** `hof/trust_path/config/setting.dist.php`, Line 68

```php
define('ENEMY_INCREASE', 1);  // Extra enemy when player is strong enough
```

### Table / 人數對照表

| Party Size | top_level <= 5 | top_level > 5 |
|-----------|---------------|---------------|
| 1 | 1 enemy | 1-2 enemies |
| 2 | 2 enemies | 2-3 enemies |
| 3 | 3 enemies | 3-4 enemies |
| 4 | 4 enemies | 4-5 enemies |
| 5 | 5 enemies | 5 enemies |

---

## 5. Monster Base Data (YAML Resources)
## 怪物基礎資料

**Directory:** `hof/trust_path/HOF/Resource/Mon/`

Monsters have **fixed base levels** with all stats defined in YAML files:

### Low-level Example: GoblinAxe (mon.1000.yml)

```yaml
no: 1000
name: GoblinAxe
level: '1'
maxhp: '140'
str: '8'
int: '3'
dex: '5'
spd: '5'
luk: '1'
atk: [20, 10]
def: [10, 3, 5, 0]
reward:
    moneyhold: '40'
    exphold: '20'
```

### Mid-level Example: DarkElfHunter (mon.1050.yml)

```yaml
no: 1050
name: DarkElfHunter
level: '39'
maxhp: '580'
str: '20'
int: '50'
dex: '140'
spd: '100'
luk: '10'
atk: [50, 30]
def: [20, 20, 30, 40]
reward:
    moneyhold: '200'
    exphold: '500'
```

---

## 6. Land System / 地圖系統

### Land Data Structure (YAML)

**Directory:** `hof/trust_path/HOF/Resource/Land/`

```yaml
# land.ac0.yml — Ancient Cave
no: ac0
land:
    name: 古の洞窟
    land: cave
monster:
    1010: [0, 1]     # monst
