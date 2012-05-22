<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Smithy extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _init()
	{
		$this->user = &HOF::user();
	}

	function _main_before()
	{
		$this->_input();

		$this->user->LoadUserItem();
	}

	function _input()
	{
		$this->input->Create = $_POST["Create"];
		$this->input->ItemNo = $_POST["ItemNo"];

		$this->input->AddMaterial = $_POST["AddMaterial"];

		$this->input->refine = $_POST["refine"];
		$this->input->item_no = $_POST["item_no"];

		$this->input->timesA = $_POST["timesA"];
		$this->input->timesB = $_POST["timesB"];

		//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	}

	// 製作
	function _main_action_create()
	{

	}

	function _create()
	{
		if ($this->SmithyCreateProcess()) $this->user->SaveData();

		$this->user->fpclose_all();
		$this->SmithyCreateShow();
	}

	// 精錬
	function _main_action_refine()
	{

	}

	function _refine()
	{
		if ($this->SmithyRefineProcess()) $this->user->SaveData();

		$this->user->fpclose_all();
		$result = $this->SmithyRefineShow();
	}

	/**
	 * 製作処理
	 */
	function SmithyCreateProcess()
	{
		if (!$this->input->Create) return false;

		// アイテムが選択されていない
		if (!$this->input->ItemNo)
		{
			HOF_Helper_Global::ShowError("製作するアイテムを選んでください");
			return false;
		}

		// アイテムを読む
		if (!$item = HOF_Model_Data::getItemData($this->input->ItemNo))
		{
			HOF_Helper_Global::ShowError("error12291703");
			return false;
		}

		// 作れるアイテムかどうかたしかめる
		if (!HOF_Class_Item_Create::HaveNeeds($item, $this->user->item))
		{
			HOF_Helper_Global::ShowError($item["name"] . " を製作する素材が足りません。");
			return false;
		}

		// 追加素材
		if ($this->input->AddMaterial)
		{
			// 所持していない場合
			if (!$this->user->item[$this->input->AddMaterial])
			{
				HOF_Helper_Global::ShowError("その追加素材はありません。");
				return false;
			}
			// 追加素材のアイテムデータ
			$ADD = HOF_Model_Data::getItemData($this->input->AddMaterial);
			$this->DeleteItem($this->input->AddMaterial);
		}

		// アイテムの製作
		// お金を減らす
		//$Price	= $item["buy"];
		$Price = 0;
		if (!$this->user->TakeMoney($Price))
		{
			HOF_Helper_Global::ShowError("お金が足りません。" . HOF_Helper_Global::MoneyFormat($Price) . "必要です。");
			return false;
		}
		// 素材を減らす
		foreach ($item["need"] as $M_item => $M_amount)
		{
			$this->user->DeleteItem($M_item, $M_amount);
		}

		$item = new HOF_Class_Item_Smithy($_POST["ItemNo"]);
		$item->CreateItem();
		// 付加効果
		if ($ADD["Add"]) $item->AddSpecial($ADD["Add"]);
		// できたアイテムを保存する
		$done = $item->ReturnItem();
		$this->user->AddItem($done);
		$this->user->SaveUserItem();

		print ("<p>");
		print (HOF_Class_Item::newInstance($done)->html());

		print ("\n<br />ができたぜ！</p>\n");
		return true;
	}

	/**
	 * 製作表示
	 */
	function SmithyCreateShow()
	{
		$CanCreate = HOF_Class_Item_Create::CanCreate($this->user);

		$CreateList = new HOF_Class_Item_Style_List();
		$CreateList->SetID("create");
		$CreateList->SetName("type_create");

		$CreateList->ListTable("<table cellspacing=\"0\">"); // テーブルタグのはじまり
		$CreateList->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">製作費</td><td class=\"align-center td9\">Item</td></tr>"); // テーブルの最初と最後の行に表示させるやつ。

		// JSを使用しない。
		if ($this->user->options['no_JS_itemlist']) $CreateList->NoJS();
		foreach ($CanCreate as $item_no)
		{
			$item = HOF_Model_Data::getItemData($item_no);
			if (!HOF_Class_Item_Create::HaveNeeds($item, $this->user->item))
			{
				// 素材不足なら次
 					continue;
				}

			// NoTable

			$CreatePrice = 0; //
			$head = '<tr><td class="td7"><input type="radio" name="ItemNo" value="' . $item_no . '"></td>';
			$head .= '<td class="td7">' . HOF_Helper_Global::MoneyFormat($CreatePrice) . '</td><td class="td8">' . HOF_Class_Item::newInstance($item)->html(false, 1, $this->user->item) . "</td>";
			$CreateList->AddItem($item, $head);
		}
		if ($head)
		{
			print ($CreateList->GetJavaScript("list"));
			print ($CreateList->ShowSelect());


?>
		<form action="<?php e(BASE_URL) ?>?menu=create" method="post">
			<div id="list">
				<?=

			$CreateList->ShowDefault()


?>
			</div>
			<input type="submit" class="btn" name="Create" value="Create">
			<input type="reset" class="btn" value="Reset">
			<input type="hidden" name="Create" value="1">
			<br />
			<?php

			// 追加素材の表示
			print ('<div class="bold u" style="margin-top:15px">追加素材</div>' . "\n");
			for ($item_no = 7000; $item_no < 7200; $item_no++)
			{
				if (!$this->user->item["$item_no"]) continue;
				if ($item = HOF_Model_Data::getItemData($item_no))
				{
					print ('<input type="radio" name="AddMaterial" value="' . $item_no . '" class="vcent">');
					print (HOF_Class_Item::newInstance($item)->html($this->user->item["$item_no"], 1) . "<br />\n");
				}
			}


?>
			<input type="submit" class="btn" name="Create" value="Create">
			<input type="reset" class="btn" value="Reset">
		</form>
		<?php

		}
		else
		{
			print ("あんたが持ってる素材じゃ何も作れそうに無いな。");
		}


		// 所持素材一覧
		print ("</div>\n");
		print ("<h4>所持素材一覧<a name=\"mat\"></a> <a href=\"#sm\">↑</a></h4>");
		print ("<div style=\"margin:0 15px\">");
		for ($i = 6000; $i < 7000; $i++)
		{
			if (!$this->user->item["$i"]) continue;

			HOF_Class_Item::newInstance($i)->html($this->user->item["$i"]);
			print ("<br />\n");
		}

		echo '</div></div>';
	}

	/**
	 * 鍛冶屋処理(精錬)
	 */
	function SmithyRefineProcess()
	{
		if (!$this->input->refine) return false;
		if (!$this->input->item_no)
		{
			HOF_Helper_Global::ShowError("Select Item.");
			return false;
		}
		// アイテムが読み込めない場合
		if (!$item = HOF_Model_Data::getItemData($this->input->item_no))
		{
			HOF_Helper_Global::ShowError("Failed to load item data.");
			return false;
		}
		// アイテムを所持していない場合
		if (!$this->user->item[$this->input->item_no])
		{
			HOF_Helper_Global::ShowError("Item \"{$item[name]}\" doesn't exists.");
			return false;
		}
		// 回数が指定されていない場合
		if ($this->input->timesA < $this->input->timesB) $times = $this->input->timesB;
		else  $times = $this->input->timesA;
		if (!$times || $times < 1 || (REFINE_LIMIT) < $times)
		{
			HOF_Helper_Global::ShowError("times?");
			return false;
		}

		$obj_item = new HOF_Class_Item_Smithy($this->input->item_no);
		// そのアイテムが精錬できない場合
		if (!$obj_item->CanRefine())
		{
			HOF_Helper_Global::ShowError("Cant refine \"{$item[name]}\"");
			return false;
		}
		// ここから精錬を始める処理
		$this->user->DeleteItem($this->input->item_no); // アイテムは消えるか変化するので消す
		$Price = round($item["buy"] / 2);
		// 最大精錬数の調整。
		if (REFINE_LIMIT < ($item["refine"] + $times))
		{
			$times = REFINE_LIMIT - $item["refine"];
		}
		$Trys = 0;
		for ($i = 0; $i < $times; $i++)
		{
			// お金を引く
			if ($this->user->TakeMoney($Price))
			{
				$MoneySum += $Price;
				$Trys++;

				list($bool, $message) = $obj_item->ItemRefine();

				print $message;

				if (!$bool)
				{ //精錬する(false=失敗なので終了する)
					break;
				}
				// お金が途中でなくなった場合。
			}
			else
			{
				HOF_Helper_Global::ShowError("Not enough money.<br />\n");
				$this->user->AddItem($obj_item->ReturnItem());
				break;
			}
			// 指定回数精錬を成功しきった場合。
			if ($i == ($times - 1))
			{
				$this->user->AddItem($obj_item->ReturnItem());
			}
		}
		print ("Money Used : " . HOF_Helper_Global::MoneyFormat($Price) . " x " . $Trys . " = " . HOF_Helper_Global::MoneyFormat($MoneySum) . "<br />\n");
		$this->user->SaveUserItem();
		return true;
		/*// お金が足りてるか計算
		$Price	= round($item["buy"]/2);
		$MoneyNeed	= $times * $Price;
		if($this->user->money < $MoneyNeed) {
		HOF_Helper_Global::ShowError("Your request needs ".HOF_Helper_Global::MoneyFormat($MoneyNeed));
		return false;
		}*/

	}

	/**
	 * 鍛冶屋表示
	 */
	function SmithyRefineShow()
	{
		// ■精錬処理
		//$Result	= $this->SmithyRefineProcess();

		// 精錬可能な物の表示
		if ($this->user->item)
		{

			$possible = HOF_Model_Data::getCanRefineType();
			$possible = array_flip($possible);
			//配列の先頭の値が"0"なので1にする(isset使わずにtrueにするため)
			$possible[key($possible)]++;

			$goods = new HOF_Class_Item_Style_List();
			$goods->SetID("my");
			$goods->SetName("type");

			$goods->ListTable("<table cellspacing=\"0\">"); // テーブルタグのはじまり
			$goods->ListTableInsert("<tr><td class=\"td9\"></td><td class=\"align-center td9\">精錬費</td><td class=\"align-center td9\">Item</td></tr>"); // テーブルの最初と最後の行に表示させるやつ。

			// JSを使用しない。
			if ($this->user->options['no_JS_itemlist']) $goods->NoJS();
			foreach ($this->user->item as $no => $val)
			{
				$item = HOF_Model_Data::getItemData($no);
				// 精錬可能な物だけ表示させる。
				if (!$possible[$item["type"]]) continue;
				$price = $item["buy"] / 2;

				$string = '<tr>';
				$string .= '<td class="td7"><input type="radio" class="vcent" name="item_no" value="' . $no . '">';
				$string .= '</td><td class="td7">' . HOF_Helper_Global::MoneyFormat($price) . '</td><td class="td8">' . HOF_Class_Item::newInstance($item)->html($val, 1) . "<td>";
				$string .= "</tr>";

				$goods->AddItem($item, $string);
			}
			// JavaScript部分の書き出し
			print ($goods->GetJavaScript("list"));
			print ('精錬可能な物一覧');
			// 種類のセレクトボックス
			print ($goods->ShowSelect());
			print ('<form action="'.BASE_URL.'?menu=refine" method="post">' . "\n");
			// [Refine]button
			print ('<input type="submit" value="Refine" name="refine" class="btn">' . "\n");
			// 精錬回数の指定
			print ('回数 : <select name="timesA">' . "\n");
			for ($i = 1; $i < 11; $i++)
			{
				print ('<option value="' . $i . '">' . $i . '</option>');
			}
			print ('</select>' . "\n");
			// リストの表示
			print ('<div id="list">' . $goods->ShowDefault() . '</div>' . "\n");
			// [Refine]button
			print ('<input type="submit" value="Refine" name="refine" class="btn">' . "\n");
			print ('<input type="hidden" value="1" name="refine">' . "\n");
			// 精錬回数の指定
			print ('回数 : <select name="timesB">' . "\n");
			for ($i = 1; $i < (REFINE_LIMIT + 1); $i++)
			{
				print ('<option value="' . $i . '">' . $i . '</option>');
			}
			print ('</select>' . "\n");
			print ('</form>' . "\n");
		}
		else
		{
			print ("No items<br />\n");
		}
		print ("</div>\n");


?>
</div>
<?php

	}

}


?>