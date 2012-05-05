<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
class HOF_Class_Battle extends battle
{

	function __construct($team0, $team1)
	{
		parent::__construct($team0, $team1);

		$this->objs['view'] = new HOF_Class_Battle_View(&$this);
	}

	function outputImage()
	{
		$output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)
			->setBg($this->BackGround)
			->setTeams($this->team1, $this->team0)
			->setMagicCircle($this->team1_mc, $this->team0_mc)
			->exec();

		echo $output;
	}

	function SkillEffect($skill, $skill_no, &$user, &$target)
	{
		if (!isset($this->objs['SkillEffect']))
		{
			$this->objs['SkillEffect'] = new HOF_Class_Skill_Effect(&$this);
		}

		return $this->objs['SkillEffect']->SkillEffect($skill, $skill_no, &$user, &$target);
	}

}
