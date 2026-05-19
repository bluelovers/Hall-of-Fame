---
tags:
  - docs/data
  - hof/resource/land
  - hof/game-data
---

# 地形 (Land) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Land/`
- **檔案數**: 25
- **檔案範例**: `land.ac0.yml`, `land.ac1.yml`, ... `land.volc1.yml`

## 結構定義

```yaml
no: ac0                  # 地形編號
land:                    # 基礎資訊
    name: 古の洞窟       # 中文名稱
    name0: TheAncientCave # 英文名稱
    land: cave           # 地形類型
    proper: 'Lv??'       # 適合等級（Lv?? 為故意設計，非缺漏，請參照 docs/log/battle/01-battle-system.md 說明）
monster:                 # 怪物分布
    1010:
        - 0
        - 1
    1011:
        - 0
        - 1
    1012:
        - 500
        - 0
    1013:
        - 150
        - 1
    1014:
        - 150
        - 1
    1015:
        - 150
        - 1
    1016:
        - 100
        - 0
    1017:
        - 50
        - 0
trigger:                 # 觸發條件
    item:
        -
            8000: 1
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | string | 地形編號 |
| `land` | object | 基礎資訊 (名稱、類型、適合等級) |
| `monster` | object | 怪物分布 (怪物編號: [最小等級, 最大等級]) |
| `trigger` | object | 觸發條件 (物品需求等) |

## 地形類型

| 類型 | 說明 |
|------|------|
| `cave` | 洞窟 |
| `grass` | 草原 |
| `jungle` | 叢林 |
| `mount` | 山地 |
| `nest` | 巢穴 |
| `ocean` | 海洋 |
| `pavement` | 鋪裝道路 |
| `sand` | 沙漠 |
| `sea` | 海域 |
| `snow` | 雪地 |
| `swamp` | 沼澤 |
| `volc` | 火山 |

## 特殊地形代碼

| 代碼 | 說明 |
|------|------|
| `ac0-ac4` | 古代洞窟系列 |
| `blow` | 爆炸地區 |
| `des0` | 沙漠系列 |
| `gb0-gb2` | 綠帶系列 |
| `horh` | 水平線 |
| `mt0` | 山頂 |
| `ocea` | 海洋 |
| `plun` | 行星 |
| `sand` | 沙地 |
| `sea0-sea1` | 海域 |
| `snow0-snow2` | 雪地 |
| `swam0-swam1` | 沼澤 |
| `volc0-volc1` | 火山 |