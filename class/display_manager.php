<?php
/**
 * 顯示管理模組
 * Display management module
 *
 * 處理HTML輸出、選單、角色顯示等功能
 * Handles HTML output, menus, character display, etc.
 */

/**
 * HTML開始部分
 * HTML head section
 *
 * 輸出HTML文檔的開始標籤和基本結構
 * Output HTML document start tags and basic structure
 */
function Head(&$main) {
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php HtmlScript();?>
<title><?php print TITLE?></title>
</head>
<body><a name="top"></a>
<div id="main_frame">
<div id="title"><img src="./image/title03.gif"></div>
<?php MyMenu($main);?><div id="contents">
<?php
}

/**
 * 樣式表和腳本
 * Stylesheets and scripts
 *
 * 輸出CSS和JavaScript引用
 * Output CSS and JavaScript references
 */
function HtmlScript() {
    ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="./basis.css" type="text/css">
<link rel="stylesheet" href="./style.css" type="text/css">
<script type="text/javascript" src="prototype.js"></script>
<?php
}

/**
 * HTML結束部分
 * HTML foot section
 *
 * 輸出HTML文檔的結束標籤和頁腳
 * Output HTML document end tags and footer
 */
function Foot() {
    ?>
</div>
<div style="clear: both;"></div>
<div id="foot">
<a href="?update">UpDate</a> -
<?php
    if(BBS_BOTTOM_TOGGLE)
        print('<a href="'.BBS_OUT.'" target="_blank">BBS</a> - '."\n");
        ?>
<a href="?manual">手冊</a> -
<a href="?tutorial">教學</a> -
<a href="?gamedata=job">遊戲數據</a> -
<a href="#top">Top</a><br>
Copy Right <a href="http://tekito.kanichat.com/">Tekito</a> 2007-2008.<br>
漢化 By <a href="http://www.firingsquad.com.cn/">FiringSquad中文網</a> 2006-2008.<br>
</div>
</div>
</body>
</html>
<?php
}

/**
 * 用戶選單顯示
 * User menu display
 *
 * 根據登入狀態顯示不同的選單
 * Display different menus based on login status
 */
function MyMenu(&$main) {
    if($main->name && $main->islogin) { // ログインしてる人用
        print('<div id="menu">'."\n");
        //print('<span class="divide"></span>');//區切り
        print('<a href="'.INDEX.'">首頁</a><span class="divide"></span>');
        print('<a href="?hunt">狩獵</a><span class="divide"></span>');
        print('<a href="?item">道具</a><span class="divide"></span>');
        print('<a href="?town">城鎮</a><span class="divide"></span>');
        print('<a href="?setting">設置</a><span class="divide"></span>');
        print('<a href="?log">記錄</a><span class="divide"></span>');
        if(BBS_OUT)
            print('<a href="'.BBS_OUT.'" target="_balnk">BBS</a><span class="divide"></span>'."\n");
        print('</div><div id="menu2">'."\n");
            ?>
<div style="width:100%">
<div style="width:30%;float:left"><?php print $main->name?></div>
<div style="width:60%;float:right">
<div style="width:40%;float:left"><span class="bold">資金</span> : <?php print MoneyFormat($main->money)?></div>
<div style="width:40%;float:right"><span class="bold">時間</span> : <?php print floor($main->time)?>/<?php print MAX_TIME?></div>
</div>
<div class="c-both"></div>
</div>
<?php
        print('</div>');
    } else if(!$main->name && $main->islogin) {// 初回ログインの人
        print('<div id="menu">');
        print("First login. Thankyou for the entry.");
        print('</div><div id="menu2">');
        print("fill the blanks. 來吧，請填寫。");
        print('</div>');
    } else { //// ログアウト狀態の人、來客用の表示
        print('<div id="menu">');
        print('<a href="'.INDEX.'">首頁</a><span class="divide"></span>'."\n");
        print('<a href="?newgame">新註冊</a><span class="divide"></span>'."\n");
        print('<a href="?manual">規則和手冊</a><span class="divide"></span>'."\n");
        print('<a href="?gamedata=job">遊戲數據</a><span class="divide"></span>'."\n");
        print('<a href="?log">戰鬥記錄</a><span class="divide"></span>'."\n");
        if(BBS_OUT)
        print('<a href="'.BBS_OUT.'" target="_balnk">BBS</a><span class="divide"></span>'."\n");
        print('</div><div id="menu2">');
        print("歡迎來到 [ ".TITLE." ]");
        print('</div>');
    }
}

