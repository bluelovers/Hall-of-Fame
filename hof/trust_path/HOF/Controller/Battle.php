<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Battle extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _init()
	{
		$this->user = &HOF_Model_Main::getInstance();
	}

	function _main_before()
	{
		$this->_input();

		$this->user->LoadUserItem();
	}

	function _input()
	{


	}

	/**
	 * 狩場
	 */
	function _main_action_hunt()
	{
		$mapList = HOF_Model_Data::getLandAppear($this->user);

		$Union = array();

		if ($files = game_core::glob(UNION))
		{
			foreach ($files as $file)
			{
				$UnionMons = HOF_Model_Char::newUnionFromFile($file);
				if ($UnionMons->is_Alive()) $Union[] = $UnionMons;
			}
		}

		if ($Union)
		{

			$result = $this->user->CanUnionBattle();

			if ($result !== true)
			{
				$left_minute = floor($result / 60);
				$left_second = $result % 60;
			}

			ob_start();
			$this->user->ShowCharacters($Union);
			$union_showchar = ob_get_clean();

		}

		$logs = array();

		$log = game_core::glob(LOG_BATTLE_UNION);
		foreach (array_reverse($log) as $file)
		{
			$limit++;

			$logs[] = $file;

			if (15 <= $limit) break;
		}

		$this->output->maps = $mapList;

		$this->output->union = $Union;
		$this->output->union_showchar = $union_showchar;

		$this->output->result = $result;
		$this->output->left_minute = $left_minute;
		$this->output->left_second = $left_second;

		$this->output->logs = $logs;

		$this->options['escapeHtml'] = false;

		$this->user->fpCloseAll();
	}

	/**
	 * 一般モンスター
	 */
	function _main_action_common()
	{
		$this->input->monster_battle = HOF::$input->post['monster_battle'];
		$this->input->common = HOF::$input->request['common'];

		$this->user->CharDataLoadAll();

		if ($this->_cache['MonsterBattle'] = $this->MonsterBattle())
		{
			$this->user->SaveData();
			$this->user->fpCloseAll();
		}
		else
		{
			$this->user->fpCloseAll();
		}
	}

	function _common()
	{
		if (!$this->_cache['MonsterBattle'])
		{
			$this->MonsterShow();
		}
	}

	/**
	 * モンスターとの戦闘
	 */
	function MonsterBattle()
	{
		if ($this->input->monster_battle)
		{
			$this->user->MemorizeParty(); //パーティー記憶
			// そのマップで戦えるかどうか確認する。

			$land = HOF_Model_Data::getLandAppear($this->user);
			if (!array_key_exists($this->input->common, $land))
			{
				HOF_Helper_Global::ShowError("マップが出現して無い", "margin15");
				return false;
			}

			// Timeが足りてるかどうか確認する
			if ($this->user->time < NORMAL_BATTLE_TIME)
			{
				HOF_Helper_Global::ShowError("Time 不足 (必要 Time:" . NORMAL_BATTLE_TIME . ")", "margin15");
				return false;
			}

			// bluelovers
			$MyParty = array();
			// bluelovers

			// 自分パーティー
			foreach ($this->user->char as $key => $val)
			{ //チェックされたやつリスト
				if (HOF::$input->post["char_" . $key]) $MyParty[] = $this->user->char[$key];
			}

			if (count($MyParty) === 0)
			{
				HOF_Helper_Global::ShowError('戦闘するには最低1人必要', "margin15");
				return false;
			}
			else
			{
				if (5 < count($MyParty))
				{
					HOF_Helper_Global::ShowError('戦闘に出せるキャラは5人まで', "margin15");
					return false;
				}
			}

			// bluelovers
			$MyParty = HOF_Class_Battle_Team::newInstance($MyParty);
			// bluelovers

			// 敵パーティー(または一匹)

			//	include (DATA_MONSTER);
			/*
			list($Land, $MonsterList) = HOF_Model_Data::getLandInfo($this->input->common);
			*/

			$land_data = HOF_Model_Data::getLandInfo($this->input->common);

			$Land = $land_data['land'];
			$MonsterList = $land_data['monster'];

			$EneNum = $this->user->EnemyNumber($MyParty);
			$EnemyParty = $this->user->EnemyParty($EneNum, $MonsterList);

			$this->user->WasteTime(NORMAL_BATTLE_TIME); //時間の消費

			$battle = new HOF_Class_Battle($MyParty, $EnemyParty);
			$battle->SetBackGround($Land["land"]); //背景
			$battle->SetTeamName($this->user->name, $Land["name"]);
			$battle->Process(); //戦闘開始
			$battle->SaveCharacters(); //キャラデータ保存
			list($UserMoney) = $battle->ReturnMoney(); //戦闘で得た合計金額
			//お金を増やす
			$this->user->GetMoney($UserMoney);
			//戦闘ログの保存
			if ($this->user->record_btl_log) $battle->RecordLog();

			// アイテムを受け取る
			if ($itemdrop = $battle->ReturnItemGet(0))
			{
				$this->user->LoadUserItem();
				foreach ($itemdrop as $itemno => $amount) $this->user->AddItem($itemno, $amount);
				$this->user->SaveUserItem();
			}

			//dump($itemdrop);
			//dump($this->user->item);
			return true;
		}
	}

	/**
	 * モンスターの表示
	 */
	function MonsterShow()
	{
		$land_id = $this->input->common;

		var_dump($this->input);

		// まだ行けないマップなのに行こうとした。
		if (!array_key_exists($this->input->common, HOF_Model_Data::getLandAppear($this->user)))
		{
			print ('<div style="margin:15px">not appeared or not exist</div>'.$this->input->common);
			return false;
		}
		/*
		list($land, $monster_list) = HOF_Model_Data::getLandInfo($land_id);
		*/
		$land_data = HOF_Model_Data::getLandInfo($land_id);

		$land = $land_data['land'];
		$monster_list = $land_data['monster'];

		if (!$land || !$monster_list)
		{
			print ('<div style="margin:15px">fail to load</div>');
			return false;
		}

		print ('<div style="margin:15px">');
		HOF_Helper_Global::ShowError($message);
		print ('<span class="bold">' . $land["name"] . '</span>');
		print ('<h4>Teams</h4></div>');
		print ('<form action="' . INDEX . '?common=' . $this->input->common . '" method="post">');
		$this->user->ShowCharacters($this->user->char, "CHECKBOX", $this->user->party_memo);


?>
<div style="margin:15px;text-align:center">
	<input type="submit" class="btn" name="monster_battle" value="Battle !">
	<input type="reset" class="btn" value="Reset">
	<br>
	Save this party:
	<input type="checkbox" name="memory_party" value="1">
</div>
</form>
<?php

		//			include (DATA_MONSTER);
		//			include (CLASS_MONSTER);
		foreach ($monster_list as $id => $val)
		{
			if ($val[1]) $monster[] = HOF_Model_Char::newMon($id);
		}
		print ('<div style="margin:15px"><h4>MonsterAppearance</h4></div>');
		$this->user->ShowCharacters($monster, "MONSTER", $land["land"]);
	}

}


?>