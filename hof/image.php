<?php

/**
 * 画像合成を非常にナンセンスな方法で行う。
 * GDライブラリ→画像の水平反転が不可能。
 * PECL ImageMagic→可能。しかしPEARの知識が無く断念。

 * 従って画像合成する場合,反転済みの画像を別で用意する。

 * sampleURL
 * http://localhost/proj/hof/image.php?f11=mon_018&f12=mon_018&f13=mon_018&f14=mon_018&b11=mon_018&b12=mon_018&f21=mon_018&f22=mon_018&b21=mon_018&b22=mon_018&b23=mon_018&f23=mon_018&f24=mon_018&info=0
 * 最後の[&info=0] は無くてもok

 * ※※※ 魔法陣の表示に未対応！！！！！！！！！
 */

include ('trust_path/bootstrap.php');

//$type = 'gif';
$type = 'png';

$img = new image();
// ( gif, png, jpeg )
// gif -> 動作確認済み
// png -> 画像の色数を保てるかどうか未確認
// jpeg -> 動作未確認
$img->SetBackGround($type); // 背景画像の形式

$img->SetCharFile($type); // *
$img->ShowInfo();
$img->CopyChar();
$img->Filter();

// 出力画像の形式
// gif > png > jpeg の順でサイズが異なる
$img->OutPutImage($type);

//////////////////////////////////////////////////////////////////////
class image
{

	var $image;

	var $background;
	var $team1_front = array();
	var $team1_back = array();
	var $team2_front = array();
	var $team2_back = array();

	var $char_img_type; //キャラ画像形式

	var $img_x, $img_y; //イメージ幅

	function SetCharFile($type)
	{
		$this->char_img_type = $type;

		$_char_no_image = preg_replace('/\.(png|jpg|gif|bmp)$/i', '', CHAR_NO_IMAGE);

		/*
		f11 = team1_front[0]
		f12 = team1_front[1]
		f13 = team1_front[2]
		f14 = team1_front[3]
		f15 = team1_front[4]
		b11 = team1_back[0]...
		f21 = team2_front[0]...
		b21 = team2_back[0]...
		*/
		for ($j = 1; $j < 6; $j++)
		{ // 1,2,3,4,5　チーム１
			if ($img = $_GET['f1' . $j])
			{
				if (strpos($img, '/') !== false) continue; // '/'が指定された場合無視
				if (!$file = $this->_get_file(IMG_CHAR . $img, $type))
				{
					$file = $this->_get_file(IMG_CHAR . $_char_no_image, $type);
				}

				if ($file) $this->team1_front[] = $file;
			}
			if ($img = $_GET['b1' . $j])
			{
				if (strpos($img, '/') !== false) continue; // '/'が指定された場合無視
				if (!$file = $this->_get_file(IMG_CHAR . $img, $type))
				{
					$file = $this->_get_file(IMG_CHAR . $_char_no_image, $type);
				}

				if ($file) $this->team1_back[] = $file;
			}
		}
		for ($j = 1; $j < 6; $j++)
		{ // 1,2,3,4,5　チーム２
			if ($img = $_GET['f2' . $j])
			{
				if (strpos($img, '/') !== false) continue; // '/'が指定された場合無視
				if (!$file = $this->_get_file(IMG_CHAR_REV . $img, $type))
				{
					$file = $this->_get_file(IMG_CHAR_REV . $_char_no_image, $type);
				}

				if ($file) $this->team2_front[] = $file;
			}
			if ($img = $_GET['b2' . $j])
			{
				if (strpos($img, '/') !== false) continue; // '/'が指定された場合無視
				if (!$file = $this->_get_file(IMG_CHAR_REV . $img, $type))
				{
					$file = $this->_get_file(IMG_CHAR_REV . $_char_no_image, $type);
				}

				if ($file) $this->team2_back[] = $file;
			}
		}
	}

	function CopyChar()
	{
		$cell_width = ($this->img_x) / 6;
		$y = $this->img_y / 2;

		$this->CopyRow($this->team1_back, 0, $cell_width * 1, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team1_front, 0, $cell_width * 2, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team2_front, 1, $cell_width * 4, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team2_back, 1, $cell_width * 5, $cell_width, $y, $this->img_y);
	}

	function CopyRow($teams, $direction, $axis_x, $cell_width, $axis_y, $cell_height)
	{
		$number = count($teams);
		if ($number == 0) return false;

		$axis_x += ($direction ? -$cell_width / 2 : + $cell_width / 2);
		$axis_y += ($direction ? -$cell_height / 2 : -$cell_height / 2);

		$gap_x = $cell_width / ($number + 1) * ($direction ? 1 : -1);
		$gap_y = $cell_height / ($number + 1) * ($direction ? 1 : 1);

		foreach ($teams as $file)
		{
			$gap++;
			$x = $axis_x + ($gap_x * $gap);
			$y = $axis_y + ($gap_y * $gap);
			$this->CopyImage($file, $x, $y);
		}
	}

