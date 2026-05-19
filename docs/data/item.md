---
tags:
  - docs/data
  - hof/resource/item
  - hof/game-data
---

# 物品 (Item) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Item/`
- **檔案數**: 181
- **檔案範例**: `item.1000.yml`, `item.1001.yml`, ... `item.9000.yml`
- **排除**: `cache/` 目錄（快取資料，不含原始 YAML）

## 結構定義

```yaml
no: 1000                 # 物品編號
name: ShortSword       # 物品名稱
type: Sword            # 物品類型
buy: '500'             # 購買價格
img: we_sword026       # 圖示代號
atk:                     # 攻擊力 [物理, 魔法]
    - 10
    - 0
handle: '1'            # 使用次數 (0=無限)
need:                  # 製作需求 (素材編號: 數量)
    6001: '4'
base_name: ShortSword # 基礎名稱
type2: WEAPON         # 詳細類型 (WEAPON, ARMOR, MAT, ITEM, OTHER)
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 物品編號 (4位數字，前4位為基底編號) |
| `name` | string | 物品名稱 |
| `type` | string | 物品類型 (Sword, TwoHandSword, Dagger, Spear, Pike, Axe, Hatchet, Wand, Staff, Mace, Bow, CrossBow, Whip, Shield, MainGauche, Book, Armor, Cloth, Robe, Item, Material) |
| `buy` | string | 購買價格 |
| `img` | string | 圖示檔名（不含副檔名） |
| `atk` | array | 攻擊力 [物理攻擊, 魔法攻擊] |
| `def` | array | 防禦力 [物理割合, 物理減免, 魔法割合, 魔法減免] |
| `dh` | boolean | 是否為雙手武器 (Double Hand) |
| `handle` | string | 使用次數 (0=無限) |
| `need` | object | 製作需求 (素材編號: 所需數量) |
| `base_name` | string | 基底名稱（精錬後會加上等級前綴） |
| `type2` | string | 詳細分類 (WEAPON, ARMOR) |
| `sell` | string | 賣出價格（可選，預設為 buy * SELLING_PRICE） |
| `P_MAXHP` ~ `P_LUK` | number | 額外能力值加成 |
| `M_MAXHP` ~ `M_MAXSP` | number | 額外能力值倍率 |

## 物品類型分布

- **武器 (WEAPON)**: 劍 (1000-1099)、雙手劍 (1100-1199)、短劍 (1200-1299)、槍 (1300-1399)、雙手斧 (1400-1499)、斧 (1500-1599)、杖 (1600-1699)、雙手杖 (1700-1799)、鈍器 (1800-1899)、弓 (2000-2099)、石弓 (2100-2199)、鞭 (2200-2299)
- **防具 (ARMOR)**: 盾 (3000-3099)、鎧 (5000-5099)、服 (5100-5199)、衣 (5200-5299)
- **飾品 (ITEM)**: 5500-5599
- **素材 (MATERIAL)**: 6000-6299（石系、木材、皮、骨、牙、羽、寶石、音、繊維、コイン）
- **製作強化**: 7000-7199
- **消耗品**: 7500-7599
- **地圖/鑰匙**: 8000-8099
- **其他**: 9000+

## 原始碼存取路徑

### 核心資料存取層

#### 1. 資料模型層 - `HOF_Model_Data`

- **檔案**: `hof/trust_path/HOF/Model/Data.php`
- **繼承**: `HOF_Class_Data` → `HOF_Class_Data` (基底)

| 函數 | 行號 | 說明 |
|------|------|------|
| `getItemData($no, $source)` | 678 | 取得單筆物品資料，`$source=true` 時跳過後處理 |
| `getItemList()` | 848 | 取得所有物品編號列表 |
| `newItem($no, $check)` | 635 | 建立 Item 物件實例 |
| `getItemCreateList($over)` | 767 | 取得可製作的物品列表 |
| `getItemCreateData($no)` | 820 | 取得單筆製作配方 |
| `getItemCreateMaterialList($key)` | 753 | 取得製作素材列表 |
| `getCanExhibitType()` | 935 | 取得可拍賣物品類型 |
| `getCanRefineType()` | 958 | 取得可精錬物品類型 |
| `getShopList()` | 887 | 取得商店販售列表 |

#### 2. 資料載入層 - `HOF_Class_Data`

- **檔案**: `hof/trust_path/HOF/Class/Data.php`
- **關鍵函數**: `_load($_key, $no)` (第 35-55 行)
- **檔案路徑產生**: `_filename($_key, $no)` (第 23-30 行)
  - 格式: `BASE_TRUST_PATH . '/HOF/Resource/' . ucfirst($_key) . '/' . $_key . '.' . $no . '.yml'`
  - 對應: `hof/trust_path/HOF/Resource/Item/item.{no}.yml`
- **快取機制**: 使用 `$this->data[$_key][$no]` 記憶體快取

#### 3. 物品物件層 - `HOF_Class_Item`

- **檔案**: `hof/trust_path/HOF/Class/Item.php`
- **繼承**: `HOF_Class_Base_ObjectAttr`
- **建構子**: `__construct($no)` (第 75 行)
  - 若 `$no` 為陣列則直接使用，否則呼叫 `HOF_Model_Data::getItemData($no, true)` 取得原始資料
  - 經 `HOF_Helper_Item::parseItemData()` 後處理

### 後處理與業務邏輯層

#### 4. 物品後處理 - `HOF_Helper_Item`

- **檔案**: `hof/trust_path/HOF/Helper/Item.php`
- **核心函數**:
  - `parseItemData($data, $no)` (第 17 行): 解析物品編號，計算精錬值、附魔效果
  - `addEnchantData(&$item, $opt)` (第 83 行): 根據附魔編號增加屬性
  - `ItemSellPrice($item)` (第 11 行): 計算物品售價

#### 5. 物品製作 - `HOF_Class_Item_Create`

- **檔案**: `hof/trust_path/HOF/Class/Item/Create.php`
- **核心函數**:
  - `CanCreate($user)` (第 14 行): 取得可製作物品列表
  - `HaveNeeds($no, $UserItem)` (第 173 行): 檢查玩家是否有足夠素材
  - `ItemAbilityPossibility($type)` (第 206 行): 取得物品可能附魔列表

#### 6. 精錬系統 - `HOF_Class_Item_Smithy`

- **檔案**: `hof/trust_path/HOF/Class/Item/Smithy.php`
- **核心函數**:
  - `SetItem($no)` (第 44 行): 解析物品編號
  - `CreateItem()` (第 67 行): 隨機生成附魔
  - `CanRefine()` (第 126 行): 檢查是否可精錬
  - `ItemRefine()` (第 138 行): 執行精錬
  - `RefineProb($now)` (第 159 行): 精錬成功率計算

### 控制器呼叫位置

#### 7. 商店控制器 - `HOF_Controller_Shop`

- **檔案**: `hof/trust_path/HOF/Controller/Shop.php`
- **觸發時機**: 玩家在商店購買物品時
- **呼叫**: `HOF_Model_Data::newItem($itemNo)` (第 72, 90 行)

#### 8. 角色控制器 - `HOF_Controller_Char`

- **檔案**: `hof/trust_path/HOF/Controller/Char.php`
- **觸發時機**: 裝備/卸下物品、角色資訊顯示
- **呼叫**:
  - `HOF_Model_Data::newItem($no)` (第 531, 590, 709, 943, 1127 行) — 裝備/卸下
  - `HOF_Model_Data::getItemData($item_no)` (第 566, 823 行) — 取得物品資料

#### 9. 精錬控制器 - `HOF_Controller_Smithy`

- **檔案**: `hof/trust_path/HOF/Controller/Smithy.php`
- **觸發時機**: 玩家進行物品精錬/製作
- **呼叫**:
  - `HOF_Model_Data::getItemData(...)` (第 99, 122, 227, 276, 383 行)
  - `HOF_Model_Data::getItemCreateMaterialList(...)` (第 224, 253 行)
  - `HOF_Model_Data::getItemCreateList()` (透過 `getItemCreateMaterialList`)

#### 10. 拍賣控制器 - `HOF_Controller_Auction`

- **檔案**: `hof/trust_path/HOF/Controller/Auction.php`
- **觸發時機**: 拍賣場物品操作
- **呼叫**:
  - `HOF_Model_Data::getItemData($item_no)` (第 294 行)
  - `HOF_Model_Data::newItem($no)` (第 389 行)

#### 11. 戰鬥系統 - `HOF_Class_Battle` / `HOF_Class_Battle_View`

- **檔案**: `hof/trust_path/HOF/Class/Battle.php` (第 900 行)
- **檔案**: `hof/trust_path/HOF/Class/Battle/View.php` (第 277, 304 行)
- **觸發時機**: 戰鬥中物品掉落、顯示
- **呼叫**: `HOF_Model_Data::getItemData($item)`

#### 12. 角色類型 - `HOF_Class_Char_Type_Char`

- **檔案**: `hof/trust_path/HOF/Class/Char/Type/Char.php`
- **觸發時機**: 角色裝備操作、取得武器類型
- **呼叫**:
  - `HOF_Model_Data::newItem($this->equip->{$chk})` (第 425 行)
  - `HOF_Model_Data::getItemData($this->equip->{$place})` (第 561 行)
  - `HOF_Model_Data::newItem($no)` (第 598 行)

#### 13. 物品類型 - `HOF_Class_Item`

- **檔案**: `hof/trust_path/HOF/Class/Item.php`
- **觸發時機**: 物品顯示、價格計算
- **呼叫**: `HOF_Model_Data::getItemData($no, true)` (第 83 行)

#### 14. 拍賣類型 - `HOF_Class_Item_Auction`

- **檔案**: `hof/trust_path/HOF/Class/Item/Auction.php`
- **觸發時機**: 拍賣場物品顯示、出價
- **呼叫**: `HOF_Model_Data::getItemData(...)` (第 394, 469, 720 行)

#### 15. 製作類型 - `HOF_Class_Item_Create` / `HOF_Class_Item_Smithy`

- **檔案**: `hof/trust_path/HOF/Class/Item/Create.php` (第 179, 197 行)
- **檔案**: `hof/trust_path/HOF/Class/Item/Smithy.php` (第 58 行)
- **呼叫**: `HOF_Model_Data::getItemData(...)` / `HOF_Model_Data::getItemCreateData(...)`

#### 16. 管理後台 - `admin/list_item.php`

- **檔案**: `hof/trust_path/admin/list_item.php`
- **觸發時機**: 管理後台物品列表
- **呼叫**: `HOF_Model_Data::getItemData($i)` (第 48, 78 行)

#### 17. 模板檔案

- **檔案**: `hof/trust_path/tpl/layout/item.detail.php` (第 60 行)
- **呼叫**: `HOF_Model_Data::getItemData($M_itemNo)`

### 資料流程

```
YAML 檔案 (item.{no}.yml)
    │
    ▼
