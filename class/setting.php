<?php

/**
 * 處理設定頁面的請求
 * Process setting page requests
 * @param main $main 主物件
 * @return bool|string
 */
function SettingProcess($main) {
		if(isset($_POST["NewName"])) {
				$NewName    = $_POST["NewName"];
				if(is_numeric(strpos($NewName,"\t"))) {
						ShowError('error1');
						return false;
				}
				$NewName    = trim($NewName);
				$NewName    = stripslashes($NewName);
				if (!$NewName) {
						ShowError('Name is blank.');
						return false;
				}
				$length    = strlen($NewName);
				if ( 0 == $length || 16 < $length) {
						ShowError('1 to 16 letters?');
						return false;
				}
				$userName    = userNameLoad();
				if(in_array($NewName,$userName)) {
						ShowError("該名稱已被使用。","margin15");
						return false;
				}
				if(!$main->TakeMoney(NEW_NAME_COST)) {
						ShowError('money not enough');
						return false;
				}
				$OldName    = $main->name;
				$NewName    = htmlspecialchars($NewName,ENT_QUOTES);
				if($main->ChangeName($NewName)) {
						ShowResult("Name Changed ({$OldName} -> {$NewName})","margin15");
						userNameAdd($NewName);
						return true;
				} else {
						ShowError("?");//名前が同じ？
						return false;
				}
		}

		if(isset($_POST["setting01"])) {
				if(isset($_POST["record_battle_log"])) {
						$main->record_btl_log = 1;
				} else {
						$main->record_btl_log = false;
				}

				if(isset($_POST["no_JS_itemlist"])) {
						$main->no_JS_itemlist = 1;
				} else {
						$main->no_JS_itemlist = false;
				}
		}
		if(isset($_POST["color"])) {
				if(    strlen($_POST["color"]) != 6 &&
						!preg_match("/^[0-9a-f]{6}$/i", $_POST["color"])) {
						return "error 12072349";
				}
				$main->UserColor    = $_POST["color"];
				ShowResult("Setting changed.","margin15");
				return true;
		}
		return false;
}

/**
 * 顯示設定頁面
 * Show setting page
 * @param main $main 主物件
 */
