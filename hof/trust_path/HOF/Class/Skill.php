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
			return HOF_Class_Icon::getImageUrl($icon, HOF_Class_Icon::IMG_SKILL);
		}
		else
		{
			return HOF_Class_Icon::getImage($icon, HOF_Class_Icon::IMG_SKILL);
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

		if ($radio) print ('<input type="radio" name="newskill" value="' . $skill["no"] . '" class="vcent" />');

		print ('<img src="' . HOF_Class_Icon::getImageUrl($skill["img"], HOF_Class_Icon::IMG_SKILL) . '" class="vcent">');
		print ("{$skill[name]}");

		if ($radio) print (" / <span class=\"bold\">{$skill[learn]}</span>pt");

		if ($skill[target][0] == "all") //対象
 				print (" / <span class=\"charge\">{$skill[target][0]}</span>");
		else
			if ($skill[target][0] == "enemy") print (" / <span class=\"dmg\">{$skill[target][0]}</span>");
			else
				if ($skill[target][0] == "friend") print (" / <span class=\"recover\">{$skill[target][0]}</span>");
				else
					if ($skill[target][0] == "self") print (" / <span class=\"support\">{$skill[target][0]}</span>");
					else
						if (isset($skill[target][0])) print (" / {$skill[target][0]}");

		if ($skill[target][1] == "all") //単体or複数or全体
 				print (" - <span class=\"charge\">{$skill[target][1]}</span>");
		else
			if ($skill[target][1] == "individual") print (" - <span class=\"recover\">{$skill[target][1]}</span>");
			else
				if ($skill[target][1] == "multi") print (" - <span class=\"spdmg\">{$skill[target][1]}</span>");
				else
					if (isset($skill[target][1])) print (" - {$skill[target][1]}");

		if (isset($skill["sacrifice"])) print (" / <span class=\"dmg\">Sacrifice:{$skill[sacrifice]}%</span>");
		// 消費SP
		if (isset($skill["sp"])) print (" / <span class=\"support\">{$skill[sp]}sp</span>");
		// 消費魔方陣
		if ($skill["MagicCircleDeleteTeam"]) print (" / <span class=\"support\">MagicCircle x" . $skill["MagicCircleDeleteTeam"] . "</span>");
		if ($skill["pow"])
		{
			print (" / <span class=\"" . ($skill["support"] ? "recover" : "dmg") . "\">{$skill[pow]}%</span>x");
			print (($skill["target"][2] ? $skill["target"][2] : "1"));
		}
		if ($skill["type"] == 1) print (" / <span class=\"spdmg\">Magic</span>");
		if ($skill["quick"]) print (" / <span class=\"charge\">Quick</span>");
		if ($skill["invalid"]) print (" / <span class=\"charge\">invalid</span>");
		if ($skill["priority"] == "Back") print (" / <span class=\"support\">BackAttack</span>");
		if ($skill["CurePoison"]) print (" / <span class=\"support\">CurePoison</span>");

		if ($skill["delay"]) print (" / <span class=\"support\">Delay-" . $skill[delay] . "%</span>");
		//		if($skill["support"])
		//			print(" / <span class=\"charge\">support</span>");

		if ($skill["UpMAXHP"]) print (" / <span class=\"charge\">MaxHP+" . $skill[UpMAXHP] . "%</span>");
		if ($skill["UpMAXSP"]) print (" / <span class=\"charge\">MaxSP+" . $skill[UpMAXSP] . "%</span>");
		if ($skill["UpSTR"]) print (" / <span class=\"charge\">Str+" . $skill[UpSTR] . "%</span>");
		if ($skill["UpINT"]) print (" / <span class=\"charge\">Int+" . $skill[UpINT] . "%</span>");
		if ($skill["UpDEX"]) print (" / <span class=\"charge\">Dex+" . $skill[UpDEX] . "%</span>");
		if ($skill["UpSPD"]) print (" / <span class=\"charge\">Spd+" . $skill[UpSPD] . "%</span>");
		if ($skill["UpLUK"]) print (" / <span class=\"charge\">Luk+" . $skill[UpLUK] . "%</span>");
		if ($skill["UpATK"]) print (" / <span class=\"charge\">Atk+" . $skill[UpATK] . "%</span>");
		if ($skill["UpMATK"]) print (" / <span class=\"charge\">Matk+" . $skill[UpMATK] . "%</span>");
		if ($skill["UpDEF"]) print (" / <span class=\"charge\">Def+" . $skill[UpDEF] . "%</span>");
		if ($skill["UpMDEF"]) print (" / <span class=\"charge\">Mdef+" . $skill[UpMDEF] . "%</span>");

		if ($skill["DownMAXHP"]) print (" / <span class=\"dmg\">MaxHP-" . $skill[DownMAXHP] . "%</span>");
		if ($skill["DownMAXSP"]) print (" / <span class=\"dmg\">MaxSP-" . $skill[DownMAXSP] . "%</span>");
		if ($skill["DownSTR"]) print (" / <span class=\"dmg\">Str-" . $skill[DownSTR] . "%</span>");
		if ($skill["DownINT"]) print (" / <span class=\"dmg\">Int-" . $skill[DownINT] . "%</span>");
		if ($skill["DownDEX"]) print (" / <span class=\"dmg\">Dex-" . $skill[DownDEX] . "%</span>");
		if ($skill["DownSPD"]) print (" / <span class=\"dmg\">Spd-" . $skill[DownSPD] . "%</span>");
		if ($skill["DownLUK"]) print (" / <span class=\"dmg\">Luk-" . $skill[DownLUK] . "%</span>");
		if ($skill["DownATK"]) print (" / <span class=\"dmg\">Atk-" . $skill[DownATK] . "%</span>");
		if ($skill["DownMATK"]) print (" / <span class=\"dmg\">Matk-" . $skill[DownMATK] . "%</span>");
		if ($skill["DownDEF"]) print (" / <span class=\"dmg\">Def-" . $skill[DownDEF] . "%</span>");
		if ($skill["DownMDEF"]) print (" / <span class=\"dmg\">Mdef-" . $skill[DownMDEF] . "%</span>");

		if ($skill["PlusSTR"]) print (" / <span class=\"charge\">Str+" . $skill[PlusSTR] . "</span>");
		if ($skill["PlusINT"]) print (" / <span class=\"charge\">Int+" . $skill[PlusINT] . "</span>");
		if ($skill["PlusDEX"]) print (" / <span class=\"charge\">Dex+" . $skill[PlusDEX] . "</span>");
		if ($skill["PlusSPD"]) print (" / <span class=\"charge\">Spd+" . $skill[PlusSPD] . "</span>");
		if ($skill["PlusLUK"]) print (" / <span class=\"charge\">Luk+" . $skill[PlusLUK] . "</span>");

		if ($skill["charge"]["0"] || $skill["charge"]["1"])
		{
			print (" / (" . ($skill["charge"]["0"] ? $skill["charge"]["0"] : "0") . ":");
			print (($skill["charge"]["1"] ? $skill["charge"]["1"] : "0") . ")");
		}

		// 武器制限表示
		if ($skill["limit"])
		{
			$Limit = " / Limit:";
			foreach ($skill["limit"] as $type => $bool)
			{
				$Limit .= $type . ", ";
			}
			print (substr($Limit, 0, -2));
		}
		if ($skill["exp"]) print (" / {$skill[exp]}");
		print ("\n");
	}

}
