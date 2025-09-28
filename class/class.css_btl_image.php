<?php

/**
* スタイルシートで画像/領域/反転可能だったのを思い出したので
* それを用いて戦闘画面を作る。
* ただしブラウザによっては上手く表示されないと思う。
*
* GDと違って反転済みの画像を用意する必要無し。
* IEは表示できる。
*/
class cssimage {

	var $background;
	var $team1_front	= array();
	var $team1_back		= array();
	var $team2_front	= array();
	var $team2_back		= array();

	var $team1_mc;
	var $team2_mc;

	var $img_x, $img_y;//イメージ幅
	var $size;

	var $div; //</div>の個数

	var $NoFlip	= false;

/**
* CSSで image.flip() を使うか使わないか。
* Determines whether to use CSS for flipping images or not.
*/
	function NoFlip() {
		$this->NoFlip	= true;
	}

/**
* 背景画像をセット。
* ついでに大きさも取得する。
* Sets the background image and retrieves its size.
*
* @param string $bg Background image name
*/
	function SetBackGround($bg) {
		$this->background	= IMG_OTHER."bg_".$bg.".gif";

		list($this->img_x, $this->img_y)	= getimagesize($this->background);
		$this->size	= "width:{$this->img_x}px;height:{$this->img_y}px;";
	}

/**
* チームの情報をセット
* 前衛後衛に分ける
* Sets the team information and separates them into front and back lines.
*
* @param array $team1 Team 1 characters
* @param array $team2 Team 2 characters
*/
	function SetTeams($team1,$team2) {
		foreach($team1 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == "front")
				$this->team1_front[]	= $char;
			else
				$this->team1_back[]	= $char;
		}
		foreach($team2 as $char) {
			// 召喚キャラが死亡している場合は飛ばす
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == "front")
				$this->team2_front[]	= $char;
			else
				$this->team2_back[]	= $char;
		}
	}

/**
* 魔方陣の数
* Sets the number of magic circles for each team.
*
* @param int $team1_mc Team 1 magic circle count
* @param int $team2_mc Team 2 magic circle count
*/
	function SetMagicCircle($team1_mc, $team2_mc) {
		$this->team1_mc	= $team2_mc;
		$this->team2_mc	= $team1_mc;
	}

/**
* CSS( キャラ画像 ,x座標 ,y座標 )
* Generates CSS for an image at a given position.
*
* @param string $url Image URL
* @param int $x X coordinate
* @param int $y Y coordinate
* @return string CSS style string
*/
	function det($url,$x,$y) {
		return "background-image:url({$url});background-repeat:no-repeat;background-position:{$x}px {$y}px;";
	}

/**
* 戦闘画面を表示
* Displays the battle screen.
*/
	function Show() {

		//print("<div style=\"postion:relative;height:{$this->img_x}px;\">\n");
		//$this->div++;
		// 背景を表示 ( 中央表示の為に左にずらす )
		$margin	= (-1) * round($this->img_x / 2);
		print("<div style=\"position:relative;left:50%;margin-left:{$margin}px;{$this->size}".$this->det($this->background,0,0)."\">\n");
		$this->div++;

		// 魔方陣を表示する
		if(0 < $this->team1_mc) {
			print("<div style=\"{$this->size}".$this->det(IMG_OTHER."mc0_".$this->team1_mc.".gif",280,0)."\">\n");
			$this->div++;
		}
		if(0 < $this->team2_mc) {
			print("<div style=\"{$this->size}".$this->det(IMG_OTHER."mc1_".$this->team2_mc.".gif",0,0)."\">\n");
			$this->div++;
		}

		$cell_width		= ($this->img_x)/6;//横幅を6分割した長さ
		$y	= $this->img_y/2;//高さの中心

		// team1 を表示(後列→前列)
		$this->CopyRow($this->team1_back, 0, $cell_width*1, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team1_front, 0, $cell_width*2, $cell_width, $y, $this->img_y);

		if(!$this->NoFlip) {
			// 反転用のCSS
			print("<div style=\"{$this->size}filter:FlipH();\">\n");
			$this->div++;
			$dir	= 0;
			$backs	= 1;
			$fore	= 2;
		} else {
			$dir	= 1;
			$backs	= 5;
			$fore	= 4;
		}

		$this->CopyRow($this->team2_back, $dir, $cell_width*$backs, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team2_front, $dir, $cell_width*$fore, $cell_width, $y, $this->img_y);

		for($i=0; $i<$this->div; $i++)
			print("</div>");
	}

/**
* 列のキャラを描き出す
* Draws characters in a row.
*
* @param array $teams Characters to draw
* @param int $direction Direction of drawing (0 for normal, 1 for flipped)
* @param int $axis_x X axis position
* @param int $cell_width Width of each cell
* @param int $axis_y Y axis position
* @param int $cell_height Height of each cell
*/
	function CopyRow($teams,$direction,$axis_x,$cell_width,$axis_y,$cell_height) {
		$number	= count($teams);
		if($number == 0) return false;

		$axis_x	+= ( $direction ? -$cell_width/2 : +$cell_width/2 );
		$axis_y	+= ( $direction ? -$cell_height/2 : -$cell_height/2 );

		$gap_x	= $cell_width/($number+1) * ($direction? 1 : -1 );
		$gap_y	= $cell_height/($number+1) * ($direction? 1 : 1 );

		$f	= $direction ? IMG_CHAR_REV : IMG_CHAR;

		foreach($teams as $char) {
			$this->div++;
			$gap++;
			$x	= $axis_x + ( $gap_x * $gap );
			$y	= $axis_y + ( $gap_y * $gap );

			$x	= floor($x);
			$y	= floor($y);
			if($char->STATE === DEAD)
				$img	= $f.DEAD_IMG;
			else
				$img	= $char->GetImageUrl($f);
			list($img_x,$img_y)	= getimagesize($img);
			$x	-= round($img_x/2);
			$y	-= round($img_y/2);
			print("<div style=\"{$this->size}".$this->det($img,$x,$y)."\">\n");
		}
	}
}
?>