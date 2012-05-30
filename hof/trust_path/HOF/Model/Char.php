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

	function getBaseCharList()
	{
		$_key = 'char';

		$_cache_key_ = $_key.'_base_list';

		if ($list = HOF::cache()->data($_cache_key_))
		{
			return $list;
		}

		$regex = HOF_Class_Data::_filename($_key, '*');

		$regex = '/^' . str_replace('\*', '(.+)', preg_quote($regex, '/')) . '$/i';

		foreach (glob(HOF_Class_Data::_filename($_key, '*')) as $file)
		{
			$list[] = preg_replace($regex, '$1', $file);
		}

		sort($list, SORT_NUMERIC);

		HOF::cache()->data($_cache_key_, $list);

		return $list;
	}

	/**
	 * $char = HOF_Class_Yaml::load(BASE_TRUST_PATH . '/HOF/Resource/Char/char.' . $no . '.yml');
	 */
	function getBaseCharStatus($no, $append = array())
	{
		$_cache_key_ = 'char_base';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = self::getInstance()->_load('char', $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		unset($data['name']);

		/*
		$data['id'] = HOF_Helper_Char::uniqid('char');
		*/
		$data['birth'] = HOF_Helper_Char::uniqid_birth();

		if (!empty($append))
		{
			$append = HOF_Class_Array::_fixArray($append);

			$data = array_merge($data, (array )$append);
		}

		return $data;
	}

	/**
	 * @return HOF_Class_Char_Type_Char
	 */
	function newBaseChar($jobNo, $append = array())
	{
		/*
		$char = self::newChar(self::getBaseCharStatus($jobNo, $append));
		*/
		$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, 'char:'.$jobNo, array('append' => $append));

		return $char;
	}

	/*
	function newChar($append = array())
	{
		$char = new HOF_Class_Char_Type_Char();

		if (!empty($append))
		{
			$append = HOF_Class_Array::_fixArray($append);

			$char->setCharData($append);
		}

		return $char;
	}
	*/

	/*
	function newCharFromFile($file = null)
	{
		$char = new HOF_Class_Char_Type_Char($file);

		return $char;
	}
	*/

	function getUnionDataBase($no)
	{
		$_key = 'union';
		$_cache_key_ = $_key.'_base';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = self::getInstance()->_load($_key, $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		return $data;
	}

	function getUnionDataMon($no)
	{
		$_key = 'union';
		$_cache_key_ = $_key.'_mon';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = false;

			if ($union_base = self::getUnionDataBase($no))
			{
				$data = self::getInstance()->_load($union_base['data']['base']['type'], $union_base['data']['base']['no']);

				$data = array_merge($data, (array)$union_base['data_ex']);

				$data['_source_'] = $union_base;

				$list[$no] = $data;

				HOF::cache()->data($_cache_key_, $list);
			}
		}

		if ($data)
		{
			$data = self::_fixMonData($data);
		}

		return $data;
	}

	function getUnionList()
	{
		$_key = 'union';
		$_cache_key_ = $_key.'_list';

		if ($list = HOF::cache()->data($_cache_key_))
		{
			return $list;
		}

		$list = self::getInstance()->_load_list($_key);

		HOF::cache()->data($_cache_key_, $list);

		return $list;
	}

	function getUnionFile($no, $skip = false)
	{
		$file = BASE_PATH_UNION.'union.'.$no.'.yml';

		if (!$skip && !file_exists($file))
		{
			self::getUnionData($no, true);
		}

		return $file;
	}

	function getUnionData($no, $skip = false)
	{
		$all = self::getUnionList();

		if (in_array($no, (array)$all))
		{
			$file = self::getUnionFile($no, true);

			if (!file_exists($file))
			{

				HOF_Class_File::mkdir(dirname($file));

				$union_base = self::getUnionDataBase($no);
				$union_mon = self::getUnionDataMon($no);

				unset($union_base['data_ex']);

				$union_base['name'] = $union_mon['name'];

				$union_base['last_death'] = 0;
				$union_base['hp'] = $union_mon['hp'];
				$union_base['sp'] = $union_mon['sp'];
				$union_base['cycle'] = $union_mon['cycle'];
				$union_base['land'] = $union_mon['land'];
				$union_base['img'] = $union_mon['img'];

				HOF_Class_Yaml::save($file, $union_base);
			}

			if ($skip) return;

			$data = HOF_Class_Yaml::load($file);

			return $data;
		}
		else
		{
			return false;
		}
	}

	function newUnion($no)
	{
		/*
		$file = self::getUnionFile($no);

		$char = new HOF_Class_Char_Type_UnionMon($file);
		*/
		$char = HOF_Class_Char::factory(array(HOF_Class_Char::TYPE_UNION, HOF_Class_Char::TYPE_MON), $no);

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

	 * "position"	=> "POSITION_FRONT or POSITION_BACK",//指定する事で位置を後列or前列に固定できる。
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
	 * "servant" = array(
	 * 敵番号 => (確立,0)
	 * );
	 * "land" = 土地(背景)

	 * 必ず出現する雑魚を指定する
	 * "servantSpecify"	=> array(敵番号, ),
	 * "name" = ユニオンの団体の名称
	 * "lv_limit" = レベル制限

	 * 雑魚の出現数を指定する
	 * 省略してもOK
	 * "servantAmount" => "6",
	 */
	function getBaseMonster($no, $over = false)
	{
		$data = self::getInstance()->_load('mon', $no);

		if (!$data) return false;

		$data = self::_fixMonData($data, $over);

		return $data;
	}

	function _fixMonData($data, $over = null)
	{
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

		$data["monster"] = "1";

		return $data;
	}

	function newMon($no, $over = false)
	{
		/*
		if (is_string($no) || is_numeric($no))
		{
			$append = self::getBaseMonster($no, $over);
		}
		else
		{
			$append = $no;
		}

		$append = HOF_Class_Array::_fixArray($append);

		$char = new HOF_Class_Char_Type_Mon($append);
		*/
		$char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_MON, $no);

		return $char;
	}

	/**
	 * 召還系スキルで呼ばれたモンスター。
	 */
	function newMonSummon($no, $strength = false)
	{
		$char = HOF_Model_Char::newMon(array(HOF_Class_Char::TYPE_MON, HOF_Class_Char::TYPE_SUMMON), array('strength' => $strength));

		$char->setBattleVariable();

		return $char;

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
		$monster->setCharType(HOF_Class_Char::TYPE_SUMMON);
		$monster->setBattleVariable();
		return $monster;
	}

}
