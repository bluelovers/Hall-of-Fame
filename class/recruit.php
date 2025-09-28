<?php

/**
 * 處理招募請求
 * @param main $main 主物件
 * @return bool
 */
function RecruitProcess($main) {
    // 僱用數限界
    if( MAX_CHAR <= count($main->char) )
        return false;

    include(DATA_BASE_CHAR);
    if(isset($_POST["recruit"])) {
        // キャラのタイプ
        switch($_POST["recruit_no"]) {
            case "1": $hire = 2000; $charNo	= 1; break;
            case "2": $hire = 2000; $charNo	= 2; break;
            case "3": $hire = 2500; $charNo	= 3; break;
            case "4": $hire = 4000; $charNo	= 4; break;
            default:
                ShowError("未選擇人物","margin15");
                return false;
        }
        // 名前處理
        if($_POST["recruit_name"]) {
            if(is_numeric(strpos($_POST["recruit_name"],"\t"))) // Corrected: escaped tab character
                return "error.";
            $name	= trim($_POST["recruit_name"]);
            $name	= stripslashes($name);
            $len	= strlen($name);
            if ( 0 == $len || 16 < $len ) {
                ShowError("名稱太短或太長","margin15");
                return false;
            }
            $name	= htmlspecialchars($name,ENT_QUOTES);
        } else {
            ShowError("名稱不能是空","margin15");
            return false;
        }
        //性別
        if( !isset($_POST["recruit_gend"]) ) {
            ShowError("未選定性別","margin15");
            return false;
        } else {
            $Gender	= $_POST["recruit_gend"]?"♀":"♂";
        }
        // キャラデ一タをクラスに入れる
        
        $plus	= array("name"=>"$name","gender"=>$_POST["recruit_gend"]); // Corrected: removed unnecessary quotes around $name
        $char	= new char();
        $char->SetCharData(array_merge(BaseCharStatus($charNo),$plus));
        //僱用金
        if($hire <= $main->money) {
            $main->TakeMoney($hire);
        } else {
            ShowError("您沒有足夠的錢","margin15");
            return false;
        }
        // キャラを保存する
        $char->SaveCharData($main->id);
        ShowResult($char->Name()."($char->job_name:{$Gender})". "加為同伴！","margin15"); // Corrected: removed unnecessary quotes around the concatenated string
        return true;
    }
    return false;
}

/**
 * 顯示招募頁面
 * @param main $main 主物件
 */
function RecruitShow($main) {
    if( MAX_CHAR <= $main->CharCount() ) {
        ?>

<div style="margin:15px">
<p>Maximum characters.<br>
Need to make a space to recruit new character.</p>
<p>人物上限數達到。<br>
要添加新的空間來僱用新人（？）。</p>
</div>
<?php 
        return;
    }
    include_once(CLASS_MONSTER);
    $char = [];
    $char[0]	= new char();
    $char[0]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"0")));
    $char[1]	= new char();
    $char[1]->SetCharData(array_merge(BaseCharStatus("1"),array("gender"=>"1")));
    $char[2]	= new char();
    $char[2]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"0")));
    $char[3]	= new char();
    $char[3]->SetCharData(array_merge(BaseCharStatus("2"),array("gender"=>"1")));
    $char[4]	= new char();
    $char[4]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"0")));
    $char[5]	= new char();
    $char[5]->SetCharData(array_merge(BaseCharStatus("3"),array("gender"=>"1")));
    $char[6]	= new char();
    $char[6]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"0")));
    $char[7]	= new char();
    $char[7]->SetCharData(array_merge(BaseCharStatus("4"),array("gender"=>"1")));
    ?>

<form action="?recruit" method="post" style="margin:15px">
<h4>新人物的職業</h4>
<table cellspacing="0"><tbody><tr>
<td class="td1" style="text-align:center">
<?php $char[0]->ShowImage()?><?php $char[1]->ShowImage()?><br>
<input type="radio" name="recruit_no" value="1" style="margin:3px"><br>
<?php print MoneyFormat(2000)?></td>
<td class="td1" style="text-align:center">
<?php $char[2]->ShowImage()?><?php $char[3]->ShowImage()?><br>
<input type="radio" name="recruit_no" value="2" style="margin:3px"><br>
<?php print MoneyFormat(2000)?></td>
<td class="td1" style="text-align:center">
<?php $char[4]->ShowImage()?><?php $char[5]->ShowImage()?><br>
<input type="radio" name="recruit_no" value="3" style="margin:3px"><br>
<?php print MoneyFormat(2500)?></td>
<td class="td1" style="text-align:center">
<?php $char[6]->ShowImage()?><?php $char[7]->ShowImage()?><br>
<input type="radio" name="recruit_no" value="4" style="margin:3px"><br>
<?php print MoneyFormat(4000)?></td>
</tr><tr>
<td class="td4" style="text-align:center">
	戰士</td>
<td class="td5" style="text-align:center">
	法師</td>
<td class="td4" style="text-align:center">
	牧師</td>
<td class="td5" style="text-align:center">
	獵人</td>
</tr>
</tbody></table>

<h4>新人物的性別</h4>
<table><tbody><tr><td valign="top">
<input type="text" class="text" name="recruit_name" style="width:160px" maxlength="16"><br>
<div style="margin:5px 0px">
<input type="radio" class="vcent" name="recruit_gend" value="0">男
<input type="radio" class="vcent" name="recruit_gend" value="1" style="margin-left:15px;">女</div>
<input type="submit" class="btn" name="recruit" value="僱傭">
<input type="hidden" class="btn" name="recruit" value="Recruit">
</td><td valign="top">
<p>1 to 16 letters.<br>
Chinese characters count as 2.<br>
1個漢字 = 2 letter.
</p>
</td></tr></tbody></table>
</form>
<?php
}

?>
