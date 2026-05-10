# Admin 系統管理介面 — 操作指南

> 記錄日期：2026-05-10
> 測試版本：develop5 分支

---

## 1. 登入資訊

| 項目 | 值 |
|------|-----|
| **網址** | `http://localhost:8085/admin.php` |
| **密碼** | `password`（預設，建議修改） |
| **Session 時效** | 30 分鐘（Cookie `adminPass`） |
| **登出** | 點擊 `logout` 按鈕 |

### 登入頁面

```
http://localhost:8085/admin.php

PASS : [textbox] [submit]
```

---

## 2. 系統設定頁面 (TOP)

**網址：** `http://localhost:8085/admin.php`

顯示遊戲所有基本設定常數的唯讀一覽表。分為以下類別：

### 基本設定

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `TITLE` | 遊戲標題 | `Hall of Rumor 噂のホール` |
| `MAX_TIME` | 最大 Time | `1000 Time` |
| `TIME_GAIN_DAY` | 1 日增加 Time | `6000 Time` |
| `CONTROL_PERIOD` | 自動管理周期 | `43200 s (12 hour)` |
| `RECORD_IP` | 記錄 IP (1=ON) | `1` |
| `SELLING_PRICE` | 賣卻倍率 | `0.2` |
| `EXP_RATE` | 經驗值倍率 | `x1` |
| `MONEY_RATE` | 金錢倍率 | `x1` |

### Auction 拍賣場設定

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `AUCTION_MAX` | 最大出品數 | `100` |
| `AUCTION_TOGGLE` | 拍賣場 ON/OFF (1=ON) | `0`（關閉） |
| `AUCTION_EXHIBIT_TOGGLE` | 出品 ON/OFF (1=ON) | `0`（關閉） |

### 判定系統

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `JUDGE_LIST_AUTO_LOAD` | 條件判定列表自動取得 (1=自動) | `0` |

### Ranking 排名戰設定

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `HOF_Class_Ranking::RANK_TEAM_SET_TIME` | 排名戰隊伍設定周期 | `172800 s (48 hour)` |
| `HOF_Class_Ranking::RANK_BATTLE_NEXT_LOSE` | 敗北後等待時間 | `86400 s (24 hour)` |
| `HOF_Class_Ranking::RANK_BATTLE_NEXT_WIN` | 勝利後等待時間 | `60 s` |

### 戰鬥相關設定

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `NORMAL_BATTLE_TIME` | 普通戰鬥消耗 Time | `100 Time` |
| `MAX_BATTLE_LOG` | 戰鬥日誌保存數（普通） | `100` |
| `MAX_BATTLE_LOG_UNION` | 戰鬥日誌保存數（Union） | `100` |
| `MAX_BATTLE_LOG_RANK` | 戰鬥日誌保存數（Ranking） | `100` |
| `UNION_BATTLE_TIME` | Union 戰消耗 Time | `10 Time` |
| `UNION_BATTLE_NEXT` | Union 戰等待時間 | `1200 s` |

### 其他設定

| 定義 | 說明 | 目前值 |
|------|------|--------|
| `BBS_BOTTOM_TOGGLE` | 下部選單一行掲示板 (1=ON) | `0` |

---

## 3. 使用者管理 (BASE_PATH_USER)

**網址：** `http://localhost:8085/admin.php?menu=user`

### 功能
- 顯示所有使用者列表
- 展開 (+) 可檢視該使用者的資料檔案：
  - `char.{hash}.yml` — 角色資料
  - `data.yml` — 使用者設定資料
  - `item.yml` — 物品資料
  - `uuid.dat` — UUID 識別碼
- **使用者刪除：** 輸入使用者名稱後點擊「削除」按鈕

### 注意
- 刪除操作不可復原
- 使用者被刪除時，其所有角色、物品、資料將一併清除

---

## 4. 資料管理 (FILE_DATA)

**網址：** `http://localhost:8085/admin.php?menu=data`

各項功能以 [+] 按鈕展開：

| 功能 | 說明 | 備註 |
|------|------|------|
| ユーザデータの集計 | 使用者資料統計 | ※1 重量級操作 |
| キャラデータの集計 | 角色資料統計 | ※1 重量級操作 |
| アイテムデータの集計 | 物品資料統計 | ※1 重量級操作 |
| ユーザのIPを表示 | 顯示使用者 IP | ※1 |
| 壊れてる可能性のあるデータを探す | 找出可能損壞的資料 | 微妙 |
| 戦闘ログの管理 | 戰鬥記錄管理 | 含數量輸入框 |
| オークションの管理 | 拍賣場管理 | |
| ランキングの管理 | 排名管理 | |
| 町広場の管理 | 城鎮廣場管理 | |
| ユーザ登録情報の管理 | 使用者註冊資訊管理 | |
| ユーザ名の管理 | 使用者名稱管理 | |
| 更新情報の管理 | 更新資訊管理 | |
| 自動管理のログ | 自動管理日誌 | |

> ※1：資料量越大處理越久，請謹慎使用。

---

## 5. 其他工具 (OTHER)

**網址：** `http://localhost:8085/admin.php?menu=other`

提供以下資料一覽頁面連結：

| 連結 | 說明 | 網址 |
|------|------|------|
| アイテム一覧 | 物品完整列表 | `/trust_path//admin/list_item.php` |
| 装備効果一覧 | 裝備強化效果一覽 | `/trust_path//admin/list_enchant.php` |
| 職業一覧 | 職業完整列表 | `/trust_path//admin/list_job.php` |
| 判定一覧 | AI 判定條件列表 | `/trust_path//admin/list_judge.php` |
| モンスター一覧 | 怪物完整列表 | `/trust_path//admin/list_monster.php` |
| スキル一覧 | 技能完整列表 | `/trust_path//admin/list_skill3.php` |
| パターン設定機 | 戰鬥 AI 模式編輯器 | `/trust_path//admin/set_action2.php` |

---

## 6. 注意事項

1. **管理介面強度有限** — 原始碼備註已警告「このとってつけたような管理機能を過信しないでください」
2. **使用者數為 0 時可能發生錯誤** — 特別是統計相關功能
3. **設定值唯讀** — 管理頁面無法修改設定值，需直接編輯 `config/setting.dist.php`
4. **URL 中的雙斜線** — 管理頁面部分連結含有 `//`，但伺服器路由器已正規化處理
5. **密碼安全性** — 預設密碼 `password` 建議在正式環境修改，於 `hof/admin.php` 第 15 行

---

## 相關檔案

| 檔案 | 說明 |
|------|------|
| `hof/admin.php` | 管理入口，包含密碼與 CLASS_DIR 修正 |
| `hof/trust_path/admin/admin.php` | 管理後台主程式 |
| `hof/trust_path/admin/list_*.php` | 各類資料一覽頁面 |
| `hof/trust_path/admin/set_action2.php` | AI 行動模式編輯器 |
