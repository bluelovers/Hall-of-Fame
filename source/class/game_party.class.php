<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_party {
	/**
	 * @abstract main
	 */
	var $main;

	function game_party($main) {
		$this->main = &$main;
	}

	/**
	 * 敵の数を返す	数～数+2(max:5)
	 */
	function EnemyNumber($party) {
		$min	= count($party);//プレイヤーのPT数
		if($min == 5)//5人なら5匹
			return 5;
		$max	= $min + ENEMY_INCREASE;// つまり、+2なら[1人:1～3匹] [2人:2～4匹] [3:3-5] [4:4-5] [5:5]
		if($max>5)
			$max	= 5;
		mt_srand();
		return mt_rand($min,$max);
	}

	/**
	 * 出現する確率から敵を選んで返す
	 */
	function SelectMonster($monster) {
		foreach($monster as $val)
			$max	+= $val[0];//確率の合計
		$pos	= mt_rand(0,$max);//0～合計 の中で乱数を取る
		foreach($monster as $monster_no => $val) {
			$upp	+= $val[0];//その時点での確率の合計
			if($pos <= $upp)//合計より低ければ　敵が決定される
				return $monster_no;
		}
	}

	/**
	 * 敵のPTを作成、返す
	 * Specify=敵指定(配列)
	 */
	function EnemyParty($Amount,$MonsterList,$Specify=false) {

		// 指定モンスター
		if($Specify) {
			$MonsterNumbers	= $Specify;
		}

		// モンスターをとりあえず配列に全部入れる
		$enemy	= array();
		if(!$Amount)
			return $enemy;
		mt_srand();
		for($i=0; $i<$Amount; $i++)
			$MonsterNumbers[]	= $this->SelectMonster($MonsterList);

		// 重複しているモンスターを調べる
		$overlap	= array_count_values($MonsterNumbers);

		// 敵情報を読んで配列に入れる。
		include(CLASS_MONSTER);
		foreach($MonsterNumbers as $Number) {
			if(1 < $overlap[$Number])//1匹以上出現するなら名前に記号をつける。
				$enemy[]	= new monster(CreateMonster($Number,true));
			else
				$enemy[]	= new monster(CreateMonster($Number));
		}
		return $enemy;
	}

	/**
	 * モンスターとの戦闘
	 */
	function MonsterBattle() {
		if($_POST["monster_battle"]) {
			$this->main->MemorizeParty();//パーティー記憶
			// そのマップで戦えるかどうか確認する。
			include_once(DATA_LAND_APPEAR);
			$land	= LoadMapAppear($this);
			if(!in_array($_GET["common"],$land)) {
				ShowError("マップが出現して無い","margin15");
				return false;
			}

			// Timeが足りてるかどうか確認する
			if($this->main->time < NORMAL_BATTLE_TIME) {
				ShowError("Time 不足 (必要 Time:".NORMAL_BATTLE_TIME.")","margin15");
				return false;
			}
			// 自分パーティー
			foreach($this->main->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					$MyParty[]	= $this->main->char[$key];
			}
			if( count($MyParty) === 0) {
				ShowError('戦闘するには最低1人必要',"margin15");
				return false;
			} else if(5 < count($MyParty)) {
				ShowError('戦闘に出せるキャラは5人まで',"margin15");
				return false;
			}
			// 敵パーティー(または一匹)
			include(DATA_LAND);
			include(DATA_MONSTER);
			list($Land,$MonsterList)	= LandInformation($_GET["common"]);
			$EneNum	= $this->EnemyNumber($MyParty);
			$EnemyParty	= $this->EnemyParty($EneNum,$MonsterList);

			$this->main->WasteTime(NORMAL_BATTLE_TIME);//時間の消費
			include(CLASS_BATTLE);
			$battle	= new battle($MyParty,$EnemyParty);
			$battle->SetBackGround($Land["land"]);//背景
			$battle->SetTeamName($this->main->name,$Land["name"]);
			$battle->Process();//戦闘開始
			$battle->SaveCharacters();//キャラデータ保存
			list($UserMoney)	= $battle->ReturnMoney();//戦闘で得た合計金額
			//お金を増やす
			$this->main->GetMoney($UserMoney);
			//戦闘ログの保存
			if($this->main->record_btl_log)
				$battle->main->RecordLog();

			// アイテムを受け取る
			if($itemdrop	= $battle->main->ReturnItemGet(0)) {
				$this->main->LoadUserItem();
				foreach($itemdrop as $itemno => $amount)
					$this->main->AddItem($itemno,$amount);
				$this->main->SaveUserItem();
			}

			//dump($itemdrop);
			//dump($this->item);
			return true;
		}
	}

	/**
	 * 戦闘時に選択したメンバーを記憶する
	 */
	function MemorizeParty() {
		if($_POST["memory_party"]) {
			//$temp	= $this->party_memo;//一時的に記憶
			//$this->party_memo	= array();
			foreach($this->main->char as $key => $val) {//チェックされたやつリスト
				if($_POST["char_".$key])
					//$this->party_memo[]	 = $key;
					$PartyMemo[]	= $key;
			}
			//if(5 < count($this->party_memo) )//5人以上は駄目
			//	$this->party_memo	= $temp;
			if(0 < count($PartyMemo) && count($PartyMemo) < 6)
				$this->main->party_memo	= implode("<>",$PartyMemo);
		}
	}
}

?>