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

}
