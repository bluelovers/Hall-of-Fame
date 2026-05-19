---
tags:
  - docs/data
  - hof/resource/skilltree
  - hof/game-data
---

# 技能樹 (Skilltree) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Skilltree/`
- **檔案數**: 163
- **檔案範例**: `skilltree.1000.yml`, `skilltree.1001.yml`, ... `skilltree.9000.yml`

## 結構定義

```yaml
no: 1000                 # 技能樹編號
check:                   # 學習條件
    -
        and:
            lv:
                - 1
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 技能樹編號 |
| `check` | array | 學習條件列表 |

## 學習條件結構

- `and`: 所有條件都必須滿足
- `or`: 任一條件滿足即可
- `lv`: 等級條件
- `skill`: 需要已學習的技能
- `job`: 職業條件

## 與 skill.tree.yml 的關聯

`hof/trust_path/test/skill.tree.yml` 是一個大型技能樹配置檔案，定義了各職業的技能學習路徑，包含：
- `base`: 基礎技能
- `base_2`: 第二類型基礎技能
- `char_skill_3`: 角色專用技能
- `job`: 各職業技能樹