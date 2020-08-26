<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

/**
 * 中華民國國民身分證(驗證 / 生成)
 *
 * @see http://zh.wikipedia.org/zh-hant/%E4%B8%AD%E8%8F%AF%E6%B0%91%E5%9C%8B%E5%9C%8B%E6%B0%91%E8%BA%AB%E5%88%86%E8%AD%89#cite_note-8
 * @see http://people.debian.org/~paulliu/ROCid.html
 * @see http://www.wretch.cc/blog/gump1002/7459167
 * @see http://www.blueshop.com.tw/download/show.asp?pgmcde=PGM200602051851008IX&extcde=PGMLSTCAT
 * @see http://doublekai.org/blog/?p=51
 */
class Sco_IdentityCard_ROC implements Sco_IdentityCard_Interface
{

	static $_city_point = array(
		'A' => 10,
		'B' => 11,
		'C' => 12,
		'D' => 13,
		'E' => 14,
		'F' => 15,
		'G' => 16,
		'H' => 17,
		'J' => 18,
		'K' => 19,
		'L' => 20,
		'M' => 21,
		'N' => 22,
		'P' => 23,
		'Q' => 24,
		'R' => 25,
		'S' => 26,
		'T' => 27,
		'U' => 28,
		'V' => 29,
		'W' => 30,
		'X' => 31,
		'Y' => 32,
		'Z' => 33,
		'I' => 34,
		'O' => 35,
		);

	static $_city_point2 = array(
		'A' => 1,
		'B' => 10,
		'C' => 19,
		'D' => 28,
		'E' => 37,
		'F' => 46,
		'G' => 55,
		'H' => 64,
		'J' => 73,
		'K' => 82,
		'L' => 2,
		'M' => 11,
		'N' => 20,
		'P' => 29,
		'Q' => 38,
		'R' => 47,
		'S' => 56,
		'T' => 65,
		'U' => 74,
		'V' => 83,
		'W' => 3,
		'X' => 12,
		'Y' => 21,
		'Z' => 30,
		'I' => 39,
		'O' => 48,
		);

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;

	const REGEX = '/^[a-zA-Z][1-2][0-9]{8}$/';
	const REGEX_FILTER = '/([a-zA-Z][1-2][0-9]{8})/';

	public function generate($city = null, $gender = null)
	{
		if ($city === null)
		{
			// 取得隨機字母分數
			$city = array_rand(self::$_city_point);
		}
		else
		{
			$city = strtoupper($city);
		}

		if ($gender === null)
		{
			$gender = mt_rand(1, 2);
		}

		//建立隨機身份證碼
		$id = $city . $gender . array_pop(explode('.', uniqid(rand(), true)));

		return substr_replace($id, self::_check_ssn_end_code($id), 9);
	}

	public function valid($id, $city = null, $gender = null)
	{
		if (preg_match(self::REGEX, $id))
		{
			$city_code = strtoupper(substr($id, 0, 1));
			$gender_code = substr($id, 1, 1);

			if ((!$n0 = self::$_city_point2[$city_code]) || ($city !== null && (string )$city !== $city_code) || ($gender !== null && (string )$gender !== $gender_code) || !($gender_code === (string )self::GENDER_MALE || $gender_code === (string )self::GENDER_FEMALE))
			{
				return false;
			}

			$n = array();

			/*
			$n[] = 0;
			$n[] = substr($n0, 0, 1);
			$n[] = substr($n0, 1, 1);
			*/

			$i = 3;

			for ($j = 1; $j < strlen($id); $j++)
			{
				$n[$i++] = substr($id, $j, 1);
			}

			//$vaild = (bool)(0 === (($n[1] + ($n[2] * 9) + ($n[3] * 8) + ($n[4] * 7) + ($n[5] * 6) + ($n[6] * 5) + ($n[7] * 4) + ($n[8] * 3) + ($n[9] * 2) + $n[10] + $n[11]) % 10));

			if (0 === (($n0 + ($n[3] * 8) + ($n[4] * 7) + ($n[5] * 6) + ($n[6] * 5) + ($n[7] * 4) + ($n[8] * 3) + ($n[9] * 2) + $n[10] + $n[11]) % 10))
			{
				return array(
					true,
					$city_code,
					$gender_code);
			}
		}

		return false;
	}

	protected function _check_ssn_end_code($ssn)
	{
		/*
		$d1 = self::$_city_point[substr($ssn, 0, 1)];
		$n1 = substr($d1, 0, 1) + (substr($d1, 1, 1) * 9);
		*/

		$n1 = self::$_city_point2[substr($ssn, 0, 1)];
		$n2 = 0;

		for ($j = 1; $j < 9; $j++)
		{
			$n2 = $n2 + substr($ssn, $j, 1) * (9 - $j);
		}

		$ssn = ((($a = 10 - ($n1 + $n2) % 10) == 10) ? 0 : $a);

		return $ssn;
	}

	protected function _city_point2()
	{
		$arr = array();

		foreach (self::$_city_point as $city => $d1)
		{
			$n1 = substr($d1, 0, 1) + (substr($d1, 1, 1) * 9);

			$arr[$city] = $n1;
		}

		return $arr;
	}

	public function filter($value)
    {
    	if (preg_match(self::REGEX_FILTER, $value, $m))
    	{
    		return strtoupper($m[0]);
    	}

        return null;
    }

}
