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

	function _main_init()
	{
		$this->ItemAuction = new HOF_Class_Item_Auction('item');
		$this->ItemAuction->article_form_query(array('auction'));
		$this->ItemAuction->article_item_check_success(); // 競売が終了した品物を調べる
		$this->ItemAuction->save_user();

		$this->user = &HOF::user();

		$this->ItemAuction->user(&$this->user);
	}

	function _main_before()
	{
		parent::_main_before();

		if (!$this->user->allowPlay())
		{
			$this->_main_stop(true);

			HOF_Class_Controller::getInstance('game', 'login')->_main_exec('login');

			return;
		}

		// アイテムデータ読む
		$this->user->LoadUserItem();

		if (!AUCTION_TOGGLE) $this->_msg_error("機能停止中");
		if (!AUCTION_EXHIBIT_TOGGLE) $this->_msg_error("出品停止中");

		$this->options['escapeHtml'] = false;

		if ($this->action != 'enter' && !$this->AuctionEnter())
		{
			$this->input->action = $this->action;

			$this->_main_setup('enter');
		}

		$this->output->action = $this->input->action;
	}

	function _main_input()
	{
		$this->input->last_article_no = HOF::request()->post->article_no;
		$this->input->BidPrice = max(0, intval(HOF::request()->post->BidPrice));

		$this->input->sort = HOF::request()->request->sort;

		$this->input->_timestamp = HOF::request()->post->_timestamp;

		$this->output['form._timestamp'] = time();
	}

	function _main_action_default()
	{
		/**
		 * 出品用のフォーム
		 * 表示を要求した場合か、
		 * 出品に失敗した場合表示する。
		 */
		$ResultBidding = $this->AuctionItemBiddingProcess();

		if ($ResultBidding === true)
		{
			$this->user->SaveData();
		}

		$this->user->fpclose_all();
	}

	function _main_action_exhibit()
	{
		$this->input->ExhibitItemForm = HOF::request()->post->ExhibitItemForm;
		$this->input->PutAuction = HOF::request()->post->PutAuction;

		$this->input->item_no = HOF::request()->post->item_no;

		$this->input->ExhibitTime = HOF::request()->post->ExhibitTime;
		$this->input->Amount = max(0, intval(HOF::request()->post->Amount));
		$this->input->StartPrice = max(0, intval(HOF::request()->post->StartPrice));
		$this->input->Comment = HOF::request()->post->Comment;

		$ResultExhibit = $this->AuctionItemExhibitProcess();

		if ($ResultExhibit !== false)
		{
			// 出品か入札に成功した場合はデータを保存する

			if ($ResultExhibit === true)
			{
				$this->user->SaveData();
			}

			$this->user->fpclose_all();

			$this->_main_exec();
		}
		else
		{
			// それ以外
			$this->user->fpclose_all();
			$this->AuctionItemExhibitForm();
		}
	}

	function _main_action_enter()
	{
		$this->input->JoinMember = HOF::request()->post->JoinMember;

		$this->AuctionJoinMember();

		if ($this->AuctionEnter())
		{
			$this->_main_exec($this->input->action);
		}
		else
		{
			$this->_main_exec('log');
		}
	}

	function _main_after()
	{
		// 変更があった場合だけ保存する。
		$this->ItemAuction->fpsave();

		parent::_main_after();
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
			//$this->_msg_error("You are already a member.\n");
			return false;
		}

		// お金が足りない
		if (!$this->user->TakeMoney(round(START_MONEY * 1.10)))
		{
			$this->_msg_error("お金が足りません<br />\n");
			return false;
		}

		// アイテムを足す
		$this->user->AddItem(9000);
		$this->user->SaveUserItem();
		$this->user->SaveData();
		$this->_msg_result("オークション会員になりました。<br />\n");

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
			$this->_msg_error("入札価格に誤りがあります。");
			return false;
		}

		// まだ出品中かどうか確認する。
		if (!$this->ItemAuction->exists($this->input->last_article_no))
		{
			$this->_msg_error("その競売品の出品が確認できません。");
			return false;
		}

		// 自分が入札できる人かどうかの確認
		if (!$this->ItemAuction->article_item_bid_right($this->input->last_article_no, $this->user->id))
		{
			$this->_msg_error("No." . $this->input->last_article_no . "&nbsp;は入札済みか出品者です。");
			return false;
		}

		// 最低入札価格を割っていないか確認する。
		$Bottom = $this->ItemAuction->article_item_price_bid_min($this->input->last_article_no);
		if ($this->input->BidPrice < $Bottom)
		{
			$this->_msg_error("最低入札価格を下回っています。");
			$this->_msg_error("提示入札価格:" . HOF_Helper_Global::MoneyFormat($this->input->BidPrice) . "&nbsp;最低入札価格:" . HOF_Helper_Global::MoneyFormat($Bottom));
			return false;
		}

		// 金持ってるか確認する
		if (!$this->user->TakeMoney($this->input->BidPrice))
		{
			$this->_msg_error("所持金が足りないようです。");
			return false;
		}

		// 実際に入札する。
		if ($this->ItemAuction->article_item_bid($this->input->last_article_no, $this->input->BidPrice, $this->user->id, $this->user->name))
		{
			$this->_msg_result("No:{$this->input->last_article_no}&nbsp;に&nbsp;" . HOF_Helper_Global::MoneyFormat($this->input->BidPrice) . "&nbsp;で入札しました。<br />\n");
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

		if (!$this->input->PutAuction) return false;

		if (!$this->input->item_no)
		{
			$this->_msg_error("Select Item.");
			return false;
		}

		// アイテムが読み込めない場合
		if (!$item = HOF_Model_Data::getItemData($this->input->item_no))
		{
			$this->_msg_error("Failed to load item data.");
			return false;
		}

		// アイテムを所持していない場合
		if (!$this->user->item[$this->input->item_no])
		{
			$this->_msg_error("Item < {$item[name]} > doesn't exists.");
			return false;
		}

		if ($message = $this->ItemAuction->article_exhibit_price_check(&$this->input->StartPrice, $item))
		{
			$this->_msg_error($message);
			return false;
		}

		// セッションによる30秒間の出品拒否
		$SessionLeft = 30 - (time() - HOF::session(true)->AuctionExhibit);
		if (HOF::session(true)->AuctionExhibit && 0 < $SessionLeft)
		{
			$this->_msg_error("Wait {$SessionLeft}seconds to ReExhibit.");
			return false;
		}
		elseif (!$this->input->_timestamp || $this->input->_timestamp >= REQUEST_TIME || (HOF::session(true)->AuctionExhibit && $this->input->_timestamp <= HOF::session(true)->AuctionExhibit))
		{
			$this->_msg_error("Unknow Error!!");
			return false;
		}

		// 同時出品数の制限
		if ($message = $this->ItemAuction->article_count_check_max())
		{
			$this->_msg_error($message);
			return false;
		}

		// 出品費用
		if ($message = $this->ItemAuction->user_take_exhibit_cost())
		{
			$this->_msg_error($message);
			return false;
		}

		if ($message = $this->ItemAuction->article_exhibit_item_check($item))
		{
			$this->_msg_error($message);
			return false;
		}

		// 出品時間の確認
		if ($message = $this->ItemAuction->article_exhibit_time_check($this->input->ExhibitTime))
		{
			var_dump($_POST);
			$this->_msg_error($message);
			return false;
		}

		// 数量の確認
		$amount = max(1, (int)$this->input->Amount);

		// 減らす(所持数より多く指定された場合その数を調節する)
		HOF::session(true)->AuctionExhibit = REQUEST_TIME; //セッションで2重出品を防ぐ

		$amount = $this->user->DeleteItem($this->input->item_no, $amount);
		$this->user->SaveUserItem();

		// 出品する
		$this->ItemAuction->article_item_add($this->input->item_no, $amount, $this->user->id, $this->input->ExhibitTime, $this->input->StartPrice, $this->input->Comment);
		$this->_msg_result($item["name"] . "&nbsp;を&nbsp;{$amount}個&nbsp;出品しました。");

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

		//$this->_render('auction/form.exhibit.body');

	}

	function _msg_error($s, $a = null)
	{
		$this->output->msg_error[] = array($s, $a);
		$this->error[] = $s;
	}

	function _msg_result($s, $a = null)
	{
		$this->output->msg_result[] = array($s, $a);
		$this->msg_result[] = $s;
	}

}
