# 資料類型關係總表

本文件描述 `hof/trust_path/HOF/Resource/` 下各 YAML 資料類型之間的關聯性，以及它們在原始碼中的交互關係。

## 資料類型總覽

| 資料類型 | 目錄 | 檔案數 | 主要模型 | 描述 |
|---------|------|--------|---------|------|
| Char (角色) | `Char/` | 4 | `HOF_Model_Char` | 玩家角色與怪物基底資料 |
| Item (物品) | `Item/` | 181 | `HOF_Model_Data` | 武器、防具、消耗品、素材等 |
| Skill (技能) | `Skill/` | 268 | `HOF_Model_Data` | 主動/被動技能 |
| Job (職業) | `Job/` | 17 | `HOF_Model_Data` | 職業定義與屬性係數 |
| Mon (怪物) | `Mon/` | 160+ | `HOF_Model_Char` | 野外怪物與聯盟怪物基底 |
| Skilltree (技能樹) | `Skilltree/` | 100+ | `HOF_Model_Data` | 職業技能學習路徑 |
| Guard (守護) | `Guard/` | 8 | `HOF_Model_Data` | 前衛/後衛防禦策略 |
| Judge (判定) | `Judge/` | 10+ | `HOF_Model_Data` | 行為模式判定條件 |
| Land (地圖) | `Land/` | 20+ | `HOF_Model_Data` | 戰鬥場地與出現條件 |
| Union (聯盟) | `Union/` | 10+ | `HOF_Model_Char` | 聯盟怪物 (基底+怪物合併) |
| Color (顏色) | (根目錄) | 1 (`Color.dat`) | `HOF_Model_Data` | 顏色代號定義 |
| Test (測試) | `test/` (非YAML) | 2+ | - | PHP 測試腳本 |

## 關係圖 (ER Diagram)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        資料類型關係圖                                    │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────┐    ┌──────┐    ┌───────┐    ┌─────────┐    ┌──────────┐      │
│  │ Char │◄───│ Job  │◄───│Skill  │◄───│Skilltree│◄───│  Judge   │      │
│  │(角色)│    │(職業)│    │(技能) │    │(技能樹) │    │(判定)    │      │
│  └──┬───┘    └──┬───┘    └───┬───┘    └─────────┘    └──────────┘      │
│     │           │             │                                          │
│     │           │             │                                          │
│  ┌──▼───┐    ┌──▼───┐    ┌──▼───┐                                      │
│  │ Mon  │◄───│Union │◄───│Guard │                                      │
│  │(怪物)│    │(聯盟)│    │(守護)│                                      │
│  └──┬───┘    └──────┘    └──────┘                                      │
│     │                                                                │
│  ┌──▼───┐    ┌──────┐    ┌───────┐                                      │
│  │ Item │◄───│ Land │◄───│ Color │                                      │
│  │(物品)│    │(地圖)│    │(顏色) │                                      │
│  └──────┘    └──────┘    └───────┘                                      │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

## 詳細關係表

### 1. Char (角色) ←→ Job (職業)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char.job → Job.no | 角色的職業編號對應到職業定義 | `HOF_Class_Char_Type_Char::source()` (Char.php:113) |
| Job.equip → Item.type | 職業的可使用裝備決定角色可裝備的物品類型 | `HOF_Class_Char_Type_Char::setEquip()` (Char.php:371-410) |
| Job.coe → Char 屬性 | 職業的屬性係數影響角色能力值計算 | `HOF_Model_Char::getBaseCharStatus()` (Char.php:72) |
| JobConditions | 職業轉職條件 (job_from/job_to) | `HOF_Model_Data::getJobConditions()` (Data.php:586) |

### 2. Char (角色) ←→ Skill (技能)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char.skill → Skill.no | 角色初始技能列表引用技能編號 | `HOF_Model_Char::getBaseCharStatus()` (Char.php:72) |
| Skill → Skilltree | 技能樹決定角色可學習的技能 | `HOF_Model_Data::getSkillTreeListByJob()` (Data.php:189) |
| Skill → Battle | 戰鬥中使用技能 | `HOF_Class_Battle` + `HOF_Class_Skill_Effect` |

### 3. Char (角色) ←→ Item (物品)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char.equip → Item.no | 角色裝備欄位存放物品編號 | `HOF_Class_Char_Type_Char::setEquip()` (Char.php:340) |
| Char.equip → Item.type | 根據物品類型決定裝備位置 | `HOF_Class_Char_Type_Char::setEquip()` (Char.php:371-410) |
| Item.need → Job.no | 物品製作需求包含職業條件 | `HOF_Helper_Item::parseItemData()` (Item.php:17) |

### 4. Char (角色) ←→ Mon (怪物)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char ←→ Mon | 角色與怪物共用相同基底結構 | `HOF_Model_Char::getBaseMonster()` (Char.php:331) |
| Mon → Char.factory | 怪物透過角色工廠建立實例 | `HOF_Model_Char::newMon()` (Char.php:342) |

### 5. Char (角色) ←→ Guard (守護)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char.behavior.guard → Guard.no | 角色後衛策略引用守護編號 | `HOF_Controller_Char::_judge_position()` (Char.php:315) |
| Guard → Battle | 戰鬥中後衛行為判定 | `HOF_Model_Data::getGuardData()` (Data.php:1128) |

