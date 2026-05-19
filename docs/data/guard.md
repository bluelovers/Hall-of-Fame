---
tags:
  - docs/data
  - hof/resource/guard
  - hof/game-data
---

# 防禦模式 (Guard) 資料結構分析

## 檔案資訊

- **路徑**: `hof/trust_path/HOF/Resource/Guard/`
- **檔案數**: 8
- **檔案範例**: `guard.always.yml`, `guard.life25.yml`, `guard.life50.yml`, `guard.life75.yml`, `guard.never.yml`, `guard.prob25.yml`, `guard.prob75.yml`, `guard.prpb50.yml`

## 結構定義

```yaml
no: always             # 防禦模式編號
info:                  # 資訊
    desc: 必ず守る
_i18n:                 # 多語系
    ja:
        desc: 必ず守る
    en:
        desc: Always
```

## 欄位說明

| 欄位 | 類型 | 說明 |
|------|------|------|
| `no` | string | 防禦模式編號 |
| `info` | object | 資訊 |
| `_i18n` | object | 多語系翻譯 |

## 防禦模式類型

| 模式 | 說明 |
|------|------|
| `always` | 必定守護 |
| `never` | 從不守護 |
| `life25` | HP 25% 以下守護 |
| `life50` | HP 50% 以下守護 |
| `life75` | HP 75% 以下守護 |
| `prob25` | 25% 機率守護 |
| `prob75` | 75% 機率守護 |
| `prpb50` | 50% 機率守護 (可能為 typo) |