HOF_Class_Data::_load('item', $base)
    │  解析編號: base = substr($no, 0, 4)
    │  檔案路徑: Resource/Item/item.{base}.yml
    │  記憶體快取: $this->data['item'][$base]
    ▼
HOF_Model_Data::getItemData($no, $source)
    │  呼叫 _load('item', $base)
    │  設定 $data["id"] = $no
    │  若 $source=false: 經 HOF_Helper_Item::parseItemData() 後處理
    │    - 計算精錬值 (refine)
    │    - 套用附魔效果 (addEnchantData)
    ▼
HOF_Class_Item::__construct($no)
    │  呼叫 getItemData($no, true) 取得原始資料
    │  呼叫 parseItemData() 後處理
    │  繼承 HOF_Class_Base_ObjectAttr
    ▼
各控制器/類型使用
    - Shop: newItem() 購買
    - Char: newItem() 裝備/卸下, getItemData() 顯示
    - Smithy: getItemData() 精錬/製作
    - Auction: getItemData() 拍賣
    - Battle: getItemData() 戰鬥掉落
```

### 與其他資料類型的關聯性

- **職業 (Job)**: 物品 `need` 欄位要求特定職業等級 (如 `6001: '4'` 表示職業6001需等級4)
- **角色 (Char)**: 透過 `equip` 欄位關聯裝備；`setEquip()` 根據 `type` 欄位決定裝備位置
- **技能 (Skill)**: 部分物品效果涉及技能增強
- **怪物 (Mon)**: 怪物掉落物品 (`itemtable` 欄位)，關聯到 Item 編號
- **精錬/製作 (Item/Create, Item/Smithy)**: 物品間的製作關係
- **地圖 (Land)**: 地圖出現條件涉及特定物品 (`trigger.item`)