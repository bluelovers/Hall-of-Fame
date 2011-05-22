<?php
include_once(DATA_JUDGE_SETUP);
?>
<div style="margin:0 15px">
<h4>判定(judge)</h4>
<?php
// 一覧を表示する。
/*
$List	= JudgeList();
foreach($List as $No) {
	$Judge	= LoadJudgeData($No);
	print($Judge["exp2"]."<br />\n");
}
*/
?>
<table border="0" cellspacing="0">
  <tbody>
    <tr>
      <td class="td6">必定</td>
      <td class="td6">一定會被執行的</td>
    </tr>
    <tr>
      <td class="td9">往下一次判斷</td>
      <td class="td9">「必定」將跳過</td>
    </tr>
    <tr>
      <td class="td6">自己的 HP10%以上的時候<br>
      自己的 HP20%以上的時候<br>
      自己的 HP30%以上的時候<br>
      自己的 HP40%以上的時候<br>
      自己的 HP50%以上的時候<br>
      自己的 HP60%以上的時候<br>
      自己的 HP70%以上的時候<br>
      自己的 HP80%以上的時候<br>
      自己的 HP90%以上的時候<br>
      自己的 HP10%以下的時候<br>
      自己的 HP20%以下的時候<br>
      自己的 HP30%以下的時候<br>
      自己的 HP40%以下的時候<br>
      自己的 HP50%以下的時候<br>
      自己的 HP60%以下的時候<br>
      自己的 HP70%以下的時候<br>
      自己的 HP80%以下的時候<br>
      自己的 HP90%以下的時候</td>
      <td valign="top" class="td6">HP**%以上/以下<br>
      的場合將被執行。</td>
    </tr>
    <tr>
      <td class="td9">我方有 HP10%以下角色的時候<br>
      我方有 HP30%以下角色的時候<br>
      我方有 HP50%以下角色的時候<br>
      我方有 HP70%以下角色的時候<br>
      我方有 HP90%以下角色的時候</td>
      <td valign="top" class="td9">HP**%以下的時候<br>
      且不只一人的場合執行</td>
    </tr>
    <tr>
      <td class="td6">我方平均 HP 10%以上的時候<br>
      我方平均 HP 30%以上的時候<br>
      我方平均 HP 50%以上的時候<br>
      我方平均 HP 70%以上的時候<br>
      我方平均 HP 90%以上的時候<br>
      我方平均 HP 10%以下的時候<br>
      我方平均 HP 30%以下的時候<br>
      我方平均 HP 50%以下的時候<br>
      我方平均 HP 70%以下的時候<br>
      我方平均 HP 90%以下的時候</td>
      <td valign="top" class="td6">平均HP**%以下/以上<br>
      時執行。</td>
    </tr>
    <tr>
      <td class="td9">自己的 SPが10%以上的時候<br>
      自己的 SPが20%以上的時候<br>
      自己的 SPが30%以上的時候<br>
      自己的 SPが40%以上的時候<br>
      自己的 SPが50%以上的時候<br>
      自己的 SPが60%以上的時候<br>
      自己的 SPが70%以上的時候<br>
      自己的 SPが80%以上的時候<br>
      自己的 SPが90%以上的時候<br>
      自己的 SPが10%以下的時候<br>
      自己的 SPが20%以下的時候<br>
      自己的 SPが30%以下的時候<br>
      自己的 SPが40%以下的時候<br>
      自己的 SPが50%以下的時候<br>
      自己的 SPが60%以下的時候<br>
      自己的 SPが70%以下的時候<br>
      自己的 SPが80%以下的時候<br>
      自己的 SPが90%以下的時候</td>
      <td valign="top" class="td9">SPが**%以上/以下<br>
      的場合執行。</td>
    </tr>
    <tr>
      <td class="td6">我方 SPが10%以下的時候<br>
      我方有 SPが30%以下角色的時候<br>
      我方有 SPが50%以下角色的時候<br>
      我方有 SPが70%以下角色的時候<br>
      我方有 SPが90%以下角色的時候</td>
      <td valign="top" class="td6">SP**%以下的時候<br>
      一人以上的場合執行。</td>
    </tr>
    <tr>
      <td class="td9">我方平均 SPが10%以上的時候<br>
      我方平均 SPが30%以上的時候<br>
      我方平均 SPが50%以上的時候<br>
      我方平均 SPが70%以上的時候<br>
      我方平均 SPが90%以上的時候<br>
      我方平均 SPが10%以下的時候<br>
      我方平均 SPが30%以下的時候<br>
      我方平均 SPが50%以下的時候<br>
      我方平均 SPが70%以下的時候<br>
      我方平均 SPが90%以下的時候</td>
      <td valign="top" class="td9">平均SPが**%以上/以下<br>
      的場合執行。</td>
    </tr>
    <tr>
      <td class="td6">1人以上被打倒的時候<br>
      2人以上被打倒的時候<br>
      3人以上被打倒的時候<br>
      只有自己一個人存活的時候<br>
      2人以上存活的時候<br>
      3人以上存活的時候<br>
      4人以上存活的時候</td>
      <td valign="top" class="td6">我方的<br>
      生存數<br>
      死亡數<br>
      相應時執行。</td>
    </tr>
    <tr>
      <td class="td9">10%概率<br>
      30%概率<br>
      50%概率<br>
      70%概率<br>
      90%概率</td>
      <td valign="top" class="td9">概率。</td>
    </tr>
    <tr>
      <td class="td6">初始行動時<br>
      第2回合行動時<br>
      第3回合行動時<br>
      第4回合行動時<br>
      第5回合行動時</td>
      <td valign="top" class="td6">指定回合<br>
      時執行。</td>
    </tr>
    <tr>
      <td class="td9">一次「必定」<br>
      二次「必定」<br>
      三次「必定」</td>
      <td valign="top" class="td9">「必定」<br>
      會使用的次數。</td>
    </tr>
    <tr>
      <td class="td6">我方 有詠唱角色 的時候<br>
      我方 沒有詠唱角色 的時候<br>
      敵方 有詠唱角色 的時候<br>
      敵方 沒有詠唱角色 的時候</td>
      <td valign="top" class="td6">我方/敵方<br>
      有/無正在詠唱的角色<br>
      的時候執行</td>
    </tr>
    <tr>
      <td class="td9">有人處於中毒狀態 時<br>
      處於中毒狀態 2人以上時<br>
      處於中毒狀態 3人以上時<br>
      處於中毒狀態 4人以上時</td>
      <td valign="top" class="td9">毒狀態角色數<br>
      對應時執行。</td>
    </tr>
    <tr>
      <td class="td6">自己 站在前排<br>
      我方 前排無人<br>
      我方 前排1人的時候<br>
      我方 前排2人以下的時候<br>
      我方 前排3人以下的時候<br>
      我方 前排4人以下的時候<br>
      我方 前排有人的時候<br>
      我方 前排2人以上的時候<br>
      我方 前排3人以上的時候<br>
      我方 前排4人以上的時候<br>
      自己 後排的時候<br>
      我方 後排的時候<br>
      我方 後排有1人的時候<br>
      我方 後排有2人以下的時候<br>
      我方 後排有3人以下的時候<br>
      我方 後排有4人以下的時候<br>
      我方 後排有1人以上的時候<br>
      我方 後排有2人以上的時候<br>
      我方 後排有3人以上的時候<br>
      我方 後排有4人以上的時候</td>
      <td valign="top" class="td6">自己的位置<br>
      <br>
      或<br>
      <br>
      我方隊列數對應時執行。<br>
      (不包含死者)</td>
    </tr>
    <tr>
      <td class="td9">敵方 前排有人時<br>
      敵方 前排無人時<br>
      敵方 後排有人時<br>
      敵方 後排無人時</td>
      <td valign="top" class="td9">敵方隊列數對應時執行。<br>
      (不包含死者)</td>
    </tr>
    <tr>
      <td class="td6">沒有召喚物時<br>
      1個召喚物的時候<br>
      1個以上召喚物的時候</td>
      <td valign="top" class="td6">根據召喚物有無執行。</td>
    </tr>
  </tbody>
</table>

</div>