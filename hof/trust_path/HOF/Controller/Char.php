<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Char extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	/**
	 * @var HOF_Class_Char
	 */
	var $char;

	protected $_cache;

	function _main_init()
	{
		$this->user = &HOF_Model_Main::getInstance();

		$this->_cache = new HOF_Class_Array($this->_cache);

		$this->user->CharDataLoadAll();

		$this->_cache->map_subaction = array();
	}

	function _main_input()
	{
		if (isset(HOF::$input->request->char))
		{
			$this->input->action = 'char';

			$this->input->char = HOF::$input->request->char;
		}
	}

	function _main_before()
	{
		parent::_main_before();

		$action = $this->action;

		if ($this->input->action == 'char')
		{
			$this->user->LoadUserItem();

			$this->char = &$this->user->char[$this->input->char];

			if (!$this->char)
			{
				return $this->_main_stop(true);
			}
		}

		$this->_router();

		if ($action != 'default' && !$this->input->char)
		{
			$action = 'default';
		}

		if ($this->input->char)
		{
			$this->_main_setup('char');
		}
		else
		{
			$this->_main_setup($action);
		}

		$this->options['autoView'] = true;
		$this->options['escapeHtml'] = false;

		debug($this->input);
		debug($this->_cache);
		debug($this->output);

		debug($action, $this->action);
	}

	function _router()
	{
		if ($this->input->action == 'char')
		{
			$method_pre = '_main_action_';

			foreach (get_class_methods(__CLASS__) as $method)
			{
				if (strpos($method, $method_pre) === 0)
				{
					$method = str_replace($method_pre, '', $method);

					if ($method != 'char' && $method != 'default')
					{
						$this->_cache->map_subaction[] = $method;
					}
				}
			}

			foreach ($this->_cache->map_subaction as $k => $v)
			{
				if (is_numeric($k))
				{
					$k = $v;
				}

				if (HOF::$input->post->{$k})
				{
					$this->input->{$k} = HOF::$input->post->{$k};

					$_action = $v;
				}
			}
		}

		if ($_action && $_action != $this->input->action && $this->_main_exists($_action))
		{
			$this->input->action = $_action;

			$this->options['autoView'] = false;

			$this->_main_exec_once($this->input->action);
		}
	}

	function _main_result($action, $ret)
	{
		$this->_cache->log['action'][$action][] = $ret;

		if ($action != self::DEFAULT_ACTION && $action != 'char')
		{
			if (1 || $ret === false)
			{
				$this->_main_stop(true);
			}
		}
	}

	function _msg_error($message, $add = 'magrin15')
	{
		$this->output->_msg_error[] = array($message, $add);
	}

	function _msg_result($message, $add = 'magrin15')
	{
		$this->output->_msg_result[] = array($message, $add);
	}

	function _main_action_default()
	{
		//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

		$this->LoginMain();
	}

	function _main_action_char()
	{
		ob_start();

		$this->CharStatProcess();

		$this->CharStatShow();

		$this->output->content = ob_get_clean();
	}

	function _main_after()
	{
		$this->user->fpCloseAll();

		parent::_main_after();
	}

	/**
	 * ステータス上昇
	 */
	function _main_action_stup()
	{
		$Stat = array(
			"Str",
			"Int",
			"Dex",
			"Spd",
			"Luk",
			);

		/**
		 * ステータスポイント超過(ねんのための絶対値)
		 */
		$Sum = 0;

		foreach ($Stat as $v)
		{
			$k = 'up' . $v;
			$this->input->$k = HOF::$input->post->$k;

			$attr = strtolower($v);

			// 最大値を超えないかチェック
			if (MAX_STATUS < ($this->char->{$attr} + $this->input->$k))
			{
				$this->_msg_error("最大ステータス超過(" . MAX_STATUS . ")");

				return false;
			}

			$Sum += abs($this->input->$k);
		}

		if ($Sum == 0) return false;

		if ($this->char->statuspoint < $Sum)
		{
			$this->_msg_error("ステータスポイント超過");
			return false;
		}

		foreach ($Stat as $v)
		{
			$k = 'up' . $v;

			if ($this->input->$k)
			{
				$attr = strtolower($v);
				$name = strtoupper($attr);

				// ステータスを増やす
				$this->char->{$attr} += $this->input->$k;

				$this->_msg_result("{$name} が <span class=\"bold\">" . $this->input->$k . "</span> 上がった。" . ($this->char->{$attr}-$this->input->$k) . " -> " . $this->char->{$attr} . "<br />\n");
			}
		}

		$this->char->SetHpSp();

		// ポイントを減らす。
		$this->char->statuspoint -= $Sum;

		$this->char->SaveCharData($this->user->id);

		return true;
	}

	/**
	 * 配置・他設定(防御)
	 */
	function _main_action_position()
	{
		$this->input->position = HOF::$input->post->position;
		$this->input->guard = HOF::$input->post->guard;

		if ($this->input->position == "front")
		{
			$this->char->position = FRONT;
			$pos = "前衛(Front)";
		}
		else
		{
			$this->char->position = BACK;
			$pos = "後衛(Back)";
		}

		$this->char->guard = $this->input->guard;
		switch ($this->input->guard)
		{
			case "never":
				$guard = "後衛を守らない";
				break;
			case "life25":
				$guard = "体力が 25%以上なら 後衛を守る";
				break;
			case "life50":
				$guard = "体力が 50%以上なら 後衛を守る";
				break;
			case "life75":
				$guard = "体力が 75%以上なら 後衛を守る";
				break;
			case "prob25":
				$guard = "25%の確率で 後衛を守る";
				break;
			case "prob50":
				$guard = "50%の確率で 後衛を守る";
				break;
			case "prob75":
				$guard = "75%の確率で 後衛を守る";
				break;
			default:
				$guard = "必ず後衛を守る";
				break;
		}

		$this->char->SaveCharData($this->user->id);
		$this->_msg_result($this->char->Name() . " の配置を {$pos} に。<br />前衛の時 {$guard} ように設定。\n", "margin15");
		return true;
	}

	/**
	 * 行動設定
	 */
	function _main_action_ChangePattern()
	{
		$max = $this->char->MaxPatterns();

		// 記憶するパターンと技の配列。
		for ($i = 0; $i < $max; $i++)
		{
			$judge[] = $_POST["judge" . $i];

			$quantity_post = (int)HOF::$input->post["quantity" . $i];

			if (4 < strlen($quantity_post))
			{
				$quantity_post = substr($quantity_post, 0, 4);
			}

			$quantity[] = $quantity_post;

			$action[] = HOF::$input->post["skill" . $i];
		}

		//if($this->char->ChangePattern($judge,$action)) {
		if ($this->char->PatternSave($judge, $quantity, $action))
		{
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result("パターン設定保存 完了", "margin15");

			return true;
		}

		$this->_msg_rerror("失敗したなんで？報告してみてください 03050242", "margin15");

		return false;
	}

	/**
	 * 行動設定 兼 模擬戦
	 */
	function _main_action_TestBattle()
	{
		$max = $this->char->MaxPatterns();
		//記憶するパターンと技の配列。
		for ($i = 0; $i < $max; $i++)
		{
			$judge[] = $_POST["judge" . $i];
			$quantity_post = (int)$_POST["quantity" . $i];
			if (4 < strlen($quantity_post))
			{
				$quantity_post = substr($quantity_post, 0, 4);
			}
			$quantity[] = $quantity_post;
			$action[] = $_POST["skill" . $i];
		}
		//if($this->char->ChangePattern($judge,$action)) {
		if ($this->char->PatternSave($judge, $quantity, $action))
		{
			$this->char->SaveCharData($this->user->id);
			$this->user->CharTestDoppel();
		}
	}

	/**
	 * 行動パターンメモ(交換)
	 */
	function _main_action_PatternMemo()
	{
		if ($this->char->ChangePatternMemo())
		{
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result("パターン交換 完了", "margin15");
			return true;
		}
	}

	/**
	 * 指定行に追加
	 */
	function _main_action_AddNewPattern()
	{
		if (!isset($_POST["PatternNumber"])) return false;
		if ($this->char->AddPattern($_POST["PatternNumber"]))
		{
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result("パターン追加 完了", "margin15");
			return true;
		}
	}

	/**
	 * 指定行を削除
	 */
	function _main_action_DeletePattern()
	{
		if (!isset($_POST["PatternNumber"])) return false;
		if ($this->char->DeletePattern($_POST["PatternNumber"]))
		{
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result("パターン削除 完了", "margin15");
			return true;
		}
	}

	/**
	 * 指定箇所だけ装備をはずす
	 */
	function _main_action_remove()
	{
		if (!$_POST["spot"])
		{
			$this->_msg_rerror("装備をはずす箇所が選択されていない", "margin15");
			return false;
		}
		if (!$this->char->{$_POST["spot"]})
		{
			// $this と $this->char の区別注意！
			$this->_msg_rerror("指定された箇所には装備無し", "margin15");
			return false;
		}
		$item = HOF_Model_Data::getItemData($this->char->{$_POST["spot"]});
		if (!$item) return false;
		$this->user->AddItem($this->char->{$_POST["spot"]});
		$this->user->SaveUserItem();
		$this->char->{$_POST["spot"]} = NULL;
		$this->char->user->SaveCharData($this->user->id);
		SHowResult($this->char->Name() . " の {$item[name]} を はずした。", "margin15");
		return true;
	}

	/**
	 * 装備全部はずす
	 */
	function _main_action_remove_all()
	{
		if ($this->char->weapon || $this->char->shield || $this->char->armor || $this->char->item)
		{
			if ($this->char->weapon)
			{
				$this->user->AddItem($this->char->weapon);
				$this->char->weapon = NULL;
			}
			if ($this->char->shield)
			{
				$this->user->AddItem($this->char->shield);
				$this->char->shield = NULL;
			}
			if ($this->char->armor)
			{
				$this->user->AddItem($this->char->armor);
				$this->char->armor = NULL;
			}
			if ($this->char->item)
			{
				$this->user->AddItem($this->char->item);
				$this->char->item = NULL;
			}
			$this->user->SaveUserItem();
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");
			return true;
		}
	}

	/**
	 * 指定物を装備する
	 */
	function _main_action_equip_item()
	{
		$item_no = $_POST["item_no"];
		if (!$this->user->item["$item_no"])
		{ //そのアイテムを所持しているか
			$this->_msg_rerror("Item not exists.", "margin15");
			return false;
		}

		$JobData = HOF_Model_Data::getJobData($this->char->job);
		$item = HOF_Model_Data::getItemData($item_no); //装備しようとしてる物
		if (!in_array($item["type"], $JobData["equip"]))
		{ //それが装備不可能なら?
			$this->_msg_rerror("{$this->char->job_name} can't equip {$item[name]}.", "margin15");
			return false;
		}

		if (false === $return = $this->char->Equip($item))
		{
			$this->_msg_rerror("Handle Over.", "margin15");
			return false;
		}
		else
		{
			$this->user->DeleteItem($item_no);
			foreach ($return as $no)
			{
				$this->user->AddItem($no);
			}
		}

		$this->user->SaveUserItem();
		$this->char->user->SaveCharData($this->user->id);
		$this->_msg_result("{$this->char->name} は {$item[name]} を装備した.", "margin15");
		return true;
	}

	/**
	 * スキル習得
	 */
	function _main_action_learnskill()
	{
		if (!$_POST["newskill"])
		{
			$this->_msg_rerror("スキル未選択", "margin15");
			return false;
		}

		$this->char->SetUser($this->id);
		list($result, $message) = $this->char->LearnNewSkill($_POST["newskill"]);
		if ($result)
		{
			$this->char->SaveCharData();
			$this->_msg_result($message, "margin15");
		}
		else
		{
			$this->_msg_rerror($message, "margin15");
		}
		return true;
	}

	/**
	 * クラスチェンジ(転職)
	 */
	function _main_action_classchange()
	{

		if (!$_POST["job"])
		{
			$this->_msg_rerror("職 未選択", "margin15");
			return false;
		}
		if ($this->char->ClassChange($_POST["job"]))
		{
			// 装備を全部解除
			if ($this->char->weapon || $this->char->shield || $this->char->armor || $this->char->item)
			{
				if ($this->char->weapon)
				{
					$this->user->AddItem($this->char->weapon);
					$this->char->weapon = NULL;
				}
				if ($this->char->shield)
				{
					$this->user->AddItem($this->char->shield);
					$this->char->shield = NULL;
				}
				if ($this->char->armor)
				{
					$this->user->AddItem($this->char->armor);
					$this->char->armor = NULL;
				}
				if ($this->char->item)
				{
					$this->user->AddItem($this->char->item);
					$this->char->item = NULL;
				}
				$this->user->SaveUserItem();
			}
			// 保存
			$this->char->SaveCharData($this->user->id);
			$this->_msg_result("転職 完了", "margin15");
			return true;
		}
		$this->_msg_rerror("failed.", "margin15");
		return false;
	}

	/**
	 * 改名(表示)
	 */
	function _main_action_rename()
	{

		$Name = $this->char->Name();
		$message = <<< EOD
<form action="?char={$_GET[char]}" method="post" class="margin15">
半角英数16文字 (全角1文字=半角2文字)<br />
<input type="text" name="NewName" style="width:160px" class="text" />
<input type="submit" class="btn" name="NameChange" value="Change" />
<input type="submit" class="btn" value="Cancel" />
</form>
EOD;
		print ($message);
		return false;
	}

	/**
	 * 改名(処理)
	 */
	function _main_action_NewName()
	{

		list($result, $return) = CheckString($_POST["NewName"], 16);
		if ($result === false)
		{
			$this->_msg_rerror($return, "margin15");
			return false;
		}
		else
			if ($result === true)
			{
				if ($this->user->DeleteItem("7500", 1) == 1)
				{
					$this->_msg_result($this->char->Name() . " から " . $return . " へ改名しました。", "margin15");
					$this->char->ChangeName($return);
					$this->char->SaveCharData($this->user->id);
					$this->user->SaveUserItem();
					return true;
				}
				else
				{
					$this->_msg_rerror("アイテムがありません。", "margin15");
					return false;
				}
				return true;
			}
	}

	/**
	 * 各種リセットの表示
	 */
	function _main_action_showreset()
	{
		$Name = $this->char->Name();
		print ('<div class="margin15">' . "\n");
		print ("使用するアイテム<br />\n");
		print ('<form action="?char=' . $_GET[char] . '" method="post">' . "\n");
		print ('<select name="itemUse">' . "\n");
		$resetItem = array(
			7510,
			7511,
			7512,
			7513,
			7520);
		foreach ($resetItem as $itemNo)
		{
			if ($this->user->item[$itemNo])
			{
				$item = HOF_Model_Data::getItemData($itemNo);
				print ('<option value="' . $itemNo . '">' . $item[name] . " x" . $this->user->item[$itemNo] . '</option>' . "\n");
			}
		}
		print ("</select>\n");
		print ('<input type="submit" class="btn" name="resetVarious" value="Reset">' . "\n");
		print ('<input type="submit" class="btn" value="Cancel">' . "\n");
		print ('</form>' . "\n");
		print ('</div>' . "\n");
		break;
	}

	/**
	 * 各種リセットの処理
	 */
	function _main_action_resetVarious()
	{

		switch ($_POST["itemUse"])
		{
			case 7510:
				$lowLimit = 1;
				break;
			case 7511:
				$lowLimit = 30;
				break;
			case 7512:
				$lowLimit = 50;
				break;
			case 7513:
				$lowLimit = 100;
				break;
				// skill
			case 7520:
				$skillReset = true;
				break;
		}
		// 石ころをSPD1に戻すアイテムにする
		if ($_POST["itemUse"] == 6000)
		{
			if ($this->user->DeleteItem(6000) == 0)
			{
				$this->_msg_rerror("アイテムがありません。", "margin15");
				return false;
			}
			if (1 < $this->char->spd)
			{
				$dif = $this->char->spd - 1;
				$this->char->spd -= $dif;
				$this->char->statuspoint += $dif;
				$this->char->SaveCharData($this->user->id);
				$this->user->SaveUserItem();
				$this->_msg_result("ポイント還元成功", "margin15");
				return true;
			}
		}
		if ($lowLimit)
		{
			if (!$this->user->item[$_POST["itemUse"]])
			{
				$this->_msg_rerror("アイテムがありません。", "margin15");
				return false;
			}
			if ($lowLimit < $this->char->str)
			{
				$dif = $this->char->str - $lowLimit;
				$this->char->str -= $dif;
				$pointBack += $dif;
			}
			if ($lowLimit < $this->char->int)
			{
				$dif = $this->char->int - $lowLimit;
				$this->char->int -= $dif;
				$pointBack += $dif;
			}
			if ($lowLimit < $this->char->dex)
			{
				$dif = $this->char->dex - $lowLimit;
				$this->char->dex -= $dif;
				$pointBack += $dif;
			}
			if ($lowLimit < $this->char->spd)
			{
				$dif = $this->char->spd - $lowLimit;
				$this->char->spd -= $dif;
				$pointBack += $dif;
			}
			if ($lowLimit < $this->char->luk)
			{
				$dif = $this->char->luk - $lowLimit;
				$this->char->luk -= $dif;
				$pointBack += $dif;
			}
			if ($pointBack)
			{
				if ($this->user->DeleteItem($_POST["itemUse"]) == 0)
				{
					$this->_msg_rerror("アイテムがありません。", "margin15");
					return false;
				}
				$this->char->statuspoint += $pointBack;
				// 装備も全部解除
				if ($this->char->weapon || $this->char->shield || $this->char->armor || $this->char->item)
				{
					if ($this->char->weapon)
					{
						$this->user->AddItem($this->char->weapon);
						$this->char->weapon = NULL;
					}
					if ($this->char->shield)
					{
						$this->user->AddItem($this->char->shield);
						$this->char->shield = NULL;
					}
					if ($this->char->armor)
					{
						$this->user->AddItem($this->char->armor);
						$this->char->armor = NULL;
					}
					if ($this->char->item)
					{
						$this->user->AddItem($this->char->item);
						$this->char->item = NULL;
					}
					$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");
				}
				$this->char->SaveCharData($this->user->id);
				$this->user->SaveUserItem();
				$this->_msg_result("ポイント還元成功", "margin15");
				return true;
			}
			else
			{
				$this->_msg_rerror("ポイント還元失敗", "margin15");
				return false;
			}
		}
		break;
	}

	/**
	 * サヨナラ(表示)
	 */
	function _main_action_byebye()
	{
		$Name = $this->char->Name();
		$message = <<< HTML_BYEBYE
<div class="margin15">
{$Name} を 解雇しますか?<br>
<form action="?char={$_GET[char]}" method="post">
<input type="submit" class="btn" name="kick" value="Yes">
<input type="submit" class="btn" value="No">
</form>
</div>
HTML_BYEBYE;
		print ($message);
		return false;
	}

	/**
	 * キャラ詳細表示から送られたリクエストを処理する
	 * 長い...(100行オーバー)
	 */
	function CharStatProcess()
	{
		switch (true):


				// サヨナラ(処理)
			case ($_POST["kick"]):
				//$this->user->DeleteChar($this->char->birth);
				$this->char->user->DeleteChar();
				$host = $_SERVER['HTTP_HOST'];
				$uri = rtrim(dirname($_SERVER['PHP_SELF']));
				//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				$extra = INDEX;
				header("Location: http://$host$uri/$extra");
				exit;
				break;
		endswitch;
	}

	/**
	 * キャラクター詳細表示・装備変更などなど
	 * 長すぎる...(200行以上)
	 */
	function CharStatShow()
	{

		if (!$this->char)
		{
			print ("Not exists");
			return false;
		}
		// 戦闘用変数の設定。
		$this->char->SetBattleVariable();

		// 職データ
		$JobData = HOF_Model_Data::getJobData($this->char->job);

		// 転職可能な職
		if ($JobData["change"])
		{
			include_once (DATA_CLASSCHANGE);
			foreach ($JobData["change"] as $job)
			{
				if (CanClassChange($this->char, $job)) $CanChange[] = $job; //転職できる候補。
			}
		}

		////// ステータス表示 //////////////////////////////



?>
<form action="?char=<?=

		$this->input->char


?>" method="post" style="padding:5px 0 0 15px">
	<?php

		// その他キャラ
		print ('<div style="padding-top:5px">');
		foreach ($this->user->char as $key => $val)
		{
			//if($key == $this->input->char) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
		}
		print ("</div>");


?>
	<h4>Character Status<a href="?manual#charstat" target="_blank" class="a0">?</a></h4>
	<?php

		$this->char->ShowCharDetail();
		// 改名
		if ($this->user->item["7500"]) print ('<input type="submit" class="btn" name="rename" value="ChangeName">' . "\n");
		// ステータスリセット系
		if ($this->user->item["7510"] || $this->user->item["7511"] || $this->user->item["7512"] || $this->user->item["7513"] || $this->user->item["7520"])
		{
			print ('<input type="submit" class="btn" name="showreset" value="Reset">' . "\n");
		}


?>
	<input type="submit" class="btn" name="byebye" value="Kick">
</form>
<?php

		// ステータス上昇 ////////////////////////////
		if (0 < $this->char->statuspoint)
		{
			print <<< HTML
	<form action="?char=$_GET[char]" method="post" style="padding:0 15px">
	<h4>Status <a href="?manual#statup" target="_blank" class="a0">?</a></h4>
HTML;

			$Stat = array(
				"Str",
				"Int",
				"Dex",
				"Spd",
				"Luk");
			print ("Point : {$this->char->statuspoint}<br />\n");
			foreach ($Stat as $val)
			{
				print ("{$val}:\n");
				print ("<select name=\"up{$val}\" class=\"vcent\">\n");
				for ($i = 0; $i < $this->char->statuspoint + 1; $i++) print ("<option value=\"{$i}\">+{$i}</option>\n");
				print ("</select>");
			}
			print ("<br />");
			print ('<input type="submit" class="btn" name="stup" value="Increase Status">');
			print ("\n");

			print ("</form>\n");
		}


?>
<form action="?char=<?=

		$this->input->char


?>" method="post" style="padding:0 15px">
	<h4>Action Pattern<a href="?manual#jdg" target="_blank" class="a0">?</a></h4>
	<?php

		// Action Pattern 行動判定 /////////////////////////
		$list = HOF_Model_Data::getJudgeList(); // 行動判定条件一覧
		print ("<table cellspacing=\"5\"><tbody>\n");
		for ($i = 0; $i < $this->char->MaxPatterns(); $i++)
		{
			print ("<tr><td>");
			//----- No
			print (($i + 1) . "</td><td>");
			//----- JudgeSelect(判定の種類)
			print ("<select name=\"judge" . $i . "\">\n");
			foreach ($list as $val)
			{ //判断のoption
				$exp = HOF_Model_Data::getJudgeData($val);
				print ("<option value=\"{$val}\"" . ($this->char->judge[$i] == $val ? " selected" : NULL) . ($exp["css"] ? ' class="select0"' : NULL) . ">" . ($exp["css"] ? '&nbsp;' : '&nbsp;&nbsp;&nbsp;') . "{$exp[exp]}</option>\n");
			}
			print ("</select>\n");
			print ("</td><td>\n");
			//----- 数値(量)
			print ("<input type=\"text\" name=\"quantity" . $i . "\" maxlength=\"4\" value=\"" . $this->char->quantity[$i] . "\" style=\"width:56px\" class=\"text\">");
			print ("</td><td>\n");
			//----- //SkillSelect(技の種類)
			print ("<select name=\"skill" . $i . "\">\n");
			foreach ($this->char->skill as $val)
			{ //技のoption
				$skill = HOF_Model_Data::getSkill($val);
				print ("<option value=\"{$val}\"" . ($this->char->action[$i] == $val ? " selected" : NULL) . ">");
				print ($skill["name"] . (isset($skill["sp"]) ? " - (SP:{$skill[sp]})" : NULL));
				print ("</option>\n");
			}
			print ("</select>\n");
			print ("</td><td>\n");
			print ('<input type="radio" name="PatternNumber" value="' . $i . '">');
			print ("</td></tr>\n");
		}
		print ("</tbody></table>\n");


?>
	<input type="submit" class="btn" value="Set Pattern" name="ChangePattern">
	<input type="submit" class="btn" value="Set & Test" name="TestBattle">
	&nbsp;<a href="?simulate">Simulate</a><br />
	<input type="submit" class="btn" value="Switch Pattern" name="PatternMemo">
	<input type="submit" class="btn" value="Add" name="AddNewPattern">
	<input type="submit" class="btn" value="Delete" name="DeletePattern">
</form>
<form action="?char=<?=

		$this->input->char


?>" method="post" style="padding:0 15px">
	<h4>Position & Guarding<a href="?manual#posi" target="_blank" class="a0">?</a></h4>
	<table>
		<tbody>
			<tr>
				<td>位置(Position) :</td>
				<td><input type="radio" class="vcent" name="position" value="front"<?php

		($this->char->position == "front" ? print (" checked") : NULL)


?>>
					前衛(Front)</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" class="vcent" name="position" value="back"<?php

		($this->char->position == "back" ? print (" checked") : NULL)


?>>
					後衛(Backs)</td>
			</tr>
			<tr>
				<td>護衛(Guarding) :</td>
				<td><select name="guard">
						<?php

		// 前衛の時の後衛守り //////////////////////////////
		$option = array(
			/*
			"always"=> "Always",
			"never"	=> "Never",
			"life25"	=> "If life more than 25%",
			"life50"	=> "If life more than 50%",
			"life75"	=> "If life more than 75%",
			"prob25"	=> "Probability of 25%",
			"prpb50"	=> "Probability of 50%",
			"prob75"	=> "Probability of 75%",
			*/
			"always" => "必ず守る",
			"never" => "守らない",
			"life25" => "体力が 25%以上なら 守る",
			"life50" => "体力が 50%以上なら 守る",
			"life75" => "体力が 75%以上なら 守る",
			"prob25" => "25%の確率で 守る",
			"prpb50" => "50%の確率で 守る",
			"prob75" => "75%の確率で 守る",
			);
		foreach ($option as $key => $val) print ("<option value=\"{$key}\"" . ($this->char->guard == $key ? " selected" : NULL) . ">{$val}</option>");


?>
					</select></td>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="btn" value="Set">
</form>
<?php

		// 装備中の物表示 ////////////////////////////////
		$weapon = HOF_Model_Data::getItemData($this->char->weapon);
		$shield = HOF_Model_Data::getItemData($this->char->shield);
		$armor = HOF_Model_Data::getItemData($this->char->armor);
		$item = HOF_Model_Data::getItemData($this->char->item);

		$handle = 0;
		$handle = $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item["handle"];


?>
<div style="margin:0 15px">
	<h4>Equipment<a href="?manual#equip" target="_blank" class="a0">?</a></h4>
	<div class="bold u">
		Current Equip's
	</div>
	<table>
		<tr>
			<td class="dmg" style="text-align:right">Atk :</td>
			<td class="dmg"><?=

		$this->char->atk[0]


?></td>
		</tr>
		<tr>
			<td class="spdmg" style="text-align:right">Matk :</td>
			<td class="spdmg"><?=

		$this->char->atk[1]


?></td>
		</tr>
		<tr>
			<td class="recover" style="text-align:right">Def :</td>
			<td class="recover"><?=

		$this->char->def[0] . " + " . $this->char->def[1]


?></td>
		</tr>
		<tr>
			<td class="support" style="text-align:right">Mdef :</td>
			<td class="support"><?=

		$this->char->def[2] . " + " . $this->char->def[3]


?></td>
		</tr>
		<tr>
			<td class="charge" style="text-align:right">handle :</td>
			<td class="charge"><?=

		$handle


?>
				/
				<?=

		$this->char->GetHandle()


?></td>
		</tr>
	</table>
	<form action="?char=<?=

		$this->input->char


?>" method="post">
		<table>
			<tr>
				<td class="align-right">Weapon :</td>
				<td><input type="radio" class="vcent" name="spot" value="weapon">
					<?php

		HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($this->char->weapon));


