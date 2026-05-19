---
tags:
  - docs/data
  - test
  - testing
---

# 測試資料 (test) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/test/`
- **檔案數**: 35+ (PHP 測試腳本 + 1 YAML)
- **格式說明**: 此目錄包含 PHP 測試腳本及少數 YAML/JSON 測試資料

## 檔案列表

### PHP 測試腳本

| 檔案 | 說明 |
|------|------|
| `test.php` | 主測試入口 / 綜合測試 |
| `char.1.php` | 角色資料測試 |
| `char.2.php` | 角色資料測試 (擴充) |
| `char.equip.php` | 角色裝備測試 |
| `skill.tree.1.php` | 技能樹資料測試 |
| `skill.tree.2.php` | 技能樹進階測試 |
| `skill.tree.3.php` | 技能樹簡易測試 |
| `judge.1.php` | 判定系統測試 |
| `judge.2.php` | 判定系統測試 (簡易) |
| `judge.php` | 判定系統測試 (綜合) |
| `land.1.php` | 地圖資料測試 |
| `mon.1.php` | 怪物資料測試 |
| `mon.2.php` | 怪物資料測試 (擴充) |
| `mon.reward.1.php` | 怪物掉落測試 |
| `job.class.change.php` | 職業轉職測試 |
| `union.2.php` | 聯盟測試 |
| `test.union.1.php` | 聯盟測試 (綜合) |
| `team.1.php` | 隊伍測試 |
| `session.1.php` | Session 測試 |
| `request.1.php` / `request.2.php` | 請求測試 |
| `env.chk.php` | 環境檢查 |
| `gender.1.php` | 性別設定測試 |
| `icon.1.php` | 圖示測試 |
| `img.delete.php` | 圖片刪除測試 |
| `obj.extend.php` | 物件擴充測試 |
| `php.clone.1.php` | PHP clone 測試 |
| `php.func.1.php` | PHP 函數測試 |
| `php.isset.1.php` | isset 測試 |
| `php.ref.1.php` / `php.ref.2.php` | 引用測試 |
| `date.2.php` / `date.scale.1.php` | 日期測試 |
| `crypto.1.php` | 加密測試 |
| `test.array.1.php` | 陣列測試 |
| `test.cache.php` | 快取測試 |
| `test.move.dir.php` | 目錄移動測試 |
| `test.router.php` | 路由測試 |
| `bootstrap.php` | 啟動測試 |
| `cache.php` | 快取測試 |

### YAML 測試資料

| 檔案 | 說明 |
|------|------|
| `skill.tree.yml` | 技能樹大型 YAML 測試資料 (24KB+) |

### 資料鎖定

| 檔案 | 說明 |
|------|------|
| `initialize.lock` | 初始化鎖定檔 |

## 結構定義 (skill.tree.yml)

```yaml
base:                    # 基礎技能
    100: {  }
    101: {  }
    102:
        - '1100'
        - '1113'
    # ... 更多基礎技能定義

base_2:                  # 第二類型基礎技能
    100:
        - '1003'
        - '1011'
        - '1013'
        - '1014'
        - '1016'
        - '1017'
        - '3110'
        - '3120'
    # ... 更多定義

char_skill_3:            # 角色專用技能
    - '1001'
    - '1002'
    - '1003'
    # ... 更多定義

job:                     # 各職業技能樹
    100:                 # 職業 100 (Warrior)
        1001:            # 技能 1001
            - '1003'
            - '1013'
            - '3110'
            - '3120'
        1003:            # 技能 1003
            - '1011'
            - '1017'
        # ... 更多職業和技能定義
```

## 說明

`skill.tree.yml` 是一個大型技能樹配置檔案，定義了各職業的技能學習路徑。它包含：

1. `base`: 基礎技能，所有職業都可以學習的技能
2. `base_2`: 第二類型基礎技能
3. `char_skill_3`: 角色專用技能
4. `job`: 各職業的技能樹，定義每個職業可以學習哪些技能以及學習條件

這個檔案用於定義遊戲中的技能進修系統，玩家需要滿足一定條件（如等級、已學習的技能等）才能學習新技能。

## 原始碼存取路徑

### 1. 技能樹測試 - `test/skill.tree.1.php`

- **觸發時機**: 測試技能樹資料載入
- **呼叫**: `HOF_Model_Data::getSkillList()`

### 2. 角色測試 - `test/char.2.php`

- **觸發時機**: 測試角色資料載入
- **呼叫**: `HOF_Model_Char::getBaseCharStatus()`

### 3. 判定測試 - `test/judge.1.php`

- **觸發時機**: 測試判定系統
- **呼叫**: `HOF_Model_Data::getJudgeData()`

### 與其他資料類型的關聯性

- **角色 (Char)**: `char.1.php`, `char.2.php`, `char.equip.php` 測試角色基礎資料
- **技能 (Skill)**: `skill.tree.*.php` 測試技能樹與技能列表
- **判定 (Judge)**: `judge.*.php` 測試判定條件
- **怪物 (Mon)**: `mon.*.php` 測試怪物資料
- **聯盟 (Union)**: `union.2.php`, `test.union.1.php` 測試聯盟功能
- **職業 (Job)**: `job.class.change.php` 測試職業轉職