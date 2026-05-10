# 人材斡旋所 — Recruit

- **網址 (URL):** `http://localhost:8085/recruit`
- **Controller:** `HOF_Controller_Recruit`
- **進入路徑:** Town → 人材斡旋所(Recruit)

## 功能說明

雇用新角色加入隊伍。每個角色有各自的費用與基礎能力。

### 雇用流程

1. 選擇職業（點擊 Radio Button）
2. 輸入角色名稱（1~16 字母，日文全角=2 字）
3. 點擊「Recruit」按鈕
4. 系統扣款 + 建立角色

### 可雇用職業一覽

| 職業 | 性別 | 費用 | STR | INT | DEX | SPD | maxhp | maxsp |
|------|:---:|:----:|:---:|:---:|:---:|:---:|:----:|:----:|
| Warrior | ♂/♀ | $2,000 | **10** | 2 | 4 | 4 | **300** | 50 |
| Sorcerer | ♂/♀ | $2,000 | 2 | **10** | 5 | 3 | 150 | **100** |
| Priest | ♂/♀ | $2,500 | 3 | **8** | 5 | 4 | 200 | 80 |
| Hunter | ♂/♀ | $4,000 | 2 | 2 | **10** | **6** | N/A | N/A |

### 各職業起始技能

| 職業 | 起始技能 | 技能說明 |
|------|---------|---------|
| Warrior | Attack(pow:100), Bash(pow:160, SP8) | 物理單體+蓄力強擊 |
| Sorcerer | Attack, FireBall(pow:100×4, SP20), ManaRecharge(SP回) | 魔法多段 + SP 回復 |
| Priest | Attack, Healing(pow:200, SP5), Blessing(SP回率:3) | 治療 + 全體 SP 回復 |
| Hunter | Shoot(pow:100, DEX, 需弓), DoubleShot(pow:80×2, 需弓) | 遠程物理（DEX依存） |

### 注意事項

- 隊伍上限為 `MAX_CHAR=5` 個角色
- 角色名稱在隊伍內必須唯一
- 雇用後角色 Lv.1 起始，無初始裝備外的物品
- Hunter 缺少 maxhp/maxsp 定義（YAML 不完整）

### 初始裝備

| 職業 | 主手武器 | 防具 |
|------|---------|------|
| Warrior | ID:1000（大劍系） | ID:5000（鎧甲） |
| Sorcerer | ID:1700（杖系） | ID:5200（法袍） |
| Priest | ID:1700（杖系） | ID:5200（法袍） |
| Hunter | ID:2000（弓系） | ID:5100（輕甲） |

## 已知問題

- Hunter YAML 缺少 `maxhp`、`maxsp`、`hp`、`sp` 欄位，雇用後可能導致數值異常
- 更換性別僅改變外觀圖示，不影響數值

## 測試日期

2026-05-10