?></td>
			</tr>
			<tr>
				<td class="align-right">Shield :</td>
				<td><input type="radio" class="vcent" name="spot" value="shield">
					<?php

		HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($this->char->shield));


?></td>
			</tr>
			<tr>
				<td class="align-right">Armor :</td>
				<td><input type="radio" class="vcent" name="spot" value="armor">
					<?php

		HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($this->char->armor));


?></td>
			</tr>
			<tr>
				<td class="align-right">Item :</td>
				<td><input type="radio" class="vcent" name="spot" value="item">
					<?php

		HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($this->char->item));


?></td>
			</tr>
				</tbody>
		</table>
		<input type="submit" class="btn" name="remove" value="Remove">
		<input type="submit" class="btn" name="remove_all" value="Remove All">
	</form>
</div>
<?php

		// 装備可能な物表示 ////////////////////////////////
		if ($JobData["equip"]) $EquipAllow = array_flip($JobData["equip"]); //装備可能な物リスト(反転)
		else  $EquipAllow = array(); //装備可能な物リスト(反転)
		$Equips = array(
			"Weapon" => "2999",
			"Shield" => "4999",
			"Armor" => "5999",
			"Item" => "9999");

		print ("<div style=\"padding:15px 15px 0 15px\">\n");
		print ("\t<div class=\"bold u\">Stock & Allowed to Equip</div>\n");
		if ($this->user->item)
		{

			$EquipList = new HOF_Class_Item_Style_List();
			$EquipList->SetID("equip");
			$EquipList->SetName("type_equip");
			// JSを使用しない。
			if ($this->user->no_JS_itemlist) $EquipList->NoJS();
			reset($this->user->item); //これが無いと装備変更時に表示されない
			foreach ($this->user->item as $key => $val)
			{
				$item = HOF_Model_Data::getItemData($key);
				// 装備できないので次
				if (!isset($EquipAllow[$item["type"]])) continue;
				$head = '<input type="radio" name="item_no" value="' . $key . '" class="vcent">';
				$head .= HOF_Class_Item::ShowItemDetail($item, $val, true) . "<br />";
				$EquipList->AddItem($item, $head);
			}
			print ($EquipList->GetJavaScript("list0"));
			print ($EquipList->ShowSelect());
			print ('<form action="?char=' . $this->input->char . '" method="post">' . "\n");
			print ('<div id="list0">' . $EquipList->ShowDefault() . '</div>' . "\n");
			print ('<input type="submit" class="btn" name="equip_item" value="Equip">' . "\n");
			print ("</form>\n");
		}
		else
		{
			print ("No items.<br />\n");
		}
		print ("</div>\n");


		/*
		print("\t<table><tbody><tr><td colspan=\"2\">\n");
		print("\t<span class=\"bold u\">Stock & Allowed to Equip</span></td></tr>\n");
		if($this->user->item):
		reset($this->user->item);//これが無いと装備変更時に表示されない
		foreach($Equips as $key => $val) {
		print("\t<tr><td class=\"align-right\" valign=\"top\">\n");
		print("\t{$key} :</td><td>\n");
		while( substr(key($this->user->item),0,4) <= $val && substr(current($this->user->item),0,4) !== false ) {
		$item	= HOF_Model_Data::getItemData(key($this->user->item));
		if(!isset( $EquipAllow[ $item["type"] ] )) {
		next($this->user->item);
		continue;
		}
		print("\t");
		print('<input type="radio" class="vcent" name="item_no" value="'.key($this->user->item).'">');
		print("\n\t");
		print(current($this->user->item)."x");
		HOF_Class_Item::ShowItemDetail($item);
		print("<br>\n");
		next($this->user->item);
		}
		print("\t</td></tr>\n");
		}
		else:
		print("<tr><td>No items.</td></tr>");
		endif;
		print("\t</tbody></table>\n");
		*/


