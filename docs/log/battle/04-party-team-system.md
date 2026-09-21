# 隊伍系統分析

> 分析日期：2026-09-21
> 分析範圍：`HOF/Controller/Battle.php` + `HOF/Class/User.php` + `HOF/Class/Battle/Team.php`

---

## 目錄

1. [系統總覽](#1-系統總覽)
2. [使用者系統](#2-使用者系統)
3. [角色管理](#3-角色管理)
4. [隊伍編成](#4-隊伍編成)
5. [戰鬥隊伍 (Battle Team)](#5-戰鬥隊伍-battle-team)
6. [敵方隊伍生成](#6-敵方隊伍生成)
7. [時間系統](#7-時間系統)
8. [金錢系統](#8-金錢系統)
9. [物品系統](#9-物品系統)
10. [排名戰隊伍](#10-排名戰隊伍)
11. [工會戰隊伍](#11-工會戰隊伍)
12. [模擬戰](#12-模擬戰)
13. [戰鬥後處理](#13-戰鬥後處理)
14. [相關原始碼檔案](#14-相關原始碼檔案)

---

## 1. 系統總覽

```
┌─────────────────────────────────────────────────────────────┐
│                      HOF 隊伍系統架構                        │
└─────────────────────────────────────────────────────────────┘

使用者 (User)
    │
    ├── 角色管理
    │   ├── char_all()          載入所有角色
    │   ├── char($no)           取得特定角色
    │   ├── char_list()         角色列表
    │   ├── char_count()        角色數量
    │   └── char_delete($no)    刪除角色
    │
    ├── 隊伍編成
    │   ├── MyParty()           建立戰鬥隊伍 (1~5人)
    │   ├── MemorizeParty()     記憶上次編成
    │   └── party_memo          記憶的隊伍設定
    │
    ├── 資源管理
    │   ├── money               金錢
    │   ├── time                Time (行動資源)
    │   ├── item                物品庫存
    │   └── party_rank          排名戰隊伍
    │
    └── 戰鬥系統
        ├── MonsterBattle()     一般怪物戰
        ├── UnionProcess()      工會戰
        ├── SimuBattleProcess() 模擬戰
        └── RankBattle()        排名戰

戰鬥隊伍 (Battle Team)
    │
    ├── HOF_Class_Battle_Team   戰鬥隊伍物件
    │   ├── CountAlive()        生存者數
    │   ├── CountDead()         死亡者數
    │   ├── CountAliveChars()   生存角色數 (不含召喚物)
    │   ├── CountTrueChars()    真實角色數 (不含召喚物)
    │   ├── pick()              隨機選擇敵人
    │   └── pickList()          批量選擇敵人
    │
    └── 敵方隊伍
        ├── EnemyParty()        生成敵方隊伍
        └── EnemyNumber()       決定敵人數量
```

---

## 2. 使用者系統

### 2.1 使用者資料結構

```php
// User.php
class HOF_Class_User
{
    var $id;            // 使用者 ID
    var $pass;          // 密碼 (加密)
    var $name;          // 使用者名稱
    var $money;         // 金錢
    var $time;          // Time (行動資源)
    var $wtime;         // 總消費時間
    var $ip;            // IP 位址
    var $char;          // 角色陣列
    var $party_memo;    // 記憶的隊伍
    var $party_rank;    // 排名戰隊伍
    var $rank_record;   // 排名戰成績
    var $union_btl_time; // 工會戰時間
    var $rank_btl_time;  // 排名戰時間
}
```

### 2.2 使用者資料儲存

```yaml
# user.{id}.yml — 使用者資料檔
uniqid: "..."
id: "demo"
pass: "..."
ip: "..."
name: "Demo"
timestamp:
  last: 1234567890
  login: 1234567890
options:
  record_btl_log: 1
money: 50000
time: 1000
wtime: 500
party_memo:
  - char_id_1
  - char_id_2
party_rank:
  - char_id_1
  - char_id_3
rank_set_time: 0
rank_btl_time: 0
rank_record:
  all: 10
  win: 7
  lose: 2
  defend: 1
union_btl_time: 0
```

### 2.3 使用者初始化流程

```php
function __construct($id, $noExit = false)
{
    $this->id = (string)$id;

    if ($data = $this->LoadData($noExit))  // 讀取 YAML
    {
        $this->DataUpDate($data);  // 計算 Time 增加
        $this->SetData($data);     // 設定所有屬性
    }
}

function DataUpDate(&$data)
{
    $now = time();
    $diff = $now - $data['timestamp']["last"];
    $data['timestamp']["last"] = $now;
    $gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
    $data["time"] += (int)$gain;
    if (MAX_TIME < $data["time"]) $data["time"] = MAX_TIME;
}
```

**Time 自動回復：**
```
Time 增加量 = (距上次登入秒數 / 86400) × TIME_GAIN_DAY
上限：MAX_TIME (1000)
```

---

## 3. 角色管理

### 3.1 角色列表取得

```php
function char_list($over = null)
{
    // 從快取或檔案系統取得角色列表
    $list_char = HOF_Helper_Char::char_list_by_user($this);

    foreach ($list_char as $no => $file)
    {
        $char = HOF_Class_Char::factory(TYPE_CHAR, $no, null, $this, $this);
        $list[$no] = $char->name;
    }

    $this->cache()->data('char_list', $list);
    return $list;
}
```

### 3.2 全角色載入

```php
function char_all()
{
    $this->char = array();

    if ($list_char = $this->char_list())
    {
        foreach (array_keys($list_char) as $no)
        {
            $file = HOF_Helper_Char::char_file($no, $this->id);

            if (!file_exists($file)) continue;

            $char = HOF_Class_Char::factory(TYPE_CHAR, $no, null, $this, $this);

            if (!$char || is_string($char)) continue;

            $this->char[$no] = $char;
        }
    }

    return $this->char;
}
```

### 3.3 角色數量限制

```php
// setting.dist.php
define('MAX_CHAR', 5);  // 最大所持角色數
```

---

## 4. 隊伍編成

### 4.1 戰鬥隊伍建立 (MyParty)

```php
function MyParty()
{
    $this->MemorizeParty();  // 先嘗試記憶上次編成

    $MyParty = array();

    foreach ((array)$this->input->input_char_id as $k)
    {
        if ($this->user->char[$k])
        {
            $MyParty[] = $this->user->char[$k];
            $i = max($i, $this->user->char[$k]->level);
        }
    }

    $this->_cache['top_level'] = $i;  // 隊伍最高等級

    // 隊伍人數檢查
    if (count($MyParty) === 0)
    {
        $this->_error('戦闘するには最低1人必要');
        return false;
    }
    elseif (5 < count($MyParty))
    {
        $this->_error('戦闘に出せるキャラは5人まで');
        return false;
    }

    return $MyParty;
}
```

### 4.2 隊伍人數限制

| 條件 | 規則 |
|------|------|
| 最少人數 | 1 人 |
| 最多人數 | 5 人 |
| 選擇方式 | 玩家勾選角色 ID |

### 4.3 隊伍記憶功能

```php
function MemorizeParty()
{
    if ($this->input->memory_party)
    {
        foreach ($this->user->char as $key => $val)
        {
            // 勾選的角色加入記憶
            if (in_array($key, $this->input->input_char_id))
            {
                $PartyMemo[] = $key;
            }
        }

        // 記憶人數限制 1~5 人
        if (0 < count($PartyMemo) && count($PartyMemo) < 6)
        {
            $this->user->party_memo = $PartyMemo;
        }
    }
}
```

### 4.4 隊伍最高等級

```php
$this->_cache['top_level'] = max($char->level);  // 隊伍中最高等級的角色
```

**用途：**
- 決定敵人數量 (`EnemyNumber`)
- 決定敵人等級調整幅度
- Union 戰的合計等級限制

---

## 5. 戰鬥隊伍 (Battle Team)

### 5.1 Battle_Team 結構

```php
class HOF_Class_Battle_Team extends HOF_Class_Array_Prop
{
    public $team_name;      // 隊伍名稱
    public $team_idx;       // 隊伍索引 (TEAM_0 或 TEAM_1)
    public $data;           // 額外資料

    // 繼承自 ArrayObject，可像陣列一樣存取角色
    // $team[0] = 第一個角色
    // $team[1] = 第二個角色
}
```

### 5.2 隊伍常數

```php
// HOF/Const/setting.dist.php
define('TEAM_0', '0');  // 玩家隊伍
define('TEAM_1', '1');  // 敵方隊伍
```

### 5.3 隊伍建立

```php
// 從玩家角色陣列建立
$MyParty = HOF_Class_Battle_Team::newInstance($MyParty);

// 或直接建立空隊伍
$team = new HOF_Class_Battle_Team();
```

### 5.4 角色加入隊伍

```php
public function offsetSet($k, $char)
{
    if (!$char instanceof HOF_Class_Char_Abstract)
    {
        throw new Exception(sprintf('%s not a vaild Char', (string)$char));
    }

    $char->team($this);  // 設定角色所屬隊伍

    self::$char_list['all'][$char->uniqid()] = &$char;

    // 重複名稱處理
    if (self::$cache['fixCharName'])
    {
        self::$cache['name_list'][$char->Name()]++;
        $this->_callback_fixCharName($char);
    }

    parent::offsetSet($k, $char);
}
```

### 5.5 隊伍查詢方法

| 方法 | 回傳值 | 說明 |
|------|--------|------|
| `CountAlive()` | int | 生存者數（不含死亡） |
| `CountDead()` | int | 死亡者數（含 Undead） |
| `CountAliveChars()` | int | 生存角色數（不含召喚物） |
| `CountTrueChars()` | int | 真實角色數（不含召喚物） |
| `pick($list)` | string | 依概率隨機選擇敵人 |
| `pickList($n, $list)` | array | 批量選擇 n 個敵人 |

### 5.6 敵人選擇機制 (pick)

```php
public function pick($pick_list = null)
{
    // 計算概率總和
    foreach ($pick_list as $val) $max += $val[0];

    // 在 0~總和 中取隨機數
    $pos = mt_rand(0, $max);

    // 依概率決定敵人
    $list = HOF_Helper_Array::array_shuffle($pick_list);
    foreach ($list as $no => $val)
    {
        $upp += $val[0];
        if ($pos <= $upp) return $no;
    }
}
```

**範例：**
```php
$MonsterList = array(
    array(1000, 4),   // [出現率, 權重]
    array(1001, 3),
    array(1002, 2),
    array(1003, 1),
);
// 總和 = 10
// 每個敵人出現概率 = 權重/10
```

---

## 6. 敵方隊伍生成

### 6.1 敵人數量決定

```php
function EnemyNumber($party)
{
    $min = count($party);  // 玩家 PT 數

    if ($min == 5) return 5;  // 5人 = 5敵

    // 最大敵人數 = min + (最高等級>5 ? ENEMY_INCREASE : 0)
    $max = $min + ($this->_cache['top_level'] > 5 ? ENEMY_INCREASE : 0);

    if ($max > 5) $max = 5;

    return mt_rand($min, $max);  // 隨機 min~max
}
```

**敵人數量規則：**

| 玩家人數 | 最高等級 | 敵人數量範圍 |
|---------|---------|-------------|
| 1 | ≤5 | 1 |
| 1 | >5 | 1~3 |
| 2 | ≤5 | 2 |
| 2 | >5 | 2~4 |
| 3 | ≤5 | 3 |
| 3 | >5 | 3~5 |
| 4 | ≤5 | 4 |
| 4 | >5 | 4~5 |
| 5 | 任何 | 5 |

### 6.2 敵方隊伍生成

```php
function EnemyParty($Amount, $MonsterList, $Specify = false)
{
    $team = new HOF_Class_Battle_Team();

    // 從怪物列表中隨機選擇
    $MonsterNumbers = array_merge(
        $Specify,  // 指定怪物
        $team->pickList($Amount, $MonsterList)  // 隨機選擇
    );

    // 等級調整
    $lv_arr = range(-3, 5);  // -3 ~ +5 的範圍
    $lv_arr = array_pad($lv_arr, count($lv_arr) + 3, 0);  // +3 個 0
    $lv_arr = array_pad($lv_arr, count($lv_arr) + 3, 1);  // +3 個 1
    shuffle($lv_arr);

    foreach ($MonsterNumbers as $Number)
    {
        $char = HOF_Model_Char::newMon($Number);

        // 等級差異大於10時，大幅調整
        if ($this->_cache['top_level'] > ($char->level + 10))
        {
            $lv = mt_rand(
                floor(($this->_cache['top_level'] - $char->level) / 3),
                round($this->_cache['top_level'] - $char->level + 5)
            );
        }
        else
        {
            $lv = $lv_arr[array_rand($lv_arr)];  // 隨機 -3~+5
        }

        $char->level_fix($lv);
        $team[] = $char;
    }

    return $team;
}
```

### 6.3 怪物等級調整邏輯

```
玩家最高等級 vs 怪物基礎等級
    │
    ├─ 差異 > 10
    │   └─ $lv = rand(floor(diff/3), round(diff+5))
    │      大幅調整，確保怪物有挑戰性
    │
    └─ 差異 ≤ 10
        └─ $lv = 隨機 -3~+5
           小幅調整，保持隨機性
```

---

## 7. 時間系統

### 7.1 Time 概念

| 常數 | 值 | 說明 |
|------|-----|------|
| `MAX_TIME` | 1000 | 時間上限 |
| `TIME_GAIN_DAY` | 依設定 | 每日自動增加量 |
| `NORMAL_BATTLE_TIME` | 100 | 一般戰鬥消耗 |
| `UNION_BATTLE_TIME` | 依設定 | 工會戰消耗 |

### 7.2 Time 消耗

```php
function WasteTime($time)
{
    if ($this->time < $time) return false;  // Time 不足

    $this->time -= $time;
    $this->wtime += $time;  // 累加總消費時間
    return true;
}
```

### 7.3 Time 自動回復

```php
function DataUpDate(&$data)
{
    $now = time();
    $diff = $now - $data['timestamp']["last"];
    $data['timestamp']["last"] = $now;

    // 每日回復量
    $gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
    $data["time"] += (int)$gain;

    // 上限檢查
    if (MAX_TIME < $data["time"]) $data["time"] = MAX_TIME;
}
```

### 7.4 時間消耗規則

| 行為 | 消耗 Time | 條件 |
|------|----------|------|
| 一般戰鬥 | 100 | 玩家時間 ≥ 100 |
| Union 戰 | UNISON_BATTLE_TIME | 冷卻時間已過 |
| 排名戰 | — | 冷卻時間已過 |
| 角色僱用 | 依價格 | — |
| 角色刪除 | 0 | — |

---

## 8. 金錢系統

### 8.1 金錢操作

```php
// 增加金錢
function getMoney($no)
{
    $this->money += $no;
}

// 減少金錢
function TakeMoney($no)
{
    if ($this->money < $no)
    {
        return false;  // 金錢不足
    }
    else
    {
        $this->money -= $no;
        return true;
    }
}
```

### 8.2 初始金錢

```php
// setting.dist.php
define('START_MONEY', 50000);  // 初始金錢
```

---

## 9. 物品系統

### 9.1 物品資料結構

```yaml
# user.{id}.item.yml — 物品庫存
1000: 5    # item_no: 數量
2001: 2
3005: 1
```

### 9.2 物品操作

```php
// 取得物品
function &item($no = null)
{
    if (!isset($this->item) || $no === true)
    {
        $file = HOF_Helper_Char::user_file($this, USER_ITEM);
        $this->fp_item = HOF_Class_File::fplock_file($file, true, true);
        $this->item = HOF_Class_Yaml::load($this->fp_item);
        $this->item = (array)$this->item;
    }

    return $this->item;
}

// 增加物品
function item_add($no, $amount = false)
{
    if ($amount) $this->item[$no] += $amount;
    else $this->item[$no]++;
}

// 刪除物品
function item_remove($no, $amount = false)
{
    if ($this->item[$no] < $amount)
    {
        $amount = $this->item[$no];
    }

    $this->item[$no] -= $amount;
    if ($this->item[$no] < 1) unset($this->item[$no]);

    return $amount;
}

// 儲存物品
function item_save()
{
    ksort($this->item, SORT_STRING);

    foreach ($this->item = array_filter($this->item) as $k => $v)
    {
        if (!$k || !$v) unset($this->item[$k]);
    }

    $file = HOF_Helper_Char::user_file($this, USER_ITEM);
    HOF_Class_Yaml::save($this->fp_item ? $this->fp_item : $file, (array)$this->item);
}
```

---

## 10. 排名戰隊伍

### 10.1 排名戰隊伍設定

```php
var $party_rank;        // 排名戰專用隊伍
var $rank_set_time;     // 設定時間
var $rank_btl_time;     // 下次可挑戰時間
var $rank_record;       // 成績
```

### 10.2 排名戰隊伍取得

```php
function RankParty()
{
    if ($this->is_exist() && !empty($this->party_rank))
    {
        $party = array();

        foreach ($this->party_rank as $no)
        {
            $char = $this->char($no);
            if ($char) $party[] = $char;
        }

        if (!empty($party))
        {
            return $party;
        }
    }

    return false;
}
```

### 10.3 排名戰成績

```php
function RankRecord($result, $side, $DefendMatch)
{
    $record = $this->RankRecordLoad();

    $record["all"]++;

    switch (true)
    {
        case ($result === 0):  // 挑戰者勝
            if ($side == "CHALLENGER") $record["win"]++;
            else $record["lose"]++;
            break;

        case ($result === 1):  // 挑戰者敗
            if ($side == "CHALLENGER") $record["lose"]++;
            else
            {
                $record["win"]++;
                if ($DefendMatch) $record["defend"]++;
            }
            break;

        default:  // 平手
            if ($side != "CHALLENGER" && $DefendMatch)
                $record["defend"]++;
            break;
    }

    $this->rank_record = $record;
}
```

### 10.4 排名戰冷卻

```php
function CanRankBattle()
{
    $now = time();
    if ($this->rank_btl_time <= $now) return true;
    if (!$this->rank_btl_time) return true;

    $left = $this->rank_btl_time - $now;
    $hour = floor($left / 3600);
    $minutes = floor(($left % 3600) / 60);
    $seconds = floor(($left % 3600) % 60);

    return array($hour, $minutes, $seconds);
}
```

---

## 11. 工會戰隊伍

### 11.1 工會戰特殊規則

```php
function UnionProcess()
{
    // 1. 檢查冷卻時間
    if ($this->user->CanUnionBattle() !== true)
    {
        $left_minute = floor($result / 60);
        $left_second = $result % 60;
    }

    // 2. 建立玩家隊伍
    $MyParty = $this->MyParty();

    // 3. 計算合計等級
    foreach ($MyParty as $char)
    {
        $TotalLevel += $char->level;
    }

    // 4. 合計等級檢查
    if ($Union->lv_limit < $TotalLevel)
    {
        $this->_error('合計レベルオーバー');
        return false;
    }

    // 5. 敵方人數決定
    if ($UnionMob['data']['team']["servantAmount"])
    {
        $EneNum = $UnionMob['data']['team']["servantAmount"] + 1;
    }
    else
    {
        $EneNum = 5;  // 預設 5 人
    }

    // 6. 生成敵方隊伍
    $EnemyParty = $this->EnemyParty($EneNum - 1, ...);

    // 7. Union Boss 插入隊伍中央
    $EnemyParty->insert(floor(count($EnemyParty) / 2), $Union);

    // 8. 標記為 Union 戰
    $EnemyParty->data('isUnion', true);
}
```

### 11.2 Union Boss 特殊處理

- Union Boss 插入敵方隊伍**中央位置**
- 敵方隊伍標記 `isUnion = true`
- Union Boss 死亡後不獲得掉落物
- 合計等級限制 (`lv_limit`)

---

## 12. 模擬戰

### 12.1 模擬戰流程

```php
function SimuBattleProcess()
{
    if ($this->input->monster_battle)
    {
        $MyParty = $this->MyParty();

        HOF_Helper_Battle::DoppelBattle($MyParty, 50);
        return true;
    }
}
```

**模擬戰特性：**
- 無需消耗 Time
- 與自己的分身戰鬥
- 用於測試隊伍配置

---

## 13. 戰鬥後處理

### 13.1 戰鬥後流程

```php
// MonsterBattle() 中
$battle = new HOF_Class_Battle($MyParty, $EnemyParty);
$battle->SetBackGround($Land["land"]);
$battle->SetTeamName($this->user->name, $Land["name"]);
$battle->Process();              // 戰鬥開始

$battle->SaveCharacters();       // 保存角色資料
list($UserMoney) = $battle->ReturnMoney();  // 取得金錢
$this->user->getMoney($UserMoney);          // 增加金錢

// 戰鬥日誌保存
if ($this->user->options['record_btl_log']) $battle->RecordLog();

// 掉落物取得
if ($itemdrop = $battle->ReturnItemGet(TEAM_0))
{
    foreach ($itemdrop as $itemno => $amount)
    {
        $this->user->item_add($itemno, $amount);
    }
    $this->user->item_save();
}

// 使用者資料保存
$this->user->SaveData();
```

### 13.2 角色資料保存

```php
// Battle.php → SaveCharacters()
function SaveCharacters()
{
    foreach ($this->teams as $idx => $data)
    {
        foreach ($data['team'] as $char)
        {
            if ($char->isChar())
            {
                $char->saveCharData();  // 保存角色 YAML
            }
        }
    }
}
```

### 13.3 金錢取得

```php
// Battle.php → ReturnMoney()
function ReturnMoney()
{
    foreach ($this->teams as $idx => $data)
    {
        foreach ($data['team'] as $char)
        {
            if ($char->isMon() && !$char->isSummon())
            {
                $money += $char->reward['moneyhold'] * MONEY_RATE;
            }
        }
    }

    return array($money);
}
```

---

## 14. 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Controller/Battle.php` | `MyParty()`, `MonsterBattle()`, `EnemyParty()`, `EnemyNumber()` | 戰鬥控制器，隊伍編成與戰鬥流程 |
| `HOF/Class/User.php` | `char_all()`, `char()`, `item_add()`, `getMoney()`, `WasteTime()` | 使用者管理，角色/物品/金錢/時間 |
| `HOF/Class/Battle/Team.php` | `CountAlive()`, `CountDead()`, `pick()`, `pickList()` | 戰鬥隊伍管理 |
| `HOF/Class/Battle.php` | `Process()`, `SaveCharacters()`, `ReturnMoney()`, `ReturnItemGet()` | 戰鬥主引擎 |
| `HOF/Helper/Char.php` | `char_list_by_user()`, `char_file()`, `user_path()` | 角色檔案路徑管理 |
| `HOF/Model/Char.php` | `newBaseChar()`, `newMon()`, `newUnion()` | 角色物件建立 |
