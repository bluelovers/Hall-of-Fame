<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Gamedata extends HOF_Class_Controller
{

	function _main_before()
	{
		if ($this->action == self::DEFAULT_ACTION)
		{
			$this->action = 'job';
		}
	}

	function _main_action_job()
	{
		$list = HOF_Model_Data::getJobList();

		/**
		 * ここでしか必要無いので 職データには書きません。
		 */
		$job_skill = array(
			100 => array(
				1001,
				3110,
				3120),
			101 => array(
				1012,
				1023,
				1019),
			102 => array(
				1100,
				1114,
				1118),
			103 => array(
				1020,
				2090,
				3215),
			200 => array(
				1002,
				2011,
				3011),
			201 => array(
				2001,
				2024,
				2015),
			202 => array(
				3020,
				2500,
				2501),
			203 => array(
				2030,
				2050,
				2460),
			300 => array(
				3000,
				3101,
				2100),
			301 => array(
				2101,
				3220,
				2481),
			302 => array(
				3050,
				3055,
				3060),
			400 => array(
				2300,
				2301,
				2302),
			401 => array(
				2305,
				2306,
				2307),
			402 => array(
				2405,
				2406,
				3300),
			403 => array(
				1200,
				1207,
				1204),
			);

		$list_data = array();

		foreach ($list as $no)
		{
			$data = HOF_Model_Data::getJobData($no);

			$data['skill'] = array();

			foreach ($job_skill[$no] as $v)
			{
				$data['skill'][$v] = HOF_Model_Data::getSkill($v);
			}

			$data["equip"] = implode(', ', $data["equip"]);

			$list_data[$no] = $data;
		}

		$this->output->list = $list_data;

		$this->options['escapeHtml'] = false;
	}

	function _main_action_item()
	{
		$ItemList = array(
			"武器(Weapon)" => array(
				1000,
				1100,
				1700,
				1800,
				2000),
			"盾(Shield)" => array(
				3000,
				3001,
				3100,
				3101),
			"鎧(Armor)" => array(
				5000,
				5001,
				5100,
				5101,
				5200,
				5202),
			"アイテム(Item)" => array(5500, 5501),
			"素材(Material)" => array(
				6000,
				6001,
				6040,
				6180,
				6800,
				7000),
			);

		$list = array();

		foreach ($ItemList as $Type => $ItemNoArray)
		{
			foreach ($ItemNoArray as $ItemNo)
			{
				$item = HOF_Model_Data::getItemData($ItemNo);

				$list[$Type][$ItemNo] = $item;
			}
		}

		$this->output->list = $list;
	}

	function _main_action_judge()
	{
		$_list = HOF_Model_Data::getJudgeList();

		$list = array();

		foreach ($_list as $no)
		{
			$data = HOF_Model_Data::getJudgeData($no);

			if ($data['tag']['no'])
			{
				$list[$data['tag']['no']]['list'][$no] = $data;
			}
			else
			{
				$list[$no]['tag'] = $data;
			}
		}

		$this->output->list = $list;
	}

	function _main_action_monster()
	{
		$map_list = array(
			1000 => array("grass", "SPがあるときは、強い攻撃をたまにしてくる程度。"),
			1001 => array("grass", "SPがあるときは、強い攻撃をたまにしてくる程度。"),
			1002 => array("grass", "後列に押し出す攻撃をする。"),
			1003 => array("grass", "そこそこな強さ。"),
			1005 => array("grass", "レベルが低いと強く感じる。"),
			1009 => array("grass", "HPが高い。"),
			1012 => array("cave", "仲間を呼んで吸血攻撃をしてくる。"),
			1014 => array("cave", "魔法で攻撃しないと倒しにくい。"),
			1017 => array("cave", "洞窟のボス。倒すと奥に行けるようになる。"),
			);

		$list = array();

		foreach ($map_list as $No => $exp)
		{
			$monster = HOF_Model_Char::getBaseMonster($No);
			$char = HOF_Model_Char::newMon($No);

			$char->land = $exp[0];

			$data['char'] = $char;
			$data['monster'] = $monster;

			$list[] = $data;
		}

		$this->output->list = $list;
	}

}
