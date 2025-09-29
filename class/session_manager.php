<?php
/**
 * 會話管理模組
 * Session management module
 *
 * 處理用戶會話、登入、驗證等功能
 * Handles user sessions, login, authentication, etc.
 */

/**
 * 會話切換處理
 * Session switch handling
 *
 * 處理會話ID的切換和Cookie管理
 * Handles session ID switching and cookie management
 */
function SessionSwitch(&$main) {
    // session消滅の時間(?)
    // how about "session_set_cookie_params()"?
    session_cache_expire(COOKIE_EXPIRE/60);
    if($_COOKIE["NO"])//クッキ一に保存してあるセッションIDのセッションを呼び出す
        session_id($_COOKIE["NO"]);

    session_start();
    if(!SESSION_SWITCH)//switchしないならここで終了
        return false;
    //print_r($_SESSION);
    //dump($_SESSION);
    $OldID    = session_id();
    $temp    = serialize($_SESSION);

    session_regenerate_id();
    $NewID    = session_id();
    setcookie("NO",$NewID,time()+COOKIE_EXPIRE);
    $_COOKIE["NO"]=$NewID;

    session_id($OldID);
    // session_start();

    if($_SESSION):
    //    session_destroy();//Sleipnirだとおかしい...?(最初期)
    //    unset($_SESSION);//こっちは大丈夫(やっぱりこれは馱目かも)(修正後)
        //結局,セッションをforeachでル一プして1個づつunset(2007/9/14 再修正)
        foreach($_SESSION as $key => $val)
            unset($_SESSION["$key"]);
    endif;

    session_id($NewID);
    // session_start();
    $_SESSION    = unserialize($temp);
}

/**
 * 設定用戶ID和密碼
 * Set user ID and password
 *
 * 從POST或GET參數中獲取ID和密碼，並進行加密處理
 * Get ID and password from POST or GET parameters and encrypt them
 */
function Set_ID_PASS(&$main) {
    $id    = ($_POST["id"])?$_POST["id"]:$_GET["id"];
    //if($_POST["id"]) {
    if($id) {
            $main->id    = $id;//$_POST["id"];
        // ↓ログイン處理した時だけ
        if (is_registered($_POST["id"])) {
            $_SESSION["id"]    = $main->id;
        }
    } else if($_SESSION["id"])
        $main->id    = $_SESSION["id"];

    $pass    = ($_POST["pass"])?$_POST["pass"]:$_GET["pass"];
    //if($_POST["pass"])
    if($pass)
        $main->pass    = $pass;//$_POST["pass"];
    else if($_SESSION["pass"])
        $main->pass    = $_SESSION["pass"];

    if($main->pass)
        $main->pass    = $main->CryptPassword($main->pass);
}

/**
 * 更新登入時間
 * Renew login time
 *
 * 設定用戶的登入時間戳記
 * Set user's login timestamp
 */
function RenewLoginTime(&$main) {
    $main->login    = time();
}

/**
 * 登入表單顯示
 * Login form display
 *
 * 顯示登入表單和排行榜信息
 * Display login form and ranking information
 */
function LoginForm(&$main, $message = NULL) {
    ?>
<div style="width:730px;">
<!-- ログイン -->
<div style="width:350px;float:right">
<h4 style="width:350px">登錄</h4>
<?php print $message?>
<form action="<?php print INDEX?>" method="post" style="padding-left:20px">
<table><tbody>
<tr>
<td><div style="text-align:right">ID:</div></td>
<td><input type="text" maxlength="16" class="text" name="id" style="width:160px"<?php print $_SESSION["id"]?" value=\"$_SESSION[id]\"":NULL?>></td>
</tr>
<tr>
<td><div style="text-align:right">PASS:</div></td>
<td><input type="password" maxlength="16" class="text" name="pass" style="width:160px"></td>
</tr>
<tr><td></td><td>
<input type="submit" class="btn" name="Login" value="登錄" style="width:80px">
<a href="?newgame">新玩家?</a>
</td></tr>
</tbody></table>
</form>

<h4 style="width:350px">排行榜</h4>
<?php
    include_once(CLASS_RANKING);
    $Rank    = new Ranking();
    $Rank->ShowRanking(0,4);
    ?>
</div>
<!-- 飾 -->
<div style="width:350px;padding:5px;float:left;">
<div style="width:350px;text-align:center">
<img src="./image/top01.gif" style="margin-bottom:20px" />
</div>
<div style="margin-left:20px">
<DIV class=u>這到底是什麼遊戲?</DIV>
<UL>
<LI>遊戲的目的是得到第一、<BR>並且保持住第一的位置。
<LI>雖然沒有冒險的要素、<BR>但有點深奧的戰鬥系統。 </LI></UL>
<DIV class=u>戰鬥的感覺是什麼?</DIV>
<UL>
<LI>5人的人物構成隊伍 。
<LI>各人物各持不同模式、<BR>根據戰鬥的狀況來使用技能。
<LI><A class=a0 href="?log">這邊</A>可以回覽戰鬥記錄。 </LI></UL></DIV></DIV>

<div class="c-both"></div>
</div>

<!-- -------------------------------------------------------- -->

<div style="margin:15px">
<h4>提示</h4>
用戶數: <?php print UserAmount()?> / <?php print MAX_USERS?><br />
<?php
    $Abandon    = ABANDONED;
    print(floor($Abandon/(60*60*24))."日中數據沒變化的話數據將消失。");
print("</div>\n");
}
?>
