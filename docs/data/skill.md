---
tags:
  - docs/data
  - hof/resource/skill
  - hof/game-data
---

# 技能 (Skill) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Skill/`
- **檔案數**: 268
- **檔案範例**: `skill.1000.yml`, `skill.1001.yml`, ... `skill.9000.yml`

## 結構定義

### 基本結構（所有技能共通）

```yaml
no: 1000                 # 技能編號
name: Attack             # 技能名稱
img: skill_042           # 圖示代號
exp: 通常攻撃            # 說明文字
sp: '0'                  # 消耗 SP
type: '0'                # 技能類型 (0=物理, 1=魔法)
learn: '0'               # 習得所需點數 (0=初始技能/不可學習)
target:                  # 目標設定
    - enemy              # 目標類型 (friend/enemy/all/self)
    - individual         # 目標方式 (individual/multi/all)
    - 1                  # 目標數量/次數
pow: '100'               # 威力百分比 (100=1.0倍基準)
```

### 常見完整結構（含可選欄位）

```yaml
no: 2000
name: FireStorm
img: skill_004a
exp: 施展火焰风暴
sp: '70'
type: '1'                # 1=魔法
learn: '4'               # 習得需 4 點
target:
    - enemy
    - multi              # 隨機多目標
    - 6                  # 6 次
pow: '100'
invalid: '1'             # 防禦貫穿 (前衛守護無效)
charge:                  # [詠唱時間, 硬直時間]
    - 70                 # 詠唱 70
    - 0                  # 無硬直
```

### 含能力變化的完整結構

```yaml
no: 1200
name: PoisonBlow
img: skill_020
exp: 毒之衝擊
sp: '15'
type: '0'
learn: '2'
target:
    - enemy
    - individual
    - 1
pow: '100'
poison: '50'             # 50% 中毒率
UpSTR: '10'              # STR +10 (自身)
DownDEF: '5'             # DEF -5 (目標)
```

## 欄位說明

> **欄位定義來源：** `HOF_Model_Data::getSkill()` (Data.php 第 79-120 行)
>
> - **原始說明** — 原始碼註解中的原始描述，用於遊戲內對照
> - **實際說明** — 經原始碼分析後的完整行為描述

