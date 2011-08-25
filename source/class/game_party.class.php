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
}

?>