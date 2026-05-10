# 頁面功能與關係分析報告

> 生成時間：2026-05-10  
> 資料來源：`docs/log/pages/` 目錄下 15 份頁面分析文件  
> 分析範圍：所有遊戲頁面的功能、控制器、資料交互與頁面間關係

---

## 一、頁面總覽與分類

### 1.1 頁面清單（15 頁）

| 編號 | 頁面名稱 | URL 路徑 | Controller | 功能類別 |
|:----:|---------|---------|-----------|---------|
| 00 | 首頁 (Main Page) | `/` | `HOF_Controller_Game` | 導航/入口 |
| 01 | 狩獵 (Hunt) | `/battle/hunt` | `HOF_Controller_Battle` | 戰鬥入口 |
| 02 | 普通怪物列表 (Common Monster List) | `/battle/list_common` | `HOF_Controller_Battle` | 戰鬥/選區 |
| 03 | 戰鬥準備 (Battle Prepare) | `/battle/common?land=xxx` | `HOF_Controller_Battle` | 戰鬥/準備 |
| 04 | 戰鬥執行 (Battle Execution) | `/battle/common?land=xxx` | `HOF_Controller_Battle` | 戰鬥/執行 |
| 05 | 商店 (Shop) | `/shop/buy`, `/shop/sell`, `/shop/work` | `HOF_Controller_Shop` | 經濟/交易 |
| 06 | 物品管理 (Item) | `/item` | `HOF_Controller_Item` | 經濟/物品 |
| 07 | 設定 (Setting) | `/game/setting` | `HOF_Controller_Game` | 系統設定 |
| 08 | 城鎮 (Town) | `/town` | `HOF_Controller_Town` | 中心樞紐 |
| 09 | 招募 (Recruit) | `/recruit` | `HOF_Controller_Recruit` | 社交/組隊 |
| 10 | 角色狀態 (Char Status) | `/char/char?char={hash}` | `HOF_Controller_Char` | 角色管理 |
| 11 | 裝備管理 (Equip) | `/char/equip?char={id}` | `HOF_Controller_Char` | 角色管理 |
| 12 | 技能學習 (Skill Learn) | `/char/skill_learn?char={id}` | `HOF_Controller_Char` | 角色成長 |
| 13 | 轉職 (Job Change) | `/char/job_change?char={id}` | `HOF_Controller_Char` | 角色成長 |
| 14 | 配點 (Stup) | `/char/stup?char={id}` | `HOF_Controller_Char` | 角色成長 |

### 1.2 功能模組分類

```
┌─────────────────────────────────────────────────────────┐
│                    遊戲頁面功能模組                        │
├─────────────┬─────────────┬─────────────┬───────────────┤
│   導航入口   │   戰鬥系統   │   經濟系統   │   角色管理    │
│             │             │             │               │
│  00 首頁    │  01 狩獵    │  05 商店    │  10 角色狀態  │
│             │  02 怪物列表 │  06 物品管理 │  11 裝備管理  │
│             │  03 戰鬥準備 │             │  12 技能學習  │
│             │  04 戰鬥執行 │             │  13 轉職      │
│             │             │             │  14 配點      │
├─────────────┼─────────────┼─────────────┼───────────────┤
│   中心樞紐   │   系統設定   │   招募系統   │               │
│             │             │             │               │
│  08 城鎮    │  07 設定    │  09 招募    │               │
└─────────────┴─────────────┴─────────────┴───────────────┘
```

---

## 二、頁面間導航關係圖

