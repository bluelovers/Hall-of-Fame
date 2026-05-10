# 職業 (Job) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Job/`
- **檔案數**: 17
- **檔案範例**: `job.100.yml`, `job.101.yml`, ... `job.901.yml`

## 結構定義

```yaml
no: '100'              # 職業編號
equip:                   # 可使用裝備類型
    - Sword
    - TwoHandSword
    - Shield
    - Armor
    - Cloth
    - Robe
    - Item
coe:                     # 屬性係數
    maxhp: 3
    maxsp: 0.5
pattern: null          # 行為模式 (null=無AI)
job: '100'             # 職業編號
img: mon_079           # 圖示代號
job_name: Warrior      # 職業名稱
gender:                # 性別差異
    1:
        img: mon_079
        job_name: Warrior
    2:
        img: mon_080r
        job_name: Warrior
info:                  # 資訊
    desc: "職業描述文字"
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | string | 職業編號 |
| `equip` | array | 可使用裝備類型列表 |
| `coe` | object | 屬性係數 (maxhp, maxsp 等) |
| `pattern` | mixed | 行為模式 (null=無AI) |
| `job` | string | 職業編號 |
| `img` | string | 圖示檔名 |
| `job_name` | string | 職業名稱 |
| `gender` | object | 性別差異設定 (1=男性, 2=女性) |
| `info` | object | 職業資訊 |
| `info.desc` | string | 職業描述 |

## 職業編號範圍

| 範圍 | 職業系統 |
|------|---------|
| 100-103 | 戦士系 (Warrior) |
| 200-203 | 魔法系 (Mage) |
| 300-302 | 弓箭系 (Archer) |
| 400-403 | 機械系 (Machine) |
| 900-901 | 特殊職業 |

## 原始碼存取路徑

### 核心資料存取層

#### 1. 資料模型層 - `HOF_Model_Data`

- **檔案**: `hof/trust_path/HOF/Model/Data.php`
- **繼承**: `HOF_Class_Data`

| 函數 | 行號 | 說明 |
|------|------|------|
| `getJobData($no)` | 861 | 取得單筆職業資料 |
| `getJobList()` | 620 | 取得所有職業編號列表 |
| `getJobConditions()` | 586 | 取得職業轉職條件 |

#### 2. 資料載入層 - `HOF_Class_Data`

- **檔案**: `hof/trust_path/HOF/Class/Data.php`
- **關鍵函數**: `_load($_key, $no)` (第 35-55 行)
- **檔案路徑產生**: `_filename($_key, $no)` (第 23-30 行)
  - 格式: `BASE_TRUST_PATH . '/HOF/Resource/' . ucfirst($_key) . '/' . $_key . '.' . $no . '.yml'`
  - 對應: `hof/trust_path/HOF/Resource/Job/job.{no}.yml`
- **快取機制**: 使用 `$this->data[$_key][$no]` 記憶體快取

### 資料流程

```
YAML 檔案 (job.{no}.yml)
    │
    ▼
HOF_Class_Data::_load('job', $no)
    │  檔案路徑: Resource/Job/job.{no}.yml
    │  記憶體快取: $this->data['job'][$no]
    ▼
HOF_Model_Data::getJobData($no)
    │  呼叫 _load('job', $no)
    │  套用快取機制
    ▼
各系統使用
    - Char: 角色職業設定、轉職
    - JobConditions: 轉職條件判定
```

### 與其他資料類型的關聯性

- **角色 (Char)**: 角色職業 (`job` 欄位) 引用職業編號；職業決定可使用裝備
- **角色類型 (Char/Type/Char)**: `source()` 函數根據職業載入角色基底資料
- **技能樹 (Skilltree)**: 技能樹條件依職業過濾 (`getSkillTreeListByJob`)
- **物品 (Item)**: 物品 `need` 欄位要求特定職業
- **轉職條件 (JobConditions)**: `data_ex.job_conditions` 定義轉職規則