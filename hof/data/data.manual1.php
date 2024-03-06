<?php 
	if(!function_exists("LoadSkillData"))
		include(DATA_SKILL);
?>
<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>目录</h4>
<UL>
<LI><A href="#mj">关于行动的多重判定</A> 
<LI><A href="#twenty">约20%的概率。</A> 
<LI><A href="#def">关于防御力的数值。</A> 
<LI><A href="#res">弱点属性以及无状态异常的原因。</A> </LI></UL><!-- ---------------------------------------------------------------- --><A name=mj></A>
<H4>关于行动的多重判定<A href="#content">↑</A></H4>
<DIV style="MARGIN: 0px 20px">
<P>学习"* think over" 技能的话<BR>可以够根据条件来进行多重判定。</P>
<P><IMG class=vcent src="./image/char/mon_079.gif">如果是战士系的话...<IMG class=vcent src="./image/char/mon_080r.gif"></P>
<TABLE cellSpacing=5>
<TBODY>
<TR>
<TD>1</TD>
<TD><SELECT> <OPTION>必须</OPTION> <OPTION selected>自己的 HP在50%以下时</OPTION> <OPTION>自己的 SP在20%以上时</OPTION> <OPTION>自己的 SP在30%以上时</OPTION> <OPTION>初次行动时</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION selected>* think over</OPTION></SELECT> </TD>

<td><?php ShowSkillDetail(LoadSkillData(9000))?></td>
</tr>
<tr>
<TD>2</TD>
<TD><SELECT> <OPTION>必须</OPTION> <OPTION>自己的 HP在50%以下时</OPTION> <OPTION selected>自己的 SP为20%以上时</OPTION> <OPTION>自己的 SP在30%以上时</OPTION> <OPTION>初次行动时</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION selected>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(3121))?></td>
</tr>
<tr>
<TD>3</TD>
<TD><SELECT> <OPTION>必须</OPTION> <OPTION>自己的 HP为50%以下时</OPTION> <OPTION>自己的 SP为20%以上时</OPTION> <OPTION>自己的 SP为30%以上时</OPTION> <OPTION selected>初次行动时</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION selected>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(3110))?></td>
</tr>
<tr>
<TD>4</TD>
<TD><SELECT> <OPTION>必须</OPTION> <OPTION>自己的 HP为50%以下时</OPTION> <OPTION>自己的 SP为20%以上时</OPTION> <OPTION selected>自己的SP为30%以上时</OPTION> <OPTION>初次行动时</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION selected>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>

<td><?php ShowSkillDetail(LoadSkillData(1017))?></td>
</tr>
<tr>
<TD>5</TD>
<TD><SELECT> <OPTION selected>必须</OPTION> <OPTION>自己的 HP为50%以下时</OPTION> <OPTION>自己的 SP为20%以上时</OPTION> <OPTION>自己的 SP为30%以上时</OPTION> <OPTION>初次行动时</OPTION></SELECT></TD>
<TD><SELECT> <OPTION selected>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(1000))?></td>
</tr>
</tbody>
</table>这种情况的话、1 和 2的 
<UL>
<LI>自己的 HP为50%以下时 
<LI>自己的 SP为20%以上时 </LI></UL>
<P>只在双方都适合的时候使用 "SelfRecovery" 。</P><!-- ----------------------------------- -->
<P>说明流程...</P>
<TABLE cellSpacing=5>
<TBODY>
<TR>
<TD>1</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT> </TD>
<TD>↓ 不适合判断的时候 往3</TD></TR>
<TR>
<TD>2</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION selected>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 如果适合1+2的判断 使用Skill 1 </TD></TR>
<TR>
<TD>3</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT></TD>
<TD>↓ 不适合判断的时候 往6</TD></TR>
<TR>
<TD>4</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT></TD>
<TD>↓ 不适合判断的时候 往6</TD></TR>
<TR>
<TD>5</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION selected>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 如果适合3+4+5的判定的话 使用 Skill 2 </TD></TR>
<TR>
<TD>6</TD>
<TD><SELECT> <OPTION selected>的时候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION selected>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 适合6的判定的话 使用 Skill 3 </TD></TR></TBODY></TABLE>
<P>...?</P></DIV><!-- ---------------------------------------------------------------- --><A name=twenty></A>
<H4>约20%的概率。<A href="#content">↑</A></H4>
<P>同时组合行动多重判定的70%概率,30%概率（？）<BR>0.7 * 0.3 = 0.21 = 21%</P><!-- ---------------------------------------------------------------- --><A name=def></A>
<H4>关于防御力的数值。<A href="#content">↑</A></H4>
<P>前面是减伤的百分比后面是直接扣去的值</P><!-- ---------------------------------------------------------------- --><A name=res></A>
<H4>弱点属性以及无状态异常的原因。<A href="#content">↑</A></H4>
<P>战斗中没有对应。</P></DIV>