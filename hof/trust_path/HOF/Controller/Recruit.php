<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Recruit extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	function _init()
	{
		$this->user = &HOF_Model_Main::getInstance();

		$this->char_recruit_money = array(
			1 => 2000,
			2 => 2000,
			3 => 2500,
			4 => 4000,
			);

		$this->output->error = array();
		$this->output->char_recruit_money = $this->char_recruit_money;
	}

	function _main_before()
	{
		$this->_input();
	}

	function _input()
	{
		$this->input->recruit = $_POST['recruit'];
		$this->input->recruit_name = $_POST['recruit_name'];
		$this->input->recruit_no = max(0, intval($_POST['recruit_no']));

		if (isset($_POST['recruit_gend']))
		{
			$this->input->recruit_gend = min(1, max(0, $_POST['recruit_gend']));
		}
		else
		{
			$this->input->recruit_gend = null;
		}
	}

	function _recruit()
	{
		if ($this->RecruitProcess()) $this->user->SaveData();

		$this->user->fpCloseAll();
		$this->RecruitShow();
	}

	function _error($s, $a = null)
	{
		$this->output->error[] = array($s, $a);
		$this->error[] = $s;
	}

	function RecruitProcess()
	{

		// 雇用数限界
		if (MAX_CHAR <= count($this->user->char)) return false;

		if ($this->input->recruit)
		{

			// キャラのタイプ
			if ($this->char_recruit_money[$this->input->recruit_no] <= 0)
			{
				$this->_error("キャラ 未選択", "margin15");
				return false;
			}

			$hire = $this->char_recruit_money[$this->input->recruit_no];
			$charNo = $this->input->recruit_no;

			// 名前処理
			if ($this->input->recruit_name)
			{
				if (is_numeric(strpos($this->input->recruit_name, "\t"))) return "error.";

				$name = preg_replace('/^[\s\t\r\n]+|[\s\t\r\n]+$/', '', $this->input->recruit_name);
				$name = preg_replace('/\s\s+?/', ' ', $this->input->recruit_name);

				$name = stripslashes($name);
				$len = strlen($name);

				if (0 == $len || 16 < $len)
				{
					$this->_error("名前が短すぎるか長すぎです", "margin15");
					return false;
				}

				$name = htmlspecialchars($name, ENT_QUOTES);
			}
			else
			{
				$this->_error("名前が空欄です", "margin15");
				return false;
			}

			//性別
			if ($this->input->recruit_gend !== 0 && $this->input->recruit_gend !== 1)
			{
				$this->_error("性別 未選択", "margin15");
				return false;
			}
			else
			{
				$Gender = $this->input->recruit_gend ? "♀" : "♂";
			}

			// キャラデータをクラスに入れる

			$plus = array("name" => "$name", "gender" => $this->input->recruit_gend);
			/*
			$char = new HOF_Class_Char();
			$char->SetCharData(array_merge(BaseCharStatus($charNo), $plus));
			*/
			$char = HOF_Model_Char::newBaseChar($charNo, $plus);
			//雇用金
			if ($hire <= $this->user->money)
			{
				$this->user->TakeMoney($hire);
			}
			else
			{
				$this->_error("お金が足りません", "margin15");
				return false;
			}
			// キャラを保存する
			$char->SaveCharData($this->user->id);
			HOF_Helper_Global::ShowResult($char->Name() . "($char->job_name:{$Gender}) が仲間になった！", "margin15");
			return true;
		}
	}

	function RecruitShow()
	{

		$this->output->error_max = (MAX_CHAR <= $this->user->CharCount());

		if (!$this->output->error_max)
		{

			$char = array();

			for ($i = 1; $i <= 4; $i++)
			{
				for ($j = 0; $j <= 1; $j++)
				{
					$char[] = HOF_Model_Char::newBaseChar($i, array('gender' => $j));
				}
			}

			$this->output->char_recruit = $char;
		}

	}
}


?>