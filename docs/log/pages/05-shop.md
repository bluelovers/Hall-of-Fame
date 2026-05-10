# 商店頁 — Shop

- **網址 (URL):** `http://localhost:8085/shop/buy`（購買）
- **Controller:** `HOF_Controller_Shop`

## 功能說明

遊戲商店系統，提供武器、防具、道具等物品的購買與販賣。

### 頁面功能

| 連結 | URL | 說明 |
|------|-----|------|
| **買う(Buy)** | `/shop/buy` | 購買物品 |
| **売る(Sell)** | `/shop/sell` | 販賣物品 |
| **アルバイト** | `/shop/work` | 打工賺錢 |

### 商品分類

| 分類 | 商品舉例 | 價格範圍 |
|------|----------|---------|
| **Sword** | GreatSword, Rapier, Cutlass | $3,000~$8,000 |
| **TwoHandSword** | Slayer, Claymore | $1,000~$5,000 |
| **Dagger** | Stiletto | $1,000 |
| **Wand** | Rod, ShortWand, WoodenWand, SilverWand | $1,000~$6,000 |
| **Staff** | Staff, LongStaff | $2,000~$5,000 |
| **Bow** | ShortBow, CompositeBow | $1,000~$4,000 |
| **Shield** | WoodShield, Baccrar, IronShield | $1,000~$4,000 |
| **Book** | TextBook, SpellDictionary | $200~$5,000 |
| **Armor** | LeatherArmor, ScaleArmor, RingMail, ChainMail | $1,000~$6,000 |
| **Cloth** | CottonShirt, LeatherJacket, LightJacket, LongCoat | $500~$5,000 |
| **Robe** | CottonRobe, SilverRobe, ElfRobe, FairyRobe | $1,000~$5,000 |
| **Item** | LifeRing(MAXHP+50), ManaRing(MAXSP+20) | $10,000 |
| **Material** | PowerSphere, MagicSphere | $3,000 |
| **Other** | RenameCard | $5,000 |
| **Map** | AncientCave, TekitoMountain | $500~$5,000 |

### 購買方式

1. 勾選要購買的物品（checkbox）
2. 可調整數量（textbox，預設 1）
3. 點擊「Buy」按鈕
4. 系統顯示訂單明細（價格 x 數量 = 小計）與總計
5. 資金自動扣減

## 已知問題

- 無

## 測試日期

2026-05-10