### 6. Char (角色) ←→ Judge (判定) ←→ Skill (技能)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Char.behavior.pattern → Judge.no | 行為模式引用判定編號 | `HOF_Controller_Char::_judge_pattern_input()` (Char.php:394) |
| Judge → Skill.no | 判定結果對應動作 (技能編號) | `HOF_Controller_Char::_main_action_action()` (Char.php:329) |
| Mon.behavior.pattern → Judge | 怪物行為模式同樣使用判定系統 | `HOF_Model_Char::getBaseMonster()` (Char.php:331) |

### 7. Mon (怪物) ←→ Item (物品)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Mon.reward.itemtable → Item.no | 怪物掉落物品表引用物品編號 | `HOF_Class_Battle` (Battle.php:899-901) |
| Mon.reward.moneyhold | 怪物掉落金錢 | 同上 |
| Mon.reward.exphold | 怪物掉落經驗值 | 同上 |

### 8. Mon (怪物) ←→ Land (地圖)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Mon.land → Land.no | 怪物出現於特定地圖 | `HOF_Model_Data::getLandAppear()` (Data.php:368) |
| Land.monster → Mon.no | 地圖定義出現的怪物 | 同上 |

### 9. Mon (怪物) ←→ Union (聯盟)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Union.data.base → Mon.no | 聯盟基底引用怪物編號 | `HOF_Model_Char::getUnionDataBase()` (Char.php:146) |
| Union.data_base.type → Mon | 聯盟怪物合併基底+怪物資料 | `HOF_Model_Char::getUnionDataMon()` (Char.php:169) |
| Union.servant → Mon.no | 聯盟召喚的雜兵 | `HOF_Model_Char::newUnion()` (Char.php:272) |

### 10. Item (物品) ←→ Job (職業)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Item.need → Job.no | 物品製作需求職業等級 (如 `6001: '4'`) | `HOF_Model_Data::getItemData()` (Data.php:678) |
| Job.equip → Item.type | 職業決定可使用物品類型 | `HOF_Class_Char_Type_Char::setEquip()` (Char.php:371) |

### 11. Item (物品) ←→ Land (地圖)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Land.trigger.item → Item.no | 地圖出現需要特定物品 | `HOF_Model_Data::getLandAppear()` (Data.php:368) |

### 12. Skilltree (技能樹) ←→ Job (職業)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Skilltree.check.job → Job.no | 技能樹學習條件依職業過濾 | `HOF_Model_Data::getSkillTreeListByJob()` (Data.php:189) |

### 13. Skill (技能) ←→ Mon (怪物)

| 關係 | 說明 | 原始碼位置 |
|------|------|-----------|
| Mon.behavior.pattern.action → Skill.no | 怪物行為動作引用技能編號 | `HOF_Model_Char::getBaseMonster()` (Char.php:331) |

## 資料流程總覽

### 角色創建流程
```
玩家選擇職業 (Job.no)
    │
    ▼
HOF_Model_Char::newBaseChar(jobNo)
    │  呼叫 getBaseCharStatus($no)
    │  → 載入 Char YAML (char.{jobNo}.yml)
    │  → 移除 name 欄位
    │  → 加入 birth 欄位
    │  → 合併 append 資料
    ▼
HOF_Class_Char::factory(TYPE_CHAR, 'char:'+jobNo, {append})
    │  建立 HOF_Class_Char_Type_Char 實例
    │  → 載入職業 (Job) 資料
    │  → 載入技能樹 (Skilltree) 資料
    ▼
角色初始化完成
    │  → 擁有初始技能 (Skill)
    │  → 擁有初始裝備 (Item)
    │  → 設定後衛策略 (Guard)
    │  → 設定行為模式 (Judge + Skill)
```

### 戰鬥流程
```
進入戰鬥
    │
    ▼
選擇地圖 (Land)
    │  → 檢查 trigger.item (物品條件)
    │  → 檢查 trigger.time (時間條件)
    │  → 確定出現怪物 (Mon)
    ▼
戰鬥開始
    │  → 角色行為模式 (Judge → Skill)
    │  → 怪物行為模式 (Judge → Skill)
    │  → 技能效果計算 (Skill_Effect)
    │  → 物品掉落 (Mon.reward.itemtable → Item)
    ▼
戰鬥結束
    │  → 獲得金錢、經驗值
    │  → 擊敗聯盟怪物 (Union) 可招募
```

### 物品製作流程
```
玩家選擇製作
    │
    ▼
HOF_Model_Data::getItemCreateList()
    │  → 掃描所有物品，篩選有 need 欄位的
    ▼
檢查素材 (Item.need)
    │  → 檢查玩家物品數量
    │  → HOF_Class_Item_Create::HaveNeeds()
    ▼
製作完成
    │  → 隨機附魔 (HOF_Class_Item_Smithy::CreateItem())
    │  → 素材消耗
    ▼
成品加入玩家物品
```

### 聯盟 (Union) 流程
```
聯盟怪物出現
    │
    ▼
HOF_Model_Char::getUnionData($no)
    │  → 載入聯盟基底 (union.{no}.yml)
    │  → 載入對應怪物 (mon.{no}.yml)
    │  → 合併 base + data_ex
    │  → 動態生成 union 檔案
    ▼
HOF_Class_Char::factory(TYPE_UNION + TYPE_MON, $no)
    │  → 建立聯盟角色實例
    ▼
戰鬥中召喚
    │  → 召喚雜兵 (Mon.servant)
    │  → 可強化 (newMonSummon)