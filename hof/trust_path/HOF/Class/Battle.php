<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

include_once CLASS_BATTLE;

/**
 * $battle	= new HOF_Class_Battle($MyParty,$EnemyParty);
 * $battle->SetTeamName($this->name,$party["name"]);
 * $battle->Process();//戦闘開始
 */
class HOF_Class_Battle extends battle
{

	function outputImage()
	{
		switch (BTL_IMG_TYPE)
		{
			case 0:
				print ('<div style="text-align:center">');
				$this->ShowGdImage(); //画像
				print ('</div>');
				break;
			case 1:
			case 2:
				$this->ShowCssImage(); //画像
				break;
		}
	}

	function ShowGdImage()
	{
		$url = BTL_IMG . "?";

		// HP=0 のキャラの画像(拡張子があればそれを取る)
		$DeadImg = substr(DEAD_IMG, 0, strpos(DEAD_IMG, "."));

		$this->data[0]['team'] = $this->team0;
		$this->data[1]['team'] = $this->team1;

		$url .= 'bg='.$this->BackGround.'&';

		foreach($this->data as $_idx => $_data)
		{
			// 前衛の数・後衛の数を初期化
			$f = 1;
			$b = 1;

			$k = 2 - $_idx;

			foreach ($_data['team'] as $char)
			{
				// 画像はキャラに設定されている画像の拡張子までの名前
				if ($char->STATE === 1)
				{
					$img = $DeadImg;
				}
				else
				{
					$img = substr($char->img, 0, strpos($char->img, "."));
				}

				if ($char->POSITION == "front")
				{
					// 前衛
					$url .= "f{$k}{$f}=$img&";
					$f++;
				}
				else
				{
					// 後衛
					$url .= "b{$k}{$b}=$img&";
					$b++;
				}
			}
		}

		// ←これが表示されるのみ
		$output = '<img src="' . $url . '">';

		echo $output;
	}

	function ShowCssImage()
	{
		$output = HOF_Class_Battle_Style::newInstance(BTL_IMG_TYPE)
			->setBg($this->BackGround)
			->setTeams($this->team1, $this->team0)
			->setMagicCircle($this->team1_mc, $this->team0_mc)
			->exec();

		echo $output;

		echo '<pre>';
		var_dump($output);
		echo '</pre>';

		exit();
	}

}
