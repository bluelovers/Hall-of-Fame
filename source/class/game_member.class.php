<?php

/**
 * @author bluelovers
 * @copyright 2011
 */

class game_member {

	/**
	 * @abstract main
	 */
	var $main;

	function game_member($main) {
		$this->main = &$main;
	}

	/**
	 * pass と id を設定する
	 */
	function Set_ID_PASS() {
		$id	= ($_POST["id"])?$_POST["id"]:$_GET["id"];
		if($id) {
				$this->main->id	= $id;//$_POST["id"];
			// ↓ログイン処理した時だけ
			if ($this->is_registered($this->main->id)) {
				$_SESSION["id"]	= $this->main->id;
			}
		} else if($_SESSION["id"])
			$this->main->id	= $_SESSION["id"];

		$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
		if($pass)
			$this->main->pass	= $pass;//$_POST["pass"];
		else if($_SESSION["pass"])
			$this->main->pass	= $_SESSION["pass"];

		if($this->main->pass)
			$this->main->pass	= $this->main->CryptPassword($this->main->pass);
	}

	/**
	 * $id が過去登録されたかどうか
	 */
	function is_registered($id) {
		if($registered = @file(REGISTER)):
			if(array_search($id."\n",$registered)!==false && !ereg("[\.\/]+",$id) )//改行記号必須
				return true;
			else
				return false;
		endif;
	}