| 欄位 | 類型 | 原始說明 | 實際說明 |
|------|------|---------|---------|
| `no` | integer | — | 技能編號 |
| `name` | string | 名前 | 技能名稱 |
| `img` | string | 画像 | 圖示檔名（不含副檔名），如 `skill_042.png` |
| `exp` | string | 技の説明 | 技能說明文字 |
| `sp` | string | 消費sp | 消耗 SP（怪物使用時自動 ×0.7） |
| `type` | string | 0=物理 1=魔法 | 技能類型：`0` = 物理（以 STR/DEX 計算），`1` = 魔法（以 INT 計算） |
| `target` | array | friend/enemy/all/self, individual/multi/all, 攻撃回數 | 目標設定 `[類型, 方式, 數量]`，詳見 Target 章節 |
| `pow` | string | 100で割った物が倍率になる... 130=1.3倍 100 が基本 | 威力百分比。`100` = 1.0 倍基準，`130` = 1.3 倍。物理/魔法各有獨立公式 |
| `hit` | string | (多分消した...技の成功率...?) | ⚠️ **已棄用** — 原為技能成功率，目前已無實際作用 |
| `invalid` | string | 後衛をかばう動作を無効化 | **防禦貫穿** — 使前衛守護（Guard）機制無效化，攻擊可穿透前衛直接命中後衛。詳見下方獨立說明 |
| `support` | string | 味方の支援魔法(↑と区別が必要) | **支援魔法標記** — 標記為支援技能（回復/增益），不觸發前衛守護，且 `pow` 改為回復倍率。詳見下方獨立說明 |
| `priority` | string | ターゲットの優先(LowHpRate,Dead,Summon,Charge) | 目標優先選擇，含 `LowHpRate`、`Dead`、`Summon`、`Charge`、`Back`。詳見 Priority 章節 |
| `charge` | array | 詠唱完了までの時間やら、力の貯め時間等(0=詠唱無し) → 配列に變更 | `[詠唱時間, 硬直時間]`。例：`[60, 0]` = 詠唱 60、無硬直。物理系顯示為「蓄力」，魔法系顯示為「詠唱」 |
| `stiff` | string | 行動後の硬直時間(0=硬直無し 100=待機時間2倍(待機時間=硬直時間)) | 行動後硬直時間。`0` = 無硬直，`100` = 硬直等於待機時間（即行動間隔×2） |
| `learn` | string | 習得に必要なポイント數 | 習得所需技能點數。`0` = 初始技能（不可透過技能樹學習） |
| `Up**` | number | — | 能力上昇（使用後臨時增益）。適用屬性：STR、INT、DEX、SPD、LUK、ATK、MATK、DEF、MDEF、MAXHP、MAXSP |
| `Down**` | number | — | 能力下降（使用後臨時減益）。適用屬性同 Up 系列。原始碼註解：`IND DEX SPD LUK ATK MATK DEF MDEF HP SP` |
| `Plus**` | number | — | 能力加值（永久性加算，非臨時增益）。如 `PlusSTR => 50` |
| `pierce` | mixed | — | 防禦貫穿傷害。在傷害計算中額外加入無視 DEF/MDEF 的傷害值 |
| `delay` | number | — | 行動延遲效果。延遲目標下次行動（透過 `DelayByRate()` 計算） |
| `knockback` | number | — | 擊退效果（後衛化）。迫使目標從前衛退至後衛，數值為成功率 % |
| `poison` | number | — | 中毒效果。使目標進入中毒狀態，數值為中毒成功率 % |
| `summon` | mixed | — | 召喚怪物。可為單一編號或陣列，召喚物加入使用者所在隊伍 |
| `move` | string | — | 使用者隊列移動。`"front"` = 前衛化，`"back"` = 後衛化 |
| `limit` | object | 武器制限 | 武器限制。限定特定武器類型才能使用，如 `{"Bow": true}` 僅限弓系武器 |
| `umove` | mixed | 使用者が移動 | 使用者使用技能後移動（同 `move`） |
| `passive` | mixed | — | 被動技能標記。`1` = 被動技能，不出現在行動選擇列表中，持續生效 |
| `quick` | mixed | — | 召喚後立即行動。僅限召喚系技能，使召喚物跳過等待直接行動 |
| `sacrifice` | mixed | — | HP 犧牲。使用技能後消耗使用者自身 HP（`SacrificeHp()`） |
| `CurePoison` | mixed | — | 解毒效果。`1` = 使用後自動解除目標中毒狀態 |
| `HpRegen` | mixed | — | 持續 HP 回復。獲得 HpRegen 特殊狀態，每回合回復 HP |
| `SpRegen` | mixed | — | 持續 SP 回復。獲得 SpRegen 特殊狀態，每回合回復 SP |
| `SpRecoveryRate` | mixed | — | SP 回復倍率。回復量 = `sqrt(MAXSP) × SpRecoveryRate` |
| `MagicCircleAdd` | mixed | — | 魔方陣增加。使用者所在隊伍的魔方陣 +N（上限 5） |
| `MagicCircleDelete` | mixed | — | 魔方陣消除。消除指定隊伍的魔方陣 |
| `MagicCircleDeleteTeam` | mixed | — | 消費己方魔方陣。使用技能時消耗己方 N 個魔方陣（不足則技能失敗） |
| `MagicCircleDeleteEnemy` | mixed | — | 消除敵方魔方陣。消除敵方隊伍 N 個魔方陣 |

---

### 🔹 `invalid` 欄位：防禦貫穿（Guard Bypass）

> ⚠️ **常見誤解：** `invalid` 並非「技能無效」或「技能不可用」的意思。

`invalid` 的實際含義是 **「使後衛守護（Guard/Protect）機制無效化」**，即該技能可以**無視前衛角色的守護**，直接命中後衛目標。

#### 原始碼依據

```php
// hof/trust_path/HOF/Class/Battle.php — Defending() 函數
function &Defending(&$target, &$candidate, $skill)
{
    if ($target === false) return false;

    // 防御無視できる技。
    if ($skill["invalid"]) return false;  // ← 直接跳過守護判定

    // 支援なのでガードしない。
    if ($skill["support"]) return false;
    // ...
}
```

當 `invalid` 為 truthy 值（`'1'` 或 `true`）時，`Defending()` 直接回傳 `false`，表示**不觸發前衛守護**，攻擊直接打到原始目標（通常是後衛角色）。

#### 遊戲機制說明

HOF 的戰鬥系統中，後衛角色預設會被前衛角色自動守護（Guard）。當敵方使用單體攻擊技能 targeting 後衛角色時，前衛角色有機率挺身而出代替後衛承受傷害（取決於 `guard` 行為設定）。

設定 `invalid: '1'` 的技能可以**穿透這層守護**，使後衛角色直接暴露在攻擊之下。

#### 使用場景