	function CopyImage($file, $x, $y)
	{
		$imginfo = $this->getimagesize($file);

		$copy = $imginfo['imagecreatefromfunc']($file);

		imagealphablending($copy, true);
		imagesavealpha($copy, true);

		list($width, $height) = $imginfo;
		$x -= $width / 2; // キャラ幅分だけずらす
		$y -= $height / 2;
		imagecopy($this->image, $copy, round($x), round($y), 0, 0, $width, $height);

		imagealphablending($this->image, true);
		imagesavealpha($this->image, true);
	}

	function SetBackGround($type)
	{
		if ($_GET['bg'] && ($file = $this->_get_file(IMG_OTHER . 'bg_' . $_GET['bg'], $type))) $this->background = $file;
		else  $this->background = $this->_get_file(IMG_OTHER . 'bg_grass', $type);

		$imginfo = $this->getimagesize($this->background);
		$this->image = $imginfo['imagecreatefromfunc']($this->background);

		list($this->img_x, $this->img_y) = $imginfo;

		imagealphablending($this->image, true);
		imagesavealpha($this->image, true);

		$color_photo = imagecreatetruecolor($this->img_x, $this->img_y);
		imagecopy($color_photo, $this->image, 0, 0, 0, 0, $this->img_x, $this->img_y);

		$this->image = $color_photo;
	}

	function Filter()
	{ //途中
		//imagegammacorrect($this->image,200,255);
		if ($_GET["gray"])
		{ //グレイスケール処理
			$val = $_GET["gray"];
			if ($val < 0) $val = 0;
			else
				if (100 < $val) $val = 100;
			imagecopymergegray($this->image, $this->image, 0, 0, 0, 0, $this->img_x, $this->img_y, $val);
		}
		//$image_p = imagecreatetruecolor(240, 100);//縮小
		//imagecopyresampled($image_p, $this->image, 0, 0, 0, 0, 240, 100, $this->img_x, $this->img_y);
		//$this->image	= $image_p;
	}

	function OutPutImage($type)
	{
		// 修正可直接顯示合成後圖片而不會顯示亂碼
		@header("Content-Type: image/{$type}");

		$func = 'image' . $type;
		$func($this->image);
		imagedestroy($this->image);
	}

	function ShowInfo()
	{
		if (!$_GET["info"]) return true;

		$image = imagecreate(360, 240);
		$bg = imagecolorallocate($image, 24, 24, 128);
		$textcolor = imagecolorallocate($image, 255, 24, 255);

		$this->imagestring_echo($image, "info-", array(
			'font-size' => 2,
			'line-height' => 14,

			'x' => 6,
			'y' => 6,

			'color' => $textcolor,
			));
		$this->imagestring_echo($image, "BG : " . $this->background);

		foreach (array(
			'team1_front' => 'TEAM1_F',
			'team1_back' => 'TEAM1_B',
			'team2_front' => 'TEAM2_F',
			'team2_back' => 'TEAM2_B') as $team_var => $team_pos)
		{
			foreach ($this->{$team_var} as $val)
			{
				$this->imagestring_echo($image, "$team_pos : " . $val);
			}
		}

		header('Content-type: image/gif');
		imagepng($image);
		exit();
	}

	function imagestring_echo($image, $conf, $base = null)
	{
		static $_base = array(
			'font-size' => 2,
			'line-height' => 14,

			'x' => 0,
			'y' => 0,
			);
		if ($base)
		{
			foreach ($base as $k => $v)
			{
				$_base[$k] = $v;
			}
		}

		if (is_array($conf))
		{
			foreach ($conf as $k => $v)
			{
				$_base[$k] = $v;
			}
		}
		else
		{
			$_base['string'] = $conf;
		}

		imagestring($image, $_base['font-size'], $_base['x'], $_base['y'], $_base['string'], $_base['color']);

		$_base['y'] += $_base['line-height'];
		$_base['string'] = '';
	}

	function _get_file($file, $type)
	{
		foreach (array(
			$type,
			'png',
			'gif',
			'jpg') as $ext)
		{
			if (file_exists($file . '.' . $ext))
			{
				return $file . '.' . $ext;
			}
		}

		return false;
	}

	function getimagesize($file)
	{
		$imginfo = @getimagesize($file);

		$imginfo['file'] = $file;
		$imginfo['width'] = $imginfo[0];
		$imginfo['height'] = $imginfo[1];

		$imginfo['type'] = $imginfo[2];

		$imginfo['size'] = @filesize($file);

		switch ($imginfo['mime'])
		{
			case 'image/jpeg':
				$imginfo['imagecreatefromfunc'] = 'imagecreatefromjpeg';
				$imginfo['imagefunc'] = 'imagejpeg';
				break;
			case 'image/gif':
				$imginfo['imagecreatefromfunc'] = 'imagecreatefromgif';
				$imginfo['imagefunc'] = 'imagegif';
				break;
			case 'image/png':
				$imginfo['imagecreatefromfunc'] = 'imagecreatefrompng';
				$imginfo['imagefunc'] = 'imagepng';
				break;
		}

		return (array )$imginfo;
	}
}


?>