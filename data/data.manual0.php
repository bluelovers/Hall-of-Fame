<div style="margin:15px">
<!-- ---------------------------------------------------------------- -->
<a name="content"></a>
<h4>目錄</h4>
<ul>
<li><a href="#rule">規則</a></li>
<li><a href="#menu">菜單</a></li>
<li><a href="#btl">戰鬥流程</a></li>
<li><a href="#char">人物設定</a></li>
<li><a href="#charstat">人物的基礎能力值</a></li>
<li><a href="#statup">能力值上升</a></li>
<li><a href="#jdg">任務在戰鬥中的命令</a></li>
<li><a href="#posi">人物的位置關係及後衛保護</a></li>
<li><a href="#equip">人物裝備</a></li>
<li><a href="#skill">人物技能</a></li>
<li><a href="#elem">攻擊屬性</a></li>
<li><a href="#state">人物狀態</a></li>
<li><a href="#jobchange">轉職(職業轉換)</a></li>
<li><a href="#sacrier">Sacrier（狂戰士）的攻擊方式</a></li>
<li><a href="#ranking">排行</a></li>
<li><a href="?manual2">高級指南</a></li>
<li><a href="#cr">使用的圖像</a></li>
</ul>
<a name="rule"></a>
<h4>規則(Rule) <a href="#content"></a></h4>
<p style="margin-left:50px">本遊戲目的、<br />
就是爬到排行榜的第一名位置。<br />
並持續維持住排名。<br />
並沒有什麼冒險的要素。</p>
<p style="margin-left:50px">可以自己組建1-5人的隊伍進行戰鬥、<br />
讓其登上排行榜。</p>
<p style="margin-left:50px">為了能登上榜首、<br />
要持續的與敵人（怪物）進行戰鬥鍛煉<br />
從敵人（怪物）手中奪取更強力的道具<br />
這就是這遊戲有趣的地方。<br />
排名戰的敵人將是其他的玩家。</p>
<p style="margin-left:50px">各個人物依照技能的使用條件等、進行詳細的設定。<br />
要調整出無瑕的戰術配置、不是一件簡單的事情。</p>
<p class="bold u" style="margin-left:50px">排行榜功能還不是很完善。</p>
<!-- ---------------------------------------------------------------- -->
<a name="menu"></a>
<h4>菜單(Menu) <a href="#content"></a></h4>
<img src="./image/manual/003.gif">
<p><span class="u bold">登錄後會顯示如下菜單</span>
<ul>
<li><span class="bold">Top</span> - 所擁有的人物一覽</li>
<li><span class="bold">戰鬥(Hunt)</span> - 和怪物戰鬥</li>
<li><span class="bold">道具(Item)</span> - 所持道具一覽</li>
<li><span class="bold">商店(Shop)</span> - 買賣道具和消費時間打工。</li>
<li><span class="bold">排名(Rank)</span> - 排行榜。</li>
<li><span class="bold">僱傭(Recruit)</span> - 僱傭新人物</li>
<li><span class="bold">設定(Setting)</span> - 各種設定。註銷。銷號。</li>
<li><span class="bold">記錄(Log)</span> - 查看過去戰鬥的記錄。</li>
</ul></p>
<p>另外、<span class="u bold">菜單的下面</span>
<ul>
<li /><span class="bold">隊伍名稱</span> - 名稱。
<li /><span class="bold">資金(Funds)</span> - 所擁有的金錢。
<li /><span class="bold">時間(Time)</span> - 時間。戰鬥時減少。會隨時間流逝逐漸恢復。
</ul></p>
<a name="btl"></a>
<!-- ---------------------------------------------------------------- -->
<h4>戰鬥流程 <a href="#content"></a></h4>
<div style="margin-left:50px">
<p>戰鬥過程是由電腦處理。<br />
戰鬥中晚間不能下達指令。</p>
1. 依照人物的能力順序行動。
<div class="bold" style="margin-left:30px">↓↓↓</div>
2. 人物會根據事先設定來行動。
<div class="bold" style="margin-left:30px">↓↓↓</div>
3. ( 重複1和2)
<div class="bold" style="margin-left:30px">↓↓↓</div>
4. 滿足以下條件，則戰鬥結束。
<p><span class="bold u">結束條件</span><br />
1.我方或敵方全員戰鬥不能。<br />
2.累計戰鬥 <?=BATTLE_MAX_TURNS?>回合則被判為平局。</p>
</div>
<!-- ---------------------------------------------------------------- -->
<a name="char"></a>
<h4>人物設定<a href="#content"></a></h4>
<p style="margin-left:50px">登錄後，點擊top頁面中的人物形象<br />
將顯示人物的相關頁面。<br />
<img src="./image/manual/001.gif"><br />
詳細說明如下。</p>
<!-- ---------------------------------------------------------------- -->
<a name="charstat"></a>
<h4>人物基本能力值 <a href="#content"></a></h4>
<p style="margin-left:50px">
<img src="./image/manual/002.gif">
<ul>
<li /><span class="bold">Exp</span> :升級所需經驗
<li /><span class="bold">MaxHP</span> : 最大體力。為0則被擊倒
<li /><span class="bold">MaxSP</span> : 使用技能所消耗的值
<li /><span class="bold">Str</span> : 力量。影響HP和物理攻擊力。
<li /><span class="bold">Int</span> : 智力。影響SP和魔法攻擊力。
<li /><span class="bold">Dex</span> : 敏捷。可裝更多的裝備（？），獵人系攻擊力上升，強化召喚物。
<li /><span class="bold">Spd</span> : 速度。越高則行動次數也越多，攻擊間隔將變短。
<li /><span class="bold">Luk</span> : 運強化召喚物。
</ul>
</p>
<!-- ---------------------------------------------------------------- -->
<a name="statup"></a>
<h4>能力值上升g<a href="#content"></a></h4>
<p style="margin-left:50px">角色持續戰鬥獲得經驗值<span class="u">升級</span>時、<br />
會得到幾個點數，可以自由用這些來強化各項能力值。</p>
<!-- ---------------------------------------------------------------- -->
<a name="jdg"></a>
<h4>人物在戰鬥中的命令 <a href="#content"></a></h4>
<p style="margin-left:50px">基本上人物戰鬥是都是依據玩家設定的動作逐步行動的。</p>
<div style="margin-left:50px">
<table cellspacing="5"><tbody>
<tr><td class="bold">No</td><td style="text-align:center" class="bold">判定</td>
<td style="text-align:center" class="bold">使用技能</td></tr>
<tr><TD>1</TD>
<TD><SELECT> <OPTION>必定</OPTION> <OPTION>自己的HP為50%以上時</OPTION> <OPTION>自己的HP為50%以下時</OPTION> <OPTION>同伴中有 HP為50%以下的人物時</OPTION> <OPTION>同伴的平均HP為 50%以上時</OPTION> <OPTION>同伴的平均HP為 50%以下時</OPTION> <OPTION>自己的SP為50%以上時</OPTION> <OPTION>自己的 SP為50%以下時</OPTION> <OPTION>以50%的概率</OPTION> <OPTION selected>初次行動時</OPTION> <OPTION>第二次行動時</OPTION> <OPTION>第三次行動時</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少兩次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>技能1</OPTION> <OPTION>技能2</OPTION> <OPTION selected>技能3</OPTION> <OPTION>恢復魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR>
<TR>
<TD>2</TD>
<TD><SELECT> <OPTION>必定</OPTION> <OPTION>自己的HP為50%以上時</OPTION> <OPTION>自己的 HP為50%以下時</OPTION> <OPTION>同伴裡有 HP為50%以下的人物時</OPTION> <OPTION>同伴的平均HP為 50%以上時</OPTION> <OPTION>同伴的平均HP為 50%以下時</OPTION> <OPTION selected>自己的SP為50%以上時</OPTION> <OPTION>自己的 SP為50%以下時</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>初次行動時</OPTION> <OPTION>第二次行動時</OPTION> <OPTION>第三次行動時</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少兩次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION>技能1</OPTION> <OPTION selected>技能2</OPTION> <OPTION>技能3</OPTION> <OPTION>恢復魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR>
<TR>
<TD>3</TD>
<TD><SELECT> <OPTION selected>必須</OPTION> <OPTION>自己的HP為50%以上時</OPTION> <OPTION>自己的HP為50%以下時</OPTION> <OPTION>同伴中有HP為50%以下的人物存在時</OPTION> <OPTION>同伴的平均HP為 50%以上時</OPTION> <OPTION>同伴的平均HP為50%以下時</OPTION> <OPTION>自己的 SP為50%以上時</OPTION> <OPTION>自己的 SP為50%以下時</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>初次行動時</OPTION> <OPTION>第二次行動時</OPTION> <OPTION>第三次行動時</OPTION> <OPTION>至少一次</OPTION> <OPTION>至少兩次</OPTION> <OPTION>至少三次</OPTION></SELECT> </TD>
<TD><SELECT> <OPTION selected>技能1</OPTION> <OPTION>技能2</OPTION> <OPTION>技能3</OPTION> <OPTION>恢復魔法</OPTION> <OPTION>使用物品</OPTION></SELECT> </TD></TR></TBODY></TABLE></DIV>

