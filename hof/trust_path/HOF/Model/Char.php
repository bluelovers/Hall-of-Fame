<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Char extends HOF_Class_Data
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
	function getBaseCharStatus($no, $append = array())
	{
		$data = self::getInstance()->_load('char', $no);

		unset($data['name']);

		$data['birth'] = time() . substr(microtime(), 2, 6);

		if (!empty($append))
		{
			$append = self::_fixArray($append);

			$data = array_merge($data, (array )$append);
		}

		return $data;
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
			$append = self::_fixArray($append);

			$char->SetCharData($append);
		}

		return $char;
	}

	function newCharFromFile($file = null)
	{
		$char = new HOF_Class_Char($file);

		return $char;
	}

	function newUnionFromFile($file = null)
	{
		$char = new HOF_Class_Union($file);

		return $char;
	}

	/**
	 * 変数はPCキャラとほぼ同じ内容。
	 * 返す直前に
	 * "monster"	=> "1",//*モンスターと区別するため。
	 * を追加する。
	 * 装備等が無いため
	 * "atk"	=> array(*,*),
	 * "def"	=> array(*,*,*,*),
	 * atk,def は直接指定する。
	 * "exphold"	=> "**",//持ってる経験値
	 * "guard"	=> "後列の防御方法",
	 * always,never,life25,life50,life75,prob25,prob50,prob75

	 * "position"	=> "FRONT or BACK",//指定する事で位置を後列or前列に固定できる。
	 * "itemtable"	=> array("アイテム番号"=>"確立","アイテム番号2"=>"確立2"),//落とすアイテム。
	 * 設定されていない場合 → 何も落とさない。
	 * 設定されている場合。
	 * 確立は x/10000
	 * array("500"=>"1011", "1500"=>"2011", "3000"=>"10"),
	 * 1011=500/10000(5%), 2011=1000/10000(10%), 3000=10/10000(0.01%),で落とす。
	 * 複数個落とす事は無く、1個しか落とさない。

	 * 特殊
	 * "SPECIAL" = array(
	 * 特殊能力(ユニオンの毒耐性とか)
	 * );

	 * ■ ユニオンモンスター専用の変数

	 * "cycle" = 出現周期

	 * ユニオンと一緒に出る雑魚出現確率
	 * 2個目の変数は無視
	 * "Slave" = array(
	 * 敵番号 => (確立,0)
	 * );
	 * "land" = 土地(背景)

	 * 必ず出現する雑魚を指定する
	 * "SlaveSpecify"	=> array(敵番号, ),
	 * "UnionName" = ユニオンの団体の名称
	 * "LevelLimit" = レベル制限

	 * 雑魚の出現数を指定する
	 * 省略してもOK
	 * "SlaveAmount" => "6",
	 */
	function getBaseMonster($no, $over = false)
	{
		$data = self::getInstance()->_load('mon', $no);

		if (!$data) return false;

		static $overlap;

		///// 色々変数追加・編集 /////////////////////

		if ($no < 2000)
		{
			$data["moneyhold"] = 100;
		}

		// 名前が重複しないように Slime(A),Slime(B)みたいにする
		if ($over)
		{
			$letter = "A"; //文字(数字でもおｋ)
			$letter = chr(ord($letter) + $overlap[$no]);
			$overlap[$no]++; //繰上げ
			$style = "({$letter})"; //どんな感じで加えるか これだと"(B)"みたいになる
			$data["name"] .= $style; //実際に名前の後ろに付け加える
		}

		// 前衛後衛が設定されていなければ設定する
		mt_srand(); //乱数初期化

		if (!$data["position"])
		{ //前列後列の設定
			$data["position"] = (mt_rand(0, 1) ? "front" : "back");
			$data["posed"] = true;
		}

		// 落とすアイテムをもたせる
		if (is_array($data["itemtable"]))
		{
			$prob = mt_rand(1, 10000);
			$sum = 0;
			foreach ($data["itemtable"] as $itemno => $upp)
			{
				$sum += $upp;
				if ($prob <= $sum)
				{
					$data["itemdrop"] = $itemno;
					break;
				}
			}
		}

		// bluelovers
		// 修正當 mon 沒有設定行動條件時的 BUG
		if (empty($data['judge']))
		{
			// 沒有行動判定時 則預設為 一定
			$data['judge'][] = 1000;
		}

		if (empty($data['quantity']))
		{
			// 沒有行動條件時 則設定為 0
			$data['quantity'][] = 0;
		}

		if (empty($data['action']))
		{
			// 沒有設定技能時 為 攻擊
			$data['action'][] = 1000;
		}
		// bluelovers

		$data["monster"] = "1";

		return $data;
	}

	function newMon($no, $over = false)
	{
		if (is_string($no) || is_numeric($no))
		{
			$append = self::getBaseMonster($no, $over);
		}
		else
		{
			$append = $no;
		}

		$append = self::_fixArray($append);

		$char = new HOF_Class_Mon($append);

		return $char;
	}

	/**
	 * 召還系スキルで呼ばれたモンスター。
	 */
	function newMonSummon($no, $strength = false)
	{
		/*
		include_once (DATA_MONSTER);
		*/
		$monster = HOF_Model_Char::getBaseMonster($no, 1);

		$monster["summon"] = true;
		// 召喚モンスターの強化。
		if ($strength)
		{
			$monster["maxhp"] = round($monster["maxhp"] * $strength);
			$monster["hp"] = round($monster["hp"] * $strength);
			$monster["maxsp"] = round($monster["maxsp"] * $strength);
			$monster["sp"] = round($monster["sp"] * $strength);
			$monster["str"] = round($monster["str"] * $strength);
			$monster["int"] = round($monster["int"] * $strength);
			$monster["dex"] = round($monster["dex"] * $strength);
			$monster["spd"] = round($monster["spd"] * $strength);
			$monster["luk"] = round($monster["luk"] * $strength);

			$monster["atk"]["0"] = round($monster["atk"]["0"] * $strength);
			$monster["atk"]["1"] = round($monster["atk"]["1"] * $strength);
		}

		/*
		$monster = new monster($monster);
		*/
		$monster = HOF_Model_Char::newMon($monster);
		$monster->SetBattleVariable();
		return $monster;
	}

}
