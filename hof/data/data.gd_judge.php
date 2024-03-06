<?php
include_once(DATA_JUDGE_SETUP);
?>
<div style="margin:0 15px">
<h4>判定(judge)</h4>
<?php
// 办枉を山绩する。
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
      <td class="td6">一定会被执行的</td>
    </tr>
    <tr>
      <td class="td9">往下一次判断</td>
      <td class="td9">“必定”将跳过</td>
    </tr>
    <tr>
      <td class="td6">自己的 HP10%以上的时候<br>
      自己的 HP20%以上的时候<br>
      自己的 HP30%以上的时候<br>
      自己的 HP40%以上的时候<br>
      自己的 HP50%以上的时候<br>
      自己的 HP60%以上的时候<br>
      自己的 HP70%以上的时候<br>
      自己的 HP80%以上的时候<br>
      自己的 HP90%以上的时候<br>
      自己的 HP10%以下的时候<br>
      自己的 HP20%以下的时候<br>
      自己的 HP30%以下的时候<br>
      自己的 HP40%以下的时候<br>
      自己的 HP50%以下的时候<br>
      自己的 HP60%以下的时候<br>
      自己的 HP70%以下的时候<br>
      自己的 HP80%以下的时候<br>
      自己的 HP90%以下的时候</td>
      <td valign="top" class="td6">HP**%以上/以下<br>
      的场合将被执行。</td>
    </tr>
    <tr>
      <td class="td9">我方有 HP10%以下角色的时候<br>
      我方有 HP30%以下角色的时候<br>
      我方有 HP50%以下角色的时候<br>
      我方有 HP70%以下角色的时候<br>
      我方有 HP90%以下角色的时候</td>
      <td valign="top" class="td9">HP**%以下的时候<br>
      且不只一人的场合执行</td>
    </tr>
    <tr>
      <td class="td6">我方平均 HP 10%以上的时候<br>
      我方平均 HP 30%以上的时候<br>
      我方平均 HP 50%以上的时候<br>
      我方平均 HP 70%以上的时候<br>
      我方平均 HP 90%以上的时候<br>
      我方平均 HP 10%以下的时候<br>
      我方平均 HP 30%以下的时候<br>
      我方平均 HP 50%以下的时候<br>
      我方平均 HP 70%以下的时候<br>
      我方平均 HP 90%以下的时候</td>
      <td valign="top" class="td6">平均HP**%以下/以上<br>
      时执行。</td>
    </tr>
    <tr>
      <td class="td9">自己的 SPが10%以上的时候<br>
      自己的 SPが20%以上的时候<br>
      自己的 SPが30%以上的时候<br>
      自己的 SPが40%以上的时候<br>
      自己的 SPが50%以上的时候<br>
      自己的 SPが60%以上的时候<br>
      自己的 SPが70%以上的时候<br>
      自己的 SPが80%以上的时候<br>
      自己的 SPが90%以上的时候<br>
      自己的 SPが10%以下的时候<br>
      自己的 SPが20%以下的时候<br>
      自己的 SPが30%以下的时候<br>
      自己的 SPが40%以下的时候<br>
      自己的 SPが50%以下的时候<br>
      自己的 SPが60%以下的时候<br>
      自己的 SPが70%以下的时候<br>
      自己的 SPが80%以下的时候<br>
      自己的 SPが90%以下的时候</td>
      <td valign="top" class="td9">SPが**%以上/以下<br>
      的场合执行。</td>
    </tr>
    <tr>
      <td class="td6">我方 SPが10%以下的时候<br>
      我方有 SPが30%以下角色的时候<br>
      我方有 SPが50%以下角色的时候<br>
      我方有 SPが70%以下角色的时候<br>
      我方有 SPが90%以下角色的时候</td>
      <td valign="top" class="td6">SP**%以下的时候<br>
      一人以上的场合执行。</td>
    </tr>
    <tr>
      <td class="td9">我方平均 SPが10%以上的时候<br>
      我方平均 SPが30%以上的时候<br>
      我方平均 SPが50%以上的时候<br>
      我方平均 SPが70%以上的时候<br>
      我方平均 SPが90%以上的时候<br>
      我方平均 SPが10%以下的时候<br>
      我方平均 SPが30%以下的时候<br>
      我方平均 SPが50%以下的时候<br>
      我方平均 SPが70%以下的时候<br>
      我方平均 SPが90%以下的时候</td>
      <td valign="top" class="td9">平均SPが**%以上/以下<br>
      的场合执行。</td>
    </tr>
    <tr>
      <td class="td6">1人以上被打倒的时候<br>
      2人以上被打倒的时候<br>
      3人以上被打倒的时候<br>
      只有自己一个人存活的时候<br>
      2人以上存活的时候<br>
      3人以上存活的时候<br>
      4人以上存活的时候</td>
      <td valign="top" class="td6">我方的<br>
      生存数<br>
      死亡数<br>
      相应时执行。</td>
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
      <td class="td6">初始行动时<br>
      第2回合行动时<br>
      第3回合行动时<br>
      第4回合行动时<br>
      第5回合行动时</td>
      <td valign="top" class="td6">指定回合<br>
      时执行。</td>
    </tr>
    <tr>
      <td class="td9">一次“必定”<br>
      二次“必定”<br>
      三次“必定”</td>
      <td valign="top" class="td9">“必定”<br>
      会使用的次数。</td>
    </tr>
    <tr>
      <td class="td6">我方 有咏唱角色 的时候<br>
      我方 没有咏唱角色 的时候<br>
      敌方 有咏唱角色 的时候<br>
      敌方 没有咏唱角色 的时候</td>
      <td valign="top" class="td6">我方/敌方<br>
      有/无正在咏唱的角色<br>
      的时候执行</td>
    </tr>
    <tr>
      <td class="td9">有人处于中毒状态 时<br>
      处于中毒状态 2人以上时<br>
      处于中毒状态 3人以上时<br>
      处于中毒状态 4人以上时</td>
      <td valign="top" class="td9">毒状态角色数<br>
      对应时执行。</td>
    </tr>
    <tr>
      <td class="td6">自己 站在前排<br>
      我方 前排无人<br>
      我方 前排1人的时候<br>
      我方 前排2人以下的时候<br>
      我方 前排3人以下的时候<br>
      我方 前排4人以下的时候<br>
      我方 前排有人的时候<br>
      我方 前排2人以上的时候<br>
      我方 前排3人以上的时候<br>
      我方 前排4人以上的时候<br>
      自己 后排的时候<br>
      我方 后排的时候<br>
      我方 后排有1人的时候<br>
      我方 后排有2人以下的时候<br>
      我方 后排有3人以下的时候<br>
      我方 后排有4人以下的时候<br>
      我方 后排有1人以上的时候<br>
      我方 后排有2人以上的时候<br>
      我方 后排有3人以上的时候<br>
      我方 后排有4人以上的时候</td>
      <td valign="top" class="td6">自己的位置<br>
      <br>
      或<br>
      <br>
      我方队列数对应时执行。<br>
      (不包含死者)</td>
    </tr>
    <tr>
      <td class="td9">敌方 前排有人时<br>
      敌方 前排无人时<br>
      敌方 后排有人时<br>
      敌方 后排无人时</td>
      <td valign="top" class="td9">敌方队列数对应时执行。<br>
      (不包含死者)</td>
    </tr>
    <tr>
      <td class="td6">没有召唤物时<br>
      1个召唤物的时候<br>
      1个以上召唤物的时候</td>
      <td valign="top" class="td6">根据召唤物有无执行。</td>
    </tr>
  </tbody>
</table>

</div>