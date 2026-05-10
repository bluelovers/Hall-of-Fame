# 裝備管理頁 — Equip

- **網址 (URL):** `http://localhost:8085/char/equip?char={角色ID}`
- **進入路徑:** Char Status → Equip 連結
- **Controller:** `HOF_Controller_Char`

## 功能說明

管理角色的裝備配置。

### 裝備欄位

| 欄位 | 說明 | 備註 |
|:----:|------|------|
| **Main-Hand** | 主手武器 | 影響 ATK 的主要來源 |
| **Off-Hand** | 副手/盾牌 | 兩手武器使用後自動清空且鎖定 |
| **Body/Armor** | 身體防具 | 影響 DEF |
| **Head/Helm** | 頭部防具 | 影響 DEF |
| **Acc/Accessory** | 飾品 | 特殊效果 |
| **Artifact** | 神器（B 欄） | 進階裝備位 |

### 裝備操作

1. 在每個欄位下拉選單中選擇要裝備的物品
2. 點擊「Equip!!」按鈕送出
3. 系統會自動檢查裝備限制（職業、等級、能力值）
4. 卸下的物品回到背包（Inventory）

### 兩手武器規則

| 情況 | 行為 |
|------|------|
| 裝備 TwoHandSword 等兩手武器 | 自動卸除 Off-Hand 物品，Off-Hand 下拉鎖定為「none(off)」 |
| 裝備 Off-Hand 物品 | 若 Main-Hand 為兩手武器，需先更換 Main-Hand |
| 從兩手武器換回單手武器 | Off-Hand 解除鎖定，可重新裝備盾牌 |

### 物品來源判定

裝備下拉選單顯示背包（Inventory）中可用的物品。物品的 `type` 欄位決定可裝備的欄位：

| 物品類型 | 裝備欄位 |
|---------|:--------:|
| 0=Weapon | Main-Hand |
| 1=Shield | Off-Hand |
| 2=Armor | Body |
| 3=Helm | Head |
| 4=Accessory | Acc |
| 5=Artifact | Artifact |

## 注意事項

- 物品一經裝備即綁定該角色（無法直接轉移給其他角色）
- 部分物品有職業限制（如法杖限 Sorcerer/Priest）
- 裝備不消耗任何資源、無任何風險

## 測試日期

2026-05-10
