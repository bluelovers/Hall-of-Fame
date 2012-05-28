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

		//$this->user->char_all();

		$this->_cache->char_list = $this->user->char_list();

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

		if (!$this->user->allowPlay())
		{
			$this->_main_stop(true);

			HOF_Class_Controller::getInstance('game', 'login')->_main_exec('login');

			return;
		}

		$action = $this->action;

		if ($this->input->action == 'char')
		{
			//$this->user->item();

			//$this->char = &$this->user->char[$this->input->char];
			$this->char = &$this->user->char($this->input->char);

			if (!$this->char)
			{
				return $this->_main_stop(true);
			}

			$this->output->char_url = HOF::url($this->controller, 'char', array('char' => $this->char->id));
		}

		//$this->_router();

		if ($action != 'default' && !$this->input->char)
		{
			$action = 'default';
		}

		if ($this->input->char)
		{
			$this->char_detail();

			//$this->_main_setup('char');
		}
		else
		{
			$this->_main_setup($action);
		}

		$this->options['autoView'] = true;
		$this->options['escapeHtml'] = false;

		$this->output->char_list = $this->_cache->char_list;

		$this->output->action = $this->action;

		//debug($this->action, $action, $this->input);
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
					break;
				}
			}
		}

		if ($_action && $_action != $this->input->action && $this->_main_exists($_action))
		{
			$this->input->action = $_action;

			//$this->options['autoView'] = false;

			$this->_main_exec_once($this->input->action);
		}
	}

	/*
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
	*/

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

	}

	function _main_action_equip()
	{
		foreach(array(
			'equip_item',
			'equip_remove',
			'equip_remove_all',
		) as $k)
		{
			if ($this->input->$k = HOF::request()->post->$k)
			{
				$this->{'_'.$k}();
				break;
			}
		}

		ob_start();

		$this->CharStatShow();

		$this->output->content = ob_get_clean();
	}

	function _main_after()
	{
		$this->user->fpclose_all();

		//debug($this->action, $action, $this->input);

		parent::_main_after();
	}

	/**
	 * ステータス上昇
	 */
	function _main_action_stup()
	{
		if (!$this->input->{$this->action} = HOF::request()->post->{$this->action})
		{
			return false;
		}


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
	function _judge_position()
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

	function _main_action_action()
	{
		$this->output->pattern_max = $this->char->pattern_max();

		/**
		 * Action Pattern 行動判定
		 * 行動判定条件一覧
		 */
		$this->output->judge_list = array();
		$judge_list = HOF_Model_Data::getJudgeList();

		foreach($judge_list as $k)
		{
			$this->output->judge_list[$k] = HOF_Model_Data::getJudgeData($k);
		}

		$this->skill_list();

		/**
		 * 前衛の時の後衛守り
		 */
		$this->output->guard_list = array();

		foreach((array)HOF_Model_Data::getGuardList() as $k)
		{
			$this->output->guard_list[$k] = HOF_Model_Data::getGuardData($k);
		}

		foreach(array(
			'pattern_change',
			'TestBattle',
			'pattern_memo',
			'pattern_insert',
			'pattern_remove',
			'position',
		) as $k)
		{
			if ($this->input->$k = HOF::request()->post->$k)
			{
				$this->{'_judge_'.$k}();
				break;
			}
		}
	}

	/**
	 * 行動設定
	 */
	function _judge_pattern_change()
	{
		$pattern = $this->_judge_pattern_input();

		if ($this->char->pattern($pattern))
		{
			$this->char->SaveCharData();
			$this->_msg_result("パターン設定保存 完了", "margin15");

			return true;
		}

		$this->_msg_error("失敗したなんで？報告してみてください 03050242", "margin15");

		return false;
	}

	function _judge_pattern_input()
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
	function _judge_TestBattle()
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
	function _judge_pattern_memo()
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
	function _judge_pattern_insert()
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
	function _judge_pattern_remove()
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
	function _equip_remove()
	{
		$this->input->spot = HOF::$input->post["spot"];

		if (!$this->input->spot)
		{
			$this->_msg_error("装備をはずす箇所が選択されていない", "margin15");

			return false;
		}

		if (!$this->char->equip->{$this->input->spot})
		{
			// $this と $this->char の区別注意！
			$this->_msg_error("指定された箇所には装備無し", "margin15");

			return false;
		}

		if ($item = $this->char->unequip($this->input->spot))
		{
			$this->user->item_add($item);

			$this->user->item_save();
		}

		$this->char->SaveCharData();

		$this->_msg_result($this->char->Name() . " の {$item[name]} を はずした。", "margin15");

		return true;
	}

	/**
	 * 装備全部はずす
	 */
	function _equip_remove_all()
	{
		if ($list = $this->char->unequip('all'))
		{
			foreach($list as $no)
			{
				if (!$no) continue;

				$_item = HOF_Model_Data::newItem($no);

				$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");

				$this->user->item_add($no);
			}

			$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");

			$this->user->item_save();
			$this->char->SaveCharData();

			return true;
		}

		return false;
	}

	/**
	 * 指定物を装備する
	 */
	function _equip_item()
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

		list ($fail, $return) = $this->char->setEquip($item);

		if ($fail)
		{
			$this->_msg_error("Handle Over. Can't Equip {$item[name]}.", "margin15");
		}
		else
		{
			$this->user->item_remove($this->input->item_no);
		}

		foreach ($return as $no)
		{
			if (!$no) continue;

			$_item = HOF_Model_Data::newItem($no);

			$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");

			$this->user->item_add($no);
		}

		if (!$fail)
		{
			$this->_msg_result("{$this->char->name} は {$item[name]} を装備した.", "margin15");
		}

		$this->user->item_save();
		$this->char->SaveCharData();

		return $fail ? false : true;
	}

	function skill_list()
	{
		$this->output->skill_list = array();

		foreach($this->char->skill as $k)
		{
			$this->output->skill_list[$k] = HOF_Model_Data::getSkill($k);
		}
	}

	/**
	 * スキル習得
	 */
	function _main_action_skill_learn()
	{
		$this->skill_list();

		$this->output->skill_learn = array();

		$tree = $this->char->skill_tree();
		$skill_learn = array_diff($tree, (array)$this->char->skill);

		$result = false;

		$this->input->{$this->action} = HOF::request()->post->{$this->action};

		while ($this->input->{$this->action})
		{
			$this->input->newskill = HOF::$input->post["newskill"];

			if (!$this->input->newskill)
			{
				$this->_msg_error("スキル未選択", "margin15");
				break;
			}

			$this->char->SetUser($this->user->id);

			list($result, $message) = $this->char->LearnNewSkill($this->input->newskill);

			if ($result)
			{
				$this->char->SaveCharData();
				$this->_msg_result($message, "margin15");

				$skill_learn_old = $skill_learn;

				$tree = $this->char->skill_tree();
				$skill_learn = array_diff($tree, (array)$this->char->skill);
				if ($skill_learn_new = array_diff($skill_learn, $skill_learn_old))
				{
					$this->_msg_result("新スキル習得可能", "margin15");
				}
			}
			else
			{
				$this->_msg_error($message, "margin15");
			}

			break;
		}

		foreach($skill_learn as $val)
		{
			$skill = HOF_Model_Data::getSkill($val);

			$this->output->skill_learn[$val] = $skill;
		}
	}

	/**
	 * クラスチェンジ(転職)
	 */
	function _main_action_job_change()
	{
		while(1)
		{
			if (!$this->input->{$this->action} = HOF::request()->post->{$this->action})
			{
				break;
			}

			$this->input->job = HOF::$input->post["job"];

			if (!$this->input->job)
			{
				$this->_msg_error("職 未選択", "margin15");
				break;
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
						$this->user->item_add($item);

						$_item = HOF_Model_Data::newItem($item);
						$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");
					}

					$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");

					$this->user->item_save();
				}

				// 保存
				$this->char->SaveCharData();

				$this->_msg_result("転職 完了", "margin15");

				break;
			}

			$this->_msg_error("failed.", "margin15");
	 		break;
 		}

 		$this->output->job_change_list = array();

		foreach ((array)$this->char->job_change_list() as $job)
		{
			$newjob = new HOF_Class_Char_Job(array('job' => $job, 'gender' => $this->char->gender));

			$this->output->job_change_list[] = $newjob;
		}
	}

	/**
	 * 改名(表示)
	 */
	function _main_action_rename()
	{
		$Name = $this->char->Name();

		$message = <<< EOD
<form action="{$this->output->char_url}" method="post" class="margin15">
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
				if ($this->user->item_remove("7500", 1) == 1)
				{
					$this->_msg_result($this->char->Name() . " から " . $return . " へ改名しました。", "margin15");

					$this->char->ChangeName($return, true);
					$this->char->SaveCharData();

					$this->user->item_save();

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
		print ('<form action="'.$this->output->char_url . '" method="post">' . "\n");
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
			if ($this->user->item_remove(6000) == 0)
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

				$this->user->item_save();

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
				if ($this->user->item_remove($this->input->itemUse) == 0)
				{
					$this->_msg_error("アイテムがありません。", "margin15");
					return false;
				}

				$this->char->statuspoint += $pointBack;

				// 装備も全部解除
				if ($items = $this->char->unequip('all'))
				{
					foreach((array)$items as $item)
					{
						$this->user->item_add($item);

						$_item = HOF_Model_Data::newItem($item);
						$this->_msg_error($this->char->Name().' unequip '.$_item->name(), "margin15");
					}

					$this->_msg_result($this->char->Name() . " の装備を 全部解除した", "margin15");

					$this->user->item_save();
				}

				$this->char->SaveCharData();

				$this->user->item_save();

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
<form action="{$this->output->char_url}" method="post">
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
		$this->char->char_delete();

		header("Location: ".HOF::url());
		HOF::end();
	}

	function char_detail()
	{
		if (!$this->char)
		{
			print ("Not exists");
			return false;
		}

		// 戦闘用変数の設定。
		$this->char->SetBattleVariable();

		$this->output->char_id = $this->input->char;
		$this->output->char = $this->char;
	}

	/**
	 * キャラクター詳細表示・装備変更などなど
	 * 長すぎる...(200行以上)
	 */
	function CharStatShow()
	{
		if ($this->char_detail() === false)
		{
			return false;
		}

		$this->output->user_item = $this->user->item;

		$JobData = $this->char->jobdata();

		$handle = $this->char->GetHandle(true);

		/**
		 * 装備中の物表示
		 */
		$this->output->equip = array();

		foreach ($this->char->map_equip_allow as $slot => $allow)
		{
			if ($allow && $this->char->equip->{$slot})
			{
				$item = HOF_Class_Item::newInstance($this->char->equip->{$slot});

				$this->output->equip[$slot] = $item;
			}
		}

		$this->char->CalcEquips();

?>
	<table class="margin15">
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
<?php

		// 装備可能な物表示 ////////////////////////////////
		if ($JobData["equip"]) $EquipAllow = array_flip($JobData["equip"]); //装備可能な物リスト(反転)
		else  $EquipAllow = array(); //装備可能な物リスト(反転)
		$Equips = array(
			"Weapon" => "2999",
			"Shield" => "4999",
			"Armor" => "5999",
			"Item" => "9999");

		print ("<div>\n");
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

				$EquipList->item_add($item, $head);
			}
			print ($EquipList->GetJavaScript("list0"));
			print ($EquipList->ShowSelect());
			print ('<form action="' . HOF::url('char', 'equip', array('char' => $this->output->char_id)) . '" method="post">' . "\n");
			print ('<div id="list0">' . $EquipList->ShowDefault() . '</div>' . "\n");
			print ('<input type="submit" class="btn" name="equip_item" value="Equip">' . "\n");
			print ("</form>\n");
		}
		else
		{
			print ("No items.<br />\n");
		}
		print ("</div>\n");

	}

	/**
	 * 自分のキャラを表示する
	 */
	function ShowMyCharacters($array = NULL)
	{
		$this->user->char_all();

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