<p style="margin-left:50px">這個是戰鬥設定的例子。<br />
角色會依序行動、<br />從
<span class="bold">No</span> 得的數字開始 <span class="bold">判定</span> 、<br />
符合判定條件的話就執行 <span class="bold">技能</span>。<br />
當戰鬥狀況變化時，根據狀況執行以下的操作。</p>
<div style="margin-left:50px">
<table><tbody>
<tr><td>"首次行動時"</td><td>"技能3"</td></tr>
<tr><td>"自己的 SP為50%以上時"</td><td>"技能2"</td></tr>
<tr><td>"必定"</td><td>"技能1"</td></tr>
</tbody></table>
</div>
<p style="margin-left:50px">如果設定、<br />
人物採取首次行動使用技能3。<br />
第二及以後SP在50%以上的回合使用技能2、<br />
SP降到49%一下則使用技能1。</p>
<p style="margin-left:50px">判定條件的數量將會根據<span class="bold">Int</span> 而增加。(No 增加)<br />
判定的種類在登錄後會顯示(說明上只有簡單的實例)。</p>
<!-- ---------------------------------------------------------------- -->
<a name="posi"></a>
<h4>人物的位置關係及後衛保護 <a href="#content"></a></h4>
<table><tbody>
<tr><td style="text-align:right">配置:</td>
<td><input class="vcent" type="radio" checked name="position">前衛(Front)</td>
</tr><tr><td></td>
<td><input class="vcent" type="radio" name="position">後衛(Backs)</td>
</tr><tr><td>後為保護方式 :</td><td>
<select>
<OPTION selected>必定</OPTION> <OPTION>不保護</OPTION> <OPTION>自己的體力為 25% 以上的話</OPTION> <OPTION>自己的體力為 50% 以上的話</OPTION> <OPTION>自己的體力為 75% 以上的話</OPTION> <OPTION>以25%的概率</OPTION> <OPTION>以50%的概率</OPTION> <OPTION>以75%的概率</OPTION></select>
</td></tr></tbody></table>
<p>人物戰鬥時決定是在前衛還是後衛。<br />
當設為前衛的時候、<br />
戰鬥時敵方攻擊我方後衛時、<br />
符合設定保護方式時、<br />
角色將替後衛承受攻擊。</p>
<!-- ---------------------------------------------------------------- -->
<a name="equip"></a>
<h4>人物裝備 <a href="#content"></a></h4>
<p>在人物頁面是會顯示當前裝備及可裝備的物品。</p>
<p>各裝備和人物都有<span class="charge">handle</span> 值、<br />
裝備和記得<span class="charge">handle</span>值、不得超過人物的<span class="charge">handle</span>值。<br />
這是裝備的限制設定。dex和級別上等的話<span class="charge">handle</span>也會隨之上升。</p>
<?php
	$sample	= array(1000,1700,5000);
	foreach($sample as $val) {
		include_once(DATA_ITEM);
		ShowItemDetail(LoadItemData($val));
		print("<br />\n");
	}
