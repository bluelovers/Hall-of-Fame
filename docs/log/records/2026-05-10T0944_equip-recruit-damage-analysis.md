# 裝備測試 + 招募角色 + 傷害公式分析記錄

- **日期:** 2026-05-10
- **目的:** 裝備 Claymore 後實測戰鬥、分析完整傷害公式、招募新隊員組隊

---

## 操作 1：購買 Claymore

| 項目 | 內容 |
|------|------|
| **步驟** | Town → Shop/Buy → 購買 Claymore（Atk:45, TwoHandSword, h:3） |
| **花費** | $5,000（Funds: $47,680 → $42,680） |
| **結果** | ✅ 成功購入 |

## 操作 2：裝備 Claymore

| 項目 | 內容 |
|------|------|
| **步驟** | Item → Equip → 在 Main-Hand 選擇 Claymore |
| **結果** | ✅ Hero1 裝備 Claymore（Atk:45），自動卸除 GreatSword（Atk:20）+ WoodShield |
| **說明** | TwoHandSword 類型佔用雙手，Off-Hand 自動清空 |
| **裝備後 ATK** | 20 → **45**（提升 2.25 倍） |

## 操作 3：實戰 — ゴブリンの戦士達 (Lv3-8)

| 項目 | 內容 |
|------|------|
| **隊伍** | Hero1 Lv.2 Warrior solo（Claymore 裝備） |
| **敵方** | GoblinMage Lv.6（經等級調整） |
| **Hero1 傷害** | Attack 每擊 **66**（vs 舊大劍約 33） |
| **GoblinMage** | FireStorm 87×6 hits = 522 total damage |
| **結果** | ❌ 敗北 — 多段魔法太強 |
| **EXP** | 獲得 1 |

## 操作 4：實戰 — ゴブリンと遊ぶ(最弱) (Lv1)

| 項目 | 內容 |
|------|------|
| **隊伍** | Hero1 solo（Claymore） |
| **敵方** | GoblinAxe Lv.4（經等級調整，基礎 Lv.1 + 微調 +3） |
| **Hero1 傷害** | Attack 每擊 **73**（共 219 total） |
| **GoblinAxe** | RagingBlow 48×5 = 240 + 普攻 48×3 |
| **結果** | ❌ 敗北（GoblinAxe 殘 HP 44/263） |
| **EXP** | 獲得 1 |
| **結論** | Lv.2 solo 即使裝備 Claymore 仍不足，需要招募隊員 |

## 操作 5：招募 Healer1 (Priestess)

| 項目 | 內容 |
|------|------|
| **步驟** | Town → 人材斡旋所(Recruit) → 選擇 Priestess → 輸入名稱 |
| **費用** | $2,500 |
| **結果** | ✅ Healer1(Priestess) が仲間になった！ |
| **初始數據** | INT 8, maxhp 200, maxsp 80, Job 300 |
| **初始技能** | Attack, Healing(pow:200, charge:30), Blessing(SP regen) |

## 操作 6：招募 Mage1 (Sorceress)

| 項目 | 內容 |
|------|------|
| **步驟** | Town → Recruit → 選擇 Sorceress → 輸入名稱 |
| **費用** | $2,000 |
| **結果** | ✅ Mage1(Sorceress) が仲間になった！ |
| **初始數據** | INT 10, maxhp 150, maxsp 100, Job 200 |
| **初始技能** | Attack, FireBall(magical×4, pow:100), ManaRecharge(SP rec) |

---

## 分析：完整傷害公式

### 原始碼位置

- 核心傷害計算：`HOF/Class/Skill/Effect.php:670-748` — `CalcBasicDamage()`
- 治癒計算：`HOF/Class/Skill/Effect.php:827-842` — `CalcRecoveryValue()`
- 戰鬥 ATK/DEF 初始化：`HOF/Class/Char/Abstract.php:434-453` — `setBattleVariable()`
- 裝備 ATK/DEF 累加：`HOF/Class/Char/Type/Char.php:542-583` — `CalcEquips()`
- Buff/Debuff 公式：`HOF/Class/Char/Battle/Effect.php:327-403`

