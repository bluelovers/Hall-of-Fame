<?php

/**
 * 加入拍賣會員
 * @param main $main 主物件
 * @return bool
 */
function AuctionJoinMember($main) {
		if(!$_POST["JoinMember"])
				return false;
		if($main->item["9000"]) {//既に會員
				return false;
		}
		// お金が足りない
		if(!$main->TakeMoney(round(START_MONEY * 1.10))) {
				ShowError("您沒有足夠的錢<br />\n");
				return false;
		}
		// 道具を足す
		$main->AddItem(9000);
		$main->SaveUserItem();
		$main->SaveData();
		ShowResult("拍賣會的成員。<br />\n");
		return true;
}

/**
 * 檢查是否為拍賣會員
 * @param main $main 主物件
 * @return bool
 */
function AuctionEnter($main) {
		if(isset($main->item["9000"]))//オ一クションメンバ一カ一ド
				return true;
		else
				return false;
}

/**
 * 顯示拍賣頁面的頁首
 * @param main $main 主物件
 */
function AuctionHeader($main) {
		?>
		<div style="margin:15px 0 0 15px">
				<h4>拍賣(Auction)</h4>
				<div style="margin-left:20px">
						<div style="width:500px">
								<div style="float:left;width:50px;">
										<img src="<?php print IMG_CHAR?>ori_003.gif" />
								</div>
								<div style="float:right;width:450px;">
										<?php
										AuctionJoinMember($main);
										if(AuctionEnter($main)) {
												print("您有會員卡麼。<br />\n");
												print("歡迎您到拍賣場。<br />\n");
												print("<a href=\"#log\">回顧記錄</a>\n");
										} else {
												print("想在拍賣會拍賣那您要加入會員啊。<br />\n");
												print("入會費用可要 ".MoneyFormat(round(START_MONEY * 1.10))." 呢。<br />\n");
												print("入會麼?<br />\n");
												print('<form action="" method="post">'."\n");
												print('<input type="submit" value="入會" name="JoinMember" class="btn"/>'."\n");
												print("</form>\n");
										}
										if(!AUCTION_TOGGLE)
												ShowError("功能暫停");
										if(!AUCTION_EXHIBIT_TOGGLE)
												ShowError("暫停拍賣");
										?>
								</div>
								<div style="clear:both"></div>
						</div>
				</div>
				<h4>道具拍賣(Item Auction)</h4>
				<div style="margin-left:20px">
		<?php
}

/**
 * 顯示拍賣頁面的頁尾
 * @param Auction $ItemAuction 拍賣物件
 */
function AuctionFoot($main, $ItemAuction) {
		?>
				</div>
				<a name="log"></a>
				<h4>拍賣紀錄(AuctionLog)</h4>
				<div style="margin-left:20px">
						<?php $ItemAuction->ShowLog();?>
				</div>
		<?php
}

/**
 * 處理競標請求
 * @param main $main 主物件
 * @param Auction $ItemAuction 拍賣物件
 * @return bool
 */
function AuctionItemBiddingProcess($main, $ItemAuction) {
		if(!AuctionEnter($main))
				return false;
		if(!isset($_POST["ArticleNo"]))
				return false;

		$ArticleNo    = $_POST["ArticleNo"];
		$BidPrice    = (int)$_POST["BidPrice"];
		if($BidPrice < 1) {
				ShowError("輸入的是個錯誤的價格。");
				return false;
		}
		// まだ出品中かどうか確認する。
		if(!$ItemAuction->ItemArticleExists($ArticleNo)) {
				ShowError("這個拍賣品的賣方無法確認。");
				return false;
		}
		// 自分が競標できる人かどうかの確認
		if(!$ItemAuction->ItemBidRight($ArticleNo,$main->id)) {
				ShowError("No.".$ArticleNo." 賣方是否已經招標");
				return false;
		}
		// 最低競標價格を割っていないか確認する。
		$Bottom    = $ItemAuction->ItemBottomPrice($ArticleNo);
		if($BidPrice < $Bottom) {
				ShowError("低於最低投標價");
				ShowError("目前出價:".MoneyFormat($BidPrice)." 最低出價:".MoneyFormat($Bottom));
				return false;
		}
		// 金持ってるか確認する
		if(!$main->TakeMoney($BidPrice)) {
				ShowError("您的資金不足。");
				return false;
		}

		// 實際に競標する。
		if($ItemAuction->ItemBid($ArticleNo,$BidPrice,$main->id,$main->name)) {
				ShowResult("No:{$ArticleNo}  ".MoneyFormat($BidPrice)." 被收購。<br />\n");
				return true;
		}
		return false;
}

