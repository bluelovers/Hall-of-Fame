# 戰鬥準備頁面 — Battle Prepare

- **網址 (URL):** `http://localhost:8085/battle/common?land=gb0`
- **Controller:** `HOF_Controller_Battle`
- **Action:** `common`

## 功能說明

進入戰鬥前的準備頁面，顯示我方隊伍與敵方怪物資訊。

### 頁面元素

| 元素 | 說明 |
|------|------|
| **Teams** | 我方隊伍：角色頭像、名稱、等級、職業 |
| **checkbox** | 勾選要出戰的角色 |
| **Battle !** | 開始戰鬥按鈕 |
| **Reset** | 重置選擇 |
| **Save this party** | 儲存當前隊伍配置 |
| **MonsterAppearance** | 敵方怪物：頭像、名稱、等級 |

### 操作流程

1. 勾選至少一個角色（必須勾選，否則顯示「戦闘するには最低1人必要」）
2. 點擊「Battle !」按鈕開始戰鬥

## 已知問題

- 無

## 測試日期

2026-05-10
