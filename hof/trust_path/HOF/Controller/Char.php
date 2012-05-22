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
		$this->user = &HOF::user();

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
	}

	function _router()
	{
		if ($this->input->action == 'char')
		{
			$list = $this->_main_list_action(array('default', 'char'));

			if (!empty($list))
			{
				$this->_cache->map_subaction = array_merge($this->_cache->map_subaction, array_values($list));
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

	/**
	 * ログインした画面
	 */
	function _main_action_default()
	{
		//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

		$this->ShowTutorial();
		$this->ShowMyCharacters();

		HOF_Helper_Global::RegularControl($this->user->id);
	}

	function _main_action_char()
	{
		ob_start();

		$this->CharStatShow();

		$this->output->content = ob_get_clean();

		$char_list = array();

		foreach ($this->user->char as $key => $val)
		{
			$char_list[$key] = $val->name;
		}

		$this->output->char_list = $char_list;
	}

	function _main_after()
	{
		$this->user->fpclose_all();

		parent::_main_after();
	}

	/**
	 * ステータス上昇
	 */
	function _main_action_stup()
	{
		$Stat = HOF_Model_Data::getChatAttrBaseList();

		/**
		 * ステータスポイント超過(ねんのための絶対値)
		 */
		$Sum = 0;

		foreach ($Stat as $v)
		{
			$k = 'up' . ucfirst($v);
			$this->input->$k = HOF::$input->post->$k;

			// 最大値を超えないかチェック
			if (MAX_STATUS < ($this->char->{$v} + $this->input->$k))
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
			$k = 'up' . ucfirst($v);

			if ($this->input->$k)
			{
				$name = strtoupper($v);

				$this->_msg_result("{$name} が <span class=\"bold\">" . $this->input->$k . "</span> 上がった。" . ($this->char->{$v}) . " -> " . ($this->char->{$v} += $this->input->$k) . "<br />\n");
			}
		}

		$this->char->hpsp();

		// ポイントを減らす。
		$this->char->statuspoint -= $Sum;

		$this->char->SaveCharData();

		return true;
	}

	/**
	 * 配置・他設定(防御)
	 */
	function _main_action_position()
	{
		$this->input->position = HOF::$input->post->position;
		$this->input->guard = HOF::$input->post->guard;

		if ($this->input->position == POSITION_FRONT)
		{
			$this->char->position = POSITION_FRONT;
			$pos = "前衛(Front)";
		}
		else
		{
			$this->char->position = POSITION_BACK;
			$pos = "後衛(Back)";
		}

		/**
		 * 前衛の時の後衛守り
		 */
		$v = HOF_Model_Data::getGuardData($this->input->guard);

		$this->input->guard = $v['no'];
		$guard = $v['info']['desc'];

		$this->char->guard = $this->input->guard;

		$this->char->SaveCharData();

		$this->_msg_result($this->char->Name() . " の配置を {$pos} に。<br />前衛の時 {$guard} ように設定。\n", "margin15");

		return true;
	}

	/**
	 * 行動設定
	 */
	function _main_action_pattern_change()
	{
		$pattern = $this->_pattern_input();

		if ($this->char->pattern($pattern))
		{
			$this->char->SaveCharData();
			$this->_msg_result("パターン設定保存 完了", "margin15");

			return true;
		}

		$this->_msg_error("失敗したなんで？報告してみてください 03050242", "margin15");

		return false;
	}

	function _pattern_input()
	{
		$max = $this->char->pattern_max();

		$pattern = array();

		// 記憶するパターンと技の配列。
		for ($i = 0; $i < $max; $i++)
		{
			$quantity_post = (int)HOF::$input->post["quantity" . $i];

			if (4 < strlen($quantity_post))
			{
				$quantity_post = substr($quantity_post, 0, 4);
			}

			$pattern[$i]['judge'] = HOF::$input->post["judge" . $i];
			$pattern[$i]['quantity'] = $quantity_post;
			$pattern[$i]['action'] = HOF::$input->post["skill" . $i];
		}

		return $pattern;
	}

	/**
	 * 行動設定 兼 模擬戦
	 */
	function _main_action_TestBattle()
	{
		$pattern = $this->_pattern_input();

		if ($this->char->pattern($pattern))
		{
			$this->char->SaveCharData();

			if ($this->input->TestBattle)
			{
				HOF_Helper_Battle::DoppelBattle(array($this->char));
			}
		}
	}

	/**
	 * 行動パターンメモ(交換)
	 */
	function _main_action_pattern_memo()
	{
		if ($this->char->pattern_switch())
		{
			$this->char->SaveCharData();
			$this->_msg_result("パターン交換 完了", "margin15");
			return true;
		}
	}

	/**
	 * 指定行に追加
	 */
	function _main_action_pattern_insert()
	{
		$this->input->pattern_no = HOF::$input->post["pattern_no"];

		if (!isset($this->input->pattern_no)) return false;

		if ($this->char->pattern_insert($this->input->pattern_no, null, true))
		{
			$this->char->SaveCharData();
			$this->_msg_result("パターン追加 完了", "margin15");

			return true;
		}
	}

	/**
	 * 指定行を削除
	 */
	function _main_action_pattern_remove()
	{
		$this->input->pattern_no = HOF::$input->post["pattern_no"];

		if (!isset($this->input->pattern_no)) return false;

		if ($this->char->pattern_remove($this->input->pattern_no))
		{
			$this->char->SaveCharData();
			$this->_msg_result("パターン削除 完了", "margin15");
			return true;
		}
	}

	/**
	 * 指定箇所だけ装備をはずす
	 */
	function _main_action_remove()
	{
		$this->input->spot = HOF::$input->post["spot"];

		if (!$this->input->spot)
		{
			$this->_msg_error("装備をはずす箇所が選択されていない", "margin15");

			return false;
		}

		if (!$this->char->{$this->input->spot})
		{
			// $this と $this->char の区別注意！
			$this->_msg_error("指定された箇所には装備無し", "margin15");

			return false;
		}

		$item = HOF_Model_Data::getItemData($this->char->{$this->input->spot});
		if (!$item) return false;

		$this->user->AddItem($this->char->{$this->input->spot});
		$this->user->SaveUserItem();

		$this->char->{$this->input->spot} = NULL;
		$this->char->SaveCharData();

		$this->_msg_result($this->char->Name() . " の {$item[name]} を はずした。", "margin15");

		return true;
	}

	/**
	 * 装備全部はずす
	 */
	function _main_action_remove_all()
	{
		if ($list = $this->char->unequip('all'))
		{
			$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");

			foreach($list as $no)
			{
				if (!$no) continue;

				$_item = HOF_Model_Data::newItem($no);

				$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");

				$this->user->AddItem($no);
			}

			return true;
		}

		return false;

		/*
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
			$this->char->SaveCharData();
			$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");
			return true;
		}
		*/
	}

	/**
	 * 指定物を装備する
	 */
	function _main_action_equip_item()
	{
		$this->input->item_no = HOF::$input->post["item_no"];

		if (!$this->user->item[$this->input->item_no])
		{
			//そのアイテムを所持しているか
			$this->_msg_error("Item not exists.", "margin15");
			return false;
		}

		$JobData = HOF_Model_Data::getJobData($this->char->job);

		// 装備しようとしてる物
		$item = HOF_Model_Data::getItemData($this->input->item_no);

		if (!in_array($item["type"], $JobData["equip"]))
		{
			//それが装備不可能なら?
			$this->_msg_error("{$this->char->job_name} can't equip {$item[name]}.", "margin15");
			return false;
		}

		list ($fail, $return) = $this->char->Equip($item);

		if ($fail)
		{
			$this->_msg_error("Handle Over.", "margin15");
		}
		else
		{
			$this->user->DeleteItem($this->input->item_no);
		}

		foreach ($return as $no)
		{
			if (!$no) continue;

			$_item = HOF_Model_Data::newItem($no);

			$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");

			$this->user->AddItem($no);
		}

		$this->user->SaveUserItem();

		$this->char->SaveCharData();

		if (!$fail)
		{
			$this->_msg_result("{$this->char->name} は {$item[name]} を装備した.", "margin15");
		}

		return $fail ? false : true;
	}

	/**
	 * スキル習得
	 */
	function _main_action_learnskill()
	{
		$this->input->newskill = HOF::$input->post["newskill"];

		if (!$this->input->newskill)
		{
			$this->_msg_error("スキル未選択", "margin15");
			return false;
		}

		$this->char->SetUser($this->user->id);

		list($result, $message) = $this->char->LearnNewSkill($this->input->newskill);

		if ($result)
		{
			$this->char->SaveCharData();
			$this->_msg_result($message, "margin15");
		}
		else
		{
			$this->_msg_error($message, "margin15");
		}

		return true;
	}

	/**
	 * クラスチェンジ(転職)
	 */
	function _main_action_job_change()
	{
		$this->input->job = HOF::$input->post["job"];

		if (!$this->input->job)
		{
			$this->_msg_error("職 未選択", "margin15");
			return false;
		}

		if ($v = $this->char->job_change_to($this->input->job))
		{
			if (!empty($v[1]))
			{
				/**
				 * 装備を全部解除
				 */
				foreach((array)$v[1] as $item)
				{
					$this->user->AddItem($item);
				}

				$this->user->SaveUserItem();
			}

			// 保存
			$this->char->SaveCharData();

			$this->_msg_result("転職 完了", "margin15");

			return true;
		}

		$this->_msg_error("failed.", "margin15");
		return false;
	}

	/**
	 * 改名(表示)
	 */
	function _main_action_rename()
	{
		$Name = $this->char->Name();

		$message = <<< EOD
<form action="{BASE_URL}?char={$_GET[char]}" method="post" class="margin15">
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

		list($result, $return) = HOF_Helper_Global::CheckString(HOF::$input->post["NewName"], 16);

		if ($result === false)
		{
			$this->_msg_error($return, "margin15");
			return false;
		}
		else
		{
			if ($result === true)
			{
				if ($this->user->DeleteItem("7500", 1) == 1)
				{
					$this->_msg_result($this->char->Name() . " から " . $return . " へ改名しました。", "margin15");

					$this->char->ChangeName($return, true);
					$this->char->SaveCharData();

					$this->user->SaveUserItem();

					return true;
				}
				else
				{
					$this->_msg_error("アイテムがありません。", "margin15");
					return false;
				}
				return true;
			}
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
		print ('<form action="'.BASE_URL.'?char=' . $_GET[char] . '" method="post">' . "\n");
		print ('<select name="itemUse">' . "\n");

		$resetItem = array(
			7510,
			7511,
			7512,
			7513,
			7520,);

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
		$this->input->itemUse = HOF::$input->post["itemUse"];

		switch ($this->input->itemUse)
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
		if ($this->input->itemUse == 6000)
		{
			if ($this->user->DeleteItem(6000) == 0)
			{
				$this->_msg_error("アイテムがありません。", "margin15");
				return false;
			}

			if (1 < $this->char->spd)
			{
				$dif = $this->char->spd - 1;

				$this->char->spd -= $dif;
				$this->char->statuspoint += $dif;
				$this->char->SaveCharData();

				$this->user->SaveUserItem();

				$this->_msg_result("ポイント還元成功 ", "margin15");

				return true;
			}
		}

		if ($lowLimit)
		{
			if (!$this->user->item[$this->input->itemUse])
			{
				$this->_msg_error("アイテムがありません。", "margin15");
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
				if ($this->user->DeleteItem($this->input->itemUse) == 0)
				{
					$this->_msg_error("アイテムがありません。", "margin15");
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

				$this->char->SaveCharData();

				$this->user->SaveUserItem();

				$this->_msg_result("ポイント還元成功 ", "margin15");

				return true;
			}
			else
			{
				$this->_msg_error("ポイント還元失敗", "margin15");
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
<form action="{BASE_URL}?char={$_GET[char]}" method="post">
<input type="submit" class="btn" name="kick" value="Yes">
<input type="submit" class="btn" value="No">
</form>
</div>
HTML_BYEBYE;
		print ($message);
		return false;
	}

	/**
	 * サヨナラ(処理)
	 */
	function _main_action_kick()
	{
		$this->char->DeleteChar();

		header("Location: ".HOF::url());
		HOF::end();
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

		$this->output->char_id = $this->input->char;

		$this->output->job_change_list = array();

		foreach ((array)$this->char->job_change_list() as $job)
		{
			$newjob = new HOF_Class_Char_Job(array('job' => $job, 'gender' => $this->char->gender));

			$this->output->job_change_list[] = $newjob;
		}

		$this->output->user_item = $this->user->item;
		$this->output->char = $this->char;

		$this->output->pattern_max = $this->char->pattern_max();

		/**
		 * Action Pattern 行動判定
		 * 行動判定条件一覧
		 */
		$this->output->judge_list = HOF_Model_Data::getJudgeList();

		/**
		 * 前衛の時の後衛守り
		 */
		$this->output->guard_list = array();

		foreach((array)HOF_Model_Data::getGuardList() as $k)
		{
			$this->output->guard_list[$k] = HOF_Model_Data::getGuardData($k);
		}

		$JobData = $this->char->jobdata();

		// 装備中の物表示 ////////////////////////////////
		$weapon = HOF_Class_Item::newInstance($this->char->weapon);
		$shield = HOF_Class_Item::newInstance($this->char->shield);
		$armor = HOF_Class_Item::newInstance($this->char->armor);
		$item = HOF_Class_Item::newInstance($this->char->item);

		$handle = 0;
		$handle = $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item["handle"];


?>
<div style="margin:0 15px">
	<h4>Equipment<a href="<?php e(HOF::url('manual', 'manual', '#equip')) ?>" target="_blank" class="a0">?</a></h4>
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
	<form action="<?php e(BASE_URL) ?>?char=<?=

		$this->input->char


?>" method="post">
		<table>
			<tr>
				<td class="align-right">Weapon :</td>
				<td><label><input type="radio" class="vcent" name="spot" value="weapon">
					<?php e($weapon->html()); ?></label></td>
			</tr>
			<tr>
				<td class="align-right">Shield :</td>
				<td><label><input type="radio" class="vcent" name="spot" value="shield">
					<?php e($shield->html()); ?></label></td>
			</tr>
			<tr>
				<td class="align-right">Armor :</td>
				<td><label><input type="radio" class="vcent" name="spot" value="armor">
					<?php e($armor->html()); ?></label></td>
			</tr>
			<tr>
				<td class="align-right">Item :</td>
				<td><label><input type="radio" class="vcent" name="spot" value="item">
					<?php e($item->html()); ?></label></td>
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
			if ($this->user->options['no_JS_itemlist']) $EquipList->NoJS();
			reset($this->user->item); //これが無いと装備変更時に表示されない
			foreach ($this->user->item as $key => $val)
			{
				$item = HOF_Model_Data::newItem($key);

				// 装備できないので次
				if (!isset($EquipAllow[$item["type"]])) continue;

				$head = '<label><input type="radio" name="item_no" value="' . $key . '" class="vcent">';
				$head .= $item->html($val) . "</label>";

				$EquipList->AddItem($item, $head);
			}
			print ($EquipList->GetJavaScript("list0"));
			print ($EquipList->ShowSelect());
			print ('<form action="'.BASE_URL.'?char=' . $this->input->char . '" method="post">' . "\n");
			print ('<div id="list0">' . $EquipList->ShowDefault() . '</div>' . "\n");
			print ('<input type="submit" class="btn" name="equip_item" value="Equip">' . "\n");
			print ("</form>\n");
		}
		else
		{
			print ("No items.<br />\n");
		}
		print ("</div>\n");

?>
<form action="?char=<?=

		$this->input->char


?>" method="post" style="padding:0 15px">
	<h4>Skill<a href="<?php e(HOF::url('manual', 'manual', '#skill')) ?>" target="_blank" class="a0">?</a></h4>
	<?php

		// スキル表示 //////////////////////////////////////
		//include(DATA_SKILL);//ActionPatternに移動
		//include_once (DATA_SKILL_TREE);
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
			$tree = $this->char->skill_tree();
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

	}

	/**
	 * 自分のキャラを表示する
	 */
	function ShowMyCharacters($array = NULL)
	{
		// $array ← 色々受け取る
		if (empty($this->user->char)) return false;

		$divide = (count($this->user->char) < CHAR_ROW ? count($this->user->char) : CHAR_ROW);
		$width = floor(100 / $divide); //各セル横幅

		$this->output->width = $this->user->width;
		$this->output->chars = (array)$this->user->char;
	}

	/**
	 * チュウトリアル
	 */
	function ShowTutorial()
	{
		$last = $this->user->timestamp['last'];
		$start = substr($this->user->timestamp['create'], 0, 10);
		$term = 60 * 60 * 1;

		if (($last - $start) < $term)
		{
			$this->output->show_tutorial = true;
		}
	}

}


?>