<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>目录</h4>
<ul>
<li><a href="#rule">规则</a></li>
<li><a href="#menu">菜单</a></li>
<li><a href="#btl">战斗流程</a></li>
<li><a href="#char">人物设定</a></li>
<li><a href="#charstat">人物的基础能力值</a></li>
<li><a href="#statup">能力值上升</a></li>
<li><a href="#jdg">任务在战斗中的命令</a></li>
<li><a href="#posi">人物的位置关系及后卫保护</a></li>
<li><a href="#equip">人物装备</a></li>
<li><a href="#skill">人物技能</a></li>
<li><a href="#elem">攻击属性</a></li>
<li><a href="#state">人物状态</a></li>
<li><a href="#jobchange">转职(职业转换)</a></li>
<li><a href="#sacrier">Sacrier（狂战士）的攻击方式</a></li>
<li><a href="#ranking">排行</a></li>
<li><a href="?manual2">高级指南</a></li>
<li><a href="#cr">使用的图像</a></li>
</ul>
<a name="rule"></a>
<h4>规则(Rule) <a href="#content"></a></h4>
<p style="margin-left:50px">本游戏目的、<br />
就是爬到排行榜的第一名位置。<br />
并持续维持住排名。<br />
并没有什么冒险的要素。</p>
<p style="margin-left:50px">可以自己组建1-5人的队伍进行战斗、<br />
让其登上排行榜。</p>
<p style="margin-left:50px">为了能登上榜首、<br />
要持续的与敌人（怪物）进行战斗锻炼<br />
从敌人（怪物）手中夺取更强力的道具<br />
这就是这游戏有趣的地方。<br />
排名战的敌人将是其他的玩家。</p>
<p style="margin-left:50px">各个人物依照技能的使用条件等、进行详细的设定。<br />
要调整出无瑕的战术配置、不是一件简单的事情。</p>
<p class="bold u" style="margin-left:50px">排行榜功能还不是很完善。</p>
<!-- ---------------------------------------------------------------- -->
<a name="menu"></a>
<h4>菜单(Menu) <a href="#content"></a></h4>
<img src="./image/manual/003.gif">
<p><span class="u bold">登录后会显示如下菜单</span>
<ul>
<li><span class="bold">Top</span> - 所拥有的人物一览</li>
<li><span class="bold">战斗(Hunt)</span> - 和怪物战斗</li>
<li><span class="bold">道具(Item)</span> - 所持道具一览</li>
<li><span class="bold">商店(Shop)</span> - 买卖道具和消费时间打工。</li>
<li><span class="bold">排名(Rank)</span> - 排行榜。</li>
<li><span class="bold">雇佣(Recruit)</span> - 雇佣新人物</li>
<li><span class="bold">设定(Setting)</span> - 各种设定。注销。销号。</li>
<li><span class="bold">记录(Log)</span> - 查看过去战斗的记录。</li>
</ul></p>
<p>另外、<span class="u bold">菜单的下面</span>
<ul>
<li /><span class="bold">队伍名称</span> - 名称。
<li /><span class="bold">资金(Funds)</span> - 所拥有的金钱。
<li /><span class="bold">时间(Time)</span> - 时间。战斗时减少。会随时间流逝逐渐恢复。
</ul></p>
<a name="btl"></a>
<!-- ---------------------------------------------------------------- -->
<h4>战斗流程 <a href="#content"></a></h4>
<div style="margin-left:50px">
<p>战斗过程是由电脑处理。<br />
战斗中晚间不能下达指令。</p>
1. 依照人物的能力顺序行动。
<div class="bold" style="margin-left:30px">↓↓↓</div>
2. 人物会根据事先设定来行动。
<div class="bold" style="margin-left:30px">↓↓↓</div>
3. ( 重复1和2)
<div class="bold" style="margin-left:30px">↓↓↓</div>
4. 满足以下条件，则战斗结束。
<p><span class="bold u">结束条件</span><br />
1.我方或敌方全员战斗不能。<br />
2.累计战斗 <?=BATTLE_MAX_TURNS?>回合则被判为平局。</p>
</div>
<!-- ---------------------------------------------------------------- -->
<a name="char"></a>
<h4>人物设定<a href="#content"></a></h4>
<p style="margin-left:50px">登录后，点击top页面中的人物形象<br />
将显示人物的相关页面。<br />
<img src="./image/manual/001.gif"><br />
详细说明如下。</p>
<!-- ---------------------------------------------------------------- -->
<a name="charstat"></a>
<h4>人物基本能力值 <a href="#content"></a></h4>
<p style="margin-left:50px">
<img src="./image/manual/002.gif">
<ul>
<li /><span class="bold">Exp</span> :升级所需经验
<li /><span class="bold">MaxHP</span> : 最大体力。为0则被击倒
<li /><span class="bold">MaxSP</span> : 使用技能所消耗的值
<li /><span class="bold">Str</span> : 力量。影响HP和物理攻击力。
<li /><span class="bold">Int</span> : 智力。影响SP和魔法攻击力。
<li /><span class="bold">Dex</span> : 敏捷。可装更多的装备（？），猎人系攻击力上升，强化召唤物。
<li /><span class="bold">Spd</span> : 速度。越高则行动次数也越多，攻击间隔将变短。
<li /><span class="bold">Luk</span> : 运强化召唤物。
</ul>
</p>
<!-- ---------------------------------------------------------------- -->
<a name="statup"></a>
<h4>能力值上升g<a href="#content"></a></h4>
<p style="margin-left:50px">角色持续战斗获得经验值<span class="u">升级</span>时、<br />
会得到几个点数，可以自由用这些来强化各项能力值。</p>
<!-- ---------------------------------------------------------------- -->
<a name="jdg"></a>
<h4>人物在战斗中的命令 <a href="#content"></a></h4>
<p style="margin-left:50px">基本上人物战斗是都是依据玩家设定的动作逐步行动的。</p>
<div style="margin-left:50px">
<table cellspacing="5"><tbody>
<tr><td class="bold">No</td><td style="text-align:center" class="bold">判定</td>
<td style="text-align:center" class="bold">使用技能</td></tr>
<tr><TD>1</TD>
<TD><SELECT> <OPTION>必定</OPTION> <OPTION>自己的HP为50%以上时</OPTION> <OPTION>自己的HP为50%以下时</OPTION> <OPTION>同伴中有 HP为50%以下的人物时</OPTION> <OPTION>同伴的平均HP为 50%以上时</OPTION> <OPTION>同伴的平均HP为 50%以下时</OPTION> <OPTION>自己的SP为50%以上时</OPTION> <OPTION>自己的 SP为50%以下时</OPTION> <OPTION>以50%的概率</OPTION> <OPTION selected>初次行动时</OPTION> <OPTION>第二次行动时</OPTION> <OPTION>第三次行动时</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少两次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>技能1</OPTION> <OPTION>技能2</OPTION> <OPTION selected>技能3</OPTION> <OPTION>恢复魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR>
<TR>
<TD>2</TD>
<TD><SELECT> <OPTION>必定</OPTION> <OPTION>自己的HP为50%以上时</OPTION> <OPTION>自己的 HP为50%以下时</OPTION> <OPTION>同伴里有 HP为50%以下的人物时</OPTION> <OPTION>同伴的平均HP为 50%以上时</OPTION> <OPTION>同伴的平均HP为 50%以下时</OPTION> <OPTION selected>自己的SP为50%以上时</OPTION> <OPTION>自己的 SP为50%以下时</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>初次行动时</OPTION> <OPTION>第二次行动时</OPTION> <OPTION>第三次行动时</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少两次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>技能1</OPTION> <OPTION selected>技能2</OPTION> <OPTION>技能3</OPTION> <OPTION>恢复魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR>
<TR>
<TD>3</TD>
<TD><SELECT> <OPTION selected>必须</OPTION> <OPTION>自己的HP为50%以上时</OPTION> <OPTION>自己的HP为50%以下时</OPTION> <OPTION>同伴中有HP为50%以下的人物存在时</OPTION> <OPTION>同伴的平均HP为 50%以上时</OPTION> <OPTION>同伴的平均HP为50%以下时</OPTION> <OPTION>自己的 SP为50%以上时</OPTION> <OPTION>自己的 SP为50%以下时</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>初次行动时</OPTION> <OPTION>第二次行动时</OPTION> <OPTION>第三次行动时</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少两次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION selected>技能1</OPTION> <OPTION>技能2</OPTION> <OPTION>技能3</OPTION> <OPTION>恢复魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR></TBODY></TABLE></DIV>

