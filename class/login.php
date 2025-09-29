<?php

/**
 * 首次登入處理
 * First login process
 * 初回ログイン用のフォ一ム
 * @param main $main 主物件
 * @return bool
 */
function FirstLogin($main) {
    // 返值:設定濟み=false / 非設定=true
    // Return value: set=false / not set=true
    if ($main->name)
        return false;

    $error = null;
    do {
        if (!isset($_POST["Done"]))
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
            $error	= '該名字已被使用。';
            break;
        }
        // 最初のキャラの名前
        // First character's name
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
        if(!isset($_POST["fjob"])) {
            $error	= 'Select characters job.';
            break;
        }
        $_POST["name"]	= htmlspecialchars($_POST["name"],ENT_QUOTES);
        $_POST["first_name"]	= htmlspecialchars($_POST["first_name"],ENT_QUOTES);

        $main->name	= $_POST["name"];
        userNameAdd($main->name);
        $main->SaveData();
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
        $char->SetCharData(array_merge(BaseCharStatus($job),array("name"=>$_POST['first_name'],"gender"=>"$gend")));
        $char->SaveCharData($main->id);
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
	<form action="<?php print INDEX?>" method="post" style="margin:15px">
    <?php ShowError($error);?>
	<h4>Name of Team</h4>
	<p>Decide the Name of the team.<br />
	It should be more than 1 and less than 16 letters.<br />
	Japanese characters count as 2 letters.</p>
	<p>1-16字符的隊伍名。<br /></p>
	<div class="bold u">TeamName</div>
	<input class="text" style="width:160px" maxlength="16" name="name"
    <?php print(isset($_POST["name"])?"value=\"{$_POST['name']}\"":"")?>>
	<h4>First Character</h4>
	<p>Decide the name of Your First Charactor.<br>
	more than 1 and less than 16 letters.</p>
	<p>第一個人物的名稱。</p>
	<div class="bold u">CharacterName</div>
	<input class="text" type="text" name="first_name" maxlength="16" style="width:160px;margin-bottom:10px">
	<table cellspacing="0" style="width:400px"><tbody>
	<tr><td class="td1" valign="bottom"><div style="text-align:center"><?php print $war_male->ShowImage()?><br><input type="radio" name="fjob" value="1" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $war_female->ShowImage()?><br><input type="radio" name="fjob" value="2" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $sor_male->ShowImage()?><br><input type="radio" name="fjob" value="3" style="margin:3px"></div></td>
	<td class="td1" valign="bottom"><div style="text-align:center"><?php print $sor_female->ShowImage()?><br><input type="radio" name="fjob" value="4" style="margin:3px"></div></td></tr>
	<tr><td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td>
	<td class="td2"><div style="text-align:center">male</div></td><td class="td3"><div style="text-align:center">female</div></td></tr>
	<tr><td colspan="2" class="td4"><div style="text-align:center">Warrior</div></td><td colspan="2" class="td4"><div style="text-align:center">Socerer</div></td></tr>
	</tbody></table>
	<p>Choose your first character\'s job & Gender.</p>
	<p>最初的人物性別與職業</p>
	<input class="btn" style="width:160px" type="submit" value="Done" name="Done">
	<input type="hidden" value="1" name="Done">
	<input class="btn" style="width:160px" type="submit" value="logout" name="logout"></form>
<?php 
        return true;
}

/**
 * 顯示新用戶註冊表單
 * Show new user registration form
 * 新規ID作成用のフォ一ム
 * @param string|null $error 錯誤訊息
 */
function NewForm($error=NULL) {
    if(MAX_USERS <= count(GlobUserList())) {
        ?>

<div style="margin:15px">
Maximum users.<br />
用戶數已達到最大。
</div>
<?php 
        return;
    }
    $idset=(isset($_POST["Newid"])?" value=\"{$_POST['Newid']}\"" : NULL);
    ?>
<div style="margin:15px">
<?php print ShowError($error);?>
<h4>註冊!</h4>
<form action="<?php print INDEX?>" method="post">

<table><tbody>
<tr><td colspan="2">ID & PASS must be 4 to 16 letters.<br />letters allowed a-z,A-Z,0-9<br />
ID 和 PASS在 4-16 個字以內。半角英數字。</td></tr>
<tr><td><div style="text-align:right">ID:</div></td>
<td><input type="text" maxlength="16" class="text" name="Newid" style="width:240px"<?php print $idset?>></td></tr>
<tr><td colspan="2"><br />Password,Re-enter.<br />PASS 以及再輸入 確認用。</td></tr>
<tr><td><div style="text-align:right">PASS:</div></td>
<td><input type="password" maxlength="16" class="text" name="pass1" style="width:240px"></td></tr>

<tr><td></td>
<td><input type="password" maxlength="16" class="text" name="pass2" style="width:240px">(verify)</td></tr>

<tr><td></td><td><input type="submit" class="btn" name="Make" value="確定" style="width:160px"></td></tr>

</tbody></table>
</form>
</div>
<?php 
}

/**
 * 建立新用戶資料
 * Create new user data
 * 入力された情報が型にはまるか判定
 * 新規デ一タを作成。
 * @param main $main 主物件
 * @return array
 */
