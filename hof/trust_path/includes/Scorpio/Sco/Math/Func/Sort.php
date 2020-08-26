<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Func_Sort
{

	/**
	 * 冒泡排序
	 */
	function BubbleSort($arr)
	{
		// 獲得數組總長度
		$num = count($arr);
		// 正向遍曆數組
		for ($i = 1; $i < $num; $i++)
		{
			// 反向遍歷
			for ($j = $num - 1; $j >= $i; $j--)
			{
				// 相鄰兩個數比較
				if ($arr[$j] < $arr[$j - 1])
				{
					// 暫存較小的數
					$iTemp = $arr[$j - 1];
					// 把較大的放前面
					$arr[$j - 1] = $arr[$j];
					// 較小的放後面
					$arr[$j] = $iTemp;
				}
			}
		}
		return $arr;
	}

	/**
	 * 交換法排序
	 */
	function ExchangeSort($arr)
	{
		$num = count($arr);
		// 遍曆數組
		for ($i = 0; $i < $num - 1; $i++)
		{
			// 獲得當前索引的下一個索引
			for ($j = $i + 1; $j < $num; $j++)
			{
				// 比較相鄰兩個的值大小
				if ($arr[$j] < $arr[$i])
				{
					// 暫存較小的數
					$iTemp = $arr[$i];
					// 把較大的放前面
					$arr[$i] = $arr[$j];
					// 較小的放後面
					$arr[$j] = $iTemp;
				}
			}
		}
		return $arr;
	}

	/**
	 * 選擇法排序
	 */
	function SelectSort($arr)
	{
		// 獲得數組總長度
		$num = count($arr);
		// 遍曆數組
		for ($i = 0; $i < $num - 1; $i++)
		{
			// 暫存當前值
			$iTemp = $arr[$i];
			// 暫存當前位置
			$iPos = $i;
			// 遍歷當前位置以後的數據
			for ($j = $i + 1; $j < $num; $j++)
			{
				// 如果有小於當前值的
				if ($arr[$j] < $iTemp)
				{
					// 暫存最小值
					$iTemp = $arr[$j];
					// 暫存位置
					$iPos = $j;
				}
			}
			// 把當前值放到算好的位置
			$arr[$iPos] = $arr[$i];
			// 把當前值換成算好的值
			$arr[$i] = $iTemp;
		}
		return $arr;
	}

	/**
	 * 插入法排序
	 */
	function InsertSort($arr)
	{
		$num = count($arr);
		// 遍曆數組
		for ($i = 1; $i < $num; $i++)
		{
			// 獲得當前值
			$iTemp = $arr[$i];
			// 獲得當前值的前一個位置
			$iPos = $i - 1;
			// 如果當前值小於前一個值切未到數組開始位置
			while (($iPos >= 0) && ($iTemp < $arr[$iPos]))
			{
				// 把前一個的值往後放一位
				$arr[$iPos + 1] = $arr[$iPos];
				// 位置遞減
				$iPos--;
			}
			$arr[$iPos + 1] = $iTemp;
		}
		return $arr;
	}

	/**
	 * 快速排序
	 */
	function QuickSort($arr)
	{
		$num = count($arr);
		$l = $r = 0;
		// 從索引的第二個開始遍曆數組
		for ($i = 1; $i < $num; $i++)
		{
			// 如果值小於索引1
			if ($arr[$i] < $arr[0])
			{
				// 裝入左索引數組(小於索引1的數據)
				$left[] = $arr[$i];
				$l++;
			}
			else
			{
				// 否則裝入右索引中(大於索引1的數據)
				$right[] = $arr[$i];
				$r++; //
			}
		}
		// 如果左索引有值 則對左索引排序
		if ($l > 1)
		{
			$left = static::QuickSort($left);
		}
		// 排序後的數組
		$new_arr = $left;
		// 將當前數組第一個放到最後
		$new_arr[] = $arr[0];
		// 如果又索引有值 則對右索引排序
		if ($r > 1)
		{
			$right = static::QuickSort($right);
		}
		// 根據右索引的長度再次增加數據
		for ($i = 0; $i < $r; $i++)
		{
			$new_arr[] = $right[$i];
		}
		return $new_arr;
	}

}


?>