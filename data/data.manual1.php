<?
	if(!function_exists("LoadSkillData"))
		include(DATA_SKILL);
?>
<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>目次</h4>
<ul>
<li><a href="#mj">行動の多重判定について</a></li>
<li><a href="#twenty">約20%の確率を作る。</a></li>
<li><a href="#def">防御力の数値について。</a></li>
<li><a href="#res">弱点属性と状態異常が少ない理由。</a></li>
</ul>
<!-- ---------------------------------------------------------------- -->
<a name="mj"></a>
<h4>行動の多重判定について<a href="#content">↑</a></h4>
<div style="margin:0 20px">
<p>"* think over" というスキルを習得すると<br />
条件による多重判定ができるようになります。</p>
<p><img src="./image/char/mon_079.gif" class="vcent">戦士系の場合だと...<img src="./image/char/mon_080r.gif" class="vcent"></p>
<table cellspacing="5">
<tbody>
<tr>
<td>1</td>
<td>
<select>
<option>必ず</option>
<option selected>自分の HPが50%以下 の時 </option>
<option>自分の SPが20%以上 の時</option>
<option>自分の SPが30%以上 の時</option>
<option>初回行動時</option>
</select>
</td>
<td>
<select>
<option>Attack</option>
<option>Bash</option>
<option>RagingBlow</option>
<option>Reinforce</option>
<option>SelfRecovery</option>
<option selected>* think over</option>
</select>
</td>
<td><?ShowSkillDetail(LoadSkillData(9000))?></td>
</tr>
<tr>
<td>2</td>
<td><select>
<option>必ず</option>
<option>自分の HPが50%以下 の時 </option>
<option selected>自分の SPが20%以上 の時</option>
<option>自分の SPが30%以上 の時</option>
<option>初回行動時</option>
</select></td>
<td><select>
<option>Attack</option>
<option>Bash</option>
<option>RagingBlow</option>
<option>Reinforce</option>
<option selected>SelfRecovery</option>
<option>* think over</option>
</select></td>
<td><?ShowSkillDetail(LoadSkillData(3121))?></td>
</tr>
<tr>
<td>3</td>
<td><select>
<option>必ず</option>
<option>自分の HPが50%以下 の時 </option>
<option>自分の SPが20%以上 の時</option>
<option>自分の SPが30%以上 の時</option>
<option selected>初回行動時</option>
</select></td>
<td><select>
<option>Attack</option>
<option>Bash</option>
<option>RagingBlow</option>
<option selected>Reinforce</option>
<option>SelfRecovery</option>
<option>* think over</option>
</select></td>
<td><?ShowSkillDetail(LoadSkillData(3110))?></td>
</tr>
<tr>
<td>4</td>
<td><select>
<option>必ず</option>
<option>自分の HPが50%以下 の時 </option>
<option>自分の SPが20%以上 の時</option>
<option selected>自分の SPが30%以上 の時</option>
<option>初回行動時</option>
</select></td>
<td><select>
<option>Attack</option>
<option>Bash</option>
<option selected>RagingBlow</option>
<option>Reinforce</option>
<option>SelfRecovery</option>
<option>* think over</option>
</select></td>
<td><?ShowSkillDetail(LoadSkillData(1017))?></td>
</tr>
<tr>
<td>5</td>
<td><select>
<option selected>必ず</option>
<option>自分の HPが50%以下 の時 </option>
<option>自分の SPが20%以上 の時</option>
<option>自分の SPが30%以上 の時</option>
<option>初回行動時</option>
</select></td>
<td><select>
<option selected>Attack</option>
<option>Bash</option>
<option>RagingBlow</option>
<option>Reinforce</option>
<option>SelfRecovery</option>
<option>* think over</option>
</select></td>
<td><?ShowSkillDetail(LoadSkillData(1000))?></td>
</tr>
</tbody>
</table>
この場合、1 と 2 の
<ul>
<li>自分の HPが50%以下の 時</li>
<li>自分の SPが20%以上の 時</li>
</ul>
<p>という判定が両方当てはまった場合のみ "SelfRecovery" を使用します。</p>
<!-- ----------------------------------- -->
<p>流れを説明すると...</p>
<table cellspacing="5">
<tbody>
<tr>
<td>1</td>
<td>
<select>
<option selected>～ の時</option>
</select>
</td>
<td>
<select>
<option>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option selected>* think over</option>
</select>
</td>
<td>↓ 判定に当てはまらない場合 3へ</td>
</tr>
<tr>
<td>2</td>
<td><select>
<option selected>～ の時</option>
</select></td>
<td><select>
<option selected>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option>* think over</option>
</select></td>
<td>← 1+2 の判定に当てはまれば Skill 1 を使用</td>
</tr>
<tr>
<td>3</td>
<td><select>
<option selected>～ の時</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option selected>* think over</option>
</select></td>
<td>↓ 判定に当てはまらない場合 6へ</td>
</tr>
<tr>
<td>4</td>
<td><select>
<option selected>～ の時</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option selected>* think over</option>
</select></td>
<td>↓ 判定に当てはまらない場合 6へ</td>
</tr>
<tr>
<td>5</td>
<td><select>
<option selected>～ の時</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option selected>Skill 2</option>
<option>Skill 3</option>
<option>* think over</option>
</select></td>
<td>← 3+4+5 の判定に当てはまれば Skill 2 を使用</td>
</tr>
<tr>
<td>6</td>
<td><select>
<option selected>～ の時</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option selected>Skill 3</option>
<option>* think over</option>
</select></td>
<td>← 6 の判定に当てはまれば Skill 3 を使用</td>
</tr>
</tbody>
</table>
<p>です...?</p>
</div>
<!-- ---------------------------------------------------------------- -->
<a name="twenty"></a>
<h4>約20%の確率を作る。<a href="#content">↑</a></h4>
<p>行動の多重判定の70%の確率,30%の確率を同時に組み合わせてください<br />
0.7 * 0.3 = 0.21 = 21%</p>
<!-- ---------------------------------------------------------------- -->
<a name="def"></a>
<h4>防御力の数値について。<a href="#content">↑</a></h4>
<p>前は割る後ろは引く</p>
<!-- ---------------------------------------------------------------- -->
<a name="res"></a>
<h4>弱点属性と状態異常が無い理由。<a href="#content">↑</a></h4>
<p>戦闘中に対応できないから。</p>
</div>