```
                    ┌──────────────────────────────────────────┐
                    │              00 首頁 (/)                   │
                    │    導航中心 / 角色顯示 / 資訊面板           │
                    └──────┬───────┬───────┬───────┬───────────┘
                           │       │       │       │
                    ┌──────▼──┐ ┌──▼──┐ ┌─▼────┐ ┌▼────────┐
                    │ 01 狩獵 │ │ 05 商店│ │ 08 城鎮│ │ 07 設定 │
                    │ /battle/│ │ /shop │ │ /town │ │/game/   │
                    │ hunt    │ │       │ │       │ │ setting │
                    └────┬────┘ └──┬───┘ └──┬────┘ └─────────┘
                         │        │        │
              ┌──────────▼──┐  ┌──▼────┐  ┌▼──────────────┐
              │ 02 怪物列表 │  │ 06 物品│  │ 09 招募       │
              │/battle/list │  │ /item │  │ /recruit      │
              │_common      │  │       │  │               │
              └──────┬──────┘  └───────┘  └───────────────┘
                     │
              ┌──────▼──────┐
              │ 03 戰鬥準備 │
              │/battle/common│
              └──────┬──────┘
                     │ (選擇角色 + 確認)
              ┌──────▼──────┐
              │ 04 戰鬥執行 │
              │ (自動戰鬥)   │
              └─────────────┘

─── 角色管理子系統 (從首頁角色圖示或城鎮進入) ───

                    ┌──────────────────────────────────────────┐
                    │          10 角色狀態 (/char/char)          │
                    │    顯示完整能力值 / 子頁面切換              │
                    └──┬───────┬───────┬───────┬───────┬───────┘
                       │       │       │       │       │
                ┌──────▼──┐┌───▼──┐┌──▼────┐┌─▼──────┐┌▼───────┐
                │ 11 裝備││ 12 技能││ 13 轉職││ 14 配點││ (返回) │
                │/char/  ││/char/ ││/char/  ││/char/  ││        │
                │equip   ││skill_ ││job_    ││stup    ││        │
                │        ││learn  ││change  ││        ││        │
                └─────────┘└───────┘└────────┘└────────┘└────────┘
```

---

## 三、各頁面詳細功能分析

### 3.1 導航/入口類

#### 00 首頁 (Main Page)

| 項目 | 內容 |
|------|------|
| **URL** | `/` |
| **Controller** | `HOF_Controller_Game` |
| **功能** | 遊戲主入口，顯示隊伍資訊（團隊名稱、資金、時間）、角色列表、導航列 |
| **導航連結** | Hunt → `/battle/hunt`、Item → `/item`、Town → `/town`、Setting → `/game/setting`、Log → `/log`、Manual → `/manual`、GameData → `/gamedata` |
| **角色互動** | 點擊角色頭像 → `/char/char?char={hash}` |
| **資料顯示** | 團隊名稱、資金（Funds）、時間（當前/最大時間 MAX_TIME=1000） |

#### 08 城鎮 (Town)

| 項目 | 內容 |
|------|------|
| **URL** | `/town` |
| **Controller** | `HOF_Controller_Town` |
| **功能** | 城中樞，匯聚商店、鍛冶、競技場、招募等功能入口 |
| **子功能連結** | 商店 `/shop`、買賣 `/shop/buy`、`/shop/sell`、打工 `/shop/work`、招募 `/recruit`、鍛冶屋 `/smithy`、精錬 `/smithy/refine`、製作 `/smithy/create`、競賣 `/auction`（需 `AUCTION_TOGGLE=1`）、競技場 `/rank` |
| **附加功能** | 廣場留言板（文字輸入 + post 按鈕） |

---

### 3.2 戰鬥系統

#### 01 狩獵 (Hunt Page)

| 項目 | 內容 |
|------|------|
| **URL** | `/battle/hunt` |
| **Controller** | `HOF_Controller_Battle` |
| **功能** | 戰鬥入口，選擇對戰類型 |
| **選項** | CommonMonster（普通怪物）→ `/battle/list_common`、UnionMonster（工會怪物）→ `/battle/list_union` |

#### 02 普通怪物列表 (Common Monster List)

| 項目 | 內容 |
|------|------|
| **URL** | `/battle/list_common` |
| **Controller** | `HOF_Controller_Battle` / Action: `list_common` |
| **功能** | 列出所有可挑戰區域，顯示建議等級範圍 |
| **區域數量** | 15+ 個地圖區域 |
| **地圖區域** | gb0（哥布林，最弱 Lv1）、gb1（Lv1-5）、gb2（Lv3-8）、blow（Lv20-30）、des0（Lv5-10）、mt0、ocean0、ocean1、plund01（Lv10-15）、sand0、sea0、sea1、swamp0、swamp1、volc0、volc1 等 |
| **選擇方式** | 點擊區域名稱或 URL 參數 `?land={地圖代號}` |
| **下一頁** | 選擇後進入戰鬥準備頁 `/battle/common?land=xxx` |

