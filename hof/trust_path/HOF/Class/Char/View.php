<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Char_View
{

	/**
	 * キャラを表組みで表示する
	 */
	function ShowCharacters($characters, $type = null, $checked = null)
	{
		if (!$characters) return false;

		$divide = (count($characters) < CHAR_ROW ? count($characters) : CHAR_ROW);
		$width = floor(100 / $divide); //各セル横幅

		if ($type == "CHECKBOX")
		{
			/**
			 * 選擇出擊的隊員時
			 *
			 * @url index.php?common=gb0
			 * @url index.php?union=0004
			 **/
			print <<< HTML
<script type="text/javascript">
<!--
function toggleCheckBox(id) {
	\$(':checkbox#box'+id+'').prop('checked', function (index, oldPropertyValue){
		return !oldPropertyValue;
	});
	\$("#text"+id).toggleClass('unselect');
}
// -->
</script>
HTML;
		}

		print ('<table cellspacing="0" style="width:100%"><tbody><tr>'); //横幅100%
		foreach ($characters as $char)
		{
			if ($i % CHAR_ROW == 0 && $i != 0) print ("\t</tr><tr>\n");
			print ("\t<td valign=\"bottom\" style=\"width:{$width}%\">"); //キャラ数に応じて%で各セル分割

			/*-------------------*/
			switch (1)
			{
				case ($type === MONSTER):
					$char->ShowCharWithLand($checked);
					break;
				case ($type === CHECKBOX):
					if (!is_array($checked)) $checked = array();
					if (in_array($char->birth, $checked)) $char->ShowCharRadio($char->birth, " checked");
					else  $char->ShowCharRadio($char->birth);
					break;
				default:
					$char->ShowCharLink();
			}

			print ("</td>\n");
			$i++;
		}
		print ("</tr></tbody></table>");
	}

}


?>