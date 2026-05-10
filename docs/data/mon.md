# 怪物 (Mon) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Mon/`
- **檔案數**: 160+
- **檔案範例**: `mon.1000.yml`, `mon.1001.yml`, ... `mon.5104.yml`

## 結構定義

```yaml
no: 1000                 # 怪物編號
name: GoblinAxe         # 怪物名稱
img: mon_053            # 圖示代號
level: '1'              # 等級
maxhp: '140'            # 最大 HP
hp: '140'               # 當前 HP
maxsp: '80'             # 最大 SP
sp: '80'                # 當前 SP
str: '8'                # 力量
int: '3'                # 智力
dex: '5'                # 敏捷
spd: '5'                # 速度
luk: '1'                # 幸運
special: {  }           # 特殊能力
atk:                     # 攻擊力 [物理, 魔法]
    - 20
    - 10
def:                     # 防禦力 [物理割合, 物理減免, 魔法割合, 魔法減免]
    - 10
    - 3
    - 5
    - 0
info:                     # 資訊
    desc: "怪物描述"
reward:                   # 獎勵
    moneyhold: '40'     # 金錢持有量
    itemtable:            # 掉落物品表
        6000: '1000'    # 物品編號: 掉落機率 (x/10000)
        6001: '1000'
        6002: '600'
        6003: '200'
        7100: '100'
    exphold: '20'       # 持有經驗值
behavior:                 # 行為模式
    guard: life75       # 後衛防禦策略
    pattern:              # 行動模式
        -
            judge: 1940   # 判定編號
            quantity: 50  # 數量/機率
            action: 9000  # 動作 (技能編號)
        -
            judge: 1205
            quantity: 40
            action: 1017
        -
            judge: 1000
            quantity: 0
            action: 1000
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 怪物編號 |
| `name` | string | 怪物名稱 |
| `img` | string | 圖示檔名 |
| `level` | string | 等級 |
| `maxhp` | string | 最大 HP |
| `hp` | string | 當前 HP |
| `maxsp` | string | 最大 SP |
| `sp` | string | 當前 SP |
| `str` | string | 力量 |
| `int` | string | 智力 |
| `dex` | string | 敏捷 |
| `spd` | string | 速度 |
| `luk` | string | 幸運 |
| `special` | object | 特殊能力 |
| `atk` | array | 攻擊力 [物理, 魔法] |
| `def` | array | 防禦力 [物理割合, 物理減免, 魔法割合, 魔法減免] |
| `info.desc` | string | 怪物描述 |
| `reward.moneyhold` | string | 金錢持有量 |
| `reward.itemtable` | object | 掉落物品表 (物品編號: 機率) |
| `reward.exphold` | string | 持有經驗值 |
| `behavior.guard` | string | 後衛防禦策略 |
| `behavior.pattern` | array | 行動模式 |
| `family` | string | 怪物家族 (舊欄位) |
| `monster` | string | 怪物標記 ("1") |
| `land` | string | 出現地圖 |
| `cycle` | string | 出現周期 (聯盟怪物用) |
| `servant` | object | 召喚的雜兵 |
| `servantSpecify` | array | 必定出現的雜兵 |
| `lv_limit` | string | 等級限制 |

## 原始碼存取路徑

### 核心資料存取層

#### 1. 角色模型層 - `HOF_Model_Char`

- **檔案**: `hof/trust_path/HOF/Model/Char.php`
- **繼承**: `HOF_Class_Data`

| 函數 | 行號 | 說明 |
|------|------|------|
| `getBaseMonster($no, $over)` | 331 | 取得怪物基礎資料，自動加上 `monster="1"` 標記 |
| `newMon($no, $over)` | 342 | 建立怪物角色實例 |
| `newMonSummon($no, $strength)` | 366 | 建立召喚怪物 (可強化) |
| `getBaseCharList()` | 42 | 取得角色/怪物基底列表 (含怪物) |

#### 2. 資料載入層 - `HOF_Class_Data`

- **檔案**: `hof/trust_path/HOF/Class/Data.php`
- **關鍵函數**: `_load($_key, $no)` (第 35-55 行)
- **檔案路徑產生**: `_filename($_key, $no)` (第 23-30 行)
  - 格式: `BASE_TRUST_PATH . '/HOF/Resource/' . ucfirst($_key) . '/' . $_key . '.' . $no . '.yml'`
  - 對應: `hof/trust_path/HOF/Resource/Mon/mon.{no}.yml`
- **快取機制**: 使用 `$this->data[$_key][$no]` 記憶體快取

### 角色類型層

#### 3. 怪物類型 - `HOF_Class_Char_Type_Mon`

- **檔案**: `hof/trust_path/HOF/Class/Char/Type/Mon.php`
- **用途**: 怪物角色實例化

### 控制器呼叫位置

#### 4. 角色控制器 - `HOF_Controller_Char`

- **檔案**: `hof/trust_path/HOF/Controller/Char.php`
- **觸發時機**: 顯示怪物資訊

#### 5. 戰鬥系統 - `HOF_Class_Battle`

- **檔案**: `hof/trust_path/HOF/Class/Battle.php`
- **觸發時機**: 戰鬥中加入怪物

### 資料流程

```
YAML 檔案 (mon.{no}.yml)
    │
    ▼
HOF_Class_Data::_load('mon', $no)
    │  檔案路徑: Resource/Mon/mon.{no}.yml
    │  記憶體快取: $this->data['mon'][$no]
    ▼
HOF_Model_Char::getBaseMonster($no)
    │  呼叫 _load('mon', $no)
    │  加上 monster="1" 標記
    ▼
HOF_Class_Char_Type_Mon / HOF_Class_Char::factory(TYPE_MON, $no)
    │  建立怪物角色實例
    ▼
各系統使用
    - Battle: 戰鬥怪物生成
    - Char: 顯示怪物資訊
    - Union: 聯盟怪物管理
```

### 與其他資料類型的關聯性

- **聯盟 (Union)**: 聯盟怪物由 `union_base` + `union_mon` 合併而成
- **判定 (Judge)**: 怪物行為模式中的判定條件
- **技能 (Skill)**: 怪物擁有技能 (`behavior.pattern.action`)
- **物品 (Item)**: 怪物掉落物品 (`reward.itemtable`)
- **地圖 (Land)**: 怪物出現於特定地圖 (`land` 欄位)
- **守護 (Guard)**: 怪物後衛防禦策略 (`behavior.guard`)
- **角色 (Char)**: 怪物資料格式與角色類似 (`HOF_Class_Char_Type_Mon`)