| 場景 | 說明 |
|------|------|
| 範圍魔法（如 FireBall, FireStorm） | 魔法攻擊範圍廣泛，前衛無法擋住所有目標 |
| 弓箭系列（2300系） | 遠程攻擊可越過前衛直接射擊後衛 |
| 特殊物理技能（如 1018） | 穿透型物理攻擊 |
| 敵人技能（5000系） | 部分敵方技能具有防禦貫穿能力 |

#### 與 `support` 的差異

| 欄位 | 行為 | 範例 |
|------|------|------|
| `invalid: '1'` | 攻擊**穿透前衛守護**，直接命中後衛 | FireBall, FireStorm |
| `support: '1'` | 支援技能**不觸發守護**（因為守護只擋攻擊，不擋增益） | 回復、增益魔法 |

兩者在 `Defending()` 中的效果相同（都跳過守護判定），但**原因不同**：
- `invalid`：攻擊太強，守護擋不住
- `support`：非攻擊技能，守護機制不適用

---

### 🔹 `support` 欄位：支援魔法標記

`support` 標記該技能為**支援類技能**（回復、增益等），具有以下特性：

1. **不觸發前衛守護** — 守護機制只保護後衛免受攻擊，支援技能是有益效果，無需守護
2. **傷害計算改為回復量計算** — 當 `pow` 存在且 `support` 為 truthy 時，`pow` 被視為回復倍率而非攻擊倍率
3. **目標為味方** — 通常搭配 `target: [friend, ...]` 使用

#### 原始碼依據

```php
// hof/trust_path/HOF/Class/Skill/Effect.php — SkillEffect() 函數
if ($skill["pow"])
{
    if ($skill["support"])
    {
        // 支援技能：pow 作為回復倍率
        $heal = self::CalcRecoveryValue($skill, $char, $target);
        self::RecoverHP($target, $heal);
        $this->StatusChanges($skill, $target);
    }
    else
    {
        // 攻擊技能：pow 作為攻擊倍率
        $dmg = self::CalcBasicDamage($skill, $char, $target, $option);
        self::DamageHP($target, $dmg);
    }
}
```

---

### 🔹 Target 欄位詳細規格

#### 結構
```
[target類型, 方式, 數量]
```

##### 1. 類型 (第一個元素)
- `friend` → 指向 **味方**
- `enemy` → 指向 **敵人**
- `all` → 指向 **全體 (味方+敵人)**
- `self` → 指向 **自身**

##### 2. 方式 (第二個元素)
- `individual` → 個別指定目標
- `multi` → 隨機多目標 (可能重複)
- `all` → 全體同時

##### 3. 數量 (第三個元素)
- 整數值，代表攻擊或作用次數
  - `1` → 一次
  - `3` → 三次
  - `5` → 五次 …以此類推

---

### 🔹 Priority 欄位詳細規格

`priority` 控制單體/多體技能的**目標選擇優先順位**。若優先條件無符合候選，則回退為隨機選擇。

| 值 | 說明 | 原始碼邏輯 |
|------|------|------|
| `LowHpRate` | 優先攻擊 **HP% 最低** 的目標 | 遍歷候選，選 HP/MAXHP 比率最低者 |
| `Dead` | 優先對 **死亡角色** 使用 (蘇生系) | 從死亡候選中隨機選取；無死亡者則技能失敗 |
| `Summon` | 優先對 **召喚角色** 使用 | 從召喚候選中隨機選取；無召喚者則技能失敗 |
| `Charge` | 優先對 **詠唱/蓄力中角色** 使用 | 從詠唱中候選中隨機選取；無詠唱者則技能失敗 |
| `Back` | 優先攻擊 **後衛角色** | 從後衛候選中隨機選取；無後衛者則回退為隨機 |
| _(省略)_ | **隨機選擇** | 從存活候選中隨機選取 |

> **注意：** `Dead`、`Summon`、`Charge` 在無符合候選時會導致**技能使用失敗**（回傳 `false`），而 `LowHpRate` 和 `Back` 會回退為隨機選擇。

---

#### 📖 範例對照表

| 設定 | 說明 |
|------|------|
| `["enemy","individual",1]` | 敵人單體攻擊 1 次 |
| `["enemy","individual",3]` | 敵人單體攻擊 3 次 |
| `["enemy","multi",3]` | 隨機敵人 3 人各 1 次 (可能重複) |
| `["enemy","all",1]` | 敵全體各 1 次 |
| `["all","individual",5]` | 全體中隨機 1 人，連續 5 次 |
| `["all","multi",5]` | 全體中隨機 5 人各 1 次 (可能重複) |
| `["all","all",3]` | 全體所有人各 3 次 |
| `["self","individual",1]` | 自身作用 1 次 |

