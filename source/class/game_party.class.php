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
}

?>