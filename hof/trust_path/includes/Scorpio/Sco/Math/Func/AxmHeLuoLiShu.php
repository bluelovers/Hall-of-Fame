<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Math_Func_AxmHeLuoLiShu
{

	/**
	 * 天干取數
	 */
	function TianGanQuShu($gan)
	{
		//甲6 乙2 丙8 丁7 戊1 己9 庚3 辛4 壬6 癸2
		$rt = array(
			0,
			6,
			2,
			8,
			7,
			1,
			9,
			3,
			4,
			6,
			2);
		return $rt[$gan];
	}

	/**
	 * 地支取數
	 */
	function DiZhiQuShu($zhi)
	{
		//子1，6 丑5，10，寅3，8，卯3，8
		//辰5，10 巳2，7 午2，7 未5，10
		//申4，9 酉4，9 戌5，10 亥1，6
		$rt = array(
			array(0, 0),
			array(1, 6),
			array(5, 10),
			array(3, 8),
			array(3, 8),
			array(5, 10),
			array(2, 7),
			array(2, 7),
			array(5, 10),
			array(4, 9),
			array(4, 9),
			array(5, 10),
			array(1, 6),
			);
		return $rt[$zhi];
	}

	/**
	 * 求天地數
	 */
	function TianDiShu($siZhu)
	{
		$tianShu = 0;
		$diShu = 0;
		for ($i = 0; $i < 4; $i++)
		{
			$gan = $siZhu[$i][0];
			$zhi = $siZhu[$i][1];
			$ganQuShu = static::TianGanQuShu($gan);
			$zhiQuShu = static::DiZhiQuShu($zhi);
			if (IsOddNo($ganQuShu))
			{
				$tianShu += $ganQuShu;
			}
			else
			{
				$diShu += $ganQuShu;
			}
			if (IsOddNo($zhiQuShu[0]))
			{
				$tianShu += $zhiQuShu[0];
				$diShu += $zhiQuShu[1];
			}
			else
			{
				$tianShu += $zhiQuShu[1];
				$diShu += $zhiQuShu[0];
			}
		}
		return array(
			0,
			$tianShu,
			$diShu);
	}

	/**
	 * 求先天卦(天地數，性別，元運，陰陽)
	 */
	function XianTianGua($tiandiShu, $sex, $yuan, $yinyang)
	{
		$xtg = new MdLiuYao();
		$shang = ReviseInt($tiandiShu[0], 25);
		if ($shang % 10 == 0)
		{
			$shang = $shang / 10;
		}
		else
		{
			$shang = $shang % 10;
		}
		if ($shang == 5)
		{
			$shang = static::JiGong($sex, $yuan, $yinyang);
		}
		$xia = ReviseInt($tiandiShu[1], 30);
		if ($xia % 10 == 0)
		{
			$xia = $xia / 10;
		}
		else
		{
			$xia = $xia % 10;
		}
		if ($xia == 5)
		{
			$xia = static::JiGong($sex, $yuan, $yinyang);
		}
		$xtg->ShangGua($shang);
		$xtg->XiaGua($xia);
		return $xtg;
	}

	/**
	 * @param $yuan 1:男艮女坤 2:陽男陰女艮，陽女陰男坤 3:男離女兌
	 */
	function JiGong($sex, $yuan, $yinyang)
	{
		switch ($yuan)
		{
				// 男艮女坤
			case 1:
				{
					if ($sex == 1)
					{
						return 8;
					}
					return 2;
				}
				// 陽男陰女艮，陽女陰男坤
			case 2:
				{
					if (($sex == 1 && $yinyang == 1) || ($sex == 0 && $yingyang == 0))
					{
						return 8;
					}
					return 2;
				}
				// 男離女兌
			case 3:
				{
					if ($sex == 1)
					{
						return 9;
					}
					return 7;
				}
		}
	}

}


?>