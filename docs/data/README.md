---
tags:
  - docs/data
  - hof/data
  - hof/yaml
  - hof/resource
---

# YAML 資料結構分析

本目錄包含 Hall of Fame 專案中 YAML 格式資料的結構分析，按資料類型分類。

## 資料分類摘要

| 類別 | 檔案數 | 路徑 |
|------|--------|------|
| 角色 (Char) | 4 | `hof/trust_path/HOF/Resource/Char/` |
| 物品 (Item) | 181 | `hof/trust_path/HOF/Resource/Item/` |
| 技能 (Skill) | 268 | `hof/trust_path/HOF/Resource/Skill/` |
| 職業 (Job) | 17 | `hof/trust_path/HOF/Resource/Job/` |
| 怪物 (Mon) | 147 | `hof/trust_path/HOF/Resource/Mon/` |
| 技能樹 (Skilltree) | 163 | `hof/trust_path/HOF/Resource/Skilltree/` |
| 防禦模式 (Guard) | 8 | `hof/trust_path/HOF/Resource/Guard/` |
| 判定 (Judge) | 128 | `hof/trust_path/HOF/Resource/Judge/` |
| 地形 (Land) | 25 | `hof/trust_path/HOF/Resource/Land/` |
| 工會 (Union) | 12 | `hof/trust_path/HOF/Resource/Union/` |
| 遊戲資料 (dat) | 4 | `hof/trust_path/dat/` |
| 測試資料 (test) | 35+ | `hof/trust_path/test/` |

**總計: 953 個 YAML 檔案** (不含 cache 目錄)

## 資料關係

- [資料類型關係總表](./relationships.md) — 完整描述各資料類型之間的關聯性、存取路徑與交互流程

## 檔案命名規則

- **Char**: `char.{編號}.yml` (編號範圍: 100-400)
- **Item**: `item.{編號}.yml` (編號範圍: 1000-9000+)
- **Skill**: `skill.{編號}.yml` (編號範圍: 1000-9000+)
- **Job**: `job.{編號}.yml` (編號範圍: 100-900)
- **Mon**: `mon.{編號}.yml` (編號範圍: 1000-5000+)
- **Skilltree**: `skilltree.{編號}.yml` (編號範圍: 1000-9000+)
- **Guard**: `guard.{類型}.yml` (類型: always, life25, life50, life75, never, prob25, prob75, prpb50)
- **Judge**: `judge.{編號}.yml` (編號範圍: 1000-9000+)
- **Land**: `land.{類型}.yml` (類型: ac0, ac1, ac2, ac3, ac4, blow, des0, gb0, gb1, gb2, horh, mt0, ocea, plun, sand, sea0, sea1, snow, swam, volc)
- **Union**: `union.{編號}.yml` (編號範圍: 0000-0011)
- **dat**: `auction.yml`, `ranking.yml`
- **test**: `skill.tree.yml`

## 詳細結構分析

- [角色 (Char)](./char.md)
- [物品 (Item)](./item.md)
- [技能 (Skill)](./skill.md)
- [職業 (Job)](./job.md)
- [怪物 (Mon)](./mon.md)
- [技能樹 (Skilltree)](./skilltree.md)
- [防禦模式 (Guard)](./guard.md)
- [判定 (Judge)](./judge.md)
- [地形 (Land)](./land.md)
- [工會 (Union)](./union.md)
- [遊戲資料 (dat)](./dat.md)
- [測試資料 (test)](./test.md)