/**
 * 顯示競標表單
 * @param main $main 主物件
 * @param Auction $ItemAuction 拍賣物件
 */
function AuctionItemBiddingForm($main, $ItemAuction) {
		if(!AUCTION_TOGGLE)
				return;

		// 出品用フォ一ムにいくボタン
		if(AuctionEnter($main)) {
				if(AUCTION_EXHIBIT_TOGGLE) {
						print("<form action=\"?menu=auction\" method=\"post\">\n");
						print('<input type="submit" value="拍賣物品" name="ExhibitItemForm" class="btn" style="width:160px">'."\n");
						print("</form>\n");
				}
				// 入會してた場合　競標できるように
				$ItemAuction->ItemSortBy($_GET["sort"]);
				$ItemAuction->ItemShowArticle2(true);

				if(AUCTION_EXHIBIT_TOGGLE) {
						print("<form action=\"?menu=auction\" method=\"post\">\n");
						print('<input type="submit" value="拍賣物品" name="ExhibitItemForm" class="btn" style="width:160px">'."\n");
						print("</form>\n");
				}
		} else {
				// 競標できない
				$ItemAuction->ItemShowArticle2(false);
		}
}

/**
 * 處理道具出品請求
 * @param main $main 主物件
 * @param Auction $ItemAuction 拍賣物件
 * @return bool|string
 */
function AuctionItemExhibitProcess($main, $ItemAuction) {
		if(!AUCTION_EXHIBIT_TOGGLE)
				return "BIDFORM";// 出品凍結

		// 保存しないで出品リストを表示する
		if(!AuctionEnter($main))
				return "BIDFORM";
		if(!$_POST["PutAuction"])
				return "BIDFORM";

		if(!$_POST["item_no"]) {
				ShowError("Select Item.");
				return false;
		}
		// セッションによる30秒間の出品拒否
		$SessionLeft    = 30 - (time() - $_SESSION["AuctionExhibit"]);
		if(isset($_SESSION["AuctionExhibit"]) && 0 < $SessionLeft) {
				ShowError("Wait {$SessionLeft}seconds to ReExhibit.");
				return false;
		}
		// 同時出品數の制限
		if(AUCTION_MAX <= $ItemAuction->ItemAmount()) {
				ShowError("拍賣數量已達到極限。(".$ItemAuction->ItemAmount()."/".AUCTION_MAX.")");
				return false;
		}
		// 出品費用
		if(!$main->TakeMoney(500)) {
				ShowError("Need ".MoneyFormat(500)." to exhibit auction.");
				return false;
		}
		// 道具が讀み迂めない場合
		if(!$item    = LoadItemData($_POST["item_no"])) {
				ShowError("Failed to load item data.");
				return false;
		}
		// 道具を所持していない場合
		if(!$main->item[$_POST["item_no"]]) {
				ShowError("Item \"{$item[name]}\" doesn't exists.");
				return false;
		}
		// その道具が出品できない場合
		$possible    = CanExhibitType();
		if(!isset($possible[$item["type"]])) {
				ShowError("Cant put \"{$item[name]}\" to the Auction");
				return false;
		}
		// 出品時間の確認
		if(    !($_POST["ExhibitTime"] === '1' ||
						$_POST["ExhibitTime"] === '3' ||
						$_POST["ExhibitTime"] === '6' ||
						$_POST["ExhibitTime"] === '12' ||
						$_POST["ExhibitTime"] === '18' ||
						$_POST["ExhibitTime"] === '24') ) {
				var_dump($_POST);
				ShowError("time?");
				return false;
		}
		// 數量の確認
		if(preg_match("/^[0-9]/",$_POST["Amount"])) {
				$amount    = (int)$_POST["Amount"];
				if($amount == 0)
						$amount    = 1;
		} else {
				$amount    = 1;
		}
		// 減らす(所持數より多く指定された場合その數を調節する)
		$_SESSION["AuctionExhibit"]    = time();//セッションで2重出品を防ぐ
		$amount    = $main->DeleteItem($_POST["item_no"],$amount);
		$main->SaveUserItem();

		// 出品する
		$ItemAuction->ItemAddArticle($_POST["item_no"],$amount,$main->id,$_POST["ExhibitTime"],$_POST["StartPrice"],$_POST["Comment"]);
		print($item["name"]."{$amount}個 展覽品。");
		return true;
}

