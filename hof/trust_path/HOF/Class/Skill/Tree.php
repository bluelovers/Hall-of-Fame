<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Skill_Tree
{

	protected $char;

	function __construct($char)
	{
		$this->char = $char;
	}

	public function skill_tree()
	{
		$_skill = (array )$this->char->skill;
		$_job = $this->char->job();
		$_lv = $this->char->level;

		$tree_all = HOF_Model_Data::getSkillTreeListByJob($_job);

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

		return $list;
	}

}
