<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_View
{

	protected $char;

	function __construct($char)
	{
		$this->char = $char;
	}

	//	キャラステータスの一番上のやつ。
	function ShowCharDetail()
	{
		$P_MAXHP = round($this->char->maxhp * $this->char->M_MAXHP / 100) + $this->char->P_MAXHP;
		$P_MAXSP = round($this->char->maxsp * $this->char->M_MAXSP / 100) + $this->char->P_MAXSP;


?>
<table>
<tr><td valign="top" style="width:180px"><?php

		$this->char->ShowCharLink();


?>
</td><td valign="top" style="padding-right:20px">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td style="text-align:right">Exp :&nbsp;</td><td><?=

		$this->char->exp


?>/<?=

		$this->char->CalcExpNeed()


?></td></tr>
<tr><td style="text-align:right">HP :&nbsp;</td><td><?=

		$this->char->maxhp


?><?php

		if ($P_MAXHP) print (" + {$P_MAXHP}");


?></td></tr>
<tr><td style="text-align:right">SP :&nbsp;</td><td><?=

		$this->char->maxsp


?><?php

		if ($P_MAXSP) print (" + {$P_MAXSP}");


?></td></tr>
<tr><td style="text-align:right">STR :&nbsp;</td><td><?=

		$this->char->str


?><?php

		if ($this->char->P_STR) print (" + {$this->char->P_STR}");


?></td></tr>
<tr><td style="text-align:right">INT :&nbsp;</td><td><?=

		$this->char->int


?><?php

		if ($this->char->P_INT) print (" + {$this->char->P_INT}");


?></td></tr>
<tr><td style="text-align:right">DEX :&nbsp;</td><td><?=

		$this->char->dex


?><?php

		if ($this->char->P_DEX) print (" + {$this->char->P_DEX}");


?></td></tr>
<tr><td style="text-align:right">SPD :&nbsp;</td><td><?=

		$this->char->spd


?><?php

		if ($this->char->P_SPD) print (" + {$this->char->P_SPD}");


?></td></tr>
<tr><td style="text-align:right">LUK :&nbsp;</td><td><?=

		$this->char->luk


?><?php

		if ($this->char->P_LUK) print (" + {$this->char->P_LUK}");


?></td></tr>
</table>
</td><td valign="top">
<?php

		if ($this->char->SPECIAL["PoisonResist"]) print ("毒抵抗 +" . $this->char->SPECIAL["PoisonResist"] . "%<br />\n");
		if ($this->char->SPECIAL["Pierce"]["0"]) print ("物理防御無視ダメージ +" . $this->char->SPECIAL["Pierce"]["0"] . "<br />\n");
		if ($this->char->SPECIAL["Pierce"]["1"]) print ("魔法防御無視ダメージ +" . $this->char->SPECIAL["Pierce"]["1"] . "<br />\n");
		if ($this->char->SPECIAL["Summon"]) print ("召喚力 +" . $this->char->SPECIAL["Summon"] . "%<br />\n");


?>
</td></tr></table>
<?php

	}

	function ShowCharWithLand($land)
	{

		if (is_array($land)) $land = reset($land);

?>
	<div class="carpet_frame">
	<div class="land" style="background-image : url(<?=

		HOF_Class_Icon::getImageUrl("land_" . $land, HOF_Class_Icon::IMG_LAND)


?>);">
	<?php

		$this->char->ShowImage()


?>
	</div>
	<?=

		$this->char->name


?><br>Lv.<?=

		$this->char->level


?>
	</div><?php

	}

	function ShowChar()
	{
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0) //carpetの並びを交互にする
 				$flag++;


?>
<div class="carpet_frame">
<div class="carpet<?=

		$flag % 2


?>"><?php

		$this->char->ShowImage();


?></div>
<?=

		$this->char->name


?><br>Lv.<?=

		$this->char->level


?>&nbsp;<?=

		$this->char->job_name


?>
</div><?php

	}


	function ShowCharLink()
	{ //$array=色々
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0) //carpetの並びを交互にする
 				$flag++;


?>
<div class="carpet_frame">
<div class="carpet<?=

		$flag % 2


?>">
<a href="<?php e(HOF::url('char', 'char', array('char' => $this->char->id))) ?>"><?php

		$this->char->ShowImage();


?></a></div>
<?=

		$this->char->name


?><?php

		if ($this->char->statuspoint) print ('<span class="bold charge">*</span>');