/**
 * 顯示出品表單
 * @param main $main 主物件
 */
function AuctionItemExhibitForm($main) {
		if(!AUCTION_EXHIBIT_TOGGLE)
				return;

		include(CLASS_JS_ITEMLIST);
		$possible    = CanExhibitType();
		?>
		<div class="u bold">如何參展</div>
		<ol>
				<li>選擇一種道具，拍賣。</li>
				<li>如果要拍賣超過兩個以上是要輸入數量。</li>
				<li>指定拍賣的時間。</li>
				<li>指定起拍價(不輸入的話為0)</li>
				<li>輸入您的描述。</li>
				<li>發送。</li>
		</ol>
		<div class="u bold">注意事項</div>
		<ul>
				<li>拍賣要交$500的手續費。</li>
				<li>負責拍賣工作的人似乎不會認真幫你辦事的樣子</li>
		</ul>
		<a href="?menu=auction">查看所有拍賣物</a>
		</div>
		<h4>出售</h4>
		<div style="margin-left:20px">
		<div class="u bold">可以拍賣的道具</div>
		<?php
		if(!$main->item) {
				print("No items<br />\n");
				return;
		}
		$ExhibitList    = new JS_ItemList();
		$ExhibitList->SetID("auc");
		$ExhibitList->SetName("type_auc");
		// JSを使用しない。
		if($main->no_JS_itemlist)
				$ExhibitList->NoJS();
		foreach($main->item as $no => $amount) {
				$item    = LoadItemData($no);
				if(!isset($possible[$item["type"]]))
						continue;
				$head    = '<input type="radio" name="item_no" value="'.$no.'" class="vcent">';
				$head    .= ShowItemDetail($item,$amount,1)."<br />";
				$ExhibitList->AddItem($item,$head);
		}
		print($ExhibitList->GetJavaScript("list"));
		print($ExhibitList->ShowSelect());
		?>
		<form action="?menu=auction" method="post">
				<div id="list"><?php print $ExhibitList->ShowDefault()?></div>
				<table>
						<tr>
								<td style="text-align:right">數量(Amount) :</td>
								<td><input type="text" name="Amount" class="text" style="width:60px" value="1" /><br /></td>
						</tr>
						<tr>
								<td style="text-align:right">時間(Time) :</td>
								<td>
										<select name="ExhibitTime">
												<option value="24" selected>24 hour</option>
												<option value="18">18 hour</option>
												<option value="12">12 hour</option>
												<option value="6">6 hour</option>
												<option value="3">3 hour</option>
												<option value="1">1 hour</option>
										</select>
								</td>
						</tr>
						<tr>
								<td>起拍價(Start Price) :</td>
								<td><input type="text" name="StartPrice" class="text" style="width:240px" maxlength="10"><br /></td>
						</tr>
						<tr>
								<td style="text-align:right">描述(Comment) :</td>
								<td><input type="text" name="Comment" class="text" style="width:240px" maxlength="40"></td>
						</tr>
						<tr>
								<td></td>
								<td>
										<input type="submit" class="btn" value="Put Auction" name="PutAuction" style="width:240px"/>
										<input type="hidden" name="PutAuction" value="1">
								</td>
						</tr>
				</table>
		</form>
		<?php
}

?>