<p style="margin-left:50px">这个是战斗设定的例子。<br />
角色会依序行动、<br />从
<span class="bold">No</span> 得的数字开始 <span class="bold">判定</span> 、<br />
符合判定条件的话就执行 <span class="bold">技能</span>。<br />
当战斗状况变化时，根据状况执行以下的操作。</p>
<div style="margin-left:50px">
<table><tbody>
<tr><td>"首次行动时"</td><td>"技能3"</td></tr>
<tr><td>"自己的 SP为50%以上时"</td><td>"技能2"</td></tr>
<tr><td>"必定"</td><td>"技能1"</td></tr>
</tbody></table>
</div>
<p style="margin-left:50px">如果设定、<br />
人物采取首次行动使用技能3。<br />
第二及以后SP在50%以上的回合使用技能2、<br />
SP降到49%一下则使用技能1。</p>
<p style="margin-left:50px">判定条件的数量将会根据<span class="bold">Int</span> 而增加。(No 增加)<br />
判定的种类在登录后会显示(说明上只有简单的实例)。</p>
<!-- ---------------------------------------------------------------- -->
<a name="posi"></a>
<h4>人物的位置关系及后卫保护 <a href="#content"></a></h4>
<table><tbody>
<tr><td style="text-align:right">配置:</td>
<td><input class="vcent" type="radio" checked name="position">前卫(Front)</td>
</tr><tr><td></td>
<td><input class="vcent" type="radio" name="position">后卫(Backs)</td>
</tr><tr><td>后为保护方式 :</td><td>
<select>
<OPTION selected>必定</OPTION> <OPTION>不保护</OPTION> <OPTION>自己的体力为 25% 以上的话</OPTION> <OPTION>自己的体力为 50% 以上的话</OPTION> <OPTION>自己的体力为 75% 以上的话</OPTION> <OPTION>以25%的概率</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>以75%的概率</OPTION></select>
</td></tr></tbody></table>
<p>人物战斗时决定是在前卫还是后卫。<br />
当设为前卫的时候、<br />
战斗时敌方攻击我方后卫时、<br />
符合设定保护方式时、<br />
角色将替后卫承受攻击。</p>
<!-- ---------------------------------------------------------------- -->
<a name="equip"></a>
<h4>人物装备 <a href="#content"></a></h4>
<p>在人物页面是会显示当前装备及可装备的物品。</p>
<p>各装备和人物都有<span class="charge">handle</span> 值、<br />
装备和记得<span class="charge">handle</span>值、不得超过人物的<span class="charge">handle</span>值。<br />
这是装备的限制设定。dex和级别上等的话<span class="charge">handle</span>也会随之上升。</p>
<?php
	$sample	= array(1000,1700,5000);
	foreach($sample as $val) {
		include_once(DATA_ITEM);
		ShowItemDetail(LoadItemData($val));
		print("<br />\n");
	}
