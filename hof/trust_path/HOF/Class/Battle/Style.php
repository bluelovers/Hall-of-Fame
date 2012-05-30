<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once BTL_IMG_CSS;

/**
 * スタイルシートで画像?領域?反転可能だったのを思い出したので
 * それを用いて戦闘画面を作る。
 * ただしブラウザによっては上手く表示されないと思う。

 * GDと違って反転済みの画像を用意する必要無し。
 * IEは表示できる。
 */
//class HOF_Class_Battle_Style extends cssimage
class HOF_Class_Battle_Style extends HOF_Class_Array
{

	/**
	 * @return self
	 */
	public static function &newInstance($style = 1, $options = array())
	{
		return new self($style, $options);
	}

	function __construct($style = 1, $options = array())
	{
		$this->_data_default_ = array();
		$this->_data_default_['style'] = $style;
		$this->_data_default_['options'] = $options;
		$this->_data_default_['output'] = '';

		parent::__construct($this->_data_default);

		$this->_init();
	}

	protected function _init()
	{
		$this->setFlip(($this->style == 1));

		$this->_options();

		return $this;
	}

	protected function _options()
	{
		$this->_setBg();
		$this->_setTeams();
		$this->_setMagicCircle();

		return $this;
	}

	/**
	 * CSS画像反転無し
	 * CSSで image.flip() を使うか使わないか。
	 */
	function setFlip($enable = true)
	{
		$this->options['flip'] = $enable;

		return $this;
	}

	/**
	 * 背景画像をセット。
	 * ついでに大きさも取得する。
	 */
	function setBg($bg)
	{
		$this->options['bg'] = $bg;

		$this->nowcast && $this->_setBg();

		return $this;
	}

	function _setBg()
	{
		if ($this->options['bg'])
		{
			//$this->data['bg'] = HOF_Class_Icon::IMG_LAND . "bg_" . $this->options['bg'] . ".gif";
			$this->data['bg'] = HOF_Class_Icon::getImageUrl("bg_" . $this->options['bg'], HOF_Class_Icon::IMG_LAND);
			$this->data['bg_file'] = HOF_Class_Icon::getImage("bg_" . $this->options['bg'], HOF_Class_Icon::IMG_LAND);

			if ($this->style == 0)
			{
				unset($this->data['css']);
			}
			else
			{
				$this->data['css']['size_x'] = $this->options['size_x'];
				$this->data['css']['size_y'] = $this->options['size_y'];

				list($this->data['css']['bg_x'], $this->data['css']['bg_y']) = getimagesize($this->data['bg_file']);

				$this->data['css']['size_x'] = $this->data['css']['size_x'] ? $this->data['css']['size_x'] : $this->data['css']['bg_x'];
				$this->data['css']['size_y'] = $this->data['css']['size_y'] ? $this->data['css']['size_y'] : $this->data['css']['bg_y'];

				$this->data['css']['size'] = "width:{$this->data['css']['size_x']}px; height:{$this->data['css']['size_y']}px;";
			}
		}
		else
		{
			unset($this->data['css']);
			unset($this->data['bg']);
			unset($this->data['bg_file']);
		}
	}

	/**
	 * チームの情報をセット
	 * 前衛後衛に分ける
	 */
	function setTeams($team1, $team2)
	{
		$this->options['team'][0]['team'] = $team1;
		$this->options['team'][1]['team'] = $team2;

		$this->nowcast && $this->_setTeams();

		return $this;
	}

	function _setTeams()
	{
		if (empty($this->options['team']))
		{
			unset($this->data['team']);

			return $this;
		}

		$this->data['team'][0]['team'] = $this->options['team'][0]['team'];
		$this->data['team'][1]['team'] = $this->options['team'][1]['team'];

		foreach ($this->data['team'] as $_idx => &$team)
		{
			unset($team['front']);
			unset($team['back']);

			//debug($team);

			foreach ($team['team'] as &$char)
			{
				// 召喚キャラが死亡している場合は飛ばす
				if ($char->STATE === STATE_DEAD && $char->isSummon())
				{
					continue;
				}

				if ($char->POSITION == POSITION_FRONT)
				{
					$team['front'][] = $char;
				}
				else
				{
					$team['back'][] = $char;
				}
			}
		}

		return $this;
	}

