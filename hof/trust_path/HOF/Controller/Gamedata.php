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
		$gamedata = HOF::cache()->data('gamedata');

		if (!isset($gamedata['job']))
		{

			$_data = HOF_Model_Data::getJobConditions();

			$list_data = $list_job = array();

			foreach ($_data['job_from'] as $job => $v)
			{
				$list_job[] = $job;

				foreach($v as $k)
				{
					$list_job[] = $k;
				}
			}

			foreach ($list_job as $job)
			{
				$data = HOF_Model_Data::getJobData($job);

				$skills = HOF_Model_Data::getSkillTreeListByJob($job);

				array_shift($skills);
				array_pop($skills);

				shuffle($skills);

				$skills_key = array_rand((array)$skills, 3);

				$data['skill'] = array();

				foreach ($skills_key as $k)
				{
					$v = $skills[$k];

					$data['skill'][$v] = HOF_Model_Data::getSkill($v);
				}

				ksort($data['skill']);

				$data["equip"] = implode(', ', $data["equip"]);

				$list_data[$job] = $data;
			}


			$gamedata['job']['job_from'] = $_data['job_from'];
			$gamedata['job']['list_data'] = $list_data;

			HOF::cache()->data('gamedata', $gamedata);
		}

		$this->output->job_from = $gamedata['job']['job_from'];

		$this->output->list = $gamedata['job']['list_data'];

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
