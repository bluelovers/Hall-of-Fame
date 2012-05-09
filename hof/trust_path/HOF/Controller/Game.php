<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class HOF_Controller_Game extends HOF_Class_Controller
{

	/**
	 * @var HOF_Class_User
	 */
	var $user;

	protected $_cache;

	function _init()
	{
		$this->user = &HOF_Model_Main::getInstance();
	}

	function _main_input()
	{
		$this->input->make = HOF::$input->post->Make;
		$this->input->newid = trim(HOF::$input->post->Newid);
		$this->input->pass1 = trim(HOF::$input->post->pass1);
		$this->input->pass2 = trim(HOF::$input->post->pass2);

		$this->input->login = HOF::$input->request->login;
		$this->input->logout = HOF::$input->request->logout;

		$QUERY_STRING = HOF::$input->server->QUERY_STRING;

		if (HOF::$input->request['logout'])
		{
			$this->input->action = 'logout';

			$this->_main_exec('logout');
		}
		elseif ($QUERY_STRING == 'newgame')
		{
			$this->input->action = 'newgame';

			$this->_main_setup('newgame');
		}
		elseif (HOF::$input->request['login'])
		{
			$this->input->action = 'login';
		}

		//error_reporting(E_ALL);
	}

	function _main_before()
	{
		parent::_main_before();

		if ($this->action != 'first_login' && $this->action != 'check_login')
		{
			$this->user->fpCloseAll();
		}
	}

	function _main_action_default($message = null)
	{
		$this->_main_exec('login', $message);
	}

	/**
	 * 初回ログイン用のフォーム
	 */
	function _main_action_first_login()
	{
		if (!$this->FirstLogin())
		{
			$this->_main_stop(true);
		}
		else
		{
			$this->user->fpCloseAll();
		}


	}

	function _main_action_delete_my_data()
	{
		if (!$this->DeleteMyData())
		{
			$this->_main_stop(true);
		}
		else
		{
			$this->user->fpCloseAll();
			$this->_main_exec('login', $message);
		}
	}

	function _ShowRanking()
	{
		include_once (CLASS_RANKING);

		$Rank = new Ranking();
		$Rank->ShowRanking(0, 4);
	}

	function _main_action_login($message = null)
	{
		$this->output->message = $message;
		$this->output->id = $_SESSION["id"];

		$this->output->game_users = HOF_Helper_Global::UserAmount();
		$this->output->game_users_max = MAX_USERS;

		$Abandon = ABANDONED;
		$Abandon = floor($Abandon / (60 * 60 * 24));

		$this->output->game_abandon = $Abandon;
	}

	/**
	 * 新規ID作成用のフォーム
	 */
	function _main_action_newgame()
	{
		$this->output->newid = $this->input->newid;

		/**
		 * 登録者数が限界の場合
		 */
		if (MAX_USERS <= count(game_core::glob(USER)))
		{
			$this->_error('Maximum users.<br />登録者数が限界に達してしまった様です。');

			/**
			 * 登録者数が限界の場合
			 */
			$this->output->user_full = true;
		}
		elseif ($this->input->make)
		{
			list($bool, $message) = $this->MakeNewData();

			if (true === $bool)
			{
				$this->_main_exec('login', $message);
			}
			elseif ($message)
			{
				$this->_error($message, 'margin15');
			}
		}

	}

	function _error($s, $a = null)
	{
		$this->output->error[] = array($s, $a);
		$this->error[] = $s;
	}

	/**
	 * $id が過去登録されたかどうか
	 */
	function is_registered($id)
	{
		if ($registered = @file(REGISTER))
		{
			// 改行記号必須
			if (array_search($id . "\n", $registered) !== false && !ereg("[\.\/]+", $id))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * 入力された情報が型にはまるか判定
	 * → 新規データを作成。
	 */
	function MakeNewData()
	{
		if (empty($this->input->newid)) return array(false, "Enter ID.");

		// 正規表現
		if (!ereg("[0-9a-zA-Z]{4,16}", $this->input->newid) || ereg("[^0-9a-zA-Z]+", $this->input->newid)) return array(false, "Bad ID");

		if (strlen($this->input->newid) < 4 || 16 < strlen($this->input->newid)) //文字制限
 				return array(false, "Bad ID");

		if ($this->is_registered($this->input->newid)) return array(false, "This ID has been already used.");

		$file = USER . $this->input->newid . "/" . DATA . '.yml';
		// PASS
		//if(isset($this->input->pass1))
		//	trim($this->input->pass1);
		if (empty($this->input->pass1) || empty($this->input->pass2)) return array(false, "Enter both Password.");

		if (!ereg("[0-9a-zA-Z]{4,16}", $this->input->pass1) || ereg("[^0-9a-zA-Z]+", $this->input->pass1)) return array(false, "Bad Password 1");
		if (strlen($this->input->pass1) < 4 || 16 < strlen($this->input->pass1)) //文字制限
 				return array(false, "Bad Password 1");
		if (!ereg("[0-9a-zA-Z]{4,16}", $this->input->pass2) || ereg("[^0-9a-zA-Z]+", $this->input->pass2)) return array(false, "Bad Password 2");
		if (strlen($this->input->pass2) < 4 || 16 < strlen($this->input->pass2)) //文字制限
 				return array(false, "Bad Password 2");

		if ($this->input->pass1 !== $this->input->pass2) return array(false, "Password dismatch.");

		$pass = $this->user->CryptPassword($this->input->pass1);
		// MAKE
		if (!file_exists($file))
		{
			mkdir(USER . $this->input->newid, 0705);
			$this->RecordRegister($this->input->newid); //ID記録

			/*
			$fp = fopen("$file", "w");
			flock($fp, LOCK_EX);
			$now = time();
			fputs($fp, "id=$_POST[Newid]\n");
			fputs($fp, "pass=$pass\n");
			fputs($fp, "last=" . $now . "\n");
			fputs($fp, "login=" . $now . "\n");
			fputs($fp, "start=" . $now . substr(microtime(), 2, 6) . "\n");
			fputs($fp, "money=" . START_MONEY . "\n");
			fputs($fp, "time=" . START_TIME . "\n");
			fputs($fp, "record_btl_log=1\n");
			fclose($fp);
			*/

			$data = array(
				'id' => $this->input->newid,
				'pass' => $pass,
				'last' => $now,
				'login' => $now,
				'start' => $now . substr(microtime(), 2, 6),
				'money' => START_MONEY,
				'time' => START_TIME,
				'record_btl_log' => 1,
				);

			HOF_Class_Yaml::save($file, $data);

			//print("ID:$_POST[Newid] success.<BR>");
			$_SESSION["id"] = $this->input->newid;
			setcookie("NO", session_id(), time() + COOKIE_EXPIRE);
			$success = "ID : {$this->input->newid} success. Try Login";
			return array(true, $success); //強引...
		}
	}

	/**
	 * $id を登録済みidとして記録する
	 */
	function RecordRegister($id)
	{
		$fp = fopen(REGISTER, "a");
		flock($fp, 2);
		fputs($fp, "$id\n");
		fclose($fp);
	}

	/**
	 * 初回ログイン用のフォーム
	 */
	function FirstLogin()
	{
		// 返値:設定済み=false / 非設定=true
		if ($this->user->name) return false;

		$chars = array();
		$k = 1;

		for ($i = 1; $i <= 4; $i++)
		{
			for ($j = 0; $j <= 1; $j++)
			{
				$chars[$k] = HOF_Model_Char::newBaseChar($i, array('gender' => $j));

				$Gender = $j ? "♀" : "♂";

				$chars[$k]->job_name .= $Gender;

				$k++;
			}
		}

		$this->input->recruit_no = HOF::$input->post->recruit_no;
		$this->input->team_name = trim(HOF::$input->post->team_name, ENT_QUOTES);
		$this->input->char_name = trim(HOF::$input->post->char_name, ENT_QUOTES);

		$this->input->done = HOF::$input->post->Done;

		$this->output->recruit_no = $this->input->recruit_no;
		$this->output->team_name = $this->input->team_name;
		$this->output->char_name = $this->input->char_name;

		do
		{
			if (!$this->input->done) break;

			if (is_numeric(strpos($this->input->team_name, "\t")) || is_numeric(strpos($this->input->char_name, "\t")))
			{
				$this->_error('error1');
				break;
			}
			if (is_numeric(strpos($this->input->team_name, "\n")) || is_numeric(strpos($this->input->char_name, "\n")))
			{
				$this->_error('error');
				break;
			}

			$this->input->team_name = stripslashes($this->input->team_name);

			// 最初のキャラの名前
			$this->input->char_name = stripslashes($this->input->char_name);

			if (!$this->input->team_name)
			{
				$this->_error('Name is blank.');
			}

			if (!$this->input->char_name)
			{
				$this->_error('Character name is blank.');
			}

			if (!$this->input->recruit_no)
			{
				$this->_error('Select characters job.');
				break;
			}

			if (!empty($this->error))
			{
				break;
			}

			$length = strlen($this->input->team_name);
			$length1 = strlen($this->input->char_name);
			if (0 == $length || 16 < $length || 0 == $length1 || 16 < $length1)
			{
				$this->_error('1 to 16 letters?');
				break;
			}

			$userName = userNameLoad();
			if (in_array($this->input->team_name, $userName))
			{
				$this->_error('その名前は使用されています。');
				break;
			}

			$char = null;

			if ($this->input->recruit_no && $chars[$this->input->recruit_no] instanceof HOF_Class_Char)
			{
				$char = HOF_Model_Char::newBaseChar(ceil($this->input->recruit_no / 2), array("name" => $this->input->char_name, "gender" => $chars[$this->input->recruit_no]->gender));
			}
			else
			{
				$this->_error('Select characters job.');
				break;
			}

			if (!$char)
			{
				$this->_error('error');
			}

			if (!empty($this->error))
			{
				break;
			}

			$this->input->team_name = htmlspecialchars($this->input->team_name, ENT_QUOTES);
			$this->input->char_name = htmlspecialchars($this->input->char_name, ENT_QUOTES);

			$this->user->name = $this->input->team_name;

			userNameAdd($this->user->name);

			$this->user->SaveData();

			$char->SaveCharData($this->user->id);

			return false;
		} while (0);

		$k = 0;
		foreach ($chars as $i => $char)
		{
			$this->output->char_recruit[$k][$i] = $char;

			if (!($i % 4)) $k++;
		}

		return true;
	}

	//	自分のデータとクッキーを消す
	function DeleteMyData()
	{
		if ($this->user->pass == $this->user->CryptPassword($_POST["deletepass"]))
		{
			$this->user->DeleteUser();
			$this->user->name = NULL;
			$this->user->pass = NULL;
			$this->user->id = NULL;
			$this->user->islogin = false;
			unset($_SESSION["id"]);
			unset($_SESSION["pass"]);
			setcookie("NO", "");
			return true;
		}
	}

	function _main_action_logout($message = null)
	{
		if ($this->input->action == 'login') return;

		$this->_cache->logout = true;

		$this->user->islogin = false;

		unset($_SESSION["pass"]);

		$this->_main_exec('login', $this->input->action == 'logout' ? 'User Logout!!' : $message);
	}

	function _main_action_check_login()
	{
		$message = $this->CheckLogin();

		if ($message !== true)
		{
			$this->_main_exec($this->input->action ? $this->input->action : 'logout', $message);
		}
		else
		{
			$this->_main_stop(true);
		}
	}

	/**
	 * ログインしたのか、しているのか、ログアウトしたのか。
	 */
	function CheckLogin()
	{

		$this->input->pass = HOF::$input->post->pass;
		$this->input->id = HOF::$input->post->id;

		//session
		if ($data = $this->user->LoadData())
		{
			//echo "<div>$data[pass] == $this->pass</div>";
			if ($this->user->pass == NULL) return false;

			if ($data["pass"] === $this->user->pass)
			{
				// ログイン状態
				$this->user->DataUpDate($data);
				$this->user->SetData($data);

				if (RECORD_IP)
				{
					$this->user->SetIp($_SERVER['REMOTE_ADDR']);
				}

				$this->user->RenewLoginTime();

				if ($this->input->pass)
				{
					// ちょうど今ログインするなら
					$_SESSION["id"] = $this->user->id;
					$_SESSION["pass"] = $this->input->pass;

					setcookie("NO", session_id(), time() + COOKIE_EXPIRE);
				}

				// ログイン状態
				$this->user->islogin = true;

				return true;
			}
			else
			{
				return "Wrong password!";
			}
		}
		else
		{
			if ($this->input->id)
			{
				return "ID \"{$this->input->id}\" doesnt exists.";
			}
		}

		return false;
	}

	function _main_action_setting()
	{
		$this->input->NewName = HOF::$input->post->NewName;
		$this->input->setting01 = HOF::$input->post->setting01;
		$this->input->record_battle_log = HOF::$input->post->record_battle_log;
		$this->input->color = HOF::$input->post->color;
		$this->input->no_JS_itemlist = HOF::$input->post->no_JS_itemlist;

		$this->output->colors = HOF_Model_Data::getColorList();

		if ($this->SettingProcess()) $this->user->SaveData();

		$this->user->fpCloseAll();

		if ($this->user->record_btl_log)
		{
			$this->output->record_btl_log = " checked";
		}

		if ($this->user->no_JS_itemlist)
		{
			$this->output->no_JS_itemlist = " checked";
		}

		return $Result;
	}

	function SettingProcess()
	{
		if ($this->input->NewName)
		{
			$NewName = $this->input->NewName;
			if (is_numeric(strpos($NewName, "\t")))
			{
				HOF_Helper_Global::ShowError('error1');
				return false;
			}
			$NewName = trim($NewName);
			$NewName = stripslashes($NewName);
			if (!$NewName)
			{
				HOF_Helper_Global::ShowError('Name is blank.');
				return false;
			}
			$length = strlen($NewName);
			if (0 == $length || 16 < $length)
			{
				HOF_Helper_Global::ShowError('1 to 16 letters?');
				return false;
			}
			$userName = userNameLoad();
			if (in_array($NewName, $userName))
			{
				HOF_Helper_Global::ShowError("その名前は使用されている。", "margin15");
				return false;
			}
			if (!$this->user->TakeMoney(NEW_NAME_COST))
			{
				HOF_Helper_Global::ShowError('money not enough');
				return false;
			}
			$OldName = $this->user->name;
			$NewName = htmlspecialchars($NewName, ENT_QUOTES);
			if ($this->user->ChangeName($NewName))
			{
				HOF_Helper_Global::ShowResult("Name Changed ({$OldName} -> {$NewName})", "margin15");
				//return false;
				userNameAdd($NewName);
				return true;
			}
			else
			{
				HOF_Helper_Global::ShowError("?"); //名前が同じ？
				return false;
			}
		}

		if ($this->input->setting01)
		{
			if ($this->input->record_battle_log) $this->user->record_btl_log = 1;
			else  $this->user->record_btl_log = false;

			if ($this->input->no_JS_itemlist) $this->user->no_JS_itemlist = 1;
			else  $this->user->no_JS_itemlist = false;
		}
		if ($this->input->color)
		{
			if (strlen($this->input->color) != 6 && !ereg("^[0369cf]{6}", $this->input->color)) return "error 12072349";
			$this->user->UserColor = $this->input->color;
			HOF_Helper_Global::ShowResult("Setting changed.", "margin15");
			return true;
		}
	}

}


?>