	/**
	 * 魔方陣の数
	 */
	function setMagicCircle($team1_mc, $team2_mc)
	{
		/*
		$this->teams[TEAM_1]['mc'] = $team2_mc;
		$this->team2_mc = $team1_mc;
		*/

		$this->options['team'][0]['mc'] = $team1_mc;
		$this->options['team'][1]['mc'] = $team2_mc;

		$this->nowcast && $this->_setMagicCircle();

		return $this;
	}

	function _setMagicCircle()
	{
		$this->data['team'][0]['mc'] = $this->options['team'][0]['mc'];
		$this->data['team'][1]['mc'] = $this->options['team'][1]['mc'];

		return $this;
	}

	/**
	 * CSS( キャラ画像 ,x座標 ,y座標 )
	 */
	function det($url, $x, $y)
	{
		return "background-image:url({$url});background-repeat:no-repeat;background-position:{$x}px {$y}px;";
	}

	/**
	 * 戦闘画面を表示
	 */
	function exec()
	{
		$this->_options();

		if ($this->style)
		{
			$this->exec_css();
		}
		else
		{
			$this->exec_img();
		}

		//debug($this);
		//exit();

		return $this;
	}

	/**
	 * 戦闘画像(画像のみ)
	 */
	function exec_img()
	{
		$this->output = '';

		// HP=0 のキャラの画像(拡張子があればそれを取る)
		$DeadImg = substr(DEAD_IMG, 0, strpos(DEAD_IMG, "."));

		//$url .= 'bg='.$this->data['bg'].'&';
		$params['bg'] = $this->options['bg'];

		foreach((array)$this->data['team'] as $_idx => $team)
		{
			//$k = 2 - $_idx;
			$k = 1 + $_idx;

			foreach(array(
				'f' => 'front',
				'b' => 'back',
			) as $u => $p)
			{
				// 前衛の数・後衛の数を初期化
				${$u} = 1;

				if (empty($team[$p]))
				{
					continue;
				}

				foreach ((array)$team[$p] as $char)
				{
					// 画像はキャラに設定されている画像の拡張子までの名前
					if ($char->STATE === STATE_DEAD)
					{
						$img = $DeadImg;
					}
					else
					{
						/*
						$img = substr($char->img, 0, strpos($char->img, "."));
						*/
						$img = $char->img;
					}

					//$url .= "{$u}{$k}{${$u}}={$img}&";
					$params[$u.$k.${$u}] = $img;

					${$u}++;
				}
			}
		}

		$url = HOF::url('image', 'battle_'.gmdate('YmdHis', time()).'.png', $params);

		// ←これが表示されるのみ
		$this->output = '<img src="' . $url . '">';

		$this->output = '<div style="text-align:center">'.$this->output.'</div>';

		return $this;
	}

