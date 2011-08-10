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
			$this->main->RecordRegister($_POST["Newid"]);//ID記録
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
}

?>