?><br>Lv.<?=

		$this->char->level


?>&nbsp;<?=

		$this->char->job_name


?>
</div><?php

	}


	/**
	 * checkboxも表示する
	 */
	function ShowCharRadio($checked = null, $input_type = 'checkbox')
	{
		static $flag = 0;

		$flag++;
		if (CHAR_ROW % 2 == 0 && $flag % (CHAR_ROW + 1) == 0)
		{
			/**
			 * carpetの並びを交互にする
			 */
			$flag++;
		}

		$output = new HOF_Class_Array();

		$output->char = $this->char;
		$output->flag = $flag;

		//$output->birth = $birth;
		$output->checked = $checked;

		$output->input_type = ($input_type) ? $input_type : 'checkbox';

		HOF_Class_View::render(null, $output, 'layout/char/input.radio')->output();
	}

	/**
	 * キャラを表組みで表示する
	 */
	static function ShowCharacters($characters, $type = null, $checked = null)
	{
		if (!$characters) return false;

		$divide = (count($characters) < CHAR_ROW ? count($characters) : CHAR_ROW);
		$width = floor(100 / $divide); //各セル横幅

		if ($type == INPUT_CHECKBOX || $type == INPUT_RADIO)
		{
			/**
			 * 選擇出擊的隊員時
			 *
			 * @url index.php?common=gb0
			 * @url index.php?union=0004
			 **/
			print <<< HTML
<script type="text/javascript">
(function(\$){

	\$(function(){
		\$('.carpet_frame')
			.on('change', ':checkbox, :radio', function(){
				var _this = \$(this);

				if (_this.prop('checked'))
				{
					if (_this.is(':radio'))
					{
						var _form = _this.parents('form');

						if (!_form.size())
						{
							_form = _this.parents('#contents');
						}

						_form.find('[name="' + _this.attr('name') + '"]:radio').filter(':not([value="' + _this.val() + '"])').trigger('change');
					}

					_this.parents('.carpet_frame:first').find('div[id^="text"]').removeClass('unselect');
				}
				else
				{
					_this.parents('.carpet_frame:first').find('div[id^="text"]').addClass('unselect');
				}

			})
			.find('div[id^="text"]')
				.on('click', function(event){

					var _this = \$(this);

					if (!\$(event.target).is(':input'))
					{

						_this.parent('.carpet_frame:first')
							.find(':checkbox, :radio')
							.prop('checked', function(idx, old){
								return !old;
							})
							.trigger('change')
						;

					}

				})
			.end()
			.find(':checkbox, :radio')
				.trigger('change')
		;
	});

})(jQuery);

</script>
HTML;
		}

		if (!is_array($checked)) $checked = array($checked);

		print '<div style="text-align: center;">';
		foreach ($characters as $char)
		{
			switch (1)
			{
				case ($type === "MONSTER"):
					$char->ShowCharWithLand($checked);
					break;
				case ($type === INPUT_CHECKBOX):
				case ($type === INPUT_RADIO):
					$char->ShowCharRadio(in_array($char->id, $checked), $type);

					break;
				default:
					$char->ShowCharLink();
			}
			$i++;
		}
		print '<div class="clearfix"></div></div>';
	}

	/**
	 * IMGタグで画像を表示するのみ
	 */
	function ShowImage($class = false, $dir = HOF_Class_Icon::IMG_CHAR)
	{
		$url = $this->char->getImageUrl($dir);

		$add = '';
		if ($class) $add .= ' class="' . $class . '"';

		$add .= ' title="' . HOF_Class_Icon::getImage($this->char->icon(), $dir, true) . '"';

		$html = '<img src="' . $url . '" ' . $add . '>';

		echo $html;
	}

	/**
	 * IMGタグで画像を表示するのみ
	 */
	function getImageUrl($dir = HOF_Class_Icon::IMG_CHAR)
	{
		$ret = HOF_Class_Icon::getImageUrl($this->char->icon(), $dir);

		return $ret;
	}

	//	名前を返す
	function Name($string = false)
	{
		$name = $this->char->NAME ? $this->char->NAME : $this->char->name;

		if ($string)
		{
			if ($this->char->isUnion())
			{
				$string .= ' g_union';
			}

			return "<span class=\"{$string}\">{$name}</span>";
		}
		else
		{
			return $name;
		}
	}

	function icon()
	{
		return (isset($this->char->icon)) ? $this->char->icon : $this->char->img;
	}

}


?>