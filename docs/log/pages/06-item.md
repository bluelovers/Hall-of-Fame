# 物品管理頁 — Item

- **網址 (URL):** `http://localhost:8085/item`
- **Controller:** `HOF_Controller_Item`

## 功能說明

查看和管理隊伍持有的所有物品。支援分類過濾顯示。

### 頁面元素

| 元素 | 說明 |
|------|------|
| **分類下拉選單** | 過濾物品類型：武器 / 防具 / アイテム / その他 / 全部 |
| **物品列表** | 依分類顯示物品名稱、數量、屬性數值 |

### 物品顯示格式

```
分類 物品名稱 (類型) x數量 / 屬性:數值 / h:重量
```

例如：
```
Weapon GreatSword (Sword) x1 / Atk:20 / h:2
```

### 分類說明

| 分類 | 內容 |
|------|------|
| **Weapon** | 所有武器類（Sword, TwoHandSword, Dagger, Wand, Staff, Bow）|
| **Armor** | 所有防具類（Shield, Armor, Cloth, Robe, Book）|
| **Item** | 道具類（LifeRing, ManaRing, Material等）|
| **Other** | 其他類（RenameCard, Map等）|
| **All** | 全部物品 |

### 注意事項

- 物品需透過商店購買取得
- 戰鬥中獲得的物品也會顯示在此
- 分類篩選僅影響顯示，不影響物品持有狀態

## 已知問題

- 無

## 測試日期

2026-05-10
