<?php
/**
 * 處理與道具相關的視圖。
 *
 * 此文件包含用於顯示用戶道具列表的功能。
 */

// 包含必要的類和數據文件
include_once(CLASS_JS_ITEMLIST);

/**
 * 處理道具相關的操作。
 *
 * @param object $self 調用此函數的對象。
 * @return void
 */
function ItemProcess(&$self) {
	// 目前為空，可根據需要擴展
}

/**
 * 顯示道具列表。
 *
 * @param object $self 調用此函數的對象。
 * @return void
 */
function ItemShow(&$self) {
?>
	<div style="margin:15px">
	<h4>道具</h4>
	<div style="margin:0 20px">
<?php 
	if($self->item) {
		$goods	= new JS_ItemList();
		$goods->SetID("my");
		$goods->SetName("type");
		// JSを使用しない。
		// 不使用 JS。
		if($self->no_JS_itemlist)
			$goods->NoJS();
		//$goods->ListTable("<table>");
		//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
		foreach($self->item as $no => $val) {
			$item	= LoadItemData($no);
			$string	= ShowItemDetail($item,$val,1)."<br />";
			//$string	= "<tr><td>".$no."</td><td>".ShowItemDetail($item,$val,1)."</td></tr>";
			$goods->AddItem($item,$string);
		}
		print($goods->GetJavaScript("list"));
		print($goods->ShowSelect());
		print('<div id="list">'.$goods->ShowDefault().'</div>');
	} else {
		print("No items.");
	}
	print("</div></div>");
}