#### 03 戰鬥準備 (Battle Prepare)

| 項目 | 內容 |
|------|------|
| **URL** | `/battle/common?land={地圖代號}` |
| **Controller** | `HOF_Controller_Battle` / Action: `common` |
| **功能** | 戰鬥前配置：顯示我方隊伍與敵方怪物資訊 |
| **我方顯示** | 角色頭像、名稱、等級、職業（checkbox 勾選出戰角色） |
| **敵方顯示** | 怪物頭像、名稱、等級 |
| **操作按鈕** | Battle!（開始戰鬥）、Reset（重置選擇）、Save this party（儲存隊伍配置） |
| **限制** | 至少選擇 1 名角色，否則顯示錯誤訊息 |

#### 04 戰鬥執行 (Battle Execution)

| 項目 | 內容 |
|------|------|
| **URL** | `/battle/common?land={地圖代號}`（與準備頁共用 URL） |
| **Controller** | `HOF_Controller_Battle` |
| **功能** | 自動回合制戰鬥 |
| **戰鬥流程** | 1. 入場 → 2. 回合制自動行動（普攻/技能/延遲系統）→ 3. 勝負判定 |
| **顯示內容** | 雙方 HP 變化、行動延遲值（Delayed）、傷害數值、戰鬥日誌 |
| **操作方式** | 全自動，玩家僅可透過 `>>` 跳到日誌底部 |
| **結果** | 勝利/失敗後顯結果，HP 歸零方失敗 |
| **注意事項** | 戰鬥不可手動控制、延遲值影響行動順序 |

---

### 3.3 經濟系統

#### 05 商店 (Shop)

| 項目 | 內容 |
|------|------|
| **URL** | `/shop/buy`、`/shop/sell`、`/shop/work` |
| **Controller** | `HOF_Controller_Shop` |
| **功能** | 物品買賣與打工賺錢 |
| **子功能** | Buy（購買）、Sell（販賣）、アルバイト（打工） |
| **商品分類** | Sword、TwoHandSword、Dagger、Wand、Staff、Bow、Shield、Book、Armor、Cloth、Robe、Item（飾品）、Material、Other、Map |
| **購買流程** | 勾選物品 → 設定數量 → 點擊 Buy → 顯示訂單明細 → 自動扣款 |
| **價格範圍** | $500~$10,000 不等 |

#### 06 物品管理 (Item Management)

| 項目 | 內容 |
|------|------|
| **URL** | `/item` |
| **Controller** | `HOF_Controller_Item` |
| **功能** | 查看和管理隊伍持有的所有物品 |
| **過濾功能** | 下拉選單過濾：Weapon、Armor、Item（道具）、Other、All |
| **顯示格式** | `分類 物品名稱 (類型) x數量 / 屬性:數值 / h:重量` |
| **注意事項** | 物品需透過商店購買或戰鬥掉落取得 |

---

### 3.4 角色管理

#### 10 角色狀態 (Character Status)

| 項目 | 內容 |
|------|------|
| **URL** | `/char/char?char={角色ID}` |
| **進入方式** | 首頁點擊角色頭像 / 導航列 Char 連結 |
| **Controller** | `HOF_Controller_Char` |
| **功能** | 顯示單一角色的完整狀態數值 |
| **顯示欄位** | 名稱、Lv（Exp 顯示如 20/30）、職業、HP/SP、STR、INT、DEX、SPD、LUK |
| **子頁面** | Stup（配點）→ `/char/stup`、Action（行動設定）→ `/char/action`、Equip（裝備）→ `/char/equip`、SkillLearn（技能學習）→ `/char/skill_learn`、JobChange（轉職）→ `/char/job_change` |
| **戰鬥素質計算** | 戰鬥 STR = 基礎 + 裝備 P_STR；MAXHP = round(基礎 × (1 + M_MAXHP/100) + P_MAXHP) |

#### 11 裝備管理 (Equip)

