<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (DATA_BASE_CHAR);
include_once (CLASS_CHAR);

class HOF_Class_Char extends char
{

	var $file_ext = '.yml';

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

	function __construct($file = false)
	{
		if (!$file) return 0;

		list($this->file_name, $this->file_ext) = HOF_Class_File::basename($file);
		$this->Number = $this->file_name;

		$this->file = $file;
		$this->fp = HOF_Class_File::FileLock($file);

		if ($this->file_ext == '.dat')
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
			HOF_Class_File::WriteFileFP($this->fp, $this->DataSavingFormat());
			$this->fpclose();
		}
		else
		{
			HOF_Class_File::WriteFile($file, $this->DataSavingFormat());
		}
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
			"Pattern",
			"PatternMemo",
			//モンスター専用
			//"monster","land","family","monster_message"//保存する必要無くなった
			);
		//$Save	= get_object_vars($this);

		$data = array();

		foreach ($Save as $k)
		{
			if (!isset($this->{$k})) continue;

			if ($this->file_ext == '.dat')
			{
				$data[$k] = "$k=" . (is_array($this->{$k}) ? implode("<>", $this->{$k}) : $this->{$k});
			}
			else
			{
				$data[$k] = $this->{$k};
			}
		}

		if ($this->file_ext == '.dat')
		{
			$text = implode("\n", $data);
		}
		else
		{
			$text = HOF_Class_Yaml::dump($data);
		}

		return $text;
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
		HOF_Class_File::fileClose($this->fp);

		unset($this->fp);
	}

	/**
	 * パターン文字列を配列にする。
	 * ****<>****<>****|****<>****<>****|****<>****<>****
	 */
	function PatternExplode()
	{
		//dump($this->judge);
		if ($this->judge) return false;

		if ($this->file_ext == '.dat' && !is_array($this->Pattern))
		{
			$Pattern = explode("|", $this->Pattern);
			$this->judge = explode("<>", $Pattern["0"]);
			$this->quantity = explode("<>", $Pattern["1"]);
			$this->action = explode("<>", $Pattern["2"]);
		}
		else
		{
			$this->judge = $this->Pattern['judge'];
			$this->quantity = $this->Pattern['quantity'];
			$this->action = $this->Pattern['action'];
		}

		return true;
	}

	/**
	 * パターン配列を保存する。
	 */
	function PatternSave($judge, $quantity, $action)
	{
		if ($this->file_ext == '.dat')
		{
			$this->Pattern = implode("<>", $judge) . "|" . implode("<>", $quantity) . "|" . implode("<>", $action);
		}
		else
		{
			$this->Pattern = array(
				'judge' => $judge,
				'quantity' => $quantity,
				'action' => $action,
				);
		}

		return true;
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

}
