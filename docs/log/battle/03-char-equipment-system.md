# 角色與裝備系統分析

> 分析日期：2026-09-21
> 分析範圍：`HOF/Class/Char/` 目錄 + `HOF/Model/Char.php` + `HOF/Helper/Char.php` + `HOF/Const/`

---

## 目錄

1. [角色類別繼承架構](#1-角色類別繼承架構)
2. [角色類型與分類](#2-角色類型與分類)
3. [角色屬性系統](#3-角色屬性系統)
4. [HP/SP 計算公式](#4-hpsp-計算公式)
5. [裝備系統](#5-裝備系統)
6. [戰鬥變數計算](#6-戰鬥變數計算)
7. [升級系統](#7-升級系統)
8. [技能樹系統](#8-技能樹系統)
9. [轉職系統](#9-轉職系統)
10. [怪物等級調整](#10-怪物等級調整)
11. [召喚系統](#11-召喚系統)
12. [相關原始碼檔案](#12-相關原始碼檔案)

---

## 1. 角色類別繼承架構

```
HOF_Class_Base_Extend_Root
    │
    ▼
HOF_Class_Char_Abstract                    ← 抽象基類，定義所有角色共通屬性
    │
    ├── HOF_Class_Char_Type_Char            ← 玩家角色
    │       ├── 延伸 HOF_Class_Char_Job         (職業系統)
    │       └── 延伸 HOF_Class_Skill_Tree       (技能樹)
    │
    ├── HOF_Class_Char_Type_Mon             ← 怪物
    │
    └── HOF_Class_Char_Type_UnionMon        ← 工會怪物 (Union Boss)

所有角色共同延伸模組 (在 Abstract._extend_init 中註冊)：
    ├── HOF_Class_Char_Attr                 ← 屬性計算（經驗值、升級）
    ├── HOF_Class_Char_Pattern              ← AI 行為模式
    ├── HOF_Class_Char_View                 ← 顯示相關
    ├── HOF_Class_Char_Battle_Effect        ← 戰鬥效果（受傷、回復、延遲、毒等）
    └── HOF_Class_Char_Battle               ← 戰鬥變數設定
```

### 角色物件建立流程

```
HOF_Model_Char::newBaseChar($jobNo)
    │
    ▼
HOF_Class_Char::factory(TYPE_CHAR, 'char:'.$jobNo, $options)
    │
    ▼
HOF_Class_Char_Type_Char::__construct()
    │  ├─ initCharType()         解析類型為 'Char'
    │  ├─ _extend_init()         註冊延伸模組 (Attr, Pattern, View, BattleEffect, Battle)
    │  ├─ init() → no()          設定角色編號
    │  └─ initCharData()
    │       ├─ source()           從 YAML 載入基礎資料 (char.{no}.yml)
    │       ├─ setCharData()      設定屬性、裝備、技能、行為
    │       ├─ extend(Job)        延伸職業模組
    │       ├─ extend(SkillTree)  延伸技能樹模組
    │       └─ hpsp()             計算 HP/SP
    │
    ▼
HOF_Class_Char_Type_Char (完整角色物件)
```

---

## 2. 角色類型與分類

### 類型常數

```php
// HOF_Class_Char
TYPE_CHAR   = 'char'     // 玩家角色
TYPE_MON    = 'mon'      // 怪物
TYPE_SUMMON = 'summon'   // 召喚物
TYPE_UNION  = 'union'    // 工會怪物
```

### 類型判定方法

| 方法 | 判定邏輯 | 說明 |
|------|---------|------|
| `isChar()` | `hasCharType(TYPE_CHAR)` | 玩家角色 |
| `isMon()` | `hasCharType(TYPE_MON)` | 怪物（含工會怪物） |
| `isSummon()` | `hasCharType(TYPE_SUMMON)` | 召喚物 |
| `isUnion()` | `hasCharType(TYPE_UNION)` | 工會怪物 |

> **注意：** 工會怪物同時具有 `mon` + `union` 類型；召喚物同時具有 `mon` + `summon` 類型。

### 性別系統

| 常數 | 值 | 說明 |
|------|-----|------|
| `GENDER_UNKNOW` | 0 | 未知（怪物/無性別） |
| `GENDER_BOY` | 1 | 男性 |
| `GENDER_GIRL` | 2 | 女性 |

性別影響：職業名稱、職業圖片（部分職業有男/女版圖）。

---

## 3. 角色屬性系統

### 3.1 基礎屬性（儲存於 YAML）

| 屬性 | 變數名 | 說明 |
|------|--------|------|
| HP | `$hp` | 目前 HP |
| MaxHP | `$maxhp` | 最大 HP |
| SP | `$sp` | 目前 SP |
| MaxSP | `$maxsp` | 最大 SP |
| STR | `$str` | 力量（物理傷害基底） |
| INT | `$int` | 智力（魔法傷害基底、回復量基底） |
| DEX | `$dex` | 敏捷（命中、部分技能威力基底） |
| SPD | `$spd` | 速度（行動順序） |
| LUK | `$luk` | 幸運（暴擊、掉落率） |

### 3.2 補正值系統

| 補正類型 | 變數前綴 | 來源 | 套用方式 |
|---------|---------|------|---------|
| P_ (Plus) | `$P_STR`, `$P_INT`... | 裝備、被動技能 | 直接加算 |
| M_ (Multiply) | `$M_MAXHP`, `$M_MAXSP` | 裝備、被動技能 | 百分比乘算 |

### 3.3 戰鬥屬性（不儲存，戰鬥時計算）

| 屬性 | 變數名 | 來源 |
|------|--------|------|
| STR | `$STR` | `str + P_STR` |
| INT | `$INT` | `int + P_INT` |
| DEX | `$DEX` | `dex + P_DEX` |
| SPD | `$SPD` | `spd + P_SPD` |
| LUK | `$LUK` | `luk + P_LUK` |
| MAXHP | `$MAXHP` | `maxhp × (1 + M_MAXHP/100) + P_MAXHP` |
| HP | `$HP` | `hp × (1 + M_MAXHP/100) + P_MAXHP` |
| MAXSP | `$MAXSP` | `maxsp × (1 + M_MAXSP/100) + P_MAXSP` |
| SP | `$SP` | `sp × (1 + M_MAXSP/100) + P_MAXSP` |
| ATK | `$atk` | `array(物理攻, 魔法攻)` — 裝備累加 |
| DEF | `$def` | `array(物理%, 物理-, 魔法%, 魔法-)` — 裝備累加 |
| WEAPON | `$WEAPON` | 主手武器類型字串 |

### 3.4 特殊能力 (SPECIAL)

```php
$this->SPECIAL = array(
    "PoisonResist" => 0,    // 毒抗性 (%)
    "HealBonus"    => 0,    // 回復量加成 (%)
    "Barrier"      => 0,    // 絕對防禦次數
    "Pierce"       => array(0 => 0, 1 => 0),  // 防禦貫穿 [物理, 魔法]
    "Summon"       => 0,    // 召喚能力
    "Undead"       => 0,    // 不死族標記
);
```

---

## 4. HP/SP 計算公式

### 4.1 HP 計算

```php
// Job.php → hpsp()
$MaxStatus = MAX_STATUS;                          // 250
$coe = $jobdata['coe'];                           // 職業係數 array('maxhp' => N, 'maxsp' => N)
$div = pow($MaxStatus, 2);                        // 250² = 62500
$RevStr = $MaxStatus - $this->char->str;          // 250 - STR

$new_maxhp = 100 * $coe['maxhp']
           * (1 + ($this->char->level - 1) / 49)
           * (1 + ($MaxStatus > $RevStr
               ? $div - pow($RevStr, 2)
               : pow($RevStr, 2)) / $div);
```

**簡化公式：**
```
MAXHP = 100 × coe_hp × (1 + (level-1)/49) × (1 + STR_factor)

其中 STR_factor：
  若 STR < 250: (250² - (250-STR)²) / 250²
  若 STR ≥ 250: (250-STR)² / 250²
```

**特性：**
- **STR 越高 HP 越多**（STR < 250 時）
- **STR 250 時 HP 因子最高**（接近 1.0）
- **STR 超過 250 後 HP 反而下降**（對稱曲線）
- **等級越高 HP 越多**（線性成長）
- **職業係數 coe['maxhp'] 決定基礎 HP 量**

### 4.2 SP 計算

```php
$RevInt = $MaxStatus - $this->char->int;

$new_maxsp = 100 * $coe['maxsp']
           * (1 + ($this->char->level - 1) / 49)
           * (1 + ($MaxStatus > $RevInt
               ? $div - pow($RevInt, 2)
               : pow($RevInt, 2)) / $div);
```

**與 HP 公式相同結構，但使用 INT 替代 STR。**

### 4.3 HP/SP 永久成長

```php
// 只有增加，不會減少
$this->char->maxhp = max($this->char->maxhp, $new_maxhp);
$this->char->maxsp = max($this->char->maxsp, $new_maxsp);
```

### 4.4 戰鬥中 HP/SP 計算

```php
// Abstract.php → setBattleVariable()
$maxhp = $this->maxhp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
$this->MAXHP = round($maxhp);

$hp = $this->hp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
$this->HP = round($hp);

$maxsp = $this->maxsp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
$this->MAXSP = round($maxsp);

$sp = $this->sp * (1 + ($this->M_MAXSP / 100)) + $this->P_MAXSP;
$this->SP = round($sp);

$this->HP = min($this->HP, $this->MAXHP);
$this->SP = min($this->SP, $this->MAXSP);
```

---

## 5. 裝備系統

### 5.1 裝備欄位

| 欄位 | 常數 | 說明 |
|------|------|------|
| 主手 | `EQUIP_SLOT_MAIN_HAND` | 武器（劍、弓、杖等） |
| 副手 | `EQUIP_SLOT_OFF_HAND` | 盾、書、副武器 |
| 身體 | `EQUIP_SLOT_ARMOR` | 盔甲、布甲、法袍 |
| 道具 | `EQUIP_SLOT_ITEM` | 攜帶道具 |

### 5.2 武器類型分類

#### 主手武器 (MAIN_HAND)

| 類型 | 說明 | dh (雙手) |
|------|------|----------|
| `Sword` | 單手劍 | — |
| `Dagger` | 匕首 | — |
| `Pike` | 長槍 | — |
| `Hatchet` | 斧頭 | — |
| `Wand` | 魔杖 | — |
| `Mace` | 鎚 | — |
| `TwoHandSword` | 雙手劍 | ✓ |
| `Spear` | 長矛 | ✓ |
| `Axe` | 大斧 | ✓ |
| `Staff` | 法杖 | ✓ |
| `Bow` | 弓 | ✓ |
| `CrossBow` | 十字弓 | ✓ |
| `Whip` | 鞭 | ✓ |

#### 副手裝備 (OFF_HAND)

| 類型 | 說明 |
|------|------|
| `Shield` | 盾 |
| `MainGauche` | 輔手劍 |
| `Book` | 書 |

#### 身體裝備 (ARMOR)

| 類型 | 說明 |
|------|------|
| `Armor` | 盔甲 |
| `Cloth` | 布甲 |
| `Robe` | 法袍 |

#### 道具 (ITEM)

| 類型 | 說明 |
|------|------|
| `Item` | 攜帶道具 |

### 5.3 裝備資料結構 (YAML)

```yaml
# item.1000.yml — 武器範例
no: 1000
name: ShortSword
type: Sword           # 武器類型
buy: '500'            # 購買價格
img: we_sword026      # 圖片檔名
atk:                  # 攻擊力 [物理, 魔法]
    - 10              # atk[0] = 物理攻擊力
    - 0               # atk[1] = 魔法攻擊力
handle: '1'           # 裝備負荷
need:                 # 職業需求
    6001: '4'         # job_no: need_level
base_name: ShortSword
type2: WEAPON         # 大分類

# item.3000.yml — 盾牌範例
no: 3000
name: WoodShield
type: Shield
def:                  # 防禦力 [物理%, 物理-, 魔法%, 魔法-]
    - 5               # def[0] = 物理防禦(%)
    - 5               # def[1] = 物理防禦(-)
    - 0               # def[2] = 魔法防禦(%)
    - 0               # def[3] = 魔法防禦(-)
handle: '1'
need:
    6001: '1'
    6020: '4'
```

### 5.4 DEF 結構詳細

```php
$this->def = array(
    [0] => 物理防禦(%),   // 傷害公式：Raw × (1 - def[0]/100)
    [1] => 物理防禦(-),   // 傷害公式：Raw -= def[1]
    [2] => 魔法防禦(%),   // 傷害公式：Raw × (1 - def[2]/100)
    [3] => 魔法防禦(-),   // 傷害公式：Raw -= def[3]
);
```

**減傷順序：百分比 → 固定值**

### 5.5 裝備限制 — Handle (負荷) 系統

```php
// 玩家的最大負荷
function getHandle($equip = false)
{
    if ($equip) {
        // 計算所有裝備的負荷總和
        $handle = 0;
        foreach ($this->equip as $k => $no) {
            if (!$no) continue;
            $_item = HOF_Model_Data::newItem($no);
            $handle += $_item->handle();
        }
        return $handle;
    }

    // 玩家的最大負荷能力
    $handle = 5 + floor($this->level / 10) + floor($this->dex / 5);
    return $handle;
}
```

**最大負荷公式：**
```
MaxHandle = 5 + floor(level / 10) + floor(DEX / 5)
```

**裝備限制規則：**
```php
// setEquip() 中
if ($this->getHandle() < $this->getHandle(true)) {
    $fail = true;  // 裝備後負荷超限，裝備失敗
}
```

- **等級越高** → 負荷上限越高
- **DEX 越高** → 負荷上限越高
- 武器/盾/盔甲/道具各有 `handle` 值
- 裝備總負荷不能超過玩家最大負荷

### 5.6 雙手武器處理 (dh)

```php
// setEquip() 中
case EQUIP_SLOT_MAIN_HAND:
case EQUIP_SLOT_OFF_HAND:
    $chk = $equip_type == EQUIP_SLOT_MAIN_HAND ? EQUIP_SLOT_OFF_HAND : EQUIP_SLOT_MAIN_HAND;

    if ($this->equip->{$chk}) {
        $_item = HOF_Model_Data::newItem($this->equip->{$chk});

        if ($item["dh"] || $_item["dh"]) {
            $return[] = $this->unequip($chk);  // 強制卸下對側裝備
        }
    }
    break;
```

- `dh: true` = 雙手裝備
- 裝備雙手武器時，自動卸下副手
- 裝備副手時，若主手是雙手武器，自動卸下主手

### 5.7 職業裝備限制

```yaml
# item.1000.yml
need:
    6001: '4'     # job 6001 需要 Lv4 才能裝備
    6020: '6'     # job 6020 需要 Lv6 才能裝備
```

> ⚠️ **注意：** 裝備的 `need` 欄位定義了職業需求，但 `setEquip()` 函式中**未檢查**職業需求。職業限制可能在其他地方（如商店購買時）檢查。

### 5.8 裝備能力加成 (CalcEquips)

```php
function CalcEquips()
{
    $this->atk = array(0, 0);
    $this->def = array(0, 0, 0, 0);

    foreach ($this->equip as $place => $no)
    {
        $item = HOF_Model_Data::getItemData($this->equip->{$place});

        // 記住武器類型
        if ($place == EQUIP_SLOT_MAIN_HAND) $this->WEAPON = $item["type"];

        // 攻擊力累加
        $this->atk[0] += $item[atk][0];  // 物理攻擊力
        $this->atk[1] += $item[atk][1];  // 魔法攻擊力

        // 防禦力累加
        $this->def[0] += $item[def][0];  // 物理防禦(%)
        $this->def[1] += $item[def][1];  // 物理防禦(-)
        $this->def[2] += $item[def][2];  // 魔法防禦(%)
        $this->def[3] += $item[def][3];  // 魔法防禦(-)

        // 屬性加成
        $this->P_MAXHP += $item["P_MAXHP"];
        $this->M_MAXHP += $item["M_MAXHP"];
        $this->P_MAXSP += $item["P_MAXSP"];
        $this->M_MAXSP += $item["M_MAXSP"];
        $this->P_STR += $item["P_STR"];
        $this->P_INT += $item["P_INT"];
        $this->P_DEX += $item["P_DEX"];
        $this->P_SPD += $item["P_SPD"];
        $this->P_LUK += $item["P_LUK"];

        // 特殊能力
        if ($item["P_SUMMON"]) $this->GetSpecial("Summon", $item["P_SUMMON"]);
        if ($item["P_PIERCE"]) $this->GetSpecial("Pierce", $item["P_PIERCE"]);
    }
}
```

---

## 6. 戰鬥變數計算

### 6.1 計算流程

```php
// Char/Type/Char.php → setBattleVariable()
function setBattleVariable()
{
    $this->skill_passive();    // 1. 載入被動技能加成
    $this->CalcEquips();       // 2. 計算裝備能力
    parent::setBattleVariable(); // 3. 呼叫 Abstract 的計算
}

// Abstract.php → setBattleVariable()
public function setBattleVariable()
{
    // 1. 設定狀態
    $this->STATE = STATE_ALIVE;
    $this->POSITION = (mt_rand(0,1) ? POSITION_FRONT : POSITION_BACK);

    // 2. 等級調整（怪物專用）
    $this->level_fix();

    // 3. 計算戰鬥 HP/SP
    $maxhp = $this->maxhp * (1 + ($this->M_MAXHP / 100)) + $this->P_MAXHP;
    $this->MAXHP = round($maxhp);
    // ... SP 同理

    // 4. 計算戰鬥屬性
    $this->STR = $this->str + $this->P_STR;
    $this->INT = $this->int + $this->P_INT;
    $this->DEX = $this->dex + $this->P_DEX;
    $this->SPD = $this->spd + $this->P_SPD;
    $this->LUK = $this->luk + $this->P_LUK;

    // 5. 設定 AI 行為模式
    $this->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);
}
```

### 6.2 被動技能加成

```php
function skill_passive()
{
    $passive_list = HOF_Model_Data::getSkillPassiveList();

    foreach ($this->skill as $no)
    {
        if (!in_array($no, $passive_list)) continue;

        $skill = HOF_Model_Data::getSkill($no);

        // 能力值上昇
        if ($skill["P_MAXHP"]) $this->P_MAXHP += $skill["P_MAXHP"];
        if ($skill["P_MAXSP"]) $this->P_MAXSP += $skill["P_MAXSP"];
        if ($skill["P_STR"]) $this->P_STR += $skill["P_STR"];
        if ($skill["P_INT"]) $this->P_INT += $skill["P_INT"];
        if ($skill["P_DEX"]) $this->P_DEX += $skill["P_DEX"];
        if ($skill["P_SPD"]) $this->P_SPD += $skill["P_SPD"];
        if ($skill["P_LUK"]) $this->P_LUK += $skill["P_LUK"];

        // 特殊技能
        if ($skill["HealBonus"]) $this->SPECIAL["HealBonus"] += $skill["HealBonus"];
    }
}
```

---

## 7. 升級系統

### 7.1 經驗值需求

```php
// Attr.php → CalcExpNeed()
switch ($this->char->level)
{
    case 40:  $exp = 30000; break;
    case 41:  $exp = 40000; break;
    case 42:  $exp = 50000; break;
    case 43:  $exp = 60000; break;
    case 44:  $exp = 70000; break;
    case 45:  $exp = 80000; break;
    case 46:  $exp = 100000; break;
    case 47:  $exp = 250000; break;
    case 48:  $exp = 500000; break;
    case 49:  $exp = 999990; break;
    case 50:
    case (50 <= $this->char->level):
        $exp = "MAX"; break;
    case (21 < $this->char->level):
        $exp = 2 * pow($this->char->level, 3) + 100 * $this->char->level + 100;
        $exp -= substr($exp, -2);
        $exp /= 5;
        break;
    default:
        $exp = pow($this->char->level - 1, 2) / 2 * 100 + 100;
        $exp /= 5;
        break;
}
```

### 7.2 經驗值需求表

| 等級 | 所需經驗值 | 公式/備註 |
|------|-----------|----------|
| Lv1→2 | 100 | `(0²/2 × 100 + 100) / 5` |
| Lv2→3 | 110 | `(1²/2 × 100 + 100) / 5` |
| Lv3→4 | 160 | `(2²/2 × 100 + 100) / 5` |
| ... | ... | 等差成長 |
| Lv20→21 | 3,720 | `(19²/2 × 100 + 100) / 5` |
| Lv21→22 | 4,250 | 進入三次方公式區間 |
| Lv22→23 | 4,832 | `2 × 22³ + 100 × 22 + 100` 取前幾位 / 5 |
| ... | ... | 三次方成長 |
| Lv40→41 | 30,000 | 固定值 |
| Lv41→42 | 40,000 | 固定值 |
| ... | ... | 固定值急劇增加 |
| Lv48→49 | 500,000 | 固定值 |
| Lv49→50 | 999,990 | 固定值 |
| Lv50+ | MAX | 無法再升級 |

### 7.3 升級獎勵

```php
function LevelUp()
{
    $this->char->exp = 0;
    $this->char->level++;
    $this->char->statuspoint += GET_STATUS_POINT;  // +5 能力點
    $this->char->skillpoint += GET_SKILL_POINT;     // +2 技能點
}
```

| 獎勵 | 數量 | 說明 |
|------|------|------|
| 能力點 (StatusPoint) | +5 | 可分配到 STR/INT/DEX/SPD/LUK |
| 技能點 (SkillPoint) | +2 | 可學習技能樹中的技能 |

### 7.4 能力值分配

能力點可透過遊戲介面分配到基礎屬性。分配後永久儲存於角色 YAML 中。

---

## 8. 技能樹系統

### 8.1 技能樹結構

```php
// Skill/Tree.php
public function skill_tree()
{
    $_skill = $this->char->skill;    // 已學技能
    $_job = $this->char->job();      // 目前職業
    $_lv = $this->char->level;       // 目前等級

    // 取得該職業的所有技能樹
    $tree_all = HOF_Model_Data::getSkillTreeListByJob($_job);

    // 檢查每個技能樹的學習條件
    foreach ($tree_all as $skill) {
        $data = HOF_Model_Data::getSkillTreeData($skill);
        foreach ($data['check'] as $check_list) {
            // not 條件：排除特定條件
            // or 條件：滿足任一即可
            // check 項目：job, skill, lv
        }
    }
}
```

### 8.2 學習條件類型

| 條件 | 說明 | 範例 |
|------|------|------|
| `job` | 職業限制 | 只有特定職業能學 |
| `skill` | 前置技能 | 需先學會特定技能 |
| `lv` | 等級限制 | 需達到特定等級 |
| `not` | 排除條件 | 滿足任一則不能學 |
| `or` | 選擇條件 | 滿足任一即可 |

### 8.3 技能學習

```php
function skill_learn($no)
{
    // 檢查是否已學
    if (in_array($no, $this->skill)) return array(false, "已修得");

    // 檢查是否在技能樹中
    $tree = $this->skill_tree();
    if (!in_array($no, $tree)) return array(false, "スキルツリーに無い");

    // 消耗技能點
    $skill = HOF_Model_Data::getSkill($no);
    if ($this->skill_point_use($skill["learn"])) {
        $this->skill_add($skill["no"]);
        return array(true, "修得成功");
    } else {
        return array(false, "技能點不足");
    }
}
```

---

## 9. 轉職系統

### 9.1 轉職流程

```php
function job_change_to($job_to)
{
    if (in_array($job_to, $this->job_change_list())) {
        $this->jobdata($job_to);       // 設定新職業
        $this->hpsp();                 // 重新計算 HP/SP

        // 卸下所有裝備
        $items = $this->char->unequip('all');

        return array(true, $items);
    }
    return false;
}
```

### 9.2 轉職條件

```php
function job_change_list()
{
    $job_conditions = HOF_Model_Data::getJobConditions();

    foreach ($job_to as $k => $v) {
        if ($this->char->level >= $v['lv']) {
            $job_allow_change_to[] = $job_to;
        }
    }
}
```

- 需要達到特定等級
- 轉職後**所有裝備會被卸下**
- HP/SP 會重新計算（可能增加或減少）

---

## 10. 怪物等級調整

### 10.1 level_fix 函式

```php
function level_fix($lv_add = 0)
{
    if ($this->isChar(true)) return false;  // 玩家角色不調整

    $old['lv'] = $this->level;
    $this->level = max(1, $this->level + $lv_add);

    $div = bcdiv($this->level, $old['lv'], 3);  // 新/舊等級比

    if (0 !== bccomp($div, 1)) {
        // 等級有變化
        if ($cmp > 0 && $div > 10) {
            $div = bcsub($div, rand(0, 5), 3);  // 大幅提升時隨機削減
        }

        foreach (array('str', 'int', 'dex', 'spd', 'luk') as $k) {
            $div2 = ($cmp > 0 && $div > 10)
                ? bcmul($div, bcdiv(mt_rand(50, 175), 100, 3), 3)  // 大幅提升時隨機倍率
                : $div;

            $this->{$k} = ceil(bcmul($this->{$k}, $div2));
        }
    }

    $this->hpsp(-1);  // 重新計算 HP/SP
}
```

**特性：**
- 等級提升時，所有基礎屬性（STR/INT/DEX/SPD/LUK）按比例成長
- 大幅等級提升（>10倍）時，成長倍率會隨機削減（50%~175%）
- HP/SP 會重新計算

> **呼叫來源：** 此函式由 `EnemyParty()` 在生成敵方隊伍時呼叫，用於調整怪物等級以匹配玩家強度。等級調整量的決定邏輯，參見 [隊伍系統 — 6. 敵方隊伍生成](04-party-team-system.md#6-敵方隊伍生成)。

---

## 11. 召喚系統

### 11.1 召喚流程

```php
function newMonSummon($no, $strength = false)
{
    $char = HOF_Class_Char::factory(
        array(TYPE_MON, TYPE_SUMMON),
        $no,
        array('strength' => $strength)
    );
    $char->setBattleVariable();
    return $char;
}
```

### 11.2 召喚強化

```php
if ($strength) {
    $monster["maxhp"] = round($monster["maxhp"] * $strength);
    $monster["hp"] = round($monster["hp"] * $strength);
    // ... 所有屬性按 $strength 倍率強化
    $monster["atk"]["0"] = round($monster["atk"]["0"] * $strength);
    $monster["atk"]["1"] = round($monster["atk"]["1"] * $strength);
}
```

### 11.3 召喚物特性

- 不獲得經驗值（`$this->reward = array()`）
- 死亡後從隊伍中移除（`unset($target[$key])`）
- 使用 `P_SUMMON` 裝備特殊能力可召喚特定怪物

---

## 12. 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Class/Char/Abstract.php` | `setBattleVariable()`, `setCharData()`, `level_fix()` | 角色抽象基類，定義屬性、戰鬥變數計算 |
| `HOF/Class/Char/Type/Char.php` | `setEquip()`, `CalcEquips()`, `getHandle()`, `skill_passive()` | 玩家角色，裝備系統、能力計算 |
| `HOF/Class/Char/Type/Mon.php` | `GetNormal()`, `CharJudgeDead()` | 怪物類型 |
| `HOF/Class/Char/Type/UnionMon.php` | `PoisonDamageFormula()`, `DelayByRate()` | 工會怪物 |
| `HOF/Class/Char/Attr.php` | `CalcExpNeed()`, `getExp()`, `LevelUp()` | 經驗值、升級計算 |
| `HOF/Class/Char/Job.php` | `hpsp()`, `job_change_to()`, `job_change_list()` | HP/SP 計算、轉職系統 |
| `HOF/Class/Char/Battle.php` | — | 戰鬥相關延伸 |
| `HOF/Class/Char/Battle/Effect.php` | `HpDamage()`, `HpRecover()`, `DelayReset()`, `PoisonDamage()` | 戰鬥效果 |
| `HOF/Class/Char/Pattern.php` | `CHECK_PATTERN()` | AI 行為模式設定 |
| `HOF/Model/Char.php` | `newBaseChar()`, `newMon()`, `newMonSummon()`, `newUnion()` | 角色物件建立 |
| `HOF/Helper/Char.php` | `char_file()`, `user_path()`, `char_is_allow_name()` | 角色輔助函式 |
| `HOF/Class/Skill/Tree.php` | `skill_tree()` | 技能樹查詢 |
