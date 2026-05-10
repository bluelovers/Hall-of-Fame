# 工會 (Union) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Union/`
- **檔案數**: 12
- **檔案範例**: `union.0000.yml`, `union.0001.yml`, ... `union.0011.yml`

## 結構定義

```yaml
no: '0000'               # 工會編號
name: DragonFleets     # 工會名稱
data:                    # 工會資料
    team:                # 團隊資訊
        name: DragonFleets # 團隊名稱
        servant:           # 成員
            1028:
                - 100
                - 0
            1030:
                - 100
                - 0
            1031:
                - 100
                - 0
            1032:
                - 100
                - 0
            1033:
                - 100
                - 0
    base:                # 基地資訊
        type: mon        # 基地類型
        no: '2000'       # 基地編號
    conditions:          # 參與條件
        lv_limit: '250'  # 等級限制
data_ex:                 # 擴展資料
    name: DragonFleets   # 名稱
    level: '250'         # 等級
    img: mon_013r        # 圖示代號
    land: swamp2         # 所在地形
    cycle: 259200        # 週期 (秒)
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | string | 工會編號 |
| `name` | string | 工會名稱 |
| `data` | object | 工會主要資料 |
| `data_ex` | object | 工會擴展資料 |

## 工會資料結構

- `team`: 團隊資訊，包含成員列表
- `base`: 基地資訊，定義工會的基地類型和位置
- `conditions`: 參與條件，如等級限制
- `data_ex`: 擴展資料，用於顯示和其他功能