?>
<p><ul>
<li><span class="dmg">Atk</span> - 物理攻擊力</li>
<li><span class="spdmg">Matk</span> - 魔法攻擊力</li>
<li><span class="recover">Def</span> - 物理防禦</li>
<li><span class="support">Mdef</span> - 魔法防禦</li>
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
<p>(圖像) 技能名稱 / 對像 - 選擇 / 消耗SP / 威力%x次數 / (準備:待機時間) ... ... ...</p>
<ul>
<p><LI><SPAN class=bold>對像</SPAN> - 技能能影響到的對象<BR><SPAN class=dmg>enemy</SPAN> - 敵人<BR><SPAN class=recover>friend</SPAN> - 同伴<BR><SPAN class=support>self</SPAN> - 對使用者自身而言<BR><SPAN class=charge>all</SPAN> - 敵人-同伴(全體) 
<LI><SPAN class=bold>選擇</SPAN> - <SPAN class=u>從對像</SPAN>（選擇）使用技能的人物。<BR><SPAN class=recover>individual</SPAN> - 對個人而言。<BR><SPAN class=spdmg>multi</SPAN> - (隨機)複數。<BR><SPAN class=charge>all</SPAN> - 對像全部人員。 
<LI><SPAN class=bold>消費SP</SPAN> - 使用技能時消費的SP。不足的話會失敗。 
<LI><SPAN class=bold>威力</SPAN> - 技能的強弱。 
<LI><SPAN class=bold>次數</SPAN> - 技能的實行次數。<BR>100%x2 的話、總計有200%的威力。 
<LI>(<SPAN class=bold>準備</SPAN>:<SPAN class=bold>待機時間</SPAN>)<BR>發動技能所需的時間。(使用例:<SPAN class=charge>○○○ 開始 發動技能準備.</SPAN>)<BR>發動技能後的僵直時間。<BR>數字越大時間越長。<BR>
<LI><SPAN class=bold>其他</SPAN><BR><SPAN class=spdmg>Magic</SPAN> - 使用魔法的技能。對威力和效果有影響int。<BR><SPAN class=charge>invalid</SPAN> - 對方的前衛(Front)未防守。<BR><SPAN class=support>BackAttack</SPAN> - 後列(Back)的人物優先成為使用對象。 </LI></UL>

