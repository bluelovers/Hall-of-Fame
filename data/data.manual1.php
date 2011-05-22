<?php 
	if(!function_exists("LoadSkillData"))
		include(DATA_SKILL);
?>
<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>目錄</h4>
<UL>
<LI><A href="#mj">關於行動的多重判定</A> 
<LI><A href="#twenty">約20%的概率。</A> 
<LI><A href="#def">關於防禦力的數值。</A> 
<LI><A href="#res">弱點屬性以及無狀態異常的原因。</A> </LI></UL><!-- ---------------------------------------------------------------- --><A name=mj></A>
<H4>關於行動的多重判定<A href="#content">↑</A></H4>
<DIV style="MARGIN: 0px 20px">
<P>學習"* think over" 技能的話<BR>可以夠根據條件來進行多重判定。</P>
<P><IMG class=vcent src="./image/char/mon_079.gif">如果是戰士系的話...<IMG class=vcent src="./image/char/mon_080r.gif"></P>
<TABLE cellSpacing=5>
<TBODY>
<TR>
<TD>1</TD>
<TD><SELECT> <OPTION>必須</OPTION> <OPTION selected>自己的 HP在50%以下時</OPTION> <OPTION>自己的 SP在20%以上時</OPTION> <OPTION>自己的 SP在30%以上時</OPTION> <OPTION>初次行動時</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION selected>* think over</OPTION></SELECT> </TD>

<td><?php ShowSkillDetail(LoadSkillData(9000))?></td>
</tr>
<tr>
<TD>2</TD>
<TD><SELECT> <OPTION>必須</OPTION> <OPTION>自己的 HP在50%以下時</OPTION> <OPTION selected>自己的 SP為20%以上時</OPTION> <OPTION>自己的 SP在30%以上時</OPTION> <OPTION>初次行動時</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION selected>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(3121))?></td>
</tr>
<tr>
<TD>3</TD>
<TD><SELECT> <OPTION>必須</OPTION> <OPTION>自己的 HP為50%以下時</OPTION> <OPTION>自己的 SP為20%以上時</OPTION> <OPTION>自己的 SP為30%以上時</OPTION> <OPTION selected>初次行動時</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION selected>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(3110))?></td>
</tr>
<tr>
<TD>4</TD>
<TD><SELECT> <OPTION>必須</OPTION> <OPTION>自己的 HP為50%以下時</OPTION> <OPTION>自己的 SP為20%以上時</OPTION> <OPTION selected>自己的SP為30%以上時</OPTION> <OPTION>初次行動時</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION selected>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>

<td><?php ShowSkillDetail(LoadSkillData(1017))?></td>
</tr>
<tr>
<TD>5</TD>
<TD><SELECT> <OPTION selected>必須</OPTION> <OPTION>自己的 HP為50%以下時</OPTION> <OPTION>自己的 SP為20%以上時</OPTION> <OPTION>自己的 SP為30%以上時</OPTION> <OPTION>初次行動時</OPTION></SELECT></TD>
<TD><SELECT> <OPTION selected>Attack</OPTION> <OPTION>Bash</OPTION> <OPTION>RagingBlow</OPTION> <OPTION>Reinforce</OPTION> <OPTION>SelfRecovery</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<td><?php ShowSkillDetail(LoadSkillData(1000))?></td>
</tr>
</tbody>
</table>這種情況的話、1 和 2的 
<UL>
<LI>自己的 HP為50%以下時 
<LI>自己的 SP為20%以上時 </LI></UL>
<P>只在雙方都適合的時候使用 "SelfRecovery" 。</P><!-- ----------------------------------- -->
<P>說明流程...</P>
<TABLE cellSpacing=5>
<TBODY>
<TR>
<TD>1</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT> </TD>
<TD>↓ 不適合判斷的時候 往3</TD></TR>
<TR>
<TD>2</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION selected>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 如果適合1+2的判斷 使用Skill 1 </TD></TR>
<TR>
<TD>3</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT></TD>
<TD>↓ 不適合判斷的時候 往6</TD></TR>
<TR>
<TD>4</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION selected>* think over</OPTION></SELECT></TD>
<TD>↓ 不適合判斷的時候 往6</TD></TR>
<TR>
<TD>5</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION selected>Skill 2</OPTION> <OPTION>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 如果適合3+4+5的判定的話 使用 Skill 2 </TD></TR>
<TR>
<TD>6</TD>
<TD><SELECT> <OPTION selected>的時候</OPTION></SELECT></TD>
<TD><SELECT> <OPTION>Skill 1</OPTION> <OPTION>Skill 2</OPTION> <OPTION selected>Skill 3</OPTION> <OPTION>* think over</OPTION></SELECT></TD>
<TD>← 適合6的判定的話 使用 Skill 3 </TD></TR></TBODY></TABLE>
<P>...?</P></DIV><!-- ---------------------------------------------------------------- --><A name=twenty></A>
<H4>約20%的概率。<A href="#content">↑</A></H4>
<P>同時組合行動多重判定的70%概率,30%概率（？）<BR>0.7 * 0.3 = 0.21 = 21%</P><!-- ---------------------------------------------------------------- --><A name=def></A>
<H4>關於防禦力的數值。<A href="#content">↑</A></H4>
<P>前面是減傷的百分比後面是直接扣去的值</P><!-- ---------------------------------------------------------------- --><A name=res></A>
<H4>弱點屬性以及無狀態異常的原因。<A href="#content">↑</A></H4>
<P>戰鬥中沒有對應。</P></DIV>