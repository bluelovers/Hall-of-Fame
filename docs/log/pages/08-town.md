# 城鎮頁 — Town

- **網址 (URL):** `http://localhost:8085/town`
- **Controller:** `HOF_Controller_Town`

## 功能說明

遊戲的城鎮中樞，提供商店、鍛冶、競技場等功能的入口。

### 頁面連結

| 連結 | URL | 說明 |
|------|-----|------|
| **店(Shop)** | `/shop` | 商店首頁 |
| **買う(Buy)** | `/shop/buy` | 購買物品 |
| **売る(Sell)** | `/shop/sell` | 販賣物品 |
| **アルバイト** | `/shop/work` | 打工賺錢 |
| **人材斡旋所(Recruit)** | `/recruit` | 雇用新角色 |
| **鍛冶屋(Smithy)** | `/smithy` | 物品精錬 |
| **精錬工房(Refine)** | `/smithy/refine` | 精錬 |
| **製作工房(Create)** | `/smithy/create` | 製作物品 |
| **競賣(オークション)** | `/auction` | 拍賣場（需 `AUCTION_TOGGLE=1`） |
| **コロシアム(Colosseum)** | `/rank` | 排名戰 |

### 廣場（掲示板）

- 文字輸入框 + `post` 按鈕
- 可發表訊息（類似留言板功能）

## 注意事項

- 購買物品後記得至 Item 頁面裝備
- 雇用角色需要足夠資金
- 精錬有機率失敗（物品可能損壞）

## 已知問題

- 無

## 測試日期

2026-05-10
