<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

class user
{

	// ファイルポインタ
	var $fp;
	var $file;

	var $id, $pass;
	var $name, $last, $login, $start;
	var $money;
	var $char;
	var $time;
	var $wtime; //総消費時間
	var $ip; //IPアドレス

	var $party_memo;
	var $party_rank; //ランキング用のパーティ
	var $rank_set_time; //ランキングPT設定した時間
	var $rank_btl_time; //次のランク戦に挑戦できる時間
	// ランキングの成績
	// = "総戦闘回数<>勝利数<>敗北数<>引き分け<>首位防衛";
	var $rank_record;
	var $union_btl_time; //次のUnion戦に挑戦できる時間

	// OPTION
	var $record_btl_log;
	var $no_JS_itemlist;
	var $UserColor;

	// ユーザーアイテム用の変数
	var $fp_item;
	var $item;

	//	IPを変更
	function SetIp($ip)
	{
		$this->ip = $ip;
	}

	/**
	 * 名前を返す
	 */
	function Name($opt = false)
	{
		if ($this->name)
		{
			if ($opt) return '<span class="' . $opt . '">' . $this->name . '</span>';
			else  return $this->name;
		}
		else
		{
			return false;
		}
	}

	//	名前を変える
	function ChangeName($name)
	{

		if ($this->name == $name) return false;

		$this->name = $name;
		return true;
	}

	//	Union戦闘した時間をセット
	function UnionSetTime()
	{
		$this->union_btl_time = time();
	}

	//	UnionBattleができるかどうか確認する。
	function CanUnionBattle()
	{
		$Now = time();
		$Past = $this->union_btl_time + UNION_BATTLE_NEXT;
		if ($Past <= $Now)
		{
			return true;
		}
		else
		{
			return abs($Now - $Past);
		}
	}

	//	次のランク戦に挑戦できる時間を記録する。
	function SetRankBattleTime($time)
	{
		$this->rank_btl_time = $time;
	}


	//	ランキング挑戦できるか？(無理なら残り時間を返す)
	function CanRankBattle()
	{
		$now = time();
		if ($this->rank_btl_time <= $now)
		{
			return true;
		}
		else
			if (!$this->rank_btl_time)
			{
				return true;
			}
			else
			{
				$left = $this->rank_btl_time - $now;
				$hour = floor($left / 3600);
				$minutes = floor(($left % 3600) / 60);
				$seconds = floor(($left % 3600) % 60);
				return array(
					$hour,
					$minutes,
					$seconds);
			}
	}


	//	お金を増やす
	function GetMoney($no)
	{
		$this->money += $no;
	}


	//	お金を減らす
	function TakeMoney($no)
	{
		if ($this->money < $no)
		{
			return false;
		}
		else
		{
			$this->money -= $no;
			return true;
		}
	}


	//	時間を消費する(総消費時間の加算)
	function WasteTime($time)
	{
		if ($this->time < $time) return false;
		$this->time -= $time;
		$this->wtime += $time;
		return true;
	}

	//	アイテムを追加
	function AddItem($no, $amount = false)
	{
		if (!isset($this->item)) //どうしたもんか…
 				$this->LoadUserItem();
		if ($amount) $this->item[$no] += $amount;
		else  $this->item[$no]++;
	}


	//	アイテムを削除
	function DeleteItem($no, $amount = false)
	{
		if (!isset($this->item)) //どうしたもんか…
 				$this->LoadUserItem();

		// 減らす数。
		if ($this->item[$no] < $amount)
		{
			$amount = $this->item[$no];
			if (!$amount) $amount = 0;
		}
		if (!is_numeric($amount)) $amount = 1;

		// 減らす。
		$this->item[$no] -= $amount;
		if ($this->item[$no] < 1) unset($this->item[$no]);

		return $amount;
	}

	//	パスワードを暗号化する
	function CryptPassword($pass)
	{
		return substr(crypt($pass, CRYPT_KEY), strlen(CRYPT_KEY));
	}


	//	名前を消す
	function DeleteName()
	{
		$this->name = NULL;
	}

	//	放棄されているかどうか確かめる
	function IsAbandoned()
	{
		$now = time();
		// $this->login がおかしければ終了する。
		if (strlen($this->login) !== 10)
		{
			return false;
		}
		if (($this->login + ABANDONED) < $now)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}


?>