function MakeNewData($main) {
    // 登錄者數が限界の場合
    // When the number of registered users is at the limit
    if(MAX_USERS <= count(GlobUserList()))
        return array(false,"Maximum users.<br />已達到最大用戶數量。");
    if(isset($_POST["Newid"]))
        $_POST["Newid"] = trim($_POST["Newid"]);
    
    if(empty($_POST["Newid"]))
        return array(false,"Enter ID.");

    if(!preg_match("/[0-9a-zA-Z]{4,16}/",$_POST["Newid"]))//正規表現
        return array(false,"Bad ID");

    if(strlen($_POST["Newid"]) < 4 || 16 < strlen($_POST["Newid"]))//文字制限
        return array(false,"Bad ID");

    if(is_registered($_POST["Newid"])) {
        return array(false,"This ID has been already used.");
    }

    $file = USER.$_POST["Newid"]."/".DATA;
    // PASS
    if(empty($_POST["pass1"]) || empty($_POST["pass2"]))
        return array(false,"Enter both Password.");

    if(!preg_match("/[0-9a-zA-Z]{4,16}/",$_POST["pass1"]))
        return array(false,"Bad Password 1");
    if(strlen($_POST["pass1"]) < 4 || 16 < strlen($_POST["pass1"]))//文字制限
        return array(false,"Bad Password 1");
    if(!preg_match("/[0-9a-zA-Z]{4,16}/",$_POST["pass2"]))
        return array(false,"Bad Password 2");
    if(strlen($_POST["pass2"]) < 4 || 16 < strlen($_POST["pass2"]))//文字制限
        return array(false,"Bad Password 2");

    if($_POST["pass1"] !== $_POST["pass2"]) {
        return array(false,"Password dismatch.");
    }

    $pass = $main->CryptPassword($_POST["pass1"]);
    // MAKE
    if(!file_exists($file)){
        mkdir(USER.$_POST["Newid"], 0705);
        RecordRegister($_POST["Newid"]);
        $fp=fopen("$file","w");
        flock($fp,LOCK_EX);
            $now	= time();
            fputs($fp,"id={$_POST['Newid']}\n");
            fputs($fp,"pass=$pass\n");
            fputs($fp,"last=".$now."
");
            fputs($fp,"login=".$now."
");
            fputs($fp,"start=".$now.substr(microtime(),2,6)."
");
            fputs($fp,"money=".START_MONEY."
");
            fputs($fp,"time=".START_TIME."
");
            fputs($fp,"record_btl_log=1
");
        fclose($fp);
        $_SESSION["id"]=$_POST["Newid"];
        setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
        $success	= "<div class=\"recover\">ID : {$_POST['Newid']} 註冊成功. 請登錄吧</div>";
        return array(true,$success);
    }
    return array(false, "Unknown error.");
}

/**
 * 刪除用戶資料
 * Delete user data
 * 自分のデ一タとクッキ一を消す
 * @param main $main 主物件
 * @return bool
 */
function DeleteMyData($main) {
    if(isset($_POST["deletepass"]) && $main->pass == $main->CryptPassword($_POST["deletepass"]) ) {
        $main->DeleteUser();
        $main->name	= NULL;
        $main->pass	= NULL;
        $main->id	= NULL;
        $main->islogin= false;
        unset($_SESSION["id"]);
        unset($_SESSION["pass"]);
        setcookie("NO","");
        $main->LoginForm();
        return true;
    }
    return false;
}

/**
 * 記錄已註冊的ID
 * Record registered ID
 * $id を登錄濟みidとして記錄する
 * @param string $id
 */
function RecordRegister($id) {
    $fp=fopen(REGISTER,"a");
    flock($fp,2);
    fputs($fp,"$id\n");
    fclose($fp);
}

/**
 * 檢查登入狀態
 * Check login status
 * ログインしたのか、しているのか、ログアウトしたのか。
 * @param main $main 主物件
 * @return bool|string
 */
function CheckLogin($main) {
    //logout
    if(isset($_POST["logout"])) {
        unset($_SESSION["pass"]);
        return false;
    }

    //session
    $file=USER.$main->id."/".DATA;//data.dat
    if ($data = $main->LoadData()) {
        if($main->pass == NULL)
            return false;
        if ($data["pass"] === $main->pass) {
            //ログイン狀態
            // Login status
            $main->DataUpDate($data);
            $main->SetData($data);
            if(RECORD_IP)
                $main->SetIp($_SERVER['REMOTE_ADDR']);
            RenewLoginTime($main);

            $pass	= isset($_POST["pass"]) ? $_POST["pass"] : (isset($_GET["pass"]) ? $_GET["pass"] : null);
            if ($pass) {//ちょうど今ログインするなら (Just logged in)
                $_SESSION["id"]	= $main->id;
                $_SESSION["pass"]	= $pass;
                setcookie("NO",session_id(),time()+COOKIE_EXPIRE);
            }

            $main->islogin	= true;
            return true;
        } else
            return "Wrong password!";
    } else {
        if(isset($_POST["id"])) {
            return "ID \"{$main->id}\" doesnt exists.";
        }
    }
    return false;
}

?>
