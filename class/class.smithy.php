<?php

/**
 * 鍛冶屋
 */
class Item {
	/**
		* アイテムの基本データを保持する変数
		*/
	var $item;

	/**
		* アイテムの基本番号
		*/
	var $base;

	/**
		* 精錬値
		*/
	var $refine;

	/**
		* 付加能力1
		*/
	var $option0;

	/**
		* 付加能力2
		*/
	var $option1;

	/**
		* 付加能力3
		*/
	var $option2;

	/**
		* アイテムの種類
		*/
	var $type;

	/**
		* コンストラクタ：アイテム番号を受け取り、初期化する
		* Constructor: Initializes the item with the given number
		*
		* @param int $no Item number
		*/
	function Item($no) {
		mt_srand();
		$this->SetItem($no);
	}

	/**
		* アイテムが渡された場合データを解析する
		* Parses item data if an item number is provided
		*
		* @param int $no Item number
		*/
	function SetItem($no) {
		if(!$no) return false;
		$this->item	= $no;

		$this->base	= substr($no,0,4);//アイテムの基本番号

		// 精錬値
		$this->refine	= (int)substr($no,4,2);
		if(!$this->refine)
			$this->refine	= 0;

		// 付加能力
		$this->option0	= substr($no,6,3);
		$this->option1	= substr($no,9,3);
		$this->option2	= substr($no,12,3);

		if($item = LoadItemData($this->base)) {
			$this->type	= $item["type"];
		}
	}

	/**
		* アイテムを製作する
		* Creates a new item
		*/
	function CreateItem() {
		$this->refine	= false;
		$this->option0	= false;
		$this->option1	= false;
		$this->option2	= false;
		list($low,$high)	= ItemAbilityPossibility($this->type);

		// 2:3:4
		// 付加能力がつく確率。
		$prob	= mt_rand(1,9);
		switch($prob) {
			case 1:
			case 2:
			case 3:
				$AddLow	= true;
				break;
			case 4:
			case 5:
			case 6:
				$AddHigh	= true;
				break;
			case 7:
			case 8:
			case 9:
				$AddLow	= true;
				$AddHigh	= true;
				break;
		}

		// array_rand() は微妙なので敬遠する。

		if($AddHigh) {
			$prob	= mt_rand(0,count($high)-1);
			$this->option1	= $high["$prob"];
		}
		if($AddLow) {
			$prob	= mt_rand(0,count($low)-1);
			$this->option2	= $low["$prob"];
		}
	}

	/**
		* 特殊なあれ？3番目の付加？
		* Adds a special option to the item
		*
		* @param string $opt Option code
		*/
	function AddSpecial($opt) {
		$this->option0	= $opt;
	}

	/**
		* 精錬可能な物かどうか。
		* Checks if the item can be refined
		*/
	function CanRefine() {
		$possible	= CanRefineType();
		if (REFINE_LIMIT <= $this->refine)
			return false;
		else if(in_array($this->type,$possible))
			return true;
		else
			return false;
	}

	/**
		* 精錬をする
		* Refines the item
		*/
	function ItemRefine() {
		if($this->RefineProb($this->refine)) {
			print("+".$this->refine." -> ");
			$this->refine++;
			print("+".$this->refine." <span class=\"recover\">Success</span>&nbsp;!<br />\n");
			return true;
		} else {
			print("+".$this->refine." -> ");
			print("+".($this->refine + 1)." <span class=\"dmg\">Failed</span>.<br />\n");
			return false;
		}
	}

	/**
		* 精錬度別に精錬成功か否かとその確率
		* Determines the success rate of refining based on the current refinement level
		*
		* @param int $now Current refinement level
		*/
	function RefineProb($now) {
		$prob	= mt_rand(0,99);
		//return true;// コメント取ると成功率100%
		switch($now) {
			case 0:
			case 1:
			case 2:
			case 3:
				return true;
			case 4:
				if($prob < 60)
				return true;
			case 5:
				if($prob < 40)
				return true;
			case 6:
				if($prob < 40)
				return true;
			case 7:
				if($prob < 20)
				return true;
			case 8:
				if($prob < 20)
				return true;
			case 9:
				if($prob < 10)
				return true;
		}
		return false;
	}

	/**
		* アイテムを返す。
		* Returns the item string
		*/
	function ReturnItem() {
		// 精錬もオプションも無い場合は先頭4文字だけ返す。
		if(!$this->refine && !$this->option0 && !$this->option1 && !$this->option2 )
			return $this->base;

		// 少なくとも精錬されているか、オプションが有る場合
		$item	= $this->base.
				sprintf("%02d",$this->refine).
				$this->option0.
				$this->option1.
				$this->option2;
		return $item;
	}
}

?>