| 項目 | 內容 |
|------|------|
| **URL** | `/char/equip?char={角色ID}` |
| **進入方式** | Char Status → Equip 連結 |
| **Controller** | `HOF_Controller_Char` |
| **功能** | 管理角色裝備配置 |
| **裝備欄位** | Main-Hand（主手武器）、Off-Hand（副手/盾牌）、Body/Armor（身體防具）、Head/Helm（頭部防具）、Acc/Accessory（飾品）、Artifact（神器 B 欄） |
| **操作方式** | 下拉選單選擇物品 → 點擊 Equip!! 按鈕 |
| **兩手武器規則** | 裝備 TwoHandSword 自動卸除 Off-Hand；裝備 Off-Hand 需先卸除 TwoHandSword |
| **物品類型對應** | 0=Weapon→Main-Hand、1=Shield→Off-Hand、2=Armor→Body、3=Helm→Head、4=Accessory→Acc、5=Artifact→Artifact |
| **注意事項** | 裝備綁定角色、部分物品有職業限制、裝備不消耗資源 |

#### 12 技能學習 (Skill Learn)

| 項目 | 內容 |
|------|------|
| **URL** | `/char/skill_learn?char={角色ID}` |
| **進入方式** | Char Status → SkillLearn 連結 |
| **Controller** | `HOF_Controller_Char` |
| **功能** | 消耗 Skill Point 學習新技能 |
| **Skill Point** | 升級獲得 1 點、學習技能消耗 SP、無法重置 |
| **技能列表** | 所有職業通用技能 + 無職業限制技能 |
| **技能限制** | 職業限制、等級限制、前置技能（技能樹機制） |
| **操作方式** | Radio Button 選擇 → Learn 按鈕學習 |

#### 13 轉職 (Job Change)

| 項目 | 內容 |
|------|------|
| **URL** | `/char/job_change?char={角色ID}` |
| **進入方式** | Char Status → JobChange 連結 |
| **Controller** | `HOF_Controller_Char` |
| **功能** | 轉換職業以獲得不同技能與能力成長 |
| **轉職條件** | 已擁有初始職業、達到等級要求（通常 Lv.5）、持有足夠資金 |
| **職業樹** | Warrior(100) → Knight(110)/Berserker(120)、Sorcerer(200) → Wizard(210)、Priest(300) → Sage(310)、Hunter(400) |
| **轉職影響** | 等級/Exp/素質/已學技能/裝備保留、可學技能變更、裝備限制可能變化 |
| **轉職費用** | $2,000 |

#### 14 配點 (Stup / Status Up)

| 項目 | 內容 |
|------|------|
| **URL** | `/char/stup?char={角色ID}` |
| **進入方式** | Char Status → Stup 連結 |
| **Controller** | `HOF_Controller_Char` |
| **功能** | 分配升級獲得的潛在能力點數 |
| **分配方式** | 每個素質（STR/INT/DEX/SPD/LUK）旁輸入框輸入點數（預設 1） |
| **總上限** | `MAX_STATUS=250`（單一素質上限亦為 250） |
| **邊際效益** | 使用 `sqrt(Stat)` 公式，大量投資單一素質效益遞減 |
| **建議策略** | Warrior: STR→DEX、Sorcerer: INT→SPD、Priest: INT 均分、Hunter: DEX 為主 |
| **注意事項** | 無法重置、與武器需求 STR/INT 相關 |

---

### 3.5 系統設定

#### 07 設定 (Setting)

| 項目 | 內容 |
|------|------|
| **URL** | `/game/setting` |
| **Controller** | `HOF_Controller_Game` |
| **功能** | 個人化設定、登出、帳號管理 |
| **設定項目** | 戰鬥日誌記錄（checkbox）、停用物品列表 JS（checkbox）、顏色設定（下拉選單） |
| **操作按鈕** | modify（儲存設定）、logout（登出）、change（變更團隊名稱，費用 $300,000）、delete（刪除帳號，需密碼） |
| **已知問題** | 顏色下拉選單所有選項顯示為 SampleColor |
| **注意事項** | 刪除帳號為不可逆操作、變更名稱限制 16 字元 |

---

### 3.6 招募系統

#### 09 招募 (Recruit)

