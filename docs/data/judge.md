# 判定 (Judge) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Judge/`
- **檔案數**: 128
- **檔案範例**: `judge.1000.yml`, `judge.1001.yml`, ... `judge.9000.yml`

## 結構定義

```yaml
no: 1000                 # 判定編號
exp: 必ず               # 說明
tag:                     # 標籤資訊
    no: '900'
    exp: 基礎
quantity: false         # 是否計算數量
info:                   # 資訊
    desc: 必ず実行される
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | integer | 判定編號 |
| `exp` | string | 判定說明 |
| `tag` | object | 標籤資訊 (no, exp) |
| `quantity` | boolean | 是否計算數量 |
| `info` | object | 詳細資訊 |

## 判定類型

判定用於定義戰鬥行為的條件和效果，例如：
- 必定執行的行動
- 條件觸發的行動 (HP百分比、隨機機率等)
- 特殊效果的觸發條件