/**
 * 登入後主畫面
 * Login main screen
 *
 * 顯示登入後的初始畫面
 * Display initial screen after login
 */
function LoginMain(&$main) {
    ShowTutorial($main);
    ShowMyCharacters($main);
    RegularControl($main->id);
}

/**
 * 教程顯示
 * Tutorial display
 *
 * 在登入後一段時間內顯示教程連結
 * Display tutorial link for a period after login
 */
function ShowTutorial(&$main) {
    $last    = $main->last;
    $start    = substr($main->start,0,10);
    $term    = 60*60*1;
    if( ($last - $start) < $term) {
        ?>
<div style="margin:5px 15px">
<a href="?tutorial">教程</a> - 戰鬥的基本(登錄後一個小時內顯示)
</div>

<?php
    }
}

/**
 * 顯示用戶角色
 * Show user characters
 *
 * 以表格形式顯示用戶的所有角色
 * Display all user characters in table format
 */
function ShowMyCharacters(&$main, $array=NULL) {// $array ← 色々受け取る
    if(!$main->char) return false;
    $divide    = (count($main->char)<CHAR_ROW ? count($main->char) : CHAR_ROW);
    $width    = floor(100/$divide);//各セル橫幅

    print('<table cellspacing="0" style="width:100%"><tbody><tr>');//橫幅100%
    foreach($main->char as $val) {
        if( $i%CHAR_ROW==0 && $i != 0 )
            print("\t</tr><tr>\n");
        print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//キャラ數に應じて%で各セル分割
        $val->ShowCharLink($array);
        print("</td>\n");
        $i++;
    }
    print("</tr></tbody></table>");
}

/**
 * 顯示角色列表
 * Show character list
 *
 * 以表格形式顯示角色陣列，可以選擇不同顯示模式
 * Display character array in table format with different display modes
 */
function ShowCharacters($characters,$type=null,$checked=null) {
    if(!$characters) return false;
    $divide    = (count($characters)<CHAR_ROW ? count($characters) : CHAR_ROW);
    $width    = floor(100/$divide);//各セル橫幅

    if($type == "CHECKBOX") {
print <<< HTML
<script type="text/javascript">
<!--
function toggleCheckBox(id) {
id0 = "box" + id;
\$("box" + id).checked = \$("box" + id).checked?false:true;
Element.toggleClassName("text"+id,'unselect');
}
// -->
</script>
HTML;
    }

    print('<table cellspacing="0" style="width:100%"><tbody><tr>');//橫幅100%
    foreach($characters as $char) {
        if( $i%CHAR_ROW==0 && $i != 0 )
            print("\t</tr><tr>\n");
        print("\t<td valign=\"bottom\" style=\"width:{$width}%\">");//キャラ數に應じて%で各セル分割

        /*-------------------*/
        switch(1) {
            case ($type === MONSTER):
                $char->ShowCharWithLand($checked); break;
            case ($type === CHECKBOX):
                if(!is_array($checked)) $checked = array();
                if(in_array($char->birth,$checked))
                    $char->ShowCharRadio($char->birth," checked");
                else
                    $char->ShowCharRadio($char->birth);
                break;
            default:
                $char->ShowCharLink();
        }

        print("</td>\n");
        $i++;
    }
    print("</tr></tbody></table>");
}

/**
 * 變數調試顯示
 * Variable debug display
 *
 * 在調試模式下顯示物件變數
 * Display object variables in debug mode
 */
function Debug(&$main) {
    if(DEBUG)
        print("<pre>".print_r(get_object_vars($main),1)."</pre>");
}

/**
 * 會話信息顯示
 * Session information display
 *
 * 顯示當前的會話和Cookie信息
 * Display current session and cookie information
 */
function ShowSession(&$main) {
    echo "this->id:$main->id<br>";
    echo "this->pass:$main->pass<br>";
    echo "SES[id]:$_SESSION[id]<br>";
    echo "SES[pass]:$_SESSION[pass]<br>";
    echo "SES[pass]:".$main->CryptPassword($_SESSION[pass])."(crypted)<br>";
    echo "CK[NO]:$_COOKIE[NO]<br>";
    echo "SES[NO]:".session_id();
    dump($_COOKIE);
    dump($_SESSION);
}
?>