	/**
	 * CSS戦闘画面
	 */
	function exec_css()
	{
		$this->output = '';

		//print("<div style=\"postion:relative;height:{$this->data['bg_x']}px;\">\n");
		//$this->div++;
		// 背景を表示 ( 中央表示の為に左にずらす )
		$margin = (-1) * round($this->data['css']['size_x'] / 2);
		$this->output .= "<div style=\"/*position:relative;left:50%;margin-left:{$margin}px;*/margin: auto;{$this->data['css']['size']};overflow: hidden;" . $this->det($this->data['bg'], 0, 0) . "\">\n";
		$this->div++;

		/*
		// 魔方陣を表示する
		if (0 < $this->teams[TEAM_1]['mc'])
		{
			$this->output .= "<div style=\"{$this->data['css']['size']}" . $this->det(HOF_Class_Icon::IMG_LAND . "mc0_" . $this->teams[TEAM_1]['mc'] . ".gif", 280, 0) . "\">\n";
			$this->div++;
		}
		if (0 < $this->team2_mc)
		{
			$this->output .= "<div style=\"{$this->data['css']['size']}" . $this->det(HOF_Class_Icon::IMG_LAND . "mc1_" . $this->team2_mc . ".gif", 0, 0) . "\">\n";
			$this->div++;
		}
		*/

		foreach ((array)$this->data['team'] as $_idx => $team)
		{
			// 魔方陣を表示する
			if (0 < $team['mc'])
			{
				$this->output .= "<div style=\"{$this->data['css']['size']}" . $this->det(HOF_Class_Icon::getImageUrl("mc{$_idx}_" . $team['mc'], HOF_Class_Icon::IMG_OTHER), 280, 0) . "\">\n";
				$this->div++;
			}
		}

		$cell_width = ($this->data['css']['size_x']) / 6; //横幅を6分割した長さ
		$y = $this->data['css']['size_y'] / 2; //高さの中心

		// team1 を表示(後列→前列)
		$this->CopyRow($this->data['team'][0]['back'], 0, $cell_width * 1, $cell_width, $y, $this->data['css']['size_y']);
		$this->CopyRow($this->data['team'][0]['front'], 0, $cell_width * 2, $cell_width, $y, $this->data['css']['size_y']);

		if ($this->options['flip'])
		{
			// 反転用のCSS
			$this->output .= "<div style=\"{$this->data['css']['size']} ".$this->_css_filp()."\">\n";
			$this->div++;
			$dir = 0;
			$backs = 1;
			$fore = 2;
		}
		else
		{
			$dir = 1;
			$backs = 5;
			$fore = 4;
		}

		$this->CopyRow($this->data['team'][1]['back'], $dir, $cell_width * $backs, $cell_width, $y, $this->data['css']['size_y']);
		$this->CopyRow($this->data['team'][1]['front'], $dir, $cell_width * $fore, $cell_width, $y, $this->data['css']['size_y']);

		for ($i = 0; $i < $this->div; $i++) $this->output .= "</div>";

		return $this;
	}

	function _css_filp()
	{
		$css = ';
		    -moz-transform: scaleX(-1);
		    -o-transform: scaleX(-1);
		    -webkit-transform: scaleX(-1);
		    transform: scaleX(-1);
		    filter: FlipH;
		    -ms-filter: \'FlipH\';
  		';

  		return $css;
	}

	/**
	 * 列のキャラを描き出す
	 */
	function CopyRow($teams, $direction, $axis_x, $cell_width, $axis_y, $cell_height)
	{
		$number = count($teams);
		if ($number == 0) return false;

		$axis_x += ($direction ? -$cell_width / 2 : + $cell_width / 2);
		$axis_y += ($direction ? -$cell_height / 2 : -$cell_height / 2);

		$gap_x = $cell_width / ($number + 1) * ($direction ? 1 : -1);
		$gap_y = $cell_height / ($number + 1) * ($direction ? 1 : 1);

		$f = $direction ? HOF_Class_Icon::IMG_CHAR_REV : HOF_Class_Icon::IMG_CHAR;

		foreach ($teams as $char)
		{
			$this->div++;
			$gap++;
			$x = $axis_x + ($gap_x * $gap);
			$y = $axis_y + ($gap_y * $gap);

			$x = floor($x);
			$y = floor($y);

			if ($char->STATE === STATE_DEAD) $img = $f . DEAD_IMG;
			else  $img = $char->getImageUrl($f);

			$img_file = HOF_Class_Icon::getImage($char->img, $f);

			list($img_x, $img_y) = getimagesize($img_file);
			$x -= round($img_x / 2);
			$y -= round($img_y / 2);
			$this->output .= "<div style=\"{$this->data['css']['size']}" . $this->det($img, $x, $y) . "\">\n";
		}
	}

	function output()
	{
		return (string)$this->output;
	}

	function __toString()
	{
		return $this->output();
	}

}