?>
<p><ul>
<li><span class="dmg">Atk</span> - 物理攻击力</li>
<li><span class="spdmg">Matk</span> - 魔法攻击力</li>
<li><span class="recover">Def</span> - 物理防御</li>
<li><span class="support">Mdef</span> - 魔法防御</li>
<li><span class="charge">h:</span> - handle值</li>
</ul></p>
<!-- ---------------------------------------------------------------- -->
<a name="skill"></a>
<h4>人物技能 <a href="#content"></a></h4>
<?php
	$sample	= array(1000,1001,1002,2300,3000,3110);
	foreach($sample as $val) {
		include_once(DATA_SKILL);
		ShowSkillDetail(LoadSkillData($val));
		print("<br />\n");
	}
?>
<p>(图像) 技能名称 / 对象 - 选择 / 消耗SP / 威力%x次数 / (准备:待机时间) ... ... ...</p>
<ul>
<p><LI><SPAN class=bold>对象</SPAN> - 技能能影响到的对象<BR><SPAN class=dmg>enemy</SPAN> - 敌人<BR><SPAN class=recover>friend</SPAN> - 同伴<BR><SPAN class=support>self</SPAN> - 对使用者自身而言<BR><SPAN class=charge>all</SPAN> - 敌人-同伴(全体) 
<LI><SPAN class=bold>选择</SPAN> - <SPAN class=u>从对象</SPAN>（选择）使用技能的人物。<BR><SPAN class=recover>individual</SPAN> - 对个人而言。<BR><SPAN class=spdmg>multi</SPAN> - (随机)复数。<BR><SPAN class=charge>all</SPAN> - 对象全部人员。 
<LI><SPAN class=bold>消费SP</SPAN> - 使用技能时消费的SP。不足的话会失败。 
<LI><SPAN class=bold>威力</SPAN> - 技能的强弱。 
<LI><SPAN class=bold>次数</SPAN> - 技能的实行次数。<BR>100%x2 的话、总计有200%的威力。 
<LI>(<SPAN class=bold>准备</SPAN>:<SPAN class=bold>待机时间</SPAN>)<BR>发动技能所需的时间。(使用例:<SPAN class=charge>○○○ 开始 发动技能准备.</SPAN>)<BR>发动技能后的僵直时间。<BR>数字越大时间越长。<BR>
<LI><SPAN class=bold>其他</SPAN><BR><SPAN class=spdmg>Magic</SPAN> - 使用魔法的技能。对威力和效果有影响int。<BR><SPAN class=charge>invalid</SPAN> - 对方的前卫(Front)未防守。<BR><SPAN class=support>BackAttack</SPAN> - 后列(Back)的人物优先成为使用对象。 </LI></UL>