function SettingShow($main) {
		print('<div style="margin:15px">');
		$record_btl_log = $main->record_btl_log ? " checked" : "";
		$no_JS_itemlist = $main->no_JS_itemlist ? " checked" : "";
		?>
		<h4>設置</h4>
		<form action="?setting" method="post">
				<table><tbody>
				<tr><td><input type="checkbox" name="record_battle_log" value="1" <?php print $record_btl_log?>></td><td>戰鬥記錄</td></tr>
				<tr><td><input type="checkbox" name="no_JS_itemlist" value="1" <?php print $no_JS_itemlist?>></td><td>道具列表不使用javascript</td></tr>
				</tbody></table>
				顏色: 
				<select class=bgcolor name=color>
						<option style="COLOR: #ffffff" value=ffffff selected>SampleColor</option>
						<option style="COLOR: #ffffcc" value=ffffcc>SampleColor</option>
						<option style="COLOR: #ffff99" value=ffff99>SampleColor</option>
						<option style="COLOR: #ffff66" value=ffff66>SampleColor</option>
						<option style="COLOR: #ffff33" value=ffff33>SampleColor</option>
						<option style="COLOR: #ffff00" value=ffff00>SampleColor</option>
						<option style="COLOR: #ffccff" value=ffccff>SampleColor</option>
						<option style="COLOR: #ffcccc" value=ffcccc>SampleColor</option>
						<option style="COLOR: #ffcc99" value=ffcc99>SampleColor</option>
						<option style="COLOR: #ffcc66" value=ffcc66>SampleColor</option>
						<option style="COLOR: #ffcc33" value=ffcc33>SampleColor</option>
						<option style="COLOR: #ffcc00" value=ffcc00>SampleColor</option>
						<option style="COLOR: #ff99ff" value=ff99ff>SampleColor</option>
						<option style="COLOR: #ff99cc" value=ff99cc>SampleColor</option>
						<option style="COLOR: #ff9999" value=ff9999>SampleColor</option>
						<option style="COLOR: #ff9966" value=ff9966>SampleColor</option>
						<option style="COLOR: #ff9933" value=ff9933>SampleColor</option>
						<option style="COLOR: #ff9900" value=ff9900>SampleColor</option>
						<option style="COLOR: #ff66ff" value=ff66ff>SampleColor</option>
						<option style="COLOR: #ff66cc" value=ff66cc>SampleColor</option>
						<option style="COLOR: #ff6699" value=ff6699>SampleColor</option>
						<option style="COLOR: #ff6666" value=ff6666>SampleColor</option>
						<option style="COLOR: #ff6633" value=ff6633>SampleColor</option>
						<option style="COLOR: #ff6600" value=ff6600>SampleColor</option>
						<option style="COLOR: #ff33ff" value=ff33ff>SampleColor</option>
						<option style="COLOR: #ff33cc" value=ff33cc>SampleColor</option>
						<option style="COLOR: #ff3399" value=ff3399>SampleColor</option>
						<option style="COLOR: #ff3366" value=ff3366>SampleColor</option>
						<option style="COLOR: #ff3333" value=ff3333>SampleColor</option>
						<option style="COLOR: #ff3300" value=ff3300>SampleColor</option>
						<option style="COLOR: #ff00ff" value=ff00ff>SampleColor</option>
						<option style="COLOR: #ff00cc" value=ff00cc>SampleColor</option>
						<option style="COLOR: #ff0099" value=ff0099>SampleColor</option>
						<option style="COLOR: #ff0066" value=ff0066>SampleColor</option>
						<option style="COLOR: #ff0033" value=ff0033>SampleColor</option>
						<option style="COLOR: #ff0000" value=ff0000>SampleColor</option>
						<option style="COLOR: #ccffff" value=ccffff>SampleColor</option>
						<option style="COLOR: #ccffcc" value=ccffcc>SampleColor</option>
						<option style="COLOR: #ccff99" value=ccff99>SampleColor</option>
						<option style="COLOR: #ccff66" value=ccff66>SampleColor</option>
						<option style="COLOR: #ccff33" value=ccff33>SampleColor</option>
						<option style="COLOR: #ccff00" value=ccff00>SampleColor</option>
						<option style="COLOR: #ccccff" value=ccccff>SampleColor</option>
						<option style="COLOR: #cccccc" value=cccccc>SampleColor</option>
						<option style="COLOR: #cccc99" value=cccc99>SampleColor</option>
						<option style="COLOR: #cccc66" value=cccc66>SampleColor</option>
						<option style="COLOR: #cccc33" value=cccc33>SampleColor</option>
						<option style="COLOR: #cccc00" value=cccc00>SampleColor</option>
						<option style="COLOR: #cc99ff" value=cc99ff>SampleColor</option>
						<option style="COLOR: #cc99cc" value=cc99cc>SampleColor</option>
						<option style="COLOR: #cc9999" value=cc9999>SampleColor</option>
						<option style="COLOR: #cc9966" value=cc9966>SampleColor</option>
						<option style="COLOR: #cc9933" value=cc9933>SampleColor</option>
						<option style="COLOR: #cc9900" value=cc9900>SampleColor</option>
						<option style="COLOR: #cc66ff" value=cc66ff>SampleColor</option>
						<option style="COLOR: #cc66cc" value=cc66cc>SampleColor</option>
						<option style="COLOR: #cc6699" value=cc6699>SampleColor</option>
						<option style="COLOR: #cc6666" value=cc6666>SampleColor</option>
						<option style="COLOR: #cc6633" value=cc6633>SampleColor</option>
						<option style="COLOR: #cc6600" value=cc6600>SampleColor</option>
						<option style="COLOR: #cc33ff" value=cc33ff>SampleColor</option>
						<option style="COLOR: #cc33cc" value=cc33cc>SampleColor</option>
						<option style="COLOR: #cc3399" value=cc3399>SampleColor</option>
						<option style="COLOR: #cc3366" value=cc3366>SampleColor</option>
						<option style="COLOR: #cc3333" value=cc3333>SampleColor</option>
						<option style="COLOR: #cc3300" value=cc3300>SampleColor</option>
						<option style="COLOR: #cc00ff" value=cc00ff>SampleColor</option>
						<option style="COLOR: #cc00cc" value=cc00cc>SampleColor</option>
						<option style="COLOR: #cc0099" value=cc0099>SampleColor</option>
						<option style="COLOR: #cc0066" value=cc0066>SampleColor</option>
						<option style="COLOR: #cc0033" value=cc0033>SampleColor</option>
						<option style="COLOR: #cc0000" value=cc0000>SampleColor</option>
						<option style="COLOR: #99ffff" value=99ffff>SampleColor</option>
						<option style="COLOR: #99ffcc" value=99ffcc>SampleColor</option>
						<option style="COLOR: #99ff99" value=99ff99>SampleColor</option>
						<option style="COLOR: #99ff66" value=99ff66>SampleColor</option>
						<option style="COLOR: #99ff33" value=99ff33>SampleColor</option>
						<option style="COLOR: #99ff00" value=99ff00>SampleColor</option>
						<option style="COLOR: #99ccff" value=99ccff>SampleColor</option>
						<option style="COLOR: #99cccc" value=99cccc>SampleColor</option>
						<option style="COLOR: #99cc99" value=99cc99>SampleColor</option>
						<option style="COLOR: #99cc66" value=99cc66>SampleColor</option>
						<option style="COLOR: #99cc33" value=99cc33>SampleColor</option>
						<option style="COLOR: #99cc00" value=99cc00>SampleColor</option>
						<option style="COLOR: #9999ff" value=9999ff>SampleColor</option>
						<option style="COLOR: #9999cc" value=9999cc>SampleColor</option>
						<option style="COLOR: #999999" value=999999>SampleColor</option>
						<option style="COLOR: #999966" value=999966>SampleColor</option>
						<option style="COLOR: #999933" value=999933>SampleColor</option>
						<option style="COLOR: #999900" value=999900>SampleColor</option>
						<option style="COLOR: #9966ff" value=9966ff>SampleColor</option>
						<option style="COLOR: #9966cc" value=9966cc>SampleColor</option>
						<option style="COLOR: #996699" value=996699>SampleColor</option>
						<option style="COLOR: #996666" value=996666>SampleColor</option>
						<option style="COLOR: #996633" value=996633>SampleColor</option>
						<option style="COLOR: #996600" value=996600>SampleColor</option>
						<option style="COLOR: #9933ff" value=9933ff>SampleColor</option>
						<option style="COLOR: #9933cc" value=9933cc>SampleColor</option>
						<option style="COLOR: #993399" value=993399>SampleColor</option>
						<option style="COLOR: #993366" value=993366>SampleColor</option>
						<option style="COLOR: #993333" value=993333>SampleColor</option>
						<option style="COLOR: #993300" value=993300>SampleColor</option>
						<option style="COLOR: #9900ff" value=9900ff>SampleColor</option>
						<option style="COLOR: #9900cc" value=9900cc>SampleColor</option>
						<option style="COLOR: #990099" value=990099>SampleColor</option>
						<option style="COLOR: #990066" value=990066>SampleColor</option>
						<option style="COLOR: #990033" value=990033>SampleColor</option>
						<option style="COLOR: #990000" value=990000>SampleColor</option>
						<option style="COLOR: #66ffff" value=66ffff>SampleColor</option>
						<option style="COLOR: #66ffcc" value=66ffcc>SampleColor</option>
						<option style="COLOR: #66ff99" value=66ff99>SampleColor</option>
						<option style="COLOR: #66ff66" value=66ff66>SampleColor</option>
						<option style="COLOR: #66ff33" value=66ff33>SampleColor</option>
						<option style="COLOR: #66ff00" value=66ff00>SampleColor</option>
						<option style="COLOR: #66ccff" value=66ccff>SampleColor</option>
						<option style="COLOR: #66cccc" value=66cccc>SampleColor</option>
						<option style="COLOR: #66cc99" value=66cc99>SampleColor</option>
						<option style="COLOR: #66cc66" value=66cc66>SampleColor</option>
						<option style="COLOR: #66cc33" value=66cc33>SampleColor</option>
						<option style="COLOR: #66cc00" value=66cc00>SampleColor</option>
						<option style="COLOR: #6699ff" value=6699ff>SampleColor</option>
						<option style="COLOR: #6699cc" value=6699cc>SampleColor</option>
						<option style="COLOR: #669999" value=669999>SampleColor</option>
						<option style="COLOR: #669966" value=669966>SampleColor</option>
						<option style="COLOR: #669933" value=669933>SampleColor</option>
						<option style="COLOR: #669900" value=669900>SampleColor</option>
						<option style="COLOR: #6666ff" value=6666ff>SampleColor</option>
						<option style="COLOR: #6666cc" value=6666cc>SampleColor</option>
						<option style="COLOR: #666699" value=666699>SampleColor</option>
						<option style="COLOR: #666666" value=666666>SampleColor</option>
						<option style="COLOR: #666633" value=666633>SampleColor</option>
						<option style="COLOR: #666600" value=666600>SampleColor</option>
						<option style="COLOR: #6633ff" value=6633ff>SampleColor</option>
						<option style="COLOR: #6633cc" value=6633cc>SampleColor</option>
						<option style="COLOR: #663399" value=663399>SampleColor</option>
						<option style="COLOR: #663366" value=663366>SampleColor</option>
						<option style="COLOR: #663333" value=663333>SampleColor</option>
						<option style="COLOR: #663300" value=663300>SampleColor</option>
						<option style="COLOR: #6600ff" value=6600ff>SampleColor</option>
						<option style="COLOR: #6600cc" value=6600cc>SampleColor</option>
						<option style="COLOR: #660099" value=660099>SampleColor</option>
						<option style="COLOR: #660066" value=660066>SampleColor</option>
						<option style="COLOR: #660033" value=660033>SampleColor</option>
						<option style="COLOR: #660000" value=660000>SampleColor</option>
						<option style="COLOR: #33ffff" value=33ffff>SampleColor</option>
						<option style="COLOR: #33ffcc" value=33ffcc>SampleColor</option>
						<option style="COLOR: #33ff99" value=33ff99>SampleColor</option>
						<option style="COLOR: #33ff66" value=33ff66>SampleColor</option>
						<option style="COLOR: #33ff33" value=33ff33>SampleColor</option>
						<option style="COLOR: #33ff00" value=33ff00>SampleColor</option>
						<option style="COLOR: #33ccff" value=33ccff>SampleColor</option>
						<option style="COLOR: #33cccc" value=33cccc>SampleColor</option>
						<option style="COLOR: #33cc99" value=33cc99>SampleColor</option>
						<option style="COLOR: #33cc66" value=33cc66>SampleColor</option>
						<option style="COLOR: #33cc33" value=33cc33>SampleColor</option>
						<option style="COLOR: #33cc00" value=33cc00>SampleColor</option>
						<option style="COLOR: #3399ff" value=3399ff>SampleColor</option>
						<option style="COLOR: #3399cc" value=3399cc>SampleColor</option>
						<option style="COLOR: #339999" value=339999>SampleColor</option>
						<option style="COLOR: #339966" value=339966>SampleColor</option>
						<option style="COLOR: #339933" value=339933>SampleColor</option>
						<option style="COLOR: #339900" value=339900>SampleColor</option>
						<option style="COLOR: #3366ff" value=3366ff>SampleColor</option>
						<option style="COLOR: #3366cc" value=3366cc>SampleColor</option>
						<option style="COLOR: #336699" value=336699>SampleColor</option>
						<option style="COLOR: #336666" value=336666>SampleColor</option>
						<option style="COLOR: #336633" value=336633>SampleColor</option>
						<option style="COLOR: #336600" value=336600>SampleColor</option>
						<option style="COLOR: #3333ff" value=3333ff>SampleColor</option>
						<option style="COLOR: #3333cc" value=3333cc>SampleColor</option>
						<option style="COLOR: #333399" value=333399>SampleColor</option>
						<option style="COLOR: #333366" value=333366>SampleColor</option>
						<option style="COLOR: #333333" value=333333>SampleColor</option>
						<option style="COLOR: #333300" value=333300>SampleColor</option>
						<option style="COLOR: #3300ff" value=3300ff>SampleColor</option>
						<option style="COLOR: #3300cc" value=3300cc>SampleColor</option>
						<option style="COLOR: #330099" value=330099>SampleColor</option>
						<option style="COLOR: #330066" value=330066>SampleColor</option>
						<option style="COLOR: #330033" value=330033>SampleColor</option>
						<option style="COLOR: #330000" value=330000>SampleColor</option>
						<option style="COLOR: #00ffff" value=00ffff>SampleColor</option>
						<option style="COLOR: #00ffcc" value=00ffcc>SampleColor</option>
						<option style="COLOR: #00ff99" value=00ff99>SampleColor</option>
						<option style="COLOR: #00ff66" value=00ff66>SampleColor</option>
						<option style="COLOR: #00ff33" value=00ff33>SampleColor</option>
						<option style="COLOR: #00ff00" value=00ff00>SampleColor</option>
						<option style="COLOR: #00ccff" value=00ccff>SampleColor</option>
						<option style="COLOR: #00cccc" value=00cccc>SampleColor</option>
						<option style="COLOR: #00cc99" value=00cc99>SampleColor</option>
						<option style="COLOR: #00cc66" value=00cc66>SampleColor</option>
						<option style="COLOR: #00cc33" value=00cc33>SampleColor</option>
						<option style="COLOR: #00cc00" value=00cc00>SampleColor</option>
						<option style="COLOR: #0099ff" value=0099ff>SampleColor</option>
						<option style="COLOR: #0099cc" value=0099cc>SampleColor</option>
						<option style="COLOR: #009999" value=009999>SampleColor</option>
						<option style="COLOR: #009966" value=009966>SampleColor</option>
						<option style="COLOR: #009933" value=009933>SampleColor</option>
						<option style="COLOR: #009900" value=009900>SampleColor</option>
						<option style="COLOR: #0066ff" value=0066ff>SampleColor</option>
						<option style="COLOR: #0066cc" value=0066cc>SampleColor</option>
						<option style="COLOR: #006699" value=006699>SampleColor</option>
						<option style="COLOR: #006666" value=006666>SampleColor</option>
						<option style="COLOR: #006633" value=006633>SampleColor</option>
						<option style="COLOR: #006600" value=006600>SampleColor</option>
						<option style="COLOR: #0033ff" value=0033ff>SampleColor</option>
						<option style="COLOR: #0033cc" value=0033cc>SampleColor</option>
						<option style="COLOR: #003399" value=003399>SampleColor</option>
						<option style="COLOR: #003366" value=003366>SampleColor</option>
						<option style="COLOR: #003333" value=003333>SampleColor</option>
						<option style="COLOR: #003300" value=003300>SampleColor</option>
						<option style="COLOR: #0000ff" value=0000ff>SampleColor</option>
						<option style="COLOR: #0000cc" value=0000cc>SampleColor</option>
						<option style="COLOR: #000099" value=000099>SampleColor</option>
						<option style="COLOR: #000066" value=000066>SampleColor</option>
						<option style="COLOR: #000033" value=000033>SampleColor</option>
						<option style="COLOR: #000000" value=000000>SampleColor</option>
				</select><br />
				<input type="submit" class="btn" name="setting01" value="修改" style="width:100px">
				<input type="hidden" name="setting01" value="1">
		</form>
		<h4>註銷</h4>
		<form action="<?php print INDEX?>" method="post">
				<input type="submit" class="btn" name="logout" value="註銷" style="width:100px">
		</form>
		<h4>變更隊伍名</h4>
		<form action="?setting" method="post">
				費用 : <?php print MoneyFormat(NEW_NAME_COST)?><br />
				16個字符(全角=2字符)<br />
				新的名稱 : <input type="text" class="text" name="NewName" size="20">
				<input type="submit" class="btn" value="變更" style="width:100px">
		</form>
		<h4>世界盡頭</h4>
		<div class="u">※自殺用</div>
		<form action="?setting" method="post">
				PassWord : <input type="text" class="text" name="deletepass" size="20">
				<input type="submit" class="btn" name="delete" value="我要自殺了..." style="width:100px">
		</form>
		</div>
		<?php
}

?>