---

#### 📌 套用範例：ChargeShot

hof/trust_path/HOF/Resource/Skill/skill.2305.yml

```yaml
no: 2305
name: ChargeShot
img: item_042
exp: 後衛化
sp: '30'
type: '0'
learn: '6'
target:
  - enemy
  - individual
  - 1
inf: dex
pow: '100'
charge:
  - 30
  - 0
knockback: '100'
limit:
  Bow: true
```

👉 在這個例子中，`target` 設定為：

- **enemy** → 目標是敵人
- **individual** → 單體指定
- **1** → 攻擊 1 次

也就是「對敵人單體進行一次攻擊」。


## 技能編號範圍

> ⚠️ **注意：** 編號範圍有部分重疊。`2000-2999` 同時出現在攻擊系和魔法系中，這反映了原始設計中物理/魔法的混合分類。實際分類以 YAML 檔案中的 `type` 欄位為準（`type: '0'` = 物理, `type: '1'` = 魔法）。

| 範圍 | 類型 | 說明 |
|------|------|------|
| 1000-1999 | 物理攻擊系 | 以 STR/DEX 為基底的物理技能 |
| 2000-2199 | 魔法攻擊系 | 以 INT 為基底的魔法技能 |
| 2110-2111 | 詠唱中適用 | 僅對正在詠唱/蓄力的目標生效 |
| 2300-2399 | 弓箭系 | 遠程物理攻擊，通常帶有 `invalid` (防禦貫穿) |
| 2400-2599 | 召喚系 | 召喚怪物加入戰鬥 |
| 3000-3299 | 回復/輔助系 | HP/SP 回復、增益魔法 |
| 3300-3399 | 召喚強化系 | 僅對召喚角色生效的強化技能 |
| 3400-3499 | 持續效果系 | 持續回復 (3400)、魔方陣繪製 (3410)、魔方陣消除 (3420) |
| 3900-3999 | 測試用 | 除錯/測試用技能 (如 GetPoison, GetDead) |
| 4000-4999 | 特殊行動系 | 隊列移動等特殊行動 (如 StanceRestore) |
| 5000-5999 | 敵人技能 | 怪物/敵方專用技能 (EnemySkills) |

## 原始碼存取路徑

### 核心資料存取層

#### 1. 資料模型層 - `HOF_Model_Data`

- **檔案**: `hof/trust_path/HOF/Model/Data.php`
- **繼承**: `HOF_Class_Data`

| 函數 | 行號 | 說明 |
|------|------|------|
| `getSkill($no)` | 77 | 取得單筆技能資料，處理 summon 欄位 |
| `getSkillList()` | 132 | 取得所有技能編號列表 |
| `getSkillPassiveList()` | 138 | 取得所有被動技能編號 |
| `getSkillTreeData($no)` | 162 | 取得技能樹資料 |
| `getSkillTreeList()` | 353 | 取得所有技能樹編號 |
| `getSkillTreeListByJob($_job)` | 189 | 依職業取得可用技能樹 |

#### 2. 資料載入層 - `HOF_Class_Data`

- **檔案**: `hof/trust_path/HOF/Class/Data.php`
- **關鍵函數**: `_load($_key, $no)` (第 35-55 行)
- **檔案路徑產生**: `_filename($_key, $no)` (第 23-30 行)
  - 格式: `BASE_TRUST_PATH . '/HOF/Resource/' . ucfirst($_key) . '/' . $_key . '.' . $no . '.yml'`
  - 對應: `hof/trust_path/HOF/Resource/Skill/skill.{no}.yml`
- **快取機制**: 使用 `$this->data[$_key][$no]` 記憶體快取

### 物件層

#### 3. 技能物件 - `HOF_Class_Skill`

- **檔案**: `hof/trust_path/HOF/Class/Skill.php`
- **繼承**: `HOF_Class_Base_ObjectAttr`
- **建構子**: `__construct($no)` (第 11 行)
  - 若 `$no` 為陣列則直接使用，否則呼叫 `HOF_Model_Data::getSkill($no, true)`

#### 4. 技能樹 - `HOF_Class_Skill_Tree`

- **檔案**: `hof/trust_path/HOF/Class/Skill/Tree.php`
- **用途**: 角色技能樹管理，學習/忘卻技能

#### 5. 技能效果 - `HOF_Class_Skill_Effect`

