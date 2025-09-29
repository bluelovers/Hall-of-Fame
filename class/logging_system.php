<?php
/**
 * 日誌系統模組
 * Logging system module
 *
 * 處理BBS、日誌記錄等功能
 * Handles BBS, log recording, etc.
 */

/**
 * 簡單的BBS系統
 * Simple BBS system
 *
 * 處理一行留言板的顯示和提交
 * Handle one-line message board display and submission
 */
function bbs01(&$main) {
    if(!BBS_BOTTOM_TOGGLE)
        return false;
    $file    = BBS_BOTTOM;
    ?>
<div style="margin:15px">
<h4>one line bbs</h4>
錯誤報告或意見，對這裡的開發建議
<form action="?bbs" method="post">
<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?php
    if(!file_exists($file))
        return false;
    $log    = file($file);
    if($_POST["message"] && strlen($_POST["message"]) < 121) {
        $_POST["message"]    = htmlspecialchars($_POST["message"],ENT_QUOTES);
        $_POST["message"]    = stripslashes($_POST["message"]);

        $name    = ($main->name ? "<span class=\"bold\">{$main->name}</span>":"無名");
        $message    = $name." > ".$_POST["message"];
        if($main->UserColor)
            $message    = "<span style=\"color:#{$main->UserColor}\">".$message."</span>";
        $message    .= " <span class=\"light\">(".date("c").")</span>\n";
        array_unshift($log,$message);
        while(150 < count($log))// ログ保存行數あ
            array_pop($log);
        WriteFile($file,implode(null,$log));
    }
    foreach($log as $mes)
        print(nl2br($mes));
    print('</div>');
}
?>
