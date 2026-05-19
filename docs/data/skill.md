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

```yaml
no: 1000                 # 技能編號
name: Attack           # 技能名稱
img: skill_042         # 圖示代號
exp: 通常攻撃           # 說明
sp: '0'               # 消耗 SP
type: '0'             # 技能類型 (0=物理, 1=魔法)
learn: '0'            # 習得類型
target:                 # 目標設定
    - enemy            # 目標類型 (friend/enemy/all/self)
    - individual     # 目標方式 (individual/multi/all)
    - 1              # 目標數量
pow: '100'            # 威力百分比
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 技能編號 |
| `name` | string | 技能名稱 |
| `img` | string | 圖示檔名（不含副檔名） |
| `exp` | string | 技能說明/經驗描述 |
| `sp` | string | 消耗 SP |
| `type` | string | 技能類型 (0=物理, 1=魔法) |
| `learn` | string | 習得類型 (0=一般, 其他值特殊) |
| `target` | array | 目標設定 [類型, 方式, 數量] |
| `pow` | string | 威力百分比 (100=1.0倍) |
| `hit` | string | 命中率 (預設100) |
| `invalid` | string | 後衛無效化 (1=是) |
| `support` | string | 支援魔法標記 |
| `priority` | string | 目標優先 (LowHpRate, Dead, Summon, Charge) |
| `charge` | array/string | 詠唱時間/硬直 (charge, stiff) |
| `stiff` | string | 行動後硬直時間 |
| `learn` | string | 習得所需點數 |
| `Up**` | number | 能力上昇 (UpSTR, UpINT, UpDEX, UpSPD, UpLUK) |
| `Down**` | number | 能力下降 |
| `Plus**` | number | 能力加成 (PlusSTR 等) |
| `pierce` | mixed | 貫穿效果 |
| `delay` | number | 延遲效果 |
| `knockback` | number | 擊退效果 |
| `poison` | number | 中毒效果 |
| `summon` | mixed | 召喚效果 |
| `move` | number | 移動效果 |
| `strict` | object | 武器限制 (如 `{"Bow": true}`) |
| `umove` | number | 使用者移動 |
| `passive` | boolean | 被動技能標記 |

## 技能編號範圍

| 範圍 | 類型 |
|------|------|
| 1000-2999 | 攻擊系 |
| 2000-2999 | 魔法系 |
| 2110 | 詠唱中適用 |
| 2300 | 弓系列 |
| 2400-2599 | 召喚系 |
| 3000 | 其他 |
| 3300 | 召喚強化系 |
| 3400 | 持續回復系 |
| 3410 | 魔法陣繪製系 |
| 3420 | 魔法陣消除系 |
| 3900 | 測試用技能 |
| 5000-5999 | 敵人技能 (EnemySkills) |

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