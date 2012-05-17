<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

require_once (CLASS_UNION);

class HOF_Class_Char_Mon_Union extends union
{

	/**
	 * コンストラクタ
	 */
	function __construct($file = false)
	{
		$this->_extend_init();

		$this->LoadData($file);
	}

	function _extend_init()
	{
		$this->extend('HOF_Class_Char_Pattern');
		$this->extend('HOF_Class_Char_View');
		$this->extend('HOF_Class_Char_Battle');
	}

	function LoadData($file)
	{
		if (!file_exists($file)) return false;

		list($this->file_name, $this->file_ext) = HOF_Class_File::basename($file);

		$this->file = $file;
		$this->fp = HOF_Class_File::fplock_file($this->file);

		$this->UnionNo = substr(basename($file), 0, 4);

		if ($this->file_ext == '.dat')
		{
			$data = HOF_Class_File::ParseFileFP($this->fp);
		}
		else
		{
			$data = HOF_Class_Yaml::parse(stream_get_contents($this->fp));
		}

		$this->SetCharData($data);

		return true;
	}

	/**
	 * キャラデータの保存
	 */
	function SaveCharData()
	{
		if (!file_exists($this->file)) return false;

		$Save = array(
			"MonsterNumber",
			"LastDefeated",
			"HP",
			"SP",
			);

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

		HOF_Class_File::fpwrite_file($this->fp, $text);
		fclose($this->fp);
		unset($this->fp);
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
		//print("<span class=\"{$sub}\">HP : ????/{$this->MAXHP}</span><br />\n");//HP
		$output .= "<span class=\"{$sub}\">HP : ????/????</span><br />\n"; //HP
		$sub = $this->STATE === 1 ? "dmg" : "support";
		$output .= "<span class=\"{$sub}\">SP : ????/????</span>\n";
		$output .= "</div>\n"; //SP

		return $output;
	}

}
