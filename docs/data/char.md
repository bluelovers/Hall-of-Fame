---
tags:
  - docs/data
  - hof/resource/char
  - hof/game-data
---

# 角色 (Char) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Char/`
- **檔案數**: 4
- **檔案範例**: `char.100.yml`, `char.200.yml`, `char.300.yml`, `char.400.yml`

## 結構定義

```yaml
no: 100                    # 角色編號
name: Warrior            # 角色名稱
level: '1'               # 等級
exp: '0'                 # 經驗值
maxhp: '300'             # 最大 HP
hp: '300'                # 當前 HP
maxsp: '50'              # 最大 SP
sp: '50'               # 當前 SP
str: '10'              # 力量
int: '2'               # 智力
dex: '4'               # 敏捷
spd: '4'               # 速度
luk: '1'               # 幸運
job: '100'             # 職業編號
skill:                   # 初始技能列表
    - 1000
    - 1001
data_ex:                 # 擴展資料
    recruit_money: 2000
equip:                   # 初始裝備
    main_hand: '1000'
    off_hand: '3000'
    armor: '5000'
behavior:                # 行為模式
    position: front
    guard: always
    pattern:
        -
            judge: '1205'
            quantity: '8'
            action: '1001'
        -
            judge: '1000'
            quantity: '0'
            action: '1000'
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 角色編號 |
| `name` | string | 角色名稱 |
| `level` | string | 等級 |
| `exp` | string | 經驗值 |
| `maxhp` | string | 最大 HP |
| `hp` | string | 當前 HP |
| `maxsp` | string | 最大 SP |
| `sp` | string | 當前 SP |
| `str` | string | 力量屬性 |
| `int` | string | 智力屬性 |
| `dex` | string | 敏捷屬性 |
| `spd` | string | 速度屬性 |
| `luk` | string | 幸運屬性 |
| `job` | string | 職業編號 |
| `skill` | array | 初始技能編號列表 |
| `data_ex` | object | 擴展資料 (如招募費用) |
| `equip` | object | 初始裝備設定 |
| `behavior` | object | 戰鬥行為模式 |

## 原始碼存取路徑

### 主要存取函數
- **檔案**: `hof/trust_path/HOF/Model/Char.php`
- **函數**: `getBaseCharStatus($no, $append = array())` (第 72-105 行)
- **存取方式**: 
  ```php
  $char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $no . '.yml');
  ```
- **快取機制**: 使用 key `'char_base'` 進行快取

### 呼叫位置
1. **角色創建**
   - **檔案**: `hof/trust_path/HOF/Model/Char.php`
   - **函數**: `newBaseChar($jobNo, $append = array())` (第 111-118 行)
   - **觸發時機**: 建立新基礎角色時

2. **角色招募**
   - **檔案**: `hof/trust_path/HOF/Controller/Recruit.php`
   - **函數**: `_recruit()` (第 63-162 行) 和 `RecruitShow()` (第 165-221 行)
   - **觸發時機**: 
     - `_recruit()`: 玩家進行角色招募時
     - `RecruitShow()`: 顯示可招募角色列表時

3. **角色類型載入**
   - **檔案**: `hof/trust_path/HOF/Class/Char/Type/Char.php`
   - **函數**: `source($over = false)` (第 80-159 行)
   - **觸發時機**: 載入角色類型資料時（當 type 為 'char' 或 'job'）

4. **測試腳本**
   - **檔案**: `hof/trust_path/test/char.2.php`
   - **觸發時機**: 測試角色資料時

### 資料流程
1. `getBaseCharStatus()` 載入 YAML 檔案並快取結果
2. 其他函數透過此函數取得角色基礎資料
3. 資料可能被進一步處理（如移除 name 欄位、加入 birth 欄位等）
4. 最終用於角色創建、顯示或其他遊戲邏輯

## 與其他資料類型的關聯性
- **職業 (Job)**: 透過 `job` 欄位關聯到 `hof/trust_path/HOF/Resource/Job/job.{no}.yml`
- **技能 (Skill)**: 透過 `skill` 陣列關聯到多個 `hof/trust_path/HOF/Resource/Skill/skill.{no}.yml`
- **裝備 (Item)**: 透過 `equip` 物件關聯到 `hof/trust_path/HOF/Resource/Item/item.{no}.yml`
- **行為模式**: 透過 `behavior.pattern` 關聯到判定 (Judge) 和動作 (Action) 資料