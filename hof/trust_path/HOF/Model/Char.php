<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Char extends HOF_Class_Array
{

	protected static $_instance;

	/**
	 * @return HOF_Model_Char
	 */
	function __construct()
	{
		if (self::$_instance === null)
		{
			parent::__construct();

			self::$_instance = $this;
		}

		return self::$_instance;
	}

	/**
	 * Retrieve singleton instance
	 *
	 * @return HOF_Model_Char
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * $char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $no . '.yml');
	 */
	function getBaseCharStatus($jobNo, $append = array())
	{
		if (!isset(self::getInstance()->char['base'][$jobNo]))
		{
			$char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $jobNo . '.yml');
			self::getInstance()->char['base'][$jobNo] = $char;
		}

		$char = self::getInstance()->char['base'][$jobNo];

		$char['birth'] = time() . substr(microtime(), 2, 6);

		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}

			$char = array_merge($char, (array )$append);
		}

		return $char;
	}

	/**
	 * @return HOF_Class_Char
	 */
	function newBaseChar($jobNo, $append = array())
	{
		$char = self::newChar(self::getBaseCharStatus($jobNo, $append));

		return $char;
	}

	function newChar($append = array())
	{
		$char = new HOF_Class_Char();

		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}

			$char->SetCharData($append);
		}

		return $char;
	}

	function newCharFromFile($file = null)
	{
		$char = new HOF_Class_Char($file);

		return $char;
	}

	function getBaseMonster($no, $over = false)
	{
		if (!isset(self::getInstance()->char['mon'][$no]))
		{
			$char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Mon/mon.' . $no . '.yml');
			self::getInstance()->char['mon'][$no] = $char;
		}

		$char = self::getInstance()->char['mon'][$no];

		if (!$char) return false;

		static $overlap;

		///// 色々変数追加・編集 /////////////////////

		if ($no < 2000)
		{
			$char["moneyhold"] = 100;
		}

		// 名前が重複しないように Slime(A),Slime(B)みたいにする
		if ($over)
		{
			$letter = "A"; //文字(数字でもおｋ)
			$letter = chr(ord($letter) + $overlap[$no]);
			$overlap[$no]++; //繰上げ
			$style = "({$letter})"; //どんな感じで加えるか これだと"(B)"みたいになる
			$char["name"] .= $style; //実際に名前の後ろに付け加える
		}

		// 前衛後衛が設定されていなければ設定する
		mt_srand(); //乱数初期化

		if (!$char["position"])
		{ //前列後列の設定
			$char["position"] = (mt_rand(0, 1) ? "front" : "back");
			$char["posed"] = true;
		}

		// 落とすアイテムをもたせる
		if (is_array($char["itemtable"]))
		{
			$prob = mt_rand(1, 10000);
			$sum = 0;
			foreach ($char["itemtable"] as $itemno => $upp)
			{
				$sum += $upp;
				if ($prob <= $sum)
				{
					$char["itemdrop"] = $itemno;
					break;
				}
			}
		}

		$char += array("monster" => "1");

		return $char;
	}

	function newMon($no)
	{
		$append = self::getBaseMonster($no);

		if (!empty($append))
		{
			if ($append instanceof HOF_Class_Array)
			{
				$append = $append->toArray();
			}
			elseif ($append instanceof ArrayObject)
			{
				$append = $append->getArrayCopy();
			}
		}

		$char = new HOF_Class_Mon($append);

		return $char;
	}

}
