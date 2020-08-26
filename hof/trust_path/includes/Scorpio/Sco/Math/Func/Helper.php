<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Func_Helper
{

	static function a($n = 2) {
		$a = array();

		//每行的第一個和最後一個都為1，寫了6行
		for ($i = 0; $i < $n; $i++) {
			$a[$i] = array();

			$a[$i][0] = 1;
			$a[$i][$i] = 1;

			//出除了第一位和最後一位的值，保存在數組中
			if ($i > 1) {
				for ($j = 1; $j < $i; $j++) {
					$a[$i][$j] = $a[$i - 1][$j - 1] + $a[$i - 1][$j];
				}
			}
		}

//      1
//     1 1
//    1 2 1
//   1 3 3 1
//  1 4 6 4 1
//1 5 10 10 5 1

//		//出除了第一位和最後一位的值，保存在數組中
//		for ($i = 2; $i < $n; $i++) {
//			for ($j = 1; $j < $i; $j++) {
//				$a[$i][$j] = $a[$i - 1][$j - 1] + $a[$i - 1][$j];
//			}
//		}
//		//打印
//		for ($i = 0; $i < $n; $i++) {
//			for ($j = 0; $j <= $i; $j++) {
//				echo $a[$i][$j] . '&nbsp;';
//			}
//			echo '<br/>';
//		}

		return $a;
	}

}

