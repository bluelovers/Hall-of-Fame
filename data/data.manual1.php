<?
	if(!function_exists("LoadSkillData"))
		include(DATA_SKILL);
?>
<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>�ܼ�</h4>
<ul>
<li><a href="#mj">��ư��¿��Ƚ��ˤĤ���</a></li>
<li><a href="#twenty">��20%�γ�Ψ���롣</a></li>
<li><a href="#def">�ɸ��Ϥο��ͤˤĤ��ơ�</a></li>
<li><a href="#res">����°���Ⱦ��ְ۾郎���ʤ���ͳ��</a></li>
</ul>
<!-- ---------------------------------------------------------------- -->
<a name="mj"></a>
<h4>��ư��¿��Ƚ��ˤĤ���<a href="#content">��</a></h4>
<div style="margin:0 20px">
<p>"* think over" �Ȥ�����������������<br />
���ˤ��¿��Ƚ�꤬�Ǥ���褦�ˤʤ�ޤ���</p>
<p><img src="./image/char/mon_079.gif" class="vcent">��ηϤξ�����...<img src="./image/char/mon_080r.gif" class="vcent"></p>
<table cellspacing="5">
<tbody>
<tr>
<td>1</td>
<td>
<select>
<option>ɬ��</option>
<option selected>��ʬ�� HP��50%�ʲ� �λ� </option>
<option>��ʬ�� SP��20%�ʾ� �λ�</option>
<option>��ʬ�� SP��30%�ʾ� �λ�</option>
<option>����ư��</option>
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
<option>ɬ��</option>
<option>��ʬ�� HP��50%�ʲ� �λ� </option>
<option selected>��ʬ�� SP��20%�ʾ� �λ�</option>
<option>��ʬ�� SP��30%�ʾ� �λ�</option>
<option>����ư��</option>
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
<option>ɬ��</option>
<option>��ʬ�� HP��50%�ʲ� �λ� </option>
<option>��ʬ�� SP��20%�ʾ� �λ�</option>
<option>��ʬ�� SP��30%�ʾ� �λ�</option>
<option selected>����ư��</option>
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
<option>ɬ��</option>
<option>��ʬ�� HP��50%�ʲ� �λ� </option>
<option>��ʬ�� SP��20%�ʾ� �λ�</option>
<option selected>��ʬ�� SP��30%�ʾ� �λ�</option>
<option>����ư��</option>
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
<option selected>ɬ��</option>
<option>��ʬ�� HP��50%�ʲ� �λ� </option>
<option>��ʬ�� SP��20%�ʾ� �λ�</option>
<option>��ʬ�� SP��30%�ʾ� �λ�</option>
<option>����ư��</option>
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
���ξ�硢1 �� 2 ��
<ul>
<li>��ʬ�� HP��50%�ʲ��� ��</li>
<li>��ʬ�� SP��20%�ʾ�� ��</li>
</ul>
<p>�Ȥ���Ƚ�꤬ξ�����ƤϤޤä����Τ� "SelfRecovery" ����Ѥ��ޤ���</p>
<!-- ----------------------------------- -->
<p>ή������������...</p>
<table cellspacing="5">
<tbody>
<tr>
<td>1</td>
<td>
<select>
<option selected>�� �λ�</option>
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
<td>�� Ƚ������ƤϤޤ�ʤ���� 3��</td>
</tr>
<tr>
<td>2</td>
<td><select>
<option selected>�� �λ�</option>
</select></td>
<td><select>
<option selected>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option>* think over</option>
</select></td>
<td>�� 1+2 ��Ƚ������ƤϤޤ�� Skill 1 �����</td>
</tr>
<tr>
<td>3</td>
<td><select>
<option selected>�� �λ�</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option selected>* think over</option>
</select></td>
<td>�� Ƚ������ƤϤޤ�ʤ���� 6��</td>
</tr>
<tr>
<td>4</td>
<td><select>
<option selected>�� �λ�</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option>Skill 3</option>
<option selected>* think over</option>
</select></td>
<td>�� Ƚ������ƤϤޤ�ʤ���� 6��</td>
</tr>
<tr>
<td>5</td>
<td><select>
<option selected>�� �λ�</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option selected>Skill 2</option>
<option>Skill 3</option>
<option>* think over</option>
</select></td>
<td>�� 3+4+5 ��Ƚ������ƤϤޤ�� Skill 2 �����</td>
</tr>
<tr>
<td>6</td>
<td><select>
<option selected>�� �λ�</option>
</select></td>
<td><select>
<option>Skill 1</option>
<option>Skill 2</option>
<option selected>Skill 3</option>
<option>* think over</option>
</select></td>
<td>�� 6 ��Ƚ������ƤϤޤ�� Skill 3 �����</td>
</tr>
</tbody>
</table>
<p>�Ǥ�...?</p>
</div>
<!-- ---------------------------------------------------------------- -->
<a name="twenty"></a>
<h4>��20%�γ�Ψ���롣<a href="#content">��</a></h4>
<p>��ư��¿��Ƚ���70%�γ�Ψ,30%�γ�Ψ��Ʊ�����Ȥ߹�碌�Ƥ�������<br />
0.7 * 0.3 = 0.21 = 21%</p>
<!-- ---------------------------------------------------------------- -->
<a name="def"></a>
<h4>�ɸ��Ϥο��ͤˤĤ��ơ�<a href="#content">��</a></h4>
<p>���ϳ����ϰ���</p>
<!-- ---------------------------------------------------------------- -->
<a name="res"></a>
<h4>����°���Ⱦ��ְ۾郎̵����ͳ��<a href="#content">��</a></h4>
<p>��Ʈ����б��Ǥ��ʤ����顣</p>
</div>