	/**
	 * 入力された情報が型にはまるか判定
	 * → 新規データを作成。
	 */
	function MakeNewData() {
		// 登録者数が限界の場合
		if(MAX_USERS <= count(game_core::glob(USER)))
			return array(false,"Maximum users.<br />登録者数が限界に達してしまった様です。");
		if(isset($_POST["Newid"]))
			trim($_POST["Newid"]);
		if(empty($_POST["Newid"]))
			return array(false,"Enter ID.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["Newid"])||
			ereg("[^0-9a-zA-Z]+",$_POST["Newid"]))//正規表現
			return array(false,"Bad ID");

		if(strlen($_POST["Newid"]) < 4 || 16 < strlen($_POST["Newid"]))//文字制限
			return array(false,"Bad ID");

		if($this->is_registered($_POST["Newid"]))
			return array(false,"This ID has been already used.");

		$file = USER.$_POST["Newid"]."/".DATA;
		// PASS
		//if(isset($_POST["pass1"]))
		//	trim($_POST["pass1"]);
		if(empty($_POST["pass1"]) || empty($_POST["pass2"]))
			return array(false,"Enter both Password.");

		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass1"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass1"]))
			return array(false,"Bad Password 1");
		if(strlen($_POST["pass1"]) < 4 || 16 < strlen($_POST["pass1"]))//文字制限
			return array(false,"Bad Password 1");
		if(!ereg("[0-9a-zA-Z]{4,16}",$_POST["pass2"]) || ereg("[^0-9a-zA-Z]+",$_POST["pass2"]))
			return array(false,"Bad Password 2");
		if(strlen($_POST["pass2"]) < 4 || 16 < strlen($_POST["pass2"]))//文字制限
			return array(false,"Bad Password 2");

		if($_POST["pass1"] !== $_POST["pass2"])
			return array(false,"Password dismatch.");

		$pass = $this->main->CryptPassword($_POST["pass1"]);
		// MAKE
		if(!file_exists($file)){
			mkdir(USER.$_POST["Newid"], 0705);
			$this->RecordRegister($_POST["Newid"]);//ID記録
			$fp=fopen("$file","w");
			flock($fp,LOCK_EX);
				$now	= time();
				fputs($fp,"id=$_POST[Newid]\n");
				fputs($fp,"pass=$pass\n");
				fputs($fp,"last=".$now."\n");
				fputs($fp,"login=".$now."\n");
				fputs($fp,"start=".$now.substr(microtime(),2,6)."\n");
				fputs($fp,"money=".START_MONEY."\n");
				fputs($fp,"time=".START_TIME."\n");
				fputs($fp,"record_btl_log=1\n");
			fclose($fp);
			//print("ID:$_POST[Newid] success.<BR>");
			$_SESSION["id"]=$_POST["Newid"];
			setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
			$success	= "<div class=\"recover\">ID : $_POST[Newid] success. Try Login</div>";
			return array(true,$success);//強引...
		}
	}

	/**
	 * ログイン用のフォーム
	 */
	function LoginForm($message = NULL) {
		?>
<div style="width:730px;">
<!-- ログイン -->
<div style="width:350px;float:right">
<h4 style="width:350px">Login</h4>
<?=$message?>
<form action="<?=INDEX?>" method="post" style="padding-left:20px">
<table><tbody>
<tr>
<td><div style="text-align:right">ID:</div></td>
<td><input type="text" maxlength="16" class="text" name="id" style="width:160px"<?=$_SESSION["id"]?" value=\"$_SESSION[id]\"":NULL?>></td>
</tr>
<tr>
<td><div style="text-align:right">PASS:</div></td>
<td><input type="password" maxlength="16" class="text" name="pass" style="width:160px"></td>
</tr>
<tr><td></td><td>
<input type="submit" class="btn" name="Login" value="login" style="width:80px">&nbsp;
<a href="?newgame">NewGame?</a>
</td></tr>
</tbody></table>
</form>

<h4 style="width:350px">Ranking</h4><?php
	include_once(CLASS_RANKING);
	$Rank	= new Ranking();
	$Rank->ShowRanking(0,4);
	?>
</div>
<!-- 飾 -->
<div style="width:350px;padding:15px;float:left;">
<div style="width:350px;text-align:center;height: 199px;overflow: hidden; margin-bottom: 20px;">
<img src="./image/hof02.gif" style="margin-top: -1px;margin-left: -70px;" />
</div>
<div style="margin-left:20px">
<div class="u">これってどんなゲーム?</div>
<ul>
<li>ゲームの目的はランキング1位になり、<br />1位を守る事です。</li>
<li>冒険要素はないですが、<br />ちょっと深い戦闘システムが売りです。</li>
</ul>
<div class="u">戦闘はどんな感じ?</div>
<ul>
<li>5人のキャラクターでパーティーを編成。</li>
<li>各キャラが行動パターンを持ち、<br />戦闘の状況に応じて技を使い分けます。</li>
<li><a href="?log" class="a0">こちら</a>で戦闘ログが回覧できます。</li>
</ul>
</div>
</div>
<div class="c-both"></div>
</div>

<!-- -------------------------------------------------------- -->

<div style="margin:15px">
<h4>info.</h4>
Users : <?=UserAmount()?> / <?=MAX_USERS?><br />
<?php
	$Abandon	= ABANDONED;
	print(floor($Abandon/(60*60*24))."日データに変化無しでデータ消える。");
print("</div>\n");
	}

	/**
	 * 自分のデータとクッキーを消す
	 */
	function DeleteMyData() {
		if($this->main->pass == $this->main->CryptPassword($_POST["deletepass"]) ) {
			$this->main->DeleteUser();
			$this->main->name	= NULL;
			$this->main->pass	= NULL;
			$this->main->id	= NULL;
			$this->main->islogin= false;
			unset($_SESSION["id"]);
			unset($_SESSION["pass"]);
			setcookie("NO","");
			$this->LoginForm();
			return true;
		}
	}

	/**
	 * 新規ID作成用のフォーム
	 */
	function NewForm($error=NULL) {
		if(MAX_USERS <= count(game_core::glob(USER))) {
			?>

	<div style="margin:15px">
	Maximum users.<br />
	登録者数が限界に達しているようです。
	</div><?php
			return false;
		}
		$idset=($_POST["Newid"]?" value=$_POST[Newid]":NULL);
		?>
	<div style="margin:15px">
	<?=ShowError($error);?>
	<h4>とりあえず New Game!</h4>
	<form action="<?=INDEX?>" method="post">

	<table><tbody>
	<tr><td colspan="2">ID & PASS must be 4 to 16 letters.<br />letters allowed a-z,A-Z,0-9<br />
	ID と PASSは 4-16 文字以内で。半角英数字。</td></tr>
	<tr><td><div style="text-align:right">ID:</div></td>
	<td><input type="text" maxlength="16" class="text" name="Newid" style="width:240px"<?=$idset?>></td></tr>
	<tr><td colspan="2"><br />Password,Re-enter.<br />PASS とその再入力です 確認用。</td></tr>
	<tr><td><div style="text-align:right">PASS:</div></td>
	<td><input type="password" maxlength="16" class="text" name="pass1" style="width:240px"></td></tr>

	<tr><td></td>
	<td><input type="password" maxlength="16" class="text" name="pass2" style="width:240px">(verify)</td></tr>

	<tr><td></td><td><input type="submit" class="btn" name="Make" value="Make" style="width:160px"></td></tr>

	</tbody></table>
	</form>
	</div>
<?php
	}

	/**
	 * 初回ログイン用のフォーム
	 */
	function FirstLogin() {
		// 返値:設定済み=false / 非設定=true
		if ($this->name)
			return false;

		do {
			if (!$_POST["Done"])
				break;
			if(is_numeric(strpos($_POST["name"],"\t"))) {
				$error	= 'error1';
				break;
			}
			if(is_numeric(strpos($_POST["name"],"\n"))) {
				$error	= 'error';
				break;
			}
			$_POST["name"]	= trim($_POST["name"]);
			$_POST["name"]	= stripslashes($_POST["name"]);
			if (!$_POST["name"]) {
				$error	= 'Name is blank.';
				break;
			}
			$length	= strlen($_POST["name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			$userName	= userNameLoad();
			if(in_array($_POST["name"],$userName)) {
				$error	= 'その名前は使用されています。';
				break;
			}
			// 最初のキャラの名前
			$_POST["first_name"]	= trim($_POST["first_name"]);
			$_POST["first_name"]	= stripslashes($_POST["first_name"]);
			if(is_numeric(strpos($_POST["first_name"],"\t"))) {
				$error	= 'error';
				break;
			}
			if(is_numeric(strpos($_POST["first_name"],"\n"))) {
				$error	= 'error';
				break;
			}
			if (!$_POST["first_name"]) {
				$error	= 'Character name is blank.';
				break;
			}
			$length	= strlen($_POST["first_name"]);
			if ( 0 == $length || 16 < $length) {
				$error	= '1 to 16 letters?';
				break;
			}
			if(!$_POST["fjob"]) {
				$error	= 'Select characters job.';
				break;
			}
			$_POST["name"]	= htmlspecialchars($_POST["name"],ENT_QUOTES);
			$_POST["first_name"]	= htmlspecialchars($_POST["first_name"],ENT_QUOTES);

			$this->main->name	= $_POST["name"];
			userNameAdd($this->main->name);
			$this->main->SaveData();
			switch($_POST["fjob"]){
				case "1":
					$job = 1; $gend = 0; break;
				case "2":
					$job = 1; $gend = 1; break;
				case "3":
					$job = 2; $gend = 0; break;
				default:
					$job = 2; $gend = 1;
			}
			include(DATA_BASE_CHAR);
			$char	= new char();
			$char->SetCharData(array_merge(BaseCharStatus($job),array("name"=>$_POST[first_name],"gender"=>"$gend")));
			$char->SaveCharData($this->id);
			return false;
		}while(0);

		include(DATA_BASE_CHAR);
		$war_male	= new char();
		$war_male->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
		$war_female	= new char();
		$war_female->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
		$sor_male	= new char();
		$sor_male->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
		$sor_female	= new char();
		$sor_female->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));

		?>
	<form action="<?=INDEX?>" method="post" style="margin:15px">
	<?php ShowError($error);?>
	<h4>Name of Team</h4>
	<p>Decide the Name of the team.<br />
	It should be more than 1 and less than 16 letters.<br />
	Japanese characters count as 2 letters.</p>
	<p>1-16文字でチームの名前決めてください。<br />
	日本語でもOK。<br />
	日本語は 1文字 = 2 letter</p>
	<div class="bold u">TeamName</div>
	<input class="text" style="width:160px" maxlength="16" name="name"<?print($_POST["name"]?"value=\"$_POST[name]\"":"")?>>
	<h4>First Character</h4>
	<p>Decide the name of Your First Charactor.<br>
	more than 1 and less than 16 letters.</p>
	<p>初期キャラの名前。</p>
	<div class="bold u">CharacterName</div>
	<input class="text" type="text" name="first_name" maxlength="16" style="width:160px;margin-bottom:10px">
	<table cellspacing="0" style="width:400px"><tbody>
	<tr><td class="td1" valign="bottom"><div style="text-align:center"><?=$war_male->ShowImage()?><br><input type="radio" name="fjob" value="1" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$war_female->ShowImage()?><br><input type="radio" name="fjob" value="2" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$sor_male->ShowImage()?><br><input type="radio" name="fjob" value="3" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?=$sor_female->ShowImage()?><br><input type="radio" name="fjob" value="4" style="margin:3px"></div></td></tr>
	<tr><td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td>
	<td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td></tr>
	<tr><td colspan="2" class="td4"><div style="text-align:center">Warrior</div></td><td colspan="2" class="td4"><div style="text-align:center">Socerer</div></td></tr>
	</tbody></table>
	<p>Choose your first character's job &amp; Gender.</p>
	<p>最初のキャラの職と性別</p>
	<input class="btn" style="width:160px" type="submit" value="Done" name="Done">
	<input type="hidden" value="1" name="Done">
	<input class="btn" style="width:160px" type="submit" value="logout" name="logout"></form><?php
			return true;
	}

	/**
	 * $id を登録済みidとして記録する
	 */
	function RecordRegister($id) {
		$fp=fopen(REGISTER,"a");
		flock($fp,2);
		fputs($fp,"$id\n");
		fclose($fp);
	}

	/**
	 * ログインした時間を設定する
	 */
	function RenewLoginTime() {
		$this->main->login	= time();
	}

	/**
	 * ログインしたのか、しているのか、ログアウトしたのか。
	 */
	function CheckLogin() {
		//logout
		if(isset($_POST["logout"])) {
		//	$_SESSION["pass"]	= NULL;
		//	echo $_SESSION["pass"];
			unset($_SESSION["pass"]);
		//	session_destroy();
			return false;
		}

		//session
		$file=USER.$this->main->id."/".DATA;//data.dat
		if ($data = $this->main->LoadData()) {
			//echo "<div>$data[pass] == $this->pass</div>";
			if($this->main->pass == NULL)
				return false;
			if ($data["pass"] === $this->main->pass) {
				//ログイン状態
				$this->main->DataUpDate($data);
				$this->main->SetData($data);
				if(RECORD_IP)
					$this->main->SetIp($_SERVER['REMOTE_ADDR']);
				$this->RenewLoginTime();

				$pass	= ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
				if ($pass) {//ちょうど今ログインするなら
					$_SESSION["id"]	= $this->main->id;
					$_SESSION["pass"]	= $pass;
					setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
				}

				$this->main->islogin	= true;//ログイン状態
				return true;
			} else
				return "Wrong password!";
		} else {
			if($_POST["id"])
				return "ID \"{$this->id}\" doesnt exists.";
		}
	}
}

?>