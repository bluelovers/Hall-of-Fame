<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Data extends HOF_Class_Data
{

	protected static $_instance;

	public static $_cache_;

	/**
	 * @return self
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
	 * @return self
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function _load_list_cache($_key, $sort = null)
	{
		$_cache_key_ = $_key.'_list';

		if ($list = HOF::cache()->data($_cache_key_))
		{
			return $list;
		}

		$list = self::getInstance()->_load_list($_key, $sort);

		HOF::cache()->data($_cache_key_, $list);

		return $list;
	}

	/**
	 * こちらは技に関する基本的な情報
	 *
	 * 1000 - 2999 攻撃系
	 * 2000 - 魔法系らしい
	 * 2110 - 詠唱中のキャラのみに適応する。
	 * 2300 - 弓系列
	 * 2400 - 召喚系
	 * 2500 - まだ召喚系
	 * 3000 - 他
	 * 3300 - 召喚キャラ強化系
	 * 3400 - 持続回復系
	 * 3410 - 魔法陣を描く系
	 * 3420 - 魔法陣を消す系
	 * 3900 - テストに便利な技
	 * 5000 - 5999 EnemySkills
	 */
	function getSkill($no)
	{
		/**
		 * "name"	=> "名前",
		 * "img"	=> "skill_042.png",//画像
		 * "exp"	=> "技の説明",
		 * "sp"	=> "消費sp",
		 * "type"	=> "0",//0=物理 1=魔法
		 * "target"=> array(friend/enemy/all/self,individual/multi/all,攻撃回数),
		 * ----(例)----------------------------------------
		 * frien/enemy	= 味方/敵
		 * all			= 味方+敵 全体
		 * self		= 自身に
		 * enemy individual 1	= 敵一人に1回
		 * enemy individual 3	= 敵一人に3回
		 * enemy multi 3		= 敵(誰か3人)に1回づつ(重複の可能性有り)
		 * enemy all 1			= 敵全員に1回攻撃
		 * all individual 5	= 味方敵全体の誰か一人に5回
		 * all multi 5			= 味方敵全体の誰か5人に1回づつ(重複の可能性有り)
		 * all all 3			= 味方敵全員に3回づつ
		 * ------------------------------------------------
		 * "pow"	=> "100",// 100で割った物が倍率になる... 130=1.3倍 100 が基本。
		 * "hit"	=> "100",// (多分消した...技の成功率...?)
		 * "invalid"	=> "1",//後衛をかばう動作を無効化
		 * "support"	=> "1",//味方の支援魔法(↑と区別が必要)
		 * "priority"	=> "LowHpRate",//ターゲットの優先(LowHpRate,Dead,Summon,Charge)
		 * "charge"	=> "",//いわゆる詠唱完了までの時間やら、力の貯め時間等(0=詠唱無し)
		 * "stiff"	=> "",//行動後の硬直時間(0=硬直無し 100=待機時間2倍(待機時間=硬直時間) )
		 * "charge" => array(charge,stiff),//配列に変更。
		 * "learn"	=> "習得に必要なポイント数",
		 * "Up**"
		 * "Down**"
		 * "pierce"
		 * "delay"
		 * "knockback"
		 * "poison"
		 * "summon"
		 * "move"
		 * "strict" => array("Bow"=>true),//武器制限
		 * "umove" // 使用者が移動。
		 * "DownSTR"	=> "40",// IND DEX SPD LUK ATK MATK DEF MDEF HP SP
		 * "UpSTR"
		 * "PlusSTR"	=> 50,
		 */
		$data = self::getInstance()->_load('skill', $no);

		if (isset($data['summon']))
		{
			// 配列じゃなかったら要素1個の配列にしちゃう。
			$data['summon'] = (array )$data['summon'];
		}

		return $data;
	}

	function getSkillList()
	{
		$_key = 'skill';
		return self::_load_list_cache($_key);
	}

	function getSkillTreeData($no)
	{
		$_key = 'skilltree';
		$_cache_key_ = $_key;

		$data = self::getInstance()->_load($_key, $no);

		/*
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
		*/

		return $data;
	}

	function getSkillTreeListByJob($_job)
	{
		$_cache_key_ = 'skilltree_job';

		$list_all = HOF::cache()->data($_cache_key_);

		if (!$_job) $_job = 0;

		if (isset($list_all[$_job]))
		{
			return $list_all[$_job];
		}

		$tree_all = HOF_Model_Data::getSkillTreeList();

		$list = array();

		foreach ($tree_all as $skill)
		{
			if ($data = HOF_Model_Data::getSkillTreeData($skill))
			{
				foreach ($data['check'] as $check_list)
				{
					$_check = -1;

					if ($check_list['not'])
					{
						$_check = true;

						foreach ((array )$check_list['not'] as $k => $check_data)
						{
							switch ($k)
							{
								case 'job':
									if (in_array($_job, $check_data))
									{
										$_check = false;
									}
									break;
									/*
								case 'skill':
									if (array_intersect($check_data, $_skill))
									{
										$_check = false;
									}
									break;
								case 'lv':
									foreach ($check_data as $_v)
									{
										if ($_lv >= $_v)
										{
											$_check = false;
											break;
										}
									}
									break;
									*/
							}

							if ($_check === false)
							{
								break;
							}
						}
					}

					if ($_check && $check_list['or'])
					{
						$_check = true;

						foreach ((array )$check_list['or'] as $k => $check_data)
						{
							switch ($k)
							{
								case 'job':
									if (!in_array($_job, $check_data))
									{
										$_check = false;
									}
									break;
									/*
								case 'skill':
									if (!array_intersect($check_data, $_skill))
									{
										$_check = false;
									}
									break;
								case 'lv':
									foreach ($check_data as $_v)
									{
										if ($_lv < $_v)
										{
											$_check = false;
											break;
										}
									}
									break;
									*/
							}

							if ($_check === false)
							{
								break;
							}
						}
					}

					if ($_check && $check_list['and'])
					{
						$_check = true;

						foreach ((array )$check_list['and'] as $k => $check_data)
						{
							switch ($k)
							{
								case 'job':
									if (!in_array($_job, $check_data))
									{
										$_check = false;
									}
									break;
									/*
								case 'skill':
									if ($check_data != array_intersect($check_data, $_skill))
									{
										$_check = false;
									}
									break;
								case 'lv':
									foreach ($check_data as $_v)
									{
										if ($_lv < $_v)
										{
											$_check = false;
											break;
										}
									}
									break;
									*/
							}

							if ($_check === false)
							{
								break;
							}
						}
					}

					if ($_check === true)
					{
						$list[] = $skill;
						break;
					}
				}
			}
		}

		$list_all[$_job] = (array)$list;

		HOF::cache()->data($_cache_key_, $list_all);

		return $list;
	}

	function getSkillTreeList()
	{
		$_key = 'skilltree';
		return self::_load_list_cache($_key);
	}

	function getLandList()
	{
		$_key = 'land';
		return self::_load_list_cache($_key, SORT_STRING | SORT_NUMERIC | SORT_ASC);
	}

	/**
	 * まっぷの出現条件判定
	 */
	function getLandAppear($user)
	{
		$list = array();

		if ($lands = self::getLandList())
		{
			foreach ($lands as $no)
			{
				if ($land = self::getLandData($no))
				{
					$allow = 1;

					if ($land['trigger'])
					{
						$allow = -1;

						if ($allow != 0)
						{
							foreach ((array )$land['trigger']['item'] as $_data)
							{
								$ok = -1;

								foreach ($_data as $_k => $_v)
								{
									$ok = 1;

									if (!$user->item[$_k] || $user->item[$_k] < $_v)
									{
										$ok = 0;
										break;
									}
								}

								if ($ok > 0)
								{
									$allow = abs($allow) + 1;

									break;
								}

								$allow = 0;
							}
						}

						if ($allow != 0)
						{
							foreach ((array )$land['trigger']['time'] as $_data)
							{
								$ok = -1;

								foreach ($_data as $_k => $_v)
								{
									$ok = 1;

									if (HOF_Helper_Global::gc_date($_k) != $_v)
									{
										$ok = 0;
										break;
									}
								}

								if ($ok > 0)
								{
									$allow = abs($allow) + 1;

									break;
								}

								$allow = 0;
							}
						}
					}

					$_data = $allow;

					/**
					 * @global DEBUG_LANDAPPEAR_ALL - force allow land
					 *
					 * @var $allow
					 * 			=0 => fail
					 * 			>0 => ok
					 * 			<0 => error
					 */
					if (DEBUG_LANDAPPEAR_ALL)
					{
						$_data = array();
						$_data['_cache']['allow'] = $allow;

						$allow = DEBUG_LANDAPPEAR_ALL;
					}

					if ($allow > 0)
					{
						$list[$no] = $_data;
					}
				}
			}
		}

		return $list;

		/*

		// 無条件
		array_push($list, "gb0", "gb1", "gb2");

		// アイテムがあれば行ける。
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8000"]) array_push($list, "ac0");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8001"]) array_push($list, "ac1");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8002"]) array_push($list, "ac2");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8003"]) array_push($list, "ac3");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8004"]) array_push($list, "ac4");

		if (DEBUG_LANDAPPEAR_ALL || $user->item["8009"]) array_push($list, "snow0");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8010"]) array_push($list, "snow1");
		if (DEBUG_LANDAPPEAR_ALL || $user->item["8011"]) array_push($list, "snow2");

		if (DEBUG_LANDAPPEAR_ALL)
		{
		array_push($list, "sea0");
		array_push($list, "sea1");
		array_push($list, "ocean0");
		array_push($list, "sand0");
		array_push($list, "swamp0");
		array_push($list, "swamp1");
		array_push($list, "mt0");
		array_push($list, "volc0");
		array_push($list, "volc1");

		array_push($list, "blow01");
		array_push($list, "plund01");
		array_push($list, "des01");
		}

		if (DEBUG_LANDAPPEAR_ALL || HOF_Helper_Global::gc_date("H") == 2 && substr(HOF_Helper_Global::gc_date("i"), 0, 1) == 5) array_push($list, "horh");
		return $list;

		*/
	}

	/**
	 * 通常戦闘における場所の情報
	 * 基本的情報、出現する敵等
	 *
	 * name0 = 未使用
	 * proper = 未使用
	 * land = 土地の画像に対応する
	 *
	 *** 敵の出現確率の設定について
	 * $monster = array(
	 * 敵番号 = array("確率",1=表示 0=隠し敵);
	 * )
	 *
	 * 確率の合計は気にしなくていい。
	 * 出現確率 = ("確率"/全体の確率合計)
	 */
	function getLandData($no)
	{
		$_cache_key_ = 'land';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = self::getInstance()->_load('land', $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		/*
		return array($data['land'], $data['monster']);
		*/
		return $data;
	}

	/**
	 * 判断材料
	 * HP
	 * SP
	 * 人数(自分だけ生存等)
	 * 状態(毒等)？？？
	 * 自分の行動回数
	 * 回数限定
	 * 相手の状態
	 * 単純な確率
	 */
	function getJudgeData($no)
	{
		$_cache_key_ = 'judge';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = self::getInstance()->_load('judge', $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		return $data;
	}

	/**
	 * $data['job_from'] 転職可能な職
	 * $data['job_to'] キャラがそのクラスに転職できるか(クラスの転職条件)
	 */
	function getJobConditions()
	{
		$_cache_key_ = 'job_conditions';

		if ($data = HOF::cache()->data($_cache_key_))
		{
			return $data;
		}

		$job_list = self::getJobList();

		foreach($job_list as $job)
		{
			$jobdata = self::getJobData($job);

			foreach ((array)$jobdata['data_ex']['job_conditions']['job_from'] as $k => $v)
			{
				/**
				 * 転職可能な職
				 */
				$data['job_from'][$k][] = $job;

				/**
				 * キャラがそのクラスに転職できるか(クラスの転職条件)
				 */
				$data['job_to'][$job][$k] = $v;
			}
		}

		HOF::cache()->data($_cache_key_, $data);

		return $data;
	}

	function getJobList()
	{
		$_key = 'job';
		return self::_load_list_cache($_key);
	}

	function getJudgeList()
	{
		$_key = 'judge';
		return self::_load_list_cache($_key);
	}

	/**
	 * @return HOF_Class_Item
	 */
	function newItem($no, $check = true)
	{
		return HOF_Class_Item::newInstance($no, $check);
	}

	/**
	 * 1000-1100	片手剣
	 * 1300-1400	両手槍
	 * 1400-1500	片手槍
	 * 1500-1600	両手斧
	 * 1600-1700	片手斧
	 * 1700-1800	片手杖
	 * 1800-1900	両手杖
	 * 1900-2000	鈍器(片手)
	 * 2000-2100	弓
	 * 2100-2199	石弓
	 * 2200-2299	鞭
	 * 2210			鞭
	 * 3000			盾
	 * 3100			本
	 * 5000-5100	鎧
	 * 5100-5200	服
	 * 5200-5300	衣
	 * 5500			装飾品
	 * 6000			素材系 石系
	 * 6020			木材
	 * 6040			皮
	 * 6060			骨
	 * 6080			牙
	 * 6100			羽
	 * 6120			宝石
	 * 6140			音
	 * 6160			コイン
	 * 6180			糸,繊維
	 * 6200			音
	 * 6600			ゴミ
	 * 6800			レア
	 * 7000			製作強化系
	 * 7100			製作強化系(モンスターレアドロップ)
	 * 7500			他消耗品
	 * 8000			地図,カギ
	 * 9000			その他
	 */
	function getItemData($no, $source = false)
	{
		if (!$no)
		{
			return false;
		}

		/**
		 * 設定項目
		 * ---------------------------------------------
		 * "name"=>"名称",
		 * "type"=>"種類",
		 * "buy"=>"買値",
		 * "img"=>"画像",
		 * "atk"=>array(物理攻撃,魔法攻撃),
		 * "def"=>array(物理割?,物理減,魔法割?,魔法減),
		 * "dh"=> true,//両手武器か否か( "D"ouble"H"and )
		 * "handle"=>"数値",
		 * "need" => array("素材番号"=>数, ...),// 製作に必要なアイテム
		 * ---------------------------------------------
		 * type
		 * "Sword"	片手剣
		 * "TwoHandSword"	両手剣
		 * "Dagger"	短剣
		 * "Spear"	両手槍
		 * "Pike"	片手槍
		 * "Axe"	両手斧
		 * "Hatchet"片手斧
		 * "Wand"	片手杖
		 * "Staff"	両手杖
		 * "Mace"	鈍器(片手)
		 * "Bow"	弓
		 * "CrossBow"	石弓
		 *
		 * "Shield"	盾
		 * "MainGauche"	防御用短剣
		 * "Book"	本
		 *
		 * "Armor"	鎧
		 * "Cloth"	服
		 * "Robe"	衣
		 *
		 * "?"
		 *--------------------------------------------
		 * 追加オプション
		 * P_MAXHP
		 * M_MAXHP
		 * P_MAXSP
		 * M_MAXSP
		 * P_STR
		 * P_INT
		 * P_DEX
		 * P_SPD
		 * P_LUK
		 * P_SUMMON = 召還力強化
		 * P_PIERCE = array(物理,魔法),
		 *--------------------------------------------
		 */

		/**
		 * アイテムの種類
		 */
		$base = substr($no, 0, 4);

		$data = self::getInstance()->_load('item', $base);

		$data["id"] = $no;

		if ($source) return $data;

		$data = HOF_Helper_Item::parseItemData($data, $no);

		return $data;
	}

	function getItemCreateMaterialList($key = 'material')
	{
		if (!$list_material = HOF::cache()->data('smithy_create_material_list'))
		{
			self::getItemCreateList(true);

			$list_material = HOF::cache()->data('smithy_create_material_list');
		}

		if (!$key) $key = 'material';

		return $list_material[$key];
	}

	function getItemCreateList($over = false)
	{
		if ($over || !$list = HOF::cache()->data('smithy_create_list'))
		{
			$list = array();

			$list_material = array();
			$list_create = array();

			$_list = HOF_Model_Data::getItemList();

			foreach ($_list as $no)
			{
				$item = HOF_Model_Data::getItemData($no);

				if (!empty($item['need']))
				{
					$list[] = $no;

					foreach(array_keys($item['need']) as $v)
					{
						$list_material['material'][] = $v;
					}

					$list_create[$no] = array(
						'need' => $item['need'],
					);
				}

				if ($item['type'] = 'Material' && $item['Add'])
				{
					$list_material['material'][] = $no;

					$list_material['material_plus'][] = $no;

					$list_material['add_plus'][] = $item['Add'];
				}
			}

			foreach ($list_material as &$v)
			{
				$v = array_unique($v);
				sort($v);
			}

			HOF::cache()->data('smithy_create_list', $list);
			HOF::cache()->data('smithy_create_material_list', $list_material);
			HOF::cache()->data('smithy_create', $list_create);
		}

		return $list;
	}

	function getItemCreateData($no)
	{
		if ($list = HOF::cache()->data('smithy_create'))
		{
			if ($list[$no])
			{
				return $list[$no];
			}
		}

		$list_all = self::getItemCreateList();

		if (!in_array($no, $list_all))
		{
			return false;
		}

		$item = self::getItemData($no);

		$list[$no] = array(
			'need' => $item['need'],
		);

		HOF::cache()->data('smithy_create', $list);

		return $list[$no];
	}

	function getItemList()
	{
		$_key = 'item';
		return self::_load_list_cache($_key);
	}

	/**
	 * 職の男性,女性名
	 * 職の画像
	 * 装備可能な物
	 * "coe"	=> array(HP係数 ,SP係数),
	 * "change"	=> array(転職可能な職),
	 */
	function getJobData($no)
	{
		$_cache_key_ = 'job';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$data = self::getInstance()->_load('job', $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		return $data;
	}

	/**
	 * 店に売ってるものデータ
	 * 店販売リスト
	 */
	function getShopList()
	{
		return array(
			1002,
			1003,
			1004,
			1100,
			1101,
			1200,
			1700,
			1701,
			1702,
			1703,
			1800,
			1801,
			2000,
			2001,
			3000,
			3001,
			3002,
			3100,
			3101,
			5000,
			5001,
			5002,
			5003,
			5100,
			5101,
			5102,
			5103,
			5200,
			5201,
			5202,
			5203,
			5500,
			5501,
			7000,
			7001,
			7500,
			//7510,7511,7512,7513,7520, // リセット系アイテム
			8000,
			8009,
			);
	}

	/**
	 * オークションに出品可能なアイテムの種類
	 */
	function getCanExhibitType()
	{
		return array(
			"Sword" => "1",
			"TwoHandSword" => "1",
			"Dagger" => "1",
			"Wand" => "1",
			"Staff" => "1",
			"Bow" => "1",
			"Whip" => "1",
			"Shield" => "1",
			"Book" => "1",
			"Armor" => "1",
			"Cloth" => "1",
			"Robe" => "1",
			"Item" => "1",
			"Material" => "1",
			);
	}

	/**
	 * 精錬可能なアイテムの種類
	 */
	function getCanRefineType()
	{
		return array(
			'sword' => 'Sword',
			'two_hand_sword' => 'TwoHandSword',
			'dagger' => 'Dagger',
			'wand' => 'Wand',
			'staff' => 'Staff',
			'bow' => 'Bow',
			'whip' => 'Whip',
			'shield' => 'Shield',
			'book' => 'Book',
			'armor' => 'Armor',
			'cloth' => 'Cloth',
			'robe' => 'Robe',
			);
	}

	/**
	 * 町でいける店とかの出現条件とか...
	 * 日付別でいける場所を変えれるとか、
	 * あるアイテムがないと行けないとかできる
	 * 別ファイルにする必要があったのかどうか微妙
	 *
	 * @param HOF_Class_User
	 */
	function getTownAppear($user = null)
	{
		$place = array();

		// 無条件で行ける
		$place["Shop"] = true;
		$place["Recruit"] = true;
		$place["Smithy"] = true;
		$place["Auction"] = true;
		$place["Colosseum"] = true;

		// 特定のアイテムがないと行けない施設
		//if($user->item[****])
		//	$place["****"]	= true;

		return $place;
	}

	/**
	 * 戦闘ログの詳細を表示(リンク)
	 */
	function getLogBattleFile($file, $dir = null, $full = false)
	{
		$file = $dir . $file;

		if (!file_exists($file))
		{
			//ログが無い
			return false;
		}

		HOF_Class_File::opened_files_add($file);

		$fp = fopen($file, "r");

		// 数行だけ読み込む。

		// 開始時間 1行目
		$time = trim(fgets($fp));

		// チーム名 2行目
		$team = explode("<>", fgets($fp));

		// 人数 3行目
		$number = explode("<>", trim(fgets($fp)));

		// 平均レベル 4行目
		$avelv = explode("<>", trim(fgets($fp)));

		// 勝利チーム 5行目
		$win = trim(fgets($fp));

		// 総行動数 6行目
		$act = trim(fgets($fp));

		$contents = '';

		if ($full)
		{
			$contents = stream_get_contents($fp);

			/*
			while (!feof($fp))
			{
				$contents .= fread($fp, 8192);
			}
			*/

		}

		fclose($fp);

		$date = HOF_Helper_Global::gc_date("m/d(D) H:i:s", substr($time, 0, 10));
		// 勝利チームによって色を分けて表示

		return array(

			'time' => $time,
			'team' => $team,
			'number' => $number,
			'avelv' => $avelv,
			'win' => $win,
			'act' => $act,
			'date' => $date,
			'contents' => $contents,

			);
	}

	function getLogBattle($no, $type = false, $full = false)
	{
		$file = $no . '.dat';

		if ($type == LOG_BATTLE_RANK)
		{
			$dir = LOG_BATTLE_RANK;
		}
		elseif ($type == LOG_BATTLE_UNION)
		{
			$dir = LOG_BATTLE_UNION;
		}
		else
		{
			$dir = LOG_BATTLE_NORMAL;
		}

		$data = self::getLogBattleFile($file, $dir, $full);

		return $data;
	}

	function getColorList()
	{
		$data = file(COLOR_FILE);

		foreach ($data as &$value)
		{
			$value = trim($value);
		}

		return $data;
	}

	/**
	 * 前衛の時の後衛守り
	 *
	 * "always"=> "Always",
	 * "never"	=> "Never",
	 * "life25"	=> "If life more than 25%",
	 * "life50"	=> "If life more than 50%",
	 * "life75"	=> "If life more than 75%",
	 * "prob25"	=> "Probability of 25%",
	 * "prpb50"	=> "Probability of 50%",
	 * "prob75"	=> "Probability of 75%",
	 *
	 * "always" => "必ず守る",
	 * "never" => "守らない",
	 * "life25" => "体力が 25%以上なら 守る",
	 * "life50" => "体力が 50%以上なら 守る",
	 * "life75" => "体力が 75%以上なら 守る",
	 * "prob25" => "25%の確率で 守る",
	 * "prpb50" => "50%の確率で 守る",
	 * "prob75" => "75%の確率で 守る",
	 */
	function getGuardData($no, $default = 'always')
	{
		$_cache_key_ = 'guard';

		$list = HOF::cache()->data($_cache_key_);

		if (isset($list[$no]))
		{
			$data = $list[$no];
		}
		else
		{
			$_key = 'guard';

			$data = self::getInstance()->_load($_key, $no);

			$list[$no] = $data;

			HOF::cache()->data($_cache_key_, $list);
		}

		if (!$data && $default)
		{
			return self::getGuardData($default);
		}

		return $data;
	}

	function getGuardList()
	{
		$_key = 'guard';
		return self::_load_list_cache($_key, SORT_STRING | SORT_NUMERIC | SORT_ASC);
	}

	function getChatAttrBaseList()
	{
		$list = array(
			'str',
			'int',
			'dex',
			'spd',
			'luk',
			);

		return $list;
	}

}