</ul></p>
<p>另外升級之後<br />
可獲得幾個技能學習點數、<br />
可以消耗一定的點數習得新的技能。</p>
<!-- ---------------------------------------------------------------- -->
<a name="elem"></a>
<h4>攻擊屬性 <a href="#content"></a></h4>
<p>沒有火怕水的類似設定。</p>
<p>只有物理和魔法兩種屬性。</p>
<!-- ---------------------------------------------------------------- -->
<a name="state"></a>
<h4>人物狀態 <a href="#content"></a></h4>
<ul>
<li><span class="recover">生存</span> - HP在1以上的狀態。</li>
<li><span class="dmg">死亡</span> - HP為0的狀態。</li>
<li><span class="spdmg">劇毒</span> - 每回合將 <span class="u">按照最大HP和級別</span> 受到相應的損傷，不會致死。</li>
</ul>
<!-- ---------------------------------------------------------------- -->
<a name="jobchange"></a>
<h4>轉職(職業轉換) <a href="#content"></a></h4>
<p>滿足專職條件後會顯示在人物最下面。</p>
<!-- ---------------------------------------------------------------- -->
<a name="sacrier"></a>
<h4>Sacrier（狂戰士）的攻擊方式  <a href="#content"></a></h4>
<p style="margin:15px">
<img src="<?=IMG_CHAR?>mon_100r.gif">
<img src="<?=IMG_CHAR?>mon_012.gif"><br />
Sacrier的大部分技能將消耗HP。<br />
人物處在後排時<span class="bold u">後排</span>時，HP消耗為平常的<span class="bold u">2倍</span>。</p>
<!-- ---------------------------------------------------------------- -->
<a name="ranking"></a>
<h4>排行榜 <a href="#content"></a></h4>
<P>排行表的<BR><IMG class=vcent src="./image/icon/crown01.png">第一名 1人<BR><IMG class=vcent src="./image/icon/crown02.png">第二名 2人<BR><IMG class=vcent src="./image/icon/crown03.png">第三名 3人<BR>第四名以下 3人<BR>...<BR>同一名次上可能有多個人物<BR>挑戰的話將和排行表上比自己名次高的人中隨機選出來的人進行對決、<BR>勝利的話將和對方互換名次。</P><!-- ---------------------------------------------------------------- --><A name=cr></A>
<h4>使用的圖像 <a href="#content"></a></h4>
<p><a href="http://whitecafe.sakura.ne.jp/">Whitecatさま</a> - 武器級技能<br />
<a href="http://www.geocities.co.jp/Milano-Cat/3319/">Rドさま</a> - 人物</p>
</div>