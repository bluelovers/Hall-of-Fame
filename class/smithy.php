<?php

/**
 * 顯示精煉頁面的頁首
 * @param main $main 主物件
 */
function SmithyRefineHeader($main) {
		?>
		<div style="margin:15px">
				<h4>精煉工房(Refine)</h4>
				<div style="width:600px">
						<div style="float:left;width:80px;">
								<img src="<?php print IMG_CHAR?>mon_053r.gif" />
						</div>
						<div style="float:right;width:520px;">
								在這裡 可以進行物品的精煉！<br />
								選擇需要精練的物品以及精練的次數。<br />
								不過加工壞了我們不負責。<br />
								弟弟在管理的 <span class="bold">製作工房</span> 在<a href="?menu=create">這邊</a>。
						</div>
						<div style="clear:both"></div>
				</div>
				<h4>精煉道具<a name="refine"></a></h4>
				<div style="margin:0 20px">
		<?php
}

/**
 * 處理精煉請求
 * @param main $main 主物件
 * @return bool
 */
function SmithyRefineProcess($main) {
		if(!$_POST["refine"])
			return false;
		if(!$_POST["item_no"]) {
			ShowError("Select Item.");
			return false;
		}
		// 道具が讀み迂めない場合
		if(!$item	= LoadItemData($_POST["item_no"])) {
			ShowError("Failed to load item data.");
			return false;
		}
		// 道具を所持していない場合
		if(!$main->item[$_POST["item_no"]]) {
			ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// 回數が指定されていない場合
		if($_POST["timesA"] < $_POST["timesB"])
			$times	= $_POST["timesB"];
		else
			$times	= $_POST["timesA"];
		if(!$times || $times < 1 || (REFINE_LIMIT) < $times ) {
			ShowError("times?");
			return false;
		}
		include(CLASS_SMITHY);
		$obj_item	= new Item($_POST["item_no"]);
		// その道具が精鍊できない場合
		if(!$obj_item->CanRefine()) {
			ShowError("Cant refine \"{$item[name]}\"");
			return false;
		}
		// ここから精鍊を始める處理
		$main->DeleteItem($_POST["item_no"]);// 道具は消えるか變化するので消す
		$Price	= round($item["buy"]/2);
		// 最大精鍊數の調整。
		if( REFINE_LIMIT < ($item["refine"] + $times) ) {
			$times	= REFINE_LIMIT - $item["refine"];
		}
		$Trys	= 0;
		for($i=0; $i<$times; $i++) {
			// お金を引く
			if($main->TakeMoney($Price)) {
				$MoneySum	+= $Price;
				$Trys++;
				if(!$obj_item->ItemRefine()) {//精鍊する(false=失敗なので終了する)
					break;
				}
			// お金が途中でなくなった場合。
			} else {
				ShowError("Not enough money.<br />\n");
				$main->AddItem($obj_item->ReturnItem());
				break;
			}
			// 指定回數精鍊を成功しきった場合。
			if($i == ($times - 1)) {
				$main->AddItem($obj_item->ReturnItem());
			}
		}
		print("Money Used : ".MoneyFormat($Price)." x ".$Trys." = ".MoneyFormat($MoneySum)."<br />\n");
		$main->SaveUserItem();
		return true;
		/*// お金が足りてるか計算
		$Price	= round($item["buy"]/2);
		$MoneyNeed	= $times * $Price;
		if($main->money < $MoneyNeed) {
			ShowError("Your request needs ".MoneyFormat($MoneyNeed));
			return false;
		}*/
		
	}

//	鍛冶屋表示
	function SmithyRefineShow() {
		// ■精鍊處理
		//$Result	= $main->SmithyRefineProcess();

		// 精鍊可能な物の表示
		if($main->item) {
			include(CLASS_JS_ITEMLIST);
			$possible	= CanRefineType();
			$possible	= array_flip($possible);
			//配列の先頭の值が"0"なので1にする(isset使わずにtrueにするため)
			$possible[key($possible)]++;

			$goods	= new JS_ItemList();
			$goods->SetID("my");
			$goods->SetName("type");

			$goods->ListTable("<table cellspacing=\"0\">");// テ一ブルタグのはじまり
			$goods->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">精煉費</td><td class=\"align-center td9\">Item</td></tr>"); // テ一ブルの最初と最後の行に表示させるやつ。

			// JSを使用しない。
			if($main->no_JS_itemlist)
				$goods->NoJS();
			foreach($main->item as $no => $val) {
				$item	= LoadItemData($no);
				// 精鍊可能な物だけ表示させる。
				if(!$possible[$item["type"]])
					continue;
				$price	= $item["buy"]/2;
				// NoTable
	//			$string	= '<input type="radio" class="vcent" name="item_no" value="'.$no.'">';
	//			$string	.= "<span style=\"padding-right:10px;width:10ex\">".MoneyFormat($price)."</span>".ShowItemDetail($item,$val,1)."<br />";

				$string	= '<tr>';
				$string	.= '<td class="td7"><input type="radio" class="vcent" name="item_no" value="'.$no.'">';
				$string	.= '</td><td class="td7">'.MoneyFormat($price).'</td><td class="td8">'.ShowItemDetail($item,$val,1)."<td>";
				$string	.= "</tr>";

				$goods->AddItem($item,$string);
			}
			// JavaScript部分の書き出し
			print($goods->GetJavaScript("list"));
			print('可以精煉的名單');
			// 種類のセレクトボックス
			print($goods->ShowSelect());
			print('<form action="?menu=refine" method="post">'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			// 精鍊回數の指定
			print('回數 : <select name="timesA">'."\n");
			for($i=1; $i<11; $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			// リストの表示
			print('<div id="list">'.$goods->ShowDefault().'</div>'."\n");
			// [Refine]button
			print('<input type="submit" value="Refine" name="refine" class="btn">'."\n");
			print('<input type="hidden" value="1" name="refine">'."\n");
			// 精鍊回數の指定
			print('回數 : <select name="timesB">'."\n");
			for($i=1; $i<(REFINE_LIMIT+1); $i++) {
				print('<option value="'.$i.'">'.$i.'</option>');
			}
			print('</select>'."\n");
			print('</form>'."\n");
		} else {
			print("No items<br />\n");
		}
		print("</div>\n");
	?>
	</div>
<?php 
	}
//////////////////////////////////////////////////
//	鍛冶屋 製作 ヘッダ
	function SmithyCreateHeader() {
		?>
<div style="margin:15px">
<h4>製作工房(Create)<a name="sm"></a></h4>
<div style="width:600px">
<div style="float:left;width:80px;">
<img src="<?php print IMG_CHAR?>mon_053rz.gif" />
</div>
<div style="float:right;width:520px;">
在這裡 可以進行物品的製作！<br />
只要你有素材就可以製作裝備。<br />
加入特殊素材的話可以製作特殊的武器。<br />
哥哥在管理的 <span class="bold">精煉工房</span> 在<a href="?menu=refine">這邊</a>。<br />
<a href="#mat">所持素材一覽</a>
</div>
<div style="clear:both"></div>
</div>
<h4>道具製作<a name="refine"></a></h4>
<div style="margin:0 15px">
<?php 
	}
//////////////////////////////////////////////////
//	製作處理
	function SmithyCreateProcess() {
		if(!$_POST["Create"]) return false;

		// 道具が選擇されていない
		if(!$_POST["ItemNo"]) {
			ShowError("請選擇一個道具製造");
			return false;
		}

		// 道具を讀む
		if(!$item	= LoadItemData($_POST["ItemNo"])) {
			ShowError("error12291703");
			return false;
		}

		// 作れる道具かどうかたしかめる
		if(!HaveNeeds($item,$main->item)) {
			ShowError($item["name"]." 您沒有足夠的原料生產。");
			return false;
		}

		// 追加素材
		if($_POST["AddMaterial"]) {
			// 所持していない場合
			if(!$main->item[$_POST["AddMaterial"]]) {
				ShowError("該素材不能追加。");
				return false;
			}
			// 追加素材の道具デ一タ
			$ADD	= LoadItemData($_POST["AddMaterial"]);
			$main->DeleteItem($_POST["AddMaterial"]);
		}

		// 道具の製作
		// お金を減らす
		//$Price	= $item["buy"];
		$Price	= 0;
		if(!$main->TakeMoney($Price)) {
			ShowError("您沒有足夠的錢。需要".MoneyFormat($Price)."。");
			return false;
		}
		// 素材を減らす
		foreach($item["need"] as $M_item => $M_amount) {
			$main->DeleteItem($M_item,$M_amount);
		}
		include(CLASS_SMITHY);
		$item	= new item($_POST["ItemNo"]);
		$item->CreateItem();
		// 付加效果
		if($ADD["Add"])
			$item->AddSpecial($ADD["Add"]);
		// できた道具を保存する
		$done	= $item->ReturnItem();
		$main->AddItem($done);
		$main->SaveUserItem();

		print("<p>");
		print(ShowItemDetail(LoadItemData($done)));
		
		print("\n<br />好了！</p>\n");
		return true;
	}
//////////////////////////////////////////////////
//	製作表示
	function SmithyCreateShow() {
		//$result	= $main->SmithyCreateProcess();

		$CanCreate	= CanCreate($main);
		include(CLASS_JS_ITEMLIST);
		$CreateList	= new JS_ItemList();
		$CreateList->SetID("create");
		$CreateList->SetName("type_create");

		$CreateList->ListTable("<table cellspacing=\"0\">");// テ一ブルタグのはじまり
		$CreateList->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">製作費用</td><td class=\"align-center td9\">Item</td></tr>"); // テ一ブルの最初と最後の行に表示させるやつ。

		// JSを使用しない。
		if($main->no_JS_itemlist)
			$CreateList->NoJS();
		foreach($CanCreate as $item_no) {
			$item	= LoadItemData($item_no);
			if(!HaveNeeds($item,$main->item))// 素材不足なら次
				continue;
			// NoTable
			//$head	= '<input type="radio" name="ItemNo" value="'.$item_no.'">'.ShowItemDetail($item,false,1,$main->item)."<br />";
			//$CreatePrice	= $item["buy"];
			$CreatePrice	= 0;//
			$head	= '<tr><td class="td7"><input type="radio" name="ItemNo" value="'.$item_no.'"></td>';
			$head	.= '<td class="td7">'.MoneyFormat($CreatePrice).'</td><td class="td8">'.ShowItemDetail($item,false,1,$main->item)."</td>";
			$CreateList->AddItem($item,$head);
		}
		if($head) {
			print($CreateList->GetJavaScript("list"));
			print($CreateList->ShowSelect());
		?>
<form action="?menu=create" method="post">
<div id="list"><?php print $CreateList->ShowDefault()?></div>
<input type="submit" class="btn" name="Create" value="創建">
<input type="reset" class="btn" value="重置">
<input type="hidden" name="Create" value="1"><br />
<?php 
		// 追加素材の表示
		print('<div class="bold u" style="margin-top:15px">追加素材</div>'."\n");
		for($item_no=7000; $item_no<7200; $item_no++) {
			if(!$main->item["$item_no"])
				continue;
			if($item	= LoadItemData($item_no)) {
				print('<input type="radio" name="AddMaterial" value="'.$item_no.'" class="vcent">');
				print(ShowItemDetail($item,$main->item["$item_no"],1)."<br />\n");
			}
		}
		?>
<input type="submit" class="btn" name="Create" value="創建">
<input type="reset" class="btn" value="重置">
</form>
<?php 
		} else {
			print("就目前手上所持有的素材的話什麼也不能作啊。");
		}


		// 所持素材一覽
		print("</div>\n");
		print("<h4>所持素材一覽<a name=\"mat\"></a> <a href=\"#sm\">↑</a></h4>");
		print("<div style=\"margin:0 15px\">");
		for($i=6000; $i<7000; $i++) {
			if(!$main->item["$i"])
				continue;
			$item	= LoadItemData($i);
			ShowItemDetail($item,$main->item["$i"]);
			print("<br />\n");
		}
		?>
</div>
</div>
<?php 
		return $result;
	}