<?php

/**
 * 角色顯示類別
 * Character display class
 *
 * @author bluelovers
 * @copyright 2012
 */
class HOF_Class_Char_View
{
	/** 角色物件 / Character object */
	protected $char;

	/**
	 * 建構函式
	 * Constructor
	 *
	 * @param mixed $char - 角色物件 / Character object
	 */
	function __construct($char)
	{
		$this->char = $char;
	}

	/**
	 * 顯示角色詳細資訊（狀態欄位）
	 * Display character detailed information (status section)
	 *
	 * キャラステータスの一番上のやつ。
	 */
	function ShowCharDetail()
	{
		/**
		 * 計算 HP 和 SP 的加成值
		 * Calculate HP and SP bonus values
		 */
		$P_MAXHP = round($this->char->maxhp * $this->char->M_MAXHP / 100) + $this->char->P_MAXHP;
		$P_MAXSP = round($this->char->maxsp * $this->char->M_MAXSP / 100) + $this->char->P_MAXSP;


?>
<table>
<tr><td valign="top" style="width:180px"><?php

		/**
		 * 顯示角色連結
		 * Display character link
		 */
		$this->char->ShowCharLink();


?>
</td><td valign="top" style="padding-right:20px">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td style="text-align:right">Exp :&nbsp;</td><td><?=

		/**
		 * 顯示當前經驗值
		 * Display current experience
		 */
		$this->char->exp


?>/<?=

		/**
		 * 顯示升級所需經驗值
		 * Display experience needed for level up
		 */
		$this->char->CalcExpNeed()


?></td></tr>
<tr><td style="text-align:right">HP :&nbsp;</td><td><?=

		/**
		 * 顯示基礎 HP
		 * Display base HP
		 */
		$this->char->maxhp


?><?php

		/**
		 * 如果有 HP 加成則顯示
		 * Display HP bonus if exists
		 */
		if ($P_MAXHP) print (" + {$P_MAXHP}");


?></td></tr>
<tr><td style="text-align:right">SP :&nbsp;</td><td><?=

		/**
		 * 顯示基礎 SP
		 * Display base SP
		 */
		$this->char->maxsp


?><?php

		/**
		 * 如果有 SP 加成則顯示
		 * Display SP bonus if exists
		 */
		if ($P_MAXSP) print (" + {$P_MAXSP}");


?></td></tr>
<tr><td style="text-align:right">STR :&nbsp;</td><td><?=

		/**
		 * 顯示力量屬性
		 * Display strength attribute
		 */
		$this->char->str


?><?php

		/**
		 * 如果有力量加成則顯示
		 * Display strength bonus if exists
		 */
		if ($this->char->P_STR) print (" + {$this->char->P_STR}");


?></td></tr>
<tr><td style="text-align:right">INT :&nbsp;</td><td><?=

		/**
		 * 顯示智力屬性
		 * Display intelligence attribute
		 */
		$this->char->int


?><?php

		/**
		 * 如果有智力加成則顯示
		 * Display intelligence bonus if exists
		 */
		if ($this->char->P_INT) print (" + {$this->char->P_INT}");


?></td></tr>
<tr><td style="text-align:right">DEX :&nbsp;</td><td><?=

		/**
		 * 顯示敏捷屬性
		 * Display dexterity attribute
		 */
		$this->char->dex


?><?php

		/**
		 * 如果有敏捷加成則顯示
		 * Display dexterity bonus if exists
		 */
		if ($this->char->P_DEX) print (" + {$this->char->P_DEX}");


?></td></tr>
<tr><td style="text-align:right">SPD :&nbsp;</td><td><?=

		/**
		 * 顯示速度屬性
		 * Display speed attribute
		 */
		$this->char->spd


?><?php

		/**
		 * 如果有速度加成則顯示
		 * Display speed bonus if exists
		 */
		if ($this->char->P_SPD) print (" + {$this->char->P_SPD}");


?></td></tr>
<tr><td style="text-align:right">LUK :&nbsp;</td><td><?=

		/**
		 * 顯示幸運屬性
		 * Display luck attribute
		 */
		$this->char->luk


?><?php

		/**
		 * 如果有幸運加成則顯示
		 * Display luck bonus if exists
		 */
		if ($this->char->P_LUK) print (" + {$this->char->P_LUK}");


?></td></tr>
</table>
</td><td valign="top">
<?php

		/**
		 * 顯示特殊能力
		 * Display special abilities
		 */
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
				case ($type === MONSTER):
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