<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Model_Data extends HOF_Class_Data
{

	protected static $_instance;

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

		return $data;
	}

	/**
	 * まっぷの出現条件判定
	 */
	function getLandAppear($user)
	{
		$land = array();

		// 無条件
		array_push($land, "gb0", "gb1", "gb2");

		// アイテムがあれば行ける。
		if ($user->item["8000"]) array_push($land, "ac0");
		if ($user->item["8001"]) array_push($land, "ac1");
		if ($user->item["8002"]) array_push($land, "ac2");
		if ($user->item["8003"]) array_push($land, "ac3");
		if ($user->item["8004"]) array_push($land, "ac4");

		if ($user->item["8009"]) array_push($land, "snow0");
		if ($user->item["8010"]) array_push($land, "snow1");
		if ($user->item["8011"]) array_push($land, "snow2");

		/*
		array_push($land,"sea0");
		array_push($land,"sea1");
		array_push($land,"ocean0");
		array_push($land,"sand0");
		array_push($land,"swamp0");
		array_push($land,"swamp1");
		array_push($land,"mt0");
		array_push($land,"volc0");
		array_push($land,"volc1");

		array_push($land,"blow01");
		array_push($land,"plund01");
		array_push($land,"des01");
		*/

		if (gc_date("H") == 2 && substr(gc_date("i"), 0, 1) == 5) array_push($land, "horh");
		return $land;
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
	function getLandInfo($no)
	{
		$data = self::getInstance()->_load('land', $no);

		return array($data['land'], $data['monster']);
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
		$data = self::getInstance()->_load('judge', $no);

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
			"Sword",
			"TwoHandSword",
			"Dagger",
			"Wand",
			"Staff",
			"Bow",
			"Whip",
			"Shield",
			"Book",
			"Armor",
			"Cloth",
			"Robe",
			);
	}

}