</ul></p>
<p>另外升级之后<br />
可获得几个技能学习点数、<br />
可以消耗一定的点数习得新的技能。</p>
<!-- ---------------------------------------------------------------- -->
<a name="elem"></a>
<h4>攻击属性 <a href="#content"></a></h4>
<p>没有火怕水的类似设定。</p>
<p>只有物理和魔法两种属性。</p>
<!-- ---------------------------------------------------------------- -->
<a name="state"></a>
<h4>人物状态 <a href="#content"></a></h4>
<ul>
<li><span class="recover">生存</span> - HP在1以上的状态。</li>
<li><span class="dmg">死亡</span> - HP为0的状态。</li>
<li><span class="spdmg">剧毒</span> - 每回合将 <span class="u">按照最大HP和级别</span> 受到相应的损伤，不会致死。</li>
</ul>
<!-- ---------------------------------------------------------------- -->
<a name="jobchange"></a>
<h4>转职(职业转换) <a href="#content"></a></h4>
<p>满足专职条件后会显示在人物最下面。</p>
<!-- ---------------------------------------------------------------- -->
<a name="sacrier"></a>
<h4>Sacrier（狂战士）的攻击方式  <a href="#content"></a></h4>
<p style="margin:15px">
<img src="<?=IMG_CHAR?>mon_100r.gif">
<img src="<?=IMG_CHAR?>mon_012.gif"><br />
Sacrier的大部分技能将消耗HP。<br />
人物处在后排时<span class="bold u">后排</span>时，HP消耗为平常的<span class="bold u">2倍</span>。</p>
<!-- ---------------------------------------------------------------- -->
<a name="ranking"></a>
<h4>排行榜 <a href="#content"></a></h4>
<P>排行表的<BR><IMG class=vcent src="./image/icon/crown01.png">第一名 1人<BR><IMG class=vcent src="./image/icon/crown02.png">第二名 2人<BR><IMG class=vcent src="./image/icon/crown03.png">第三名 3人<BR>第四名以下 3人<BR>...<BR>同一名次上可能有多个人物<BR>挑战的话将和排行表上比自己名次高的人中随机选出来的人进行对决、<BR>胜利的话将和对方互换名次。</P><!-- ---------------------------------------------------------------- --><A name=cr></A>
<h4>使用的图像 <a href="#content"></a></h4>
<p><a href="http://whitecafe.sakura.ne.jp/">Whitecatさま</a> - 武器级技能<br />
<a href="http://www.geocities.co.jp/Milano-Cat/3319/">Rドさま</a> - 人物</p>
</div>