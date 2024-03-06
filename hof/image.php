<?php
include("setting.php");

$img	= new image();
$img->SetBackGround("gif");

$img->SetCharFile("gif");
$img->ShowInfo();
$img->CopyChar();
$img->Filter();

$img->OutPutImage("gif");

class image{

	var $image;

	var $background;
	var $team1_front	= array();
	var $team1_back		= array();
	var $team2_front	= array();
	var $team2_back		= array();

	var $char_img_type;

	var $img_x, $img_y;

	function SetCharFile($type) {
		$this->char_img_type	= $type;
		for($j=1; $j<6; $j++) {
			if( $img = $_GET["f1".$j] ) {
				if( strpos($img,"/") !== false ) continue;
				$file	= IMG_CHAR.$img.".".$type;
				if(file_exists($file))
					$this->team1_front[]	= $file;
			}
			if( $img = $_GET["b1".$j]) {
				if( strpos($img,"/") !== false ) continue;
				$file	= IMG_CHAR.$img.".".$type;
				if(file_exists($file))
					$this->team1_back[]	= $file;
			}
		}
		for($j=1; $j<6; $j++) {
			if( $img = $_GET["f2".$j] ) {
				if( strpos($img,"/") !== false ) continue;
				$file	= IMG_CHAR_REV.$img.".".$type;
				if(file_exists($file))
					$this->team2_front[]	= $file;
			}
			if( $img = $_GET["b2".$j]) {
				if( strpos($img,"/") !== false ) continue;
				$file	= IMG_CHAR_REV.$img.".".$type;
				if(file_exists($file))
					$this->team2_back[]	= $file;
			}
		}
	}

	function CopyChar() {
		$cell_width		= ($this->img_x)/6;
		$y	= $this->img_y/2;

		$this->CopyRow($this->team1_back, 0, $cell_width*1, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team1_front, 0, $cell_width*2, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team2_front, 1, $cell_width*4, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team2_back, 1, $cell_width*5, $cell_width, $y, $this->img_y);
	}

	function CopyRow($teams,$direction,$axis_x,$cell_width,$axis_y,$cell_height) {
		$number	= count($teams);
		if($number == 0) return false;

		$axis_x	+= ( $direction ? -$cell_width/2 : +$cell_width/2 );
		$axis_y	+= ( $direction ? -$cell_height/2 : -$cell_height/2 );

		$gap_x	= $cell_width/($number+1) * ($direction? 1 : -1 );
		$gap_y	= $cell_height/($number+1) * ($direction? 1 : 1 );

		foreach($teams as $file) {
			$gap++;
			$x	= $axis_x + ( $gap_x * $gap );
			$y	= $axis_y + ( $gap_y * $gap );
			$this->CopyImage($file,$x,$y);
		}
	}

	function CopyImage($file,$x,$y) {
		$imgcreatefrom	= "imagecreatefrom{$this->char_img_type}";

		$copy	= $imgcreatefrom($file);
		list($width, $height)	= getimagesize($file);
		$x	-= $width/2;
		$y	-= $height/2;
		imagecopy($this->image,$copy,round($x),round($y),0,0,$width,$height);
	}

	function SetBackGround($type) {
		if($_GET["bg"])
			$file	= IMG_OTHER."bg_".$_GET["bg"].".".$type;
		if(file_exists($file))
			$this->background	= $file;
		else
			$this->background	= IMG_OTHER."bg_grass.".$type;

		$func	= "imagecreatefrom".$type;
		$this->image	= $func($this->background);

		list($this->img_x, $this->img_y)	= getimagesize($this->background);
	}

	function Filter() {
		if($_GET["gray"]) {
			$val	= $_GET["gray"];
			if($val < 0) $val	= 0;
			else if(100 < $val) $val	= 100;
			imagecopymergegray($this->image,$this->image,0,0,0,0,$this->img_x,$this->img_y,$val);
		}
	}

	function OutPutImage($type) {
		$func	= "image".$type;
		$func($this->image);
		header("Content-Type: image/{$type}");
		imagedestroy($this->image);
	}

	function ShowInfo() {
		if(!$_GET["info"]) return true;

		$image	= imagecreate(360, 240);
		$bg = imagecolorallocate($image, 24, 24, 128);
		$textcolor = imagecolorallocate($image, 255, 24, 255);
		$size	= 2;
		$height	= 14;
		$mar_l	= 6;
		$mar_t	= 6;
		imagestring($image, $size, $mar_l, $mar_t, "info-", $textcolor);

		imagestring($image, $size, $mar_l, $mar_t + $height, "BG : ".$this->background, $textcolor);

		$row	= 2;
		$teams	= array(
		"team1_front"	=> "TEAM1_F",
		"team1_back"	=> "TEAM1_B",
		"team2_front"	=> "TEAM2_F",
		"team2_back"	=> "TEAM2_B");
		foreach($teams as $team_var => $team_pos) {
			foreach($this->{$team_var} as $val) {
				imagestring($image, $size, $mar_l, $mar_t + $height * $row, "$team_pos : ".$val, $textcolor);
				$row++;
			}
		}

		header("Content-type: image/gif");
		imagepng($image);
		exit();
	}
}

?>