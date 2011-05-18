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
      <td class="td6">必ず</td>
      <td class="td6">必ず実行される</td>
    </tr>
    <tr>
      <td class="td9">次の判断へ</td>
      <td class="td9">必ず(パス)飛ばされる</td>
    </tr>
    <tr>
      <td class="td6">自分の HPが10%以上 の時<br>
      自分の HPが20%以上 の時<br>
      自分の HPが30%以上 の時<br>
      自分の HPが40%以上 の時<br>
      自分の HPが50%以上 の時<br>
      自分の HPが60%以上 の時<br>
      自分の HPが70%以上 の時<br>
      自分の HPが80%以上 の時<br>
      自分の HPが90%以上 の時<br>
      自分の HPが10%以下 の時<br>
      自分の HPが20%以下 の時<br>
      自分の HPが30%以下 の時<br>
      自分の HPが40%以下 の時<br>
      自分の HPが50%以下 の時<br>
      自分の HPが60%以下 の時<br>
      自分の HPが70%以下 の時<br>
      自分の HPが80%以下 の時<br>
      自分の HPが90%以下 の時</td>
      <td valign="top" class="td6">HPが**%以上/以下<br>
      だった場合実行される。</td>
    </tr>
    <tr>
      <td class="td9">味方に HPが10%以下のキャラ がいる時<br>
      味方に HPが30%以下のキャラ がいる時<br>
      味方に HPが50%以下のキャラ がいる時<br>
      味方に HPが70%以下のキャラ がいる時<br>
      味方に HPが90%以下のキャラ がいる時</td>
      <td valign="top" class="td9">HPが**%以下のキャラが<br>
      一人以上居た場合実行。</td>
    </tr>
    <tr>
      <td class="td6">味方の 平均HPが 10%以上の時<br>
      味方の 平均HPが 30%以上の時<br>
      味方の 平均HPが 50%以上の時<br>
      味方の 平均HPが 70%以上の時<br>
      味方の 平均HPが 90%以上の時<br>
      味方の 平均HPが 10%以下の時<br>
      味方の 平均HPが 30%以下の時<br>
      味方の 平均HPが 50%以下の時<br>
      味方の 平均HPが 70%以下の時<br>
      味方の 平均HPが 90%以下の時</td>
      <td valign="top" class="td6">平均HPが**%以上/以下<br>
      なら実行。</td>
    </tr>
    <tr>
      <td class="td9">自分の SPが10%以上 の時<br>
      自分の SPが20%以上 の時<br>
      自分の SPが30%以上 の時<br>
      自分の SPが40%以上 の時<br>
      自分の SPが50%以上 の時<br>
      自分の SPが60%以上 の時<br>
      自分の SPが70%以上 の時<br>
      自分の SPが80%以上 の時<br>
      自分の SPが90%以上 の時<br>
      自分の SPが10%以下 の時<br>
      自分の SPが20%以下 の時<br>
      自分の SPが30%以下 の時<br>
      自分の SPが40%以下 の時<br>
      自分の SPが50%以下 の時<br>
      自分の SPが60%以下 の時<br>
      自分の SPが70%以下 の時<br>
      自分の SPが80%以下 の時<br>
      自分の SPが90%以下 の時</td>
      <td valign="top" class="td9">SPが**%以上/以下<br>
      だった場合実行される。</td>
    </tr>
    <tr>
      <td class="td6">味方に SPが10%以下のキャラ がいる時<br>
      味方に SPが30%以下のキャラ がいる時<br>
      味方に SPが50%以下のキャラ がいる時<br>
      味方に SPが70%以下のキャラ がいる時<br>
      味方に SPが90%以下のキャラ がいる時</td>
      <td valign="top" class="td6">SPが**%以下のキャラが<br>
      一人以上居た場合実行。</td>
    </tr>
    <tr>
      <td class="td9">味方の 平均SPが10%以上 の時<br>
      味方の 平均SPが30%以上 の時<br>
      味方の 平均SPが50%以上 の時<br>
      味方の 平均SPが70%以上 の時<br>
      味方の 平均SPが90%以上 の時<br>
      味方の 平均SPが10%以下 の時<br>
      味方の 平均SPが30%以下 の時<br>
      味方の 平均SPが50%以下 の時<br>
      味方の 平均SPが70%以下 の時<br>
      味方の 平均SPが90%以下 の時</td>
      <td valign="top" class="td9">平均SPが**%以上/以下<br>
      なら実行。</td>
    </tr>
    <tr>
      <td class="td6">1人以上の 味方が 倒れている時<br>
      2人以上の 味方が 倒れている時<br>
      3人以上の 味方が 倒れている時<br>
      自分一人だけ 生きている時<br>
      2人以上が 生きている時<br>
      3人以上が 生きている時<br>
      4人以上が 生きている時</td>
      <td valign="top" class="td6">味方の<br>
      生存数<br>
      死亡数<br>
      に応じて実行する。</td>
    </tr>
    <tr>
      <td class="td9">10%の確率で<br>
      30%の確率で<br>
      50%の確率で<br>
      70%の確率で<br>
      90%の確率で</td>
      <td valign="top" class="td9">確率。</td>
    </tr>
    <tr>
      <td class="td6">初回行動時<br>
      2回目行動時<br>
      3回目行動時<br>
      4回目行動時<br>
      5回目行動時</td>
      <td valign="top" class="td6">指定された行動回数に<br>
      実行する。</td>
    </tr>
    <tr>
      <td class="td9">一度だけ必ず<br>
      二度だけ必ず<br>
      三度だけ必ず</td>
      <td valign="top" class="td9">回数を制限して<br>
      必ず実行する。</td>
    </tr>
    <tr>
      <td class="td6">味方に チャージ(詠唱)中 のキャラがいる時<br>
      味方に チャージ(詠唱)中 のキャラがいない時<br>
      敵に チャージ(詠唱)中 のキャラがいる時<br>
      敵に チャージ(詠唱)中 のキャラがいない時</td>
      <td valign="top" class="td6">味方/敵<br>
      の詠唱キャラの有無に<br>
      応じて実行</td>
    </tr>
    <tr>
      <td class="td9">毒状態のキャラが いる時<br>
      毒状態のキャラが 2人以上いる時<br>
      毒状態のキャラが 3人以上いる時<br>
      毒状態のキャラが 4人以上いる時</td>
      <td valign="top" class="td9">毒状態のキャラの数に<br>
      応じて実行。</td>
    </tr>
    <tr>
      <td class="td6">自分が 前列の時<br>
      味方に 前列がいない時<br>
      味方の 前列が1人の時<br>
      味方の 前列が2人以下の時<br>
      味方の 前列が3人以下の時<br>
      味方の 前列が4人以下の時<br>
      味方に 前列が居る時<br>
      味方の 前列が2人以上の時<br>
      味方の 前列が3人以上の時<br>
      味方の 前列が4人以上の時<br>
      自分が 後列の時<br>
      味方に 後列がいない時<br>
      味方の 後列が1人の時<br>
      味方の 後列が2人以下の時<br>
      味方の 後列が3人以下の時<br>
      味方の 後列が4人以下の時<br>
      味方の 後列が1人以上の時<br>
      味方の 後列が2人以上の時<br>
      味方の 後列が3人以上の時<br>
      味方の 後列が4人以上の時</td>
      <td valign="top" class="td6">自分の位置<br>
      <br>
      または<br>
      <br>
      味方の隊列の数に応じて実行する。<br>
      (死亡者は含まれない)</td>
    </tr>
    <tr>
      <td class="td9">敵に 前列がいる時<br>
      敵に 前列がいない時<br>
      敵に 後列がいる時<br>
      敵に 後列がいない時</td>
      <td valign="top" class="td9">敵の隊列の状況に応じて実行。<br>
      (死亡者は含まれない)</td>
    </tr>
    <tr>
      <td class="td6">召喚キャラが居ない時<br>
      召喚キャラが1匹のみ居る時<br>
      召喚キャラが1匹以上居る時</td>
      <td valign="top" class="td6">召喚キャラの有無によって実行。</td>
    </tr>
  </tbody>
</table>

</div>