### 物理傷害公式

```
Base = sqrt(STR) × 10 + WeaponATK[0]
       (若技能有 inf:dex，改用 sqrt(DEX) × 10)
Raw  = Base × (SkillPow / 100)
減傷 = Raw × (1 - TargetDEF[0]/100) - TargetDEF[1]
最小 = Raw × 10%
最終 = ceil( max(減傷 + Pierce, 最小) )
```

### 魔法傷害公式

```
Base = sqrt(INT) × 10 + WeaponATK[1]
Raw  = Base × (SkillPow / 100)
減傷 = Raw × (1 - TargetDEF[2]/100) - TargetDEF[3]
最終 = ceil( max(減傷 + Pierce, 最小) )
```

### 治療公式

```
Heal = ceil( (sqrt(INT) × 10 + WeaponATK[1]) × (SkillPow / 100) )
```

### 關鍵發現

| 發現 | 說明 |
|------|------|
| **STR/INT 邊際遞減** | 貢獻為 sqrt(Stat)×10，高數值時效益銳減 |
| **武器 ATK 直接加總** | 無衰減，比 STR 更重要 |
| **無隨機變異** | `mt_rand(90,110)/100` 已被註解，傷害固定 |
| **DEF 雙層減傷** | 百分比 def[0]/def[2] + 固定減傷 def[1]/def[3] |
| **玩家保護** | HP>10 且傷害≥HP 時留 1 HP（但多段技能後續段仍擊殺） |

### 實戰驗證

**Hero1 Lv.2 (STR=15, Claymore ATK=45, Attack pow=100):**

```
理論 Base = sqrt(15) × 10 + 45 = 38.73 + 45 = 83.73
Raw = 83.73 × (100/100) = 83.73
```

| 敵人 | 實測傷害 | 反推敵方 DEF |
|------|---------|-------------|
| GoblinMage Lv.6 | 66 | 約 def[0]=15% + def[1]=5 |
| GoblinAxe Lv.4 | 73 | 約 def[0]=10% + def[1]=2~3 |

---

## 各職業基礎數據對比

| 職業 | STR | INT | DEX | SPD | maxhp | maxsp | 費用 | 起始技能 |
|------|:---:|:---:|:---:|:---:|:----:|:----:|:----:|---------|
| Warrior | 10 | 2 | 4 | 4 | 300 | 50 | $2,000 | Attack(100), Bash(160) |
| Sorcerer | 2 | **10** | 5 | 3 | 150 | **100** | $2,000 | Attack, FireBall(100×4), ManaRecharge |
| Priest | 3 | **8** | 5 | 4 | 200 | 80 | $2,500 | Attack, Healing(200), Blessing(SP regen) |
| Hunter | 2 | 2 | **10** | **6** | N/A | N/A | $4,000 | Shoot(dex×1), DoubleShot(dex×2) |

---

## 隊伍現狀

| 角色 | 職業 | Lv | 狀態 |
|------|------|:--:|:----:|
| **Hero1** | Warrior | Lv.2 (Exp 20/30) | Claymore裝備, 前排 |
| **Healer1** | Priestess | Lv.1 | 初始裝備, 後排補師 |
| **Mage1** | Sorceress | Lv.1 | 初始裝備, 後排魔法師 |
| **Funds** | — | — | 約 $38,180 |

## 已知問題

- PHP Warning: `Invalid argument supplied for foreach() in Model/Data.php:418` — 出現在 Hunt 頁面
- Hunter 職業缺少 `maxhp`/`maxsp` 定義，可能導致異常

## 待研究

- [ ] 戰鬥方針系統（Judge.php / Pattern.php）
- [ ] 技能學習條件與技能樹（Skilltree/）
- [ ] 轉職系統的具體條件
- [ ] 精錬系統的數值成長
