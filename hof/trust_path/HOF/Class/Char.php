<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (DATA_BASE_CHAR);
include_once (CLASS_CHAR);

class HOF_Class_Char extends char
{

	var $file_ext = BASE_EXT;

	var $map_equip_allow = array(
		"weapon" => true,
		"shield" => true,
		"armor" => true,
		"item" => true,
		);

	static $map_equip = array(
		"weapon" => true,
		"shield" => true,
		"armor" => true,
		"item" => true,
		);

	protected $_extends_ = array();
	protected $_extends_method_ = array();

	protected $_extends_method_invalids_ = array();

	public function extend($extend)
	{
		$this->_extends_[$class] = null;

		if (is_object($extend))
		{
			$class = get_class($extend);
			$this->_extends_[$class]['obj'] = &$extend;
		}
		else
		{
			$class = $extend;
			$this->_extends_[$class]['obj'] = null;
		}

		$this->_extends_[$class]['class'] = $class;

		$methods = HOF_Helper_Object::get_public_methods($class, $this->_extends_method_invalids_);

		foreach ($methods as $v)
		{
			$this->_extends_method_[$v] = $class;
		}

		return $this;
	}

	public function __call($func, $argv)
	{
		if (isset($this->_extends_method_[$func]) && !empty($this->_extends_method_[$func]))
		{
			$class = $this->_extends_method_[$func];

			if (!is_object($this->_extends_[$class]['obj']))
			{
				$this->_extends_[$class]['obj'] = new $class(&$this);
			}

			return call_user_func_array(array($this->_extends_[$class]['obj'], $func), $argv);
		}
		else
		{
			throw new BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $func . '()');
		}
	}

	function __construct($file = false)
	{
		$this->extend('HOF_Class_Char_Pattern');

		$this->jobdata = new HOF_Class_Char_Job(&$this);

		if (!$file) return 0;

		list($this->file_name, $this->file_ext) = HOF_Class_File::basename($file);
		$this->Number = $this->file_name;

		$this->file = $file;
		$this->fp = HOF_Class_File::fplock_file($file);

		if (0 && $this->file_ext == '.dat')
		{
			$data = HOF_Class_File::ParseFileFP($this->fp);
		}
		else
		{
			$data = HOF_Class_Yaml::parse(stream_get_contents($this->fp));
		}

		$this->SetCharData($data);
	}

	function __destruct()
	{
		@HOF_Class_Char_Pattern::getInstance($this)->__destruct();

		$this->fpclose();
	}

	/**
	 * キャラデータの保存
	 */
	function SaveCharData($id = false)
	{
		// モンスターは保存しない。
		//if($this->monster)	return false;

		if ($id)
		{
			$dir = USER . $id;
		}
		else
		{
			if (!$this->user) return false;
			$dir = USER . $this->user;
		}

		// ユーザーが存在しない場合保存しない
		if (!is_dir($dir)) return false;

		if (isset($this->file_name))
		{
			$file = $dir . "/" . $this->file_name . $this->file_ext;
		}
		elseif (isset($this->file))
		{
			list($this->file_name, $this->file_ext) = HOF_Class_File::basename($this->file);

			$file = $dir . "/" . $this->file_name . $this->file_ext;
		}
		else
		{
			$file = $dir . "/" . $this->birth . $this->file_ext;
		}

		if (file_exists($file) && $this->fp)
		{
			//sleep(10);//ファイルロック確認用
			HOF_Class_File::fpwrite_file($this->fp, $this->DataSavingFormat());
			$this->fpclose();
		}
		else
		{
			HOF_Class_File::WriteFile($file, $this->DataSavingFormat());
		}
	}

	/**
	 * 誰のキャラか設定する
	 */
	function SetUser($user)
	{
		$this->user = $user;
	}

	function DataSavingFormat()
	{
		$Save = array(
			"name",
			"gender",
			"job",
			"birth",
			"level",
			"exp",
			"statuspoint",
			"skillpoint",
			//"maxhp","hp","maxsp","sp",// (2007/9/30 保存しなくなった)
			"str",
			"int",
			"dex",
			"spd",
			"luk",
			"weapon",
			"shield",
			"armor",
			"item",
			"position",
			"guard",
			"skill",
			//"judge","action",
			"pattern",
			"pattern_memo",
			//モンスター専用
			//"monster","land","family","monster_message"//保存する必要無くなった

			'user',

			);
		//$Save	= get_object_vars($this);

		$data = array();

		foreach ($Save as $k)
		{
			if (!isset($this->{$k})) continue;

			if (0 && $this->file_ext == '.dat')
			{
				$data[$k] = "$k=" . (is_array($this->{$k}) ? implode("<>", $this->{$k}) : $this->{$k});
			}
			else
			{
				$data[$k] = $this->{$k};
			}
		}

		if (0 && $this->file_ext == '.dat')
		{
			$text = implode("\n", $data);
		}
		else
		{
			$text = HOF_Class_Yaml::dump($data);
		}

		return $text;
	}

	function SetJobData()
	{
		$this->jobdata->_jobdata();
	}

	/**
	 * HPとSPを計算して設定する
	 */
	function SetHpSp()
	{
		$this->jobdata->hpsp();
	}

	/**
	 * 戦闘中のキャラ名,HP,SP を色を分けて表示する
	 * それ以外にも必要な物があれば表示するようにした。
	 */
	function ShowHpSp()
	{
		$output = '';

		if ($this->STATE === 1) $sub = " dmg";
		else
			if ($this->STATE === 2) $sub = " spdmg";
		//名前
		$output .= "<span class=\"bold{$sub}\">{$this->name}</span>\n";
		// チャージor詠唱
		if ($this->expect_type === 0) $output .= '<span class="charge">(charging)</span>' . "\n";
		else
			if ($this->expect_type === 1) $output .= '<span class="charge">(casting)</span>' . "\n";
		// HP,SP
		$output .= "<div class=\"hpsp\">\n";
		$sub = $this->STATE === 1 ? "dmg" : "recover";
		$output .= "<span class=\"{$sub}\">HP : {$this->HP}/{$this->MAXHP}</span><br />\n"; //HP
		$sub = $this->STATE === 1 ? "dmg" : "support";
		$output .= "<span class=\"{$sub}\">SP : {$this->SP}/{$this->MAXSP}</span>\n";
		$output .= "</div>\n"; //SP

		return $output;
	}

	function setTeamObj(&$team)
	{
		$this->team_obj = &$team;
	}

	function &getTeamObj()
	{
		return $this->team_obj;
	}

	/**
	 * ファイルポインタが開かれていれば閉じる
	 */
	function fpclose()
	{
		HOF_Class_File::fpclose($this->fp);

		unset($this->fp);
	}

	function &user()
	{
		$user = HOF_Class_User::getInstance($this->user);

		return $user;
	}

	function unequip($spot)
	{

		if ($spot == 'all')
		{
			foreach (array_keys(self::$map_equip) as $k)
			{
				if ($item = $this->unequip($k))
				{
					$list[] = $item;
				}
			}

			return (array)$list;
		}

		if (!in_array($spot, self::$map_equip))
		{
			return false;
		}

		if ($item = $this->{$spot})
		{
			$this->{$spot} = NULL;
		}

		return $item;
	}

	/**
	 * アイテムを装備する(職が装備可能な物かどうかは調べない)
	 */
	function Equip($item)
	{
		/**
		 * はずした装備
		 */
		$return = array();

		$fail = false;

		/**
		 * 現在の装備を仮に保存しておく。
		 */
		$old = array();

		foreach (array_keys(self::$map_equip) as $k)
		{
			$v = $this->map_equip_allow[$k];

			if (!$v && $this->{$k})
			{
				$return[] = $this->unequip($k);
			}
			elseif ($v && $this->{$k})
			{
				$old[$k] = $this->{$k};
			}
		}

		/**
		 * 種類別
		 */
		switch ($item["type"])
		{

			case "Sword": //片手武器
			case "Dagger":
			case "Pike":
			case "Hatchet":
			case "Wand":
			case "Mace":
			case "TwoHandSword": //両手武器
			case "Spear":
			case "Axe":
			case "Staff":
			case "Bow":
			case "CrossBow":
			case "Whip":

				$equip_type = 'weapon';


				break;
			case "Shield": //盾
			case "MainGauche":
			case "Book":
				$equip_type = 'shield';


				break;
			case "Armor": //鎧
			case "Cloth":
			case "Robe":
				$equip_type = 'armor';
				break;
			case "Item":
				$equip_type = 'item';
				break;
			default:
				$fail = true;
				break;
		}

		if (!$fail && $equip_type && $this->map_equip_allow[$equip_type])
		{
			$return[] = $this->unequip($equip_type);

			switch ($equip_type)
			{
				case 'weapon':

					if ($item["dh"] && $this->shield)
					{
						/**
						 * 両手持ちの武器の場合。
						 * 盾を装備していたらはずす。
						 */
						$return[] = $this->unequip('shield');
					}

					break;

				case 'shield':

					if ($this->weapon)
					{
						//両手武器ならそれははずす
						$weapon = HOF_Model_Data::newItem($this->weapon);

						if ($weapon["dh"])
						{
							$return[] = $this->unequip('weapon');
						}
					}

					break;
			}

			$this->{$equip_type} = $item["id"];
		}
		else
		{
			$fail = true;
		}

		if (!$fail)
		{
			$handle = 0;

			foreach (array_keys(self::$map_equip) as $k)
			{
				$_item = HOF_Model_Data::newItem($this->{$k});

				$handle += $_item->handle();
			}

			if ($this->GetHandle() < $handle)
			{
				$fail = true;

				// handle over
				foreach ($old as $key => $val)
				{
					// 元に戻す。
					$this->{$key} = $val;
				}

				//return false;
			}
		}

		$return = array_filter($return);

		return array($fail, $return);
	}

	/**
	 * 召喚力?召喚した時の召喚モンスターの強さ
	 */
	function SummonPower()
	{
		$DEX_PART = sqrt($this->DEX) * 5; // DEX分の強化分
		$Strength = 1 + ($DEX_PART + $this->LUK) / 250;
		if ($this->SPECIAL["Summon"]) $Strength *= (100 + $this->SPECIAL["Summon"]) / 100;
		return $Strength;
	}

	/**
	 * 必要経験値
	 */
	function CalcExpNeed()
	{
		switch ($this->level)
		{
			case 40:
				$no = 30000;
				break;
			case 41:
				$no = 40000;
				break;
			case 42:
				$no = 50000;
				break;
			case 43:
				$no = 60000;
				break;
			case 44:
				$no = 70000;
				break;
			case 45:
				$no = 80000;
				break;
			case 46:
				$no = 100000;
				break;
			case 47:
				$no = 250000;
				break;
			case 48:
				$no = 500000;
				break;
			case 49:
				$no = 999990;
				break;
			case 50:
			case (50 <= $this->level):
				$no = "MAX";
				break;
			case (21 < $this->level):
				$no = 2 * pow($this->level, 3) + 100 * $this->level + 100;
				$no -= substr($no, -2);
				$no /= 5;
				break;
			default:
				$no = pow($this->level - 1, 2) / 2 * 100 + 100;
				$no /= 5;
				break;
		}

		return $no;
	}

	/**
	 * 新ワザを追加する。
	 */
	function GetNewSkill($no)
	{
		$this->skill[] = $no;
		asort($this->skill);
	}

	/**
	 * IMGタグで画像を表示するのみ
	 */
	function ShowImage($class = false, $dir = HOF_Class_Icon::IMG_CHAR)
	{
		$url = $this->GetImageURL($dir);

		$add = '';
		if ($class) $add .= ' class="' . $class . '"';

		$add .= ' title="' . HOF_Class_Icon::getImage($this->img, $dir, true) . '"';

		$html = '<img src="' . $url . '" '.$add.'>';

		echo $html;
	}

	/**
	 * IMGタグで画像を表示するのみ
	 */
	function GetImageURL($dir = HOF_Class_Icon::IMG_CHAR)
	{
		$ret = HOF_Class_Icon::getImageUrl($this->img, $dir);

		return $ret;
	}

}
