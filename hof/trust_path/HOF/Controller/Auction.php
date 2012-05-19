<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Auction extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_Item_Auction
	 */
	var $ItemAuction;

	function _init()
	{
		$this->ItemAuction = new HOF_Class_Item_Auction('item');
		$this->ItemAuction->article_form_query("auction");
		$this->ItemAuction->article_item_check_success(); // 競売が終了した品物を調べる
		$this->ItemAuction->save_user();

		$this->user = &HOF::user();

		$this->ItemAuction->user(&$this->user);
	}

	function _main_before()
	{
		$this->_input();

		if (true === $message = $this->user->CheckLogin())
		{

			if ($this->user->FirstLogin())
			{
				$this->_main_stop(true);

				return 0;
			}

			// アイテムデータ読む
			$this->user->LoadUserItem();
			$this->AuctionJoinMember();

			if (!AUCTION_TOGGLE) HOF_Helper_Global::ShowError("機能停止中");
			if (!AUCTION_EXHIBIT_TOGGLE) HOF_Helper_Global::ShowError("出品停止中");


		}
		else
		{
			$this->_main_stop(true);

			$c = HOF_Class_Controller::getInstance('game', 'login')
				->_main_exec('login', $message ? $message : null)
			;
		}

	}

	function _input()
	{
		$this->input->ExhibitItemForm = $_POST["ExhibitItemForm"];

		$this->input->JoinMember = $_POST["JoinMember"];

		$this->input->last_article_no = $_POST["article_no"];
		$this->input->BidPrice = max(0, intval($_POST["BidPrice"]));

		$this->input->sort = $_GET["sort"];

		$this->input->PutAuction = $_POST["PutAuction"];
		$this->input->item_no = $_POST["item_no"];
		$this->input->ExhibitTime = $_POST["ExhibitTime"];
		$this->input->Amount = max(0, intval($_POST["Amount"]));
		$this->input->StartPrice = max(0, intval($_POST["StartPrice"]));
		$this->input->Comment = $_POST["Comment"];

		$this->input->_timestamp = $_POST['_timestamp'];

		$this->output['form._timestamp'] = time();
	}

	function _main()
	{
		/**
		 * 出品用のフォーム
		 * 表示を要求した場合か、
		 * 出品に失敗した場合表示する。
		 */
		$ResultExhibit = $this->AuctionItemExhibitProcess();
		$ResultBidding = $this->AuctionItemBiddingProcess();
		$this->ItemAuction->fpsave(); // 変更があった場合だけ保存する。

		// 出品リストを表示する
		if ($this->input->ExhibitItemForm)
		{
			$this->user->fpclose_all();
			$this->AuctionItemExhibitForm();

			// 出品か入札に成功した場合はデータを保存する
		}
		else
		{
			if ($ResultExhibit !== false)
			{

				if ($ResultExhibit === true || $ResultBidding === true)
				{
					$this->user->SaveData();
				}

				$this->user->fpclose_all();
				$this->AuctionItemBiddingForm();

				// それ以外
			}
			else
			{
				$this->user->fpclose_all();
				$this->AuctionItemExhibitForm();
			}
		}
	}

	/**
	 * メンバーになる処理
	 */
	function AuctionJoinMember()
	{


		if (!$this->input->JoinMember)
		{
			return false;
		}

		if ($this->user->item["9000"])
		{
			//既に会員
			//HOF_Helper_Global::ShowError("You are already a member.\n");
			return false;
		}

		// お金が足りない
		if (!$this->user->TakeMoney(round(START_MONEY * 1.10)))
		{
			HOF_Helper_Global::ShowError("お金が足りません<br />\n");
			return false;
		}

		// アイテムを足す
		$this->user->AddItem(9000);
		$this->user->SaveUserItem();
		$this->user->SaveData();
		HOF_Helper_Global::ShowResult("オークション会員になりました。<br />\n");

		return true;
	}

	function AuctionEnter()
	{
		// オークションメンバーカード
		if ($this->user->item["9000"])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 入札処理
	 */
	function AuctionItemBiddingProcess()
	{
		if (!$this->AuctionEnter()) return false;

		if (!$this->input->last_article_no) return false;

		if ($this->input->BidPrice < 1)
		{
			HOF_Helper_Global::ShowError("入札価格に誤りがあります。");
			return false;
		}

		// まだ出品中かどうか確認する。
		if (!$this->ItemAuction->exists($this->input->last_article_no))
		{
			HOF_Helper_Global::ShowError("その競売品の出品が確認できません。");
			return false;
		}

		// 自分が入札できる人かどうかの確認
		if (!$this->ItemAuction->article_item_bid_right($this->input->last_article_no, $this->user->id))
		{
			HOF_Helper_Global::ShowError("No." . $this->input->last_article_no . "&nbsp;は入札済みか出品者です。");
			return false;
		}

		// 最低入札価格を割っていないか確認する。
		$Bottom = $this->ItemAuction->article_item_price_bid_min($this->input->last_article_no);
		if ($this->input->BidPrice < $Bottom)
		{
			HOF_Helper_Global::ShowError("最低入札価格を下回っています。");
			HOF_Helper_Global::ShowError("提示入札価格:" . HOF_Helper_Global::MoneyFormat($this->input->BidPrice) . "&nbsp;最低入札価格:" . HOF_Helper_Global::MoneyFormat($Bottom));
			return false;
		}

		// 金持ってるか確認する
		if (!$this->user->TakeMoney($this->input->BidPrice))
		{
			HOF_Helper_Global::ShowError("所持金が足りないようです。");
			return false;
		}

		// 実際に入札する。
		if ($this->ItemAuction->article_item_bid($this->input->last_article_no, $this->input->BidPrice, $this->user->id, $this->user->name))
		{
			HOF_Helper_Global::ShowResult("No:{$this->input->last_article_no}&nbsp;に&nbsp;" . HOF_Helper_Global::MoneyFormat($this->input->BidPrice) . "&nbsp;で入札しました。<br />\n");
			return true;
		}
	}
	/**
	 * 入札用フォーム(画面)
	 */
	function AuctionItemBiddingForm()
	{

		if (!AUCTION_TOGGLE) return false;

		// 出品用フォームにいくボタン
		if ($this->AuctionEnter())
		{
			// 入会してた場合　入札できるように
			$this->ItemAuction->article_item_sortby($this->input->sort);
			$this->ItemAuction->article_item_show(true);

			if (AUCTION_EXHIBIT_TOGGLE)
			{
				$this->_render('auction/form.exhibit');
			}

		}
		else
		{
			// 入札できない
			$this->ItemAuction->article_item_show(false);
		}
	}
	/**
	 * アイテム出品処理
	 */
	function AuctionItemExhibitProcess()
	{
		if (!AUCTION_EXHIBIT_TOGGLE) return "BIDFORM"; // 出品凍結

		// 保存しないで出品リストを表示する

		if (!$this->AuctionEnter())
		{
			return "BIDFORM";
		}

		if (!$this->input->PutAuction) return "BIDFORM";

		if (!$this->input->item_no)
		{
			HOF_Helper_Global::ShowError("Select Item.");
			return false;
		}

		// アイテムが読み込めない場合
		if (!$item = HOF_Model_Data::getItemData($this->input->item_no))
		{
			HOF_Helper_Global::ShowError("Failed to load item data.");
			return false;
		}

		// アイテムを所持していない場合
		if (!$this->user->item[$this->input->item_no])
		{
			HOF_Helper_Global::ShowError("Item < {$item[name]} > doesn't exists.");
			return false;
		}

		if ($message = $this->ItemAuction->article_exhibit_price_check(&$this->input->StartPrice, $item))
		{
			HOF_Helper_Global::ShowError($message);
			return false;
		}

		// セッションによる30秒間の出品拒否
		$SessionLeft = 30 - (time() - $_SESSION["AuctionExhibit"]);
		if ($_SESSION["AuctionExhibit"] && 0 < $SessionLeft)
		{
			HOF_Helper_Global::ShowError("Wait {$SessionLeft}seconds to ReExhibit.");
			return false;
		}
		elseif (
			!$this->input->_timestamp
			|| $this->input->_timestamp >= REQUEST_TIME
			|| ($_SESSION["AuctionExhibit"] && $this->input->_timestamp <= $_SESSION["AuctionExhibit"])
		)
		{
			HOF_Helper_Global::ShowError("Unknow Error!!");
			return false;
		}

		// 同時出品数の制限
		if ($message = $this->ItemAuction->article_count_check_max())
		{
			HOF_Helper_Global::ShowError($message);
			return false;
		}

		// 出品費用
		if ($message = $this->ItemAuction->user_take_exhibit_cost())
		{
			HOF_Helper_Global::ShowError($message);
			return false;
		}

		if ($message = $this->ItemAuction->article_exhibit_item_check($item))
		{
			HOF_Helper_Global::ShowError($message);
			return false;
		}

		// 出品時間の確認
		if ($message = $this->ItemAuction->article_exhibit_time_check($this->input->ExhibitTime))
		{
			var_dump($_POST);
			HOF_Helper_Global::ShowError($message);
			return false;
		}

		// 数量の確認
		$amount = max(1, (int)$this->input->Amount);

		// 減らす(所持数より多く指定された場合その数を調節する)
		$_SESSION["AuctionExhibit"] = REQUEST_TIME; //セッションで2重出品を防ぐ

		$amount = $this->user->DeleteItem($this->input->item_no, $amount);
		$this->user->SaveUserItem();

		// 出品する
		$this->ItemAuction->article_item_add($this->input->item_no, $amount, $this->user->id, $this->input->ExhibitTime, $this->input->StartPrice, $this->input->Comment);
		HOF_Helper_Global::ShowResult($item["name"] . "&nbsp;を&nbsp;{$amount}個&nbsp;出品しました。");

		return true;
	}
	/**
	 * 出品用フォーム
	 */
	function AuctionItemExhibitForm()
	{
		if (!AUCTION_EXHIBIT_TOGGLE) return false;

		if ($this->user->item)
		{
			$possible = HOF_Model_Data::getCanExhibitType();

			$ExhibitList = new HOF_Class_Item_Style_List();
			$ExhibitList->SetID("auc");
			$ExhibitList->SetName("type_auc");

			// JSを使用しない。
			if ($this->user->options['no_JS_itemlist']) $ExhibitList->NoJS();

			foreach ($this->user->item as $no => $amount)
			{
				$item = HOF_Model_Data::newItem($no);

				if (!$possible[$item["type"]]) continue;

				$head = '<label><input type="radio" name="item_no" value="' . $no . '" class="vcent">';

				$head .= $item->html($amount) . "</label>";

				$ExhibitList->AddItem($item, $head);
			}

			$this->output->select = $ExhibitList->GetJavaScript("list");
			$this->output->select .= $ExhibitList->ShowSelect();

			$this->output->show = $ExhibitList->ShowDefault();
		}

		$this->output->article_exhibit_cost = $this->ItemAuction->article_exhibit_cost();

		$this->_render('auction/form.exhibit.body');

	}

}
