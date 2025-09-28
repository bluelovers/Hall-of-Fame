<?php

//	マニュアルを表示する
function showManual() {
    include(MANUAL);
    return true;
}

//	マニュアルを表示する
function showManual2() {
    include(MANUAL_HIGH);
    return true;
}

//	チュートリアルを表示する
function showTutorial() {
    include(TUTORIAL);
    return true;
}

//	更新内容の表示
function showUpDate() {
    print('<div style="margin:15px">');
    print("<p><a href=\"?\">Back</a><br><a href=\"#btm\">to bottom</a></p>");

    if($_POST["updatetext"]) {
        $update    = htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
        $update    = stripslashes($update);
    } else
        $update    = @file_get_contents(UPDATE);

    print('<form action="?update" method="post">');
    if($_POST["updatepass"] == UP_PASS) {
        print('<textarea class="text" rows="12" cols="60" name="updatetext">');
        print("$update");
        print('</textarea><br>');
        print('<input type="submit" class="btn" value="update">');
        print('<a href="?update">刷新<br>');
    }

    print(nl2br($update)."\n");
    print('<br><a name="btm"></a>');
    if($_POST["updatepass"] == UP_PASS && $_POST["updatetext"]) {
        $fp    = fopen(UPDATE,"w");
        $text    = htmlspecialchars($_POST["updatetext"],ENT_QUOTES);
        $text    = stripslashes($text);
        flock($fp,2);
        fputs($fp,$text);
        fclose($fp);
    }
print <<< EOD
    <input type="password" class="text" name="updatepass" style="width:100px" value="$_POST[updatepass]">
    <input type="submit" class="btn" value="update">
    </form>
EOD;
    print("<p><a href=\"?\">Back</a></p></div>");
}

function showGameData() {
    ?>
<div style="margin:15px">
<h4>GameData</h4>
<div style="margin:0 20px">
| <a href="?gamedata=job">職業(Job)</a> |
<a href="?gamedata=item">道具(item)</a> |
<a href="?gamedata=judge">判定</a> |
<a href="?gamedata=monster">モンスター</a> |
</div>
</div><?php 
switch($_GET["gamedata"]) {
    case "job": include(GAME_DATA_JOB); break;
    case "item": include(GAME_DATA_ITEM); break;
    case "judge": include(GAME_DATA_JUDGE); break;
    case "monster": include(GAME_DATA_MONSTER); break;
    default: include(GAME_DATA_JOB); break;
}

}
?>