| 項目 | 內容 |
|------|------|
| **URL** | `/recruit` |
| **Controller** | `HOF_Controller_Recruit` |
| **功能** | 雇用新角色加入隊伍 |
| **流程** | 選擇職業 → 輸入名稱（1~16 字母）→ 點擊 Recruit → 扣款 + 建立角色 |
| **隊伍上限** | `MAX_CHAR=5` |
| **可雇職業** | Warrior($2,000)、Sorcerer($2,000)、Priest($2,500)、Hunter($4,000) |
| **初始裝備** | Warrior: 大劍+鎧甲、Sorcerer: 法杖+法袍、Priest: 法杖+法袍、Hunter: 弓+輕甲 |
| **已知問題** | Hunter YAML 缺少 maxhp/maxsp 欄位 |

---

## 四、頁面與後端系統的關係

### 4.1 Controller 對應關係

| Controller | 負責頁面 | 檔案路徑 |
|-----------|---------|---------|
| `HOF_Controller_Game` | 首頁 (00)、設定 (07) | `hof/trust_path/HOF/Controller/Game.php` |
| `HOF_Controller_Battle` | 狩獵 (01)、怪物列表 (02)、戰鬥準備 (03)、戰鬥執行 (04) | `hof/trust_path/HOF/Controller/Battle.php` |
| `HOF_Controller_Shop` | 商店 (05) | `hof/trust_path/HOF/Controller/Shop.php` |
| `HOF_Controller_Item` | 物品管理 (06) | `hof/trust_path/HOF/Controller/Item.php` |
| `HOF_Controller_Town` | 城鎮 (08) | `hof/trust_path/HOF/Controller/Town.php` |
| `HOF_Controller_Recruit` | 招募 (09) | `hof/trust_path/HOF/Controller/Recruit.php` |
| `HOF_Controller_Char` | 角色狀態 (10)、裝備 (11)、技能學習 (12)、轉職 (13)、配點 (14) | `hof/trust_path/HOF/Controller/Char.php` |

### 4.2 頁面與 YAML 資料的關係

```
頁面                    YAML 資料類型
──────────────────────────────────────────────────────
00 首頁                 Char (角色顯示)
01 狩獵                 Mon (怪物列表)
02 怪物列表              Land (地圖區域)、Mon (怪物)
03 戰鬥準備              Char (角色資料)、Mon (怪物)
04 戰鬥執行              Char、Mon、Skill (技能)、Judge (判定)、Guard (守護)
05 商店                  Item (物品列表)
06 物品管理              Item (所有物品)
07 設定                  Color.dat (顏色)
08 城鎮                  (靜態頁面，入口匯聚)
09 招募                  Char (職業定義)、Job (職業資料)
10 角色狀態              Char (角色資料)、Job (職業)
11 裝備管理              Item (物品)、Char (角色)
12 技能學習              Skill、Skilltree (技能樹)、Job (職業)
13 轉職                  Job (職業定義)
14 配點                  (角色屬性計算，無直接 YAML)
```

### 4.3 頁面間的資料流

```
首頁 (00)
  ├──→ 狩獵 (01) → 怪物列表 (02) → 戰鬥準備 (03) → 戰鬥執行 (04)
  │                                              ↑
  │                                    (戰鬥結果 → 獲得物品/金錢/經驗)
  ├──→ 商店 (05) → 物品管理 (06)
  ├──→ 城鎮 (08) → 商店/鍛冶/招募/競技場
  ├──→ 招募 (09) → (新角色加入) → 角色狀態 (10)
  ├──→ 角色狀態 (10) → 裝備 (11) / 技能學習 (12) / 轉職 (13) / 配點 (14)
  └──→ 設定 (07) → (系統設定變更)
```

---

## 五、核心資料流圖

### 5.1 角色創建到戰鬥的完整流程

```
招募 (09) → 建立角色 (Char YAML + Job YAML)
    │
    ▼
角色狀態 (10) → 配點 (14) → 屬性計算
    │
    ├──→ 裝備 (11) → 物品 (Item YAML) 裝備效果疊加
    ├──→ 技能學習 (12) → 技能樹 (Skilltree YAML) → 技能 (Skill YAML)
    └──→ 轉職 (13) → 職業變更 (Job YAML)
    │
    ▼
城鎮 (08) → 商店 (05) → 購買物品 (Item YAML)
    │
    ▼
狩獵 (01) → 怪物列表 (02) → 選擇地圖 (Land YAML)
    │
    ▼
戰鬥準備 (03) → 選擇隊伍 → 載入怪物 (Mon YAML)
    │
    ▼
戰鬥執行 (04)
    ├── 角色行為: Judge (判定) → Skill (技能)
    ├── 怪物行為: Judge (判定) → Skill (技能)
    ├── 守護: Guard (守護)
    └── 結果: 掉落物品 (Item)、金錢、經驗
```

