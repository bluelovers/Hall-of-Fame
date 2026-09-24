# 戰鬥訊息格式與顯示類型分析

> 分析日期：2026-09-25
> 分析範圍：`HOF/Class/Battle/Skill.php` + `HOF/Class/Skill/Effect.php` + `HOF/Class/Char/Battle/Effect.php` + `HOF/Class/Battle.php` + `HOF/Class/Battle/View.php` + `hof/static/style/basis.css`

---

## 目錄

1. [CSS 顯示類型總覽](#1-css-顯示類型總覽)
2. [核心訊息格式](#2-核心訊息格式)
3. [技能使用訊息](#3-技能使用訊息)
4. [傷害與回復訊息](#4-傷害與回復訊息)
5. [狀態變更訊息](#5-狀態變更訊息)
6. [特殊效果訊息](#6-特殊效果訊息)
7. [死亡與獎勵訊息](#7-死亡與獎勵訊息)
8. [戰鬥流程訊息](#8-戰鬥流程訊息)
9. [HP/SP 面板格式](#9-hpsp-面板格式)
10. [相關原始碼檔案](#10-相關原始碼檔案)

---

## 1. CSS 顯示類型總覽

> 來源：`hof/static/style/basis.css` 第 160-186 行

### 1.1 色彩語意

| CSS Class | 顏色 | 色碼 | Text-Shadow | 語意 |
|-----------|------|------|-------------|------|
| `.dmg` | 紅色 | `#cc3300` | `#E63D3D` | 傷害、HP 減少、死亡、失敗 |
| `.recover` | 藍色 | `#3366ff` | `#2E7398` | HP 回復、復活 |
| `.support` | 綠色 | `#66cc66` | `#88FF77` | SP 回復、增益效果、支援 |
| `.spdmg` | 紫色 | `#993399` | `#B02FDD` | SP 傷害、中毒、減益 |
| `.levelup` | 黃色粗體 | `#ffff33` | `#D34F16` | 升級 |
| `.charge` | 金色 | `#ffcc33` | `#8F7` | 詠唱、蓄力、魔方陣 |

### 1.2 通用工具 Class

| CSS Class | 樣式 | 用途 |
|-----------|------|------|
| `.bold` | `font-weight: bold` | 強調文字（角色名、數值） |
| `.u` | `text-decoration: underline` | 技能名稱標題 |
| `.vcent` | `vertical-align: middle; margin: 0 5px` | 圖標垂直置中 |
| `.result` | — | 戰鬥結果訊息 |
| `.break` | — | 分隔線 |
| `.battle_frame` | — | 戰鬥表格框線 |

### 1.3 色彩映射規則

```
紅色 (.dmg)     ← 造成傷害、死亡、失敗、消滅
藍色 (.recover) ← HP 回復、復活、自動回復
綠色 (.support) ← SP 回復、增益 Buff、魔方陣增加、支援效果
紫色 (.spdmg)   ← SP 傷害、中毒傷害、中毒狀態、減益 Debuff
黃色 (.levelup) ← 升級
金色 (.charge)   ← 詠唱/蓄力、魔方陣消費、延遲
```

---

## 2. 核心訊息格式

### 2.1 訊息結構模板

所有戰鬥訊息都遵循以下結構模式：

```
[樣式標籤] 角色名稱 [效果描述] [/樣式標籤] <br />
```

### 2.2 角色名稱格式

```php
// Char/View.php → Name()
$char->Name('bold')   // <span class="bold">角色名</span>
$char->Name(true)     // 同上（布林值等同 'bold'）
$char->Name()         // 純文字角色名
```

### 2.3 數值強調格式

```php
// 內層用 .bold 強調數值
'<span class="dmg"><span class="bold">1234</span> Damage</span>'
// 外層 = 語意色，內層 = 粗體
```

### 2.4 值變化追蹤格式

```php
// ShowValueChange() — 附在傷害/回復訊息後的 HP/SP 變化
"({$from} &gt; {$to})"
// 範例：(1500 > 1200)
// 由 HpDamage()/HpRecover()/SpDamage()/SpRecover() 自動附加
```

---

## 3. 技能使用訊息

> 來源：`HOF_Class_Battle_Skill::UseSkill()` (Battle/Skill.php)

### 3.1 技能標題（行動宣告）

```php
// 第 115-117 行
echo '<div class="u">' . $My->Name('bold');
echo '<img src="[圖示URL]" class="vcent"/>';
echo $skill[name] . '</div>';
```

**格式：**
```html
<div class="u"><b>角色名</b><img src="..." class="vcent"/>技能名</div>
```

**特徵：**
- `<div class="u">` 底線區塊
- 角色名 + 圖示 + 技能名
- 每次實際使用技能時輸出（詠唱/蓄力開始不輸出）

### 3.2 詠唱/蓄力開始

```php
// 物理技能
'<span class="charge">' . $My->Name('bold') . ' start charging.</span>'

// 魔法技能
'<span class="charge">' . $My->Name('bold') . ' start casting.</span>'
```

**格式：**
```html
<span class="charge"><b>角色名</b> start charging.</span>
<span class="charge"><b>角色名</b> start casting.</span>
```

### 3.3 行動後硬直

```php
// 第 228 行
echo $My->Name('bold') . " Delayed";
$My->DelayByRate($skill["charge"]["1"], $this->battle->delay, 1);
echo "<br />\n";
```

**格式：**
```html
<b>角色名</b> Delayed(15 >>> 25/100)<br />
```

> `DelayByRate()` 的 `$Show=true` 會輸出 `(舊值 >>> 新值/基準)` 格式。

### 3.4 失敗訊息

| 情況 | 格式 | 樣式 |
|------|------|------|
| 武器類型不符 | `<span class="u">{名}<span class="dmg"> Failed </span>to <img>{技名}</span>` | `.u` + `.dmg` |
| 武器原因說明 | `(Weapon type doesnt match)` | 無樣式 |
| SP 不足 | `{名} failed to {技名}(SP shortage)` | 無樣式 |
| 魔方陣不足 | `<span class="dmg">failed!(MagicCircle isn't enough)</span>` | `.dmg` |
| 無目標 | `No target.Failed!` | 無樣式 |
| 無 pattern | `{名} sunk in thought and couldn't act.<br />(No more patterns)` | 無樣式 |

---

## 4. 傷害與回復訊息

> 來源：`HOF_Class_Skill_Effect` (Skill/Effect.php 第 753-822 行)

### 4.1 HP 傷害 — DamageHP() / DamageHP2()

```php
print('<span class="dmg"><span class="bold">' . $value . '</span> Damage</span> to ' . $target->Name("bold"));
$target->HpDamage($value);
print("<br />\n");
// HpDamage() 內部會呼叫 ShowValueChange() 附加 (before > after)
```

**輸出範例：**
```html
<span class="dmg"><span class="bold">1234</span> Damage</span> to <b>史萊姆</b>(1500 > 266)<br />
```

### 4.2 SP 傷害 — DamageSP()

```php
print('<span class="spdmg"><span class="bold">' . $value . '</span>SP Damage</span> to ' . $target->Name("bold"));
$target->SpDamage($value);
print("<br />\n");
```

**輸出範例：**
```html
<span class="spdmg"><span class="bold">50</span>SP Damage</span> to <b>法師</b>(100 > 50)<br />
```

### 4.3 HP 回復 — RecoverHP()

```php
print($target->Name("bold") . ' <span class="recover">Recovered <span class="bold">' . $value . ' HP</span></span>');
$target->HpRecover($value);
print("<br />\n");
```

**輸出範例：**
```html
<b>戰士</b> <span class="recover">Recovered <span class="bold">500 HP</span></span>(1000 > 1500)<br />
```

### 4.4 SP 回復 — RecoverSP()

```php
print($target->Name("bold") . ' <span class="support">Recovered <span class="bold">' . $value . ' SP</span></span>');
$target->SpRecover($value);
print("<br />\n");
```

**輸出範例：**
```html
<b>法師</b> <span class="support">Recovered <span class="bold">80 SP</span></span>(120 > 200)<br />
```

### 4.5 HP 吸收 — AbsorbHP()

```php
print('Drained <span class="recover"><span class="bold">' . $value . '</span> HP</span>');
$char->HpRecover($value);
print(' from ' . $target->Name('bold'));
$target->HpDamage($value);
print("<br />\n");
```

**輸出範例：**
```html
Drained <span class="recover"><span class="bold">300</span> HP</span> from <b>敵人</b>(1500 > 1200)<b>我方</b>(800 > 1100)<br />
```

### 4.6 SP 吸收 — AbsorbSP()

```php
print('Drained <span class="support"><span class="bold">' . $value . '</span> SP</span>');
$char->SpRecover($value);
print(' from ' . $target->Name('bold'));
$target->SpDamage($value);
print("<br />\n");
```

**輸出範例：**
```html
Drained <span class="support"><span class="bold">60</span> SP</span> from <b>敵法師</b>(100 > 40)<b>我方</b>(50 > 110)<br />
```

### 4.7 毒傷害 — PoisonDamage()

```php
print('<span class="spdmg">' . $this->char->Name('bold') . " got ");
print('<span class="bold">' . $poison . '</span> damage by poison.');
$this->char->HpDamage2($poison);
print('</span><br />');
```

**輸出範例：**
```html
<span class="spdmg"><b>哥布林</b> got <span class="bold">150</span> damage by poison.(1200 > 1050)</span><br />
```

### 4.8 傷害減免訊息

```php
// Barrier 擋下攻擊
print("Attack has disappeared.<br />\n");
// LifeDivision 値過大補正
print("※値が大きすぎて補正されました。<br />\n");
```

### 4.9 傷害倍率提示

```php
// PoisonBlow 中毒時
print("Damage x6!<br />\n");

// SoulRevenge 依死亡數
print("Damage x" . $option["multiply"] . "!<br />\n");
// 範例：Damage x3!<br />
```

---

## 5. 狀態變更訊息

### 5.1 屬性上升 (Plus) — 固定值

> 來源：`Char/Battle/Effect.php` 第 236-260 行

```php
print($this->char->Name('bold') . " STR rise {$no}<br />\n");
```

| 函式 | 訊息格式 |
|------|---------|
| `PlusSTR()` | `{名} STR rise {N}` |
| `PlusINT()` | `{名} INT rise {N}` |
| `PlusDEX()` | `{名} DEX rise {N}` |
| `PlusSPD()` | `{名} SPD rise {N}` |
| `PlusLUK()` | `{名} LUK rise {N}` |

### 5.2 屬性上升 (Up) — 百分比

> 來源：`Char/Battle/Effect.php` 第 263-348 行

```php
// 一般情況
print($this->char->Name('bold') . " STR rise {$no}%<br />\n");

// 達到上限
print($this->char->Name('bold') . " STR rise to the maximum(" . MAX_STATUS_MAXIMUM . "%).<br />\n");
```

| 函式 | 訊息格式 |
|------|---------|
| `UpSTR()` | `{名} STR rise {N}%` |
| `UpINT()` | `{名} INT rise {N}%` |
| `UpDEX()` | `{名} DEX rise {N}%` |
| `UpSPD()` | `{名} SPD rise {N}%` |
| `UpATK()` | `{名} ATK rise {N}%` |
| `UpMATK()` | `{名} MATK rise {N}%` |
| `UpDEF()` | `{名} DEF rise {N}%` |
| `UpMDEF()` | `{名} MDEF rise {N}%` |
| `UpMAXHP()` | `{名} MAXHP({舊值}) extended to {新值}` |
| `UpMAXSP()` | `{名} MAXSP({舊值}) extended to {新值}` |

**上限訊息：**
```
{名} STR rise to the maximum(2500%).
```

### 5.3 屬性下降 (Down)

> 來源：`Char/Battle/Effect.php` 第 350-403 行

| 函式 | 訊息格式 |
|------|---------|
| `DownSTR()` | `{名} STR down {N}%` |
| `DownINT()` | `{名} INT down {N}%` |
| `DownDEX()` | `{名} DEX down {N}%` |
| `DownSPD()` | `{名} SPD down {N}%` |
| `DownATK()` | `{名} ATK down {N}%` |
| `DownMATK()` | `{名} MATK down {N}%` |
| `DownDEF()` | `{名} DEF down {N}%` |
| `DownMDEF()` | `{名} MDEF down {N}%` |
| `DownMAXHP()` | `{名} MAXHP({舊值}) down to {新值}` |
| `DownMAXSP()` | `{名} MAXSP({舊值}) down to {新值}` |

> **注意：** 屬性變更訊息**無 CSS 樣式**（純文字）。

### 5.4 中毒狀態

```php
// 中毒成功
print($target->Name('bold') . ' get <span class="spdmg">poisoned</span>&nbsp;!<br />\n');

// 中毒被抵抗
print($target->Name('bold') . ' blocked poison.<br />\n');

// 自我中毒 (GetPoison 技能)
print("Got poisoned<br />\n");
```

**輸出範例：**
```html
<b>盜賊</b> get <span class="spdmg">poisoned</span>&nbsp;!<br />
<b>戰士</b> blocked poison.<br />
```

### 5.5 中毒抗性

```php
print('<span class="support">');
print($this->char->Name('bold') . ' got PoisonResist!(' . $val . '%)');
print('</span><br />');
```

**輸出範例：**
```html
<span class="support"><b>角色名</b> got PoisonResist!(50%)</span><br />
```

### 5.6 Regen 持續回復

```php
// HP Regen
print($target->Name('bold') . '<span class="recover"> gained HP regeneration +' . $val . '%</span><br />');

// SP Regen
print($target->Name('bold') . '<span class="support"> gained SP regeneration +' . $val . '%</span><br />');
```

**輸出範例：**
```html
<b>角色名</b><span class="recover"> gained HP regeneration +5%</span><br />
<b>角色名</b><span class="support"> gained SP regeneration +10%</span><br />
```

### 5.7 自動回復 (AutoRegeneration)

```php
// HP Regen
print('<span class="recover">* </span>' . $this->char->Name('bold') . '<span class="recover"> Auto Regenerate <span class="bold">' . $Regen . ' HP</span></span> ');

// SP Regen
print('<span class="support">* </span>' . $this->char->Name('bold') . '<span class="support"> Auto Regenerate <span class="bold">' . $Regen . ' SP</span></span> ');
```

**輸出範例：**
```html
<span class="recover">* </span><b>角色名</b><span class="recover"> Auto Regenerate <span class="bold">150 HP</span></span>
```

---

## 6. 特殊效果訊息

### 6.1 增益效果 (Support)

| 效果 | 訊息格式 | 樣式 |
|------|---------|------|
| Quick | `{名} got quicked!` | `.support` |
| Cast 加速 | `{名} casting shorted!` | `.support` |
| Barrier | `{名} got barriered!` | `.support` |
| 魔方陣增加 | `{名} draw MagicCircle x{N}` | `.support` |

**範例：**
```html
<span class="support"><b>角色名</b> got quicked!</span>
<span class="support"><b>角色名</b> got barriered!</span><br />
<b>角色名</b><span class="support"> draw MagicCircle x2</span><br />
```

### 6.2 減益效果 (Debuff)

| 效果 | 訊息格式 | 樣式 |
|------|---------|------|
| 魔方陣消除 | `{名} erased enemy MagicCircle x{N}` | `.dmg` |
| 擊退 | `{名} knock backed!` | 無樣式 |
| 延遲 | `{名} delayed (值 >>> 新值/基準)` | 無樣式 |
| HP 犧牲 | `{名} sacrifice {N} HP` | `.dmg` |

**範例：**
```html
<b>角色名</b><span class="dmg"> erased enemy MagicCircle x1</span><br />
<b>角色名</b> knock backed!<br />
<b>角色名</b> delayed <span style="font-size:80%">&gt;&gt;&gt;</span>(25/100).<br />
<span class="dmg"><b>角色名</b> sacrifice <span class="bold">1500</span> HP</span><br />
```

### 6.3 移動訊息

```php
// Move()
print($this->char->Name('bold') . ' moved to front.<br />\n');
print($this->char->Name('bold') . ' moved to back.<br />\n');

// Charge!!! 技能
print($target->Name('bold') . ' goes forward.<br />');
```

**輸出範例：**
```html
<b>角色名</b> moved to front.<br />
<b>角色名</b> moved to back.<br />
```

### 6.4 詠唱/蓄力狀態顯示

```php
// ShowHpSp() 面板中的狀態標示
if ($this->char->expect_type === EXPECT_CHARGE)
    $output .= '<span class="charge">(charging)</span>';
elseif ($this->char->expect_type === EXPECT_CAST)
    $output .= '<span class="charge">(casting)</span>';
```

### 6.5 HP/SP 交換 — EnergyExchange

```php
print($target->Name(true) . ' exchanged rate of HP and SP.<br />');
print('HP: ' . $target->HP . '(' . $HpRate . '%) to ');
// 計算新值
print($target->HP . '(' . $SpRate . '%)<br />');
print('SP: ' . $target->SP . '(' . $SpRate . '%) to ');
// 計算新值
print($target->SP . '(' . $HpRate . '%)<br />');
```

**輸出範例：**
```html
<b>角色名</b> exchanged rate of HP and SP.<br />
HP: 500(50%) to 800(80%)<br />
SP: 80(80%) to 50(50%)<br />
```

### 6.6 魔方陣消費 (Skill.php)

```php
echo($My->Name('bold') . '<span class="charge"> use MagicCircle x' . $N . '</span><br />');
```

### 6.7 Barrier 擋下攻擊

```php
print('Attack has disappeared.<br />');
```

### 6.8 參戰/退場

```php
// enterBattlefield()
printf('<span class="%s">%s Lv.%d %s the Battlefield.</span>',
    $leave ? 'dmg' : 'result',
    $this->char->Name('blod'),   // ⚠️ 原始碼 typo: blod → bold
    $this->char->level,
    $leave ? 'leave' : 'enter');
```

**輸出範例：**
```html
<span class="result">角色名 Lv.15 enter the Battlefield.</span>
<span class="dmg">角色名 Lv.15 leave the Battlefield.</span>
```

### 6.9 召喚入場

```php
$add->ShowImage(vcent);
print($add->Name('bold') . ' joined to the team.<br />');
$add->enterBattlefield();
```

**輸出範例：**
```html
<img src="..." class="vcent"/><b>召喚獸</b> joined to the team.<br />
<span class="result">召喚獸 Lv.10 enter the Battlefield.</span>
```

---

## 7. 死亡與獎勵訊息

### 7.1 死亡訊息

```php
// JudgeTargetsDead()
echo('<span class="dmg">' . $target->Name('bold') . ' down.</span><br />');
```

**輸出範例：**
```html
<span class="dmg"><b>哥布林</b> down.</span><br />
```

### 7.2 復活訊息

```php
// GetNormal() — Mon.php / UnionMon.php / Effect.php
print($this->Name('bold') . ' <span class="recover">revived</span>!<br />');
```

**輸出範例：**
```html
<b>角色名</b> <span class="recover">revived</span>!<br />
```

### 7.3 升級訊息

```php
echo('<span class="levelup">' . $char->Name() . ' LevelUp!</span><br />');
```

**輸出範例：**
```html
<span class="levelup">角色名 LevelUp!</span><br />
```

### 7.4 EXP 分配訊息

```php
echo("Alives get {$ExpGet}exps.<br />");
```

**輸出範例：**
```html
Alives get 250exps.<br />
```

### 7.5 金錢獲取訊息

```php
echo($team->team_name() . ' Get ' . HOF_Helper_Global::MoneyFormat($money) . '.<br />');
```

**輸出範例：**
```html
我的隊伍 Get 1,500.<br />
```

### 7.6 物品掉落訊息

```php
echo($char->Name('bold') . ' dropped');
echo('<img src="[圖示URL]" class="vcent"/>');
echo('<span class="bold u">' . $item[name] . '</span>.<br />');
```

**輸出範例：**
```html
<b>哥布林</b> dropped<img src="..." class="vcent"/><span class="bold u">鐵劍</span>.<br />
```

### 7.7 守護訊息

```php
printf('%s protected %s!<br />', $defender->Name('bold'), $target->Name('bold'));
```

**輸出範例：**
```html
<b>戰士</b> protected <b>法師</b>!<br />
```

---

## 8. 戰鬥流程訊息

### 8.1 入場訊息

```php
// initEnterBattlefield() → showEnterBattlefield() → enterBattlefield()
printf('<span class="result">%s Lv.%d enter the Battlefield.</span>', $name, $level);
```

### 8.2 戰況快照 — BattleState()

> 來源：`HOF_Class_Battle_View::BattleState()` (View.php)

每 `BATTLE_STAT_TURNS=10` 次行動顯示：
- 場景圖片（含捲動導覽 `<< >>`）
- 左右隊伍 HP/SP 面板

### 8.3 回合延長訊息

```php
// ExtendTurns() — Battle.php 第 690-694 行
echo <<< HTML
<tr><td colspan="2" class="break break-top bold" style="text-align:center;padding:20px 0;">
battle turns extended.
</td></tr>
HTML;
```

### 8.4 戰鬥結果訊息 — ShowResult()

```php
// 平手
echo('<span style="font-size:150%">Draw Game</span><br />');

// 勝利
echo('<span style="font-size:200%">' . $TeamName . ' Wins!</span><br />');

// 模擬戰
echo('模擬戦終了');
```

**輸出範例：**
```html
<span style="font-size:200%">我的隊伍 Wins!</span><br />
<span style="font-size:150%">Draw Game</span><br />
```

### 8.5 結果面板欄位

```
HP remain : {殘血}/{總血}        ← Union 顯示 ????/????
Alive : {存活}/{總數}
TotalDamage : {總傷害}
TotalExp : {總經驗值}            ← 有經驗值時
Funds : {金錢}                   ← 有金錢時
Items                            ← 有物品時
  [圖示] 物品名 x 數量
```

---

## 9. HP/SP 面板格式

> 來源：`HOF_Class_Char_Battle_Effect::ShowHpSp()` (Effect.php 第 22-49 行)

### 9.1 結構

```php
$output = '';

// 1. 狀態標記（影響名稱顏色）
if (STATE_DEAD)    $sub = " dmg";      // 紅色
elseif (STATE_POISON) $sub = " spdmg"; // 紫色

// 2. 角色名
$output .= '<span class="bold' . $sub . '">' . $name . '</span>';

// 3. 詠唱/蓄力標記
if (EXPECT_CHARGE) $output .= '<span class="charge">(charging)</span>';
elseif (EXPECT_CAST) $output .= '<span class="charge">(casting)</span>';

// 4. HP/SP 數值
$output .= '<div class="hpsp">';
$output .= '<span class="{dmg|recover}">HP : {hp}/{maxhp}</span><br />';
$output .= '<span class="{dmg|support}">SP : {sp}/{maxsp}</span>';
$output .= '</div>';
```

### 9.2 狀態對應色彩

| 狀態 | 角色名顏色 | HP 顏色 | SP 顏色 |
|------|-----------|---------|---------|
| 正常 | 白（預設） | `.recover` 藍 | `.support` 綠 |
| 中毒 | `.spdmg` 紫 | `.recover` 藍 | `.support` 綠 |
| 死亡 | `.dmg` 紅 | `.dmg` 紅 | `.dmg` 紅 |

### 9.3 輸出範例

```html
<!-- 正常狀態 -->
<span class="bold">戰士</span>
<div class="hpsp">
  <span class="recover">HP : 1500/2000</span><br />
  <span class="support">SP : 80/100</span>
</div>

<!-- 中毒 + 詠唱 -->
<span class="bold spdmg">法師</span><span class="charge">(casting)</span>
<div class="hpsp">
  <span class="recover">HP : 600/1200</span><br />
  <span class="support">SP : 150/300</span>
</div>

<!-- 死亡 -->
<span class="bold dmg">盜賊</span>
<div class="hpsp">
  <span class="dmg">HP : 0/900</span><br />
  <span class="dmg">SP : 0/80</span>
</div>
```

---

## 10. 相關原始碼檔案

| 檔案 | 關鍵函式 | 說明 |
|------|---------|------|
| `HOF/Class/Battle/Skill.php` | `UseSkill()` | 技能宣告、詠唱、失敗、硬直訊息 |
| `HOF/Class/Skill/Effect.php` | `DamageHP()`, `DamageHP2()`, `DamageSP()`, `RecoverHP()`, `RecoverSP()`, `AbsorbHP()`, `AbsorbSP()`, `SkillEffect()`, `StatusChanges()`, `DelayChar()` | 傷害/回復核心訊息、技能效果訊息 |
| `HOF/Class/Char/Battle/Effect.php` | `ShowHpSp()`, `ShowValueChange()`, `PoisonDamage()`, `AutoRegeneration()`, `SacrificeHp()`, `Move()`, `KnockBack()`, `Plus*/Up*/Down*()`, `GetPoison()`, `GetPoisonResist()`, `enterBattlefield()` | HP/SP 面板、狀態訊息、屬性變更訊息 |
| `HOF/Class/Battle.php` | `Action()`, `BattleResult()`, `ExtendTurns()`, `JudgeTargetsDead()`, `Defending()`, `getExp()`, `getMoney()` | 戰鬥流程訊息、死亡/獎勵訊息 |
| `HOF/Class/Battle/View.php` | `BattleHeader()`, `BattleState()`, `ShowResult()` | 戰鬥面板、結果畫面 |
| `HOF/Class/Char/Type/Mon.php` | `GetNormal()` | 怪物復活訊息 |
| `HOF/Class/Char/Type/UnionMon.php` | `GetNormal()`, `UpMAXHP()`, `UpMAXSP()` | Union 怪特殊訊息 |
| `hof/static/style/basis.css` | `.dmg`, `.recover`, `.support`, `.spdmg`, `.levelup`, `.charge`, `.bold`, `.u` | 戰鬥訊息色彩定義 |

### 相關分析文件

| 文件 | 內容 |
|------|------|
| [戰鬥機制與算法分析](02-battle-mechanism.md) | 傷害公式、回復公式、Buff/Debuff、詠唱系統 |
| [角色與裝備系統分析](03-char-equipment-system.md) | 角色屬性、HP/SP 公式、裝備系統 |
| [隊伍系統分析](04-party-team-system.md) | 隊伍編成、敵方生成、時間系統 |
| [戰鬥細節系統分析](05-battle-details.md) | 常數數值、AI 判定、行為模式 |
| [戰鬥過程與結果系統分析](06-battle-process-result.md) | Process 迴圈、BattleResult 判定、View 顯示、獎勵系統 |
