<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Skill extends HOF_Class_Base_ObjectAttr
{

	function __construct($no)
	{
		if (is_array($no))
		{
			$data = $no;
		}
		else
		{
			$data = HOF_Model_Data::getSkill($no, true);
		}

		$_source_data_ = $data;

		parent::__construct((array)$data);

		$this->id = $this->no;
		$this->icon = $this->img;

		$this->_source_data_ = $_source_data_;

		return $this;
	}

	function exists()
	{
		return ($this->no) ? true : false;
	}

	function &newCopy($clone = false)
	{
		if ($clone)
		{
			$item = clone $this;
		}
		else
		{
			$item = new self($this->no);
		}

		return $item;
	}

	function &newInstance($no, $check = true)
	{
		if ($check && ($no instanceof self))
		{
			return $no;
		}

		return new self($no);
	}

	function id($over = false)
	{
		$var = $this->id;
		$var = printf("[%04s]", $var);

		return (string)$var;
	}

	function no()
	{
		$var = $this->no;
		$var = printf("[%04s]", $var);

		return (string)$var;
	}

	function __toString()
	{
		return $this->id();
	}

	function icon($url = false, $true = false)
	{
		$icon = $true ? $this->img : $this->icon;

		if ($url)
		{
			return HOF_Class_Icon::getImageUrl($icon, HOF_Class_Icon::IMG_SKILL, $true);
		}
		else
		{
			return HOF_Class_Icon::getImage($icon, HOF_Class_Icon::IMG_SKILL, $true);
		}
	}

	function html($radio = false, $text = true)
	{
		$data = $this->toArray();
		$data['img'] = $this->icon();
		$data['name'] = $this->name();

		return self::ShowSkillDetail($data, $radio, $text);
	}

	function name()
	{
		$name = $this->name;

		if (!$name)
		{
			$name = self::UNKNOW_NAME.'#'.$this->id();
		}

		return $name;
	}

	/**
	 * 技の詳細を表示
	 */
	function ShowSkillDetail($skill, $radio = false, $text = false)
	{
		if (!$skill) return false;

		$output = array();

		$output['skill'] = $skill;
		$output['radio'] = $radio;

		$content = HOF_Class_View::render(null, $output, 'layout/skill.detail');

		if ($text)
		{
			return $content;
		}

		$content->output();

		return;

	}

}