### 5.2 物品系統流程

```
YAML 定義 (Item/{no}.yml)
    │
    ▼
商店購買 (05) → HOF_Model_Data::newItem()
    │
    ├──→ 物品管理 (06) → 顯示/過濾
    ├──→ 裝備 (11) → 角色能力加成
    ├──→ 精錬 (Smithy) → 屬性提升
    ├──→ 製作 (Create) → 素材合成
    └──→ 拍賣 (Auction) → 玩家交易
```

### 5.3 技能系統流程

```
YAML 定義 (Skill/{no}.yml + Skilltree/{no}.yml)
    │
    ▼
技能學習 (12) → 技能樹條件檢查 (Skilltree YAML check)
    │
    ▼
轉職 (13) → 可學技能變更 (Job → Skilltree 對應)
    │
    ▼
行動設定 (Action) → 選擇技能對應判定 (Judge)
    │
    ▼
戰鬥執行 (04) → 判定通過 → 技能效果 (Skill_Effect)
```

---

## 六、頁面功能矩陣

| 功能 | 00 | 01 | 02 | 03 | 04 | 05 | 06 | 07 | 08 | 09 | 10 | 11 | 12 | 13 | 14 |
|------|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|
| 導航 | ● | | | | | | | | | | | | | | |
| 戰鬥 | | ● | ● | ● | ● | | | | | | | | | | |
| 商店 | | | | | | ● | ● | | ● | | | | | | |
| 角色管理 | | | | | | | | | | | ● | ● | ● | ● | ● |
| 設定 | | | | | | | | ● | | | | | | | |
| 招募 | | | | | | | | | ● | ● | | | | | |
| 資料顯示 | ● | ● | ● | ● | ● | ● | ● | | | ● | ● | | | | |
| 資料變更 | | | | | | ● | | ● | | ● | | ● | ● | ● | ● |
| 導航入口 | ● | | | | | | | | ● | | | | | | |

---

## 七、頁面間的資料傳遞

### 7.1 URL 參數

| 頁面 | 參數 | 說明 |
|------|------|------|
| 02 | `?land={地圖代號}` | 選擇戰鬥地圖 |
| 03 | `?land={地圖代號}` | 傳遞地圖到戰鬥準備 |
| 04 | `?land={地圖代號}` | 傳遞地圖到戰鬥執行 |
| 10 | `?char={角色ID}` | 指定查看的角色 |
| 11 | `?char={角色ID}` | 指定裝備的角色 |
| 12 | `?char={角色ID}` | 指定學習技能的角色 |
| 13 | `?char={角色ID}` | 指定轉職的角色 |
| 14 | `?char={角色ID}` | 指定配點的角色 |

### 7.2 Session/Server 資料

| 資料類型 | 作用域 | 說明 |
|---------|--------|------|
| `$_SESSION['team']` | 全會話 | 隊伍角色列表 |
| `$_SESSION['party']` | 全會話 | 當前出戰隊伍 |
| `$_SESSION['money']` | 全會話 | 玩家資金 |
| `$_SESSION['time']` | 全會話 | 剩餘遊戲時間 |
| Server 端快取 | 全局 | YAML 資料記憶體快取 |

---

## 八、與 YAML 資料類型的完整對應

### 8.1 Char (角色)

| 頁面 | 使用方式 |
|------|---------|
| 00 首頁 | 顯示角色頭像、名稱、等級 |
| 03 戰鬥準備 | 顯示可出戰角色 |
| 04 戰鬥執行 | 角色戰鬥行為 |
| 10 角色狀態 | 顯示完整能力值 |
| 11 裝備管理 | 角色裝備操作 |
| 12 技能學習 | 角色可學技能 |
| 13 轉職 | 角色職業變更 |
| 14 配點 | 角色屬性分配 |

