<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Class_Item
{

	/**
	 * アイテムの詳細を返す...ちょっと修正したいな。
	 */
	function ShowItemDetail($item, $amount = false, $text = false, $need = false)
	{
		if (!$item) return false;

		$html = "<img src=\"" . HOF_Class_Icon::getImageUrl($item["img"], HOF_Class_Icon::IMG_ITEM) . "\" class=\"vcent\">";
		// 精錬値
		if ($item["refine"]) $html .= "+{$item[refine]} ";
		if ($item["AddName"]) $html .= "{$item[AddName]} ";
		$html .= "{$item[base_name]}"; // 名前

		if ($item["type"]) $html .= "<span class=\"light\"> ({$item[type]})</span>";
		if ($amount)
		{ //数量
			$html .= " x<span class=\"bold\" style=\"font-size:80%\">{$amount}</span>";
		}
		if ($item["atk"]["0"]) //物理攻撃
 				$html .= ' / <span class="dmg">Atk:' . $item[atk][0] . '</span>';
		if ($item["atk"]["1"]) //魔法攻撃
 				$html .= ' / <span class="spdmg">Matk:' . $item[atk][1] . '</span>';
		if ($item["def"])
		{
			$html .= " / <span class=\"recover\">Def:{$item[def][0]}+{$item[def][1]}</span>";
			$html .= " / <span class=\"support\">Mdef:{$item[def][2]}+{$item[def][3]}</span>";
		}
		if ($item["P_SUMMON"]) $html .= ' / <span class="support">Summon+' . $item["P_SUMMON"] . '%</span>';
		if (isset($item["handle"])) $html .= ' / <span class="charge">h:' . $item[handle] . '</span>';
		if ($item["option"]) $html .= ' / <span style="font-size:80%">' . substr($item["option"], 0, -2) . "</span>";

		if ($need && $item["need"])
		{
			$html .= " /";
			foreach ($item["need"] as $M_itemNo => $M_amount)
			{
				$M_item = HOF_Model_Data::getItemData($M_itemNo);
				$html .= "<img src=\"" . HOF_Class_Icon::getImageUrl($M_item["img"], HOF_Class_Icon::IMG_ITEM) . "\" class=\"vcent\">";
				$html .= "{$M_item[base_name]}"; // 名前
				$html .= " x<span class=\"bold\" style=\"font-size:80%\">{$M_amount}</span>";
				if ($need["$M_itemNo"]) $html .= "<span class=\"light\">(" . $need["$M_itemNo"] . ")</span>";
			}
		}

		if ($text) return $html;

		print ($html);
	}

}
