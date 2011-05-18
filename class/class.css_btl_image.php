<?php
/*
	�������륷���Ȥǲ���?�ΰ�?ȿž��ǽ���ä��Τ�פ��Ф����Τ�
	������Ѥ�����Ʈ���̤��롣
	�������֥饦���ˤ�äƤϾ�꤯ɽ������ʤ��Ȼפ���

	GD�Ȱ�ä�ȿž�Ѥߤβ������Ѱդ���ɬ��̵����
	IE��ɽ���Ǥ��롣
*/
class cssimage {

	var $background;
	var $team1_front	= array();
	var $team1_back		= array();
	var $team2_front	= array();
	var $team2_back		= array();

	var $team1_mc;
	var $team2_mc;

	var $img_x, $img_y;//���᡼����
	var $size;

	var $div; //</div>�θĿ�

	var $NoFlip	= false;

//////////////////////////////////////////////////
//	CSS�� image.flip() ��Ȥ����Ȥ�ʤ�����
	function NoFlip() {
		$this->NoFlip	= true;
	}
//////////////////////////////////////////////////
//	�طʲ����򥻥åȡ�
//	�Ĥ��Ǥ��礭����������롣
	function SetBackGround($bg) {
		$this->background	= IMG_OTHER."bg_".$bg.".gif";

		list($this->img_x, $this->img_y)	= getimagesize($this->background);
		$this->size	= "width:{$this->img_x};height:{$this->img_y};";
	}
//////////////////////////////////////////////////
//	������ξ���򥻥å�
//	���Ҹ�Ҥ�ʬ����
	function SetTeams($team1,$team2) {
		foreach($team1 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == "front")
				$this->team1_front[]	= $char;
			else
				$this->team1_back[]	= $char;
		}
		foreach($team2 as $char) {
			// ��������餬��˴���Ƥ���������Ф�
			if($char->STATE === DEAD && $char->summon == true)
				continue;
			if($char->POSITION == "front")
				$this->team2_front[]	= $char;
			else
				$this->team2_back[]	= $char;
		}
	}
//////////////////////////////////////////////////
//	�����ؤο�
	function SetMagicCircle($team1_mc, $team2_mc) {
		$this->team1_mc	= $team2_mc;
		$this->team2_mc	= $team1_mc;
	}
//////////////////////////////////////////////////
//	CSS( �������� ,x��ɸ ,y��ɸ )
	function det($url,$x,$y) {
		return "background-image:url({$url});background-repeat:no-repeat;background-position:{$x}px {$y}px;";
	}

//////////////////////////////////////////////////
//	��Ʈ���̤�ɽ��
	function Show() {

		//print("<div style=\"postion:relative;height:{$this->img_x}px;\">\n");
		//$this->div++;
		// �طʤ�ɽ�� ( ���ɽ���ΰ٤˺��ˤ��餹 )
		$margin	= (-1) * round($this->img_x / 2);
		print("<div style=\"position:relative;left:50%;margin-left:{$margin}px;{$this->size}".$this->det($this->background,0,0)."\">\n");
		$this->div++;

		// �����ؤ�ɽ������
		if(0 < $this->team1_mc) {
			print("<div style=\"{$this->size}".$this->det(IMG_OTHER."mc0_".$this->team1_mc.".gif",280,0)."\">\n");
			$this->div++;
		}
		if(0 < $this->team2_mc) {
			print("<div style=\"{$this->size}".$this->det(IMG_OTHER."mc1_".$this->team2_mc.".gif",0,0)."\">\n");
			$this->div++;
		}

		$cell_width		= ($this->img_x)/6;//������6ʬ�䤷��Ĺ��
		$y	= $this->img_y/2;//�⤵���濴

		// team1 ��ɽ��(��������)
		$this->CopyRow($this->team1_back, 0, $cell_width*1, $cell_width, $y, $this->img_y);
		$this->CopyRow($this->team1_front, 0, $cell_width*2, $cell_width, $y, $this->img_y);

		if(!$this->NoFlip) {
			// ȿž�Ѥ�CSS
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

//////////////////////////////////////////////////
//	��Υ����������Ф�
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