### 8.2 Item (物品)

| 頁面 | 使用方式 |
|------|---------|
| 05 商店 | 物品購買/販賣 |
| 06 物品管理 | 物品顯示/過濾 |
| 11 裝備管理 | 物品裝備/卸下 |
| 04 戰鬥執行 | 戰鬥掉落 |

### 8.3 Skill (技能)

| 頁面 | 使用方式 |
|------|---------|
| 12 技能學習 | 可學技能列表 |
| 04 戰鬥執行 | 技能效果發動 |
| 10 角色狀態 | 技能顯示 |

### 8.4 Job (職業)

| 頁面 | 使用方式 |
|------|---------|
| 09 招募 | 職業選擇 |
| 13 轉職 | 職業轉換 |
| 10 角色狀態 | 職業顯示 |
| 12 技能學習 | 職業技能過濾 |

### 8.5 Mon (怪物)

| 頁面 | 使用方式 |
|------|---------|
| 02 怪物列表 | 區域怪物顯示 |
| 03 戰鬥準備 | 敵方怪物資訊 |
| 04 戰鬥執行 | 怪物行為 AI |

### 8.6 Skilltree (技能樹)

| 頁面 | 使用方式 |
|------|---------|
| 12 技能學習 | 技能學習條件判定 |
| 13 轉職 | 新職業技能樹載入 |

### 8.7 Guard (守護)

| 頁面 | 使用方式 |
|------|---------|
| 04 戰鬥執行 | 後衛行為判定 |

### 8.8 Judge (判定)

| 頁面 | 使用方式 |
|------|---------|
| 04 戰鬥執行 | 行為模式判定 |

### 8.9 Land (地圖)

| 頁面 | 使用方式 |
|------|---------|
| 02 怪物列表 | 地圖區域顯示 |
| 03 戰鬥準備 | 地圖怪物出現條件 |

### 8.10 Union (聯盟)

| 頁面 | 使用方式 |
|------|---------|
| 01 狩獵 | 工會怪物入口 |
| 04 戰鬥執行 | 聯盟戰 |

### 8.11 Color.dat (顏色)

| 頁面 | 使用方式 |
|------|---------|
| 07 設定 | 顏色主題選擇 |

---

## 九、特殊注意事項

### 9.1 已知問題匯總

| 頁面 | 問題描述 |
|------|---------|
| 07 設定 | 顏色下拉選單所有選項顯示為 SampleColor |
| 09 招募 | Hunter YAML 缺少 maxhp/maxsp 欄位 |
| 09 招募 | 更換性別僅改變外觀圖示，不影響數值 |

### 9.2 操作限制

| 項目 | 限制 |
|------|------|
| 配點 (14) | 無法重置、總上限 MAX_STATUS=250 |
| 技能學習 (12) | 無法重置、升級獲得 SP |
| 轉職 (13) | 需等級 Lv.5、費用 $2,000 |
| 改名 (07) | 費用 $300,000、限制 16 字元 |
| 刪除帳號 (07) | 不可逆操作 |
| 裝備 (11) | 裝備綁定角色、不可轉移 |
| 兩手武器 (11) | 裝備 TwoHandSword 時 Off-Hand 鎖定 |

---

## 十、建議與延伸

### 10.1 功能完善建議

1. **顏色設定修復**：07 設定頁的顏色下拉選單顯示問題需修正
2. **Hunter 資料補全**：Hunter YAML 缺少 maxhp/maxsp 欄位
3. **技能重置功能**：目前技能學習無法重置，建議增加洗技能功能
4. **配點重置功能**：目前配點無法重置，建議增加洗點功能

### 10.2 頁面間的缺失連結

1. **首頁缺少直接到招募的連結**：需先至城鎮再招募
2. **戰鬥結果頁面**：缺少專屬的戰鬥結果展示頁
3. **排名頁面**：雖有 `/rank` 路徑但文件未分析

---

> **本報告基於 `docs/log/pages/` 目錄下 15 份頁面分析文件整理而成**  
> **相關 YAML 資料結構分析請參閱 `docs/data/` 目錄**  
> **資料類型關係請參閱 `docs/data/relationships.md`**