?>
<form action="?char=<?=

		$this->input->char


?>" method="post" style="padding:0 15px">
	<h4>Skill<a href="?manual#skill" target="_blank" class="a0">?</a></h4>
	<?php

		// スキル表示 //////////////////////////////////////
		//include(DATA_SKILL);//ActionPatternに移動
		include_once (DATA_SKILL_TREE);
		if ($this->char->skill)
		{
			print ('<div class="u bold">Mastered</div>');
			print ("<table><tbody>");
			foreach ($this->char->skill as $val)
			{
				print ("<tr><td>");
				$skill = HOF_Model_Data::getSkill($val);
				HOF_Class_Skill::ShowSkillDetail($skill);
				print ("</td></tr>");
			}
			print ("</tbody></table>");
			print ('<div class="u bold">Learn New</div>');
			print ("Skill Point : {$this->char->skillpoint}");
			print ("<table><tbody>");
			$tree = LoadSkillTree($this->char);
			foreach (array_diff($tree, $this->char->skill) as $val)
			{
				print ("<tr><td>");
				$skill = HOF_Model_Data::getSkill($val);
				HOF_Class_Skill::ShowSkillDetail($skill, 1);
				print ("</td></tr>");
			}
			print ("</tbody></table>");
			//dump($this->char->skill);
			//dump($tree);
			print ('<input type="submit" class="btn" name="learnskill" value="Learn">' . "\n");
			print ('<input type="hidden" name="learnskill" value="1">' . "\n");
		}
		// 転職 ////////////////////////////////////////////
		if ($CanChange)
		{


?>
</form>
<form action="?char=<?=

			$this->input->char


?>" method="post" style="padding:0 15px">
	<h4>ClassChange</h4>
	<table>
		<tbody>
			<tr>
				<?php

			foreach ($CanChange as $job)
			{
				print ("<td valign=\"bottom\" style=\"padding:5px 30px;text-align:center\">");
				$JOB = HOF_Model_Data::getJobData($job);
				print ('<img src="' . IMG_CHAR . $JOB["img_" . ($this->char->gender ? "female" : "male")] . '">' . "<br />\n"); //画像
				print ('<input type="radio" value="' . $job . '" name="job">' . "<br />\n");
				print ($JOB["name_" . ($this->char->gender ? "female" : "male")]);
				print ("</td>");
			}


?>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="btn" name="classchange" value="ClassChange">
	<input type="hidden" name="classchange" value="1">
	<?php

		}


?>
</form>
<?php

		//その他キャラ
		print ('<div  style="padding:15px">');
		foreach ($this->user->char as $key => $val)
		{
			//if($key == $this->input->char) continue;//表示中キャラスキップ
			echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
		}
		print ('</div>');
	}

	/**
	 * ログインした画面
	 */
	function LoginMain()
	{
		$this->ShowTutorial();
		$this->ShowMyCharacters();

		RegularControl($this->user->id);
	}

	/**
	 * 自分のキャラを表示する
	 */
	function ShowMyCharacters($array = NULL)
	{
		// $array ← 色々受け取る
		if (!$this->user->char) return false;

		$divide = (count($this->user->char) < CHAR_ROW ? count($this->user->char) : CHAR_ROW);
		$width = floor(100 / $divide); //各セル横幅

		$this->output->width = $this->user->width;
		$this->output->chars = $this->user->char;
	}

	/**
	 * チュウトリアル
	 */
	function ShowTutorial()
	{
		$last = $this->user->last;
		$start = substr($this->user->start, 0, 10);
		$term = 60 * 60 * 1;

		if (($last - $start) < $term)
		{
			$this->output->show_tutorial = true;
		}
	}

}


?>