- **檔案**: `hof/trust_path/HOF/Class/Skill/Effect.php`
- **用途**: 技能效果計算與應用

### 控制器呼叫位置

#### 6. 戰鬥系統 - `HOF_Class_Battle`

- **檔案**: `hof/trust_path/HOF/Class/Battle.php`
- **觸發時機**: 戰鬥中使用技能
- **呼叫**: 透過 `HOF_Class_Skill_Effect` 擴展

#### 7. 角色控制器 - `HOF_Controller_Char`

- **檔案**: `hof/trust_path/HOF/Controller/Char.php`
- **觸發時機**: 行動設定、技能管理
- **呼叫**: `HOF_Model_Data::getJudgeList()` (第 338 行), `HOF_Model_Data::getJudgeData($k)` (第 342 行)

#### 8. 模板顯示

- **檔案**: `hof/trust_path/tpl/char/char.skill.php` — 角色技能列表
- **檔案**: `hof/trust_path/tpl/char/char.skill_learn.php` — 技能學習
- **檔案**: `hof/trust_path/tpl/manual.manual.php` (第 441 行) — 說明手冊
- **檔案**: `hof/trust_path/tpl/manual.manual2.php` (第 56, 80, 104, 128, 152 行) — 說明手冊2
- **呼叫**: `HOF_Class_Skill::ShowSkillDetail(HOF_Model_Data::getSkill($no))`

#### 9. 管理後台

- **檔案**: `hof/trust_path/admin/list_job.php` (第 82-83 行)
- **檔案**: `hof/trust_path/admin/list_monster.php` (第 98 行)
- **呼叫**: `HOF_Model_Data::getSkill($skill)`, `HOF_Class_Skill::ShowSkillDetail($skill)`

#### 10. 資料管理控制器

- **檔案**: `hof/trust_path/HOF/Controller/Gamedata.php` (第 120 行)
- **呼叫**: `HOF_Model_Data::getItemData($ItemNo)` (物品資料)

### 資料流程

```
YAML 檔案 (skill.{no}.yml)
    │
    ▼
HOF_Class_Data::_load('skill', $no)
    │  檔案路徑: Resource/Skill/skill.{no}.yml
    │  記憶體快取: $this->data['skill'][$no]
    ▼
HOF_Model_Data::getSkill($no)
    │  呼叫 _load('skill', $no)
    │  處理 summon 欄位 (轉為陣列)
    ▼
HOF_Class_Skill::__construct($no)
    │  呼叫 getSkill($no, true) 取得原始資料
    │  繼承 HOF_Class_Base_ObjectAttr
    ▼
各系統使用
    - Battle: 戰鬥行動判定
    - Char: 技能學習/顯示
    - Skill_Tree: 技能樹管理
    - Skill_Effect: 技能效果計算
```

### 與其他資料類型的關聯性

- **角色 (Char)**: 角色初始技能列表 (`skill` 欄位) 引用技能編號
- **職業 (Job)**: 職業決定可學習的技能
- **技能樹 (Skilltree)**: 技能樹定義技能學習條件與路徑
- **判定 (Judge)**: 行為模式中的判定條件決定何時使用技能
- **怪物 (Mon)**: 怪物擁有技能 (`skill` 欄位)
- **物品 (Item)**: 部分物品效果涉及技能增強

---

## 參考檔案

> 本文件分析所依據的核心原始碼檔案。

| 檔案 | 說明 | 關鍵內容 |
|------|------|---------|
| `hof/trust_path/HOF/Model/Data.php` | 資料模型層 | 技能欄位定義（`getSkill()` 第 79-120 行）、技能列表取得、技能樹查詢、`invalid`/`support` 的原始說明 |
| `hof/trust_path/HOF/Class/Battle.php` | 戰鬥系統核心 | `Defending()` — `invalid` 與 `support` 的守護跳過邏輯（第 790-860 行）；`SelectTarget()` — `priority` 的目標選擇邏輯（第 933-1018 行）；`BattleResult()` — 戰鬥結果判定 |
| `hof/trust_path/HOF/Class/Skill.php` | 技能物件 | 技能資料封裝、`ShowSkillDetail()` 顯示技能資訊 |
| `hof/trust_path/HOF/Class/Battle/Skill.php` | 戰鬥技能使用流程 | `UseSkill()` — 完整的技能使用流程：武器限制檢查、SP 檢查、詠唱/蓄力處理、目標選擇、守護判定、效果套用、硬直處理（第 25-236 行） |
| `hof/trust_path/tpl/layout/skill.detail.php` | 技能詳細資訊模板 | 技能資料的前端顯示佈局 |